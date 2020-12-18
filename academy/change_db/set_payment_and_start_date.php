<?php
	include_once('../common/connection.php');

	try {
		$stmt = $connect->prepare("SELECT gs.*, lp.subtopic_id, lp.created_date AS lp_created_date
									FROM group_student gs,
										lesson_progress lp
									WHERE lp.group_info_id = gs.group_info_id
										AND lp.subtopic_id = gs.start_from");
		$stmt->execute();
		$sql_res = $stmt->fetchAll();
		print_r($sql_res);
		echo "<br><br><br>";
		$query = "UPDATE group_student SET has_payment = 1, start_date = :start_date WHERE id = :group_student_id";
		foreach ($sql_res as $value) {
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':start_date', $value['lp_created_date'], PDO::PARAM_STR);
			$stmt->bindParam(':group_student_id', $value['id'], PDO::PARAM_INT);
			$stmt->execute();
		}
	} catch(Exception $e) {
		throw $e;
	}
?>