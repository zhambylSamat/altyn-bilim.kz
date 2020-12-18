<?php
include_once('../connection.php');
if(isset($_POST['signIn']) || isset($_GET['signIn'])){
	try {
		$stmt = $conn->prepare("SELECT p.name, p.surname FROM parent p, student s WHERE p.phone = :phone AND p.parent_order = 1 AND p.student_num = s.student_num AND s.block != 6");
		$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
		$phone = '';
		if (isset($_POST['phone'])) {
			$phone = $_POST['phone'];
		} else if (isset($_GET['phone'])) {
			$phone = $_GET['phone'];
		}
		$stmt->execute();
	   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	   	$c = $stmt->rowCount();
	   	if($c>0){
	   		// $_SESSION['parent_num'] = $result['parent_num'];
	   		$_SESSION['parent_name'] = $result['name'];
	   		$_SESSION['parent_surname'] = $result['surname'];
	   		$_SESSION['parent_phone'] = $phone;
	   		header('location:index.php');
	   	}	
	  	else {
	  		header('location:signin.php');
	  	}
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
?>