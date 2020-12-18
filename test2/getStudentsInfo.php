<?php
	include_once '../connection.php';

	try {

		$query = "SELECT s.name,
						s.surname,
						s.altyn_belgi,
						s.red,
						s.phone,
						s.username,
						s.dob,
						s.school,
						s.class,
						s.home_phone,
						s.address,
						s.target_subject,
						s.target_from,
						s.instagram
					FROM student s
					WHERE s.block != 6
					ORDER BY s.student_num";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		// var_dump($sql_result);
		$result = json_encode($sql_result);
		// echo "<br><br><br><br><br><br><br><br><br>";
		// echo $stmt->rowCount();
		// echo "<br><br>";
		// echo "<br><br>";
		echo $result;

		// $result = array();

		// $subject_num = '';
		// $topic_num = '';
		// $subtopic_num = '';
		// foreach ($sql_result as $value) {
		// 	if ($subject_num != $value['subject_num']) {
		// 		$subject_num = $value['subject_num'];
		// 	}
		// }
		
	} catch (Exception $e) {
		throw $e;
	}
?>