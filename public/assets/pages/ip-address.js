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
        html += `<td> ${val.address} </td>`;
        html += `<td> ${val.network} </td>`;
        html += `<td> ${interface} </td>`;
        html += `<td class="text-center">`;
        html += `<button type="button" class="btn btn-sm btn-${ ds_class }" onclick="changeStat('${ val['.id'] }', '${ ds_name }')"> ${ ds_name } </button> `;
        html += `<button type="button" class="btn btn-sm btn-danger" onclick="remove('${ val['.id'] }', '${ val.address }')"> Remove </button>`;
        html += `</td>`;
        html += `</tr>`;
        $('#table-body').append(html);
      });
      $('#tabel-ip').DataTable();
    }

    requestData('post', `${baseL}/api/ip-address`, null, response, 'logout', () => {
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
  requestData('post', `${baseL}/api/ip-address/stat-update`, post_data, response);
}

const remove = (id, address) => {
  const callback = () => {
    blokUI('Loading...');
    let post_data = postData;
    post_data.id = id;

    const response = (resp) => {
      notif('success', resp.message);
      getData();
  
      if (resp.status === 'fail') {
        notif('error', resp.message);
      }
    }
    requestData('delete', `${baseL}/api/ip-address`, post_data, response);
  }

  swalConfirm('warning', 'Konfirmasi', `Yakin Menghapus IP Address ${ address }`, callback);
}

const modalAdd = () => {
  generateModal('modal-ip', 'modal-ip-form', 'Tambah IP Addresss');

  //modal body
  var html = '';
  html += '<div class="row mb-3">' +
      '<div class="col-sm-4"><label class="col-form-label">IP Address :</label></div>' +
      '<div class="col-sm-8"><input type="text" name="ip" id="modal-form-ip" onkeypress="validateIP(event)" class="form-control" placeholder="0.0.0.0/0" title="Penulisan IP Address menggunakan kode CIDR [x.x.x.x/x]"></div>' +
      '</div>';
  html += '<div class="row mb-3">' +
      '<div class="col-sm-4"><label class="col-form-label">Interface :</label></div>' +
      '<div class="col-sm-8"><select name="interface" id="modal-form-interface" class="form-select"></select></div>' +
      '</div>';
  $('.modal-body').append(html);

  //get interfaces
  const getInterface = (resp) => {
    resp.data.forEach(val => {
      $('#modal-form-interface').append(`<option>${ val.name }</option>`);
    });

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/interfaces`, null, getInterface);

  $('#modal-ip').modal('show');

  $('#modal-form-ip').focusout(() => {
    const ip = $('#modal-form-ip').val();
    if (ip.split('/').length !== 2 || ip.split('.').length !== 4) {
      notif('error', 'Kesalahan', 'IP Address tidak valid [eg. 192.168.1.1/24]');
      $('#modal-form-ip').val('');
    }

    const cidr = ip.split('/')[1];
    if (cidr < 8 || cidr > 32) {
      notif('error', 'Kesalahan', 'Kode CIDR tidak valid [8-32]');
      $('#modal-form-ip').val(`${ ip.split('/')[0] }/`);
    }
    
  });
  
  //submit
  $('#modal-ip-form').submit(function(e){
    e.preventDefault();
    e.stopImmediatePropagation();

    const post_data = postData;
    post_data.ip = $('#modal-form-ip').val();
    post_data.interface = $('#modal-form-interface').val();

    blokUI('Loading...');
    const submit = (resp) => {
      notif('success', resp.message);
      getData();
  
      if (resp.status === 'fail') {
        notif('error', resp.message);
      }
    }
    requestData('post', `${baseL}/api/ip-address/store`, post_data, submit, null, () => {
      $('#modal-ip').modal('hide');
      $.unblockUI()
    });
  });
}