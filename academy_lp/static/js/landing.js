// $location = {
// 	1: "Алматы",
// 	2: "Астана",
// 	3: "Ақтау",
// 	4: "Ақтөбе",
// 	5: "Атырау",
// 	6: "Алматы облысы",
// 	7: "Ақмола облысы",
// 	8: "Ақтөбе облысы",
// 	9: "Атырау облысы",
// 	10: "Батыс Қазақстан облысы",
// 	11: "Жезқазған",
// 	12: "Жамбыл облысы",
// 	13: "Көкшетау",
// 	14: "Қызылорда",
// 	15: "Қостанай",
// 	16: "Қызылорда облысы",
// 	17: "Қостанай облысы",
// 	18: "Қарағанды облысы",
// 	19: "Маңғыстау облысы",
// 	20: "Орал",
// 	21: "Өскемен",
// 	22: "Павлодар",
// 	23: "Петропавл",
// 	24: "Павлодар облысы",
// 	25: "Семей",
// 	26: "Солтүстік Қазақстан облысы",
// 	27: "Түркістан",
// 	28: "Түркістан облысы",
// 	29: "Тараз",
// 	30: "Талдықорған",
// 	31: "Шымкент",
// 	32: "Шығыс Қазақстан облысы"
// };

$xsMaxWidth = 767;

$(document).ready(function() {
	$win_width = $(window).width();

	$img_size = 1;
	if ($win_width > $xsMaxWidth) {
		$img_size = 2;

		$('.ia_action').addClass('ia_action_before');
	}
	$('.autoplay').slick({
		slidesToShow: $img_size,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 3000,
	});

	$width = 560;
	$height = 315;
	if ($win_width - 80 <= $width) {
		$width = $win_width - 80;
		$height = $width / 2;
	}

	$("#av_video").find('iframe').css({
		"width": $width + "px",
		"height": $height + "px"
	});

	$whatsapp_text = "Сәлеметсіз бе. Мен ҰБТ онлайн академясы жайлы хабарласып тұрмын.";
	$whatsapp_phone = 77777044551;
	$whatsapp_desktop_url = 'https://web.whatsapp.com/send?phone=' + $whatsapp_phone + '&text=' + $whatsapp_text;
	$whatsapp_mobile_url = 'https://wa.me/' + $whatsapp_phone + '/?text=' + $whatsapp_text;

	if (is_mobile()) {
		$("#whatsapp").find('a').attr("href", $whatsapp_mobile_url);
	} else {
		$("#whatsapp").find('a').attr("href", $whatsapp_desktop_url);
	}


	// $("#click").click(function() {
	// 	$('html, body').animate({
	// 		scrollTop: $("#div1").offset().top
	// 	}, 2000);
	// });

});


$(window).load(function() {
	$win_width = $(window).width();
	if ($win_width > $xsMaxWidth) {
		$desc_height = $("#description").height();
		$("#dt_content").css({
			"height": ($desc_height - 50) + "px"
		});
	}
});

$(window).resize(function() {
	$win_width = $(window).width();
	if ($win_width > $xsMaxWidth) {
		$('.c-subtitle').show();
	} else {
		$('.c-subtitle').hide();
	}
	$width = 560;
	$height = 315;
	if ($win_width - 80 <= $width) {
		$width = $win_width - 80;
		$height = $width / 2;
	}
	$("#av_video").find('iframe').css({
		"width": $width + "px",
		"height": $height + "px"
	});
	console.log("width: " + $width + "px height: " + $height + "px");

	if ($win_width > $xsMaxWidth) {
		$desc_height = $("#description").height();
		$("#dt_content").css({
			"height": ($desc_height - 50) + "px"
		});
	} else {
		$("#dt_content").css({
			"height": "0px"
		});
	}
});

$(document).on('click', '.courses-list-toggle', function() {
	$width = $(window).width();
	if ($width <= $xsMaxWidth) {
		$(this).parents('.c-title').next().slideToggle();
	}
});

$(document).on('mouseover', '.ia_action', function() {
	$vid = $(this).find('video');
	$vid[0].play();
});

$(document).on('mouseout', '.ia_action', function() {
	$vid = $(this).find('video');
	$vid[0].pause();
});

$(window).load(function() {
	for ($i = 1; $i <= 8; $i++) {
		if ($i == 1 && $(window).width() >= 600) {
			$("#all-gallery-img").append('<div class="row">');
		}
		$("#all-gallery-img").append('<div class="col-md-3 col-sm-3 col-xs-12 gallery-img"><img style="cursor:pointer; box-shadow:0px 0px 5px black; margin:10px" src="static/test/img/our_students/insta-' + $i + '.JPG" class="img-responsive '+(is_mobile_by_width() ? "" : "big-img")+'" '+(is_mobile_by_width() ? "" : 'data-toggle="modal" data-target=".works"')+'></div>');
		if ($i == 4 && $(window).width() >= 600) {
			$("#all-gallery-img").append('</div><div class="row">');
		}
		if ($i == 8 && $(window).width() >= 600) {
			$("#all-gallery-img").append('</div>');
		}
	}
});

$(document).on('click', '.big-img', function() {
	$first_img = $(this).attr('src');
	$big_img_arr = [];
	$point = 0;
	$(this).parents("#all-gallery-img").find(".gallery-img").each(function() {
		$big_img_arr.push($(this).find('img').attr('src'));
		if ($(this).find('img').attr('src') == $first_img) $point = $big_img_arr.length - 1;
	});
	$(".works .modal-body img").attr("src", $big_img_arr[$point]);
	console.log($big_img_arr);
});

$(document).on('click','.mfp-arraw-left',function(){
	$point = (--$point < 0) ? $big_img_arr.length - 1 : $point;
	$(".modal .modal-dialog .modal-body img").attr("src", $big_img_arr[$point]);
});
$(document).on('click', '.mfp-arraw-right', function() {
	$point = (++$point == $big_img_arr.length) ? 0 : $point;
	$(".works .modal-body img").attr("src", $big_img_arr[$point]);
});
$(document).keydown(function(e) {
	if (e.keyCode == 37) {
		$point = (--$point < 0) ? $big_img_arr.length - 1 : $point;
		$(".works .modal-body img").attr("src", $big_img_arr[$point]);
	}
});
$(document).keydown(function(e) {
	if (e.keyCode == 39) {
		$point = (++$point == $big_img_arr.length) ? 0 : $point;
		$(".modal .modal-dialog .modal-body img").attr("src", $big_img_arr[$point]);
	}
});


function is_mobile() {
	if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) ||
		/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
		return true;
	}
	return false;
}
function is_mobile_by_width(){
	$win_width = $(window).width();
	if ($win_width <= $xsMaxWidth) {
		return true;
	}
	return false;
}

$('#exampleModal').on('show.bs.modal', function(event) {
	var button = $(event.relatedTarget)
	var recipient = button.data('whatever')
	
	var modal = $(this)
	modal.find('.modal-title').text('New message to ' + recipient)
	modal.find('.modal-body input').val(recipient)
})

// (function(a){jQuery.browser.mobile=/android.+mobile|avantgo|bada/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)/|plucker|pocket|psp|symbian|treo|up.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw-(n|u)|c55/|capi|ccwa|cdm-|cell|chtm|cldc|cmd-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc-s|devi|dica|dmob|do(c|p)o|ds(12|-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(-|_)|g1 u|g560|gene|gf-5|g-mo|go(.w|od)|gr(ad|un)|haie|hcit|hd-(m|p|t)|hei-|hi(pt|ta)|hp( i|ip)|hs-c|ht(c(-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i-(20|go|ma)|i230|iac( |-|/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |/)|klon|kpt |kwc-|kyo(c|k)|le(no|xi)|lg( g|/(k|l|u)|50|54|e-|e/|-[a-w])|libw|lynx|m1-w|m3ga|m50/|ma(te|ui|xo)|mc(01|21|ca)|m-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|-([1-8]|c))|phil|pire|pl(ay|uc)|pn-2|po(ck|rt|se)|prox|psio|pt-g|qa-a|qc(07|12|21|32|60|-[2-7]|i-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55/|sa(ge|ma|mm|ms|ny|va)|sc(01|h-|oo|p-)|sdk/|se(c(-|0|1)|47|mc|nd|ri)|sgh-|shar|sie(-|m)|sk-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h-|v-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl-|tdg-|tel(i|m)|tim-|t-mo|to(pl|sh)|ts(70|m-|m3|m5)|tx-9|up(.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(-|2|g)|yas-|your|zeto|zte-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);