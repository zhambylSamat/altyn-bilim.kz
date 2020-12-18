function ab_root () {
	$ab_root = $('#ab-root').val();
	return $ab_root;
}

$isDragging = false;
$(document).on('mousedown', '.uploaded-imgs .ui-sortable-handle', function() {
	$isDragging = false;
	$('.change-trial-test-img-order').hide();
}).on('mousemove', '.uploaded-imgs .ui-sortable-handle', function() {
	$isDragging = true;
}).on('mouseup', '.uploaded-imgs .ui-sortable-handle', function() {
	if ($isDragging) {
		$isDragging = false;
		$('.change-trial-test-img-order').show(function() {
			replace_trial_test_img_order();
		});
	}
});

function replace_trial_test_img_order () {
	$numeration = 1;
	$('.trial-test-img-box').each(function() {
		$(this).find('.numeration').text($numeration);
		$numeration++;
	});
}

$(document).on('click', '.change-trial-test-img-order .cancel-order', function() {
	$('.change-trial-test-img-order').hide();
	reset_trial_test_img_order();
});

function reset_trial_test_img_order () {
	$elements = {};
	$('.trial-test-img-box').each(function() {
		$(this).find('.numeration').text($(this).data('order'));
		$elements[$(this).data('order')] = $(this);
	});

	$.each($elements, function($index, $element) {
		$('.uploaded-imgs').append($element);
	});
}

$(document).on('click', '.change-trial-test-img-order .save-order', function() {
	$trial_test_file_ids = {};
	$('.trial-test-img-box').each(function() {
		$trial_test_file_id = $(this).data('trial-test-file-id');
		$numeration = $(this).find('.numeration').text();
		$trial_test_file_ids[$numeration] = $trial_test_file_id;
	});
	$form_data = new FormData();
	$form_data.append('trial_test_file_ids', JSON.stringify($trial_test_file_ids));
	$.ajax({
		type: 'POST',
		method: 'POST',
		url: ab_root()+'/academy/staff/trial_test/controller.php?change-trial-test-img-order',
		data: $form_data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load($('.uploaded-imgs'));
		},
		success: function($data) {
			remove_load();
			console.log($data);
			$json = $.parseJSON($data);
			if ($json.success) {
				$('.trial-test-img-box').each(function() {
					$(this).attr('data-order', $(this).find('.numeration').text());
				});
				lightAlert($('.uploaded-imgs'), 'green', 0, 500);
				$('.change-trial-test-img-order').hide();
			}
		}
	});
});


$(document).on('click', '.choose-subject-to-trial-test', function() {
	$subject_id = $(this).data('subject-id');

	$('.choose-subject-to-trial-test').each(function() {
		$(this).attr('choosen', '0');
		$(this).removeClass('btn-success');
		$(this).addClass('btn-default');
	});

	$(this).removeClass('btn-default');
	$(this).addClass('btn-success');
	$(this).attr('choosen', '1');

	load_trial_test_list($subject_id);
});

function load_trial_test_list($subject_id) {
	set_load($('.trial-test-list-content'));
	$('.trial-test-list-content').load(ab_root()+'/academy/staff/trial_test/components/trial_test_list.php?subject_id='+$subject_id, function() {
		remove_load();
		lightAlert($(this), 'green', 0, 500);
	});
}

$(document).on('click', '.open-trial-test-form', function() {
	$subject_id = $(this).data('subject-id');
	$html = "<tr>";
		$html += "<td colspan='2'>";
			$html += "<form id='add-new-trial-test-form' class='form-horizontal'>";
				$html += "<div class='form-group'>";
					$html += "<label for='new-trial-test-title' class='control-label col-md-4 col-sm-4 col-xs-6'>Пробный тесттің варианты:</label>";
					$html += "<div class='col-md-8 col-sm-8 col-xs-6'>";
						$html += "<input type='text' class='form-control' id='new-trial-test-title' name='trial_test_title' placeholder='Вариант' required>";
						$html += "<input type='hidden' name='subject_id' value='"+$subject_id+"'>";
					$html += "</div>";
				$html += "</div>";
				$html += "<div class='pull-right'>";
					$html += "<button type='submit' class='btn btn-success btn-sm'>Сақтау</button> ";
					$html += " <button type='button' class='btn btn-warning btn-xs cancel-trial-test-form'>Отмена</button>";
				$html += "</div>";
			$html += "</form>";
		$html += "</td>";
	$html += "</tr>";

	$(this).parents('tr').hide();
	$(this).parents('tr').before($html);
});

$(document).on('click', '.cancel-trial-test-form', function() {
	$(this).parents('table').find('tr').last().show();
	$(this).parents('tr').remove();
});

$(document).on('click', '.edit-trial-test-title-btn', function() {
	$element = $(this).parents('.trial-test-title-box').find('.trial-test-title-link');
	$trial_test_id = $element.data('trial-test-id');
	$title = $element.text();

	$html = "";
	$html += "<form class='trial-test-title-edit'>";
		$html += "<input type='text' class='form-control' name='trial-test-title' value='"+$title+"'>";
		$html += "<input type='hidden' name='trial-test-id' value='"+$trial_test_id+"'>";
		$html += " <button type='submit' class='btn btn-sm btn-success edit-trial-test-title'>Сақтау</button> ";
		$html += " <button type='button' class='btn btn-xs btn-warning cancel-trial-test-title'>Отмена</button> ";
	$html += "</form>";

	$(this).parents('.trial-test-title-box').after($html);
	$(this).parents('.trial-test-title-box').hide();
});

$(document).on('click', '.cancel-trial-test-title', function() {
	$(this).parents('td').find('.trial-test-title-box').show();
	$(this).parents('.trial-test-title-edit').remove();
});

$(document).on('submit', '.trial-test-title-edit', function($e) {
	$e.preventDefault();

	$form_element = $(this);
	$.ajax({
		type: 'POST',
		method: 'POST',
		url: ab_root()+'/academy/staff/trial_test/controller.php?edit_trial_test_title',
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load($form_element);
		},
		success: function($data) {
			remove_load();
			console.log($data);
			$json = $.parseJSON($data);
			if ($json.success) {
				$html = "<div class='trial-test-title-box'>";
					$html += "<a href='#' class='trial-test-title-link' data-trial-test-id='"+$json.data.trial_test_id+"'>"+$json.data.trial_test_title+"</a>";
					$html += "<button class='btn btn-xs btn-info pull-right edit-trial-test-title-btn'><i class='fas fa-pen'></i></button>";
				$html += "</div>";
				lightAlert($form_element.parents('td'), 'green', 0, 500, function() {
					$form_element.parents('td').html($html);
				});
			}
		}
	});
});

$(document).on('submit', '#add-new-trial-test-form', function($e) {
	$e.preventDefault();
	$subject_id = $(this).find('input[name=subject_id]').val();
	$form_element = $(this);
	
	$.ajax({
		type: 'POST',
		method: 'POST',
		url: ab_root()+'/academy/staff/trial_test/controller.php?add_new_trial_test',
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load($form_element);
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				load_trial_test_list($subject_id);
			}
		}
	});
});

$(document).on('click', '.trial-test-title-link', function() {
	console.log('okk');
	$('.trial-test-title-link').each(function() {
		$(this).removeClass('trial-test-title-link-active');
	});
	$(this).addClass('trial-test-title-link-active');
	$trial_test_id = $(this).data('trial-test-id');
	reset_trial_test_content($trial_test_id);
	// $('#trial-test-content').load(ab_root()+'/academy/staff/trial_test/components/trial_test.php?trial_test_id='+$trial_test_id, function() {
	// 	lightAlert($('#trial-test-content'), 'green', 0, 500);
	// });
});

$(document).on('change', '.upload-img-input', function() {
	$img_fake_path = $(this).val();
	$file_extentions = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];
	$progress = $(this).parents('.upload-img-box').find('.percent');
	$trial_test_id = $('#trial-test-id').val();
	$current_element = $(this);

	if ($img_fake_path != '') {
		$($(this).prop('files')).each(function($i, $v) {
			if ($v.size < 524288000) {
				if ($file_extentions.includes($v.name.split('.').pop()) == 1) {
					$form_data = new FormData();
					$form_data.append('trial_test_img', $v);
					$form_data.append('trial_test_id', $trial_test_id);

					$.ajax({
						url: ab_root()+'/academy/staff/trial_test/controller.php?upload_trial_test_img',
						type: 'POST',
						data: $form_data,
						processData: false,
						contentType: false,
						cache: false,
						xhr: function() {
			                var xhr = new window.XMLHttpRequest();
			                xhr.upload.addEventListener("progress", function(evt) {
			                    if (evt.lengthComputable) {
			                        var percentComplete = parseInt((evt.loaded / evt.total) * 100)+'%';
			                        $progress.html(percentComplete);
			                    }
			                }, false);
			                return xhr;
			            },
			            beforeSend: function() {
							$percentVal = '0%';
							$progress.html($percentVal);
						},
						complete: function(xhr) {
							$progress.html('');
						},
						success: function($data) {
							console.log($data);
							$json = $.parseJSON($data);
							if ($json.success) {
								$.get(ab_root()+'/academy/staff/trial_test/components/trial_test_single_img.php?trial_test_file_id='+$json.data.trial_test_file_id+'&trial_test_file_link='+$json.data.trial_test_file_link+'&trial_test_file_order='+$json.data.trial_test_file_order, function(data) {
									$(data).appendTo('.uploaded-imgs');
								});
							} else {
								alert($json.message);
							}
						}
					});
				} else {
					alert('Жүктелген суреттің типі "jpeg", "jpg", "png" болу керек.');
				}
			} else {
				alert('Жүктелген суреттің салмағы 5 мб (мега бит) тан көп болмауы керек');
			}
		});
	}
});

$(document).on('click', '.trial-test-img-box .remove', function() {
	$trial_test_file_id = $(this).data('trial-test-file-id');
	$trial_test_id = $(this).parents('#trial-test-content').find('#trial-test-id').val();
	console.log($trial_test_id);
	$current_element = $(this);

	if (confirm('Таңдаған суретті өшіруге келісесінба?')) {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?remove-trial-test-file&trial_test_file_id='+$trial_test_file_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('.trial-test-img-box'));
			},
			success: function($data) {
				console.log($data);
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					// $current_element.parents('.trial-test-img-box').remove();
					reset_trial_test_content($trial_test_id);
				}
			}
		});
	}
});

$(document).on('click', '.add-answer', function() {

	$trial_test_id = $(this).data('trial-test-id');
	$.ajax({
		url: ab_root()+'/academy/staff/trial_test/controller.php?add_new_answer&trial_test_id='+$trial_test_id,
		type: 'GET',
		cache: false,
		beforeSend: function() {
			set_load($('.trial-test-answers-box'));
		},
		success: function($data) {
			console.log($data);
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				$.get(ab_root()+'/academy/staff/trial_test/components/trial_test_single_answer.php?answer_numeration='+$json.numeration+'&answer_value='+JSON.stringify($json.values), function(data) {
					$(data).appendTo('.trial-test-answers-box');
				});				
			}
		}
	});
});

$(document).on('click', '.add-prefix-mark', function() {

	$trial_test_id = $('.trial-test-answers-box').find('input[name=trial-test-id]').val();
	$numeration = $(this).parents('.trial-test-answer-box').find('input[name=numeration]').val();
	$current_element = $(this);

	$.ajax({
		url: ab_root()+'/academy/staff/trial_test/controller.php?add_new_prefix&trial_test_id='+$trial_test_id+'&numeration='+$numeration,
		type: 'GET',
		cache: false,
		beforeSend: function() {
			set_load($('.trial-test-answers-box'));
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				if ($json.exists) {
					$.get(ab_root()+'/academy/staff/trial_test/components/trial_test_single_answer.php?answer_numeration='+$json.numeration+'&answer_value='+JSON.stringify($json.values), function(data) {
						$current_element.parents('.trial-test-answer-box').replaceWith(data);
					});
				}
			}
		}
	});
});

$(document).on('click', '.remove-last-answer-mark', function() {
	$trial_test_id = $('.trial-test-answers-box').find('input[name=trial-test-id]').val();
	$numeration = $(this).parents('.trial-test-answer-box').find('input[name=numeration]').val();
	$trial_test_answer_id = $(this).data('trial-test-answer-id');
	$current_element = $(this);

	if (confirm('Тесттің соңғы вариантын өшіресінба?')) {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?remove_last_answer&trial_test_answer_id='+$trial_test_answer_id+'&trial_test_id='+$trial_test_id+'&numeration='+$numeration,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('.trial-test-answers-box'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					if ($json.exists) {
						$.get(ab_root()+'/academy/staff/trial_test/components/trial_test_single_answer.php?answer_numeration='+$json.numeration+'&answer_value='+JSON.stringify($json.values), function(data) {
							$current_element.parents('.trial-test-answer-box').replaceWith(data);
						});
						check_exists_true_ans();
					} else {
						reset_trial_test_content($trial_test_id);
					}
	 			}
			}
		});
	}
});

$(document).on('click', '.remove-answer-mark', function() {
	$trial_test_id = $('.trial-test-answers-box').find('input[name=trial-test-id]').val();
	$numeration = $(this).parents('.trial-test-answer-box').find('input[name=numeration]').val();
	$current_element = $(this);
	if (confirm('Теcттің жауабын өшіресінба?')) {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?remove_answer&trial_test_id='+$trial_test_id+'&numeration='+$numeration,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('.trial-test-answers-box'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					reset_trial_test_content($trial_test_id);
	 			}
			}
		});
	}
});

function reset_trial_test_content ($trial_test_id) {
	$('#trial-test-content').load(ab_root()+'/academy/staff/trial_test/components/trial_test.php?trial_test_id='+$trial_test_id, function() {
		lightAlert($('#trial-test-content'), 'green', 0, 500);
	});
}

$(document).on('change', '.answer-checkbox', function() {
	$trial_test_answer_id = $(this).parents('.prefix-box').find('input[name=trial-test-answer-id]').val();
	$current_element = $(this);
	if ($(this).prop('checked')) {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?set_true_ans&trial_test_answer_id='+$trial_test_answer_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('.trial-test-answers-box'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					check_exists_true_ans();
					if (!$current_element.parents('.prefix-box').hasClass('ans-checked')) {
						$current_element.parents('.prefix-box').addClass('ans-checked');
					}
				}
			}
		});
	} else {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?unset_true_ans&trial_test_answer_id='+$trial_test_answer_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('.trial-test-answers-box'));
			},
			success: function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					check_exists_true_ans();
					$current_element.parents('.prefix-box').removeClass('ans-checked');
				}
			}
		});
	}
});

function check_exists_true_ans () {
	$('.trial-test-answer-box').each(function() {
		$true_count = 0;
		$(this).find('.prefix-box').each(function() {
			if ($(this).find('.answer-checkbox').prop('checked')) {
				$true_count++;
			}
		});
		if ($true_count == 0) {
			if (!$(this).hasClass('no-true-ans')) {
				$(this).addClass('no-true-ans');
			}
		} else {
			if ($(this).hasClass('no-true-ans')) {
				$(this).removeClass('no-true-ans');
			}
		}
	});
}

$(document).on('click', '.remove-trial-test', function() {
	$trial_test_id = $(this).data('trial-test-id');

	$subject_id = 0;
	$('.choose-subject-to-trial-test').each(function() {
		if ($(this).attr('choosen') == 1) {
			$subject_id = $(this).data('subject-id');
		}
	});

	if ($subject_id != 0 && confirm('Таңдаған пробный тестті өшіресінба?')) {
		$.ajax({
			url: ab_root()+'/academy/staff/trial_test/controller.php?remove_trial_test&trial_test_id='+$trial_test_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('.trial-test-list-content'));
			},
			success: function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					load_trial_test_list($subject_id);
				}
			}
		});
	}
});
