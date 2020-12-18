$(document).on('click', '.comander-avatar, .army-student-avatar', function() {
	$fio = $(this).data('user-fio');
	$rank = $(this).data('rank');
	$src = $(this).attr('src');

	$header_html = "<center><span>"+$fio+"</span> | <span>"+$rank+"</span></center>";
	$body_html = "<img style='width: 100%; height: auto;' src='"+$src+"'>";
	$('#army-user-info').find('.modal-title').html($header_html);
	$('#army-user-info').find('.modal-body').html($body_html);
});

$(document).on('change', '#student-avatar-file', function() {
	$img_fake_path = $(this).val();
	$file_extentions = ['jpeg', 'jpg', 'png', 'gif', 'JPEG', 'JPG', 'PNG', 'GIF'];
	if ($img_fake_path != '') {
		if ($(this).prop('files')[0].size < 5242880) {
			if ($file_extentions.includes($(this).val().split('.').pop()) == 1) {
				$form_data = new FormData();
				$form_data.append('avatar_img', $(this).prop('files')[0]);
				$.ajax({
					url: 'army_diagram/controller.php?upload_avatar',
					type: 'POST',
					data: $form_data,
					processData: false,
					contentType: false,
					cache: false,
					beforeSend: function() {
						set_load($('body'));
					},
					success: function($data) {
						console.log($data);
						$json = $.parseJSON($data);
						remove_load();
						$ab_root = $('input[name=ab-root]').val();
						if ($json.success) {
							$.each($('.self-avatar-no-img'), function() {
								$fio = $(this).data('user-fio');
								$rank = $(this).data('rank');
								$html = "<img data-toggle='modal' data-target='#army-user-info' data-user-fio='"+$fio+"' data-rank='"+$rank+"' class='army-student-avatar self-avatar-img' src='"+$ab_root+"/academh/'"+$json.avatar_link+" />";
								$(this).after($html);
								$(this).remove();
							});
							$.each($('.self-avatar-img'), function() {
								$(this).attr('src', $ab_root+'/academy/'+$json.avatar_link);
							});
						} else {
							if ($json.message != '') {
								error_alert_message($json.message);
							}
						}
					}
				});
			} else {
				error_alert_message('Жүктелген суреттің типі "jpeg", "jpg", "png", "gif" болу керек.');
			}
		} else {
			error_alert_message('Жүктелген суреттің салмағы 5 мб (мега бит) тан көп болмауы керек');
		}
	}
});

function error_alert_message($message) {
	Swal.fire({
		width: '50em',
		title: $message,
		icon: 'error'
	});
}
