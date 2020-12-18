<?php
	// $servername = "srv-pleskdb21.ps.kz:3306";
	// $username = "altyn_bilim";
	// $password = "glkR283*";
	// $name = "altynbil_db";
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	// $password = "zhambylsamat";
	$dbname = "altyn_bilim";

	$conn = mysqli_connect($servername, $username, $password, $name);
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	mysqli_query($conn,'SET CHARACTER SET utf8'); 
	mysqli_select_db($conn,$name);
?>