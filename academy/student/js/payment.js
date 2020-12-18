$(document).load('.payment-form', function() {
	// check_payment_checkbox();
	render_promo_code_discount();
});

$(document).on('change', '.select-group-to-pay', function() {
	// check_payment_checkbox();
	$group_student_id = $(this).val();
	if (!$(this).prop('checked')) {
		reset_and_disable_selection($group_student_id);
	} else {
		unset_disable_selection($group_student_id);
	}
	render_promo_code_discount();
});

$(document).on('change', '.use-promo-code-selection', function() {
	$select_element = $(this);
	$('.amounts').each(function() {
		$val = $select_element.val().split('|');
		$group_student_id = $val[0];
		$student_used_promo_code_id = $val[1];
		$row_element = $('#row-'+$group_student_id);
		if ($row_element[0] != undefined && !$row_element.find('input[name="group_student_id[]"]').prop('checked')) {
			$select_element.val('');
		} else {
			$init_amount = $(this).find('input[name=init-amount]').val();
			$(this).find('input[name=amount]').val($init_amount);
			$(this).find('.supc_id').remove();
		}
	});
	render_promo_code_discount();
});

function set_amounts() {
	$('.amounts').each(function() {
		$group_info_id = $(this).parents('tr').find('input[name="group_student_id[]"]').val();
		$amount_element = $(this);
		$init_amount = $amount_element.find('input[name=init-amount]').val();
		$fixed_discount_element = $amount_element.find('.fixed-discount');
		$percent = 0;
		$html = "";
		if ($fixed_discount_element[0] != undefined) {
			$percent += parseInt($fixed_discount_element.val());
			$html += "<input type='hidden' class='fixed-discount' value='"+$percent+"'>";
		}
		$promo_code_discount = $('#promo-code-discount').val();
		$amount_element.find('.supc_id').each(function() {
			$student_used_promo_code_id = $(this).val();
			$html += "<input type='hidden' class='supc_id' name='student_used_promo_code_id["+$group_student_id+"][]' value='"+$student_used_promo_code_id+"'>";
			$percent += parseInt($promo_code_discount);
		});
		$html += "<input type='hidden' name='init-amount' value='"+$init_amount+"'>";

		$html += "<span class='payment-amount-info'>";
		if ($percent > 0) {
			$amount = $init_amount*(100-$percent)/100;
			if ($amount < 0) {
				$amount = 0;
			}
			$html += "<span style='text-decoration: line-through;'>"+$init_amount+"</span><span> теңгенің орнына:</span>";
			$html += "<p><b>"+$percent+"% жеңілдікпен: "+$amount+"</b></p>";
			$html += "<input type='hidden' name='amount' value='"+$amount+"'>";
		} else {
			$html += "<span>"+$init_amount+" тг.</span>";
			$html += "<input type='hidden' name='amount' value='"+$init_amount+"'>";
		}
		$html += "</span>";
		$amount_element.html($html);
	});
}

function reset_and_disable_selection($group_student_id) {
	$('.use-promo-code-selection').each(function() {
		$val = $(this).val();
		$splitted_val = $(this).val().split('|');
		if ($splitted_val[0] == $group_student_id) {
			$(this).val('');

			$row_element = $('#row-'+$group_student_id);
			$amount_element = $row_element.find('.amounts');
			$amount_element.find('.supc_id').remove();
		}

		$(this).find('option').each(function() {
			$val = $(this).val();
			$splitted_val = $val.split('|');
			if ($splitted_val[0] == $group_student_id) {
				$(this).attr('disabled', 'disabled');
			}
		});
	});
	set_amounts();
}

function unset_disable_selection ($group_student_id) {
	console.log($group_student_id);
	$('.use-promo-code-selection').each(function() {
		$(this).find('option').each(function() {
			$val = $(this).val();
			$splitted_val = $val.split('|');
			if ($splitted_val[0] == $group_student_id) {
				$(this).removeAttr('disabled');
			}
		});
	});
}

function render_promo_code_discount () {
	$ids = {};

	$('.use-promo-code-selection').each(function() {
		if ($(this).val() != '') {
			$val = $(this).val().split('|');
			$group_student_id = $val[0];
			$student_used_promo_code_id = $val[1];
			if ($ids[$group_student_id] === undefined) {
				$ids[$group_student_id] = [];
			}
			$ids[$group_student_id].push($student_used_promo_code_id);
		}
	});
	$.each($ids, function($group_student_id, $student_used_promo_code_ids) {
		$percent = 0.0;
		$promo_code_discount = $('#promo-code-discount').val();
		$.each($student_used_promo_code_ids, function($index, $id) {
			$amount_element = $('#row-'+$group_student_id+' .amounts');
			$html = "<input type='hidden' class='supc_id' name='student_used_promo_code_id["+$group_student_id+"][]' value='"+$id+"'>";
			$amount_element.append($html);
		});
	});
	set_amounts();
	check_payment_checkbox();
}

function check_payment_checkbox() {
	$total_amount = 0;
	$.each($('.select-group-to-pay'), function() {
		if ($(this).prop('checked')) {
			$parents = $(this).parents('tr');
			$element = $parents.find('input[name=amount]');
			$total_amount += parseInt($element.val());
		}
	});

	$('#payment-total-amount').html($total_amount);

	if ($total_amount > 0) {
		$('.payment-form').find('button[type=submit]').removeAttr('disabled');
	} else {
		$('.payment-form').find('button[type=submit]').attr('disabled', 'disabled');
	}
}

$(document).on('submit', '.payment-form', function($e) {

	// if (confirm('Төлемді жалғастырасыңба?')) {
		// return true;	
	// }

	return true;
});

function get_not_started_subjects () {
	return $.ajax({
				url: 'payment/controller.php?get_not_started_subjects',
				type: 'GET',
				cache: false
			});
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


// $(document).on('click', '.open-payment-instruction-modal', function() {
// 	// $link = 'https://vimeo.com/481386833';
// 	$link = 'https://vimeo.com/486813296';
// 	$html = "<div class='payment-instruction-vimeo-video'></div>";
// 	$.when($('#payment-instruction-modal').modal('show')).done(function() {
// 		$('#payment-instruction-modal .modal-body').html($html);

// 		$.ajax({
// 	    	url: "https://vimeo.com/api/oembed.json?url="+$link+'&autoplay=1',
// 			type: "GET",
// 			beforeSend:function(){
// 				$('#vimeo-content').html("<center>Загрузка...</center>");
// 			},
// 			success: function(data){
// 				$('#lll').css('display','none');
// 				$('.payment-instruction-vimeo-video').html('<div style="width: 100%;"><center>'+data.html+'</center></div>');
// 				$('.payment-instruction-vimeo-video').find('iframe').attr('width', '100%');
// 				if ($('body').width() > 768) {
// 					$('.payment-instruction-vimeo-video').find('iframe').attr('height', '400');
// 				}
// 		    },
// 		  	error: function(dataS) 
// 	    	{
// 	    		console.log(dataS);
// 	    	} 	     
// 	   	});
// 	});
// });

function render_payment_video_instruction() {
	// $link = 'https://vimeo.com/481386833';
	$link = 'https://vimeo.com/486813296';
	$width = $('#payment-video-vimeo-instruction').width();
	if ($('body').width() > 768) {
		$width = $width*0.6;
	}
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+$link+'&width='+$width,
		type: "GET",
		beforeSend:function(){
			$('#vimeo-content').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#payment-video-vimeo-instruction').html('<center>'+data.html+'</center>');
			
			if ($('body').width() > 768) {
				// $('.payment-instruction-vimeo-video').find('iframe').attr('height', '400');
				$('#payment-video-vimeo-instruction').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 6%;');
			} else {
				$('#payment-video-vimeo-instruction').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 14%;');
			}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
}