<?php
	include_once('../common/connection_datas.php');
	mb_internal_encoding("UTF-8");
	date_default_timezone_set("Asia/Almaty");
	$connect = mysqli_connect($servername, $username, $password, $dbname);
	if (!$connect) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_query($connect,'SET CHARACTER SET utf8'); 
	mysqli_select_db($connect,$dbname);
?>