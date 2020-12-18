<?php
	include_once('../common/connection.php');

	try {

		$new_group_id = 27; //25, 24, 23, 27
		$old_group_id = 10; //16, 15, 14, 10

		$query = "SELECT gs.id, gs.access_until FROM group_student gs WHERE gs.group_info_id IN (25, 24, 23, 27)";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();


		$query = "UPDATE group_student_payment SET access_until = :access_until WHERE group_student_id = :group_student_id";
		$group_id = 0;
		foreach ($sql_result as $value) {
			if($value['access_until'] != '' && $value['access_until'] != null) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':access_until', $value['access_until'], PDO::PARAM_STR);
				$stmt->bindParam(':group_student_id', $value['id'], PDO::PARAM_INT);
				$stmt->execute();
			}
		}

	} catch (Exception $e) {
		throw $e;
	}
?>