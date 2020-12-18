<?php
	include_once '../connection.php';

	try {

		$query = "SELECT name,
						surname,
						dob,
						username
					FROM teacher
					WHERE block = 0";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		// var_dump($sql_result);
		$result = json_encode($sql_result);
		// echo "<br><br><br><br><br><br><br><br><br>";
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