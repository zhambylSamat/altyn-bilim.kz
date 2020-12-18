$(document).ready(function() {
	$error_notification_count = '';
	$is_mobile = $('body').width() < 768;
	$.ajax({
		url: 'notification/view.php?notifications_count',
		type: 'GET',
		cache: false,
		success: function($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				$html = "";
				$total_notification_count = 0;
				if ($json.error_notifications_count > 0) {
					if ($is_mobile) {
						$html += "<span class='label error-sn' style='background-color: #E31C27; display:block; margin-bottom: 5px;'>"+$json.error_notifications_count+"</span>";
					} else {
						$html += "&nbsp;<span class='label error-sn' style='background-color: #E31C27;'>"+$json.error_notifications_count+"</span>";
					}
					$total_notification_count += $json.error_notifications_count;
					set_group_student_no_home_work_notification_pop_up($json.group_student_no_home_work_notification);
					set_group_student_discount_notification_pop_up($json.group_student_discount_notification);
					console.log($json.group_student_discount_notification);
				}
				if ($json.success_notifications_count > 0) {
					if ($is_mobile) {
						$html += "<span class='label success-sn' style='background-color: #61BA67; display:block; margin-bottom: 5px;'>"+$json.success_notifications_count+"</span>";	
					} else {
						$html += "&nbsp;<span class='label success-sn' style='background-color: #61BA67;;'>"+$json.success_notifications_count+"</span>";
					}
					
				}

				if ($total_notification_count > 0) {
					$('.student-notification').addClass('pulse');
				}

				$('.student-notification').append($html);

				// render_submitted_tests();
			}
		}
	});
});

function set_group_student_no_home_work_notification_pop_up ($data) {
	$.each($data, function($i, $val) {
		if ($val.is_notified == 0) {
			$text = $val.text;
			Swal.fire({
				width: '50em',
				title: $text,
				icon: 'warning'
			});
		}
	});
}

function set_group_student_discount_notification_pop_up ($data) {
	if ($data.is_notified == 0) {
		Swal.fire({
			width: '50em',
			title: $data.notification_text,
			icon: 'success',
			iconHtml: '<i class="fas fa-gift"></i>'
		});
	}
}