<?php
include_once('../connection.php');
if(isset($_POST['signIn'])){
	try {
		$phone = $_POST['phone'];
		// if($phone=='7059009356' || $phone=='7475665750' || $phone=='7471507048' || $phone=='7081253002'){
			// $_SESSION['tst_number'] = $phone;
		// }
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
?>