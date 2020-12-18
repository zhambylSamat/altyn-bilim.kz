<?php
	session_start();

	echo $_SESSION['user_id'];
	echo "<br>";
	echo $_SESSION['user_first_name'];
	echo "<br>";
?>