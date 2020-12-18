// ---------------------------------------review_start------------------------------------
$(document).on('click','.add-row-review',function(){
	$('.modal-body .form-class').append('<div class="form-group" style="display:block;"><label><b></b></label><input type="text" class="form-control" required="" name="new_review[]" placeholder="М: Сабақ үлгерімі.">&nbsp;&nbsp;<a class="btn btn-sm btn-danger" data-action="remove" name="">Удалить</a><a style="display:none;" class="btn btn-sm btn-primary" data-action="restore" name="">Восстановить</a>&nbsp;&nbsp;<a style="display:none;" class="btn btn-sm btn-warning" data-action="reset" name="">Отмена</a></div>');
	console.log($('.modal-body .form-class').find('.form-group').length);
});
$(document).on('click','a[data-action=remove]',function(){
	// console.log($(this).parent().find('input').attr('name'));
	if($(this).parent().find('input').attr('name')=='new_review[]'){
		$(this).parent().remove();
	}
	else if($(this).parent().find('input').attr('name')=='review'){
		lightAlert($(this).parent(), '#d9534f', 0.3, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin_remove[]');
		$(this).parent().find('a[data-action=restore]').show();
		$(this).parent().find('input[name=review]').prop( "disabled", true );
		$(this).parent().find('input[name=review]').attr('name','remove_review[]');
	}
});
$(document).on('click','a[data-action=restore]',function(){
	if($(this).parent().find('input').attr('name')=='remove_review[]'){
		lightAlert($(this).parent(), '#5cb85c', 0, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		$(this).parent().find('a[data-action=remove]').show();
		$(this).parent().find('input[name="remove_review[]"]').prop( "disabled", false );
		$(this).parent().find('input[name="remove_review[]"]').attr('name','review');
	}
});
$(document).on('click', 'a[data-action=remove-prize-notification]',function(){
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name','spn[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-prize-notification]',function(){
	console.log($(this).prev().prev().attr('name'));
	if($(this).prev().prev().attr('name')=='spn[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('click', 'a[data-action=remove-attendance-notification]',function(){
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name','ann[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-attendance-notification]',function(){
	console.log($(this).prev().prev().attr('name'));
	if($(this).prev().prev().attr('name')=='ann[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('keyup','.modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action=reset]').show();
		if($(this).parent().find('input').attr('name')=='review'){
			$(this).parent().find('input[name=review]').attr('name','update_review[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_review[]'){
			$(this).parent().find('input[name="update_review[]"]').attr('name','review');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});
$(document).on('click','.modal-body .form-class a[data-action=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_review[]'){
		$(this).parent().find('input[name="update_review[]"]').attr('name','review');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});
// ---------------------------------------review_end--------------------------------------
$(document).on('keyup','.topic-input input',function(){
	if($(this).val().toLowerCase()=='бақылау'){
		$(this).val('');
		$(this).removeAttr('name','');
		$(this).parent().slideUp('fast');
		$(this).parent().next().find('textarea').val("Аралық бақылау: ");
		$(this).parent().next().find('textarea').attr('name','new-quiz-name');
		$(this).parent().next().slideDown('fast');
	}
});
$(document).on('keyup','.quiz-input textarea',function(){
	if($(this).val().toLowerCase().substr(0,15)!="аралық бақылау:"){
		$(this).val('');
		$(this).removeAttr('name');
		$(this).parent().slideUp('fast');
		$(this).parent().prev().find('input').val('');
		$(this).parent().prev().find('input').attr('name','new-topic-name');
		$(this).parent().prev().slideDown('fast');
	}
});
// -------------------------------start-parent-----------------------------
$selected_student = [];
$(document).on('change',".student-list",function(){
$val = $(this).val();
$this = $(this);
// if($.inArray($val, $selected_student)){
// 	$selected_student.push($val);
// }
$exists = false;
if($(this).parents('form').find('.single-student').length==0){
	$exists = false;
}
else{
	$(this).parents('form').find('.single-student').each(function(index){
		console.log($(this).find('input[type=hidden]').val()!=$val);	
		if($(this).find('input[type=hidden]').val()==$val){
			$exists = true;
			return false;
		}
	});
}
if(!$exists){
	$text = $this.find('option[value="'+$val+'"]').text();
	$this.parents('form').find('.std').append("<div class='single-student' style='border:1px solid lightgray; border-radius: 5px; padding:2% 4%;'><span style='overflow:hidden;'>"+$text+"</span><a class='btn btn-xs pull-right remove-student-from-list'><span class='glyphicon glyphicon-remove text-danger'></span></a><input type='hidden' name='students[]' value='"+$val+"'></div>");
}
});

$(document).on('click','.remove-student-from-list',function(){
$val = $(this).next().val();
$(this).parent().remove();
// $selected_student = jQuery.grep($selected_student, function(value) {
//   return value != $val;
// });
});
// -------------------------------end-parent-------------------------------

// $(function () { $('[data-toggle="tooltip"]').tooltip();	});
$globalSubjectName = '<?php echo $subject_name;?>';
$(document).on('mouseover','.edit-input, .input_comment',function(){
	$('[data-toggle="tooltip"]').tooltip();
});
function lightAlert($element, $color, $opacity, $time){
	$element.css({'background-color':$color});
	$res = $element.css( "background-color" )
	$bgColor = $res.substring(4,$res.length-1);
	$element.stop();
	$element.animate({backgroundColor: 'rgba('+$bgColor+', '+$opacity+')' },$time);
}
function beforeSubmit(){
	if(confirm("Вы точно хотите удалить предмет \""+$globalSubjectName+"\". Все данные включая все темы и подтемы будут удалены!")){
		if(confirm("Подтвердите действие!")){
			return true;
		}
		else return false;
	}
	return false;
}
// -------------------------------------start-edit-modal--------------------------------
$(document).on('change','.edit-modal',function(){
	$data_name = $(this).attr('data-name');
	console.log($data_name);
	if($data_name=='subject'){
		$val = $(this).find('option:selected').val();
		$text = $(this).find('option:selected').text();
		$(this).parents('.modal-header').find('.delete-btn').val('Удалить "'+$text+'"');
		$(this).parents('.modal-header').find('.delete-btn').prev().val($val);
		$globalSubjectName = $text;
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=hidden]').val($val);
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=text]').val($text);
		// $(this).parents('.modal-header').find('.delete_subject').attr('onsubmit','return confirm("Вы точно хотите удалить предмет \"'+$text+'\"")');
		$('.topic-list').text("Loading...");
		$('.topic-list').load("edit_modal.php?part=header-part&data_num="+$val);
		$('.box-data .modal-body').text('Loading...');
		$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
	}
	else if($data_name='topic_list'){
		$val = $(this).find('option:selected').val();
		$data = $(this).find('option:selected').attr('data');
		if($data=='all'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
		}
		else if($data=='single'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=subtopic&data_num="+$val);
		}
	}
});
$(document).on('keyup','.edit-input',function(){
	$(this).css({'box-shadow':'0px 0px 10px #f0ad4e',"border-color":'#f0ad4e'});
	$(this).attr('data-original-title',$(this).val());
	$(this).parents('.form-group').find('.cancel-edit').show();
});
$(document).on('click','.cancel-edit',function(){
	$val = $(this).attr('data');
	$child = $(this).parents('.form-group').find('.edit-input');
	$child.val($val);
	$child.attr('data-original-title',$val);
	$child.css({'box-shadow':'none',"border-color":'#ccc'});
	$(this).hide();
});
$(document).on('click','.remove-data',function(){
	lightAlert($(this).parents('.form-group'),"#d9534f", 0.3, 800);
	$(this).parents('.form-group').find('.edit-input').attr('name','null');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','deleted[]');
	$(this).parent().prev().hide();
	$(this).next().show();
	$(this).hide();
});
$(document).on('click','.restore',function(){
	lightAlert($(this).parents('.form-group'),"#5cb85c",0, 1000);
	$(this).parents('.form-group').find('.edit-input').attr('name','data_name[]');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','data_num[]');
	$(this).parent().prev().show();
	$(this).prev().show();
	$(this).hide();
});
$(document).on('click','.move',function(){
	$direction = $(this).attr('direction');
	$classUp = 'glyphicon-chevron-up';
	$classDown = 'glyphicon-chevron-down';
	$classStop = 'glyphicon-record';
	$parent = $(this).parents('.form-group');
	if($direction!='none'){
		$parent.slideUp(200,function(){
			if($direction=='up'){
				$down = $parent.find('.move-down').attr('direction');
				$up = $parent.prev().find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','down');
					$parent.find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
					$parent.prev().find('.move-down').attr('direction','none');
					$parent.prev().find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','none');
					$parent.find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
					$parent.prev().find('.move-up').attr('direction','up');
					$parent.prev().find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
				}
				$parent.prev().before($parent);
			}
			if($direction=='down'){
				$down = $parent.next().find('.move-down').attr('direction');
				$up = $parent.find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','none');
					$parent.find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
					$parent.next().find('.move-down').attr('direction','down');
					$parent.next().find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','up');
					$parent.find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
					$parent.next().find('.move-up').attr('direction','none');
					$parent.next().find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
				}
				$parent.next().after($parent);
			}
			$parent.slideDown(200,function(){
				lightAlert($parent,'#f0ad4e',0,1000);
			});
			
		});
	}
});
// -------------------------------------end-edit-modal----------------------------------
// ----------------------------------
$(document).ready(function(){
	$("#lll").css('display','none');
});
$(function(){
	$('#lll').hide().ajaxStart( function() {
		$(this).css('display','block');  // show Loading Div
	} ).ajaxStop ( function(){
		$(this).css('display','none'); // hide loading div
	});
});
// ----------------------
$(document).on('click','.edit_user, .cancel_edit',function(){
	$(this).parents('.head').find('.user_info').toggle();
});
$(document).on('click','.more_info',function(){
	$data_toggle = $(this).attr('data_toggle');
	$data_num = $(this).attr('data_num');
	$data_name = $(this).attr('data-name');
	// console.log('out');
	// console.log($data_name);
	// console.log($data_toggle);
	// console.log($data_num);
	if($data_name == 'student'){
		console.log('middle');
		if($data_toggle=='false'){
			console.log("in");
			$(this).parents('.head').next().load("students_in_group.php?data_num="+$data_num);
			// $(this).parents('.head').next().load("student-info.php?<?php echo md5('student_num')?>="+$data_num);
			$(this).attr('data_toggle','true');
		}
	}
	$(this).parents('.head').next().toggle();
});
$(document).on('click','.close_body',function(){
	$(this).parents('.body').hide();
});
$(document).on('click','.info-list, .new-student, .new-teacher, .new-group, .new-parent',function(){
	$at = $(this).attr('at');
	$("#"+$at).slideToggle('fast');
});
$(document).on('click','.close-add-new-student',function(){
	$(this).parents('#new-student').hide();
});
$(document).on('click','.close-add-new-teacher',function(){
	$(this).parents('#new-teacher').hide();
});
$(document).on('click','.close-add-new-group',function(){
	$(this).parents('#new-group').hide();
});
$(document).on('click','.news',function(){
	$data_type = $(this).attr('data-type');
	if($data_type=='student'){
		$('.box-news .modal-header .modal-title').text('Студенттерге арналған жаңалықтар');
	}
	else if($data_type=='teacher'){
		$('.box-news .modal-header .modal-title').text('Мұғалімдерге арналған жаңалықтар');
	}
	$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>");
	$('.box-news .modal-body').load('index_news.php?data_type='+$data_type);
});
$(document).on('click','.row-groups-info .btn',function(){
	if($(this).attr('btn-info')=='edit'){
		$(this).parents('.row-groups-info').find('.group-info').toggle();
		$(this).parents('.row-groups-info').find(".group-form").toggle();
	}
	if($(this).attr('btn-info')=='schedule'){
		$data_name = $(this).parents('.row-groups-info').find('form').find('input[name=group_name]').val();
		$data_num = $(this).parents('.row-groups-info').find('form').find('input[name=data_num]').val();
		$(".box-group-schedule .modal-header #group_name").text($data_name);
		$(".box-group-schedule .modal-body").text("Loading");
		$(".box-group-schedule .modal-body").load("load_group_schedule.php?data_num="+$data_num);
	}
});
$(document).on('click','.row-parents-info .btn',function(){
	if($(this).attr('type')!='submit'){
		$(this).parents('.row-parents-info').find('.parent-info').toggle();
		$(this).parents('.row-parents-info').find(".parent-form").toggle();
	}
});

// ----------------------------------------------------
$('.navigation').on('click',function(){
	if(!$(this).hasClass('active')){
		$('.navigation').removeClass('active');
		$(this).addClass('active');
		$attr = $(this).attr('data');
		$('.box').css('display',"none");
		$('.'+$attr).css('display','block');
		if($attr=='schedule'){
			$('.'+$attr).html("<center><h1>Loading...</h1></center>");
			$('.'+$attr).load('admin_schedule.php');
		}
	}
});
function hide(objHide){
	$(function(){
		// console.log(objHide+" --function hide(obj)");
		$(objHide).css('display','none');
	});
}
$(document).on('click','.reset_password',function(){
	$val = $(this).next().val();
	$this = $(this);
	$data_name = $(this).attr('data-name');
	$a = '';
	if($data_name=='student'){
		$a = 'Altynbilim';
		$goTo = "<?php echo md5(md5('resetThisStudent'))?>";
	}
	else if ($data_name == 'teacher'){
		$a = 'AltynbilimT';
		$goTo = "<?php echo md5(md5('resetThisTeacher'))?>";
	}
	// console.log($data_name);

	var formData = {
		'action':"reset",
		'reset' : $val
	};
	if(confirm("Пароль поменяется на 'Altynbilim'. Подтвердите действие?")){
		$.ajax({
			type 		: 'POST',
			url 		: 'reset.php?'+$goTo, 
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
					// $this.parents('.user_info').addClass('pull-right');
					$this.parents('.user_info').find('table tr td').last().html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
		    		// $this.parents('.user_info').html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
		    	}
		    	else{
		    		console.log(data);
		    	}
			}
		});
	}
});
$(document).ready(function(){
	$(document).on('submit','#create_student',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createStudent'))?>",
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
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.students').load('index_students.php');
		    		document.getElementById("create_student").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-teacher',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createTeacher'))?>",
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
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.teachers').load('index_teachers.php');
		    		document.getElementById("create-teacher").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-group',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createGroup'))?>",
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
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.groups').load('index_groups.php');
		    		document.getElementById("create-group").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#schedule-form',(function(e) {
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('schedule'))?>",
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
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $(".box-group-schedule").css('display','none').removeClass('in');
		    		// $(".modal-backdrop").remove();
		    		// $('body').removeAttr("class");
		    		// $('body').removeAttr('style');
					$(".box-group-schedule").modal('hide');
		    		$("#alert").html('<div class="alert alert-success alert-dismissible" role="alert" style="position: fixed; z-index: 10000; top:5%; width: 80%; left:10%; box-shadow: 0px 0px 10px green;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><center><strong>'+$(".box-group-schedule #group_name").text()+'</strong> группасының сабақ кестесі өзгерді</center></div>');

		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-parent',(function(e) {
		$this = $(this);
		$student_quantity = $("#create-parent").find('.single-student').length;
		console.log($student_quantity);
		if($student_quantity==0){
			alert("Ең кем дегенде 1 студентті таңдаңыз!");
			return false;
		}
		else{
			if(confirm('Подтвердите действие!')){
				thisParent = $(this);
				e.preventDefault();
				$.ajax({
		        	url: "ajaxDb.php?<?php echo md5(md5('createParent'))?>",
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
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		// $('.students').html(data.text);
				    		$('.parents').load('index_parents.php');
				    		$this.find('.single-student').remove();
				    		document.getElementById("create-parent").reset();
				    	} 
				    	else{
				    		console.log(data);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			return false;
		}
	}));
});
$(document).on('keyup','#search',function(){
	$data_name = $(this).attr('data-name');
	$val = $(this).val();
	$val = $val.replace(" ","_");
	if($data_name=='student'){
		$('.students').load('index_students.php?search='+$val);
	}
	else if($data_name=='teacher'){
		$('.teachers').load('index_teachers.php?search='+$val);
	}
	else if($data_name=='group'){
		$('.groups').load('index_groups.php?search='+$val);
	}
	else if($data_name=='parent'){
		$('.parents').load('index_parents.php?search='+$val);
	}
});
$(document).on("click",'.data-list',function(){
	$(this).parent().next().slideToggle('fast');
});
// --------------------------------------------modal-group-schedule-start-----------------------------------
$(document).on('click','.schedules .btn-week',function(){
	$week_id = $(this).attr('week-id');
	if($(this).hasClass('active')){
		$(this).removeClass('active');
		$('.schedules .hidden-datas').find('input[value='+$week_id+']').remove();
	}
	else{
		$(this).addClass('active');
		$('.schedules .hidden-datas').append('<input type="hidden" name="week_id[]" value="'+$week_id+'">');
	}
});
// --------------------------------------------modal-group-schedule-end-------------------------------------
function newsValidation(){
	var ext = $('#news_img').val();
	console.log(ext);
	if(ext!=''){
		$img_size = $('#news_img')[0].files[0].size;
		console.log($img_size);
		ext = ext.split('.').pop().toLowerCase();
		console.log(ext);
		if($.inArray(ext, ['gif','png','jpg','jpeg','GIF','PNG','JPG','JPEG']) == -1) {
	    	alert('Не правильный формат картинки. Доступный форматы : ".jpg , .png , .jpeg , .gif, .JPG , .PNG , .JPEG , .GIF"');
	    	return false;
		}
		else if($img_size>=1572864){
			alert('Ошибка! Максимальный размер изображении 1.5MБ ~ (1572864 байт). Размер загруженного изображения = '+$img_size+' байт.');
			return false;
		}
		else if(confirm("Подтвердите действие!")){
			return true;
		}
		else {
			return false;
		}
	}
	else if(confirm("Подтвердите действие!")){
		return true;
	}
	else{
		return false;
	}
}
$(document).on('click','#remove_img',function(){
	if(confirm("Вы точно хотите удалить изображение?")){
		$(this).parents('.form-group').find('input[name=uploaded_photo]').val('');
		$(this).parents("b").html('<p class="text-danger">Изображение удалено!</p>');
	}
});
$(document).on('keyup','#context',function(){
	// console.log('asdfasdf');
     var text = $(this).val();
     var arr = text.split("\n");
     for(var i = 0; i < arr.length; i++) {
         if(arr[i].length > 75) {
            $('#news-content-helper').html("Жолдың (қатардын реті: "+(i+1)+") ұзындығы 76 символдан артық кетті!<br>Жолдың ұзақ болғаны не желательно.");
            event.preventDefault(); // prevent characters from appearing
            break;
         }
         else{
         	$('#news-content-helper').html('');
         }
     }

     // console.log(arr.length + " : " + JSON.stringify(arr));
});