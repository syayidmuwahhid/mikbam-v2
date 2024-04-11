$(document).ready(function(){
	$("#ip-address").keypress((e) => validateIP(e));

	$('#btn-scan').click(() => {
		console.log(scanningResult);
	});

	$('#btn-next').click(() => {
		const ip = $('#ip-address').val();
		
		if (!ip) {
			notif('warning', 'IP Address Wajib diisi');
		} else {
			$('#login_title').html(`Login Router ${ip}`);
			$('#ip-container').addClass('d-none');
			$('#username-container').removeClass('d-none');
		}
	});
	
	$('#btn-back').click(() => {
			$('#ip-container').removeClass('d-none');
			$('#username-container').addClass('d-none');
	});

	$("#form-login").submit(function(e){
		e.preventDefault();
		const form = $(this);
		// const post_data = new FormData(form[0]);
		const post_data = new FormData();
		post_data.append('host', $('#ip-address').val());
		post_data.append('user', $('#username').val());
		post_data.append('pass', $('#password').val());
		post_data.append('port', $('#port').val());
		
		error = 0;

		if (error === 0) {
			$.ajax({
				url : `${baseL}/api/login`,
				type: 'post',
				data : post_data,
				processData: false,
				contentType: false,
				dataType:"JSON",
				beforeSend: function(){
					blokUI('Data sedang di proses . . ');
				},
				success: function(resp) {
					if (resp.status == 'success')
					{
							notif('success', resp.message);
							simpanSesi('login-data', JSON.stringify(resp.data));
							simpanSesi('timeout', 10000);
							simpanSesi('sumber-internet', 'ether1');
							window.location.href=`${baseL}`;
					}else{
							notif('error', 'Kesalahan', resp.message);
					}
				},
				error: function(resp){
					notif('error', 'Kesalahan', resp.statusText);
				},
				complete: function() {
					$.unblockUI();
				}
			});
		}		
	});
});