<?php include_once('../connection.php');?>
<?php
	if(!isset($_GET[md5('subjectNum')]) && strlen($_GET[md5('subjectNum')])>=20){
		header('location:index.php');
	}
	else{
		$s_num = $_GET[md5('subjectNum')];
		$content_name = $_SESSION["content_name"];
		$subject_arr  = $_SESSION["subject_arr"];
		$topic_arr    = $_SESSION["topic_arr"];
		$subtopic_arr = $_SESSION["subtopic_arr"];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admins-Test-Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet/less" type='text/css' href="css/style.less">
</head>
<body onload="startAjax('<?php echo $s_num;?>','ajax_adminTestMain.php')">
	<section>
		<div class="container">
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<h3 class='text-primary' id='header-nav'><?php echo $content_name[$s_num]." / ";?></h3>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-3 col-sm-3'>
					<div class='btn-group-vertical' style='width:100%;'>
						<!-- <button type='button' class='btn btn-primary' onclick='show("#primary",".secondary")'>Басты бет</button> -->
						<button type='button' class='btn btn-primary section' data_name='main_section' data_num='<?php echo $s_num;?>'><?php echo $content_name[$s_num];?></button>
						<?php 
							foreach($topic_arr as $topic_key => $topic_value){
								if($topic_value == $s_num && array_search($topic_key,$subtopic_arr)!=''){
						?>
						<div class='btn-group' role='group' target_name='<?php echo $content_name[$topic_key];?>'>
							<button id='btn-dropdown-1' type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
								<?php echo $content_name[$topic_key];?>
								<span class='caret'></span>
							</button>
							<ul class='dropdown-menu' aria-labelledby='btn-dropdown-1' style="width:100%;">
								<?php 
									foreach($subtopic_arr as $subtopic_key => $subtopic_value){
										if($subtopic_value == $topic_key){
								?>
								<li>
									<a target_name='<?php echo $content_name[$subtopic_key];?>' data_name='subtopic' data_num='<?php echo $subtopic_key;?>' class='section' style='cursor:pointer;'><span class='glyphicon glyphicon-triangle-right'></span>&nbsp;<?php echo $content_name[$subtopic_key];?></a>
								</li>
								<?php }} ?>
							</ul>
						</div>
						<?php }} ?>
					</div>
				</div>

				<div class="col-md-9 col-sm-9" id='main-content'>
				</div>
			</div>
		</div>
	</section>
	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>
	<?php include_once('js.php');?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#lll").css('display','none');
		});
		$(function(){
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
				window['startAjax']($elemNum,'ajax_adminTest.php');

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
	</script>

	<script type="text/javascript">
		thisParent = '';
		// -------------------------------------------------AJAX-----------------------------------------------
		function startAjax(data_num,page){
			console.log(data_num);
			$(function(){
				$.ajax({url:page+'?<?php echo md5('elementNum')?>='+data_num,
					beforeSend:function(){
						$('#lll').css('display','block');
					},
					success: function(result){
					$('#lll').css('display','none');
					$("#main-content").html(result);
				}});
			});
		}
		$(document).on('click',".section",function(){
			$data_name = $(this).attr('data_name');
			$data_num = $(this).attr('data_num');
			if($data_name == 'main_section'){
				$.ajax({url:'ajax_adminTestMain.php?<?php echo md5('elementNum')?>='+$data_num,success: function(result){
					$("#main-content").html(result);
				}});
			}
			else if($data_name == 'subtopic'){
				$target_name = "<span id='header-nav-tmp'>"+$(this).parent().parent().parent().attr('target_name')+" / "+$(this).attr('target_name')+" / </span>";
				$("#header-nav-tmp").remove();
				$("#header-nav").append($target_name);
				$element = '';
				$element += "<div class='row'>";
				$element += "<div class='col-md-6 col-sm-6 section-block'>";
				$element += "<h4>";
				$element += "<center><a class='section' data_name='video' data_num='"+$data_num+"'>Видео урок</a></center>";
				$element += "</h4>";
				$element += "</div>";
				$element += "<div class='col-md-6 col-sm-6 section-block'>";
				$element += "<h4>";
				$element += "<center><a class='section' data_name='test' data_num='"+$data_num+"'>Тестовые вопросы</a></center>";
				$element += "</h4>";
				$element += "</div>";
				$element += "</div>";
				$element += "";
				$("#main-content").html($element);
			}
			else if($data_name == 'test'){
				$.ajax({url:'ajax_adminTest.php?<?php echo md5('elementNum')?>='+$data_num,
					beforeSend:function(){
						$('#lll').css('display','block');
					},
					success:function(result){
					$('#lll').css('display','none');
					$('#main-content').html(result);
				}});
			}
			else if($data_name == 'video'){
				$.ajax({url:'ajax_adminVideo.php?<?php echo md5('elementNum')?>='+$data_num,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success:function(result){
					$('#lll').css('display','none');
					$('#main-content').html(result);
				}});
			}
		});
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
			$(thisParent).prev().html("<center>Ең кем дегенде екі сұраққа жауап және оның бір дұрыс жауабы болу керек!</center>");
		}
		function notEmptyCheckbox(){
			$(thisParent).prev().html("");
		}
		function setSection(element_num){
		}
		$(document).ready(function(event){
			$(document).on('submit','form',(function(e) {
				thisParent = $(this);
				$elemNum = $(this).children(":last-child").find('input').attr('data_num');
				e.preventDefault();
				$tmp = $(this).find('input[name=number_of_answers]').val();
				$.ajax({
		        	url: "ajaxDb.php?<?php echo md5('elementNum')?>="+$elemNum+"&<?php echo md5(md5('new_question')); ?>",
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
				    		$('#main-content').html(data.text);
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
						url 		: 'ajaxDb.php?<?php echo md5('elementNum')?>='+$elemNum, 
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
					    		$('#main-content').html(data.text);
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
			$(document).on("click",".remove_video",function(){
				$elemNum = $(this).attr('data_num');	
				$elemName = $(this).attr('data_name');			
				var formdata = new FormData();
				formdata.append("<?php echo md5('rmvVideo');?>",$elemNum);
				formdata.append("<?php echo md5('elemName');?>",$elemName);
				if(confirm("Вы точно хотите удалить Видео: "+$elemName+" ?")){
					$.ajax({
						type 		: 'POST',
						url 		: 'uploadVideo.php', 
						data 		: formdata, 
						contentType: false,
			    	    cache: false,
						processData:false,
						success: function(dataS){
							console.log(dataS);
							data = $.parseJSON(dataS);
							console.log(data);
							if(data.success){
					    		$('#main-content').html(data.text);
					    	}
					    	else{
					    		console.log(data.error);
					    	}
						}
					});
				}
			});
		});
	</script>
	<script>
    function reload_js(src) {
        $('script[src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.j"]').remove();
        $('<script>').attr('src', src).appendTo('head');
    }
    reload_js('source_file.js');
</script>
	
</body>
</html>