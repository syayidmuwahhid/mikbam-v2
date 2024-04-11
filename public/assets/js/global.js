const loginData = JSON.parse(localStorage.getItem('login-data'));
const timeout = localStorage.getItem('timeout');
const sumberInternet = localStorage.getItem('sumber-internet');
const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const sBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
let isDemo = false;

initData();

const postData = {
   '_token': token,
   'host' : loginData['host'],
   'user' : loginData['user'],
   'pass' : loginData['pass'],
   'port' : loginData['port'],
};

function simpanSesi (nama, data) {
   localStorage.setItem(nama, data);
}

function getSesi (nama) {
   localStorage.getItem(nama);
}

function hapusSesi (nama = null){
   if (nama === null) {
      localStorage.clear();
   } else {
      localStorage.removeItem(nama);
   }
}

function notif(type, title, text = null) {
    Swal.mixin({
       toast: true,
       position: 'top-right',
       iconColor: 'white',
       customClass: {
          popup: 'colored-toast'
       },
       showConfirmButton: false,
       timer: 5000,
       timerProgressBar: true
    }).fire({
       icon: type,
       title: title,
       text: text,
    })
}

function swalModal(type, title, text = null) {
   Swal.fire({
      title: title,
      text: text,
      icon: type,
   });
}

function swalConfirm(type, title, text, callback, cancel=null) {
   Swal.fire({
      icon: type,
      title: title,
      text: text,
      showCancelButton: true,
      confirmButtonText: 'Yes',
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
   }).then((result) => {
      if (result.isConfirmed) {
         callback();
      } else {
         swalModal('error', 'Cancelled');
         if(cancel) {
            cancel();
         }
      }
   });
}
 
function blokUI(msg){
    $.blockUI({
       message: '<div class="spinner-border text-primary" style="width:50px; height: 50px;"></div><br><span class="text-semibold text-white">' + msg + '</span>',
       // timeout: 2000, //unblock after 2 seconds
       overlayCSS: {
          backgroundColor: '#000',
          opacity: 0.5,
          cursor: 'wait'
       },
       css: {
          border: 0,
          padding: 0,
          backgroundColor: 'transparent'
       }
    });
}

function chartDonat() {
   document.addEventListener("DOMContentLoaded", () => {
      echarts.init(document.querySelector("#memory_stat")).setOption({
         tooltip: {
            trigger: 'item'
         },
         legend: {
            top: '0%',
            left: 'center'
         },
         series: [{
            name: 'Memory (MiB)',
            type: 'pie',
            radius: ['40%', '80%'],
            avoidLabelOverlap: false,
            label: {
               show: false,
               position: 'center'
            },
            emphasis: {
               label: {
                  show: true,
                  fontSize: '18',
                  fontWeight: 'bold'
               }
            },
            labelLine: {
               show: false
            },
            data: [{
               value: 50,
               name: 'Used'
            },
            {
               value: 100,
               name: 'Free'
            },
            ]
         }]
      });
   });
}

function initData(){
   $('.router-name').html(loginData['name']);
   $('#router-host').html(`${ loginData['user'] }@${ loginData['host'] }`);
}

function validateIP(event) {
  const allowedChars = /[0-9\.\/]/; // Hanya angka dan titik yang diperbolehkan
  const inputChar = String.fromCharCode(event.keyCode);

  // Cek apakah karakter yang dimasukkan diperbolehkan
  if (!allowedChars.test(inputChar)) {
    event.preventDefault(); // Mencegah input karakter yang tidak diperbolehkan
  }
}

function validateIPnoPrefix(event) {
  const allowedChars = /[0-9\.]/; // Hanya angka dan titik yang diperbolehkan
  const inputChar = String.fromCharCode(event.keyCode);

  // Cek apakah karakter yang dimasukkan diperbolehkan
  if (!allowedChars.test(inputChar)) {
    event.preventDefault(); // Mencegah input karakter yang tidak diperbolehkan
  }
}

function generateModal(modalId, modalFormId, modalTitle){
   let html = '';
   html += `<div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="ModalFormLabel" aria-hidden="true">`;
   html += `<div class="modal-dialog">`;
   html += `<div class="modal-content">`;
   html += `<form id="${modalFormId}">`;
   html += `<div class="modal-header">`;
   html += `<h5 class="modal-title">${modalTitle}</h5>`;
   html += `<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>`;
   html += `</div>`;
   html += `<div class="modal-body">`;
   html += `</div>`;
   html += `<div class="modal-footer">`;
   // html += `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>`;
   html += `<button type="submit" class="btn btn-primary">Simpan</button>`;
   html += `</div>`;
   html += `</form>`;
   html += `</div>`;
   html += `</div>`;
   html += `</div>`;
   
   $('#modal-placement').empty().append(html);
}

function requestData(method, url, data=null, done, fail=null, always=null){
   if (!isDemo) {
      data = data ? data : postData;
      if (method === 'post') {
         const req = $.post(url, data, done).fail((resp) => {
            notif('error', 'Error', JSON.parse(resp.responseText).message);
            if (fail === 'logout') {
               setTimeout(() => {
                  window.location.href = `${baseL}/logout`;
               }, 4000);
            }
         });
         if (always) {
            always();
         } else {
            req.always(() => $.unblockUI());
         }
   
      } else if (method === 'delete') {
         $.ajax({
            url: url,
            type: 'DELETE',
            data: data
          }).done(done).fail((resp) => {
            notif('error', resp.statusText, JSON.parse(resp.responseText).message);
          }).always(() => $.unblockUI());
      }
   } else {
      $.unblockUI();
   }
}