<?php
	include_once('../connection.php');

	$static_modules_list = array('',
								'Модуль 1 - Фундамент',
								'Модуль 2 - Дәреже. Түбір',
								'Модуль 3 - Өрнекті ықшамдау',
								'Модуль 4 - Теңдеулер',
								'Модуль 5 - Теңсіздіктер',
								'Модуль 6 - Иррационал теңдеулер',
								'Модуль 7 - Прогрессия');

	$result = array();
	$name = '';
	$surname = '';
	$ees_id = isset($ees_id) ? $ees_id : $_SESSION['ees_id'];
	$stmt = $conn->prepare("SELECT id,
								entrance_code,
								result_json,
								student_name,
								student_surname,
								test_content,
								DATE_FORMAT(submit_date, '%d.%m.%Y %H:%i:%S') AS submit_date
							FROM entrance_examination_student ees 
							WHERE ees.id = :id");
	$stmt->bindParam(':id', $ees_id, PDO::PARAM_INT);
	$stmt->execute();

	$sql_res = $stmt->fetch(PDO::FETCH_ASSOC);
	// print_r($sql_res['result_json']);
	$result = json_decode($sql_res['result_json'], true);

	$prefixes = array('', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

	$which_module = "";

	foreach ($result['content'] as $value) {
		$result_mark = 0;
		foreach ($value['content'] as $val) {
			$result_mark += $val['result'];
		}
		if ($result_mark <= 0) {
			$module_num = explode('.', explode(' ', $value['test_name'])[1])[0];
			$which_module = $static_modules_list[intval($module_num)];
			break;
		}
	}

	$test_content = array();

	foreach (json_decode($sql_res['test_content'], true) as $key => $value) {
		$test_content[$key]['name'] = $value['name'];
		$test_content[$key]['test_order'] = $value['test_order'];
	}

	$name = $sql_res['student_name'];
	$surname = $sql_res['student_surname'];
	$id = $sql_res['id'];
	$code = $sql_res['entrance_code'];

	$link = "https://online.altyn-bilim.kz/academy/registration.php?recomendation_text=".$which_module;
	// $link = "http://localhost/altynbilim/academy/registration.php?recomendation_text=".$which_module;
	if (isset($_SESSION['to_cabinet']) && $_SESSION['to_cabinet']) {
		$link = "https://online.altyn-bilim.kz/academy/student/index.php?recomendation_text=".$which_module."&nav=registration-navigation";
		// $link = "http://localhost/altynbilim/academy/student/index.php?recomendation_text=".$which_module."&nav=registration-navigation";
	}
?>
<div class='container'>
	<div class='result-box'>
		<center><p class='text-success result-test-title'><b>Тест аяқталды</b></p></center>
		<p id='test-result-text'>&nbsp;&nbsp;&nbsp;Тест қорытындысы бойынша сізге <span id='which-module-title'><a href="<?php echo $link;?>" target='_blank'>"<?php echo $which_module; ?>"</a></span> тарауынан бастауыңызға кеңес береміз</p>

		<!--  -->
		<!-- <p>Оқушының аты: <b><?php echo $surname." ".$name; ?></b></p> -->
		<!-- <p>Тест тапсырушының ID коды: <b><?php echo $code.$id; ?></b></p> -->
		<!-- <p>Тапсырылған уақыт: <b><?php echo $sql_res['submit_date']; ?></b></p> -->
		<p>Тестте болған есептердің тақырыптары: 
			<?php
				if (isset($result['content']) && count($result['content'])>0) { 
					foreach ($result['content'] as $value){ 
						$html = "";
						$test_name = $value['test_name'].": ";
						$test_num = $value['test_num'];
						$checkpoint = $value['test_order']%2==0 && $value['test_order']!=0;
						$test_order = $value['test_order'];
						$torf = false;
						$text_result = "";
						foreach ($value['content'] as $value) {
							switch ($value['result']) {
								case '0':
									$torf = false;
									$text_result .= "<b><i><span class='text-warning'>Белгіленбеген;</span></i></b>&nbsp;";
									break;
								case '1':
									$torf = true;
									$text_result .= "<b><i><span class='text-success'>Дұрыс;</span></i></b>&nbsp;";
									break;
								case '-1':
									$torf = false;
									$text_result .= "<b><i><span class='text-danger'>Қате;</span></i></b>&nbsp;";
									break;
							}
						}
						$style = !$torf ? "border-left:10px solid red; color:red;" : "color:green;";
						$style .= $checkpoint ? " font-weight: bold;  " : "";
						$html .= "<td><span style='".$style."' >".$test_name."</span></td>";
						$html .= "<td>".$text_result."</td>";
						$test_content[$test_num]['html'] = $html;
						$test_content[$test_num]['is_correct'] = $torf ? 'true' : 'false';
					}
				} 
			?>
			<div class='row'>
				<div class='col-md-8 col-sm-8 col-sm-12'>
					<table class='table'>
					<?php 
						$count = 0;
						$test_order = 1;
						foreach ($test_content as $key => $value) {
							if ($test_order != 0 && $test_order%2!=0 && !isset($value['html'])) {
								$attrs = "class='topic' style='display:none;' data-order='order-".$test_order."'";
								echo "<tr ".$attrs.">";
								echo "<td></td><td colspan='3' style='font-style:italic;'>".$value['name']."</td>";
							}
							if (isset($value['html'])) {
								$is_correct = 'false';
								$has_child = false;
								$child_order = 0;
								if ($value['test_order'] != 0 && $value['test_order']%2==0) { 
									$is_correct = $value['is_correct'];
									$has_child = true;
									$child_order = $test_order;
									$test_order = $value['test_order']+1;
								}
								echo "</tr>";
								$attrs = "";
								if ($has_child) {
									$attrs = "class='show-child-topics' data-is-correct='".$is_correct."' data-child-order='".$child_order."'";
								}
								echo "<tr ".$attrs."><td>".(++$count)."</td>".$value['html']."</tr>";
							}
						} 
					?>
					</table>
				</div>
			</div>
		</p>
		<p id='text-result-subtext'>Математика курсын бастау керек тарау: <span id='which-module-title'><a href="<?php echo $link; ?>" target='_blank'>"<?php echo $which_module; ?>"</a></span></p>
		<a type='button' class='btn btn-info btn-lg btn-block'  href="<?php echo $link; ?>" target='_blank'>Курсқа жазылу</a>
		<hr>
		<script type="text/javascript">test_topic_list();</script>
	</div>
	<div class='test-box'>
	<?php
		$question_count = 0;
		if (isset($result['content']) && count($result['content'])) {
			echo "<p id='work-on-results-title'>Қатемен жұмыс: ";
			foreach($result['content'] as $tValue){
				$more_than_one_question = false;
				$sub_question_count = 0;
				$question_count++;
				foreach ($tValue['content'] as $qValue) {
					$sub_text = "";
					if ($qValue['result'] == '0') {
						$sub_text = "&nbsp;<b><i><span class='text-warning'>Белгіленбеген</span></i></b>";
					} else if ($qValue['result'] == '1') {
						$sub_text = "&nbsp;<b><i><span class='text-success'>Дұрыс</span></i></b>";
					} else if ($qValue['result'] == '-1') {
						$sub_text = "&nbsp;<b><i><span class='text-danger'>Қате</span></i></b>";
					}

					$sub_question_count++;
					$more_than_one_question = true;
	?>
		<div class='box-test' style='display: block; border-bottom:2px solid #0068CA;'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 question'>
					<p id='question-number'>Сұрақ нөмірі: <?php echo $question_count." (".$sub_question_count.")".$sub_text?></p>
					<p id='question-topic-title'><i><?php echo $tValue['test_name']; ?></i></p>
					<?php if($qValue['question_txt']!=''){ ?>
					<div class='question_txt'>
						<?php echo nl2br($qValue['question_txt']);?>
					</div>
					<?php } ?>
					<?php if($qValue['question_img']!=''){ ?>
					<div class='question_img img-big'>
						<center>
							<center><img src="../img/test/<?php echo $qValue['question_img'];?>"></center>
							<!-- <p style='margin:10px 0;' data-src = "../img/test/<?php echo $qValue['question_img'];?>">Суретті ашу +</p> -->
						</center>
					</div>
					<?php } ?>
				</div>
				<div class='col-md-12 col-sm-12'>
					<div class='row'>
					<?php 
						$count = 1;
						$torf = 'none';

						$selected_answer_num = $qValue['answer_num'];
						foreach($qValue['answer'] as $aValue){								
					?>
					<?php
						$css = '';

						// if($aValue['torf']==1){
						// 	$css = "background-color:lightgreen;";
						// }

						if($selected_answer_num==$aValue['answer_num'] && $aValue['torf']==1){
							$css = "background-color:lightgreen;";
						}
						else if($selected_answer_num==$aValue['answer_num'] && $aValue['torf']==0){
							$css = "background-color:#FE4D4D;";
						}
					?>
					<div class='col-md-4 col-sm-4 answer' style='<?php echo $css; ?>'>
						<div class='row'>
							<div class='col-md-12 col-sm-12 col-xs-3 prefix-answers'>
								<?php
									if($aValue['torf']==1){
								?>
								<span class='glyphicon glyphicon-ok-sign text-success'></span>
								<?php } ?>
								<span><?php echo $prefixes[$count].")";?></span>
							</div>
							<?php if($aValue['answer_txt']!=''){ ?>
								<span style='font-family: arial; font-weight: bold; font-size: 120%;'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $aValue['answer_txt'];?></span>
							<?php }?>
							<?php if($aValue['answer_img']!=''){ ?>
							<div class='col-md-12 col-sm-12 col-xs-9 answer_img img-big'>
								<center>
									<center>
										<img src="../img/test/<?php echo $aValue['answer_img'];?>">
									</center>
									<!-- <p style='margin:10px 0;' data-src="../img/test/<?php echo $aValue['answer_img'];?>" style='vertical-align: middle;'>Суретті ашу +</p> -->
								</center>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php $count++; } ?>
					</div>
				</div>
			</div>
		</div>
	<?php }}} ?>
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