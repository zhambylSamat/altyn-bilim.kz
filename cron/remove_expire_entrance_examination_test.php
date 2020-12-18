<?php
	include_once('../connection.php');

	remove_expred_entrance_examination_students();

	function remove_expred_entrance_examination_students () {
		GLOBAL $conn;

		try {

			$query = "DELETE FROM entrance_examination_student WHERE DATE_ADD(DATE_FORMAT(create_date, '%Y-%m-%d'), INTERVAL 2 DAY) <= DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;	
		}
	}
?>