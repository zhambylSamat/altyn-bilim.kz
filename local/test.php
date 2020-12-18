<?php
include('../connection.php');
$test_data = array();
echo $_GET[md5(md5('dataNum'))];
if(isset($_SESSION['student_num']) && isset($_GET[md5(md5('dataNum'))])){
	try {
		$stmt_test_num = $conn->prepare("SELECT st.topic_num stTopicNum, t.test_num tNum FROM test t, subtopic st WHERE t.subtopic_num = :subtopic_num AND st.subtopic_num = t.subtopic_num AND t.type = 'subtopic_test'");
		$stmt_test_num->bindParam(":subtopic_num", $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);
		$stmt_test_num->execute();
		$test_num = $stmt_test_num->fetch(PDO::FETCH_ASSOC);
		$_SESSION['test_num'] = $test_num['tNum'];
		$_SESSION['topic_num'] = $test_num['stTopicNum'];
		$_SESSION['subtopic_num'] = $_GET[md5(md5('dataNum'))];

		$stmt_test_name = $conn->prepare("SELECT s.subject_name sName, t.topic_name tName, st.subtopic_name stName FROM subject s, topic t, subtopic st WHERE st.subtopic_num = :st_num AND t.topic_num = st.topic_num AND s.subject_num = t.subject_num ");
		$stmt_test_name->bindParam(":st_num", $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);
		$stmt_test_name->execute();
		$test_name = $stmt_test_name->fetch(PDO::FETCH_ASSOC);

		$stmt_question = $conn->prepare("SELECT q.question_num questionNum, q.question_text questionText, q.question_img questionImg FROM question q, test t WHERE t.subtopic_num = :subtopic_num AND q.test_num = t.test_num AND t.type = 'subtopic_test' ORDER BY updated_date ASC");
     	
	    $stmt_question->bindParam(':subtopic_num', $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);

	    $stmt_question->execute();
	    $question_quantity = $stmt_question->rowCount();
	    $result_question = $stmt_question->fetchAll(); 
	    foreach ($result_question as $readrow_question) {
	    	$question_num = $readrow_question['questionNum'];
	    	$test_data[$question_num]['text'] = $readrow_question['questionText'];
	    	$test_data[$question_num]['image'] = $readrow_question['questionImg'];
	    }
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	try {
		if($question_quantity>0){
			$tmp = array();
			foreach($test_data as $data_key => $data_value){
				array_push($tmp,$data_key);
			}
			$arr = implode(',',array_fill(0, count($tmp), "?"));
			$query = 'SELECT * FROM answer WHERE question_num in ('.$arr.')';
			$stmt_answer = $conn->prepare($query);
			foreach($tmp as $k => $v){
				$stmt_answer->bindValue(($k+1),$v);
			}
			$stmt_answer->execute();
			$result_answers = $stmt_answer->fetchAll();
			foreach ($result_answers as $readrow_answer) {
				$q_num = $readrow_answer['question_num'];
				$a_num = $readrow_answer['answer_num'];
				$test_data[$q_num]['answer'][$a_num]['text'] = $readrow_answer['answer_text'];
				$test_data[$q_num]['answer'][$a_num]['image'] = $readrow_answer['answer_img'];
				$test_data[$q_num]['answer'][$a_num]['torf'] = $readrow_answer['torf'];
			}
			$_SESSION['test_data'] = json_encode($test_data);
		}
	} catch (PDOException $e) {
		echo "ERROR: ".$e->getMessage()."!!!";
	}
}
?>

<div class='test-box'>
<?php
	$first_show = 'block';
	$number_of_exists_question = 1;
	foreach($test_data as $data_key => $data_value){
?>
	<div class='box-test' data_num = '<?php echo $data_key;?>' <?php if($first_show=='block'){ echo 'style="display:block"'; $first_show = 'none';}?>>
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
					<center><img src="../img/test/<?php echo $data_value['image'];?>"></center>
				</div>
				<?php } ?>
			</div>
			<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<h3>Ответы:</h3>
			</div>
			<?php 
				$count = 1;
				foreach($data_value['answer'] as $answer_key => $answer_value){
					if($count%2!=0 && $count!=1){
						echo "</div><div class='row'>";
					}
					
			?>
			<div class='col-md-6 col-sm-6 answer'>
				<div class='row'>
					<div class='col-md-12 col-sm-12'>
						<?php echo $count.")";?>&nbsp;&nbsp;
						<input type="checkbox" class='answer-box' data_number="<?php echo $number_of_exists_question;?>" data_num = '<?php echo $answer_key;?>' name="answer[]">
					</div>
					<?php if($answer_value['text']!=''){ ?>
					<div class='col-md-12 col-sm-12'>
						<p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $answer_value['text'];?></p>
					</div>
					<?php }?>
					<?php if($answer_value['image']!=''){ ?>
					<div class='answer_img col-md-12 col-sm-12 img-big'>
						<center>
							<img src="../img/test/<?php echo $answer_value['image'];?>">
						</center>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php $count++; } ?>
			</div>
		</div>
	</div>
<?php $number_of_exists_question++; }?>
</div>
<div class='row footer_navigation'>
	<span><b>Номер вопроса:&nbsp;</b></span>
	<?php 
		for($i = 1; $i<$number_of_exists_question; $i++){
	?>
	<a class='btn <?php if($i==1) echo 'btn-primary text-underline'; else echo 'btn-info';?> btn-sm btn-question' data-number='<?php echo $i;?>'><?php echo $i;?></a>
	<?php }?>
	<button class='btn btn-success btn-sm submit'>Отправить</button>
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
	$count_question = <?php echo $number_of_exists_question-1;?>;
	$dic = {};
	$arr = [];
	$t_name = '<?php echo json_encode($test_name); ?>';
</script>