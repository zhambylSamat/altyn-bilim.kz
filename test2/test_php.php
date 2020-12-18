<?php
	include_once('../connection.php');
	// $username = $_POST['username'];
	// $password = $_POST['pass'];
	// $stmt = $mysqli->query("SELECT * FROM username = '".$username."' AND "."password = '".$password."'");
	// $stmt->execute();
	// $result = $stmt->num_rows();
	// echo $result;
	// foreach($result as $value){
	// 	echo $value['username'];
	// 	echo "<br>";
	// 	echo $value['password'];
	// 	echo "<br>";
	// 	echo "OKKKK";
	// 	echo "<br>";
	// }

	$stmt = $conn->prepare("SELECT AVG(mark) as a FROM trial_test_mark WHERE trial_test_mark_num IN ('')");
	$stmt->execute();
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	print_r($res);
	echo $res['a']==null ? "okey" : "not okey";
?>