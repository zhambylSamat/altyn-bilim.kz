<?php
	function process($c, $d = 25){
		echo $c."<br>";
		echo $d."<br>";
		global $e;
		echo $e."<br>";
		$retval = $c + $d - $_GET['c'] - $e;
		return $retval;
	}
	$e = 10;
	echo process(5);
?>