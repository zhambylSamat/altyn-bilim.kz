<?php
	include_once('../common/connection.php');

	try {

		$stmt = $connect->prepare("SELECT rr.student_id,
										rr.topic_id, 
										s.last_name, 
										s.first_name,
										t.title
									FROM registration_reserve rr,
										student s,
										topic t
									WHERE rr.topic_id = 49
										AND s.id = rr.student_id
										AND t.id = rr.topic_id");
		$stmt->execute();
		$sql_res = $stmt->fetchAll();

		$group_info_id = 18;
		$query = "INSERT INTO group_student (group_info_id, student_id)
									VALUES (:group_info_id, :student_id)";
		foreach ($sql_res as $value) {
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":group_info_id", $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->execute();
		}

	} catch (Exception $e) {
		throw $e;
	}
?>