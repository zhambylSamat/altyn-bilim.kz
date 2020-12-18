<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	$text = $_POST['order_id'].' '.$_POST['client_id'];

	$query = "INSERT INTO payment_test (test_test) VALUES(:test_text)";

	$stmt = $connect->prepare($query);
	$stmt->bindParam(':test_text', $text, PDO::PARAM_STR);
	$stmt->execute();
?>