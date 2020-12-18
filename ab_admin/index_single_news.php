<?php
	$student_num = $_GET['data_num'];
	try {
		include_once('../connection.php');
		$stmt = $conn->prepare("SELECT content FROM news where type = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
?>
<form id='form-single-student-news' method='post'>
	<div class='form-group'>
		<label class='col-md-12 col-sm-12 col-xs-12' for='context-id'>Мәтін:</label>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<textarea style='font-family: "Times New Roman", Times, serif; font-size:20px; line-height: 20px; padding:30px; resize:none;' class="form-control" name='news_context' rows="10" cols='5' wrap='hard' placeholder="Жаңалықтың мәтіні"><?php echo $result['content'];?></textarea>
		</div>
	</div>
	<?php if(isset($result) && $result['content']!=''){ ?>
	<h3 class="text-success" id='news-content-helper'>Жарияланды</h3>
	<?php }else{?>
	<h3 class="text-warning" id='news-content-helper'>Жарияланбады</h3>
	<?php } ?>
	<center>
		<input type="hidden" name="data_num" value='<?php echo $student_num; ?>'>
		<input type="submit" data-action='save' name="save_single_student_news" class='btn btn-sm btn-success' value="Сақтау және Жариялау">
		<input type="reset" class='btn btn-sm btn-warning' value='Отмена'>
		<a id='delete-single-student-news' style='<?php echo (isset($result) && $result['content']!='') ? "" : "display:none;"; ?>' class='btn btn-sm btn-danger' data-action='delete'>Өшіру</a>
	</center>
</form>