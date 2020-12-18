<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

     if (isset($_POST['insert-new-holiday'])) {
		try {

			$from_date = $_POST['from-date'];
			$to_date = $_POST['to-date'];
			$title = $_POST['title'];
			// $comment = $_POST['comment'];

			$from_date_splitted = explode('.', $from_date);
			$from_date = $from_date_splitted[2].'-'.$from_date_splitted[1].'-'.$from_date_splitted[0];

			$to_date_splitted = explode('.', $to_date);
			$to_date = $to_date_splitted[2].'-'.$to_date_splitted[1].'-'.$to_date_splitted[0];

			$query = "INSERT INTO holidays (from_date, to_date, title)
									VALUES (:from_date, :to_date, :title)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
			$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			// $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->execute();

			header("Location:".$ab_root.'/academy/staff');
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>