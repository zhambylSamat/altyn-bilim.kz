<?php
	include_once('views.php');

	if (isset($_GET['get_next_question'])) {
		$data = array();
		if (isset($_GET['index'])) {
			$data['data'] = get_next_question_and_answer($_GET['index']);
			$data['success'] = true;
		} else {
			$data['data'] = array();
			$data['success'] = false;
		}
		echo json_encode($data);
	}
?>