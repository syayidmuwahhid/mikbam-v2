let requestPending = false;
$(document).ready(function() {
  $('#status-title').html(`Data Router <span>| Sumber Internet: ${ sumberInternet }</span>`);
  $('#konfig-title').html(`Sumber Internet: ${ sumberInternet } | <a href="${baseL}/setting" class="text-warning">ubah</a>`);

  blokUI('Sedang Mengambil Data . . .');
  getData();
  setInterval(getData, timeout);
});

const getData = () => {
  if (!requestPending) {
    requestPending = true;
    let post_data = postData;
    post_data.interface = sumberInternet;

    const response = (resp) => {
      $('#status-dhcp').html(resp.data['dhcp-client'] === true ? 
        `<label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>` :
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
      $('#status-ip-address').html(resp.data['ip-address'] !== false ? 
        `<label>${ resp.data['ip-address'] }</label>`:
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
      $('#status-dns').html(resp.data['dns'] !== false ? 
        `<label>${ resp.data['dns'].join("<br>") }</label>`:
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
      $('#status-gateway').html(resp.data['gateway'] !== false ? 
        `<label>${ resp.data['gateway'] }</label>`:
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
      $('#status-nat').html(resp.data['nat'] !== false ?
        `<label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>` :
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
      $('#status-internet').html(resp.data['internet'] === true ? 
        `<label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>` :
        `<label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>`);
    }

    requestData('post', `${baseL}/api/internet-gateway`, post_data, response, 'logout', () => {
      requestPending = false;         
      $.unblockUI();
    });
  }
}

const konfAuto = () => {
  swalConfirm('warning', 'Yakin', 'mengonfigurasi Internet Gateway Otomatis?', () => {
    let post_data = postData;
    post_data.interface = sumberInternet;
    requestData('post', `${baseL}/api/internet-gateway/run-auto`, post_data, (resp) => {
      if (resp.status === 'success') {
        notif('success', resp.message);
        getData();
      } else {
        notif('error', resp.message);
      }
    });
  });
}

const konfMan = () => {
  generateModal('ig-manual-modal', 'ig-manual-form', 'Konfigurasi Internet Gateway Manual');

  //modal body
  var html = '';
  html += '<div class="row mb-3">' +
      '<div class="col-sm-4"><label class="col-form-label">IP Address :</label></div>' +
      '<div class="col-sm-8"><input required type="text" name="ip" id="ig-manual-ip" onkeypress="validateIP(event)" class="form-control" placeholder="0.0.0.0/0" title="Penulisan IP Address menggunakan kode CIDR [x.x.x.x/x]"></div>' +
      '</div>';
  html += '<div class="row mb-3">' +
      '<div class="col-sm-4"><label class="col-form-label">DNS :</label></div>' +
      '<div class="col-sm-8"><input required type="text" name="DNS" id="ig-manual-dns" class="form-control" onkeypress="validateIPnoPrefix(event)" placeholder="0.0.0.0" /></div>' +
      '</div>';
  html += '<div class="row mb-3">' +
      '<div class="col-sm-4"><label class="col-form-label">Gateway :</label></div>' +
      '<div class="col-sm-8"><input required type="text" name="Gateway" id="ig-manual-gateway" class="form-control" onkeypress="validateIPnoPrefix(event)" placeholder="0.0.0.0" /></div>' +
      '</div>';
  $('.modal-body').append(html);

  $('#ig-manual-ip').focusout(() => {
    const ip = $('#ig-manual-ip').val();
    if (ip.split('/').length !== 2 || ip.split('.').length !== 4) {
      notif('error', 'Kesalahan', 'IP Address tidak valid [eg. 192.168.1.1/24]');
      $('#ig-manual-ip').val('');
    }

    const cidr = ip.split('/')[1];
    if (cidr < 8 || cidr > 32) {
      notif('error', 'Kesalahan', 'Kode CIDR tidak valid [8-32]');
      $('#ig-manual-ip').val(`${ ip.split('/')[0] }/`);
    }
    
  });

  $('#ig-manual-modal').modal('show');

  //submit
  $('#ig-manual-form').submit(function(e){
    e.preventDefault();
    e.stopImmediatePropagation();

    const post_data = postData;
    post_data.ip = $('#ig-manual-ip').val();
    post_data.dns = $('#ig-manual-dns').val();
    post_data.gateway = $('#ig-manual-gateway').val();

    console.log(post_data);

    // blokUI('Loading...');
    // const submit = (resp) => {
    //   notif('success', resp.message);
    //   getData();
  
    //   if (resp.status === 'fail') {
    //     notif('error', resp.message);
    //   }
    // }
    // requestData('post', `${baseL}/api/ip-address/store`, post_data, submit, null, () => {
    //   $('#modal-ip').modal('hide');
    //   $.unblockUI()
    // });
  });
}