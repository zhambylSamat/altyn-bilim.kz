<?php

include_once('../connection.php');
if(isset($_POST[''])){
	try {
		
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}

?>