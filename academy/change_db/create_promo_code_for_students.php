<?php
	include_once('../common/connection.php');
	include_once('../common/global_controller.php');
	
	$query = "SELECT s.id
				FROM student s";

	$stmt = $connect->prepare($query);
	$stmt->execute();
	$query_result = $stmt->fetchAll();

	foreach ($query_result as $value) {
		set_generated_promo_code($value['id']);
	}
?>