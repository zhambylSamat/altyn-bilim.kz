<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admin - Altyn Bilim</title>
	<?php include_once('style.php');?>
	<style type="text/css">
		input[type='file']{
			display: none;
		}
		.img-upload-style{
			border:2px dashed gray;
			border-radius: 10px;
			display: inline-block;
			padding:5px 0;
			width: 100%;
			cursor: pointer;
			font-size: 18px;
			color:gray;
			transition:0.1s;
			/*overflow:hidden;*/
		}
		.img-upload-style:hover{
			border-color:#8BA0FD;
			color:#8BA0FD;
			transition:0.1s;
		}
		.upload-img-body{
			position: relative;
		}
		.uploaded-img{
			height: 70px;
			width: auto;
			max-height: 70px;
			max-width: 100%;
		}
		.cover{
			line-height: 84px;
			height: 84px;
			position: absolute;
			width: 100%;
			top:0;
			background-color: rgba(0,0,0,0);
			font-size: 0px;
			transition:0.2s;
			color:white;
		}
		.cover:hover{
			cursor: pointer;
			background-color: rgba(0,0,0,0.7);
			font-size: 150%;
			transition: 0.2s;
		}
		.answer-content{
			/*margin:20px 10px;*/
			height: 130px;
			margin:10px 0;
			padding:10px 20px;
			border:1px solid lightgray;
		}
	</style>
</head>
<body>
	<section>
		<div class="container">
			<div class='row'>
				<div class='col-md-3 col-sm-3'>
					<div class='btn-group-vertical' style='width:100%;'>
						<!-- <button type='button' class='btn btn-primary' onclick='show("#primary",".secondary")'>Басты бет</button> -->
						<div class='btn-group' role='group'>
							<button id='btn-dropdown-1' type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
								Кинемтика
								<span class='caret'></span>
							</button>
							<ul class='dropdown-menu' aria-labelledby='btn-dropdown-1' style="width:100%;">
								<li>
									<a>Тақырып 1</a>
								</li>
								<li>
									<a>Тақырып 2</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-md-9 col-sm-9">
					<div id='sub?topic_num' style='display:none;'>
						<br>
						<h4>Физика > Кинематика > Такырып 1</h4>
						<h3><a style='cursor: pointer;'>>&nbsp;Создать тест</a></h3>
					</div>
					<div id='sub?topic_num2' style='display:block;'>
						<br>
						<h4>Физика > Кинематика > Такырып 2</h4>
						<br><br>
						<div id='question_num'>
							<form>
								<div class='row'>
									<div class='form-group col-md-12 col-sm-12'>
										<label for='question'>
											Сұрақ құрастырыңыз
										</label>
										<input type="text" class='form-control disabled' name="question-txt" id='question' placeholder="'Сұрақты' белгілеңіз">
										<br>
										<div class='upload-img-body'>
											<div class='cover hidden' onclick='removeFile("#question-img")' class='delete'><center>Delete</center></div>
											<label id='question-img-label' for='question-img' class='img-upload-style'>
												<center><img class='uploaded-img' src='../img/123.JPG' class='img-responsive'></center>
											</label>
											<input type="file" class='disabled' name="question_img" onchange="uploadImg('#question-img','#question-img-label')" id='question-img'>
										</div>
										<br><br>
									</div>
									<div class='form-group answer' id='id-0'>
										<div class='col-md-6 col-sm-6 answer-content'>
											<div class='col-md-10 col-sm-10'>
												<input type="text" name="answer[0]" class='form-control disabled' placeholder="Жауапты белгілеңіз">	
											</div>
											<div class='col-md-2 col-sm-2'>
												<center><input type="checkbox" class='disabled' checked name="torf[0]" value='1'></center>
											</div>
											<br>
											<div class='col-md-10 col-sm-10 upload-img-body'>
												<div class='cover hidden' onclick='removeFile("#answer-img-0")' class='delete'><center>Delete</center></div>
												<label id='answer-img-label-0' for='answer-img-0' class='img-upload-style'>
													<center><img class='uploaded-img' src='../img/321.JPG' class='img-responsive'></center>
												</label>
												<input type="file" class='disabled' name="answer_img[0]" onchange="uploadImg('#answer-img-0','#answer-img-label-0')" id='answer-img-0'>
											</div>
										</div>
									</div>
									<div class='form-group answer' id='id-1'>
										<div class='col-md-6 col-sm-6 answer-content'>
											<div class='col-md-10 col-sm-10'>
												<input type="text" name="answer[1]" class='form-control disabled' placeholder="Жауапты белгілеңіз">
											</div>
											<div class='col-md-2 col-sm-2'>
												<center><input type="checkbox" class='disabled' name="torf[1]" value='1'></center>
											</div>
											<br>
											<div class='col-md-10 col-sm-10 upload-img-body'>
												<div class='cover hidden' onclick='removeFile("#answer-img-1")' class='delete'><center>Delete</center></div>
												<label id='answer-img-label-1' for='answer-img-1' class='img-upload-style'>
													<center><img class='uploaded-img' src='../img/asdf.JPG' class='img-responsive'></center>
												</label>
												<input type="file" class='disabled' name="answer_img[1]" onchange="uploadImg('#answer-img-1','#answer-img-label-1')" id='answer-img-1'>
											</div>
										</div>
									</div>

									<div class='form-group answer' id='id-100'>
										<div class='col-md-6 col-sm-6 answer-content'>
											<div class='col-md-10 col-sm-10'>
												<input type="text" name="answer[1]" class='form-control disabled' placeholder="Жауапты белгілеңіз">
											</div>
											<div class='col-md-2 col-sm-2'>
												<center>
													<input type="checkbox" class='disabled' name="torf[1]" value='1'>
													<a class='btn btn-xs btn-danger pull-right hidden' title='Осы сұрақты өшіру' onclick='removeAnswer("id-100")'>X</a>
												</center>
												
											</div>
											<br>
											<div class='col-md-10 col-sm-10 upload-img-body'>
												<div class='cover hidden' onclick='removeFile("#answer-img-1")' class='delete'><center>Delete</center></div>
												<label id='answer-img-label-1' for='answer-img-1' class='img-upload-style'>
													<center><img class='uploaded-img' src='../img/asdf.JPG' class='img-responsive'></center>
												</label>
												<input type="file" class='disabled' name="answer_img[1]" onchange="uploadImg('#answer-img-1','#answer-img-label-1')" id='answer-img-1'>
											</div>
										</div>
									</div>

									<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 hidden'>
										<center style='border:1px dashed grey; border-radius: 5px; margin:10px 0px; cursor: pointer;' class='plus_sign'><a style='text-decoration: none; font-size:30px;'>+</a></center>
									</div>
								</div>
								<center><button type='submit' class='btn btn-primary btn-sm hidden'>Сохранить</button>&nbsp;<a class='btn btn-info btn-sm hidden cancel'>Отмена</a>&nbsp;<a class='btn btn-danger btn-sm hidden cancel'>Удалить вопрос</a></center>
								<center><a class='btn btn-warning btn-sm edit'>Изменить</a></center>
							</form>
						</div>
						<div id='question_num_else'>
							<form style='display:none;'>
								<div class='row'>
									<div class='form-group col-md-12 col-sm-12'>
										<label for='question'>
											Сұрақ құрастырыңыз
										</label>
										<input type="text" class='form-control' name="question-txt" id='question' placeholder="'Сұрақты' белгілеңіз">
										<br>
										<div class='upload-img-body'>
											<label id='question-img-label' for='question-img' class='img-upload-style'>
												<center>Выберите изображение</center>
											</label>
											<input type="file" name="question_img" onchange="uploadImg('#question-img','#question-img-label')" id='question-img'>
										</div>
										<br><br>
									</div>
									<div class='form-group answer' id='id-0'>
										<div class='col-md-6 col-sm-6 answer-content'>
											<div class='col-md-10 col-sm-10'>
												<input type="text" name="answer[0]" class='form-control' placeholder="Жауапты белгілеңіз">	
											</div>
											<div class='col-md-2 col-sm-2'>
												<center><input type="checkbox" name="torf[0]" value='1'></center>
											</div>
											<br>
											<div class='col-md-10 col-sm-10 upload-img-body'>
												<label id='answer-img-label-0' for='answer-img-0' class='img-upload-style'>
													<center>Выберите изображение</center>
												</label>
												<input type="file" name="answer_img[0]" onchange="uploadImg('#answer-img-0','#answer-img-label-0')" id='answer-img-0'>
											</div>
										</div>
									</div>
									<div class='form-group answer' id='id-1'>
										<div class='col-md-6 col-sm-6 answer-content'>
											<div class='col-md-10 col-sm-10'>
												<input type="text" name="answer[1]" class='form-control' placeholder="Жауапты белгілеңіз">
											</div>
											<div class='col-md-2 col-sm-2'>
												<center><input type="checkbox" name="torf[1]" value='1'></center>
											</div>
											<br>
											<div class='col-md-10 col-sm-10 upload-img-body'>
												<label id='answer-img-label-1' for='answer-img-1' class='img-upload-style'>
													<center>Выберите изображение</center>
												</label>
												<input type="file" name="answer_img[1]" onchange="uploadImg('#answer-img-1','#answer-img-label-1')" id='answer-img-1'>
											</div>
										</div>
									</div>

									<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3'>
										<!-- <center style='border:1px dashed grey; border-radius: 5px; margin:10px 0px; cursor: pointer;' onclick="addAnswer('.answer')"><a style='text-decoration: none; font-size:30px;'>+</a></center> -->
										<center style='border:1px dashed grey; border-radius: 5px; margin:10px 0px; cursor: pointer;' class='plus_sign'><a style='text-decoration: none; font-size:30px;'>+</a></center>
									</div>
								</div>
								<center><button type='submit' class='btn btn-primary btn-sm'>Отправить</button></center>
							</form>
						</div>
						<div class='row' style='border:1px solid lightgray; padding:5px 5px; margin:5px 0px;'>
							<a class='btn btn-primary btn-sm btn-answer'>1</a>
							<a class='btn btn-info btn-sm btn-answer'>2</a>
							<a class='btn btn-info btn-sm btn-answer'>3</a>
							<a class='btn btn-info btn-sm btn-answer'>3</a>
							<a class='btn btn-info btn-sm btn-answer'>3</a>
							<a class='btn btn-info btn-sm btn-answer'>3</a>
							<a class='btn btn-default btn-sm btn-answer-add' style='font-weight: bold;'>+</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php include_once('js.php');?>
	<script type="text/javascript">
		var id = 2
		function addAnswer(obj){
			$(function(){
				$element = "";
				$element += "<div class='form-group answer' id='id-"+id+"'><div class='col-md-6 col-sm-6 answer-content'><div class='col-md-10 col-sm-10'>";
				$element += "<input type='text' name='answer["+id+"]' class='form-control' placeholder='Жауапты белгілеңіз''>";
				$element += "</div><div class='col-md-2 col-sm-2'>";
				$element += "<input type='checkbox' name='torf["+id+"]' value='1'>";
				$element += "";
				$element += "<a class='btn btn-xs btn-danger pull-right' title='Осы сұрақты өшіру' onclick='removeAnswer(\"id-"+id+"\")'>X</a>";
				$element += "</div>";
				$element += "<br><div class='col-md-10 col-sm-10 upload-img-body'>";
				$element += "<label id='answer-img-label-"+id+"' for='answer-img-"+id+"' class='img-upload-style'>";
				$element += "<center>Выберите изображение</center>";
				$element += "</label>";
				$element += "<input type='file' name='answer_img["+id+"]' onchange='uploadImg(\"#answer-img-"+id+"\",\"#answer-img-label-"+id+"\")' id='answer-img-"+id+"'>";
				$element += "</div>";
				$element += "</div></div>";
				$(obj).siblings().append($element);
				id++;
			});
		}
		function uploadImg(objInput,objLabel){
            $(function(){
                if($(objInput).val()!=''){
                    $img_link = $(objInput).val();
                    $img_index = $img_link.lastIndexOf('\\');
                    $img = $img_link.substring($img_index+1);
                    $(objLabel).html("<center><img class='uploaded-img' src='../img/"+$img+"' class='img-responsive'></center>");
                    console.log(objLabel+' - '+objInput);
                    $(objLabel).parent().prepend("<div class='cover' onclick='removeFile(\""+objInput+"\")' class='delete'><center>Delete</center></div>");
                }
            });
    	}
    	function removeFile(objRemove){
	        var conf = confirm("Are your shure to remove file?");
	        if(conf){
	            $(function(){
	                $(objRemove).attr('value', ''); 
	                $(objRemove).siblings('label').html("<center>Выберите изображение</center>");
	                $(objRemove).siblings(".cover").remove();
	            });
	        }
	    }
		function removeAnswer(n){
			$(function(){				$('#'+n).remove();
			});
		}
		$(function(){
			$(".btn-answer-add").on('click',function(){
				$(this).before("<a class='btn btn-info btn-sm btn-answer'>3</a>");
				$(this).remove();
			});
			$(".edit").on('click',function(){
				$(".hidden").addClass('not-hidden');
				$(".hidden").removeClass('hidden');
				$('.disabled').removeAttr('disabled');
				$(this).addClass('hidden');
			});
			$('.cancel').on('click',function(){
				$(".hidden").removeClass('hidden');
				$(".not-hidden").addClass('hidden');
				$(".not-hidden").removeClass('not-hidden');
				$('.disabled').attr('disabled','');
				$('.answer_tmp').remove();
				// $(this).addClass('hidden');
			});
		});
		$(document).ready(function(){
			$('.disabled').attr('disabled','');
		});
		$(function(){
			$(".plus_sign").on('click',function(){
				$element = "";
				$element += "<div class='form-group answer answer_tmp' id='id-"+id+"'><div class='col-md-6 col-sm-6 answer-content'><div class='col-md-10 col-sm-10'>";
				$element += "<input type='text' name='answer["+id+"]' class='form-control' placeholder='Жауапты белгілеңіз''>";
				$element += "</div><div class='col-md-2 col-sm-2'>";
				$element += "<input type='checkbox' name='torf["+id+"]' value='1'>";
				$element += "";
				$element += "<a class='btn btn-xs btn-danger pull-right' title='Осы сұрақты өшіру' onclick='removeAnswer(\"id-"+id+"\")'>X</a>";
				$element += "</div>";
				$element += "<br><div class='col-md-10 col-sm-10 upload-img-body'>";
				$element += "<label id='answer-img-label-"+id+"' for='answer-img-"+id+"' class='img-upload-style'>";
				$element += "<center>Выберите изображение</center>";
				$element += "</label>";
				$element += "<input type='file' name='answer_img["+id+"]' onchange='uploadImg(\"#answer-img-"+id+"\",\"#answer-img-label-"+id+"\")' id='answer-img-"+id+"'>";
				$element += "</div>";
				$element += "</div></div>";
				$(this).parent().before($element);
				id++;
			});
		});
	</script>
</body>
</html>