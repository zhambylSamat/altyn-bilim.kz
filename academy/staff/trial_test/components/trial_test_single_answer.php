<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/trial_test/views.php');

	if (isset($_GET['answer_numeration']) && isset($_GET['answer_value'])) {
		$answer_numeration = $_GET['answer_numeration'];
		$answer_value = json_decode($_GET['answer_value'], true);
		echo trial_test_single_answer($answer_numeration, $answer_value);
	} else {
		echo 'error';
	}
?>