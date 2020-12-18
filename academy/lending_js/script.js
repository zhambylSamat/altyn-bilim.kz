$(document).on('click', '#lesson-step-btn-group button', function() {
	$step = $(this).data('step');

	$('#lesson-step-btn-group button').each(function() {
		$(this).removeClass('lesson-step-btn-focus');
	});

	$(this).addClass('lesson-step-btn-focus');

	$('.lesson-step').each(function() {
		$(this).css({'display': 'none'});
	});

	$('#step-'+$step).css({'display': 'block'});
});

$(document).on('click', '.parent-faq-question', function(){

	if (!$(this).parents('.parent-faq').hasClass('parent-faq-active')) {
		$data_num = $(this).parents('.parent-faq').data('num');

		$.each($('.parent-faq'), function() {
			$(this).removeClass('parent-faq-active');

			$.each($(this).find('.parent-faq-answer'), function() {
				$(this).slideUp();
			})
		});

		$.each($('.parent-faq-appropriate-img'), function() {
			$(this).css({'display': 'none'});
		});

		$(this).parents('.parent-faq').addClass('parent-faq-active');
		$(this).parents('.parent-faq').find('.parent-faq-answer').slideDown();
		$('.parent-faq-appropriate-img[data-num='+$data_num+']').css({'position' : 'relative', 'display' : 'block', 'opacity': 0, 'left' : '250px'}).animate({left: '0px', opacity: 1});
	} else {
		$(this).parents('.parent-faq').find('.parent-faq-answer').slideUp();
	}
});

$(document).on('click', '.section-11-question', function() {
	if (!$(this).hasClass('section-11-question-active')) {
		$data_num = $(this).data('num');

		$.each($('.section-11-question'), function() {
			$(this).removeClass('section-11-question-active');
		});

		$.each($('.section-11-answer'), function() {
			$(this).removeClass('section-11-answer-active');
			$(this).removeAttr('style');
		});

		$(this).addClass('section-11-question-active');

		$('.section-11-answer[data-num='+$data_num+']').addClass('section-11-answer-active');
		$('.section-11-answer[data-num='+$data_num+']').css({'position' : 'relative', 'display' : 'block', 'opacity': 0, 'left' : '250px'}).animate({left: '0px', opacity: 1});
	}
});

$(document).on('click', '.section-11-faq-mobile-question', function() {
	if (!$(this).hasClass('section-11-faq-mobile-question-active')) {
		$.each($('.section-11-faq-mobile'), function() {
			$(this).removeClass('section-11-faq-mobile-active');
		});

		$.each($('.section-11-faq-mobile-question'), function() {
			$(this).removeClass('section-11-faq-mobile-question-active');
		});

		$.each($('.section-11-answer-mobile'), function() {
			$elem = $(this);
			$(this).slideUp();
		});

		$(this).parents('.section-11-faq-mobile').addClass('section-11-faq-mobile-active');
		$(this).addClass('section-11-faq-mobile-question-active');
		$(this).parents('.section-11-faq-mobile').find('.section-11-answer-mobile').slideDown();
	}
});



$(document).on('click', '.subject-box-btn', function() {
	$subject_title = $(this).data('title');
	$subject_id = $(this).data('id');
	$type = $(this).data('type');

	if ($type == 'topic') {
		$.ajax({
			url: 'controller.php?get_topic_by_subject='+$subject_id,
			beforeSend: function() {
				$('#topics-modal .modal-body').html("<center><b>Загрузка...</b></center>");
			},
			success: function(dataS) {
				data = $.parseJSON(dataS);
				if (data.success) {
					$html = "<table class='table table-condensed'>";
					$.each(data.topics, function(i, item) {
						$html += "<tr style='cursor: pointer; width: 100%;'>";
							$html += "<td style='width: 70%;'>"+item.title+"</td>";
							// $html += "<td style='width: 25%;'>Ұзақтығы: "+item.subtopic_count+" сабақ</td>";
						$html += "</tr>";
					});
					$html += "</table>";
					$('#topics-modal .title').html($subject_title+' | тараулары');
					$('#topics-modal .modal-body').html($html);
				} else {
					$('#topics-modal .modal-body').html('<center><b>ERROR</b></center>');
				}
			}
		});
	} else if ($type == 'subtopic') {
		$.ajax({
			url: 'controller.php?get_subtopic_by_topic='+$subject_id,
			beforeSend: function() {
				$('#topics-modal .modal-body').html("<center><b>Загрузка...</b></center>");
			},
			success: function(dataS) {
				data = $.parseJSON(dataS);
				if (data.success) {
					$html = "<table class='table table-condensed'>";
					$.each(data.subtopics, function(i, item) {
						$html += "<tr style='cursor: pointer; width: 100%;'>";
							$html += "<td style='width: 70%;'>"+item.title+"</td>";
						$html += "</tr>";
					});
					$html += "</table>";
					$('#topics-modal .title').html($subject_title+' | тақырыптары');
					$('#topics-modal .modal-body').html($html);
				} else {
					$('#topics-modal .modal-body').html('<center><b>ERROR</b></center>');
				}
			}
		});
	}
});


$(document).on('click', '.faq-title', function() {
	$this = $(this).parents('.faq-topics');
	if (!$this.hasClass('faq-open')) {
		$('.faq-content').each(function() {
			$(this).slideUp();
			$(this).parents('.faq-topics').removeClass('faq-open');
		});

		$this.find('.faq-content').slideDown();
		$this.addClass('faq-open');
	} else {
		$this.find('.faq-content').slideUp();
		$this.removeClass('faq-open')
	}
});


$(document).on('click', '#welcome-video-btn, .teacher-welcome', function() {
	$url = $(this).data('url');
	$('#vimeo-video').fadeIn();
	load_vimeo_video($url);
});

$(document).on('click', '#vimeo-video', function() {
	$('#vimeo-video').fadeOut();
	$('#vimeo-content').html('');
});

function load_vimeo_video(link, action, video_count){
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+link+'&autoplay=1',
		type: "GET",
		beforeSend:function(){
			$('#vimeo-content').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#vimeo-content').html('<div class="vimeo_video"><button id="video-close-btn" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><center>'+data.html+'</center></div>');
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
}

$(document).ready(function() {
	check_login_fail();

	$vimeo_url = $('#welcome-vimeo-video').data('url');
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+$vimeo_url,
		type: "GET",
		beforeSend:function(){
			$('#welcome-vimeo-video').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#welcome-vimeo-video').html(data.html);
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
});

function check_login_fail() {
	$login_failure = getUrlParameter('login_error');
	if ($login_failure == 'password_wrong') {
		$('#login-error').show();
		$('#login-error').html('Телефон номерің немесе құпия сөзің қате енгізілді');
		$('#login-modal').modal('show');
	} else if ($login_failure == 'student_account_blocked') {
		$('#login-error').show();
		$('#login-error').html('Сенің жеке кабинетің блокка түсіп қалды менеджерге хабарлас! <a target="_blank" href="https://wa.me/77773890099?text=Сәлеметсіз бе. Менің жеке кабинетім блокка түсіп қалды.">+7 777 389 0099</a>');
		$('#login-modal').modal('show');
	}
	var uri = window.location.toString();
	if (uri.indexOf("?") > 0) {
	    var clean_uri = uri.substring(0, uri.indexOf("?"));
	    window.history.replaceState({}, document.title, clean_uri);
	}
}


$anim = true;
$(document).scroll(function(){
	$h = $(window).height();
	$y = $(this).scrollTop();
	$s_3 = $("#section-counter").offset().top;
	if(($s_3-$y)<=($h) && $anim){
		anim();
		$anim = false;
	}
	else if (($s_3-$y)>=$h){
		$anim = true;
	}
});
function anim(){
	var decimal_factor = 1;
	$('#count-1').addClass('big-count');
	$live_students_count = $('#live-students-count').html();
	$('#count-1').animateNumber({
	    number: $live_students_count * decimal_factor,
	    // color: '#2A3279',
	    // 'font-size': '300%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number);
	    }
	},1500);

	decimal_factor = 1;
	$('#count-2').addClass('big-count');
	$('#count-2').animateNumber({
	    number: 96 * decimal_factor,
	    // color: '#2A3279',
	    // 'font-size': '300%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number+"%");
	    }
	},1500);

	decimal_factor = 1;
	$('#count-3').addClass('big-count');
	$('#count-3').animateNumber({
	    number: 8 * decimal_factor,
	    // color: '#2A3279',
	    // 'font-size': '300%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number + " жыл");
	    }
	},1500);

	decimal_factor = 1;
	$('#count-4').addClass('big-count');
	$('#count-4').animateNumber({
	    number: 104 * decimal_factor,
	    // color: '#2A3279',
	    // 'font-size': '300%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number);
	    }
	},1500);
}

var getUrlParameter = function getUrlParameter(sParam) {
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

$(document).on('click', '#reset-password-btn', function() {
	$.when($('#login-modal').modal('hide')).done(function() {
		$('#enter-phone-for-reset-password').modal('show');
	});
});

$(document).on('submit', '#send-sms-code-form', function($e) {
	$e.preventDefault();
	$phone = $('input[name=phone-sms]').val();

	$.ajax({
		type: 'GET',
		url: 'controller.php?send-sms-code&phone='+$phone,
		beforeSend: function() {
			$('#student-does-not-exists-message').text('');
			set_load($('#enter-phone-for-reset-password'));
		},
		success: function($data) {
			remove_load();
			console.log($data);
			$json = $.parseJSON($data);

			if ($json.success) {
				if (!$json.student_exists) {
					$('#student-does-not-exists-message').text('Мұндай телефон номерімен оқушы тіркелмеген!');
				} else {
					$.when($('#enter-phone-for-reset-password').modal('hide')).done(function() {
						$('#enter-sms-code-password-modal').find('input[name=phone]').val($phone);
						$('#enter-sms-code-password-modal #sms-code-wrong-message').text("");
						$('#enter-sms-code-password-modal').modal('show');
					});
				}
			}
		}
	});
});

$(document).on('keyup', '#enter-sms-code-password-modal input[name=sms-code]', function() {
	$phone = $('#enter-sms-code-password-modal').find('input[name=phone]').val();
	$sms_code = $(this).val();
	if ($phone != '' && $sms_code.length == 4) {
		$('#enter-sms-code-password-modal #sms-code-wrong-message').text("");
	} else if ($sms_code.length > 4) {
		$('#enter-sms-code-password-modal #sms-code-wrong-message').text("СМС тен келген код 4 саннан тұрады");
	} else {
		$('#enter-sms-code-password-modal #sms-code-wrong-message').text("");
	}
});

$(document).on('keyup', '.sms-code-digit', function() {
	$phone = $('#enter-sms-code-password-modal').find('input[name=phone]').val();
	$id = $(this).data('id');
	$val = $(this).val();
	if ($val != '') {
		if ($id < 4) {
			$next_id = parseInt($id) + 1;
			$('#digit-'+$next_id).focus();
		} else if ($id == '4') {
			$code = '';
			$code += $('#digit-1').val();
			$code += $('#digit-2').val();
			$code += $('#digit-3').val();
			$code += $('#digit-4').val();
			check_sms_code($phone, $code);
		}
	} else {
		if ($id > 1) {
			$prev_id = parseInt($id) - 1;
			$('#digit-'+$prev_id).focus();
		}
	}
});

function check_sms_code($phone, $code) {
	$.ajax({
		type: 'GET',
		url: 'controller.php?check_sms_code&phone='+$phone+'&code='+$code,
		beforeSend: function() {
			set_load($('#enter-sms-code-password-modal'));
		},
		success: function($data) {
			remove_load()
			$json = $.parseJSON($data);
			if ($json.success) {
				if ($json.validation) {
					window.location.replace('reset-password.php?reset_password_by_sms');
				} else {
					$('#sms-code-wrong-message').text('Код қате');
				}
			}
		}
	});
}
