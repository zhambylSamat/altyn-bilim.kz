$(document).on('click', '.notification-select', function() {
	$current_element = $(this);
	$data_id = $(this).data('id');
	$data_type = $(this).data('type');

	$form_data = new FormData();
	$form_data.append('id', $data_id);
	$form_data.append('type', $data_type);
	if (confirm('Ескертілдіма?')) {
		$.ajax({
			type: 'POST',
			method: 'POST',
			url: 'notification/controller.php?notification_select',
			data: $form_data,
			contentType: false,
		    cache: false,
			processData:false,
			beforeSend: function() {
				set_load($current_element.parents('table'));
			},
			success: function($data) {
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					$html = "<br><b><i class='text-success'>Ескертілді</i></b>";
					$current_element.parents('.notification-select-content').html($html);
				}
			}
		});
	}
});

$(document).on('click', '.remove-no-progress-student-notification', function() {
	$npsn_id = $(this).data('id');
	$this = $(this);
	if (confirm('Оқушыға ескертілдіма?')) {
		$.ajax({
			type: "GET",
			url: 'notification/controller.php?remove_no_progress_student_notification&npsn_id='+$npsn_id,
			beforeSend: function() {
				set_load($(this).parents('.table'));
			},
			success: function($data) {
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					function remove_content_row($element) {
						$element.remove();
					}
					lightAlert($this.parents('tr'), 'green', 0, 500, remove_content_row);
				}
			}
		});
	}
});