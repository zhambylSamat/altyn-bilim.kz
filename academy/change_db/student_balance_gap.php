<?php
	include_once('../common/connection.php');

	$query = "SELECT id FROM student_balance WHERE is_used = 1";
	$stmt = $connect->prepare($query);
	$stmt->execute();

	$result = array();
	foreach ($stmt->fetchAll() as $value) {
		array_push($result, $value['id']);
	}
	echo json_encode($result);
?>