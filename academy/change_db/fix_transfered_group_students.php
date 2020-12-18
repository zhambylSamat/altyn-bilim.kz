<?php
	include_once('../common/connection.php');
	try {
	
		$query = "SELECT gs.student_id,
						gs.group_info_id,
						gs.access_until
					FROM group_student gs
					WHERE gs.group_info_id IN (14, 15)";
		$stmt = $connect->prepare($query);
		$stmt->execute();

		$query = "UPDATE group_student SET access_until = :access_until WHERE student_id = :student_id AND group_info_id = :group_id";
		foreach ($stmt->fetchAll() as $value) {
			$group_id = 0;
			if ($value['group_info_id'] == 14) {
				$group_id = 23;
			} else {
				$group_id = 24;
			}
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':access_until', $value['access_until'], PDO::PARAM_STR);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
		}


	} catch (Exception $e) {
		throw $e;
	}
?>