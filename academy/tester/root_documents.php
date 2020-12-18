<?php
	echo $_SERVER['DOCUMENT_ROOT'];

	$_SERVER['DOCUMENT_ROOT'] = "abc";
	echo $_SERVER['DOCUMENT_ROOT'];
?>