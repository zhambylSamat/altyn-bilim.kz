<?php
	include_once('../common/connection.php');

	$student_login_logs = get_student_login_logs();

	if (count($student_login_logs)) {
		remove_sll($student_login_logs);
	}

	function get_student_login_logs () {
		GLOBAL $connect;

		try {

			$query = "SELECT sll.id
						FROM student_login_log sll
						WHERE DATE_ADD(sll.created_date, INTERVAL 10 DAY) < NOW()";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_sll ($sll_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM student_login_log WHERE id = :id";

			foreach ($sll_ids as $id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>