$(document).on('click', '.not-activated-student', function() {
	$action = $(this).data('action');

	// if ($action == 'confirm-accept') {
	// 	lightAlert($(this).parents('tr'), 'green', 0.4, 500);
	// 	$(this).parents('.confirm-box').addClass('hide');
	// 	$(this).parents('td').find('.accept-box').removeClass('hide');
	// } else 
	if ($action == 'cancel-accept') {
		lightAlert($(this).parents('tr'), 'orange', 0, 300);
		$(this).parents('.accept-box').addClass('hide');
		$(this).parents('td').find('.confirm-box').removeClass('hide');
	} else if ($action == 'confirm-delete') {
		if (confirm('Оқушыны өшіуге сенімдісіңбе?')) {
			$current_element = $(this);
			$id = $(this).data('id');
			$form_data = new FormData();
			$form_data.append('id', $id);
			$form_data.append('remove', 'not-activated-student');
			$.ajax({
				url: 'student/profile/controller.php',
				type: 'POST',
				data: $form_data,
				contentType: false,
    	    	cache: false,
				processData: false,
				beforeSend: function() {
					set_load($current_element.parents('table'));
				},
				success: function($data) {
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parents('tr'), 'red', 0, 500, tableRemoveRow);
					}
				}
			});
		}
	} else if ($action == 'confirm-accept') { //accept
		$current_element = $(this);
		$id = $(this).data('id');
		$form_data = new FormData();
		$form_data.append('id', $id);
		$form_data.append('action', 'accept_student');
		$.ajax({
			url: 'student/profile/controller.php',
			type: 'POST',
			data: $form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function() {
				set_load($current_element.parents('table'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					lightAlert($current_element.parents('tr'), 'green', 0, 500, tableRemoveRow);
				}
			}
		});
	}
});