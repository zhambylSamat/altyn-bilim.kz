<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	$data = array();

	if (isset($_GET['get_not_started_subjects'])) {
		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT sc.id AS subject_id
						FROM subject_configuration sc
						WHERE sc.subject_id NOT IN (SELECT gi.subject_id
													FROM group_student gs,
														group_info gi
													WHERE gs.student_id = :student_id
														AND gi.id = gs.group_info_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['subject_id']);
			}

			$data['result'] = $result;
			$data['success'] = true;
	    } catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = "ERROR: ".$e->getMessage()."!!!";
    	}
    	echo json_encode($data);
	}
?>