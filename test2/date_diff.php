<?php
	$date = new DateTime('2017-11-18');
	$date2 = new DateTime('20-12-2019');
	echo "---------------------<br>";
	print_r($date);
	echo "<br>";
	print_r($date2);
	echo "<br>";
	echo $date2->format('Y-m-d');
	echo "<br>";
	echo $date->format('Y-m-d') < (new DateTime())->format('Y-m-d');
	// print_r(new DateTime());
	echo "<br>---------------------<br>";

	$dt = $date->format('Y-m-d');
	$dt2 = $date2->format('Y-m-d');
	$interval = date_diff(date_create($dt2), date_create($dt));
	echo intval($interval->format("%a"));
	echo "<br><br>";



	$current_day_month = date('d-m', strtotime(date('d-m')));
	// $current_date = '20-04-2019';
	$current_date = date('d-m-Y');
	$current_day = 5;
	$start_day = 25;
	$end_day = 10;
	$start_date = "";
	$end_date = "";

	if ($current_day >= $start_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime($current_date))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime($current_date)))));
	} else if ($current_day <= $end_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime('-1 month', strtotime($current_date)))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime($current_date))));
	}

	echo "current_date: ".$current_date."<br>";
	echo "start_date: ".$start_date."<br>";
	echo "end_date: ".$end_date."<br>";
?>