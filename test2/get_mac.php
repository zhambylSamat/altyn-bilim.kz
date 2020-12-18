<?php
	include('../connection.php');
	echo "mac<br>";
	echo "mac: ".exec('getmac');
	$_SESSION['mac'] = exec('getmac');
	echo "<br>";
	echo "session mac: ".$_SESSION['mac'];
?>