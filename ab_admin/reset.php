<?php
	if(isset($_GET[md5(md5("resetThisStudent"))])){
		$studentNum = $_POST['reset'];
		$data['success'] = false;
		$data['error'] = '';
		try {
			include_once('../connection.php');
			$stmt = $conn->prepare("UPDATE student SET password_type = 'default', password = :password WHERE student_num = :student_num");
   			$password = md5('12345');
	    	$stmt->bindParam(':student_num', $studentNum, PDO::PARAM_STR);
	    	$stmt->bindParam(':password', $password, PDO::PARAM_STR);
	       
	    	$stmt->execute();

	    	$data['success'] = true;
		} catch(PDOException $e) {
        	$data['success'] = false;
        	$data['error'] = "Error: " . $e->getMessage();
    	}
    	echo json_encode($data);
	}
	if(isset($_GET[md5(md5("resetThisTeacher"))])){
		$teacherNum = $_POST['reset'];
		$data['success'] = false;
		$data['error'] = '';
		try {
			include_once('../connection.php');
			$stmt = $conn->prepare("UPDATE teacher SET password_type = 'default', password = :password WHERE teacher_num = :teacher_num");
   
	    	$stmt->bindParam(':teacher_num', $teacherNum, PDO::PARAM_STR);
	    	$stmt->bindParam(':password', $password, PDO::PARAM_STR);

	    	$password = md5('123456');
	       
	    	$stmt->execute();

	    	$data['success'] = true;
		} catch(PDOException $e) {
        	$data['success'] = false;
        	$data['error'] = "Error: " . $e->getMessage();
    	}
    	echo json_encode($data);
	}
?>