let requestPending = false;

$(document).ready(function() {
  blokUI('Sedang Mengambil Data . . .');
  getData();
  setInterval(getData, timeout);
});

const getData = () => {
  if (!requestPending) {
    requestPending = true;
    const response = (resp) => {
      $('#table-body').empty();
      resp.data.forEach((val, i) => {
        let tr_class = '';
        let ds_class = 'warning';
        let ds_name = 'Disable';
        let flag = 'S';
        flag_tip = 'Static';
        let interface = val.interface.charAt(0) === '*' ? 'Uknown' : val.interface;

        if (val.disabled === 'true') {
          tr_class = 'text-warning fst-italic';
          ds_class = 'success';
          ds_name = 'Enable';
        }
        
        if (val.dynamic === 'true') {
          flag = 'D';
          flag_tip = 'Dynamic';
        }
        
        if (val.invalid === 'true') {
          flag = 'I';
          flag_tip = 'Invalid';
          tr_class = 'text-danger fst-italic';
        }

        let html = `<tr class="${ tr_class }">`;
        html += `<td class="text-center"> ${++i} </td>`;
        html += `<td class="text-center" data-toggle="tooltip" data-placement="top" title="${ flag_tip }"> ${ flag } </td>`;
        html += `<td> ${interface} </td>`;
        html += `<td> ${val.network} </td>`;
        html += `<td> ${val.gateway} </td>`;
        html += `<td> ${val.pool} </td>`;
        html += `<td> ${val['lease-time']} </td>`;
        html += `<td class="text-center">`;
        html += `<button type="button" class="btn btn-sm btn-${ ds_class }" onclick="changeStat('${ val['.id'] }', '${ ds_name }')"> ${ ds_name } </button> `;
        html += `<button type="button" class="btn btn-sm btn-danger" onclick="remove('${ val['.id'] }', '${ interface }')"> Remove </button>`;
        html += `</td>`;
        html += `</tr>`;
        $('#table-body').append(html);
      });
      $('#tabel-dhcp').DataTable();
    }

    requestData('post', `${baseL}/api/dhcp-server`, null, response, 'logout', () => {
      requestPending = false;         
      $.unblockUI();
    });
  }
}

const changeStat = (id, stat) => {
  let post_data = postData;
  post_data.id = id;
  post_data.stat = stat;

  blokUI('Loading...');

  const response = (resp) => {
    notif('success', resp.message);
    getData();

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/dhcp-server/stat-update`, post_data, response);
}

const remove = (id, interface) => {
  const callback = () => {
    let post_data = postData;
    post_data.id = id;

    blokUI('Loading...');

    const response = (resp) => {
      notif('success', resp.message);
      getData();
  
      if (resp.status === 'fail') {
        notif('error', resp.message);
      }
    }
    requestData('delete', `${baseL}/api/dhcp-server`, post_data, response);
  }

  swalConfirm('warning', 'Konfirmasi', `Yakin Menghapus DHCP Server interface ${ interface }`, callback);
}

const modalAdd = () => {
  generateModal('modal-dhcp', 'modal-dhcp-form', 'Tambah DHCP Client');

  //modal body
  var html = '';
  html += '<label>Pilih Interface :</label>';
  html += '<select class="form-select" name="dhcp_int" id="dhcp_int" onchange="intChange()"><option disabled="">Pastikan Interface memiliki IP Address</option></select><br>';
  html += '<label>DHCP Pool : </label>';
  html += '<input type="text" name="dhcp_pool" id="dhcp_pool" class="form-control" required><span>e.g. 192.168.0.1-192.168.0.5</span><br><span>e.g. 192.168.0.10,192.168.0.20-192.168.0.100</span><br><span>e.g. 192.168.0.1-192.168.0.18,192.168.0.20-192.168.0.100</span><br><br><label>DNS Server : </label>';
  html += '<input type="text" name="dhcp_dns" id="dhcp_dns" class="form-control" title="Jika Memiliki lebih dari 1 DNS Server penulisannya dipisahkan oleh koma (,)">';
  $('.modal-body').append(html);

  //get interfaces
  const getInterface = (resp) => {
    resp.data.forEach(val => {
      $('#dhcp_int').append(`<option id="int${ val.name }" value="${ val.name }">${ val.name }</option>`);
    });

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }

    intChange();
  }
  requestData('post', `${baseL}/api/interfaces`, null, getInterface);

  //get DNS
  const dns = (resp) => {
    let html = '';
    resp.data.forEach(val => {
      html += val + ',';
    });
    $('#dhcp_dns').val(html);
  }
  
  requestData('post', `${baseL}/api/dns`, null, dns);
  
  $('#modal-dhcp').modal('show');
  //submit
  $('#modal-dhcp-form').submit(function(e){
    e.preventDefault();
    e.stopImmediatePropagation();

    const post_data = postData;
    post_data.interface = $('#dhcp_int').val();
    post_data.pool = $('#dhcp_pool').val();
    post_data.dns = $('#dhcp_dns').val();

    const submit = (resp) => {
      notif('success', resp.message);
      getData();
  
      if (resp.status === 'fail') {
        notif('error', resp.message);
      }
    }
    requestData('post', `${baseL}/api/dhcp-server/store`, post_data, submit, null, () => {
      $('#modal-dhcp').modal('hide');
      $.unblockUI()
    });
  });
}

const intChange = () => {
  blokUI('Loading...');
  const interface = $('#dhcp_int').val();
  let address = null;
  
  //get ip
  let post_data = postData;
  post_data.interface = interface;

  const getIP = (resp) => {
    address = resp.data[0] ? resp.data[0]['address'] : null;
    network = resp.data[0] ? resp.data[0]['network'] : null;
    
    if (address === null) {
      notif('warning', 'Interface tidak memiliki IP Address');
      $('#dhcp_int').val('');
    } else {
      $(`#int${ interface }`).html(`${ interface } [${ address }]`);
      generatePool(address, network);
    }
  }
  requestData('post', `${baseL}/api/interfaces/get-ip`, post_data, getIP);

}

const generatePool = (address, network) => {
  let post_data = postData;
  post_data.ip = address;
  post_data.network = network;

  const response = (resp) => {
    let html = '';
    resp.data.ranges.forEach(val => {
      html += val + ',';
    });
    $('#dhcp_pool').val(html);
  }
  requestData('post', `${baseL}/api/dhcp-server/generate-pool`, post_data, response);
}