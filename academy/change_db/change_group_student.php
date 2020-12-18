<?php
	include_once('../common/connection.php');

	try {
		
		$query = "SELECT gs.* from group_student gs";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$query = "UPDATE group_student SET status = :status WHERE id = :id";

		foreach ($sql_result as $value) {
			if ($value['has_payment'] == 1) {
				if ($value['start_from'] == '' || $value['start_from'] == null) {
					$status = 'waiting';
				} else {
					$status = "active";
				}
			} else {
				$status = 'inactive';
			}

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':id', $value['id'], PDO::PARAM_INT);
			$stmt->execute();
		}
		

	} catch (Exception $e) {
		throw $e;
	}
?>