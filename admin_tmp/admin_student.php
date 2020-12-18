<?php 
	include('../connection.php'); 
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'student';
	}
?>
