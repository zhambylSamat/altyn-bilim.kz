$(document).ready(function(){
	$("#lll").css('display','none');
});


$(document).on('click','.plus_sign',function(){
	$id2 = parseInt($(this).siblings().val());
	console.log($id2);
	$element = "";
	$element += "<div class='form-group answer answer_tmp'><div class='col-md-6 col-sm-6 answer-content'><div class='col-md-10 col-sm-10'>";
	$element += "<input type='text' name='answer["+$id2+"]' class='form-control' placeholder='Жауапты белгілеңіз''>";
	$element += "</div><div class='col-md-2 col-sm-2'>";
	$element += "<input type='checkbox' name='torf["+$id2+"]' value='1'>";
	$element += "";
	$element += "<a class='btn btn-xs btn-danger pull-right remove_answer' title='Осы сұрақты өшіру'>X</a>";
	$element += "</div>";
	$element += "<br><div class='col-md-10 col-sm-10 upload-img-body'>";
	$element += "<label id='answer-img-label-"+$id2+"' for='answer-img-"+$id2+"' class='img-upload-style'>";
	$element += "<center>Выберите изображение</center>";
	$element += "</label>";
	$element += "<input type='file' name='answer_img["+$id2+"]' onchange='uploadImg(\"#answer-img-"+$id2+"\",\"#answer-img-label-"+$id2+"\")' id='answer-img-"+$id2+"'>";
	$element += "</div>";
	$element += "</div></div>";
	$(this).siblings("input[name=number_of_answers]").val($id2+1);
	$(this).parent().before($element);
	
});
$(document).on('click','.edit',function(){
	$('.img-big').remove();
	$(".hidden").addClass('not-hidden');
	$(".hidden").removeClass('hidden');
	$('.disabledd').removeAttr('disabled');
	$(this).addClass('hidden');

	$(this).parent().parent().parent().parent().children(":last-child").children('.btn-question').removeClass('btn-question').addClass("do_not_move");
	$(this).parent().parent().parent().parent().children(":last-child").children('.btn-question-add').removeClass('btn-question-add').addClass("do_not_move2");
	$(this).parent().parent().parent().parent().children(":last-child").children('a').attr('disabled','disabled');
});
$(document).on('click','.cancel',function(){
	$elemNum = $(this).prev().attr('data_num');
	window['startAjax']($elemNum,'ajax_test.php');
});
$new_question = '';
$(document).on('click','.btn-question',function(){
	$(this).parent().parent().children().css('display','none');
	$(this).parent().css('display','block');
	$(this).parent().parent().children(":nth-child("+$(this).attr('data-number')+")").css('display','block');
	$(this).siblings('.btn-primary').removeClass('btn-primary').addClass('btn-info');
	$(this).removeClass('btn-info').addClass('btn-primary');
});
$(document).on('click','.btn-question-add',function(){
	$new_question = $(this);
	$(this).parent().parent().children().css('display','none');
	$(this).parent().css('display','block');
	$(this).siblings().attr('disabled','disabled');
	$(this).siblings().removeClass('btn-question').addClass('do_not_move');
	$id = $(this).attr('data');

	$(this).html(parseInt($(this).prev().attr('data-number'))+1);
	$(this).siblings('.btn-primary').removeClass('btn-primary').addClass('btn-info');
	$(this).removeClass('btn-default').addClass('btn-success');
	$("#"+$id).css('display','block');
});
$(document).on('click',".new_quetion_cancel",function(){
	$($new_question).siblings().removeAttr('disabled');
	$($new_question).parent().parent().children().css('display','none');
	$($new_question).parent().css('display','block');
	$($new_question).html("+");
	$($new_question).removeClass('btn-success').addClass('btn-default');
	// $("#"+$(this).attr(data)).css('display');
	$($new_question).parent().prev().prev().css('display','block');
	$($new_question).prev().removeClass('btn-info').addClass('btn-primary');
	$($new_question).siblings().removeClass('do_not_move').addClass('btn-question');
});
$(document).on('click','.remove_answer',function(){
	$val = parseInt($(this).parents("div.answer").parent().children(':last-child').find('input[type=hidden]').val())-1;
	console.log($val);
	// $(this).parents("div.answer").parent().children(':last-child').find('input[type=hidden]').val($val)
	$(this).parents("div.answer").remove();
});
$(document).on("click",'.img-big',function(){
	$img_link = $(this).parent().find('img').attr('src');
	console.log($img_link);
	$('.img-section').css('display','block');
	$('.img-section').find('img').attr('src',$img_link);
});
$(document).on('click','.remove-img-section',function(){
	$(this).siblings().attr('src','');
	$(this).parents('.img-section').css('display','none');
});
$(document).on('click','.img-section',function(){
	$(this).find('img').attr('src','');
	$(this).css('display','none');
});


$(document).on('change','.img-box',function(){
	$img_size = $(this)[0].files[0].size;
	console.log($img_size);
	if($img_size<=307200){
		if($(this).val()!=''){
			$img_link = $(this).val();
			console.log($img_link);
            $img_index = $img_link.lastIndexOf('\\');
            $img = $img_link.substring($img_index+1);
            $label = $(this).prev();
            $label.parent().prepend("<div class='cover_main' class='delete'><center>Delete</center></div>");
            $label.html("<center><h2>"+$img+"</h2></center>");
            console.log("worked");
		}
	}
	else{
		alert('Ошибка! Максимальный размер изображении 300КБ ~ (307200 байт). Размер загруженного изображения = '+$img_size+' байт.');
		if($(this).val()!=''){
			$(this).val('');
		}
	}
});
$(document).on('click','.cover_main',function(){
	if(confirm("Are your shure to remove file?")){
		$attr = $(this).parent().children(':last-child').val();
		$(this).parent().children(':last-child').val(''); 
		$(this).parent().prev().val('');
        $(this).next().html("<center>Выберите изображение</center>");
        $(this).remove();
	}
});


thisParent = '';
// -------------------------------------------------AJAX-----------------------------------------------
function startAjax(data_num,page){
	console.log(data_num);
	$(function(){
		$.ajax({url:page+'?elementNum='+data_num,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(result){
			$('#lll').css('display','none');
			$("#main-content").html(result);
		}});
	});
}

function emptyQuestion(){
	console.log(thisParent);
	// $(thisParent).find('input[name="question-txt"]').addClass('error');
	$(thisParent).find('textarea').addClass('error');
	$(thisParent).find('#question-img-label').addClass('error');
}
function notEmptyQuestion(){
	// $(thisParent).find('input[name="question-txt"]').removeClass('error');
	$(thisParent).find('textarea').removeClass('error');
	$(thisParent).find('#question-img-label').removeClass('error');
}
function emptyAnswer(child){
	$(thisParent).children(":first-child").children(':nth-child('+child+')').find("input[type=text]").addClass('error');
	$(thisParent).children(":first-child").children(':nth-child('+child+')').find("label").addClass('error');
}
function notEmptyAnswer(child){
	$(thisParent).children(":first-child").children(':nth-child('+child+')').find("input:first-child").removeClass('error');
	$(thisParent).children(":first-child").children(':nth-child('+child+')').find("label").removeClass('error');
}
function emptyCheckbox(){
	alert("Ең кем дегенде екі сұраққа жауап және оның бір дұрыс жауабы болу керек!");
	$(thisParent).prev().html("<center>Ең кем дегенде сұрақтың екі жауабы және бір дұрыс жауабы болу керек!</center>");
}
function notEmptyCheckbox(){
	$(thisParent).prev().html("");
}
// function setSection(element_num){
// }

$(document).ready(function(event){
	$(document).on('submit','.add-test-form',(function(e) {
		thisParent = $(this);
		$elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		$tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?elementNum="+$elemNum+"&new_question",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('#main-content').html(data.text);
		    		$('#main-content').load("ajax_test.php?elementNum="+$elemNum);
		    	}
		    	else{
		    		// console.log(data.script);
		    		$('#main-content').children('script').remove();
		    		$('#main-content').prepend(data.script);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
	$(document).on("click",".delete",function(){
		$elemNum = $(this).attr('data_num');	
		$questionNum = $(this).attr('question_num');			
		var formData = {
			'delete_question':"delete_question",
			'question_num' : $questionNum
		};
		if(confirm("Вы точно хотите удалить этот вопрос?")){
			$.ajax({
				type 		: 'POST',
				url 		: 'ajaxDb.php', 
				data 		: formData, 
				cache		: false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
					console.log(dataS);
					data = $.parseJSON(dataS);
					console.log(data);
					if(data.success){
			    		// $('#main-content').html(data.text);
			    		$('#main-content').load('ajax_test.php?elementNum='+$elemNum);
			    	}
			    	else{
			    		console.log(data.script);
			    		$('#main-content').children('script').remove();
			    		$('#main-content').prepend(data.script);
			    	}
				}
			});
		}
	});
});





$(document).on('click',"#edit-test-name",function(){
	$form = $(this).parents('form');
	$($form).find('input[type=submit]').show();
	$($form).find('input[type=reset]').show();
	$($form).find('input[type=text]').removeAttr('disabled');
	$(this).hide();
});
$(document).on('click','#test-title input[type=reset]',function(){
	$form = $(this).parents('form');
	$($form).find('input[type=submit]').hide();
	$($form).find('input[type=reset]').hide();
	$($form).find('input[type=text]').attr('disabled','disabled');
	$($form).find('#edit-test-name').show();
});

$(document).on('submit','#test-title',function(e){
	e.preventDefault();
	$this = $(this);
	$.ajax({
    	url: "ajaxDb.php?set_test_name",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$this.find('input[type=submit]').hide();
				$this.find('input[type=reset]').hide();
				$this.find('input[type=text]').attr('disabled','disabled');
				$this.find('#edit-test-name').show();

				$this.stop();
	    		$this.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
	    	}
	    	else{
	    		$this.stop();
	    		$this.css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});