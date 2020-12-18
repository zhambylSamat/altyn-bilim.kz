<?php
	$data = array();
	include_once("../connection.php");
	if(isset($_GET[md5(md5('fromArchive'))])){
		try {

			$data_num = $_GET['data_num'];
			$data_name = $_GET['data_name'];

			if($data_name=='student'){
				$stmt = $conn->prepare("UPDATE student SET block = 0, unblocked_date = NOW() WHERE student_num = :student_num");
				$stmt->bindParam(':student_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($data_name=='teacher'){
				$stmt = $conn->prepare("UPDATE teacher SET block = 0 WHERE teacher_num = :teacher_num");
				$stmt->bindParam(':teacher_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($data_name=='group_student'){
				$unblocked_date = date("Y-m-d");
				$stmt = $conn->prepare("UPDATE group_student SET block = 0, unblocked_date = :unblocked_date WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':unblocked_date', $unblocked_date, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();

				$stmt = $conn->prepare("INSERT INTO statistics_student_frequency 
											(student_num, subject_num, group_student_num, status, period)
											SELECT gs.student_num, gi.subject_num, gs.group_student_num, 'in', DATE_FORMAT(NOW(), '%Y-%m-01')
											FROM group_student gs,
												group_info gi
											WHERE gi.group_info_num = gs.group_info_num
												AND gs.group_student_num = :group_student_num");
				$stmt->bindParam(":group_student_num", $data_num, PDO::PARAM_STR);
				$stmt->execute();

				$stmt = $conn->prepare("UPDATE student SET block = 0, unblocked_date = NOW() WHERE student_num = (SELECT student_num FROM group_student WHERE group_student_num = :group_student_num)");
				$stmt->bindParam(':group_student_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($data_name=='group'){
				$stmt = $conn->prepare("UPDATE group_info SET block = 0 WHERE group_info_num = :group_info_num");
				$stmt->bindParam(':group_info_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();
			}

			$data['success'] = true;
		} catch (PDOException $e) {
			$data['success'] = false;
			$data['error'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	}
?>