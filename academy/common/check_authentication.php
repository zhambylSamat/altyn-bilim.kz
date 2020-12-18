<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	function check_admin_student_access () {
		GLOBAL $ADMIN;
		GLOBAL $MODERATOR;
		GLOBAL $STUDENT;

		check_access();
		if ($_SESSION['user'] != $ADMIN && $_SESSION['user'] != $MODERATOR && $_SESSION['user'] != $STUDENT) {
			go_to_authorization();
		}
	}

	function check_admin_access() {
		GLOBAL $ADMIN;
		GLOBAL $MODERATOR;
		check_access();
		if ($_SESSION['user'] != $ADMIN && $_SESSION['user'] != $MODERATOR) {
			go_to_authorization();
		}
	}

	function check_teacher_access() {
		GLOBAL $TEACHER;
		check_access();
		if ($_SESSION['user'] != $TEACHER) {
			go_to_authorization();
		}
	}

	function check_student_access() {
		GLOBAL $STUDENT;
		check_access();
		if ($_SESSION['user'] != $STUDENT) {
			go_to_authorization();
		}
	}

	function check_access () {
		if (!isset($_SESSION['user'])) {
			go_to_authorization();
		}
	}

	function go_to_authorization() {
		GLOBAL $ab_root;
		$sign_in_path = $ab_root.'/academy/sign_in.php';
		header('Location:'.$sign_in_path);
	}

	function admin_create_edit_remove_access() {
		GLOBAL $ADMIN;
		check_access();
		if ($_SESSION['user'] == $ADMIN) {
			return true;
		}
		return false;
	}
?>