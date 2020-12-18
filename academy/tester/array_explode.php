<?php
	$timecode = '1h3m3s';
	$reminder = $timecode;
	$hour = 0;
	$minute = 0;
	$second = 0;

	if (strpos($reminder, 'h')) {
		$split_result = explode('h', $reminder);
		$hour = intval($split_result[0]);
		$reminder = $split_result[1];
	} else {
		$reminder = $timecode;
	}

	if (strpos($reminder, 'm')) {
		$split_result = explode('m', $reminder);
		$minute = intval($split_result[0]);
		$reminder = $split_result[1];
	} else {
		$reminder = $timecode;
	}

	if (strpos($reminder, 's')) {
		$split_result = explode('s', $reminder);
		$second = intval($split_result[0]);
		$reminder = $split_result[1];
	} else {
		$reminder = $timecode;
	}

	echo $hour;
	echo "<br>";
	echo $minute;
	echo "<br>";
	echo $second;
	echo "<br>";
?>