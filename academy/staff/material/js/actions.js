$(document).on('click', '.order-actions .change-order', function() {
	$action = $(this).data('action');
	$elem = $(this);
	if ($action == 'save') {
		$arr = {'data': []};
		$count = 1;
		$('.materials .sortable').find('li').each(function() {
			$tmp_arr = {
				'id': $(this).data('id'),
				'order': $count++
			}
			$arr['data'].push($tmp_arr);
		});
		$obj = $('.materials .sortable').data('obj');
		$load_url = $('.materials .sortable').data('dir') + '&obj_content=' + $obj;
		$('.materials #breadcrumb').find("li").each(function() {
			$tmp_obj = $(this).data('obj');
			$tmp_id = $(this).data('id');
			if ($tmp_obj != '' && $tmp_obj != '') {
				$load_url += '&' + $tmp_obj + "_id=" + $tmp_id;
			}
		});
		if (confirm("Өзгертуге сенімдісіңбе?")) {
			$.ajax({
				type: 'POST',
				url: 'material/controller.php?change-order=' + $obj,
				data: $arr,
				cache: false,
				beforeSend: function() {
					set_load('.materials #material-body');
				},
				success: function($data) {
					$json = $.parseJSON($data);
					remove_load();
					if ($json.success) {
						if ($elem.parents('#material-content').length) {
							$('.materials #material-content').load($load_url);
						} else {
							$('.materials #material-body').load($load_url);
						}
					}
				}
			});
		}
	}
});

$(document).on('click', '.adding-vimeo-video', function() {
	$(this).parents('.form').find('.add-vimeo-video').removeClass('hide');
	$(this).addClass('hide');
});

$(document).on('click', '.cancel-vimeo-video', function() {
	$(this).parents('.form').find('.add-vimeo-video').addClass('hide');
	$(this).parents('.add-vimeo-video').find('input[name=vimeo-link]').val('');
	$(this).parents('.form').find('.adding-vimeo-video').removeClass('hide');
});

$(document).on('click', '.adding-document', function() {
	$(this).parents('.form').find('.add-document').removeClass('hide');
	$(this).addClass('hide');
});

$(document).on('click', '.cancel-adding-document', function() {
	$(this).parents('.form').find('.add-document').addClass('hide');
	$(this).parents('.add-document').find('input[name=document]').val('');
	$(this).parents('.form').find('.adding-document').removeClass('hide');
});

$(document).on('submit', '.add-vimeo-video', function(e) {
	e.preventDefault();
	$obj = $(this).parents('#material-content').find('.sortable').data('obj');
	$controller_params = '?vimeo-link&type=' + $obj;
	// $load_url = $(this).data('dir') + '&obj_content=' + $obj;
	$load_url = 'material/' + $(this).parents('#material-body').find('.material-btn-groups').find('.active').data('dir');
	$form_data = new FormData(this);
	if (confirm('Жаңа видео енгізуге келісесізбе?')) {
		$.when(get_vimeo_oembed($form_data.get('vimeo-link'))).done(function($vimeo_result) {
			$duration = $vimeo_result.duration;
			if ($vimeo_result.duration == undefined) {
				$duration = 0;
			}
			$form_data.append('duration', $duration);
			$form_data.append('title', $vimeo_result.title);
			$.ajax({
				type: 'POST',
				method: 'POST',
				url: 'material/controller.php' + $controller_params,
				data: $form_data,
				contentType: false,
	    	    cache: false,
				processData:false,
				beforeSend: function() {
					set_load('.materials .form');
				},
				success: function($data) {
					$json = $.parseJSON($data);
					remove_load();
					if ($json.success) {
						$('.materials #material-content').load($load_url);
					}
				}
			});
		});
	}
});


$(document).on('click', '.show-video', function() {
	$(this).parent().find('li').removeClass('bg-success');
	$(this).addClass('bg-success');
	$link = $(this).data('link');
	$title = $(this).data('title');
	$id = $(this).data('id');
	$obj = $(this).parent().data('obj');
	$vimeo_id = get_vimeo_id_by_link($link);
	$options = {
		id: $vimeo_id,
		width: 480,
		height: 360
	};

	$html = "<center>";
		$html += "<p>" + $title + "</p>";
		$html += "<div class='vimeo-video' id='vimeo-" + $vimeo_id + "' data-id='" + $vimeo_id + "'></div>";
		$html += "<button class='btn btn-xs btn-danger delete-vimeo-video' value='" + $id + "' data-obj='" + $obj + "'>Удалить видео</button>";
		$html += "<div class='timecode'></div>";
		$html += "<hr>";
	$html += "</center>";
	$('.vimeo-video-content').html($html);
	render_vimeo_video($('#vimeo-' + $vimeo_id), $options);
	if ($obj == 'end_video') {
		set_timecode_list($id);
	}
});

function set_timecode_list($id, $element = '.timecode') {
	$.when(get_end_video_timecode_by_id($id, $element)).done(function($result){
		$json = $.parseJSON($result);
		$html = '';
		$html += "<form class='form-inline' id='time-code-form'>";
			$html += "<div id='timecode-list'>";
			$html += "<ol>";
				$.each($json.result, function($index, $element){
					$timer = "";
					if ($element.detailed_information.hour != 0) {
						$timer += $element.detailed_information.hour + " сағат";
					}
					if ($element.detailed_information.minute != 0) {
						$timer += " " + $element.detailed_information.minute + " минут";
					}
					if ($element.detailed_information.second != 0) {
						$timer += " " + $element.detailed_information.second + " секунд";
					}
					$html += "<li style='padding: 5px 0;'>";
						$html += "<a style='cursor:pointer;' class='go-to-time' data-time='" + $element.detailed_information.total_seconds + "'>" + $element.title + " (" + $timer + ")</a>";
						$html += "<a class='btn btn-xs btn-danger pull-right remove-timecode' data-id='" + $element.id + "'>Удалить timecode</a>";
						$html += "<div></div>";
					$html += "</li>";
				});
				$html += "</ol>";
			$html += "</div>";
			$html += "<div id='time-code-form-box'>";
				$html += "<div class='form-group'>";
					$html += "<input type='text' name='timecode' class='form-control' placeholder='Time code'>"
				$html += "</div>";
				$html += "<div class='form-group'>";
					$html += "<input type='text' name='title' class='form-control' placeholder='Title'>"
				$html += "</div>";
				$html += "<div><p><span>Шаблон: </span><span style='color: gray'>1h15m30s</span></p></div>";
				$html += "<input type='hidden' name='end-video-id' value='" + $id + "'>";
				$html += "<button type='submit' class='btn btn-sm btn-default'>Сақтау</button>";
				$html += "<a id='cancel-time-code-btn' class='btn btn-sm btn-warning'>Отмена</a>";
			$html += "</div>";
			$html += "<a class='timecode-btn btn btn-sm btn-info' id='add-time-code-btn'>Timecode қосу</a>";
		$html += "</form";
		$($element).html($html);
	});
}

$(document).on('click', '#timecode-list .go-to-time', function() {
	$time = $(this).data('time');
	$vimeo_id = $(this).parents('.vimeo-video-content').find('.vimeo-video').data('id');
	$players[$vimeo_id].setCurrentTime($time);
	$players[$vimeo_id].play();
});

$(document).on('click', '#add-time-code-btn', function() {
	time_code_toggle();
});
$(document).on('click', '#cancel-time-code-btn', function() {
	time_code_toggle();
});

function time_code_toggle() {
	$form_box = $('#time-code-form-box');
	$('#add-time-code-btn').toggle();
	$form_box.find('input[name=timecode]').val('');
	$form_box.find('input[name=title]').val('');
	$form_box.toggle();
}

$(document).on('submit', '#time-code-form', function($e){
	$e.preventDefault();
	$this = $(this);
	$.ajax({
		url: 'material/controller.php?add-time-code',
		type: 'POST',
		data: new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend: function() {
			set_load('body');
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				set_timecode_list($this.find('input[name=end-video-id]').val());
			}
		}
	});
});

$(document).on('click', '.remove-timecode', function() {
	if (confirm('Timecode-ті өшіріледі')) {
		$id = $(this).data('id');
		$this = $(this);
		$.ajax({
			url: 'material/controller.php?remove-timecode&id='+$id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($this.parents('.timecode'));
			}, 
			success: function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					set_timecode_list($this.parents('#time-code-form').find('input[name=end-video-id]').val());
				}
			}
		});
	}
});

$(document).on('click', '.delete-vimeo-video', function() {
	$id = $(this).val();
	$load_url = $(this).parents('.materials').find('.add-vimeo-video').data('dir');
	// $load_url = $(this).parents('#material-body').find('.material-btn-groups').find('.active').data('dir');
	$obj = $(this).data('obj');
	$confirm_message = "Сіз осы видеоны порталдан өшіруді растайсызба?";
	$parent_element = '.materials #material-content';
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-subtopic', function() {
	$id = $(this).data('id');
	$load_url = $(this).parents('.materials').find('ol').data('dir');
	// $load_url = $(this).parents('#material-body').find('.material-btn-groups').find('.active').data('dir');
	$obj = $(this).parents('.materials').find('ol').data('obj');
	$confirm_message = "Сіз осы тақырыпты порталдан өшіруді растайсызба?";
	$parent_element = '.materials #material-body';
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-topic', function() {
	$id = $(this).data('id');
	$load_url = $(this).parents('.materials').find('ol').data('dir');
	$obj = $(this).parents('.materials').find('ol').data('obj');
	$confirm_message = "Сіз осы тарауды порталдан өшіруді растайсызба?";
	$parent_element = '.materials #material-body';
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-subject', function() {
	$id = $(this).data('id');
	$load_url = $(this).parents('.materials').find('ol').data('dir');
	$obj = $(this).parents('.materials').find('ol').data('obj');
	$confirm_message = "Сіз осы тақырыпты порталдан өшіруді растайсызба?";
	$parent_element = '.materials #material-body';
	delete_material_by_id($id, $obj, undefined, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-tutorial-document', function() {
	$id = $(this).val();
	$load_url = $(this).parents('.materials').find('ul').data('dir');
	$parent_element = '.materials #material-content';
	$obj = $(this).parents('.materials').find('ul').data('obj');
	$confirm_message = "Сіз осы ақпаратты өшіруді растайсызба?";
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-material-test', function() {
	$id = $(this).val();
	$load_url = $(this).parents('.materials').find('ul').data('dir');
	$parent_element = '.materials #material-content';
	$obj = $(this).parents('.materials').find('ul').data('obj');
	$confirm_message = "Сіз осы тестті өшіруге келісесізбе?";
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

$(document).on('click', '.delete-material-test-solve', function() {
	$id = $(this).val();
	$load_url = $(this).parents('.materials').find('ul').data('dir');
	$parent_element = '.materials #material-content';
	$obj = $(this).parents('.materials').find('ul').data('obj');
	$confirm_message = "Сіз осы файлды өшіруге келісесізбе?";
	delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element);
});

function delete_material_by_id($id, $obj, $load_url, $confirm_message, $parent_element) {
	$link = 'material/controller.php?remove_materials&obj=' + $obj + '&id=' + $id;
	if (confirm($confirm_message)) {
		$.ajax({
			type: 'GET',
			url: $link,
			cache: false,
			beforeSend: function() {
				set_load($parent_element);
			},
			success: function($data) {
				remove_load();
				console.log($data);
				$json = $.parseJSON($data);
				if ($json.success) {
					if ($load_url === undefined) {
						location.reload();
					} else {
						$($parent_element).load($load_url);
					}
				}
			}
		});
	}
}

$(document).on('click', '.show-add-material-form', function() {
	$(this).addClass('hide');
	$(this).next().removeClass('hide');
});

$(document).on('click', '.cancel-add-material-form', function() {
	$(this).parents('form').addClass('hide');
	$(this).parents('form').prev().removeClass('hide');
});

$(document).on('submit', '.add-material-content', function(e) {
	e.preventDefault();
	$obj = $(this).data('obj');
	$form_data = new FormData(this);
	$form_data.append('obj', $obj);
	$load_url = $(this).parents('ol').data('dir');
	save_material_content($form_data, $load_url);
});

function save_material_content($form_data, $load_url) {
	$link = 'material/controller.php?create-materials';
	$.ajax({
		type: 'POST',
		url: $link,
		data: $form_data,
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend: function() {
			set_load('.materials #material-body');
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				if ($load_url === undefined) {
					location.reload();
				} else {
					$('.materials #material-body').load($load_url);
				}
			}
		}
	});
}

$(document).on('submit', '.add-document', function(e) {
	e.preventDefault();
	$obj = $(this).parents('#material-content').find('.sortable').data('obj');
	$controller_params = '?document-link';
	$load_url = $(this).data('dir') + '&obj_content=' + $obj;
	$file = $(this).find('input[name=document]').prop('files')[0];
	if (confirm('Жаңа документ енгізуге келісесізбе?')) {
		if ($file.size <= 10410760) {
			$.ajax({
				type: 'POST',
				method: 'POST',
				url: 'material/controller.php' + $controller_params,
				data: new FormData(this),
				contentType: false,
	    	    cache: false,
				processData: false,
				beforeSend: function() {
					set_load('.materials .form');
				},
				success: function($data) {
					$json = $.parseJSON($data);
					console.log($json);
					remove_load();
					if ($json.success) {
						$('.materials #material-body #material-content').load($load_url);
					}
				}
			});
		} else {
			alert("Файлдың көлемі 4МБ тан (немесе '4 096'КБ немесе '4 194 304'Б тан) аспау керек!");
		}
	}
});

$(document).on('change', '.perceive #perceive', function() {
	if ($('.perceive .material-config-btn').hasClass('hide')) {
		$('.perceive .material-config-btn').removeClass('hide');
	} else {
		$('.perceive .material-config-btn').addClass('hide');
	}
});

$(document).on('click', '.material-config-btn', function() {
	$btn_element = $(this);
	$subtopic_id = $(this).data('subtopic-id');
	$type = $(this).data('type');
	$is_checked = $('.perceive #perceive').prop('checked');
	$form_data = new FormData();
	$form_data.append('subtopic_id', $subtopic_id);
	$form_data.append('type', $type);
	$form_data.append('is_checked', $is_checked);
	$.ajax({
		type: 'POST',
		url: 'material/controller.php?edit-material-config',
		data: $form_data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load('#material-body');
		},
		success: function($data) {
			$json = $.parseJSON($data);
			remove_load();
			if ($json.success) {
				$btn_element.addClass('hide');
				lightAlert($('.perceive'), 'green', 0, 300);
			}
		}
	});
});

$(document).on('submit', '#add-test-form', function(e) {
	e.preventDefault();
	// $obj = $(this).parents('#material-content').find('.sortable').data('obj');
	$controller_params = '?add-test';
	$current_element = $(this);
	$file = $(this).find('input[name=document]').prop('files')[0];
	$load_url = $(this).data('dir');
	if (confirm('Жаңа тест енгізуге келісесізбе?')) {
		if ($file.size <= 10410760) {
			$.ajax({
				type: 'POST',
				method: 'POST',
				url: 'material/controller.php' + $controller_params,
				data: new FormData(this),
				contentType: false,
	    	    cache: false,
				processData: false,
				beforeSend: function() {
					set_load($current_element);
				},
				success: function($data) {
					console.log($data);
					$json = $.parseJSON($data);
					console.log($json);
					remove_load();
					if ($json.success) {
						$('.materials #material-body #material-content').load($load_url);
					}
				}
			});
		} else {
			alert("Файлдың көлемі 4МБ тан (немесе '4 096'КБ немесе '4 194 304'Б тан) аспау керек!");
		}
	}
});

$(document).on('submit', '#add-test-solve-form', function(e) {
	e.preventDefault();
	$controller_params = '?add-test-solve';
	$current_element = $(this);
	$file = $(this).find('input[name=document]').prop('files')[0];
	$load_url = $(this).data('dir');
	if ($file.size <= 10410760) {
		$.ajax({
			type: 'POST',
			method: 'POST',
			url: 'material/controller.php' + $controller_params,
			data: new FormData(this),
			contentType: false,
    	    cache: false,
			processData: false,
			beforeSend: function() {
				set_load($current_element);
			},
			success: function($data) {
				console.log($data);
				$json = $.parseJSON($data);
				console.log($json);
				remove_load();
				if ($json.success) {
					$('.materials #material-body #material-content').load($load_url);
				}
			}
		});
	} else {
		alert("Файлдың көлемі 4МБ тан (немесе '4 096'КБ немесе '4 194 304'Б тан) аспау керек!");
	}
});

$(document).on('click', '.add-prefixes', function() {
	$default_prefixes = ['A', 'B', 'C', 'D', 'E'];
	
	$last_prefix_element = $(this).parents('.ans-box').find('.prefixes').last().find('input[name=numeration]');
	if ($last_prefix_element[0] === undefined) {
		$numeration = 0;
	} else {
		$numeration = parseInt($last_prefix_element.val());	
	}
	$subtopic_id = $(this).data('subtopic-id');
	$html = "<form class='prefixes new'>";
	$html += "<input type='hidden' name='subtopic_id' value='"+$subtopic_id+"'>";
	$html += "<input type='hidden' name='numeration' value='"+($numeration+1)+"'>"
	$html += "<span>"+($numeration+1)+") &nbsp;&nbsp;</span>";
	$.each($default_prefixes, function($i, $elem) {
		$html += "<label class='inline-ans-prefix' for='ans-default-"+$i+"'>";
			$html += "<input type='hidden' name='ans_id["+$i+"]' class='ans-id' value='new'>";
			$html += "<input type='text' name='prefix["+$i+"]' required class='form-control ans-prefix-input' value='"+$elem+"'>";
			$html += "<input type='radio' class='ans-prefix-radio' name='ans_radio["+$i+"]' id='ans-default-"+$i+"'>";
			$html += "<button type='button' class='btn btn-xs btn-danger btn-block remove-prefix'><span class='glyphicon glyphicon-remove'></span></button>";
		$html += "</label>";
	});
	$html += "<button type='button' style='margin-left: 10px;' class='btn btn-info btn-sm extra-prefix'><span class='glyphicon glyphicon-plus'></span></button>";
	$html += "<button type='submit' style='margin-left: 10px;' class='btn btn-success btn-sm save-add-prefixes'>Сақтау</button>";
	$html += "<button type='button' style='margin-left: 10px;' class='btn btn-warning btn-sm cancel-add-prefixes'>Отмена</button>";
	$html += "<p class='message text-danger'></p>";
	$html += "</form>";
	$(this).addClass('hidden');
	$(this).parents('.ans-box').append($html);
});

$(document).on("click", '.cancel-add-prefixes', function() {
	$(this).parents('.ans-box').find('.add-prefixes').removeClass('hidden');
	$is_new = $(this).parents('.prefixes').hasClass('new');
	if ($is_new) {
		$(this).parents('.prefixes').remove();
	} else {
		$.each($(this).parents('.prefixes').find('.inline-ans-prefix'), function(){
			if ($(this).find('input[type=hidden]').val() == 'new') {
				$(this).remove();
			} else if ($(this).hasClass('hidden')) {
				$ans_id = $(this).find('.ans-id');
				$ans_id.attr('name', $ans_id.data('name'));
				$ans_id.removeAttr('data-name');

				$prefix = $(this).find('.ans-prefix-input');
				$prefix.attr('name', $prefix.data('name'));
				$prefix.removeAttr('data-name');

				$torf = $(this).find('.ans-prefix-radio');
				$torf.attr('name', $torf.data('name'));
				$torf.removeAttr('data-name');
				$(this).parents('.prefixes').trigger('reset');
				set_checked_class($(this).parents('.prefixes'));

				$(this).removeClass('hidden');
			} else {
				$(this).parents('.prefixes').trigger('reset');
				set_checked_class($(this).parents('.prefixes'));				
			}
		});
		$(this).parents('.prefixes').find('.message').html('');
		$(this).parents('.prefixes').find('.save-add-prefixes').addClass('hidden');
		$(this).addClass('hidden');
	}
});

function set_checked_class($elem) {
	$.each($elem.find('.ans-prefix-radio'), function() {
		if ($(this).prop('checked')) {
			$(this).parents('.inline-ans-prefix').addClass('ans-prefix-checked');
		} else {
			$(this).parents('.inline-ans-prefix').removeClass('ans-prefix-checked');
		}
	});
}

$(document).on('click', '.extra-prefix', function() {
	$count = 0;
	$.each($(this).parents('.prefixes').find('.inline-ans-prefix'), function() {
		$count++;
	});
	$html = "<label class='inline-ans-prefix' for='ans-default-"+$count+"'>";
		$html += "<input type='hidden' name='ans_id["+$count+"]' class='ans-id' value='new'>";
		$html += "<input required type='text' name='prefix["+$count+"]' class='form-control ans-prefix-input' value=''>";
		$html += "<input type='radio' class='ans-prefix-radio' name='ans_radio["+$count+"]' id='ans-default-"+$count+"'>";
		$html += "<button type='button' class='btn btn-xs btn-danger btn-block remove-prefix'><span class='glyphicon glyphicon-remove'></span></button>";
	$html += "</label>";
	if (!$(this).parents('.prefixes').hasClass('new')) {
		$(this).parents('.prefixes').find('.save-add-prefixes').removeClass('hidden');
		$(this).parents('.prefixes').find('.cancel-add-prefixes').removeClass('hidden');
	}
	$(this).before($html);
});

$(document).on('change', '.ans-prefix-radio', function() {
	$.each($(this).parents('.prefixes').find('.ans-prefix-radio'), function() {
		$(this).prop('checked', false);
	});
	$(this).prop('checked', true);
	set_checked_class($(this).parents('.prefixes'));
	if (!$(this).parents('.prefixes').hasClass('new')) {
		$(this).parents('.prefixes').find('.save-add-prefixes').removeClass('hidden');
		$(this).parents('.prefixes').find('.cancel-add-prefixes').removeClass('hidden');
	}
});

$(document).on('change', '.ans-prefix-input', function() {
	if (!$(this).parents('.prefixes').hasClass('new')) {
		$(this).parents('.prefixes').find('.save-add-prefixes').removeClass('hidden');
		$(this).parents('.prefixes').find('.cancel-add-prefixes').removeClass('hidden');
	}
});

$(document).on('click', '.remove-prefix', function() {
	if (confirm("Жауапты өшіруге келісесізбе?")) {
		if ($(this).parents('.inline-ans-prefix').find('.ans-id').val() != 'new') {
			$(this).parents('.prefixes').find('.save-add-prefixes').removeClass('hidden');
			$(this).parents('.prefixes').find('.cancel-add-prefixes').removeClass('hidden');
			
			$ans_label = $(this).parents('.inline-ans-prefix');
			$ans_id = $ans_label.find('.ans-id');
			$ans_id.attr('data-name', $ans_id.attr('name'));
			$ans_id.removeAttr('name','');

			$prefix = $ans_label.find('.ans-prefix-input')
			$prefix.attr('data-name', $prefix.attr('name'));
			$prefix.removeAttr('name', '');

			$torf = $ans_label.find('.ans-prefix-radio');
			$torf.attr('data-name', $torf.attr('name'));
			$torf.prop('checked', false);
			$torf.removeAttr('name', '');
			$(this).parents('.inline-ans-prefix').addClass('hidden');
		} else {
			$(this).parents('.inline-ans-prefix').remove();
		}
	}
});

$(document).on('submit', '.prefixes', function($e) {
	$e.preventDefault();
	$has_checked = false;
	$ans_count = 0;
	$.each($(this).find('.ans-prefix-radio'), function() {
		if ($(this).prop('checked') && !$has_checked) {
			$has_checked = true;
		}

		if (!$(this).parents('.inline-ans-prefix').hasClass('hidden')) {
			$ans_count++;
		}
	});
	if ($ans_count == 0) {
		$has_checked = true;
	}
	if (!$has_checked) {
		$(this).find('.message').html('Дурыс жауабын белгілеу керек');
	} else {
		$load_url = $(this).parents('.list-position-inside').data('dir');
		console.log($load_url);
		$current_element = $(this);
		$.ajax({
			type: 'POST',
			method: 'POST',
			url: 'material/controller.php?add_test_prefixes',
			data: new FormData(this),
			contentType: false,
    	    cache: false,
			processData: false,
			beforeSend: function() {
				set_load($current_element);
			},
			success: function($data) {
				console.log($data);
				$json = $.parseJSON($data);
				console.log($json);
				remove_load();
				if ($json.success) {
					$('.materials #material-body #material-content').load($load_url);
				}
			}
		});	
	}
});

$(document).on('click', '.pop-up-on', function() {

	$tva_id = $(this).parents('li').data('id');
	$subtopic_id = $(this).data('sid');
	$btn_text = $(this).text();
	$this = $(this);
	$.ajax({
		url: 'material/controller.php?set_pop_up_on&tva_id='+$tva_id+'&subtopic_id='+$subtopic_id,
		type: 'GET',
		cache: false,
		beforeSend: function() {
			$this.text('Загрузка...');
		},
		success: function($data) {
			$this.text($btn_text);
			$json = $.parseJSON($data);
			if ($json.success) {
				$this.parents('ul').find('li').each(function() {
					$tmp_tva_id = $(this).data('id');
					if ($tmp_tva_id == $tva_id) {
						$(this).find('.pop-up-btn').removeClass('pop-up-on');
						$(this).find('.pop-up-btn').removeClass('btn-default');
						$(this).find('.pop-up-btn').addClass('pop-up-off');
						$(this).find('.pop-up-btn').addClass('btn-success');
						$(this).find('.pop-up-btn').text('pop up on');
					} else {
						$(this).find('.pop-up-btn').removeClass('pop-up-off');
						$(this).find('.pop-up-btn').removeClass('btn-success');
						$(this).find('.pop-up-btn').addClass('pop-up-on');
						$(this).find('.pop-up-btn').addClass('btn-default');
						$(this).find('.pop-up-btn').text('pop up off');
					}
				});
			}
		}
	});
});

$(document).on('click', '.pop-up-off', function() {

	$tva_id = $(this).parents('li').data('id');
	$btn_text = $(this).text();
	$this = $(this);
	$.ajax({
		url: 'material/controller.php?set_pop_up_off&tva_id='+$tva_id,
		type: 'GET',
		cache: false,
		beforeSend: function() {
			$this.text('Загрузка...');
		},
		success: function($data) {
			$this.text($btn_text);
			$json = $.parseJSON($data);
			if ($json.success) {
				$this.removeClass('pop-up-off');
				$this.removeClass('btn-success');
				$this.addClass('pop-up-on');
				$this.addClass('btn-default');
				$this.text('pop up off');
			}
		}
	});
});