$(document).on('change', '#group-name', function() {
	set_group_info_and_validation();
});

$(document).on('change', '#add-group-form select[name=subject]', function() {

	$subject_id = $(this).val();
	set_topic_selection($subject_id);

	set_subject_topic_info();
	set_group_info_and_validation();
});

$(document).on('change', '#add-group-form select[name=topic]', function() {
	set_subject_topic_info();
	set_group_info_and_validation();
});

$(document).on('click', '.add-student-to-group', function() {
	$id = $(this).data('id');
	$root_elem = $('.list-students').find('a[data-id=' + $id + ']').parents('li');
	$full_name = $root_elem.find('span').text();
	
	$('.students-in-group #datas').append("<input type='hidden' name='student_id[]' value='" + $id + "'>");
	$html = "<li style='margin: 5px 10px;'>";
	$html += "<span data-id='" + $id + "'>" + $full_name + "</span>";
	$html += "<a class='btn btn-xs btn-danger pull-right remove-student-from-group' data-id='" + $id + "'><span class='glyphicon glyphicon-arrow-left'></span> Группадан шығару</a>";
	$html += "</li>";
	$('.students-in-group #display ol').append($html);
	$root_elem.remove();
	set_group_info_and_validation();
});

$(document).on('click', '#group-schedule a', function() {
	$id = $(this).data('week-id');
	$data_elem = $('#group-schedule-data');
	if ($data_elem.find('input[value=' + $id + ']').length == 0) {
		$data_elem.append("<input type='hidden' name='week_id[]' value='" + $id + "'>");
		$(this).removeClass('btn-default').addClass('btn-success').addClass('active');
	} else {
		$data_elem.find('input[value=' + $id + ']').remove();
		$(this).removeClass('active').removeClass('btn-success').addClass('btn-default');
	}
	set_group_info_and_validation();
});

$(document).on('focus', '#group-start-date', function() {
	$(this).datepicker({
		format: 'dd.mm.yyyy',
		daysOfWeekDisabled: "0",
		daysOfWeekHighlighted: "0",
		todayHighlight: true,
		language: "ru",
		autoclose: true,
		maxViewMode: 0,
		todayBtn: "linked"
	});
});

$(document).on('change', '#group-start-date', function() {
	set_group_info_and_validation();
});

$(document).on('click', '#add-group-btn', function() {
	$('#add-group-form').removeClass('hide');
	$(this).hide();
});

$(document).on('click', '#cancel-add-group-form', function() {
	$('#add-group-form').addClass('hide');
	$('#add-group-btn').show();
});

$(document).on('submit', '#add-group-form form', function($e) {
	$e.preventDefault();
	$elem = $(this);
	if (set_group_info_and_validation() || true) {
		$form_data = new FormData(this);
		$.ajax({
			type: 'POST',
			url: 'group/controller.php?create-group',
			data: $form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function() {
				set_load($elem);
			},
			success: function($data) {
				console.log($data);
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					location.reload();
				} else {
					$subject_elem = $('#subject-err').text($json.error.subject.message);
					$topic_elem = $('#topic-err').text($json.error.topic.message);
					$week_id_elem = $('#week-id-err').text($json.error.week_id.message);
					$group_start_date_elem = $('#group-start-date-err').text($json.error.group_start_date.message);
				}
			}
		});
	}
});

$(document).on('click', '.show-group-info', function() {
	$elem = $(this).parents('tr').find('.full-info');
	if ($elem.hasClass('hide')) {
		$elem.removeClass('hide');
	} else {
		$elem.addClass('hide');
	}
});

$(document).on('click', '.transfer-students', function() {
	$group_id = $(this).data('id');
	$current_element = $(this);
	$modal = $('#transfer-students-modal');
	$modal.find('#transfer-to-form').removeClass('hidden');
	$modal.find('.alert').addClass('hidden');
	$.ajax({
		type: 'GET',
		url: 'group/controller.php?get_groups&group_id='+$group_id,
		beforeSend: function() {
			set_load($modal);
		},
		success: function($data) {
			$json = $.parseJSON($data);
			remove_load();
			if ($json.success) {
				$html = "<option value=''>Таңдаңыз</option>";
				$.each($json.result, function($i, $val) {
					$html += "<option value='"+$val.group_id+"'>"+$val.group_name+"</option>";
				});
				$modal.find('select[name=group]').html($html);
				$modal.find('input[name=group-id]').val($group_id);
			}
		}
	});
});

$(document).on('submit', '#transfer-to-form', function($e) {
	$e.preventDefault();
	console.log('okkkk');
	$modal = $(this).parents('.modal');
	// $current_element.parents('.modal').modal('hide');
	if (confirm('Группадағы оқушыларды таңдаған группаға көшіруге келісесізба?')) {
		$.ajax({
			type: 'POST',
			url: 'group/controller.php?transfer_students',
			data: new FormData(this),
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function() {
				set_load($modal);
			},
			success: function($data) {
				$json = $.parseJSON($data);
				if ($json.success) {
					$modal.find('#old-group').html("<span>"+$json.result.old_group+"</span>");
					$modal.find('#new-group').html("<span>"+$json.result.new_group+"</span>");
					$modal.find('.alert').removeClass('hidden');
					$modal.find('#transfer-to-form').addClass('hidden');
					remove_load();
				}
			}
		});
	}
});

$(document).on('change', '#group-army', function() {
	$checked = $(this).prop('checked');
	$group_info_id = $(this).val();
	$group_army_element = $(this);
	$.ajax({
		type: 'GET',
		url: 'group/controller.php?set_is_army='+$checked+'&group_info_id='+$group_info_id,
		beforeSend: function() {
			set_load($group_army_element.parents('tr'));
		},
		success: function($data) {
			console.log($data);
			$json = $.parseJSON($data);
			remove_load();
			if ($json.success) {
				if ($checked) {
					$group_army_element.parents('tr').css({'border': '2px solid #5B7742'});
				} else {
					$group_army_element.parents('tr').css({'border': 'none'});
				}
			}
		}
	});
});

$(document).on('change', '#group-marathon', function() {
	$checked = $(this).prop('checked');
	$group_info_id = $(this).val();
	$group_marathon_element = $(this);
	$.ajax({
		type: 'GET',
		url: 'group/controller.php?set_is_marathon='+$checked+'&group_info_id='+$group_info_id,
		beforeSend: function() {
			set_load($group_marathon_element.parents('tr'));
		},
		success: function($data) {
			console.log($data);
			$json = $.parseJSON($data);
			remove_load();
			if ($json.success) {
				if ($checked) {
					$group_marathon_element.parents('tr').css({'border': '2px solid #FF7E00'});
				} else {
					$group_marathon_element.parents('tr').css({'border': 'none'});
				}
			}
		}
	});
});