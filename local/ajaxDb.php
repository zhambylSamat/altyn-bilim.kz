<?php
$includeBool = false;
$error = array();
$data = array();
$data['script'] = "";
$data['text'] = "";
$data['success'] = false;
$data['error'] = '';
$question_img = '';


if(isset($_GET[md5(md5('notified'))])){
	try {
		include_once('../connection.php');
		
		$object_num = $_POST['notification_id'];
		$stmt = $conn->prepare("UPDATE chocolate SET notified = 1 WHERE object_num = ?");
		for ($i=0; $i < count($object_num); $i++) {
			$data['text'] .= $object_num[$i];
			$stmt->execute(array($object_num[$i]));
		}

	    $data['success'] = true;
	    $_SESSION['notification_news_show']='false';
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	$data['text'] .= '';
	echo json_encode($data);
} else if (isset($_GET['setEntInfo'])) {
	try {
		include_once("../connection.php");

		$phone = $_POST['phone2'];
		$tzk = $_POST['tzk'];
		$iin = $_POST['iin'];
		$potok = $_POST['potok'];
		$student_num = $_POST['student_num'];
		$er_id = $_POST['id'];

		if ($er_id == '') {
			$stmt = $conn->prepare("INSERT INTO ent_result (student_num, phone, tzk, iin, potok) VALUES (:student_num, :phone, :tzk, :iin, :potok)");
			$stmt->bindParam(":student_num", $student_num, PDO::PARAM_STR);
			$stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindParam(":iin", $iin, PDO::PARAM_STR);
			$stmt->bindParam(":tzk", $tzk, PDO::PARAM_STR);
			$stmt->bindParam(":potok", $potok, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $conn->prepare("UPDATE student SET class = 11 WHERE student_num = :student_num");
			$stmt->bindParam(":student_num", $student_num, PDO::PARAM_STR);
			$stmt->execute();
		} else {
			$stmt = $conn->prepare("UPDATE ent_result SET tzk = :tzk, iin = :iin, potok = :potok, phone = :phone WHERE id = :id");
			$stmt->bindParam(":tzk", $tzk, PDO::PARAM_STR);
			$stmt->bindParam(":iin", $iin, PDO::PARAM_STR);
			$stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindParam(":id", $er_id, PDO::PARAM_INT);
			$stmt->bindParam(":potok", $potok, PDO::PARAM_INT);
			$stmt->execute();
		}
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['phone'] = $_POST['phone2'];
		$data['phone_length'] = strlen($_POST['phone2']);
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	$data['text'] .= '';
	echo json_encode($data);
}
?>