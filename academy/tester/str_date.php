<?php
	date_default_timezone_set("Asia/Almaty");
	$date1 = date('Y-m-d H:i:s');
	$date2 = date('Y-m-d H:i:s', strtotime('2019-08-12 12:39:43'));
	echo $date1;
	echo "<br>";
	echo $date2;
	echo "<br>";
	echo $date1 > $date2;

	echo "<br><br><br>";
	$date = '21.10.2019';
	echo $date."<br>";
	$date = date('Y-m-d', strtotime($date));
	echo $date."<br>";
?>