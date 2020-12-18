<?php
date_default_timezone_set("Asia/Almaty");
$admin = 'ab_admin';
if(isset($_GET['admin'])){
$file = fopen($admin."/logDB/log.txt", "a") or die("Unable to open file!");
$txt = "--------------------------Admin LogOut---".date("d-m-Y h:i:sa")."------------------------------\n\n\n";
fwrite($file, $txt);
fclose($file);
}
session_start();
session_unset();
if(isset($_GET['local'])){
	header('location:local/signin.php');
}
else if(isset($_GET['admin'])){
	header('location:'.$admin.'/signin.php');
}
else if(isset($_GET['teacher'])){
	header('location:teacher/signin.php');
}
else if(isset($_GET['parent'])){
	header('location:parent/signin.php');
}
else if(isset($_GET['archive'])){
	header('location:archive/signin.php');
}
else if(isset($_GET['tst'])){
	header('location:tst/signin.php');
}
else if(isset($_GET['eet'])){
	header('location:test/signin.php');
}
// header("location:signin.php");
?>