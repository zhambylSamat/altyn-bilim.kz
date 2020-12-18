<?php
	date_default_timezone_set("Asia/Almaty");
	echo date('l');
	echo "<br>";
	echo date('l jS \of F Y h:i:s A');
	echo "<br>";
	echo "July 1, 2000 is on a " . date("l", mktime(0, 0, 0, 7, 1, 2000));
	echo "<br>";
	echo date(DATE_RFC822);
	echo "<br>";
	echo date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));
	echo "<br>";
	echo "The time is " . date("h:i:sa");
	echo "<br>";
	echo "The time is " . date("ha");
	echo "<br>";
	echo "The time is " . date("a");
	echo "<br>";
	echo date("Y-m-d h:i:s");
	echo "<br>";
	echo date('Y-m-d h:i:s','1512235801');
	echo "<br><br>";

	$date1 = strtotime('30-07-2019');
	$date2 = strtotime('31-07-2019');
	// echo date('d-m-Y', strtotime('30-07-2019'));
	echo ($date1>$date2) ? "true" : "false";
?>