<?php

	include_once("../../connection.php");

	if(isset($_POST['set_student_filter'])) {

		$_SESSION['filter_school'] = false;
		$_SESSION['filter_subject'] = false;
		$_SESSION['filter_finish_course'] = false;
		$_SESSION['filter_study_session'] = "";


		if (isset($_POST['filter_study_session'])) {
			$_SESSION['filter_study_session'] = $_POST['filter_study_session'];
		}

		$_SESSION['filter_school'] = isset($_POST['filter_school']) ? true : false;
		$_SESSION['filter_subject'] = isset($_POST['filter_subject']) ? true : false;
		$_SESSION['filter_finish_course'] = isset($_POST['filter_finish_course']) ? true : false;

		header('location:../index.php');
	}
	echo "wrong";
	echo "<br>";
	echo isset($_POST['set_student_filter']) ? "true" : "false";

?>