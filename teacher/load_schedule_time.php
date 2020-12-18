<?php
	include_once('../connection.php');
	$data = array();
	try {
		$stmt = $conn->prepare("SELECT DISTINCT DATE_FORMAT(gi.finish_lesson, '%H:%i') as finish_lesson
								FROM group_info gi, 
									schedule sch 
								WHERE gi.teacher_num = :teacher_num 
									AND sch.group_info_num = gi.group_info_num 
									AND sch.week_id = WEEKDAY(CURDATE())+1 
									AND gi.finish_lesson >= CURRENT_TIME()
								ORDER BY finish_lesson ASC");
		$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
		for ($i = 0; $i < count($result); $i++) {
			$data['data'][$i] = $result[$i]['finish_lesson'];
		}
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
?>