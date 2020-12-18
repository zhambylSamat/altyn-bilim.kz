$player_events = {};
$is_mobile = $('body').width() < 768 ? true : false;



$(document).ready(function() {
	$navigation = get_url_params('nav');
	if ($navigation !== undefined) {
		if ($navigation == 'registration-navigation') {
			$element = $('.'+$navigation).parents('.navigation');
			$extra_param = get_url_params('recomendation_text');
			if ($extra_param !== undefined) {
				console.log($extra_param);
				set_navigation($element, ('recomendation_text='+$extra_param.replaceAll(' ', '_')));
			} else {
				set_navigation($element);
			}
		}
	}
});


$(document).on('click', '.lessons-box', function() {
	$group_id = $(this).data('gi-id');
	$current_element = $(this);
	if ($group_id != '') {
		$.ajax({
			url: 'lesson/controller.php?select_lesson&group_id='+$group_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('#lesson-body'));
			},
			success: function($data) {
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					$('#lesson-body').load('lesson/components/index.php?content='+$data);
				}
			}
		});
	}
});

$(document).on('click', '#back-to-subtopic-list, #lesson-process-nav', function(){
	$current_element = $(this);
	$.ajax({
		url: 'lesson/controller.php?select_subtopic',
		type: 'GET',
		cache: false,
		beforeSend: function() {
			set_load($('#lesson-body'));
		},
		success: function($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				$('#lesson-body').load('lesson/components/index.php', function() {
					remove_load();
				});
			}
		}
	});
});






$(document).on('click', '.unbox-material-body', function() {
	$element = $(this);
	$element.removeClass('unbox-material-body');
	$.when($element.addClass('material-body')).done(function() {
		if (!$element.hasClass('shown')) {
			render_selected_content($element);
		}
		$element.addClass('shown');
	});
});

$(document).on('click', '.topic-title', function() {
	$element = $(this).parents('.material-body');;
	$element.removeClass('material-body');
	$element.addClass('unbox-material-body');
});


function render_selected_content($element) {
	$video_count = 0;
	$element.find('.tutorial-video-tmp').each(function() {
		$video_count++;
		$video_id = $(this).find('input[name=video_id]').val();
		$link = $(this).find('input[name=link]').val();
		$duration = $(this).find('input[name=duration]').val();
		$access_before = $(this).find('input[name=access_before]').val();
		$action_id = $(this).find('input[name=action-id]').val();
		$pop_up = $(this).find('input[name=video-pop-up]').val();

		$hours = parseInt($duration/3600);
		$minutes = parseInt(($duration-$hours*3600)/60);
		$seconds = parseInt(($duration-$hours*3600-$minutes*60));
		$duration = '';
		if ($hours != 0) {
			$duration += $hours + ' сағат ';
		}
		if ($hours > 0 || $minutes != 0) {
			$duration += $minutes + ' минут ';
		}
		$duration += $seconds + ' секунд';
		$parent = $(this).parents('.tutorial_video_content');
		if ($link == '') {
			$html = "<div class='col-md-6 col-sm-6 col-xs-6'><center><h3>Видео көретін уақыт аяқталды!</h3></center></div>";
			$parent.append($html);
		} else {
			$vimeo_id = get_vimeo_id_by_link($link);
			if ($is_mobile) {
				// $options = {
				// 	id: $vimeo_id,
				// 	width: $(this).width()
				// };
				$options = {
					id: $vimeo_id,
					width: $(this).parents('.tutorial_video_content').width()			
				}
			} else {
				$options = {
					id: $vimeo_id,
					// width: $(this).width()
					height: screen.height*0.5
				};
			}

			// $parent.find('.video-duration').html("Видеоның ұзақтығы: "+$duration);
			$extra_html = "";
			if ($video_count > 1) {
				$extra_html = "<span style='margin-top:3%;' class='glyphicon glyphicon-menu-down'></span>";
			}
			$html = $extra_html+"<div class='vimeo-video' id='vimeo-"+$vimeo_id+"' data-id='"+$video_id+"'></div>";
			// $html += "<h4 class='access_before'>";
			// 	$html += $access_before != undefined && $access_before != '' ? "Видеоны келесі уақытқа дейін көруге болады: " + $access_before : '';
			// $html += "</h4>";
			$parent.append($html);
			// render_vimeo_video($('#vimeo-'+$vimeo_id), $options);
			$.when(render_vimeo_video($('#vimeo-'+$vimeo_id), $options)).done(function() {
				set_vimeo_functions($vimeo_id, $video_id, $action_id, 'tutorial_video');
				$(this).remove();
			});

			if ($pop_up == 1) {
				$('#vimeo-video').fadeIn();
				load_vimeo_video($link);
			}
		}
	});

	$element.find('.end_video_tmp').each(function() {
		$video_id = $(this).find('input[name=video_id]').val();
		$link = $(this).find('input[name=link]').val();
		$duration = $(this).find('input[name=duration]').val();
		$access_before = $(this).find('input[name=access_before]').val();
		$action_id = $(this).find('input[name=action-id]').val();

		$hours = parseInt($duration/3600);
		$minutes = parseInt(($duration-$hours*3600)/60);
		$seconds = parseInt(($duration-$hours*3600-$minutes*60));
		$duration = '';
		if ($hours != 0) {
			$duration += $hours + ' сағат ';
		}
		if ($hours > 0 || $minutes != 0) {
			$duration += $minutes + ' минут ';
		}
		$duration += $seconds + ' секунд';
		$parent = $(this).parents('.end_video_content');

		$vimeo_id = get_vimeo_id_by_link($link);
		// $body_width = $(this).parents('.material-body').width();
		$body_width = $(this).width();
		$options = {
			id: $vimeo_id,
			// width: $is_mobile ? $body_width : $body_width / 1.8
			width: $(this).parents('.end_video_content').width()
		};

		// $parent.find('.video-duration').html("Видеоның ұзақтығы: "+$duration);
		$html = "<div class='vimeo-video' id='vimeo-"+$vimeo_id+"' data-id='"+$video_id+"'></div>";
		// $html += "<h4 class='access_before'>";
		// 	$html += $access_before != undefined && $access_before != '' ? "Видеоны келесі уақытқа дейін көруге болады: " + $access_before : '';
		// $html += "</h4>";
		$parent.append($html);

		$parent.find('.timecode').addClass('timecode-'+$vimeo_id);

		$this = $(this);
		$.when(render_vimeo_video($this, $options)).done(function() {
			$element = $this.parents('.end_video_content').find('.timecode-'+$vimeo_id);
			set_timecode_list($video_id, $vimeo_id, $element);
			set_vimeo_functions($vimeo_id, $video_id, $action_id, 'end_video');
			$(this).remove();
		});
	});
}


$(document).on('click', '#vimeo-video', function() {
	$('#vimeo-video').fadeOut();
	$('#vimeo-content').html('');
});

function load_vimeo_video(link, action, video_count){
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+link,
		type: "GET",
		beforeSend:function(){
			$('#vimeo-content').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#vimeo-content').html('<div class="vimeo_video"><button id="video-close-btn" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><center>'+data.html+'<b></b></center></div>');
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
}



function set_timecode_list($id, $vimeo_id, $element = '.timecode') {
	$.when(get_end_video_timecode_by_id($id, $element)).done(function($result){
		$json = $.parseJSON($result);
		$html = '';
		$html += "<div class='timecode-list scroll-container'>";
		if ($is_mobile) {
			$html += "<div class='container'><div class='row'>";
		}
		$count = 1;
		$.each($json.result, function($index, $value){
			$timer = "| ";
			if ($value.detailed_information.hour != 0) {
				$timer += ("0" + $value.detailed_information.hour).slice(-2)+':';
			} else if ($duration >= 3600) {
				$timer += "00:";
			}
			if ($value.detailed_information.minute != 0) {
				$timer += ("0" + $value.detailed_information.minute).slice(-2)+':';
			} else if ($duration >= 60) {
				$timer += '00:';
			}
			if ($value.detailed_information.second != 0) {
				$timer += ("0" + $value.detailed_information.second).slice(-2);
			} else {
				$timer += "00";
			}
			$title = $value.title.split(' ')[0]+' есеп';
			if ($is_mobile) {
				$html += "<div class='col-xs-6 go-to-time-box'><span class='item'>";
					$html += "<button class='btn btn-info btn-xs go-to-time' style='margin: 5px;' data-vimeo-id='" + $vimeo_id + "' data-time='"+$value.detailed_information.total_seconds+"'>"+$title+" "+$timer+"</button>";
				$html += "</span></div>";
			} else {
				$html += "<button class='btn btn-info btn-xs go-to-time' style='margin: 5px;' data-vimeo-id='" + $vimeo_id + "' data-time='"+$value.detailed_information.total_seconds+"'>"+$title+" "+$timer+"</button>";
			}
		});
		if ($is_mobile) {
			$html += "</div></div>";
		}
		$html += "</div>";
		$($element).html($html);
	});
}

$(document).on('click', '.timecode-list .go-to-time', function() {
	$time = $(this).data('time');
	$vimeo_id = $(this).data('vimeo-id');
	$players[$vimeo_id].setCurrentTime($time);
	$players[$vimeo_id].play();
});

function set_vimeo_functions($vimeo_id, $video_id, $action_id, $obj) {
	$player_events[$vimeo_id] = {is_played: false,
								interval: false,
								video_id: $video_id,
								when_play: $players[$vimeo_id].on('play', function() {
									if (!$player_events[$vimeo_id].is_played || true) {
										$.when(set_material_started_date($video_id, $action_id, $obj)).done(function($result){
											$html = '';
											$json = $.parseJSON($result);
											if ($json.success && $json.access_before != '') {
												// $html += "Видеоны келесі уақытқа дейін көруге болады: " + $json.access_before;
												$('#vimeo-' + $vimeo_id).parents('.video-content').find('.access_before').html($html);
											}
										});
										$player_events[$vimeo_id].is_played = true;
										$player_events[$vimeo_id].interval = setInterval(check_player_access_duration, 300000, $vimeo_id, $video_id, $action_id, $obj);
									}
								})};
}

function check_player_access_duration($vimeo_id, $video_id, $action_id, $obj, $element_class = '.vimeo-video') {
	$.ajax({
		url: 'lesson/controller.php?check_video_access&video_id=' + $video_id + "&action_id=" + $action_id + "&obj=" + $obj,
		type: 'GET',
		cache: false,
		success: function($data) {
			$json = $.parseJSON($data);
			if (!$json.success) {
				clearInterval($player_events[$vimeo_id].interval);
				$class = $element_class + ' #vimeo-' + $vimeo_id;
				$($class).parents('center').html("<h3>Видео көретін уақыт аяқталды!</h3>");
				$players[$vimeo_id].destroy();
				delete $player_events[$vimeo_id];
				delete $players[$vimeo_id];
			}
		}
	});
}

function set_material_started_date($obj_id, $action_id, $obj) {
	$form_data = new FormData();
	$form_data.append('obj_id', $obj_id);
	$form_data.append('action_id', $action_id);
	$form_data.append('obj', $obj);
	return $.ajax({
		url: 'lesson/controller.php?set_material_start_time',
		type: 'POST',
		data: $form_data,
		contentType: false,
	    cache: false,
		processData:false
	});
}







$selected_courses = [];
$selected_courses_content = {};
$selected_reserve = [];
$selected_reserve_content = {};
$(document).on('click', '.choose-group', function() {
	$subject_id = $(this).data('subject');
	$subject_title = $(this).text();
	$.ajax({
    	url: "register/controller.php?get_groups_by_subject="+$subject_id,
		beforeSend:function(){
			$('#choose-group .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS){
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$groups = data.groups;
	    		$html = "";
	    		$.each($groups, function(id, item) {
	    			$target_elem = "choose-topic";
	    			if (item.lesson_type == 'topic') {
	    				$target_elem = "choose-subtopic";
	    			}
	    			$html += "<button type='button' data-toggle='modal' data-target='#"+$target_elem+"' data-group='"+item.id+"' class='btn btn-md btn-info btn-block "+$target_elem+"' data-subject='"+$subject_title+"'>"+item.group_name+"</button>";
	    		});
	    		$('#choose-group .title').html($subject_title);
	    		$('#choose-group .modal-body').html($html);
	    	} else {
	    		$('#choose-group .modal-body').html("<center><b>ERROR</b></center>");
	    	}
	    },
	  	error: function(dataS) 
    	{
    		alert("Қате. Программистпен жолығыңыз. "+dataS);
    	} 	        
   	});
});

$(document).on('click', '.choose-topic', function() {
	$group_id = $(this).data('group');
	$group_name = $(this).text();
	$subject_title = $(this).data('subject');
	$.ajax({
		url: "register/controller.php?get_topics_by_group="+$group_id,
		beforeSend:function() {
			$('#choose-topic .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS) {
			data = $.parseJSON(dataS);
			if (data.success) {
				$topics = data.topics;
				$selected_courses_content[$group_id] = {'subject_title': $subject_title, 'group_name': $group_name, 'topic_title': '', 'subtopic_title': ''};
	    		$html = "";
	    		$.each($topics, function(id, item) {
	    			$html += "<button type='button' data-toggle='modal' data-target='#choose-subtopic' data-group='"+$group_id+"' data-topic='"+item.id+"' class='btn btn-md btn-info btn-block choose-subtopic'>"+item.title+"</button>";
	    		});
	    		$('#choose-topic .title').html($group_name);
	    		$('#choose-topic .modal-body').html($html);
			} else {
				$('#choose-topic .modal-body').html("<center><b>ERROR</b></center>");
			}
		}
	});
});

$(document).on('click', '.choose-subtopic', function() {
	$topic_id = $(this).data('topic');
	$group_id = $(this).data('group');
	$topic_title = $(this).text();
	$url = "register/controller.php?get_subtopics_by_topic="+$topic_id+"&group="+$group_id;
	if ($topic_id === undefined) {
		$url = "register/controller.php?get_subtopics_by_group="+$group_id;	
	}
	$.ajax({
		url: $url,
		beforeSend: function() {
			$('#choose-subtopic .modal-body').html("<center><b>Загрузка...</b></center>");
		},
		success: function(dataS) {
			data = $.parseJSON(dataS);
			if (data.success) {
				if ($selected_courses_content[$group_id] == undefined) {
					$selected_courses_content[$group_id] = {'subject_title': $subject_title, 'group_name': $topic_title, 'topic_title': '', 'subtopic_title': ''};
				}
				$subtopics = data.subtopics;
				$html = "<form><table class='table table-condensed'>";
				$prev_learned_date = null;
				$current_subtopic = null;
				$.each($subtopics, function(id, item) {
							$text_color = "";
							$content_disabled = "choose-subtopic-radio";
							$content_color = "";
							$checked = "";

							if (item.learned2 == 1) {
			    				$date = item.learned_date;
			    			} else {
			    				$date = item.will_learn_date;
			    			}
			    			$disabled = false;
			    			if (item.learned == '1' && item.learned2 == '1') {
			    				$disabled = true;
			    				$text_color = '#777';
			    				$content_disabled = 'active';
			    			}
			    			$extra_html = '';
			    			if (item.learned == '0' && item.learned2 == '1') {
			    				$extra_html = "<i style='color:#4B974B; display:block;'>Қазір группа осы тақырыпта</i>";
			    			}

			    			$value = $group_id+"-"+item.id;
							if ($selected_courses.includes($value)) {
								$checked = 'checked';
								$content_color = 'success';
							}

			    			$html += "<tr style='color:"+$text_color+"; cursor:pointer;' class='"+$content_disabled+" "+$content_color+"'>";
			    				$html += "<td>";
			    				if (!$disabled) {
			    					$html += "<input type='radio' class='subtopic-radio' data-topic='"+$topic_title+"' data-subtopic='"+item.title+"' name='subtopic' value='"+$value+"' "+$checked+">";
			    				}
			    				$html += "</td>";
				    			$html += "<td>"+item.title+$extra_html+"</td>";
				    			$html += "<td>"+$date+"</td>";
				    		$html += "</tr>";
						});
				$html += "</table></form>";
				$('#choose-subtopic .title').html($topic_title);
				$('#choose-subtopic .modal-body').html($html);
			} else {
				$('#choose-subtopic .modal-body').html('<center><b>ERROR</b></center>');
			}
		}
	});
});

$(document).on('click', '.reserve-topic', function() {
	$subject_id = $(this).data('subject');
	$subject_title = $(this).text();
	$.ajax({
		url: 'register/controller.php?get_topic_by_subject='+$subject_id,
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
					$html += "<tr style='cursor: pointer;' class='reserve-topic-radio "+$content_color+"'>";
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

$(document).on('click', '.choose-subtopic-radio', function(){
	$(this).find('.subtopic-radio').prop('checked', true);
	$(this).parent().find('.choose-subtopic-radio').removeClass('success');
	$(this).addClass('success');
	$topic_title = $(this).find('.subtopic-radio').data('topic');
	$subtopic_title = $(this).find('.subtopic-radio').data('subtopic');
	$group_subtopic = $(this).find('.subtopic-radio').val();
	$.each($(this).parent().find('.choose-subtopic-radio .subtopic-radio'), function(i, elem) {
		$removeItem = $(this).val().split('-')[0];
		$selected_courses = jQuery.grep($selected_courses, function(value) {
				return value.split('-')[0] != $removeItem;
		});
	});
	$selected_courses.push($(this).find('.subtopic-radio').val());
	$group_id = $(this).find('.subtopic-radio').val().split('-')[0];
	$selected_courses_content[$group_id]['topic_title'] = $topic_title;
	$selected_courses_content[$group_id]['subtopic_title'] = $subtopic_title;
	set_course_content($selected_courses, $selected_courses_content);
	$('#choose-subtopic').modal('hide');
	$('#choose-group').modal('hide');
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
	set_reserve_content($selected_reserve, $selected_reserve_content);
	$('#reserve-topic').modal('hide');
});

function set_course_content($courses_id, $courses) {
	if ($courses_id.length > 0) {
		$('.choosen-courses').removeClass('hidden');
	} else {
		$('.choosen-courses').addClass('hidden');
	}
	$html = "<ol>";
	$.each($courses_id, function(i, elem) {
		$id = parseInt(elem.split('-')[0]);
		$subject = $courses[$id]['subject_title'];
		$group = $courses[$id]['group_name'];
		$topic = $courses[$id]['topic_title'];
		$subtopic = $courses[$id]['subtopic_title'];
		$html += "<li>";
			$html += $subject + " | ";
			$html += $group + " | ";
			if ($group != $topic) {
				$html += $topic + " | ";
			}
			$html += $subtopic;
		$html += "</li>";
	});
	$html += "</ol>"
	$('.choosen-courses .course-content').html($html);
	$('input[name=courses]').val($courses_id.join('|'));
}

function set_reserve_content($reserves_id, $reserves) {
	if ($reserves_id.length > 0) {
		$('.choosen-reserves').removeClass('hidden');
	} else {
		$('.choosen-reserves').addClass('hidden');
	}
	$html = "<ol style='list-style: none;'>";
	$.each($reserves_id, function(i, elem) {
		$html += "<li style='font-size: 15px;'>";
			$html += "<table class='table' style='margin: 0; padding: 0;'><tr>";
				$html += "<td style='border: none;'>"+(i+1)+".</td>";
				$html += "<td style='border: none;'>"+$reserves[elem]['subject_title']+" | "+$reserves[elem]['topic_title']+"</td>";
				$html += "<td style='border: none;'><button type='button' class='btn btn-xs btn-danger pull-right remove-selected-topic' data-id='"+elem+"'><i class='fas fa-trash-alt'></i></button></td>";
			$html += "</tr></table>";
		$html += "</li>";
		// $html += "<li>"+$reserves[elem]['subject_title']+" | "+$reserves[elem]['topic_title']+"</li>";
	});
	$html += "</ol>";
	$('.choosen-reserves .reserve-content').html($html);
	$('input[name=reserves]').val($reserves_id.join('|'));
}

$(document).on('change', '.answer-prefix-radio', function() {
	$.each($(this).parents('tr').find('td'), function() {
		$(this).removeClass('success');
	});
	if ($(this).prop('checked')) {
		$(this).parents('td').addClass('success');
	}
});

$(document).on('click', '.register-btn', function() {
	$(this).parents('.row').find('.navigation').each(function() {
		if ($(this).attr('dir') == 'register/') {
			$(this).addClass('active');
			$level = $(this).attr('level');
			$content_key = $(this).attr('content-key');
			$direction = $(this).attr('dir');
			set_load('.box-content-' + $level);
			$('.box-content-' + $level).load($direction + 'index.php?content_key=' + $content_key);
		} else {
			$(this).removeClass('active');
		}
	});
});

$(document).on('click', '.show-subtopics-btn', function() {
	$(this).parents('.panel').find('.panel-body').slideToggle();
});

$(document).on('click', '.remove-registartion-reserve', function() {
	$this = $(this);
	if (confirm('Таңдаған тарауыңызды өшіруге келісесізбе?')) {
		$registration_reserve_id = $(this).data('id');
		$.ajax({
			url: 'lesson/controller.php?remove_registration_reserve&registration_reserve_id='+$registration_reserve_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($this.parents('#lesson-body'));
			},
			success: function($data) {
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					$this.parents('.lessons-box').remove();
				}
			}
		});
	}
});

function set_test_solve($subtopic_id, $material_test_action_id) {
	$.ajax({
		url: 'controller.php?get_material_test_solve&subtopic_id='+$subtopic_id+'&material_test_action_id='+$material_test_action_id,
		type: 'GET',
		cache: false,
		beforeSend: function() {
			$('#test-solve').html("<center>Загрузка...</center>");
		},
		success: function($data) {
			$json = $.parseJSON($data);

			if ($json.success) {
				$html_title = "<center><p id='test-solve-title'>Тесттегі есептердің шығару жолдары</p></center>";
				$html = "";
				$.each($json.result, function($index, $val) {
					$html += "<div class='test-solve-box' data-url='"+$val.link+"'>";
						$html += "<div class='test-solve-content'>";
							$html += "<span>"+$val.title+"</span>";
						$html += "</div>"
					$html += "</div>";
				});

				if ($html != '') {
					$html = $html_title+$html;
				}

				$('#test-solve').html($html);
			}
		}
	});
}


function set_test_result() {
	$.each($('.finish-test'), function() {
		$subtopic_id = $(this).find('input[name=subtopic_id]').val();
		$material_test_action_id = $(this).find('input[name=material_test_action_id]').val();
		$actual_result = $(this).find('input[name=actual_result]').val();
		$total_result = $(this).find('input[name=total_result]').val();

		$percent = ($actual_result / $total_result) * 100;
		$percent = Math.ceil($percent);
		$html = "<p style='font-size: 16px;'><b>Тестің қорытындысы:</b></p>";
		$html += "<p style='margin-left: 3%;'>Дұрыс жауап: <b>"+$actual_result+"</b></p>";
		$html += "<p style='margin-left: 3%;'>Қате жауап: <b>"+($total_result - $actual_result)+"</b></p>";
		$html += "<p style='margin-left: 3%;'>Қорытынды: <b>"+$percent+"</b>%</p>";
		
		$test_result = $.parseJSON(localStorage.getItem('test_result'));
		$access_to_result = false;
		if ($test_result !== null) {
			if ($test_result[$subtopic_id] !== undefined) {
				$.each($test_result[$subtopic_id], function($index, $test) {
					if ($test['material_test_action_id'] == $material_test_action_id) {
						$access_to_result = true;
					}
				});
			}
		}

		if ($access_to_result) {
			$html += "<a href='lesson/testing.php?subtopic_id="+$subtopic_id+"&mta="+$material_test_action_id+"' target='_blank' class='btn btn-md btn-success'>Қатемен жұмыс</a>";
		}


		$(this).append($html);
	});
}


function get_material_titles_by_subtopic($subtopic_ids) {
	$str_json = JSON.stringify($subtopic_ids);
	$data = new FormData();
	$data.append('subtopic_ids', $str_json);
	return $.ajax({
		url: 'lesson/controller.php?get_material_titles_by_subtopic',
		type: 'POST',
		data: $data,
		contentType: false,
	    cache: false,
		processData:false
	});
}


$(document).on('click', '.do-payment', function() {
	$element = $('#payment-nav').parents('.navigation');
	set_navigation($element);
});

$(document).on('click', '.open-trial-test-nav', function() {
	$element = $('#trial-test-nav').parents('.navigation');
	set_navigation($element);
});

$(document).on('click', '#no-lesson-btn', function() {
	$element = $('.registration-navigation').parents('.navigation');
	set_navigation($element);
});

$(document).on('click', '.go-to-instruction-page', function() {
	$element = $('.instruction-navigation').parents('.navigation');
	set_navigation($element);
});

$(document).on('click', '#student-nav-brand', function() {
	$element = $('.lesson-process-navigation').parents('.navigation');
	set_navigation($element);
});

$(document).on('click', '.go-to-full-course-navigation', function() {
	$element = $('.full-course-navigation').parents('.navigation');
	set_navigation($element);
});

$(document).on('keyup', '#promo-code', function() {
	$(this).val($(this).val().toUpperCase());
});

$(document).on('submit', '#friend-promo-code-form', function($e) {
	$e.preventDefault();
	$form = $(this);

	$.ajax({
		url: 'controller.php?submit_friends_promo_code',
		type: 'POST',
		data: new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend: function() {
			$form.find('input[type=submit]').val('Загрузка...').attr('disabled', 'disabled');
		},
		success: function($data) {
			$json = $.parseJSON($data);

			if ($json.success) {
				$form.find('#error-promo-code-message').text('');
				$form.find('#promo-code').val('');
				$html = "<center>";
					$html += "<p><span id='friends-name'>"+$json.data.last_name+" "+$json.data.first_name+"</span> досыңның промокодын енгіздің.</p>";
					$html += "<p>Осы жеңілдікті <i>'төлем'</i> парақшасында қолдана аласың.</p>";
				$html += "</center>";
				$('#friend-promo-code-content').html($html);
				lightAlert($('#friend-promo-code-content'), 'green', 0, 1000);
			} else {
				$message = "";
				if ($json.promo_code_message.friends_already_used) {
					$message = "Досың сенің промокдыңды қолданды. Оның промокдың қолдануға болмайды.";
				} else if ($json.promo_code_message.promo_code_already_used) {
					$message = "Досыңның промокодын бір рет қана қолдануға болады.";
				} else if ($json.promo_code_message.incorrect_promo_code) {
					$message = "Енгізіген промокод қате";
				} else if ($json.promo_code_message.use_self_promo_code) {
					$message = "Өзіңнің промокодыңды қолдануға боламайды!";
				} else if ($json.promo_code_message.already_payment_done) {
					$message = "Промо кодты тек жаңадан тіркелген оқушы қолдана алады.";
				}
				$form.find('#error-promo-code-message').text($message);
				$form.find('#promo-code').val('');
			}

			$form.find('input[type=submit]').val('Сақтау').removeAttr('disabled');
		}
	});
});

$(document).on('submit', '#use-coins-for-group-form', function($e) {
	$e.preventDefault();

	$selected = false;
	$group_student_id = 0;
	$group_name = "";
	$(this).find('input[name="group-student-id"]').each(function() {
		if ($(this).prop('checked') && !$selected) {
			$selected = true;
			$group_student_id = $(this).val();
			$group_name = $(this).parents('label').text();
		}
	});

	// console.log($selected, $group_student_id, $group_name);

	if ($selected) {
		$.ajax({
			url: 'lesson/controller.php?set_bonus_days_from_coins&group_student_id='+$group_student_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($('body'));
				$('#choose-groups-for-coins').modal('hide');
			},
			success: function($data) {
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					$title = $group_name + " пәніне қосымша 10 күн қосылды!<br><br>ЖАРАЙСЫҢ! Мақсатыңнан тайма!";
					Swal.fire({
						width: '50em',
						title: $title,
						icon: 'success'
					}).then((result) => {
						location.reload();
					});
					$('#collected-coin-box').remove();
					$total_coins = $('#total-coins-coin').text();
					$('#total-coins-coin').text($total_coins - 2000);
				} else {
					$title = "Белгісіз ақаулар шығып қалды менеджерге хабарласыңыз";
					Swal.fire({
						width: '50em',
						title: $title,
						icon: 'error',
					});
				}
			}
		});
	}
});

$(document).on('focus', '.freeze-lesson-datepicker-input', function() {
	$(this).datepicker({
		format: 'dd.mm.yyyy',
		// daysOfWeekDisabled: "0",
		daysOfWeekHighlighted: "0",
		todayHighlight: true,
		language: "ru",
		autoclose: true,
		maxViewMode: 0,
		todayBtn: "linked",
		weekStart: 1,
	});
});

$(document).on('click', '.cancel-freeze-lesson', function() {
	if (confirm("Оқуды жалғастыруға келісесің бе?")) {
		$.ajax({
			type: 'GET',
			url: 'lesson/controller.php?cancel_freeze_lesson',
			beforeSend: function() {
				set_load('body');
			},
			success: function($data) {
				$json = $.parseJSON($data);
				if ($json.success) {
					$.when(set_global_lesson_access()).done(function() {
						$.when(set_global_lesson_access()).done(function() {
							$.when(enable_global_previews_lesson()).done(function() {
								location.reload();
							});
						});
					});
				}
			}
		});
	}
});

function set_global_lesson_access() {
	return $.ajax({
		type: 'GET',
		url: '../cron/set_lesson_access.php'
	});
}

function enable_global_previews_lesson () {
	return $.ajax({
		type: 'GET',
		url: '../cron/enable_previews_lesson_after_cron.php'
	});
}

$(document).on('click', '.freeze-off', function() {
	$group_info_id = $(this).data('group-info-id');
	$.ajax({
		type: 'GET',
		url: 'lesson/controller.php?freeze_off&group_info_id='+$group_info_id,
		beforeSend: function() {
			set_load('body');
		},
		success: function($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				// $.when(set_global_lesson_access()).done(function() {
					// $.when(set_global_lesson_access()).done(function() {
						$.when(enable_global_previews_lesson()).done(function() {
							location.reload();
						});
					// });
				// });
			}
		}
	});
});



$(document).on('click', '.entrance-examination-btn', function() {
	$.ajax({
		type: "GET",
		url: '../controller.php?create_entrance_examination_object',
		beforeSend: function() {
			set_load('body');
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			console.log($json);
			if ($json.success) {
				window.open('https://old.altyn-bilim.kz/test/force_sign_in.php?ees_id='+$json.data.ees_id
																			+'&ees_code='+$json.data.ees_code
																			+'&ees_surname='+$json.data.ees_surname
																			+'&ees_name='+$json.data.ees_name
																			+'&test_result='+JSON.stringify($json.data.test_result)
																			+'&finish='+($json.data.finish ? 1 : 0)
																			+'&cabinet=1', '_blank');
				// window.open('http://localhost/altynbilim/test/force_sign_in.php?ees_id='+$json.data.ees_id
				// 															+'&ees_code='+$json.data.ees_code
				// 															+'&ees_surname='+$json.data.ees_surname
				// 															+'&ees_name='+$json.data.ees_name
				// 															+'&test_result='+JSON.stringify($json.data.test_result)
				// 															+'&finish='+($json.data.finish ? 1 : 0)
				// 															+'&cabinet=1', '_blank');
			}
		}
	});
});

$(document).on('click', '.remove-selected-topic', function() {
	$element_id = $(this).data('id');
	$removeItem = $element_id.split('-')[0];
	$selected_reserve = jQuery.grep($selected_reserve, function(value) {
		return value.split('-')[0] != $removeItem;
	});
	$(this).parents('li').remove();

	if ($selected_reserve.length == 0) {
		$('.choosen-reserves').addClass('hidden');
	}
});


function render_register_course_video_instruction() {
	$link = 'https://vimeo.com/489747665';
	// $link = 'https://vimeo.com/481386833';
	$width = $('#register-course-instruction-video').width();
	if ($('body').width() > 768) {
		$width = $width*0.7;
	}
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+$link+'&width='+$width,
		type: "GET",
		beforeSend:function(){
			$('#vimeo-content').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#register-course-instruction-video').html('<center>'+data.html+'</center>');
			
			if ($('body').width() > 768) {
				// $('.payment-instruction-vimeo-video').find('iframe').attr('height', '400');
				$('#register-course-instruction-video').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 4%;');
			} else {
				$('#register-course-instruction-video').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 10%;');
			}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
}
