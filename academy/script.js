$(document).ready(function(){
	if (typeof(Storage) !== 'undefined') {
		$phone = localStorage.getItem('phone');
		$password = localStorage.getItem('password');
		$is_save = localStorage.getItem('save-password');

		$('#phone').val($phone);
		$('#password').val($password);
		$('#save-password').prop('checked', $is_save);
	}
});

function beforeSubmit() {
	$phone = $('#phone').val();
	$password = $('#password').val();
	$is_save = $('#save-password').prop('checked');

	if ($is_save) {
		if (typeof(Storage) !== "undefined") {
		  localStorage.setItem("phone", $phone);
		  localStorage.setItem("password", $password);
		  localStorage.setItem("save-password", $is_save);
		}
	} else {
		if (typeof(Storage) !== "undefined") {
			localStorage.removeItem('phone');
			localStorage.removeItem('password');
			localStorage.removeItem('save-password');
		}
	}

	return true;
}


$(document).on('click', '.groups.active', function() {
	$group_id = $(this).data('id');
	console.log($group_id);
	$.ajax({
		url: "controller.php?get_subtopics_by_group="+$group_id,
		beforeSend:function() {
			$('#topic-list .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS) {
			data = $.parseJSON(dataS);
			if (data.success) {
				$subtopics = data.subtopics;
	    		$html = "<table class='table table-striped table-bordered'>";
	    		$.each($subtopics, function(id, item) {
	    			// item.id item.title
	    			if (item.learned2 == 1) {
	    				$date = item.learned_date;
	    			} else {
	    				$date = item.will_learn_date;
	    			}
	    			$disabled = false;
	    			if (item.learned == '1' && item.learned2 == '1') {
	    				$disabled = true;
	    			}
	    			$extra_html = '';
	    			if (item.learned == '0' && item.learned2 == '1') {
	    				$extra_html = "<br><i class='text-success'>Қазір группа осы тақырыпта</i>";
	    			}
	    			$html += "<tr "+($disabled ? "style='background-color: #eee; color: #888;'" : '')+">";
	    				$html += "<td>"+item.title+$extra_html+"</td>";
	    				$html += "<td>"+$date+"</td>";
	    			$html += "</tr>";
	    		});
	    		$html += "</table>";
	    		$('#topic-list .modal-body').html($html);
			} else {
				$('#topic-list .modal-body').html("<center><b>ERROR</b></center>");
			}
		}
	});
});


$(document).on('click', '.future-group', function() {
	$topic_id = $(this).data('id');
	$group_id = $(this).data('group-id');
	$start_date = $(this).data('start-date');
	$.ajax({
		url: "controller.php?get_subtopics_by_topic_id&topic_id="+$topic_id+'&group_id='+$group_id+'&start_date='+$start_date,
		beforeSend:function() {
			$('#topic-list .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS) {
			data = $.parseJSON(dataS);
			if (data.success) {
				$subtopics = data.subtopics;
	    		$html = "<table class='table table-striped table-bordered'>";
	    		$.each($subtopics, function(id, item) {
	    			$html += "<tr>";
	    				$html += "<td>"+item.title+"</td>";
	    				$html += "<td>"+item.will_learn_date+"</td>";
	    			$html += "</tr>";
	    		});
	    		$html += "</table>";
	    		$('#topic-list .modal-body').html($html);
			} else {
				$('#topic-list .modal-body').html("<center><b>ERROR</b></center>");
			}
		}
	});
});




$selected_reserve = [];
$selected_reserve_content = {};

$(document).ready(function() {
	if (typeof(Storage) !== "undefined") {
		localStorage.removeItem('selected_reserve');
		localStorage.removeItem('selected_reserve_content');
	}
});

$(document).on('click', '.subject-box-btn', function() {
	$subject_id = $(this).data('subject');
	$subject_title = $(this).text();
	$.ajax({
		url: 'controller.php?get_topic_by_subject='+$subject_id,
		beforeSend: function() {
			$('#reserve-topic .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS) {
			data = $.parseJSON(dataS);
			if (data.success) {
				$html = "<form><table class='table table-condensed'>";
				$.each(data.topics, function(i, item) {
					$checked = '';
					$content_color = "";
					$value = $subject_id+'-'+item.id;
					if ($selected_reserve.includes($value)) {
						$checked = 'checked';
						$content_color = 'success';
					}
					$html += "<tr style='cursor: pointer; width: 100%;' class='reserve-topic-radio "+$content_color+"'>";
						$html += "<td style='width: 5%;'><input type='radio' "+$checked+" class='topic-radio' name='topic' value='"+$value+"' data-subject='"+$subject_title+"' data-topic='"+item.title+"'></td>";
						$html += "<td style='width: 70%;'>"+item.title+"</td>";
						$html += "<td style='width: 25%;'>Ұзақтығы: "+item.subtopic_count+" сабақ</td>";
					$html += "</tr>";
				});
				$html += "</form></table>";
				$('#reserve-topic .title').html($subject_title);
				$('#reserve-topic .modal-body').html($html);
			} else {
				$('#reserve-topic .modal-body').html('<center><b>ERROR</b></center>');
			}
		}
	});
});
$(document).on('click', '.reserve-topic-radio', function() {
	$(this).find('.topic-radio').prop('checked', true);
	$(this).parent().find('.reserve-topic-radio').removeClass('success');
	$(this).addClass('success');
	$value = $(this).find('.topic-radio').val();
	$subject_title = $(this).find('.topic-radio').data('subject');
	$topic_title = $(this).find('.topic-radio').data('topic');
	$.each($(this).parent().find('.reserve-topic-radio .topic-radio'), function(i, elem) {
		$removeItem = $(this).val().split('-')[0];
		$selected_reserve = jQuery.grep($selected_reserve, function(value) {
			return value.split('-')[0] != $removeItem;
		});
	});
	$selected_reserve.push($value);
	$selected_reserve_content[$value] = {'subject_title': $subject_title, 'topic_title': $topic_title};
});

$(document).on('click', '.select_topic_and_register_btn', function() {
	if (typeof(Storage) !== "undefined") {
		localStorage.removeItem('selected_reserve');
		localStorage.removeItem('selected_reserve_content');
		localStorage.setItem('selected_reserve', JSON.stringify($selected_reserve));
		localStorage.setItem('selected_reserve_content', JSON.stringify($selected_reserve_content));
	}
	window.location.assign("registration.php");
});

$(document).on('click', '.future-group-register', function() {
	$topic_id = $(this).data('topic-id');
	$topic_title = $(this).data('topic-title');
	$subject_id = $(this).data('subject-id');
	$subject_title = $(this).data('subject-title');

	$value = $subject_id+'-'+$topic_id;
	$selected_reserve.push($value);
	$selected_reserve_content[$value] = {'subject_title': $subject_title, 'topic_title': $topic_title};

	if (typeof(Storage) !== "undefined") {
		localStorage.removeItem('selected_reserve');
		localStorage.removeItem('selected_reserve_content');
		localStorage.setItem('selected_reserve', JSON.stringify($selected_reserve));
		localStorage.setItem('selected_reserve_content', JSON.stringify($selected_reserve_content));
	}
	window.location.assign("registration.php");
});
