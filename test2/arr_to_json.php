<?php
	$ar1 = [28,0,0,0];
	$ar2 = ['first','second','third','fourth'];
	$ar3 = ['20%','22%','23%','24%'];
	$result = array();
	if (count($ar1) == count($ar2) && count($ar2) == count($ar3)) {
		for ($i=0; $i<count($ar1); $i++) { 
			array_push($result, array(
										'point' => $ar1[$i],
										'name' => $ar2[$i],
										'percent' => $ar3[$i]
									));
		}
	}
	print_r($result);
	echo "<br>";
	echo "<br>";
	echo json_encode($result);
?>