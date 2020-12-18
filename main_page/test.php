<?php
include('connection.php');
if(isset($_SESSION['student_num']) && isset($_GET[md5(md5('dataNum'))])){
	try {
		$stmt_question = $conn->prepare("SELECT q.question_num questionNum, q.question_text questionText, q.question_img questionImg FROM question q, test t WHERE t.subtopic_num = :subtopic_num AND q.test_num = t.test_num ORDER BY updated_date ASC");
     	
	    $stmt_question->bindParam(':subtopic_num', $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);

	    $stmt_question->execute();
	    $result_question = $stmt_question->fetchAll(); 
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
?>
<form>
	<?php
		foreach($result_question as $readrow_question){
			try {
				$stmt_answer = $conn->prepare("SELECT * FROM answer WHERE question_num = :question_num ORDER BY answer_num ASC");
					     	
			    $stmt_answer->bindParam(':question_num', $readrow_question['questionNum'], PDO::PARAM_STR);

			    $stmt_answer->execute();
			    $result_answer = $stmt_answer->fetchAll(); 
			} catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
	?>
	<div class='box'>
		<div class='row'>
			<div class='form-group col-md-12 col-sm-12'>
				<input type="hidden" name="hidden_question_num" value='<?php echo $readrow_question['questionNum'];?>'>
				<h3>Вопрос:</h3>
				<div class='question_txt'>
					<p>
						<?php echo $readrow_question['questionText'];?>
					</p>
				</div>
				<div class='question_img'>
					<?php if($readrow_question['questionImg']!=''){ ?>
					<div class='img-big'><center>Увеличить</center></div>
					<img src="img/test/<?php echo $readrow_question['questionImg'];?>">
					<?php } ?>
				</div>
			</div>
			<?php 
				foreach($result_answer as $readrow_answer){
			?>
			<div class='form-group col-md-6 col-sm-6'>
				<div class='row'>
					<div class='col-md-11 col-sm-11'>
						<p><?php echo $readrow_answer['answer_text'];?></p>
					</div>
					<div class='col-md-1 col-sm-1'>
						<input type="checkbox" name="answer[]">
					</div>
					<div class='col-md-11 col-sm-11'>
						<div class='answer_img'>
							<?php if($readrow_answer['answer_img']!=''){ ?>
							<div class='img-big'><center>Увеличить</center></div>
							<img src="img/test/<?php echo $readrow_answer['answer_img'];?>">
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</form>
<?php }?>