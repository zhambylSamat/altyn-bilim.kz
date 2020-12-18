function render_full_instruction() {
	$link = 'https://vimeo.com/487212668';
	// $link = 'https://vimeo.com/481386833';
	$vimeo_id = get_vimeo_id_by_link($link);

	$options = {
		id: $vimeo_id,
		width: $('#vimeo-full-instruction-video').width()
	};

	$html = "<div class='vimeo-video' id='vimeo-video-box'></div>";

	$('#vimeo-full-instruction-video').html($html);

	$.when(render_instruction_vimeo_video($('#vimeo-video-box'), $options)).done(function() {
		set_intruction_vimeo_function($vimeo_id);
		$('.instruction-video-timecode').attr('data-vimeo-id', $vimeo_id);
	});
}

function render_instruction_vimeo_video($element, $options) {
	$element.html("<b>Загрузка...</b>");
	var player = new Vimeo.Player($element, $options);
	$players[$options.id] = player;
	$players[$options.id].ready().then(function() {
		$('#vimeo-video-box').find('iframe').css({'border' : '1px solid #BEBEBE', 'border-radius': '5px'});
		console.log('video successfully loaded');
		$element.find('b').remove();
	}).catch(function($error) {
		console.log('error on load video');
		console.log($error);
		$element.find('b').remove();
	});
}

function set_intruction_vimeo_function($vimeo_id) {
	$player_events[$vimeo_id] = {is_played: false,
								interval: false,
								video_id: $vimeo_id};
}

$(document).on('click', '.go-to-instruction-time', function() {
	$(this).css({'color': 'purple'});
	$("html, body").animate({ 
            scrollTop: 0 
        }, "slow");
	$time = $(this).data('time');
	$vimeo_id = $(this).parents('.instruction-video-timecode').data('vimeo-id');
	$players[$vimeo_id].setCurrentTime($time);
	$players[$vimeo_id].play();
});


$(document).on('click', '.open-use-coin-modal', function() {
	$link = $(this).data('url');
	// $link = 'https://vimeo.com/481386833';

	$html = "<div class='coin-use-insturction-vimeo-video'></div>";
	$.when($('#coin-use-modal').modal('show')).done(function() {
		$('#coin-use-modal .modal-body').html($html);

		$.ajax({
			url: 'https://vimeo.com/api/oembed.json?url='+$link+'&autoplay=1',
			type: 'GET',
			beforeSend: function() {
				$('.coin-use-insturction-vimeo-video').html("<center>Загрузка...</center>");
			}, 
			success: function($data) {
				$('#lll').css('display','none');
				$('.coin-use-insturction-vimeo-video').html('<div style="width: 100%;"><center>'+$data.html+'</center></div>');
				$('.coin-use-insturction-vimeo-video').find('iframe').attr('width', '100%');
				if ($('body').width() > 768) {
					$('.coin-use-insturction-vimeo-video').find('iframe').attr('height', '400');
				}
			}
		});
	});
});