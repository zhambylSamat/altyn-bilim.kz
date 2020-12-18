<?php
	
	$arr1 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	$arr2 = array('a', 'b', 'c', 'd', 5, 10, '9');

	$result1 = array();
	$result2 = array();
	foreach ($arr1 as $value) {
		foreach ($arr2 as $val) {
			if ($value == $val) {
				array_push($result1, $val);
				continue 2;
			}
		}
		array_push($result2, $value);
	}

	print_r($result1);
	echo "<br><br>";
	print_r($result2);

?>