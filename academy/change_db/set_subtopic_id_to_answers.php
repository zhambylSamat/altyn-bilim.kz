<?php
	include_once('../common/connection.php');

	try {

		$query = "SELECT mt.id, mt.subtopic_id FROM material_test mt";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$mt_info = $stmt->fetchAll();

		$query = "UPDATE answers SET subtopic_id = :subtopic_id WHERE material_test_id = :material_test_id";
		foreach ($mt_info as $value) {
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $value['subtopic_id'], PDO::PARAM_INT);
			$stmt->bindParam(':material_test_id', $value['id'], PDO::PARAM_INT);
			$stmt->execute();
		}
		
	} catch (Exception $e) {
		throw $e;
	}
?>