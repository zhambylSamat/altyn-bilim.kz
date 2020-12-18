<?php

	include_once("../../connection.php");
	$subject_num = '';
	$practice = 0;
	$theory = 0;
	if (isset($_GET['subject_num']) && $_GET['subject_num'] != '') {
		$subject_num = $_GET['subject_num'];
	}
	try {
		$stmt = $conn->prepare("SELECT * FROM subject WHERE subject_num = :subject_num");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_subject = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT * FROM config_subject_quiz WHERE subject_num = :subject_num");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		$row_count = $stmt->rowCount();
		if ($row_count == 1) {
	    	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	    	$practice = $result['practice'];
	    	$theory = $result['theory'];
		} else if ($row_count == 0) {
			$stmt = $conn->prepare("INSERT INTO config_subject_quiz (subject_num) VALUES (:subject_num)");
			$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
			$stmt->execute();

			$practice = 0;
			$theory = 0;
		}
	} catch (PDOException $e) {
		throw $e;
	}
?>
<center>
	<a role='button' class='config pull-left' data-type='start'>Бастапқы бет</a>
	<br>
	<h3><?php echo $result_subject['subject_name']; ?></h3>
	<p class='help-block'>Мұғалімнің аралық бақылаудың бағасын қоя алатын жері</p>
	<form class='config_form' data-type='<?php echo md5('subject_quiz'); ?>'>
		<div class='checkbox'>
			<label>
				<input type="checkbox" name='practice' <?php echo $practice==1 ? 'checked' : ''; ?>> Есеп
			</label>
		</div>
		<div class='checkbox'>
			<label>
				<input type="checkbox" name='theory' <?php echo $theory==1 ? 'checked' : ''; ?>> Теория
			</label>
		</div>
		<input type="hidden" name="subject_num" value='<?php echo $subject_num; ?>'>
		<input type="submit" class='btn btn-xs btn-success' value='Сақтау'>
	</form>
</center>