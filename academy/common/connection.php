<?php
 
include_once('connection_datas.php');
include_once('constants.php');
include_once('check_authentication.php');

try {
	$connect = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// $mysqli = new mysqli("localhost", "root", "", $dbname);
	$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connect->exec("set names utf8");	
} catch (PDOException $e) {
	echo "Error ".$e->getMessage()." !!!";
}
// session_set_cookie_params(0);
date_default_timezone_set("Asia/Almaty");
mb_internal_encoding("UTF-8");
if(!isset($_SESSION)) {
	session_start();
}

$_SESSION['month'] = $month;
$_SESSION['day_name'] = $day_name;

if (isset($_SESSION['user']) && !isset($_SESSION['page_navigator'])) {
	if (!isset($_SESSION['page_navigator'])) {
		if ($_SESSION['user'] == $ADMIN) {
			$_SESSION['page_navigator'] = $admin_page_navigator;
		} else if ($_SESSION['user'] == $MODERATOR) {
			$_SESSION['page_navigator'] = $admin_page_navigator;
		} else if ($_SESSION['user'] == $TEACHER) {
			$_SESSION['page_navigator'] = $teacher_page_navigator;
		} else if  ($_SESSION['user'] == $STUDENT) {
			$_SESSION['page_navigator'] = $student_page_navigator;
		}
	}
}
include_once('global_controller.php');
?>