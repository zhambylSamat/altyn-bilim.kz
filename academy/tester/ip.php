<?php
	echo $_SERVER['REMOTE_ADDR'];
	echo "<br>";
	echo $_SERVER['HTTP_X_FORWARDED_FOR'];
	echo "<br>";
	echo $_SERVER['HTTP_USER_AGENT'];
	echo "<br>";
	echo $_SERVER['HTTP_CLIENT_IP'];
?>