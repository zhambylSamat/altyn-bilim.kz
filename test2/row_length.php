<?php
	include_once('../connection.php');
	$stmt = $conn->prepare("SELECT student_num FROM student");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$count = 0;
	echo time()."<br>";
	$uniqId = time().'-'.mt_rand();
	echo $uniqId."<br>";
	echo strlen($uniqId)."<br>";
	echo strlen(time())."<br>";
	foreach ($result as $key => $value) {
		echo (++$count).") ".strlen($value['student_num'])."<br>";
	}
?>