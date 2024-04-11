$(document).ready(function() {
  blokUI('Sedang Mengambil Data . . .');

  $('#setting-timeout').val(timeout/1000);
  $('#setting-interface').val(sumberInternet);
  
  const getInterface = (resp) => {
    $('#setting-interface').empty();
    resp.data.forEach(val => {
      $('#setting-interface').append(`<option>${ val.name }</option>`);
    });
    $('#setting-interface').val(sumberInternet);

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/interfaces`, null, getInterface);

});

const ubahDefInterface = () => {
  swalConfirm('question', 'Ubah Interface', 'Yakin Merubah Sumber Internet?', () => {
    simpanSesi('sumber-internet', $('#setting-interface').val());
    notif('success', `Sumber internet berhasil diubah menjadi ${ $('#setting-interface').val() }`);
  }, () => {
    $('#setting-interface').val(sumberInternet);
  });
}

const ubahTimeout = () => {
  if ($('#setting-timeout').val() < 5) {
    notif('warning', 'Kesalahan', 'Refresh Page tidak boleh lebih kecil dari 5 detik agar tidak terjadi Crash');
    $('#setting-timeout').val(5);
  } else {
    notif('success', `Refresh Page berhasil diubah menjadi setiap ${ $('#setting-timeout').val() } detik`);
  }
  simpanSesi('timeout', $('#setting-timeout').val()*1000);
}