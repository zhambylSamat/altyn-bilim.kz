<?php
	if (isset($_GET) && $_GET['u']) {
		echo md5($_GET['u']);
	}
	if (isset($_GET) && $_GET['id']) {
		echo uniqid($_GET['id'], true);
	}
?>