$loader_gif = "https://online.altyn-bilim.kz/academy/common/assets/icons/loader.gif";
$loader_id = "loader";
$loader_box_css = 'width: 100%; height: 100%; min-height: 256px; position: absolute; z-index: 1000; background-color: rgba(240, 240, 240, 0.6); box-shadow: 0px 0px 10px #F0F0F0; border-radius: 10px;';
$loader_content_css = "position: absolute; left: 50%; transform: translate(-50%, -50%);";
$load_html = "<div id='" + $loader_id + "' style='" + $loader_box_css + "'><img src='" + $loader_gif + "' style='" + $loader_content_css + "'></div>";

$players = {};

function set_load($elem) {
	remove_load();
	$($elem).prepend($load_html);
}

function remove_load() {
	$('body').find('#' + $loader_id).remove();
}

$(function() {
	set_load('body');
});

$(document).ready(function() {
	remove_load();
});

$(document).on('click', '.navigation', function() {
	set_navigation($(this));
});

function set_navigation($element, $params='') {
	if (!$element.hasClass('active')) {
		$level = $element.attr('level');
		$('.nav-' + $level).removeClass('active');
		$element.addClass('active');
		$content_key = $element.attr('content-key');
		$direction = $element.attr('dir');
		set_load('.box-content-' + $level);
		$('.box-content-' + $level).load($direction + 'index.php?content_key=' + $content_key+'&'+$params);
	}
}

$(document).on('mouseover', '.sortable', function() {
	if (!$(this).hasClass('ui-sortable')) {
		$(this).sortable();
    	$(this).disableSelection();
	}
});

function get_vimeo_oembed($link, $content = 'body') {
	return $.ajax({
		url: 'https://vimeo.com/api/oembed.json?url=' + $link,
		type: "GET",
		beforeSend: function(){
			set_load($content);
		}, success: function($data) {
			remove_load();
		}
	});
}

function get_end_video_timecode_by_id($id, $content = 'body') {
	$loc = window.location.href.split('/');
	$url = '';
	for ($i = 0; $i < $loc.length; $i++) {
		if ($loc[$i] == 'academy') {
			$url += $loc[$i] + '/';
			break;
		}
		$url += $loc[$i] + '/';
	}
	$url += 'controller.php?get_timecode';
	$form_data = new FormData();
	$form_data.append('id', $id);
	return $.ajax({
		url: $url,
		type: 'POST',
		data: $form_data,
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend: function() {
			set_load($content);
		}, success: function($data) {
			remove_load();
		}
	});
}

function render_vimeo_video($element, $options, $is_student = true) {
	$element.html("<b>Загрузка...</b>");
	console.log($options);
	var player = new Vimeo.Player($element, $options);
	$players[$options.id] = player;
	$players[$options.id].ready().then(function() {
		console.log('video successfully loaded');
		$element.find('b').remove();
	}).catch(function($error) {
		console.log('error on load video');
		console.log($error);
		$element.find('b').remove();
	});
}

function get_vimeo_id_by_link($link) {
	$splitted_link = $link.split('/');
	if ($splitted_link[$splitted_link.length - 1] != '') {
		return $splitted_link[$splitted_link.length - 1];
	}
	return $splitted_link[$splitted_link.length - 2];

}

function lightAlert($element, $color, $opacity, $time, callback = undefined){
	if ($color == 'red') {
		$color = '#D9534F';
	} else if ($color == 'green') {
		$color = '#5CB85C';
	} else if ($color == 'orange') {
		$color = '#F0AD4E';
	} else if ($color == 'lightBlue') {
		$color = "#339AF0";
	} else if ($color.substr(0, 1) != '#' || $color.length != 4 || $color.length != 7) {
		$color = '#FFFFFF';
	}

	$element.css({'background-color':$color});
	$res = $element.css( "background-color" );
	$bgColor = $res.substring(4, $res.length-1);
	$element.stop();
	$element.animate({backgroundColor: 'rgba('+$bgColor+', '+$opacity+')' },$time, function(){
		if (callback != undefined) {
			callback($element);
		}
	});
}



$(document).ready(function() {
	$('script').remove();
});


$close_nav_content_timer = '';
$(document).on('click', '.mob-nav-expand-btn', function() {
	if ($(this).hasClass('active')) {
		hide_nav_content();
	} else {
		show_nav_content();
	}
});

$(document).on('click', '.mob-nav-btn-text', function() {
	hide_nav_content();
});

function hide_nav_content() {
	clearInterval($close_nav_content_timer);
	$('.nav-animation-menu').removeClass('col-xs-3').addClass('col-xs-1');
	$('.nav-animation-content').removeClass('col-xs-9').addClass('col-xs-11');
	$('.mob-nav-icon').show();
	$('.mob-nav-text').hide();
	$('.mob-nav-btn').removeClass('mob-nav-btn-text');
	$('.mob-nav-expand-btn').removeClass('active');
}

function show_nav_content () {
	$close_nav_content_timer = setInterval(hide_nav_content, 5000);
	$('.nav-animation-menu').removeClass('col-xs-1').addClass('col-xs-3');
	$('.nav-animation-content').removeClass('col-xs-11').addClass('col-xs-9');
	$('.mob-nav-icon').hide();
	$('.mob-nav-text').show();
	$('.mob-nav-btn').addClass('mob-nav-btn-text');
	$('.mob-nav-expand-btn').addClass('active');
}


var get_url_params = function get_url_parameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

var clean_url = function clean_url_parameter() {
	var uri = window.location.toString();
	if (uri.indexOf("?") > 0) {
	    var clean_uri = uri.substring(0, uri.indexOf("?"));
	    window.history.replaceState({}, document.title, clean_uri);
	}
}
