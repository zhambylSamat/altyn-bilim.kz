$weeks = ['', 'Пн.', 'Вт.', 'Ср.', 'Чт.', 'Пт.', 'Сб.', 'Вс.'];

function set_topic_selection($subtopic_id) {
	if ($subject_id != '') {
		$topic_type = $('#add-group-form #topic');
		$topic_type.find('select').html("<option value=''>Загрузка...</option>");
		$.when(get_topics_by_subject_id($subject_id)).done(function($result) {
			$topics = $.parseJSON($result);
			if ($topics.success) {
				$html = "<option value=''>Тарауды таңдаңыз</option>";
				for ($i = 0; $i < $topics.data.length; $i++) {
					$html += "<option value='" + $topics.data[$i]['id'] + "'>" + $topics.data[$i]['title'] + "</option>";
				}
				$topic_type.find('select').html($html).removeAttr('disabled');
			}
		});
	} else {
		$topic_type.find('select').attr('disabled', 'true');
		$topic_type.find('select').html("<option value=''>Пәнді таңдаңыз</option>");
	}
}

function get_topics_by_subject_id($subject_id) {
	return $.ajax({
		url: 'group/view.php?topics-by-subject-id&subject_id=' + $subject_id,
		type: 'GET'
	});
}

function set_subject_topic_info($subject_id, $topic_id, $lesson_type) {
	$subject_id = $('#add-group-form select[name=subject]').val();
	$topic_id = $('#add-group-form select[name=topic]').val();
	$html_content = $('#add-group-form .lesson-content-info');
	$.when(get_subject_topic_info($subject_id, $topic_id, $html_content))
	.done(function($result) {
		$html = "";
		if ($result != '') {
			$('#group-start-date').removeAttr('disabled');
			$materials = $.parseJSON($result);
			if ($materials.success) {
				$html += "<p style='font-size: 20px;'>Барлығы <b>" + $materials.content_result.topic_count + "</b> тарау ";
				$html += "<b id='subtopic-count-info'>" + $materials.content_result.total_subtopic_count + "</b> тақырып</p>";
				$has_not_contents = false;
				$html_no_content = "<p class='text-danger' style='font-size: 20px; font-weight: bold;'><span class='glyphicon glyphicon-arrow-down'></span> Материалдары толық емес тақырыптар <span class='glyphicon glyphicon-arrow-down'></span></p><ol>";
				$.each($materials.content_result.topics, function($i, $topic_val) {
					$subtopic_count = $topic_val.subtopic_count;
					$topic_title = $topic_val.title;
					if ($subtopic_count == 0) {
						$has_not_contents = true;
						$html_no_content += "<li>Тақырып жоқ!</li>";
					} else {
						$has_not_materials = false;
						$html_no_material = "<table class='table table-bordered table-striped' style='font-size: 15px;'>";
						$html_no_material += "<tr><th>Тақырып</th><th>Тақырыптық видео</th><th>Тақырыптық файлдар</th><th>Қорытынды видео</th></tr>";
						$.each($topic_val.subtopics, function($j, $subtopic_val){
							if (($subtopic_val.tutorial_video_count == 0 ||
									$subtopic_val.tutorial_document_count == 0 ||
									$subtopic_val.end_video_count == 0) 
								&& ($subtopic_val.tutorial_video_config == 0 ||
									$subtopic_val.tutorial_document_config == 0 ||
									$subtopic_val.end_video_config == 0)) {
								$has_not_contents = true;
								$has_not_materials = true;
								$icon_ok = "<span class='glyphicon glyphicon-ok text-success'></span>";
								$icon_minus = "<span class='glyphicon glyphicon-minus text-success'></span>";
								$icon_remove = "<span class='glyphicon glyphicon-remove text-danger'></span>";

								$html_no_material += "<tr style='padding: 0px 10px;'>";
									$html_no_material += "<td style='text-align: left; padding: 0px 10px;'>";
									$html_no_material += "<b>" + $subtopic_val.title + "</b>"
									$html_no_material += "</td>";

									$html_no_material += "<td style='padding: 0px 10px;'>";
									if ($subtopic_val.tutorial_video_config > 0) {
										$html_no_material += $icon_minus;
									} else {
										$html_no_material += ($subtopic_val.tutorial_video_count == 0 ?
																$icon_remove :
																$icon_ok);
										$html_no_material += " <span>" + $subtopic_val.tutorial_video_count + " видео</span>";
									}
									$html_no_material += "</td>";

									$html_no_material += "<td style='padding: 0px 10px;'>";
									if ($subtopic_val.tutorial_document_config > 0) {
										$html_no_material += $icon_minus;
									} else {
										$html_no_material += ($subtopic_val.tutorial_document_count == 0 ?
																$icon_remove :
																$icon_ok);
										$html_no_material += " <span>" + $subtopic_val.tutorial_document_count + " файл</span>";
									}
									$html_no_material += "</td>";

									$html_no_material += "<td style='padding: 0px 10px;'>";
									if ($subtopic_val.end_video_config > 0) {
										$html_no_material += $icon_minus;
									} else {
										$html_no_material += ($subtopic_val.end_video_count == 0 ?
																$icon_remove :
																$icon_ok);
										$html_no_material += " <span>" + $subtopic_val.end_video_count + " видео</span>";
									}
									$html_no_material += "</td>";
								$html_no_material += "</tr>";
							}
						});
						$html_no_material += "</table>";
						if ($has_not_materials) {
							$html_no_content += "<li>";
							$html_no_content += "<p style='text-align: left; font-size: 20px; font-weight: bold;'>" + $topic_val.title + "</p>";
							$html_no_content += $html_no_material;
							$html_no_content += "</li>";
						}
					}
				});
				$html_no_content += "</ol>";
				if ($has_not_contents) {
					$html += $html_no_content;
				}
			}
		} else {
			$('#group-start-date').attr('disabled', 'true');
			$('#group-start-date').val('');
		}
		$html_content.html($html);
	});
}

function get_subject_topic_info($subject_id, $topic_id, $html_content) {
	$params = '?content-info&subject_id=' + $subject_id;
	if ($topic_id == '') {
		return '';
	} else {
		$params += '&topic_id=' + $topic_id;
	}
	return $.ajax({
		url: 'group/view.php' + $params,
		type: 'GET',
		beforeSend: function() {
			set_load($html_content);
		},
		success: function() {
			remove_load();
		}
	});
}

function set_group_info_and_validation() {
	$start_date_str = $('#group-start-date').val();
	$subtopic_count = $('#subtopic-count-info').text();
	$subtopic_count = $subtopic_count == '' ? 0 : parseInt($subtopic_count);

	$week_ids = [];
	$('#group-schedule-data').find('input').each(function(){
		$week_ids.push($(this).val());
	});
	$week_ids.sort();

	$start_date = set_start_date_info($start_date_str);
	set_end_date_info($start_date, $week_ids, $subtopic_count);
	$has_week_id = set_week_id_info($week_ids);

	$validation = ($start_date != undefined &&
				$has_week_id);
	if ($validation) {
		$('#save-group-btn').removeClass('hide');
		return true;
	} else {
		$('#save-group-btn').addClass('hide');
		return false;
	}
}

function set_start_date_info($start_date_str) {
	$start_date = undefined;
	if ($start_date_str != '') {
		$start_date_arr = $start_date_str.split('.');
		$start_date = new Date($start_date_arr[2] + '-' + $start_date_arr[1] + '-' + $start_date_arr[0]); //YYYY-MM-DD
	}

	if ($start_date != undefined) {
		$html = '<h4>Алғашқы сабақ күні: <b>' + $start_date_str + '</b>';
		$html += '</h4>';
		$('#start-course-date-info').html($html);
	} else {
		$('#start-course-date-info').html('');
		$('#end-course-date-info').html();
	}

	return $start_date;
}

function set_end_date_info($start_date, $week_ids, $subtopic_count) {
	if ($start_date != undefined && $week_ids.length != 0 && $subtopic_count != 0) {
		$end_date = $start_date;
		$end_date.setDate($end_date.getDate() - 1);
		$days = 0;
		while ($days < $subtopic_count) {
			$end_date.setDate($end_date.getDate() + 1);
			$day = $end_date.getDay() == 0 ? '7' : $end_date.getDay().toString();
			if ($week_ids.includes($day)) {
				$days++;
			}
		}

		$dd = $end_date.getDate();
		$mm = $end_date.getMonth() + 1;
		$yyyy = $end_date.getFullYear();
		$dd = parseInt($dd) < 10 ? $dd = '0' + $dd : $dd.toString();
		$mm = parseInt($mm) < 10 ? $mm = '0' + $mm : $mm.toString();
		$end_date_str = $dd + '.' + $mm + '.' + $yyyy;

		$html = '<h4>Соңғы сабақ күні: <b>' + $end_date_str + '</b>';
		$html += '</h4>';
		$('#end-course-date-info').html($html);
	} else {
		$('#end-course-date-info').html('');
	}
}

function set_week_id_info($week_ids) {
	if ($week_ids.length > 0) {
		$html = '<h4>';
		$.each($week_ids, function($i, $val){
			$html += '<b>' + $weeks[parseInt($val)] + ' </b>';
		});
		$html += ': күндері сағат 7:00 де <br> тақырыпқа байланысы материалдар автоматты түрде ашылады.';
		$html += '</h4>';
		$('#week-id-info').html($html);
		return true;
	} else {
		$('#week-id-info').html('');
		return false;
	}
}