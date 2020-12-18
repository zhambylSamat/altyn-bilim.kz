<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim - Test Result</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet" type="text/less" href="css/style.less">
</head>
<body>
	<?php include_once('../connection.php');
		if(isset($_GET[md5(md5('data_json'))])){
			$data_json = json_decode($_GET[md5(md5('data_json'))],true);
			$test_data = json_decode($_SESSION['test_data'],true);
		}
	?>
	<div class='container'>
		<div class='result-box'>
			<h3>Результат теста</h3>
			<p>Дата: <b><?php echo $_GET['date']?></b></p>
			<p>Тема: <b><?php $test_name = json_decode($_GET['t_name'],true); echo $test_name['sName']." / ".$test_name['tName']." / ".$test_name['stName'];?></b></p>
			<p>Количество вопросов: <b><?php echo count($test_data);?></b></p>
			<p>Количество всего правильных ответов: <b>
				<?php 
					$true_answers = 0;
					$true_answer = 0;
					$wrong_answer = 0;
					foreach ($test_data as $test_key => $test_value) {
						foreach ($test_value['answer'] as $answer_key => $answer_value) {
							if($answer_value['torf']=='1'){
								$true_answers ++;
							}
							if(isset($data_json[$test_key]) && in_array($answer_key,$data_json[$test_key]) && $answer_value['torf']=='1'){
								$true_answer ++;
							}
							else if(isset($data_json[$test_key]) && in_array($answer_key,$data_json[$test_key]) && $answer_value['torf']=='0'){
								$wrong_answer++;
							}
						}
					}
					echo $true_answers;
				?>	
			</b></p>
			<p>Количество неправильно выбранных ответов: <b class='text-danger'><?php echo $wrong_answer;?></b></p>
			<p>Количество правильно выбранных ответов: <b class='text-success'>
				<?php
					if($true_answer-$wrong_answer<=0){
						$percent = 0;
					}
					else{ 
						$percent = round(((($true_answer-$wrong_answer)/$true_answers)*100),2); 
					}
					echo $true_answer." / ".$percent."%"; 
				?>
			</b></p>
			<p class="help-block">&lt;&lt;
			<?php
				if($percent>=80){
			?>
			<u>Поздравляю, Вы прошли тест!!!</u> <!-- if result >= 80% -->
			<?php } else if ($percent<80 && $percent>=70){ ?>
 			<u>Попробуйте еще раз, Вы сможете :)</u> <!-- if result < 80% and result >= 70% -->
 			<?php } else {?>
			<u>Вам следует посмотреть видео урок еще раз.</u> <!-- if result < 70% -->
			<?php } ?>
			&gt;&gt;</p>
			<hr>
		</div>
		<div class='test-box'>
		<?php
			$number_of_exists_question = 1;
			foreach($test_data as $data_key => $data_value){
		?>
			<div class='box-test' style='display: block; border-bottom:2px solid #0068CA; padding-bottom:20px;' data_num = '<?php echo $data_key;?>'>
				<div class='row'>
					<div class='col-md-12 col-sm-12 question'>
						<h3>Вопрос №<?php echo $number_of_exists_question;?>:</h3>
						<?php if($data_value['text']!=''){ ?>
						<div class='question_txt'>
							&nbsp;&nbsp;&nbsp;&nbsp;<?php echo nl2br($data_value['text']);?>
						</div>
						<?php } ?>
						<?php if($data_value['image']!=''){ ?>
						<div class='question_img img-big'>
							<center>
								<!-- <img src="../img/test/"> -->
								<p style='margin:10px 0;' data_src = "../img/test/<?php echo $data_value['image'];?>">Посмотреть изображение +</p>
							</center>
						</div>
						<?php } ?>
					</div>
					<div class='col-md-12 col-sm-12'>
						<div class='row'>
						<div class='col-md-12 col-sm-12'>
							<h3>Ответы:</h3>
						</div>
						<?php 
							$count = 1;
							foreach($data_value['answer'] as $answer_key => $answer_value){								
						?>
						<?php
							$css = '';
							$torf = 'none';
							if(isset($data_json[$data_key]) && in_array($answer_key,$data_json[$data_key]) && $answer_value['torf']=='1'){
								$css .= 'border:1px solid rgb(50,150,50); ';
								$torf = 'true';
							}
							else if(isset($data_json[$data_key]) && in_array($answer_key,$data_json[$data_key]) && $answer_value['torf']=='0'){
								$css .= 'border:1px solid rgb(237,149,149); ';
								$torf = 'false';
							}
						?>
						<div class='col-md-6 col-sm-6 answer' style='border-radius: 5px;'>
							<?php
								if($torf=='true'){
							?>
							<span class='glyphicon glyphicon-ok-sign text-success'></span>
							<?php } else if ($torf=='false'){?>
							<span class='glyphicon glyphicon-remove-sign text-danger'></span>
							<?php }?>
							<span><?php echo $count.")";?></span>
							<?php if($answer_value['text']!=''){ ?>
								<span style='font-family: arial; font-weight: bold; font-size: 120%;'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $answer_value['text'];?></span>
							<?php }?>
							<?php if($answer_value['image']!=''){ ?>
							<div class='answer_img img-big'>
								<center>
									<!-- <img src="../img/test/"> -->
									<p style='margin:10px 0;' data_src = "../img/test/<?php echo $answer_value['image'];?>" style='vertical-align: middle;'>Посмотреть изображение +</p>
								</center>
							</div>
							<?php } ?>
						</div>
						<?php $count++; } ?>
						</div>
					</div>
				</div>
			</div>
		<?php $number_of_exists_question++; }?>
		</div>
		<div class='img-section'>
			<center>
				<div class='img-big-box'>
					<img src="" class='img-responsive'>
					<span class='glyphicon glyphicon-remove remove-img-section'></span>
				</div>
			</center>
		</div>
	</div>

<?php include_once('js.php');?>
<script type="text/javascript">
	$(document).on('click','.img-big',function(){
		$attr = $(this).find('p').attr('data_src');
		console.log($attr);
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
</script>
</body>
</html>