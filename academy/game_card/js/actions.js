function set_loading() {
	$loading_url = 'img/source.gif';
	$('#content').addClass('hide');
	if ($('#loading').hasClass('hide')) {
		$('#loading').removeClass('hide');
	} else {
		$('#loading').html('<center><img src="'+$loading_url+'" class="img-responsive"></center>');
	}
}

function remove_loading() {
	$('#content').removeClass('hide');
	$('#loading').addClass('hide').html('<center><img src="'+$loading_url+'"></center>');
}

$(document).ready(function() {
	set_loading();
	$urlParams = new URLSearchParams(window.location.search);
	if ($urlParams.has('state')) {
		if ($urlParams.get('state') == 'topic' && $urlParams.has('dir') && $urlParams.has('status')) {
			set_topics_html($urlParams.get('dir'), $urlParams.get('status'));
		} else if ($urlParams.get('state') == 'question' && $urlParams.has('dir') && $urlParams.has('random')) {
			set_question_html($urlParams.get('dir'), $urlParams.get('random'));
		} else {
			set_cards_html();
		}
	} else {
		set_cards_html();
	}
});

$(document).on('click', '.choose-subject-img', function() {
	$dir = $(this).data('dir');
	$status = $(this).data('status');
	$urlParams = new URLSearchParams(window.location.search);
	// console.log(urlParams.has('post')); // true
	// console.log(urlParams.get('action')); // "edit"
	// console.log(urlParams.getAll('action')); // ["edit"]
	// console.log(urlParams.toString()); // "?post=1234&action=edit"
	// console.log(urlParams.append('active', '1')); // "?post=1234&action=edit&active=1"
	set_topics_html($dir, $status, 1);
});

$(document).on('click', '.choose-topic-img', function() {
	$dir = $(this).data('dir');
	set_question_html($dir, false, 1);
});

$(document).on('click', '.choose-all-random-topic-img', function() {
	$dir = $(this).data('dir');
	set_question_html($dir, true, 1);
});

function set_cards_html() {
	set_loading();
	$('#content').html('<center><h3>Загрузка...</h3></center>');
	$('#content').load('cards.php', function() {
		remove_loading();
	});
}

function set_topics_html($dir, $status, $load=0) {
	if ($dir != '' && $status != '') {
		if ($status == 'continue') {
			$dir_extra = '?state=topic&dir='+$dir+'&status='+$status;
			if ($load == 1) {
				window.location.href = $dir_extra;
			} else {
				set_loading();
				$('#content').html('<center><h3>Загрузка...</h3></center>');
				$('#content').load('topics.php?dir='+$dir, function() {
					remove_loading();
				});
			}
		} else {
			console.log('question_html');
		}
	} else {
		set_cards_html();
	}
}

function set_question_html($dir, $is_all_topic_random=false, $load=0) {
	if ($dir != '') {
		$dir_extra = '?state=question&dir='+$dir+'&random='+$is_all_topic_random;
		if ($load == 1) {
			window.location.href = $dir_extra;
		} else {
			set_loading();
			$('#content').html('<center><h>Загрузка...</h></center>');
			$('#content').load('subtopics.php?dir='+$dir+'&is_random='+$is_all_topic_random, function() {
				remove_loading();
			});
		}
	} else {
		set_cards_html();
	}
}

$(document).on('click', '.question-img', function() {
	$(this).removeClass('card-front').addClass('card-front-shown');
	$(this).parents('.q-a-images').find('.answer-img').removeClass('card-back').addClass('card-back-shown');
});

$(document).on('click', '.answer-img', function() {
	hide_prev_question_btn();
	$index = $(this).parents('.q-a-images').data('index');
	$(this).parents('.q-a-images').animate({right: '2000px', opacity: 0}, function() {
		$(this).removeClass('current-question');
		$.when($(this).addClass('shown-question')).done(function() {
			$card_front = $(this).find('.card-front-shown');
			$card_front.addClass('card-front');
			$card_front.removeClass('card-front-shown');
			$card_back = $(this).find('.card-back-shown');
			$card_back.addClass('card-back');
			$card_back.removeClass('card-back-shown');
		});
	});
	$next = $(this).parents('.q-a-images').next();
	$next.css({'left': '2000px', 'opacity': '0'}).removeClass('questions-in-queue').addClass('current-question').animate({left: '0px', opacity: '1'}, function() {
		$.when($(this).removeAttr('style')).done(function() {
			if ($index + 1 > 0) {
				show_prev_question_btn();
			}
		});
		append_next_question();
	});
});

$(document).on('click', '.previous-question', function() {
	hide_prev_question_btn();
	$current_question = $('.select-question').find('.current-question');
	$current_index = $current_question.data('index');
	$current_question.animate({left: '2000px', opacity: 0}, function() {
		$(this).removeClass('current-question');
		$.when($(this).addClass('questions-in-queue')).done(function() {
			$card_front = $(this).find('.card-front-shown');
			$card_front.addClass('card-front');
			$card_front.removeClass('card-front-shown');
			$card_back = $(this).find('.card-back-shown');
			$card_back.addClass('card-back');
			$card_back.removeClass('card-back-shown');
			$(this).removeAttr('style');
		});
	});
	$current_question.prev().css({'right': '2000px', 'opacity': '0'}).removeClass('shown-question').addClass('current-question').animate({right: '0px', opacity: '1'}, function() {
		$(this).removeAttr('style');
		if ($current_index == -1 || $current_index-1 > 0) {
			show_prev_question_btn();
		}
	});
});

function show_prev_question_btn() {
	$('.previous-question').removeAttr('disabled');
}

function hide_prev_question_btn() {
	$('.previous-question').attr('disabled', 'disabled');
}

function append_next_question() {
	$last_question = $('.q-a-images').last();
	$index = $last_question.data('index') + 1;
	$stop_img = 'stop.jpg';
	if ($index != 0) {
		$.ajax({
			url: 'controllers.php?get_next_question&index='+$index,
			type: 'GET',
			cache: false,
			success: function($data) {
				$json = $.parseJSON($data);
				$html = "";
				if ($json.data.length != 0) {
					$.each($json.data, function($index, $value) {
						$html += "<div class='q-a-images questions-in-queue' data-index='"+$index+"'>";
							$html += "<div class='card-image question-img card-front'>";
								$html += "<img src='"+$value.q_dir+"' class='img-response'/>";
							$html += "</div>";
							$html += "<div class='card-image answer-img card-back'>";
								$html += "<img src='"+$value.a_dir+"' class='img-response'/>";
							$html += "</div>";
						$html += "</div>";
					});
				} else {
					$question_img = $last_question.find('.question-img').find('img').attr('src');
					$question_img_splitted = $question_img.split('/');
					$root_dir = $question_img_splitted[0]+'/'+$question_img_splitted[1];
					$html += "<div class='q-a-images questions-in-queue finish-img' data-index='-1'>";
						$html += "<div class='card-image'>";
							$html += "<img src='"+($root_dir+'/'+$stop_img)+"' class='img-response'/>";
						$html += "</div>"
					$html += "</div>";
				}
				$last_question.after($html);
			}
		});
	}
}

$(document).on('click', '.finish-img', function() {
	window.history.back();
});
