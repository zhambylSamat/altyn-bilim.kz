<?php
	include_once('../common/connection.php');

	try {
		
		// $group_ids = array(9, 10, 11, 12, 18, 16);	
		$group_ids = array(13, 23, 24);	

		$query = "SELECT gs.id,
						gs.student_id,
						s.last_name, 
						s.first_name,
						gs.group_info_id,
						gi.group_name
					FROM group_student gs,
						student s,
						group_info gi
					WHERE gi.id = gs.group_info_id
						AND s.id = gs.student_id
						AND gs.group_info_id IN (".implode(',', $group_ids).")
					ORDER BY gi.group_name";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();


		$query = "INSERT INTO student_balance (student_id, group_id, days, comment)
					VALUES (:student_id, :group_id, 3, 'Бонуспен қосылған күндер')";
		foreach ($sql_result as $value) {
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $value['group_info_id'], PDO::PARAM_INT);
			$stmt->execute();
			// echo 'id: '.$value['id'].'<br>';
			// echo 'student_id: '.$value['student_id'].'<br>';
			// echo 'last_name: '.$value['last_name'].'<br>';
			// echo 'first_name: '.$value['first_name'].'<br>';
			// echo 'group_info_id: '.$value['group_info_id'].'<br>';
			// echo 'group_name: '.$value['group_name'].'<br>';
			// echo "<br>";
		}
		

	} catch (Exception $e) {
		// throw $e;
		echo 'ERROR: '.$e->getMessage()."!!!";
	}
?>