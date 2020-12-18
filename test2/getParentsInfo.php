<?php
	include_once '../connection.php';

	try {

		$query = "SELECT p.name,
						p.surname,
						p.parent_order,
						p.phone,
						s.name s_name,
						s.surname s_surname,
						s.username,
						s.block
					FROM parent p,
						student s
					WHERE p.student_num = s.student_num
						AND s.block != 6
						AND p.parent_order = 2
					ORDER BY p.parent_order";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		// var_dump($sql_result);
		$result = json_encode($sql_result);
		// echo "<br><br><br><br><br><br><br><br><br>";
		// echo $stmt->rowCount();
		// echo "<br><br><br><br>";
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