$is_mobile = $('body').width() < 768 ? true : false;

$(document).ready(function() {
	$element = $('.material-body');
	render_selected_content($element);
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
	$element.find('.tutorial-video-tmp').each(function() {
		$video_id = $(this).find('input[name=video_id]').val();
		$link = $(this).find('input[name=link]').val();
		$vimeo_id = get_vimeo_id_by_link($link);
		$options = {
			id: $vimeo_id,
			width: $(this).width()
		};
		render_vimeo_video($(this), $options);
	});

	$element.find('.end_video_tmp').each(function() {
		$video_id = $(this).find('input[name=video_id]').val();
		$link = $(this).find('input[name=link]').val();
		$video_duration = $(this).find('input[name=video_duration]').val();
		$video_second_duration = $(this).find('input[name=video_second_duration]').val();
		$vimeo_id = get_vimeo_id_by_link($link);
		$body_width = $(this).parents('.material-body').width();
		$options = {
			id: $vimeo_id,
			width: $is_mobile ? $body_width : $body_width / 2.1
		};
		$this = $(this);
		$.when(render_vimeo_video($this, $options)).done(function() {
			$html = "<div id='timecode-"+$video_id+"'></div>";
			$this.parents('.end_video_content').find('.timecode').html($html);
			$element = $this.parents('.end_video_content').find('#timecode-'+$video_id);
			set_timecode_list($video_id, $vimeo_id, $video_second_duration, $element);
		});
	});
}


function set_timecode_list($id, $vimeo_id, $duration, $element = '.timecode') {
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
				$html += "<div class='col-xs-6'><span class='item'>";
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
});

$(document).on('click', '.open-pre-test-start', function() {
	$subtopic_id = $(this).data('subtopic-id');
	$('#pre-test-start-form').find('input[name=subtopic_id]').val($subtopic_id);
});

$(document).on('submit', '#pre-test-start-form', function($e) {
	$e.preventDefault();
	$fio = $(this).find('input[name=fio]').val();
	$code = $(this).find('input[name=code]').val();
	$subtopic_id = $(this).find('input[name=subtopic_id]').val();
	window.open('testing.php?fio='+$fio+'&code='+$code+'&subtopic_id='+$subtopic_id);
});




$(document).on('change', '.answer-prefix-radio', function() {
	$.each($(this).parents('tr').find('td'), function() {
		$(this).removeClass('success');
	});
	if ($(this).prop('checked')) {
		$(this).parents('td').addClass('success');
	}
});

$(document).on('submit', '.submit-test-form', function($e) {
	$e.preventDefault();

	if (confirm('Тестті аяқтайсыңба?')) {
		$.ajax({
			url: 'controller.php?submit_test',
			type: 'POST',
			data: new FormData(this),
			contentType: false,
		    cache: false,
			processData:false,
			beforeSend: function() {
				set_load($('body'));
			},
			success: function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					location.reload();
				}
			}
		});
	}
});