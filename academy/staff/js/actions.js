$(document).ready(function() {
	$navigation = get_url_params('nav');
	if ($navigation !== undefined) {
		if ($navigation == 'active-student-nav') {
			$student_phone = get_url_params('student-phone');
			$element = $('.'+$navigation).parents('.navigation');
			set_navigation($element);
		}
	}
});

function tableRemoveRow($element) {
	$el = $element.parents($element);
	$element.remove();
	$count = 1;
	$el.find('tr').each(function() {
		$(this).find('.count').html($count++);
	});
}

$(document).on('click', '.reset-password-btn', function() {
	$elem = $(this);
	$student_id = $(this).data('id');
	$form_data = new FormData();
	$form_data.append('student_id', $student_id);
	if (confirm('Вы точно хотите сбросить пароль студента?')) {
		$.ajax({
			type: 'POST',
			url: 'student/controller.php?reset-student-password',
			data: $form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSuccess: function() {
				set_load('body');
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					function set_default_password_text($element) {
						$element.find('.password').html("<div class='password pull-right'><i class='text-warning'>Пороль: <b>12345</b></i></div>");
					}
					lightAlert($elem.parents('.std-info'), 'green', 0, 500, set_default_password_text);
				}
			}
		});
	}
});

$(document).on('keyup', 'input[name=search_student]', function() {
	$val = $(this).val().toLowerCase();
	filter_student_list($val);	
});

function filter_student_list ($query) {
	$('.student-table').find('.student-short-info').each(function() {
		$student_info = $(this).text().toLowerCase();
		if (!$student_info.includes($query)) {
			$(this).parents('tr').hide();
		} else {
			$(this).parents('tr').show();
		}
	});
}
