<?php
	include_once('../common/connection.php');

	try {
		
		$query = "SELECT gs.id AS group_student_id,
					(SELECT lp2.created_date
					FROM lesson_progress lp2
					WHERE lp2.group_info_id = gs.group_info_id
						AND lp2.subtopic_id = gs.start_from) AS lp_created_date
					FROM group_student gs
					WHERE gs.has_payment = 1
						AND gs.start_from != 0
						AND gs.access_until IS NOT NULL";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		$result = array();
		$query = "UPDATE group_student SET start_date = :start_date, access_until = :access_until WHERE id = :gs_id";
		foreach ($sql_result as $value) {
			array_push($result, array('gs_id' => $value['group_student_id'],
									'lp_created_date' => $value['lp_created_date']));
			$stmt = $connect->prepare($query);
			$access_until = date('Y-m-d', strtotime($value['lp_created_date'].' + 1 month'));
			$stmt->bindParam(':gs_id', $value['group_student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':start_date', $value['lp_created_date'], PDO::PARAM_STR);
			$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
			$stmt->execute();
		}
		

	} catch (Exception $e) {
		throw $e;
	}
?>