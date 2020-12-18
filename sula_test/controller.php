<?php

	if (isset($_POST['sbm1'])) {
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		echo 'ok';
	} 
	else if (isset($_POST['sbm2'])) {
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		echo 'no';
	} 

	// $connection -> insert into user ()

	// header("Location:index.php");
?>