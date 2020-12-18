<?php
	// $servername = "srv-pleskdb21.ps.kz:3306";
	// $username = "altyn_bilim";
	// $password = "glkR283*";
	// $dbname = "altynbil_db";

	$servername = "localhost";
	$username = "root";
	$password = "";
	// $password = "zhambylsamat";
	$dbname = "altyn_bilim";

	try {
		$ab_connect = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$ab_connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$ab_connect->exec("set names utf8");	
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>