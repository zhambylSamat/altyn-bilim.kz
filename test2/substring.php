<?php
	$txt = 'Аралық бақылау: aefsdfas';
	echo substr($txt, 0,28);
	echo "<br>";
	echo substr_replace($txt, "Аралық бақылау:", 0,5);
	echo "<br>";
	echo upper($txt);
?>