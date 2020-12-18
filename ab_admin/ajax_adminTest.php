 <?php
	include("../connection.php");
	if(isset($_GET[md5('elementNum')])){
		$elementNum = $_GET[md5('elementNum')];
		try {
			$stmt = $conn->prepare("INSERT IGNORE INTO test (test_num, subtopic_num, type) VALUES(:test_num, :subtopic_num, 'subtopic_test') ");

			$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
		    $stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);

		    $test_num = str_replace('.','',uniqid('TT', true));
		    $subtopic_num = $elementNum;

		    $stmt->execute();
		} catch(PDOException $e) {
	        echo "Error: " . $e->getMessage();
	    }
	}
	else{
		header('location:index.php');
	}
?>
<div class='test_area'>
	<?php
		$result_question = '';
		try {
			$stmt = $conn->prepare("SELECT question_num,question_text,question_img FROM question, test WHERE test.subtopic_num = :subtopic_num AND question.test_num = test.test_num ORDER BY updated_date ASC");
	     	
		    $stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);

		    $stmt->execute();
		    $result_question = $stmt->fetchAll(); 
		} catch(PDOException $e) {
	        echo "Error: " . $e->getMessage();
	    }
	?>
	<?php 
	if($result_question != ''){
		$first_display = 'block';
		$number_of_exists_question = 0;
		foreach($result_question as $readrow_question){
		$number_of_exists_question++;
	?>
	<div id='<?php echo $readrow_question['question_num'];?>' style='display:<?php echo $first_display; $first_display='none'?>'>
		<h3 class='text-danger'></h3>
		<form method='post' class='add-test-form' enctype="multipart/form-data"> <!-- ajaxDbTest.php -->
			<div class='row'>
				<div class='form-group col-md-12 col-sm-12'>
					<input type="hidden" name="hidden_question_num" value='<?php echo $readrow_question['question_num'];?>'>
					<label for='question hidden'>
						Вопрос:
					</label>
					<textarea class='form-control disabledd' rows="6" name="question-txt" id='question' placeholder="'Сұрақты' белгілеңіз"><?php echo $readrow_question['question_text'];?></textarea>
					<br>
					<?php if($readrow_question['question_img']!=null) {?>
					<input type="hidden" name="question-img-hidden" value='<?php echo $readrow_question['question_img'];?>'>
					<?php } else {?>
					<input type="hidden" name="question-img-hidden" value=''>
					<?php }?>
					<div class='upload-img-body'>
						<?php if($readrow_question['question_img']!=null) {?>
						<div class='hidden cover_main' class='delete'><center>Delete</center></div>
						<div class='img-big'><center>Увеличить</center></div>
						<?php }?>
						<label id='question-img-label' for='question-img-<?php echo $readrow_question['question_num'];?>' class='img-upload-style question_img_label'>
							<center>
							<?php
								if($readrow_question['question_img']==null) echo "Выберите изображение";
								else echo "<img class='uploaded-img' src='../img/test/".$readrow_question['question_img']."'>";
							?>
							</center>
						</label>

						<input type="file" class='disabledd img-box' name="question-img" id='question-img-<?php echo $readrow_question['question_num'];?>'>
					</div>
					<br><br>
				</div>
				<?php
					try {
						$stmt = $conn->prepare("SELECT * FROM answer WHERE question_num = :question_num ORDER BY answer_num ASC");
				     	
					    $stmt->bindParam(':question_num', $question_num, PDO::PARAM_STR);
					    $question_num = $readrow_question['question_num'];

					    $stmt->execute();
					    $result_answer = $stmt->fetchAll(); 
					} catch(PDOException $e) {
				        echo "Error: " . $e->getMessage();
				    }
				?>
				<?php $count_answer = 0; foreach($result_answer as $readrow_answer){?>
				<div class='form-group answer'>
					<div class='col-md-6 col-sm-6 answer-content'>
						<div class='col-md-10 col-sm-10'>
							<input type="text" name="answer[<?php echo $count_answer;?>]" class='form-control disabledd' value='<?php echo $readrow_answer['answer_text'];?>' placeholder="Жауапты белгілеңіз">	
						</div>
						<div class='col-md-2 col-sm-2'>
							<center>
								<input type="checkbox" class='disabledd' <?php if($readrow_answer['torf']=='1') echo "checked";?> name="torf[<?php echo $count_answer;?>]" value='1'>
								<?php if($count_answer>=2) {?><a class='btn btn-xs btn-danger pull-right hidden remove_answer' title='Осы сұрақты өшіру'>X</a>
								<?php }?>
							</center>
						</div>
						<br>
						<div class='col-md-10 col-sm-10 upload-img-body'>
							<?php if($readrow_answer['answer_img']!=null) {?>
							<div class='cover hidden' onclick='removeFile("#answer-img-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>")' class='delete'><center>Delete</center></div>
							<div class='img-big'><center>Увеличить</center></div>
							<input type="hidden" name="answer-img-hidden[<?php echo $count_answer;?>]" value="<?php echo $readrow_answer['answer_img'];?>">
							<?php } else {?>
							<input type="hidden" name="answer-img-hidden[<?php echo $count_answer;?>]" value=''>
							<?php }?>
							<label id='answer-img-label-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>' for='answer-img-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>' class='img-upload-style'>
								<center>
									<?php
										if($readrow_answer['answer_img']==null) echo "Выберите изображение";
										else echo "<img class='uploaded-img' src='../img/test/".$readrow_answer['answer_img']."'>";
									?>
								</center>
							</label>
							<input type="file" class='disabledd' name="answer_img[<?php echo $count_answer;?>]" onchange="uploadImg('#answer-img-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>','#answer-img-label-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>')" id='answer-img-<?php echo str_replace(".","",$readrow_answer['answer_num']);?>'>
						</div>
					</div>
				</div>
				<?php $count_answer++;}?>

				<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 hidden'>
					<center style='border:1px dashed grey; border-radius: 5px; margin:10px 0px; cursor: pointer;' class='plus_sign'><a style='text-decoration: none; font-size:30px;'>+</a></center>
				<input type="hidden" name="number_of_answers" value='<?php echo $count_answer;?>'>
				</div>
			</div>
			<center><a class='btn btn-warning btn-sm edit'>Изменить</a></center>
			<center>
				<input type='submit' class='btn btn-primary btn-sm hidden' data_num = '<?php echo $elementNum;?>' value='Сохранить'>&nbsp;
				<a class='btn btn-warning btn-sm hidden cancel'>Отмена</a>&nbsp;
				<a class='btn btn-danger btn-sm hidden delete' data_num = '<?php echo $elementNum;?>' question_num = '<?php echo $readrow_question['question_num'];?>'>Удалить вопрос</a>
			</center>
		</form>
	</div>
	<?php }}?>
	<div id='new_question_section' style='display:<?php echo $first_display;?>;'>
		<h3 class='text-danger'></h3>
		<form method='post' class='add-test-form' enctype="multipart/form-data"> <!-- ajaxDbTest.php -->
			<div class='row'>
				<div class='form-group col-md-12 col-sm-12'>
					<label for='question'>
						Вопрос:
					</label>
					<textarea class='form-control' rows="6" name="question-txt" id='question' placeholder="&#171;Сұрақты&#187; белгілеңіз"></textarea>
					<br>
					<div class='upload-img-body'>
						<label id='question-img-label' for='question-img' class='img-upload-style question_img_label'>
							<center>Выберите изображение</center>
						</label>
						<input type="file" name="question-img" id='question-img' class='img-box'>
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
							<input type="file" name="answer_img[0]" class='answer-img-box' onchange="uploadImg('#answer-img-0','#answer-img-label-0')" id='answer-img-0'>
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
							<input type="file" name="answer_img[1]" class='answer-img-box' onchange="uploadImg('#answer-img-1','#answer-img-label-1')" id='answer-img-1'>
						</div>
					</div>
				</div>

				<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3'>
					
					<center style='border:1px dashed grey; border-radius: 5px; margin:10px 0px; cursor: pointer;' class='plus_sign'><a style='text-decoration: none; font-size:30px;'>+</a></center>
					<input type="hidden" name="number_of_answers" value='2'>
				</div>
			</div>
			<center>
				<input type='submit' name='new_question' data_num = '<?php echo $elementNum;?>' class='btn btn-primary btn-sm' value='Отправить'>
				<a data_num = '<?php echo $elementNum;?>' class='btn btn-warning btn-sm new_quetion_cancel'>Отмена</a>
			</center>
			<!-- <a class='btn btn-primary' id='aaa'>asd</a> -->
		</form>
	</div>
	<div class='row' style='border:1px solid lightgray; padding:5px 5px; margin:5px 0px; display:<?php if($first_display=='block') echo 'none';?>'>
		<?php 
			for($i = 1; $i<=$number_of_exists_question; $i++){
		?>
		<a class='btn <?php if($i==1) echo 'btn-primary'; else echo 'btn-info';?> btn-sm btn-question' data-number='<?php echo $i;?>'><?php echo $i;?></a>
		<?php }?>
		<a class='btn btn-default btn-sm btn-question-add' data='new_question_section' style='font-weight: bold;'>+</a>
	</div>
</div>
<div class='img-section'>
	<center>
		<div class='img-big-box'>
			<img src="" class='img-responsive'>
			<span class='glyphicon glyphicon-remove remove-img-section'></span>
		</div>
	</center>
</div>
<script type="text/javascript">
	// var id = 2;

	function uploadImg(objInput,objLabel){
        $(function(){
        	$img_size = $(objInput)[0].files[0].size;
        	console.log($img_size);
        	if($img_size<=307200){
	            if($(objInput).val()!=''){
	                $img_link = $(objInput).val();
	                $img_index = $img_link.lastIndexOf('\\');
	                $img = $img_link.substring($img_index+1);
	                $(objLabel).html("<center><h2>"+$img+"</h2></center>");
	                $(objLabel).parent().prepend("<div class='cover' onclick='removeFile(\""+objInput+"\")' class='delete'><center>Delete</center></div>");
	            }
	        }
	        else{
	        	alert('Ошибка! Максимальный размер изображении 300КБ ~ (307200 байт). Размер загруженного изображения = '+$img_size+' байт.');
	        	if($(objInput).val()!=''){
	        		$(objInput).val('');
	        	}
	        }
        });
	}

	function removeFile(objRemove){
        var conf = confirm("Are your shure to remove file?");
        if(conf){
            $(function(){
                $(objRemove).val(''); 
                $(objRemove).siblings('label').html("<center>Выберите изображение</center>");
                $(objRemove).siblings(".cover").remove();
                $(objRemove).prev().prev().val('');
            });
        }
    }
	function removeAnswer(n){
		$(function(){				
			$('#'+n).remove();
			console.log(n);
			$val = $('#'+n).parent().children(":last-child").find('input[type=hidden]').val();
			console.log($val);
			$('#'+n).parent().children(":last-child").find('input[type=hidden]').val(parseInt($val)-1);
			console.log($val);
		});
	}
	$(document).ready(function(){
		$('.disabledd').attr('disabled','');
	});
	
</script>