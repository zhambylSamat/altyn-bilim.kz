<?php
include_once('../connection.php');
if(isset($_POST['signIn'])){
	try {
		$stmt = $conn->prepare("SELECT entrance_code, student_name, student_surname, result_json, finish FROM entrance_examination_student WHERE entrance_code = :code AND id = :id ");
		$stmt->bindParam(':code', $code, PDO::PARAM_INT);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$code = substr($_POST['code'], 0, 2);
		$id = substr($_POST['code'], 2);
		$stmt->execute();
	   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	   	$c = $stmt->rowCount();
	   	if($c==1){
	   		$_SESSION['finish'] = $result['finish']==1 ? true : false;
	   		$_SESSION['test_result'] = $result['result_json'] != "" ? json_decode($result['result_json'], true) : "";
	   		$_SESSION['ees_name'] = $result['student_name'];
	   		$_SESSION['ees_surname'] = $result['student_surname'];
	   		$_SESSION['ees_code'] = $code;
	   		$_SESSION['ees_id'] = $id;
	   		// print_r($result);
	   		// print_r($_SESSION);
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