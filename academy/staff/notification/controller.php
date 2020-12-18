<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	if (isset($_GET['notification_select'])) {
		$data = array();
		try {
			$id = $_POST['id'];
			$type = $_POST['type'];

			$query = "SELECT count(id) AS count FROM notification_selection WHERE object_id = :id AND object_type_id = :type";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->execute();
			$row_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
			if ($row_count == 0) {
				$query = "INSERT INTO notification_selection (object_id, object_type_id) VALUES(:object_id, :object_type)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':object_id', $id, PDO::PARAM_INT);
				$stmt->bindParam(':object_type', $type, PDO::PARAM_INT);
				$stmt->execute();
			}

			$data['success'] = true;
			$data['count'] = $row_count;
			
		} catch (Exception $e) {
			// throw $e;
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['remove_no_progress_student_notification'])) {
		$data = array();
		try {

			$npsn_id = $_GET['npsn_id'];

			$query = "DELETE FROM no_progress_student_notification WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $npsn_id, PDO::PARAM_INT);
			$stmt->execute();
			
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	}
?>