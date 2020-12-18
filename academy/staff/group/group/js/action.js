$(document).on('change', '#select-subtopic', function() {
	$lesson_progress_id = $(this).val();
	set_students_log($lesson_progress_id);
});

$(document).on('change', '#select-topic', function() {
	$.urlParam = function(name){
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		return results[1] || 0;
	}
	$topic_id = $(this).val();
	$group_id = $.urlParam('group');

	$.when(get_lesson_progress($group_id, $topic_id)).done(function($result){
		$json = $.parseJSON($result);
		$html = "";
		if ($json.success) {
			$lesson_progress_id = 0;
			$.each($json.result, function($index, $element){
				if ($lesson_progress_id == 0) {
					$lesson_progress_id = $element.lp_id;
				}
				$html += "<option value='"+$element.lp_id+"'>"+$element.title+"</option>";
			});
			$('#select-subtopic').html($html);
			set_students_log($lesson_progress_id);
		}
	});
});

function get_lesson_progress($group_id, $topic_id) {
	$form_data = new FormData();
	$form_data.append('group_id', $group_id);
	$form_data.append('topic_id', $topic_id);
	return $.ajax({
		type: "POST",
		url: 'group/controller.php?get_lesson_progress',
		data: $form_data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load($('#select-subtopic'));
		},
		success: function($data) {
			remove_load();
		}
	});
}

function set_students_log($lesson_progress_id, $position='#students-table') {
	set_load($($position));
	$($position).load('group/group/components/student_list.php?lp_id='+$lesson_progress_id, function() {
		remove_load();
	});
}

function set_student_log($lesson_progress_id, $group_student_id, $element) {
	$.ajax({
		type: "GET",
		url: 'group/controller.php?get_single_student_log&lp_id='+$lesson_progress_id+'&gs_id='+$group_student_id,
		beforeSend: function() {
			set_load($element);
		},
		success: function($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				remove_load();
				$first_child = $element.children().first();
				$last_child = $element.children().last();
				$rowspan = parseInt($first_child.attr('rowspan')) + 1;
				$first_child.attr('rowspan', $rowspan);
				$last_child.attr('rowspan', $rowspan);
				$html = "<tr>";
				$html += "<td title='"+$json.result.progress_log.created_date+"'><center>"+$json.result.progress_log.created_date+"</center></td>";
				if ($json.result.tutorial_video_logs !== undefined) {
					$.each($json.result.tutorial_video_logs, function($i, $elem) {
						$html += "<td title='Видео көрілмеген'><center>-</center></td>";
					});
				}
				// if ($json.result.tutorial_document_logs !== undefined) {
				// 	$.each($json.result.tutorial_document_logs, function($i, $elem) {
				// 		$html += "<td title='Файл ашылмаған'><center>-</center></td>";
				// 	});
				// }
				if ($json.result.end_video_logs !== undefined) {
					$.each($json.result.end_video_logs, function($i, $elem) {
						$html += "<td title='Видео көрілмеген'><center>-</center></td>";
					});
				}
				$html += "</tr>";
				$last_child.html('<b class="text-success">Доступ ашық</b>');
				$element.after($html);
			}
		}
	});
}

$(document).on('click', '.reset-log', function() {
	$lp_id = $(this).closest('table').data('lp-id');
	$log_id = $(this).data('log-id');
	$obj = $(this).data('obj');
	if (confirm('Вы уверены заново открыть доступ?')) {
		$.when(reset_log($log_id, $obj)).done(function($result) {
			$json = $.parseJSON($result);
			if ($json.success) {
				set_students_log($lp_id);
			}
		});
	}
});

function reset_log($log_id, $obj) {
	return $.ajax({
		type: 'GET',
		url: 'group/controller.php?reset_log&log_id='+$log_id+'&obj='+$obj,
		beforeSend: function() {
			set_load($('#students-table'));
		},
		success: function($data) {
			remove_load();
		}
	});
}

$(document).on('click', '.reaccess-material', function() {
	$current_element = $(this);
	$current_parent = $(this).parents('tr');
	$lp_id = $(this).data('lp-id');
	$group_student_id = $(this).data('group-student-id');
	if (confirm('Вы уверены заново открыть доступ?')) {
		$.when(reaccess_material($lp_id, $group_student_id)).done(function($result) {
			console.log($result);
			$json = $.parseJSON($result);
			if ($json.success) {
				// set_students_log($lp_id);
				set_student_log($lp_id, $group_student_id, $current_parent);
			}
		});
	}
});

function reaccess_material($lesson_progress_id, $group_student_id) {
	return $.ajax({
		type: 'GET',
		url: 'group/controller.php?reaccess_material&lesson_progress_id='+$lesson_progress_id+'&group_student_id='+$group_student_id,
		cache: false,
		beforeSend: function() {
			set_load($('#students-table'));
		},
		success: function($data) {
			remove_load();
		}
	});
}

$(document).on('click', '.set-no-home-work-warning', function() {
	$group_student_id = $(this).data('gs-id');
	$lesson_progress_id = $(this).data('lp-id');
	$gsnhww_id = $(this).data('gsnhww-id');
	console.log($group_student_id, $lesson_progress_id, $gsnhww_id);
	$current_element = $(this);
	if (confirm("Оқушыға ескерту жасауға сенімдісіңба?")) {
		$.ajax({
			type: 'GET',
			url: 'group/controller.php?set_group_student_no_home_work_warning&lesson_progress_id='+$lesson_progress_id+'&group_student_id='+$group_student_id+'&gsnhww_id='+$gsnhww_id,
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('td'));
			},
			success: function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					$warning_count = $json.warning_count;
					$gsnhww_id = $json.gsnhww_id;
					$html = "";
					if ($json.student_deleted !== undefined) {
						$html += "<p class='text-danger' class='warning-setted-text'>Оқушы группадан шығарылды</p>";
					} else {
						if ($warning_count == 1) {
							$html += "<p class='text-danger' class='warning-setted-text'>"+$warning_count+"-ші ескерту жасалды</p>";
							$html += "<button class='btn btn-sm btn-warning undo-no-home-work-warning' data-gs-id='"+$group_student_id+"' data-lp-id='"+$lesson_progress_id+"' data-gsnhww-id='"+$gsnhww_id+"'>"+$warning_count+"-ші ескертуді болдырмау</button>";
						} else {
							$html += "<button class='btn btn-md btn-danger' class='set-no-home-work-warning' data-gs-id='"+$group_student_id+"' data-lp-id='"+$lesson_progress_id+"' data-gsnhww-id='"+$gsnhww_id+"'>Үй жұмысы жоқ. "+(++$warning_count)+"-ші ескерту</button>";
						}
					}
					$current_element_parents = $current_element.parents('td');
					lightAlert($current_element_parents, 'green', 0, 300);
					$current_element_parents.html($html);
				}
			}
		});
	}
});

$(document).on('click', '.undo-no-home-work-warning', function() {
	$lesson_progress_id = $(this).data('lp-id');
	$gsnhww_id = $(this).data('gsnhww-id');
	$current_element = $(this);
	if (confirm('Оқушының ескеруін кері қайтаруға келісесінба?')) {
		$.ajax({
			type: 'GET',
			url: 'group/controller.php?undo_group_student_no_home_work_warning&lesson_progress_id='+$lesson_progress_id+'&gsnhww_id='+$gsnhww_id,
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('td'));
			},
			success: function ($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					$html = "<p class='text-success'>Ескертуі алынып тасталды</p>";
					$current_element_parents = $current_element.parents('td');
					lightAlert($current_element_parents, 'green', 0, 300);
					$current_element_parents.html($html);
				}
			}
		});
	}
});

$(document).on('keyup', '#add-student-to-group input[name=phone]', function() {
	$phone = $(this).val();
	$group_info_id = $(this).parents('form').find('input[name=group_info_id]').val();
	$('#student-info-by-phone').attr('class', '');
	if ($phone.length == 10) {
		$.ajax({
			url: 'group/controller.php?get_student_info_by_phone&phone='+$phone+'&group_info_id='+$group_info_id,
			beforeSend:function() {
				$('#student-info-by-phone').text('Загрузка...');
			},
			success:function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					$('#student-info-by-phone').text($json.message);
					if ($json.style == 'success') {
						$('#student-info-by-phone').addClass('text-success');
					} else if ($json.style == 'warning') {
						$('#student-info-by-phone').addClass('text-warning');
					} else if ($json.style == 'error') {
						$('#student-info-by-phone').addClass('text-danger');
					}
				}
			}
		});
	} else {
		$('#student-info-by-phone').text('');
	}
});

$(document).on('click', '#open-add-student-to-group', function() {
	$(this).hide();
	$('#add-student-to-group').show();
});

$(document).on('click', '#cancel-add-student-to-group', function() {
	$('#open-add-student-to-group').show();
	$('#add-student-to-group').hide();
});

$(document).on('click', '.comander-avatar, .army-student-avatar', function() {
	$fio = $(this).data('user-fio');
	$rank = $(this).data('rank');
	$src = $(this).attr('src');

	$header_html = "<center><span>"+$fio+"</span> | <span>"+$rank+"</span></center>";
	$body_html = "<img style='width: 100%; height: auto;' src='"+$src+"'>";
	$('#army-user-info').find('.modal-title').html($header_html);
	$('#army-user-info').find('.modal-body').html($body_html);
});


$(document).on('click', '.transfer-group-btn', function() {
	$group_student_id = $(this).data('gs-id');
	
	$('#transfer-group-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");

	$.ajax({
		type: "GET",
		url: 'group/controller.php?get_available_groups_for_transfer&group_student_id='+$group_student_id,
		beforeSend: function() {
			set_load($('#transfer-group-modal'));
		},
		success: function($data) {
			console.log($data);
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				$('#transfer-group-modal .modal-title').html('<center>'+$json.info.subject_title+'</center>');
				$html = "<table class='table table-bordered table-striped'>";
				$count = 0;
				$.each($json.info.groups, function($group_info_id, $group_name) {
					$count++;
					$html += "<tr>";
						$html += "<td><span><center>"+$group_name+"</center></span></td>";
						$html += "<td><button class='btn btn-success btn-sm transfer-to-group' data-group-id='"+$group_info_id+"' data-group-student-id='"+$group_student_id+"'>Ауыстыру</button></td>";
					$html += "</tr>";
				});
				if ($count == 0) {
					$html += "<tr><td><center>Ауыстыратын группа жоқ</center></td></tr>";
				}
				$html += "</table>";
				$('#transfer-group-modal .modal-body').html($html);
			}
		}
	});
});

$(document).on('click', '.transfer-to-group', function() {
	$group_info_id = $(this).data('group-id');
	$group_student_id = $(this).data('group-student-id');

	if (confirm('Таңдаған оқушыны басқа группаға ауыстырасыңба?')) {
		$.ajax({
			type: "GET",
			url: 'group/controller.php?transfer_student_to_group&group_student_id='+$group_student_id+'&group_info_id='+$group_info_id,
			beforeSend: function() {
				set_load($('#transfer-group-modal'));
			},
			success: function($data) {
				$json = $.parseJSON($data);
				if ($json.success) {
					$.ajax({
						url: 'student/controller.php?set_student_lesson_access_after_payment&group_student_id='+$json.group_student_id,
						type: "GET",
						success: function($data) {
							remove_load();
							location.reload();
						}
					});
				}
			}
		});
	}
});

$(document).on('click', '.set-group-student-trial-test, .set-group-student-trial-test-notification-btn', function() {
	$lesson_progress_id = $(this).data('lp-id');

	if (confirm('Оқушыларға пробный тесттерді жіберуге келісесінба?')) {
		$.ajax({
			type: 'GET',
			url: 'group/controller.php?set_group_student_trial_test&lesson_progress_id='+$lesson_progress_id,
			beforeSend: function() {
				set_load('body');
			},
			success: function($data) {
				console.log($data)
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					location.reload();
				}
			}
		});
	}
});


function render_trial_test_results ($group_info_id) {
	$element = $('#group-student-trial-test-container');
	$ab_root = $('#ab_root').val();

	set_load('body');
	$.when(get_group_trial_test_assync($group_info_id)).done(function($data) {
		remove_load();
		$json = $.parseJSON($data);
		console.log($json);

		$student_ids = {};

		$html = "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$.each($json.result, function($index, $value) {
				$html += "<table class='table table-striped table-bordered'>";
					$html += "<tr>";
						$html += "<th>#</th>";
						$html += "<th>Оқушының аты-жөні</th>";
						$html += "<th>Берілген нұсқа</th>";
						$html += "<th>Берілген уақыт</th>";
						$html += "<th>Баллы</th>";
						$html += "<th>Тестті бітірген уықыты</th>";
					$html += "</tr>";
					$count = 0;
					$.each($value, function($index, $student) {
						if (!$student_ids.hasOwnProperty($student.student_id)) {
							$student_ids[$student.student_id] = {'last_name' : $student.last_name,
																'first_name' : $student.first_name,
																'results' : []};
						}
						if ($student.result != null) {
							$student_ids[$student.student_id]['results'].push({'total_result' : $student.result.total_result,
																			'actual_result' : $student.result.actual_result,
																			'submit_date' : $student.submit_date});
						}
						$html += "<tr>";
							$html += "<td>"+(++$count)+"</td>";
							$html += "<td><a target='_blank' href='"+$ab_root+"/academy/staff/student/student_cabinet/?student_id="+$student.student_id+"'>"+($student.last_name+' '+$student.first_name+'<br>'+$student.phone)+"</a></td>";
							$html += "<td>"+$student.trial_test_title+"</td>";
							$html += "<td>"+$student.appointment_date+"</td>";
							if ($student.result == null) {
								$result_link = '/';
							} else {
								$result_link = "<a target='_blank' href='"+$ab_root+"/academy/student/trial_test/components/testing.php?student_trial_test_id="+$student.student_trial_test_id+"'>"+$student.result.actual_result+'/'+$student.result.total_result+"</a>";
							}
							$html += "<td>"+$result_link+"</td>";
							$html += "<td>"+($student.submit_date == null ? '-' : $student.submit_date)+"</td>";
						$html += "</tr>";
					});
				$html += "</table>";
			});
		$html += "</div>"; // .col-...

		$.each($student_ids, function ($student_id, $test_result) {
			$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
				$html += "<canvas class='student-trial-test' data-student-id='"+$student_id+"' style='display: none; border-bottom: 1px solid gray;'></canvas>";
			$html += "</div>"; // .col-...
		});

		$element.html($html);

		render_student_trial_test_chart($student_ids);
	});
}

function get_group_trial_test_assync ($group_info_id) {
	return $.ajax({
		type: 'GET',
		url: '../../controller.php?get-group-trial-test-result&group_info_id='+$group_info_id
	});
}

function render_student_trial_test_chart($student_ids) {
	$('.student-trial-test').each(function() {
		$student_id = $(this).data('student-id');

		if ($student_ids[$student_id] != undefined && $student_ids[$student_id].results.length > 0) {
			
			$labels = [];
			$datasets_data = [];
			$max_mark = 0;
			console.log($student_ids[$student_id]);
			$.each($student_ids[$student_id]['results'], function($index, $value) {
				if ($value.submit_date != null) {
					$labels.push($value.submit_date);
					$datasets_data.push($value.actual_result);
					$max_mark = $value.total_result;
				}
			});

			$datasets = [{
				backgroundColor: '#1F77B4',
				borderColor: '#1F77B4',
				fill: false,
				data: $datasets_data
			}];
			// var ctx = document.getElementById('student-trial-test-'+$student_id).getContext('2d');
			$ctx = $(this);
			$chart = new Chart($ctx, {
				type: 'line',
				data: {
					labels: $labels,
					datasets: $datasets
				},
				options: {
					maintainAspectRatio: false,
					layout: {
						padding: 10,
					},
					legend: {
						display: false
					},
					title: {
						display: true,
						text: $student_ids[$student_id]['last_name']+' '+$student_ids[$student_id]['first_name']
					},
					scales: {
						yAxes: [{
							scaleLabel: {
								display: true,
								labelString: 'Балл'
							},
							ticks: {
								min: 0,
								max: $max_mark,
								stepSize: $max_mark / 5
							}
						}],
						xAxes: [{
							scaleLabel: {
								display: true,
								labelString: 'Тестті орындаған уақыт'
							}
						}]
					}
				}
			});
			$chart.canvas.parentNode.style.width = '100%';
			$chart.canvas.parentNode.style.height = '300px';
			$chart.canvas.style.width = '100%';
			$chart.canvas.style.height = '300px';
			$chart.resize();
			$(this).show();
		}
	});
}

$(document).on('click', '.activate-group-student', function() {
	$group_student_id = $(this).data('gs-id');
	$current_element = $(this);

	if (confirm('Оқушыны архивтен шығарып қайта группаға кіргізуге келісесінба?')) {
		$.when(activate_archive_group_student($group_student_id)).done(function($data) {
			$json = $.parseJSON($data);
			if ($json.success && $json.set_lesson_access) {
				$.when(set_student_lesson_access_after_payment($current_element.parents('table'), $group_student_id)).done(function() {
					location.reload();
				});
			} else {
				location.reload();
			}
		});
	}
});

function activate_archive_group_student ($group_student_id) {
	return $.ajax({
		type: 'GET',
		url: 'group/controller.php?activate_archive_group_student&group_student_id='+$group_student_id
	});
}

$(document).on('click', '.find-student-in-list', function() {
	$phone = $(this).data('phone');
	window.open('?nav=active-student-nav&student-phone='+$phone);
});
