// $marathon_form_arr = {};
// $(document).on('click', '.choose-group-btn', function () {
// 	$marathon_form_id = $(this).data('id');
// 	$('#choose-group input[name=marathon-form-id]').val($marathon_form_id);
// 	if ($marathon_form_arr[$marathon_form_id] === undefined) {
// 		$marathon_form_arr[$marathon_form_id] = [];
// 	}

// 	$('#choose-group input[name="group_infos[]"]').each(function() {
// 		$group_info_id = $(this).val();
// 		if ($marathon_form_arr[$marathon_form_id].includes($group_info_id)) {
// 			$(this).prop('checked', true);
// 		} else {
// 			$(this).prop('checked', false);
// 		}
// 	});
// });

// $(document).on('change', 'input[name="group_infos[]"]', function() {
// 	$group_info_id = $(this).val();
// 	$marathon_form_id = $(this).parents('.modal-body').find('input[name=marathon-form-id]').val();
// 	if ($(this).prop('checked')) {
// 		if (!$marathon_form_arr[$marathon_form_id].includes($group_info_id)) {
// 			$marathon_form_arr[$marathon_form_id].push($group_info_id);
// 		}
// 	} else {
// 		if ($marathon_form_arr[$marathon_form_id].includes($group_info_id)) {
// 			$marathon_form_arr[$marathon_form_id] = $marathon_form_arr[$marathon_form_id].filter(function(value, index, arr) {
// 				return value != $group_info_id;
// 			});
// 		}
// 	}
// 	set_group_info_to_html_table($marathon_form_id);
// });

function set_group_info_to_html_table ($marathon_form_id) {
	$group_info_arr = {};
	$('#choose-group input[name="group_infos[]"]').each(function() {
		$group_info_id = $(this).val();
		$group_info_name = $(this).parents('label').find('.group-name').text();
		$group_info_arr[$group_info_id] = $group_info_name;
	});

	if ($marathon_form_arr[$marathon_form_id] !== undefined) {
		$html = "";
		$.each($marathon_form_arr[$marathon_form_id], function($index, $value) {
			$html += "<p>"+$group_info_arr[$value]+"</p>";
		});
		$('#mf-id-'+$marathon_form_id).find('.choosen-groups').html($html);
	}
}

$(document).on('click', '.remove-marathon-student', function() {
	$marathon_form_id = $(this).data('id');
	$current_element = $(this);
	if (confirm('Оқушының анкетасын өшіруге келісесінба?')) {
		$.ajax({
			type: 'GET',
			url: 'marathon_form/controller.php?remove_marathon_student&marathon_form_id='+$marathon_form_id,
			cache: false,
			beforeSuccess: function() {
				set_load('body');
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					lightAlert($current_element.parents('tr'), 'red', 0, 500, function() {
						$current_element.parents('tr').remove();
					});
				}
			}
		});
	}
});


$(document).on('click', '.submit-marathon-student', function() {
	$marathon_form_id = $(this).data('id');
	$current_element = $(this);
	$checked = false;
	$marathon_form_arr = [];
	$(this).parents('tr').find('#mf-id-'+$marathon_form_id).each(function() {
		$(this).find('input[name="group_infos[]"]').each(function() {
			$element_checked = $(this).prop('checked');
			if ($element_checked) {
				$marathon_form_arr.push($(this).val());
			}
		});
	});
	// if ($marathon_form_arr[$marathon_form_id] !== undefined) {
	console.log($marathon_form_arr, $marathon_form_arr.length);
	if ($marathon_form_arr.length > 0) {
		if (confirm("Оқушыны таңдаған группаларға байланысты марафонға қосуға келісесінба?")) {
			$group_infos = JSON.stringify($marathon_form_arr);
			// console.log($group_infos);
			$form_data = new FormData();
			$form_data.append('group_infos', $group_infos);
			$.ajax({
				type: 'POST',
				url: 'marathon_form/controller.php?submit_marathon_student&marathon_form_id='+$marathon_form_id,
				data: $form_data,
				contentType: false,
				cache: false,
				processData: false,
				beforeSuccess: function() {
					set_load('body');
				},
				success: function($data) {
					console.log($data);
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parents('tr'), 'green', 0, 500, function() {
							$current_element.parents('tr').remove();
						});
					}
				}
			});
		}
	} else {
		alert('Марафонға қоспас бұрын группаны таңдау керек');
	}
});