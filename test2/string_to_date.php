<?php

$dstr = "17.07.2018";

$d = strtotime($dstr);
$d1 = strtotime("16.07.".(date("Y", strtotime("-1 year", $d))));
$d2 = strtotime("15.07.".(date("Y", $d)));

$d0 = date('d.m.Y', $d);
$d01 = date('d.m.Y', strtotime("16.07.".(date("Y", strtotime("-1 year", $d)))));
$d02 = date('d.m.Y', strtotime("15.07.".(date("Y", $d))));

echo "d0 -> ".$d0;
echo "<br><br>";
echo "d01 -> ".$d01;
echo "<br><br>";
echo "d02 -> ".$d02;
echo "<br><br>";
echo "d -> ".$d;
echo "<br><br>";
echo "d1 -> ".$d1;
echo "<br><br>";
echo "d2 -> ".$d2;
echo "<br><br>";
echo date('d.m.Y', change_year($d, "-1 year", "16.07."));
echo "<br><br>";
echo date('d.m.Y', change_year($d, "", "15.07."));
echo "<br><br>";
echo date('d.m.Y', change_year($d, "", "16.07."));
echo "<br><br>";
echo date('d.m.Y', change_year($d, "+1 year", "15.07."));
echo "<br><br>";

if ($d >= change_year($d, "-1 year", "16.07.") && $d < change_year($d, "", "15.07.")) {
	echo strtodate($d, "-1 year")."-".strtodate($d, "");	
} else if ($d >= change_year($d, "", "16.07.") && $d < change_year($d, "+1 year", "15.07.")) { 
	echo strtodate($d, "")."-".strtodate($d, "+1 year");
}

function change_year($date, $pattern, $day_month) {
	if ($pattern == "") {
		return strtotime($day_month.(date("Y", $date)));
	} 
	return strtotime($day_month.(date("Y", strtotime($pattern, $date))));
}
function strtodate($date, $pattern) {
	if ($pattern == "") {
		return date("Y", $date);
	}
	return date("Y", strtotime($pattern, $date));
}


// $d1 = strtotime("05.05.2018");
// $d2 = date("Y", strtotime("-1 year", $d1));
// $d3 = "16.07.".$d2;
// echo $d3."<br><br>";
// $d4 = date("d.m.Y", strtotime($d3));
// echo $d4."<br><br>";

// echo date("d.m.Y", $d1);
// echo "<br><br>";
// echo date("d.m.Y", $d2);
// echo "<br>";

// if ($d1 > $d2) {
// 	echo "okey";
// } else {
// 	echo "not okey";
// }
?>