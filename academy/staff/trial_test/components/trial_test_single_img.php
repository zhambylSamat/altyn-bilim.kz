<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/trial_test/views.php');

	if (isset($_GET['trial_test_file_id']) && isset($_GET['trial_test_file_link']) && isset($_GET['trial_test_file_order'])) {
		$trial_test_file_id = $_GET['trial_test_file_id'];
		$trial_test_file_link = $_GET['trial_test_file_link'];
		$trial_test_file_order = $_GET['trial_test_file_order'];
		echo trial_test_single_img($trial_test_file_id, $trial_test_file_link, $trial_test_file_order);
	} else {
		echo 'error';
	}
?>