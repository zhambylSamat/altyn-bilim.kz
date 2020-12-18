<?php include_once('../connection.php');?>
<?php
	if(!isset($_SESSION['adminNum'])){
		header('location:signin.php');
	}
	if(!isset($_GET['data_num'])){
		header('location:index.php');
	}

	try {
		$stmt = $conn->prepare("SELECT s.subject_name subject_name, 
									t.topic_num topic_num, 
									t.topic_name topic_name, 
									st.subtopic_num subtopic_num, 
									st.subtopic_name subtopic_name 
									FROM subject s
									LEFT JOIN topic t 
										ON t.subject_num = s.subject_num
											AND t.quiz = 'n'
									LEFT JOIN subtopic st 
										ON st.topic_num = t.topic_num
									WHERE s.subject_num = :subject_num
									ORDER BY t.topic_order, 
										st.subtopic_order ASC");
		$stmt->bindParam(':subject_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admins-Test-Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet/less" type='text/css' href="css/style.less">
	<style type="text/css">
		button[title]:hover:after, a[title]:hover:after {
		  	content: attr(title);
		  	padding: 4px 8px;
		  	color: #fff;
		  	position: absolute;
		  	left: 100%;
		  	top: 10%;
		  	z-index: 20;
		  	white-space: nowrap;
		    -moz-border-radius: 5px;
		    -webkit-border-radius: 5px;
		  	border-radius: 5px;
		    -moz-box-shadow: 0px 0px 4px #222;
		    -webkit-box-shadow: 0px 0px 4px #222;
		  	background-color: black;
		}
	</style>
</head>
<body onload="startAjax('<?php echo $_GET['data_num'];?>','ajax_adminTestMain.php')">
<?php include_once('nav.php');?>
	<section>
		<div class="container">
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<h3 class='text-primary' id='header-nav'><?php echo $result[0][0]." / ";?></h3>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-3 col-sm-3'>
					<div class='btn-group-vertical' style='width:100%;'>
						<!-- <button type='button' class='btn btn-primary' onclick='show("#primary",".secondary")'>Басты бет</button> -->
						<button type='button' class='btn btn-primary section' data_name='main_section' data_num='<?php echo $_GET['data_num'];?>'><?php echo $result[0][0];?></button>
						<?php 
							$topic_num = '';
							$count = 0;
							foreach($result as $value){
								if($value['subtopic_num']!=null){
						?>
							<?php 
								if($topic_num != $value['topic_num']){ 
									if($count!=0){
										echo "</ul></div>";
									}
							?>
							<div class='btn-group' role='group' target_name='<?php echo $value['topic_name'];?>'>
							<button id='btn-dropdown-1' type='button' class='btn btn-primary dropdown-toggle tooltp' data-toggle='dropdown' title='<?php echo $value['topic_name'];?>' aria-haspopup='true' aria-expanded='true'>
								<?php echo substr($value['topic_name'],0,35).((strlen($value['topic_name'])>35) ? "..." : "");?>
								<span class='caret'></span>
							</button>
							<ul class='dropdown-menu' aria-labelledby='btn-dropdown-1' style="width:100%;">
							<?php } $topic_num = $value['topic_num']; ?>
								<li>
									<a title='<?php echo $value['subtopic_name'];?>' target_name='<?php echo $value['subtopic_name'];?>' data_name='subtopic' data_num='<?php echo $value['subtopic_num'];?>' class='section' style='cursor:pointer;'><span class='glyphicon glyphicon-triangle-right'></span>&nbsp;<?php echo substr($value['subtopic_name'],0,35).((strlen($value['subtopic_name'])>35) ? "..." : "");?></a>
								</li>
						<?php $count++; }} ?>
						</div>
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
<?php 
// include_once('js.php');
?>
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
					$element += "<?php if($_SESSION['role']==md5('admin')) {?>";
					$element += "<div class='col-md-6 col-sm-6 section-block'>";
						$element += "<h4>";
							$element += "<center><a class='section' data_name='video' data_num='"+$data_num+"'>Видео урок</a></center>";
						$element += "</h4>";
					$element += "</div>";
					$element += "<?php } ?>";

					$element += "<div class='col-md-6 col-sm-6 section-block'>";
						$element += "<h4>";
							$element += "<center><a class='section' data_name='test' data_num='"+$data_num+"'>Тестовые вопросы</a></center>";
						$element += "</h4>";
					$element += "</div>";

					$element += "<div class='col-md-12 col-sm-12' id='problem-solution'>";
					$element += "</div>";
				$element += "</div>";
				$element += "";
				$("#main-content").html($element);
				$("#problem-solution").html("<center><h3>Loading...</h3></center>");
				$("#problem-solution").load('problem_solution.php?data_num='+$data_num);
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
			$(document).on('submit','.add-test-form',(function(e) {
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
				    	console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		// $('#main-content').html(data.text);
				    		$('#main-content').load("ajax_adminTest.php?<?php echo md5('elementNum')?>="+$elemNum);
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
					    		$('#main-content').load('ajax_adminTest.php?<?php echo md5('elementNum')?>='+$elemNum);
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
					    		// $('#main-content').html(data.text);
					    		$('#main-content').load('ajax_adminVideo.php');
					    	}
					    	else{
					    		console.log(data.error);
					    	}
						}
					});
				}
			});
		});



		$(document).on('submit','.form-problem-solving-new-file',function(e) {
			$this = $(this);
			e.preventDefault();
			$.ajax({
	        	url: "ajaxDb.php?<?php echo md5(md5('newProblemSolvingFile')); ?>",
				type: "POST",
				data:  new FormData(this),
				contentType: false,
	    	    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					
			    	console.log(dataS);
			    	data = $.parseJSON(dataS);
			    	// console.log(data);
			    	if(data.success){
			    		// $('#main-content').html(data.text);
			    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },1000,function(){
			    			$("#problem-solution").html("<center><h4>Loading...</h4></center>");
			    			$("#problem-solution").load('problem_solution.php?data_num='+$this.find('input[name=sbtn]').val());
			    			$('#lll').css('display','none');
			    		});
			    	}
			    	else{
			    		$this.parents('tr').stop().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },1000,function(){
			    			$("#problem-solution").html("<center><h4>Loading...</h4></center>");
			    			$("#problem-solution").load('problem_solution.php?data_num='+$this.find('input[name=sbtn]').val());
			    		});
			    		$('#main-content').children('script').remove();
				    	$('#main-content').prepend(data.script);
				    	$('#lll').css('display','none');
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		});
		$(document).on('submit','.form-problem-solving-remove-file',function(e){
			$this = $(this);
			e.preventDefault();
			if(confirm("Вы точно хотите удалить файл?")){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('removeProblemSolvingFile'))?>",
					type: "POST",
					data:  new FormData(this),
					contentType: false,
		    	    cache: false,
					processData:false,
					beforeSend:function(){
						$('#lll').css('display','block');
					},
					success: function(dataS){
						
				    	console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		// $('#main-content').html(data.text);
				    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },1000,function(){
				    			$("#problem-solution").html("<center><h4>Loading...</h4></center>");
				    			$("#problem-solution").load('problem_solution.php?data_num='+$this.find('input[name=sbtn]').val());
				    			$('#lll').css('display','none');
				    		});
				    	}
				    	else{
				    		$this.parents('tr').stop().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },1000,function(){
				    			$("#problem-solution").html("<center><h4>Loading...</h4></center>");
				    			$("#problem-solution").load('problem_solution.php?data_num='+$this.find('input[name=sbtn]').val());
				    		});
				    		$('#main-content').children('script').remove();
					    	$('#main-content').prepend(data.script);
					    	$('#lll').css('display','none');
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	     
			   	});
			}
		});
		// -------------------------------------------------------------------START-VIMEO-VIDEO-------------------------------------------------------------------
		$ccc = 0;
		$(document).on('click','#refresh',function(){
			console.log($ccc++);
			$("#vimeo-content").load('ajax_vimeo_video.php');
		});


		$(document).on('submit','#vimeo-video-form',function(e){
			$this = $(this);
			e.preventDefault();
			$.ajax({
		    	url: "ajaxDb.php?<?php echo md5(md5('addVimeoVideoLink'))?>",
				type: "POST",
				data:  new FormData(this),
				contentType: false,
	    	    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					
			    	console.log(dataS);
			    	data = $.parseJSON(dataS);
			    	// console.log(data);
			    	if(data.success){
			    		$("#vimeo-content").load('ajax_vimeo_video.php');
			    		$('#lll').css('display','none');
			    		$this.parents('.row').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'});
			    	}
			    	else{
			    		$this.parents('.row').stop().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'});
				    	$('#lll').css('display','none');
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	     
		   	});
		});
		$(document).on('submit','#remove-link',function(e){
			$this = $(this);
			e.preventDefault();
			if(confirm("Вы точно хотите удалить видео.")){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('removeVimeoVideoLink'))?>",
					type: "POST",
					data:  new FormData(this),
					contentType: false,
		    	    cache: false,
					processData:false,
					beforeSend:function(){
						$('#lll').css('display','block');
					},
					success: function(dataS){
						console.log("okkkkeeeyyy");
						
				    	console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		$("#vimeo-content").load('ajax_vimeo_video.php');
				    		$('#lll').css('display','none');
				    		$this.parents('.row').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'});
				    	}
				    	else{
				    		$this.parents('.row').stop().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'});
					    	$('#lll').css('display','none');
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	     
			   	});
			}
		});
		// -------------------------------------------------------------------END-VIMEO-VIDEO---------------------------------------------------------------------
	</script>
	<script>
    function reload_js(src) {
        $('script[src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.j"]').remove();
        $('<script>').attr('src', src).appendTo('head');
    }
    reload_js('source_file.js');
</script>
	
</body>
</html>