<?php
	if(!isset($_SESSION)) {
		session_start();
	}

	if (!isset($_GET['ees_id']) || 
		!isset($_GET['ees_code']) || 
		!isset($_GET['ees_surname']) || 
		!isset($_GET['ees_name']) || 
		!isset($_GET['test_result']) || 
		!isset($_GET['finish'])) {
		header('location:signin.php');
	}

	if (isset($_GET['cabinet']) && $_GET['cabinet'] == 1) {
		$_SESSION['to_cabinet'] = true;
	} else {
		$_SESSION['to_cabinet'] = false;
	}

	$_SESSION['ees_id'] = $_GET['ees_id'];
	$_SESSION['ees_code'] = $_GET['ees_code'];
	$_SESSION['ees_surname'] = $_GET['ees_surname'];
	$_SESSION['ees_name'] = $_GET['ees_name'];
	$_SESSION['test_result'] = $_GET['test_result'] != "" ? json_decode($_GET['test_result'], true) : "";
	$_SESSION['finish'] = $_GET['finish'];
	// print_r($_SESSION);
	header('location:index.php?first_instruction');
?>