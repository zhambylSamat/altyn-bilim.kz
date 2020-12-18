$answer_num = "";

$(document).ready(function(){
	$("#lll").css('display','none');
});

$(document).on('click', '.answer-box', function(){
	// if ($(this).prop('checked')) {
		// $('.answer-box').attr('disabled','true');
		// $('.answer-box').next().addClass('checkbox_checkmark_disabled');
		// $(this).removeAttr('disabled');
		// $(this).next().removeClass('checkbox_checkmark_disabled');
		$answer_num = $(this).data('num');
	// } else {
		// $('.answer-box').removeAttr('disabled');
		// $('.answer-box').next().removeClass('checkbox_checkmark_disabled');
		// $answer_num = "";
	// }

});

$(document).on('click','#submit_question',function(){
	$type = $(this).data('type');
	$ees_id = $(this).data('num');
	if($type == 'skip') { //  && confirm("Сіз сұрақты белгілемедіңіз. Жалғастырасызба?")
		$('.section_content').removeAttr('disabled');
		sendAjax($answer_num, $ees_id);
		$answer_num = "";
	} else if ($type == 'submit') {
		if ($answer_num == "") {
			$('#select-answer-modal').modal('show');
		} else {
			$('.section_content').removeAttr('disabled');
			sendAjax($answer_num, $ees_id);
			$answer_num = "";	
		}
	}
	function sendAjax($answer_num, $ees_id){
		$('#select-answer-modal').modal('hide');
		var formData = {
			'answer_num': $answer_num,
			'ees_id' : $ees_id 
		};
		$.ajax({
			type 		: 'POST',
			url 		: 'ajaxDb.php?test_result', 
			data 		: formData, 
			cache		: false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
				// console.log(dataS);
				data = $.parseJSON(dataS);
				// console.log(data);
				if(data.success){
					if(data.finish){	
						$("#test-content #main-content").html("<center><h3>Loading...</h3></center>");
						$("#test-content #main-content").load("test_result.php");
					}
					else{
						$("#test-content #main-content").html("<center><h3>Loading...</h3></center>");
						$("#test-content #main-content").load("test.php");	
					}
		    	}
		    	else{
		    		console.log(data);
		    	}
			}
		});
	}
});

function test_topic_list(){
	$attrs = [];
	$('.show-child-topics').each(function(){
		$attrs["order-"+$(this).data('child-order')] = $(this).data('is-correct');
	});
	$('.topic').each(function(){
		$order = $(this).data('order');
		if ($attrs[$order]!=undefined) {
			$is_correct = $attrs[$order];
			$(this).show();
			if ($is_correct) $(this).addClass('text-success');
			if (!$is_correct) $(this).addClass('text-danger');
		}
	});
}




	// --------------------------------------------------TEST_RESULT_START----------------------------------------------------------------------
	$(document).on('click','.img-big',function(){
		$attr = $(this).find('p').data('src');
		if ($attr == undefined) {
			$attr = $(this).find('img').attr('src');		
		}
		$('.img-section').find('img').attr('src',$attr);
		$('.img-section').css('display','block');
	});
	$(document).on('click','.remove-img-section',function(){
		$(this).siblings().attr('src','');
		$(this).parents('.img-section').css('display','none');
	});
	$(document).on('click','.img-section',function(){
		$(this).find('img').attr('src','');
		$(this).css('display','none');
	});
	// --------------------------------------------------TEST_RESULT_END------------------------------------------------------------------------
