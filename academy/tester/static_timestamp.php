<?php
	$cdate = explode('-', date('Y-m-d'));
	print_r($cdate);
	echo "<br>";
	$day = intval($cdate[2]);
	$month = intval($cdate[1]);
	$year = intval($cdate[0]);
	echo $day.".".$month.".".$year."<br>";
	$d = mktime(7, 0, 0, $month, $day, $year);
	echo $d."<br>";
	$created_date = date('Y-m-d H:i:s', $d);
	echo $created_date."<br>";
?>