<?php

include_once '../send_sms/index.php';

$includeBool = false;
$error = array();
$data = array();
$data['script'] = "";
$data['text'] = "";
$data['success'] = false;
$data['error'] = '';
$question_img = '';


if(isset($_GET[md5(md5('new_question'))])){
	if((isset($_POST['question-txt']) && $_POST['question-txt']!= '') ||  (isset($_FILES['question-img']) && $_FILES['question-img']['name']!='') || (isset($_POST['question-img-hidden']) && $_POST['question-img-hidden']!='')){
		$data['script'] .= "<script type='text/javascript'> window['notEmptyQuestion'](); </script>";
		$photo_path = '../img/test/';
		$photo_path = $photo_path.basename($_FILES['question-img']['name']);
		$photo_torf = true;
		if($_FILES['question-img']['name']!=''){
			$question_img = $_FILES['question-img']['name'];
			$photo_torf = checkFile($photo_path, $_FILES['question-img']['name'], $_FILES['question-img']['tmp_name'], $_FILES['question-img']['size']);
		}
		else if(isset($_POST['question-img-hidden'])){
			$question_img = $_POST['question-img-hidden'];
		}
		if($photo_torf){
			if(isset($_POST['torf'])){
				$data['script'] .= "<script type='text/javascript'> window['notEmptyCheckbox'](); </script>";
				addToDB();
			}
			else{
				$data['script'] .= "<script type='text/javascript'> emptyCheckbox.call(); </script>";
				echo json_encode($data);
			}
		}
		else{
			echo json_encode($data);
		}
	}
	else{
		$data['script'] .= "<script type='text/javascript'> emptyQuestion.call(); </script>";
		echo json_encode($data);
	}	
}
else if(isset($_POST['delete_question']) && $_POST['delete_question'] = 'delete_question' && isset($_POST['question_num'])){
	deleteQuestion($_POST['question_num']);
	deleteAnswer($_POST['question_num']);
	// $obj = requireToVar('ajax_adminTest.php');
	$data['text'] = '';
	$data['success']=true;
	echo json_encode($data);

}
else if(isset($_GET[md5(md5('addNewStudent'))])){
	try {
		include_once('../connection.php');

		$student_num 	= uniqid('US', true)."_".time();
		$name 			= $_POST['student_name'];
		$surname 		= $_POST['student_surname'];
		$phone 			= $_POST['student_phone'];
		$username 		= $_POST['student_username'];
		$password 		= md5('12345');
		$dob 			= $_POST['student_dob'];
		$school 		= str_replace(' ', '_', $_POST['student_school']);
		$class 			= $_POST['student_class'];
		$home_phone 	= $_POST['home_phone'];
		$address 		= $_POST['home_address'];
		$target_subject = $_POST['target_subject'];
		$target_from 	= $_POST['target_from'];
		$instagram		= $_POST['instagram'];
		$altyn_belgi	= isset($_POST['student_altyn_belgi']) && $_POST['student_altyn_belgi']=="on" ? 1 : 0;
		$red			= isset($_POST['student_red']) && $_POST['student_red']=="on" ? 1 : 0;
		$data['altyn_belgi'] = $altyn_belgi;

		// $data['values'] = $student_num." ".$name." ".$surname." ".$phone." ".$username." ".$password." ".$dob." ".$school." ".$class." ".$home_phone." ".$address;

		$parent_num_1 	= uniqid('P', true)."_".time();
		$name_1 		= $_POST['parent_name_1'];
		$surname_1 		= $_POST['parent_surname_1'];
		$phone_1 		= $_POST['parent_phone_1'];

		$parent_num_2 	= uniqid('P', true)."_".time();
		$name_2 		= $_POST['parent_name_2'];
		$surname_2 		= $_POST['parent_surname_2'];
		$phone_2 		= $_POST['parent_phone_2'];


		$stmt = $conn->prepare("INSERT INTO student 
									(student_num, name, surname, altyn_belgi, red, phone, username, 
									password, dob, school, class, home_phone, address, password_type,
									target_subject, target_from, instagram) 
								VALUES(:student_num,  :name, :surname, :altyn_belgi, :red, :phone, :username, 
									:password, :dob, :school, :class, :home_phone, :address, 'default',
									:target_subject, :target_from, :instagram)");

		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
	    $stmt->bindParam(':altyn_belgi', $altyn_belgi, PDO::PARAM_INT);
	    $stmt->bindParam(':red', $red, PDO::PARAM_INT);
	    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
	    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
	    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
	    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
	    $stmt->bindParam(':school', $school, PDO::PARAM_STR);
	    $stmt->bindParam(':class', $class, PDO::PARAM_STR);
	    $stmt->bindParam(':home_phone', $home_phone, PDO::PARAM_INT);
	    $stmt->bindParam(':address', $address, PDO::PARAM_INT);
	    $stmt->bindParam(':target_subject', $target_subject, PDO::PARAM_STR);
	    $stmt->bindParam(':target_from', $target_from, PDO::PARAM_STR);
	    $stmt->bindParam(':instagram', $instagram, PDO::PARAM_STR);

	    $stmt->execute();

	    if($phone_1!=''){
		    $stmt = $conn->prepare("INSERT INTO parent (parent_num, name, surname, student_num, phone, parent_order) VALUE(:parent_num, :name, :surname, :student_num, :phone, 1)");
		    $stmt->bindParam(':parent_num', $parent_num_1, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_1, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_1, PDO::PARAM_STR);
		    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_1, PDO::PARAM_STR);
		    $stmt->execute();
		}
		if($phone_2!=''){
		    $stmt = $conn->prepare("INSERT INTO parent (parent_num, name, surname, student_num, phone, parent_order) VALUE(:parent_num, :name, :surname, :student_num, :phone, 2)");
		    $stmt->bindParam(':parent_num', $parent_num_2, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_2, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_2, PDO::PARAM_STR);
		    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_2, PDO::PARAM_STR);
		    $stmt->execute();
		}

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);

}

else if(isset($_GET[md5(md5('editStudent'))])){
	try {
		include_once('../connection.php');

		$student_num 	= $_POST['student_num'];
		$name 			= $_POST['student_name'];
		$surname 		= $_POST['student_surname'];
		$phone 			= $_POST['student_phone'];
		$username 		= $_POST['student_username'];
		$dob 			= $_POST['student_dob'];
		$school 		= str_replace(' ', '_', $_POST['student_school']);
		$class 			= $_POST['student_class'];
		$home_phone 	= $_POST['home_phone'];
		$address 		= $_POST['home_address'];
		$target_subject = $_POST['target_subject'];
		$target_from 	= $_POST['target_from'];
		$instagram 		= $_POST['instagram'];
		$altyn_belgi	= isset($_POST['student_altyn_belgi']) && $_POST['student_altyn_belgi']=="on" ? 1 : 0;
		$red			= isset($_POST['student_red']) && $_POST['student_red']=="on" ? 1 : 0;

		// $data['values'] = $student_num." ".$name." ".$surname." ".$phone." ".$username." ".$password." ".$dob." ".$school." ".$class." ".$home_phone." ".$address;

		$parent_num_1 	= $_POST['parent_num_1'];
		$name_1 		= $_POST['parent_name_1'];
		$surname_1 		= $_POST['parent_surname_1'];
		$phone_1 		= $_POST['parent_phone_1'];

		$parent_num_2 	= $_POST['parent_num_2'];
		$name_2 		= $_POST['parent_name_2'];
		$surname_2 		= $_POST['parent_surname_2'];
		$phone_2 		= $_POST['parent_phone_2'];

		$stmt = $conn->prepare("UPDATE student
								SET name = :name, 
									surname = :surname, 
									altyn_belgi = :altyn_belgi,
									red = :red,
									phone = :phone,
									username = :username,
									dob = :dob,
									school = :school,
									class = :class,
									home_phone = :home_phone,
									address = :address,
									target_subject = :target_subject,
									target_from = :target_from,
									instagram = :instagram
								WHERE student_num = :student_num");

		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
	    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
	    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
	    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
	    $stmt->bindParam(':school', $school, PDO::PARAM_STR);
	    $stmt->bindParam(':class', $class, PDO::PARAM_STR);
	    $stmt->bindParam(':home_phone', $home_phone, PDO::PARAM_STR);
	    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
	    $stmt->bindParam(':altyn_belgi', $altyn_belgi, PDO::PARAM_INT);
	    $stmt->bindParam(':red', $red, PDO::PARAM_INT);
	    $stmt->bindParam(':target_subject', $target_subject, PDO::PARAM_STR);
	    $stmt->bindParam(':target_from', $target_from, PDO::PARAM_STR);
	    $stmt->bindParam(':instagram', $instagram, PDO::PARAM_STR);

	    $stmt->execute();

	    if($phone_1!='' && $parent_num_1 != ''){
		    $stmt = $conn->prepare("UPDATE parent SET name = :name, surname = :surname, phone = :phone WHERE parent_num = :parent_num");
		    $stmt->bindParam(':parent_num', $parent_num_1, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_1, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_1, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_1, PDO::PARAM_STR);
		    $stmt->execute();
		}
		else if($phone_1 != '' && $parent_num_1 == ''){
			$parent_num_1 = uniqid('P', true)."_".time();
			$stmt = $conn->prepare("INSERT INTO parent (parent_num, name, surname, student_num, phone, parent_order) VALUE(:parent_num, :name, :surname, :student_num, :phone, 1)");
		    $stmt->bindParam(':parent_num', $parent_num_1, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_1, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_1, PDO::PARAM_STR);
		    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_1, PDO::PARAM_STR);
		    $stmt->execute();
		}
		else if($phone_1 == '' && $parent_num_1 != ''){
			$stmt = $conn->prepare("DELETE FROM parent WHERE parent_num = :parent_num");
			$stmt->bindParam(':parent_num', $parent_num_1, PDO::PARAM_STR);
			$stmt->execute();
		}
		if($phone_2!='' && $parent_num_2 != ''){
		    $stmt = $conn->prepare("UPDATE parent SET name = :name, surname = :surname, phone = :phone WHERE parent_num = :parent_num");
		    $stmt->bindParam(':parent_num', $parent_num_2, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_2, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_2, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_2, PDO::PARAM_STR);
		    $stmt->execute();
		}
		else if($phone_2 != '' && $parent_num_2 == ''){
			$parent_num_2 = uniqid('P', true)."_".time();
			$stmt = $conn->prepare("INSERT INTO parent (parent_num, name, surname, student_num, phone, parent_order) VALUE(:parent_num, :name, :surname, :student_num, :phone, 2)");
		    $stmt->bindParam(':parent_num', $parent_num_2, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_2, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_2, PDO::PARAM_STR);
		    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_2, PDO::PARAM_STR);
		    $stmt->execute();
		}
		else if($phone_2 == '' && $parent_num_2 != ''){
			$stmt = $conn->prepare("DELETE FROM parent WHERE parent_num = :parent_num");
			$stmt->bindParam(':parent_num', $parent_num_2, PDO::PARAM_STR);
			$stmt->execute();
		}

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);

}

else if(isset($_GET[md5(md5('editStudent'))])){
	try {
		include_once('../connection.php');

		$student_num 	= $_POST['student_num'];
		$name 			= $_POST['student_name'];
		$surname 		= $_POST['student_surname'];
		$phone 			= $_POST['student_phone'];
		$username 		= $_POST['student_username'];
		$dob 			= $_POST['student_dob'];
		$school 		= $_POST['student_school'];
		$class 			= $_POST['student_class'];
		$home_phone 	= $_POST['home_phone'];
		$address 		= $_POST['home_address'];

		$parent_num_1 	= $_POST['parent_num_1'];
		$name_1 		= $_POST['parent_name_1'];
		$surname_1 		= $_POST['parent_surname_1'];
		$phone_1 		= $_POST['parent_phone_1'];

		$parent_num_2 	= $_POST['parent_num_2'];
		$name_2 		= $_POST['parent_name_2'];
		$surname_2 		= $_POST['parent_surname_2'];
		$phone_2 		= $_POST['parent_phone_2'];


		$stmt = $conn->prepare("UPDATE student SET name = :name, surname = :surname, phone = :phone, username = :username, dob = :dob, school = :school, class = :class, home_phone = :home_phone, address = :address WHERE student_num = :student_num");

		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
	    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
	    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
	    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
	    $stmt->bindParam(':school', $school, PDO::PARAM_STR);
	    $stmt->bindParam(':class', $class, PDO::PARAM_STR);
	    $stmt->bindParam(':home_phone', $home_phone, PDO::PARAM_STR);
	    $stmt->bindParam(':address', $address, PDO::PARAM_STR);

	    $stmt->execute();

	    if($phone_1!=''){
		    $stmt = $conn->prepare("UPDATE parent SET name = :name, surname = :surname, phone = :phone WHERE parent_num = :parent_num");
		    $stmt->bindParam(':parent_num', $parent_num_1, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_1, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_1, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_1, PDO::PARAM_STR);
		    $stmt->execute();
		}
		if($phone_2!=''){
		    $stmt = $conn->prepare("UPDATE parent SET name = :name, surname = :surname, phone = :phone WHERE parent_num = :parent_num");
		    $stmt->bindParam(':parent_num', $parent_num_2, PDO::PARAM_STR);
		    $stmt->bindParam(':name', $name_2, PDO::PARAM_STR);
		    $stmt->bindParam(':surname', $surname_2, PDO::PARAM_STR);
		    $stmt->bindParam(':phone', $phone_2, PDO::PARAM_STR);
		    $stmt->execute();
		}

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);

}

else if(isset($_GET[md5(md5('createTeacher'))])){
	try {
		include_once('../connection.php');
		$stmt = $conn->prepare("INSERT INTO teacher (teacher_num, 
													name, 
													surname, 
													password, 
													username,
													dob) 
								VALUES(:teacher_num, 
										:name, 
										:surname, 
										:password, 
										:username,
										:dob)");
   
	    $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
	    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
	    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
	    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);

	    $teacher_num = uniqid('UT', true)."_".time();
	    $name = $_POST['name'];
	    $surname = $_POST['surname'];
	    $username = strtolower($_POST['username']);
	    $password = md5('123456');
	    $dob = $_POST['dob'];
	       
	    $stmt->execute();
	    $data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	// $obj = requireToVar('index_students.php');
	$data['text'] .= '';
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('createGroup'))])){
	try {
		include_once('../connection.php');
		$stmt = $conn->prepare("INSERT INTO group_info (group_info_num, subject_num, teacher_num, group_name) VALUES(:group_info_num, :subject_num, :teacher_num, :group_name)");
   
	    $stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
	    $stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
	    $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	    $stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
	    // $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

	    $group_info_num = uniqid('GI', true)."_".time();
	    $subject_num = $_POST['subject'];
	    $teacher_num = $_POST['teacher'];
	    $group_name = $_POST['group_name'];
	    // $comment = $_POST['comment'];
	       
	    $stmt->execute();
	    $data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	// $obj = requireToVar('index_students.php');
	$data['text'] .= '';
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('set_permission'))])){
	try {
		include_once('../connection.php');

		$stmt = $conn->prepare("SELECT video_num, timer FROM video WHERE subtopic_num = :subtopic_num AND vimeo_link = 'y'");
		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$subtopic_num = $_POST['extra_num'];
		$stmt->execute();
		$video_exist = ($stmt->rowCount()==0) ? false : true;

		if($video_exist){
			$stmt = $conn->prepare("INSERT IGNORE INTO student_permission (student_permission_num, student_num) VALUES(:student_permission_num, :student_num) ");
	 
		    $stmt->bindParam(':student_permission_num', $student_permission_num, PDO::PARAM_STR);
		    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);

		    $student_permission_num = uniqid('S_P', true)."_".time();
		    $student_num = $_POST['data_num'];

		    $stmt->execute();

		    $stmt_check = $conn->prepare("SELECT stp.student_permission_num studentPermissionNum FROM student_test_permission stp, student_permission sp WHERE sp.student_num = :student_num AND stp.student_permission_num = sp.student_permission_num AND stp.subtopic_num = :subtopic_num");

		    $stmt_check->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		    $stmt_check->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);

		    $subtopic_num = $_POST['extra_num'];
		    $video_permission = isset($_POST['video_permission']) ? "t" : "f";
		    $test_permission = isset($_POST['test_permission']) ? "t" : "f";

		    $stmt_check->execute();
	        $result_exists = $stmt_check->rowCount(); 
	        if($result_exists==0){
	        	$stmt2=$conn->prepare("INSERT INTO student_test_permission (student_permission_num, subtopic_num, video_permission, test_permission) SELECT (SELECT student_permission_num FROM student_permission WHERE student_num = :student_num2), :subtopic_num, :video_permission, :test_permission");
	        	$stmt2->bindParam(':student_num2', $student_num, PDO::PARAM_STR);
			    $stmt2->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
			    $stmt2->bindParam(':video_permission', $video_permission, PDO::PARAM_STR);
			    $stmt2->bindParam(':test_permission', $test_permission, PDO::PARAM_STR);
			    $stmt2->execute();
	        }
	        else if($result_exists==1){
	        	$result = $stmt_check->fetch(PDO::FETCH_ASSOC);
	        	$stmt2 = $conn->prepare("UPDATE student_test_permission SET video_permission = :video_permission, test_permission = :test_permission WHERE student_permission_num = :student_permission_num AND subtopic_num = :subtopic_num");
	   
			   	$stmt2->bindParam(':student_permission_num', $result['studentPermissionNum'], PDO::PARAM_STR);
			    $stmt2->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
			    $stmt2->bindParam(':video_permission', $video_permission, PDO::PARAM_STR);
			    $stmt2->bindParam(':test_permission', $test_permission, PDO::PARAM_STR);
			       
			    $stmt2->execute();
	        }
	    }
    	else if(!$video_exist){
    		$data['text'] = "noVideo";
    	}
        $data['success'] = true;
	    // header('location:group.php?data_num='.$_SESSION['tmp_group_info_num']);
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if (isset($_GET[md5(md5('check_timer'))])) {
	try {
		include_once('../connection.php');

		$stmt = $conn->prepare("SELECT video_num, timer FROM video WHERE subtopic_num = :subtopic_num AND vimeo_link = 'y'");
		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$subtopic_num = $_GET['id'];
		$stmt->execute();
		$video_result = $stmt->fetchAll();
		$video_exist = ($stmt->rowCount()==0) ? false : true;

		if($video_exist){
	        $data['timer'] = "";
	        foreach ($video_result as $key => $value) {
	        	$data['timer'] .= $value['timer']."; ";
	        }
	    }
    	else if(!$video_exist){
    		$data['text'] = "noVideo";
    	}
        $data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('schedule'))])){
	try {
		include_once('../connection.php');
		$start_lesson = $_POST['start_hour'].":".$_POST['start_minute'].":00";
		$finish_lesson = $_POST['finish_hour'].":".$_POST['finish_minute'].":00"; 
		$group_info_num = $_POST['data_num'];
		$weeks = $_POST['week_id'];
		$office = $_POST['office'];

		$stmt = $conn->prepare("UPDATE group_info SET start_lesson = :start_lesson, finish_lesson = :finish_lesson, office_number = :office WHERE group_info_num = :group_info_num");
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->bindParam(':office', $office, PDO::PARAM_STR);
		$stmt->bindParam(':start_lesson', $start_lesson, PDO::PARAM_STR);
		$stmt->bindParam(':finish_lesson', $finish_lesson, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM schedule WHERE group_info_num = :group_info_num");
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->execute();

		$query = "INSERT INTO schedule (group_info_num, week_id) VALUES";
	    $qPart = array_fill(0, count($weeks), "(?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($weeks); $i++){
	    	$stmtA->bindValue($j++, $group_info_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $weeks[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();

		$data['success']=true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('submitNews'))])){
	try {
		include_once('../connection.php');

		$news_content = $_POST['news_content'];
		$id = $_POST['id'];
		$last_updated_date = date("Y-m-d ");
		$stmt = $conn->prepare("UPDATE news SET content = :content, last_updated_date = :last_updated_date WHERE type = :type");
		$stmt->bindParam(':type', $id, PDO::PARAM_STR);
		$stmt->bindParam(':content', $news_content, PDO::PARAM_STR);
		$stmt->bindParam(':last_updated_date', $last_updated_date, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('change-attendance-date'))])){
	try {
		include_once('../connection.php');
		$date = date("Y-m-d", strtotime($_POST['to_date']));
		$progress_group_num = $_POST['pgn'];
		$stmt = $conn->prepare("UPDATE progress_group SET created_date = :created_date WHERE progress_group_num = :progress_group_num");
		$stmt->bindParam(':created_date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':progress_group_num', $progress_group_num, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('singelStudentNews'))])){
	try {
		include_once('../connection.php');
		$data['context'] = 'empty';
		$student_num = $_POST['data_num'];
		$content = $_POST['news_context'];
		$last_updated_date = date("Y-m-d ");
		$publish = 1;
		$readed = 0;
		if($content!=''){
			$stmt = $conn->prepare("INSERT INTO news (type, content, last_updated_date, readed, publish) 
										VALUES(:type, :content, :last_updated_date, :readed, :publish)
										ON DUPLICATE KEY UPDATE content = :content, readed = :readed");
			$stmt->bindParam(':type', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':content', $content, PDO::PARAM_STR);
			$stmt->bindParam(':last_updated_date', $last_updated_date, PDO::PARAM_STR);
			$stmt->bindParam(':publish', $publish, PDO::PARAM_INT);
			$stmt->bindParam(':readed', $readed, PDO::PARAM_INT);
			$stmt->execute();
			$data['context'] = 'notEmpty';
		}
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('removeSingelStudentNews'))])){
	try {
		include_once('../connection.php');
		$student_num = $_GET['data_num'];
		$stmt = $conn->prepare("DELETE FROM news WHERE type = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('acceptSuggestions'))])){
	try {
		include_once('../connection.php');
		$sid = $_POST['sid'];
		$stmt = $conn->prepare("UPDATE suggestion SET status = ? WHERE suggestion_id = ?");
		for ($i=0; $i < count($sid); $i++) {
			$stmt->execute(array('1', $sid[$i]));
		}
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('implementSuggestions'))])){
	try {
		include_once('../connection.php');
		$sid = $_POST['sid'];
		$last_changed_date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("UPDATE suggestion SET status = ?, last_changed_date = ? WHERE suggestion_id = ?");
		for ($i=0; $i < count($sid); $i++) {
			$stmt->execute(array('2', $last_changed_date, $sid[$i]));
		}
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('rejectSuggestions'))])){
	try {
		include_once('../connection.php');

		$suggestion_id = $_POST['sid'];

		$query = 'DELETE FROM suggestion WHERE suggestion_id = ';
		$qPart = array_fill(0, count($suggestion_id), "?");
	    $query .= implode(" OR suggestion_id = ",$qPart);
	    $stmt = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($suggestion_id); $i++){
	    	$stmt->bindValue($j++, $suggestion_id[$i], PDO::PARAM_STR);
	    }
	    $stmt->execute();

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('newProblemSolvingFile'))])){
	try {
		// setlocale(LC_ALL, 'en_US.UTF8');
		$subtopic_num = $_POST['sbtn'];
		$document_link = $_FILES['pdf_file']['name'];
		$document_link = merge($document_link,"___".md5($subtopic_num));
		// $current = file_get_contents($document_link);
		// $current .= md5($subtopic_num);
		// file_put_contents($document_link, $current);
		// file_put_contents($document_link, md5($subtopic_num), FILE_APPEND | LOCK_EX);

		$document_dir = $_POST['file_dir'];
		$document_dir = $document_dir.basename($document_link);

		// $document_link = iconv("UTF-8", "cp1251", $document_link);
		$data['text'] = $document_link;
		if(checkPDFFile($document_dir, $document_link, $_FILES['pdf_file']['tmp_name'], $_FILES['pdf_file']['size'])){
			include_once('../connection.php');

			$stmt = $conn->prepare("INSERT INTO problem_solution (document_link, subtopic_num) VALUES(:document_link, :subtopic_num)");
			$stmt->bindParam(':document_link', $document_link, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
			$stmt->execute();

			$data['success'] = true;
		}
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('removeProblemSolvingFile'))])){
	try {
		include_once('../connection.php');

		$problem_solution_id = $_POST['id'];
		$file_name = $_POST['file_name'];
		$file_dir = $_POST['file_dir'];

		if (unlink($file_dir.$file_name)){
			$stmt = $conn->prepare("DELETE FROM problem_solution WHERE problem_solution_id = :problem_solution_id");
			$stmt->bindParam(':problem_solution_id', $problem_solution_id, PDO::PARAM_STR);
			$stmt->execute();

			$data['success'] = true;
		}
		else {
			$data['success'] = false;
			$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Файл не был удален. Попробуйте еще раз'); </script>";
		}
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('openAccess'))])){
	try {
		include_once('../connection.php');
		$student_num = $_POST['sn'];
		$block = $_POST['block'];
		$block_date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("UPDATE student SET block = :block, block_date = :block_date WHERE student_num = :student_num");
		$stmt->bindParam(':block', $block, PDO::PARAM_INT);
		$stmt->bindParam(':block_date', $block_date, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('start-lesson-date'))])){
	try {
		include_once('../connection.php');

		$group_student_num = $_POST['gsNum'];
		$start_date = date("Y-m-d", strtotime($_POST['start_lesson']));
		$stmt = $conn->prepare("UPDATE group_student SET start_date = :start_lesson WHERE group_student_num = :group_student_num");
		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
		$stmt->bindParam(':start_lesson', $start_date, PDO::PARAM_STR);
		$stmt->execute();

		$period = date("Y-m-", strtotime($_POST['start_lesson']))."01";
		$data['group_student_num'] = $group_student_num;
		$stmt = $conn->prepare("UPDATE statistics_student_frequency SET period = :period WHERE group_student_num = :group_student_num AND status = 'in'");
		$stmt->bindParam(":group_student_num", $group_student_num, PDO::PARAM_STR);
		$stmt->bindParam(":period", $period, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('addVimeoVideoLink'))])){
	try {
		include_once('../connection.php');

		$subtopic_num = $_POST['subtopic_num'];
		$video_link = $_POST['vimeo_link'];
		$video_num = uniqid('vV', true)."_".time();
		$video_timer = $_POST['vimeo_timer'];
		$vimeo_link = "y";
		if(substr( $video_link, 0, 8 ) != "https://"){
			$video_link = "https://".$video_link;
		}
		$data['data'] = $subtopic_num." - ".$video_link." - ".$video_num." - ".$vimeo_link;

		$stmt = $conn->prepare("INSERT INTO video (video_num, subtopic_num, video_link, vimeo_link, timer) VALUES(:video_num, :subtopic_num, :video_link, :vimeo_link, :video_timer)");
		$stmt->bindParam(':video_num', $video_num, PDO::PARAM_STR);
		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$stmt->bindParam(':video_link', $video_link, PDO::PARAM_STR);
		$stmt->bindParam(':vimeo_link', $vimeo_link, PDO::PARAM_STR);
		$stmt->bindParam(':video_timer', $video_timer, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}

else if(isset($_GET[md5(md5('removeVimeoVideoLink'))])){
	try {
		include_once('../connection.php');

		$video_num = $_POST['video_num'];

		$stmt = $conn->prepare("DELETE FROM video WHERE video_num = :video_num");
		$stmt->bindParam(':video_num', $video_num, PDO::PARAM_STR);
		$stmt->execute();
		$data['video_num'] = $video_num;
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}else if(isset($_GET[md5(md5('add-new-suggestion'))])){
	try {
		include_once('../connection.php');

		$text = $_POST['suggestion_text'];
		$status = 0;
		$last_changed_date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("INSERT INTO suggestion (user_num, text, status, last_changed_date) VALUES(:user_num, :text, :status, :last_changed_date)");
		$stmt->bindParam(':user_num', $_SESSION['adminNum'], PDO::PARAM_STR);
		$stmt->bindParam(':text', $text, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_INT);
		$stmt->bindParam(':last_changed_date', $last_changed_date, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);

}else if(isset($_GET[md5(md5('edit-suggestion'))])){
	try {
		include_once('../connection.php');

		$text = $_POST['suggestion_text'];
		$last_changed_date = date("Y-m-d H:i:s");
		$suggestion_id = $_POST['sid'];
		$stmt = $conn->prepare("UPDATE suggestion SET text = :text, last_changed_date = :last_changed_date WHERE suggestion_id = :suggestion_id");
		$stmt->bindParam(':text', $text, PDO::PARAM_STR);
		$stmt->bindParam(':last_changed_date', $last_changed_date, PDO::PARAM_STR);
		$stmt->bindParam(':suggestion_id', $suggestion_id, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);

}else if(isset($_GET[md5(md5('remove-suggestion'))])){
	try {
		include_once('../connection.php');

		$suggestion_id = $_GET['sid'];
		$stmt = $conn->prepare("DELETE FROM suggestion WHERE suggestion_id = :suggestion_id");
		$stmt->bindParam(':suggestion_id', $suggestion_id, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}else if(isset($_GET[md5(md5('studentUsername'))])){
	try {
		include_once('../connection.php');

		$value = $_GET["value"];
		$stmt = $conn->prepare("SELECT 1 FROM student WHERE username = :username");
		$stmt->bindParam(':username', $value, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['count'] = $stmt->rowCount();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}else if(isset($_GET[md5(md5('studentPhone'))])){
	try {
		include_once('../connection.php');

		$value = $_GET["value"];
		$stmt = $conn->prepare("SELECT 1 FROM student WHERE phone = :phone");
		$stmt->bindParam(':phone', $value, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['count'] = $stmt->rowCount();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}else if(isset($_GET[md5(md5('parentPhone'))])){
	try {
		include_once('../connection.php');

		$value = $_GET["value"];
		$stmt = $conn->prepare("SELECT p.parent_num,
									p.name parent_name, 
								    p.surname parent_surname,
								    s.student_num,
									s.name student_name, 
									s.surname student_surname
								FROM parent p,
									student s
								WHERE p.phone = :phone
									AND p.student_num = s.student_num
								ORDER BY p.surname, 
										p.name, 
										s.surname, 
										s.name ASC");
		$stmt->bindParam(':phone', $value, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['data'] = $stmt->fetchAll();
		$data['count'] = $stmt->rowCount();
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('toArchive'))])){
	try {

		include_once("../connection.php");

		$data_num = $_GET['data_num'];
		$data_name = $_GET['data_name'];
		$curr_date = date("Y-m-d ");

		$query = "";

		if($data_name=='student'){
			$query = "UPDATE student SET block = 6, block_date = :block_date WHERE student_num = :student_num";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':student_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ":student_num"."  :  ".$data_num;
			writeToLog($query, $values, "1. archive student");

			$query = "SELECT csf.id, 
						csf.student_num, 
						sj.subject_name
					FROM course_started_flag csf,
						subject sj
					WHERE csf.in_progress = 1
						AND csf.student_num = :student_num
						AND sj.subject_num = csf.subject_num
						AND csf.subject_num IN (SELECT gi2.subject_num 
												FROM group_info gi2,
													group_student gs2 
												WHERE gs2.student_num = csf.student_num
													AND gi2.group_info_num = gs2.group_info_num)";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(":student_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$csf_query_result = $stmt->fetchAll();
			$data['csf_query_result'] = $csf_query_result;
			$stmt = $conn->prepare("UPDATE course_started_flag SET in_progress = 0 WHERE id = ?");
			$sms_data = array();
			for ($i=0; $i<count($csf_query_result); $i++) { 
				$stmt->execute(array($csf_query_result[$i]['id']));
				if (!isset($sms_data[$csf_query_result[$i]['student_num']]) || !is_array($sms_data[$csf_query_result[$i]['student_num']]['subject_name'])) {
					$sms_data[$csf_query_result[$i]['student_num']]['subject_name'] = array();
				}
				array_push($sms_data[$csf_query_result[$i]['student_num']]['subject_name'], $csf_query_result[$i]['subject_name']);
			}

			$tmp_sql_result = array();
			$stmt = $conn->prepare("SELECT gi.subject_num,
										gs.group_student_num
									FROM group_info gi,
										group_student gs
									WHERE gs.block != 6
										AND gs.student_num = :student_num
										AND gi.group_info_num = gs.group_info_num");
			$stmt->bindParam(':student_num', $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$tmp_sql_result = $stmt->fetchAll();
			if (count($tmp_sql_result) != 0) {
				$period = date("Y-m-").'01';
				$query = "INSERT INTO statistics_student_frequency (student_num, subject_num, group_student_num,  period, status) VALUES ";
				foreach ($tmp_sql_result as $key => $value) {
					$subject_num = $value['subject_num'];
					$group_student_num = $value['group_student_num'];
					$query .= " ('$data_num', '$subject_num', '$group_student_num', '$period', 'out'),";
				}
				$query = rtrim($query,",");
				$stmt = $conn->prepare($query);
				$stmt->execute();
			}


			$query = 'UPDATE group_student SET block = 6, block_date = :block_date WHERE student_num = :student_num AND block != 6';
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':student_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ':student_num'."  :  ".$data_num;
			writeToLog($query, $values, "2. archive group student");
			if (count($sms_data) > 0) {
				$data['sms'] = send_sms_for_finishing_course($conn, $sms_data);
			}
		}
		else if($data_name=='teacher'){
			$query = "UPDATE teacher SET block = 6, block_date = :block_date WHERE teacher_num = :teacher_num";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':teacher_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ':teacher_num'."  :  ".$data_num;
			$values .= "\n:block_date"."  :  ".$curr_date."\n";
			writeToLog($query, $values, "1. archive teacher");
		}
		else if($data_name=='student_group'){

			$query = "UPDATE group_student SET block = 6, block_date = :block_date WHERE group_student_num = :group_student_num";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':group_student_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ":group_student_num"."  :  ".$data_num;
			$values .= "\n:block_date"."  :  ".$curr_date."\n";
			writeToLog($query, $values, "1. archive group_student");

			$stmt = $conn->prepare("SELECT gs.student_num, 
										gi.subject_num
									FROM group_info gi,
										group_student gs
									WHERE gs.group_student_num = :group_student_num
										AND gi.group_info_num = gs.group_info_num");
			$stmt->bindParam(":group_student_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$tmp_sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$period = date("Y-m-").'01';
			$student_num = $tmp_sql_result['student_num'];
			$subject_num = $tmp_sql_result['subject_num'];
			$stmt = $conn->prepare("INSERT INTO statistics_student_frequency (student_num, subject_num, group_student_num, status, period) VALUES ('$student_num', '$subject_num', '$data_num', 'out', '$period')");
			$stmt->execute();


			$stmt = $conn->prepare("SELECT count(gs.student_num) as c
									FROM group_student gs 
									WHERE gs.student_num = (SELECT gs2.student_num 
															FROM group_student gs2 
															WHERE gs2.group_student_num = :group_student_num)
										AND gs.block != 6");
			$stmt->bindParam(':group_student_num', $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
			$data['count'] = $count;
			$data['data_num'] = $data_num;
			if ($count==0) {
				$query = "UPDATE student 
										SET block = 6 
										WHERE student_num = (SELECT gs.student_num
															FROM group_student gs
															WHERE gs.group_student_num = :group_student_num)";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':group_student_num', $data_num, PDO::PARAM_STR);
				$stmt->execute();
				$values = ":group_student_num"."  :  ".$data_num;
				writeToLog($query, $values, "2. archive student if no group_student_num in this student");
			}

			$stmt = $conn->prepare("SELECT csf.id, 
										csf.student_num, 
										sj.subject_name,
										(SELECT count(gi2.group_info_num)
									    FROM group_info gi2,
									    	group_student gs2
									    WHERE gs2.student_num = csf.student_num
									     	AND gs2.block != 6
									    	AND gi2.group_info_num = gs2.group_info_num
									    	AND gi2.block != 6
									    	AND gi2.subject_num = csf.subject_num) as c
									FROM course_started_flag csf,
										subject sj
									WHERE csf.in_progress = 1
										AND csf.student_num = (SELECT gs2.student_num 
															FROM group_student gs2 
															WHERE gs2.group_student_num = :group_student_num)
										AND sj.subject_num = csf.subject_num
										AND csf.subject_num = (SELECT gi2.subject_num 
																FROM group_info gi2,
																	group_student gs2
																WHERE gs2.group_student_num = :group_student_num
																	AND gi2.group_info_num = gs2.group_info_num)");
			$stmt->bindParam(":group_student_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$tmp_res = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($tmp_res != "" && $tmp_res['c'] == 0) {
				$stmt = $conn->prepare("UPDATE course_started_flag SET in_progress = 0 WHERE id = :id");
				$stmt->bindParam(":id", $tmp_res['id'], PDO::PARAM_INT);
				$stmt->execute();

				$sms_data = array($tmp_res['student_num'] => array("subject_name" => array($tmp_res['subject_name'])));
				$data['sms'] = send_sms_for_finishing_course($conn, $sms_data);
			}

		}
		else if($data_name=='group'){

			$query = "SELECT csf.id,
							csf.student_num,
							sj.subject_name,
							(SELECT count(gi2.group_info_num)-1
							FROM group_info gi2,
								group_student gs2
							WHERE gs2.student_num = csf.student_num
								AND gi2.group_info_num = gs2.group_info_num
								AND gs2.block != 6
								AND gi2.block != 6
								AND gi2.subject_num = csf.subject_num) as c
						FROM course_started_flag csf,
							subject sj
						WHERE csf.student_num IN (SELECT gs2.student_num 
												FROM group_student gs2,
													group_info gi2
												WHERE gi2.group_info_num = :group_info_num
													AND gs2.group_info_num = gi2.group_info_num
													AND gs2.block != 6)
							AND csf.subject_num = (SELECT gi2.subject_num
												FROM group_info gi2
												WHERE gi2.group_info_num = :group_info_num)
							AND sj.subject_num = csf.subject_num
							AND csf.in_progress = 1";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(":group_info_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$csf_query_result = $stmt->fetchAll();

			$sms_data = array();
			$stmt = $conn->prepare("UPDATE course_started_flag SET in_progress = 0 WHERE id = ?");
			for ($i=0; $i<count($csf_query_result); $i++) {
				if ($csf_query_result[$i]['c'] == 0) {
					$stmt->execute(array($csf_query_result[$i]['id']));
					$sms_data[$csf_query_result[$i]['student_num']]['subject_name'] = array($csf_query_result[$i]['subject_name']);
				}
			}

			$stmt = $conn->prepare("SELECT gs.student_num,
										gi.subject_num,
										gs.group_student_num
									FROM group_info gi,
										group_student gs
									WHERE gi.group_info_num = :group_info_num
										AND gs.group_info_num = gi.group_info_num
										AND gs.block != 6");
			$stmt->bindParam(":group_info_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$tmp_sql_result = $stmt->fetchAll();

			if (count($tmp_sql_result) != 0) {
				$period = date("Y-m-").'01';
				$query = "INSERT INTO statistics_student_frequency (student_num, subject_num, group_student_num,  period, status) VALUES ";
				foreach ($tmp_sql_result as $key => $value) {
					$subject_num = $value['subject_num'];
					$student_num = $value['student_num'];
					$group_student_num = $value['group_student_num'];
					$query .= " ('$student_num', '$subject_num', '$group_student_num', '$period', 'out'),";
				}
				$query = rtrim($query,",");
				$stmt = $conn->prepare($query);
				$stmt->execute();
			}

			$query = "UPDATE group_info SET block = 6 , block_date = :block_date WHERE group_info_num = :group_info_num";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':group_info_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ":group_info_num"."  :  ".$data_num;
			$values .= "\n:block_date"."  :  ".$curr_date."\n";
			writeToLog($query, $values, "1. archive group");


			$query = "UPDATE group_student SET block = 6, block_date = :block_date WHERE group_info_num = :group_info_num AND block != 6";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(':group_info_num', $data_num, PDO::PARAM_STR);
			$stmt->bindParam(':block_date', $curr_date, PDO::PARAM_STR);
			$stmt->execute();
			$values = ":group_info_num"."  :  ".$data_num;
			$values .= "\n:block_date"."  :  ".$curr_date."\n";
			writeToLog($query, $values, "2. archive all student in this group");

			$data['sms'] = send_sms_for_finishing_course($conn, $sms_data);
		}

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('checkGroupActivation'))])){
	try {
		include_once("../connection.php");

		$data_num = $_GET['data_num'];
		$data_name = $_GET['data_name'];
		if($data_name == 'student'){
			$stmt = $conn->prepare("SELECT count(gs.group_info_num) as c
									FROM group_student gs
									WHERE gs.student_num = :student_num
										AND gs.start_date >= CURRENT_DATE");
			$stmt->bindParam(':student_num', $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$data['count'] = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
		}
		else if($data_name == 'group'){
			$stmt = $conn->prepare("SELECT count(gs.group_student_num) as c
									FROM group_student gs
									WHERE gs.group_info_num = :group_info_num
										AND gs.start_date >= CURRENT_DATE");
			$stmt->bindParam(':group_info_num', $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$data['count'] = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
		}
		else if($data_name == 'teacher'){
			$stmt = $conn->prepare("SELECT count(gi.group_info_num) as c
									FROM group_info gi
									WHERE gi.teacher_num = :teacher_num
										AND gi.block = 0;");
			$stmt->bindParam(":teacher_num", $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$data['count'] = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
		}

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET['delete_test'])){
	$test_num = $_GET['data_num'];
	try {
		include_once("../connection.php");

		$stmt = $conn->prepare("DELETE FROM entrance_examination WHERE test_num = :test_num");
		$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM question WHERE test_num = :test_num");
		$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM test WHERE test_num = :test_num");
		$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}
else if(isset($_GET['delete_pocket'])){
	$pocket_id = $_GET['data_num'];
	try {
		include_once("../connection.php");
		$stmt = $conn->prepare("DELETE FROM entrance_examination WHERE eep_id = :eep_id");
		$stmt->bindParam(':eep_id', $pocket_id, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM entrance_examination_pocket WHERE id = :eep_id");
		$stmt->bindParam(':eep_id', $pocket_id, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}
else if(isset($_GET['delete_pocket_test'])){
	$id = $_GET['data_num'];
	try {
		include_once("../connection.php");

		$data['id'] = $id;
		if ($eetc_row_count>0){
			$conf_arr = array();
			$test_num = $eetc_result[0]['tm'];
			$eep_id = $eetc_result[0]['eep_id'];
			foreach ($eetc_result as $value) {
				if ($value['test_num'] != $test_num) {
					array_push($conf_arr, $value['test_num']);	
				}
			}
		}

		$stmt = $conn->prepare("DELETE FROM entrance_examination WHERE id = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}
else if(isset($_GET[md5(md5('add_to_pocket'))])){
	$pocket_name = $_POST['pocket_name'];
	$test_num_list = isset($_POST['test_num_list']) ? $_POST['test_num_list'] : array();
	$pocket_num = $_POST['pocket_num'];

	try {
		include_once("../connection.php");
		if($pocket_num == 'new'){
			$stmt = $conn->prepare("INSERT INTO entrance_examination_pocket (name) VALUES (:name);");
			$stmt->bindParam(':name', $pocket_name, PDO::PARAM_STR);
			$stmt->execute();
			$pocket_num = $conn->lastInsertId();
		}
		else{
			$stmt = $conn->prepare("UPDATE entrance_examination_pocket SET name = :name WHeRE id = :id");
			$stmt->bindParam(':name', $pocket_name, PDO::PARAM_STR);
			$stmt->bindParam(':id', $pocket_num, PDO::PARAM_INT);
			$stmt->execute();
		}

		if(count($test_num_list) > 0) {
			$query = "INSERT INTO entrance_examination (eep_id, test_num) VALUES ";
			$qPart = array_fill(0, count($test_num_list), "(?, ?)");
			$query .= implode(',', $qPart);
			$stmt = $conn->prepare($query);
			$j = 1;
			for ($i=0; $i < count($test_num_list); $i++) { 
				$stmt->bindValue($j++, $pocket_num, PDO::PARAM_INT);
				$stmt->bindValue($j++, $test_num_list[$i], PDO::PARAM_STR);
			}
			$stmt->execute();
		}

		$data['success'] = true;
		$data['pocket_num'] = $pocket_num;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}
else if(isset($_GET[md5(md5('creat_student_ee'))])){
	$name = $_POST['name'];
	$surname = $_POST['surname'];
	$test_pocket = $_POST['test_pocket'];
	$code = '';
	for($i = 0; $i < 2; $i++) {
        $code .= mt_rand(1, 9);
    }
	try {
		include_once("../connection.php");

		$stmt = $conn->prepare("INSERT INTO entrance_examination_student (eep_id, student_name, student_surname, entrance_code) VALUE(:eep_id, :name, :surname, :code)");
		$stmt->bindParam(':eep_id', $test_pocket, PDO::PARAM_INT);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
		$stmt->bindParam(':code', $code, PDO::PARAM_INT);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}
else if(isset($_GET[md5(md5('delete_student_ee'))])){
	$id = $_GET['data_num'];
	try {
		include_once("../connection.php");

		$stmt = $conn->prepare("DELETE FROM entrance_examination_student WHERE id = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET[md5('subject_quiz')])) {
	try {
		$subject_num = $_POST['subject_num'];
		$theory = isset($_POST['theory']) ? 1 : 0;
		$practice = isset($_POST['practice']) ? 1 : 0;

		include_once('../connection.php');

		$stmt = $conn->prepare("UPDATE config_subject_quiz SET practice = :practice, theory = :theory WHERE subject_num = :subject_num");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->bindParam(':practice', $practice, PDO::PARAM_INT);
		$stmt->bindParam(':theory', $theory, PDO::PARAM_INT);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['update_entrance_examination_test_config'])) {
	try {
		include_once('../connection.php');
		$conf_arr = isset($_POST['config']) ? $_POST['config'] : array();
		$eep_id = $_POST['eep_id'];
		$data['success'] = edit_entrance_examination_test_config($conn, $eep_id, $conf_arr);
		$data['eep_id'] = $eep_id;
		$data['conf_arr'] = $conf_arr;
		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['set_test_pocket_coefficient'])) {
	try {

		include_once('../connection.php');

		$action = $_POST['coefficient_bool'];

		$is_percent = $action == 'percent' ? 1 : 0;
		$is_test = $action == 'topic' ? 1 : 0;

		$eep_id = $_POST['eep_id'];
		$ee_id = $is_test == 1 ? $_POST['test'] : "";
		$percent = $is_percent == 1 ? $_POST['percent'] : 0;

		$stmt = $conn->prepare("DELETE FROM pocket_test_coefficient WHERE eep_id = :eep_id");
		$stmt->bindParam(":eep_id", $eep_id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $conn->prepare("INSERT INTO pocket_test_coefficient 
												(eep_id, is_test, ee_id, is_percent, percent) 
												VALUE(:eep_id, :is_test, :ee_id, :is_percent, :percent)");
		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
		$stmt->bindParam(':is_test', $is_test, PDO::PARAM_INT);
		$stmt->bindParam(':ee_id', $ee_id, PDO::PARAM_INT);
		$stmt->bindParam(':is_percent', $is_percent, PDO::PARAM_INT);
		$stmt->bindParam(':percent', $percent, PDO::PARAM_INT);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['change_student_school_class'])) {
	try {
		include_once('../connection.php');

		$student_num = $_POST['student_num'];
		$class = $_POST['class'];

		$stmt = $conn->prepare("UPDATE student SET class = :class WHERE student_num = :student_num");
		$stmt->bindParam(':class', $class, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['change_student_ent_info'])) {
	try {
		include_once('../connection.php');

		$student_num = $_POST['student_num'];
		$phone = $_POST['phone1'];
		$tzk = $_POST['tzk'];
		$iin = $_POST['iin'];
		$potok = $_POST['potok'];
		$new = $_POST['new'] == 'true' ? true : false;
		
		if ($new) {
			$stmt = $conn->prepare("INSERT INTO ent_result (student_num, phone, tzk, iin, potok) VALUES (:student_num, :phone, :tzk, :iin, :potok)");
			$stmt->bindParam(":student_num", $student_num, PDO::PARAM_STR);
			$stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindParam(":iin", $iin, PDO::PARAM_STR);
			$stmt->bindParam(":potok", $potok, PDO::PARAM_INT);
			$stmt->bindParam(":tzk", $tzk, PDO::PARAM_STR);
			$stmt->execute();
		} else {
			$stmt = $conn->prepare("UPDATE ent_result SET tzk = :tzk, iin = :iin, potok = :potok WHERE student_num = :student_num");
			$stmt->bindParam(":student_num", $student_num, PDO::PARAM_STR);
			$stmt->bindParam(":iin", $iin, PDO::PARAM_STR);
			$stmt->bindParam(":potok", $potok, PDO::PARAM_INT);
			$stmt->bindParam(":tzk", $tzk, PDO::PARAM_STR);
			$stmt->execute();
		}
		
		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['confirm_parent'])) {
	try {
		include_once('../connection.php');

		$parent_num = $_POST['pid'];

		$stmt = $conn->prepare("UPDATE parent SET checked = 1 WHERE parent_num = :parent_num");
		$stmt->bindParam(":parent_num", $parent_num, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['start_parsing'])) {
	try {
		include_once('../connection.php');

		$stmt = $conn->prepare("UPDATE config_ent SET parse = 1 WHERE id = 1");
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['stop_parsing'])) {
	try {
		include_once('../connection.php');

		$stmt = $conn->prepare("UPDATE config_ent SET parse = 0 WHERE id = 1");
		$stmt->execute();

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
} else if (isset($_GET['poll_info_form'])) {
	try {

		include_once('../connection.php');

		$new_poll_infos = isset($_POST['new-poll-info']) ? $_POST['new-poll-info'] : array();
		$delete_poll_infos = isset($_POST['delete-poll-info']) ? $_POST['delete-poll-info'] : array();
		$edit_poll_infos = isset($_POST['edit-poll-info']) ? $_POST['edit-poll-info'] : array();
		$edit_poll_infos_text = isset($_POST['edit-poll-info-text']) ? $_POST['edit-poll-info-text'] : array();
		// $poll_infos = $_POST['poll-info'];
		if (count($new_poll_infos) > 0) {
			$query = "INSERT INTO teacher_poll_info (text) VALUES ";
			$qPart = array_fill(0, count($new_poll_infos), "(?)");
			$query .= implode(',', $qPart);
			$stmt = $conn->prepare($query);
			$j = 1;
			for ($i=0; $i < count($new_poll_infos); $i++) { 
				$stmt->bindValue($j++, $new_poll_infos[$i], PDO::PARAM_STR);
			}
			$stmt->execute();
		}

		if (count($edit_poll_infos) > 0) {
			$stmt = $conn->prepare("UPDATE teacher_poll_info SET text = ? WHERE id = ?");
			for ($i=0; $i<count($edit_poll_infos_text); $i++) {
				$stmt->execute(array($edit_poll_infos_text[$i], $edit_poll_infos[$i]));
			}
		}

		if (count($delete_poll_infos) > 0) {
			$stmt = $conn->prepare("DELETE FROM teacher_poll_info WHERE id IN (".implode(", ", $delete_poll_infos).")");
			$stmt->execute();
		}

		$data['success'] = true;
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
    }
    echo json_encode($data); 
}

















// ----------------------------------------------------------------------------------------------------functions--------------------------------------



function send_sms_for_finishing_course($conn, $sms_data){

	global $RECIPIENT;
	global $TEXT;
	global $END_COURSE;
	global $RECIPIENT_TYPE_P;

	try {
		
		$stmt = $conn->prepare("SELECT s.student_num, 
									s.name,
									s.surname,
									p.phone
								FROM student s,
									parent p
								WHERE p.student_num = s.student_num
									AND p.parent_order = 1
									AND s.student_num IN ("."'".implode("','", array_keys($sms_data))."'".")");
		$stmt->bindParam(":student_num", $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll();

		foreach ($res as $value) {
			$sms_data[$value['student_num']]['name'] = $value['name'];
			$sms_data[$value['student_num']]['surname'] = $value['surname'];
			$sms_data[$value['student_num']]['phone'] = $value['phone'];
		}

		$sms_result = array();
		$tmp = array("data" => array(), "save_sms_res" => array());
		foreach ($sms_data as $value) {
			if (isset($value['phone'])) {
				foreach ($value['subject_name'] as $val) {
					$data = array(
						$RECIPIENT => "7".$value['phone'],
						$TEXT => kiril2latin(sprintf($END_COURSE, $value['name'], $val))
					);
					array_push($tmp['data'], $data);
					$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
					array_push($sms_result, $res['manual_sms_response']);
				}
			}
		}
		$save_sms_res = save_sms($conn, $sms_result);
		$tmp['save_sms_res'] = $save_sms_res;
		if (!$save_sms_res == "true") {
			return "ERROR ".$save_sms_res;
		} else {
			return $tmp;
		}

	} catch (PDOException $e) {
		throw $e;
	}
}



function merge($file, $language){
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $filename = str_replace('.'.$ext, '', $file).$language.'.'.$ext;
    return ($filename);
}

function checkPDFFile($file_path, $file, $tmp_name, $filesize){
	// 10485760 byte = 10MB
	global $data;
	$file_test = false;
	$fileType = pathinfo($file_path, PATHINFO_EXTENSION);
	if($fileType == "PDF" || $fileType == 'pdf'){
		if($filesize>10485760){
			$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Максимальный размер файла 10MБ ~ (10485760 байт)'); </script>";
		}
		else if(!file_exists($file_path)) {
        	// $data['test'] .= $tmp_name;
            if(move_uploaded_file($tmp_name, $file_path)){
                $file_test = true;
            }
            else{
            	$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Файл не загружен. Попробуйте еще раз!'); </script>";
            }
        }
        else if(file_exists($file_path)){
        	$data['script'] .= "<script type='text/javascript'> alert('Файл с таким названием уже сушествует'); </script>";
        }
        else{
        	$file_test = true;
        }
	}
	else{
    	$data['script'] .= "<script type='text/javascript'> alert('Не правильный формат файла. Доступные форматы : \".pdf, .PDF\"'); </script>";
    }
    return $file_test;
}
function checkFile($photo_path, $file, $tmp_name, $filesize){
	global $data;
	$file_test = false;
	$img_corr = 'false';
    $imageFileType = pathinfo($photo_path,PATHINFO_EXTENSION);
	if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "JPG" || $imageFileType == "PNG" || $imageFileType == "JPEG" || $imageFileType == "GIF"){
		if($filesize>307200){
			$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Максимальный размер изображении 300КБ ~ (307200 байт)'); </script>";
		}
        else if(!file_exists($photo_path)) {
        	// $data['test'] .= $tmp_name;
            if(move_uploaded_file($tmp_name, $photo_path)){
                $file_test = true;
            }
            else{
            	$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Картинка не загружено. Попробуйте еще раз!'); </script>";
            }
        }
        else{
        	$file_test = true;
        }
    }
    else{
    	$data['script'] .= "<script type='text/javascript'> alert('Не правильный формат картинки. Доступный форматы : \".jpg , .png , .jpeg , .gif\"'); </script>";
    }
    if($file_test){
    	return true;
    }
    else{ 
    	return false;
    }
    
}
function addToDB(){
	global $question_img;
	$photo_path = '../img/test/';
	$answer_txt = $_POST['answer'];
	$answer_img_file_size = array();
	$answer_torf = array();
	$answer_img = array();
	$answer_tmp_name = array();
	$answer_torf_array = $_POST['torf'];
	$number_of_answers = $_POST['number_of_answers'];
	$answer_error = false;
	global $data;
	$decrease = 0;
	$decrease_txt = 0;
	$decrease_img = 0;
	for($i=2; $i<$number_of_answers+2; $i++){
		if(isset($_POST['answer'][$i-2]) || isset($_FILES['answer_img']['name'][$i-2]) || isset($_POST['answer-img-hidden'][$i-2])){
			if(isset($_FILES['answer_img']['name'][$i-2]) && $_FILES['answer_img']['name'][$i-2]!=''){
				$answer_img[$decrease] = $_FILES['answer_img']['name'][$i-2];
				$answer_tmp_name[$decrease] = $_FILES['answer_img']['tmp_name'][$i-2];
				$answer_img_file_size[$decrease] = $_FILES['answer_img']['size'][$i-2];
			}
			else if (isset($_POST['answer-img-hidden'][$i-2]) && $_POST['answer-img-hidden'][$i-2]!=''){
				$answer_img[$decrease] = $_POST['answer-img-hidden'][$i-2];
				$answer_tmp_name[$decrease] = $_POST['answer-img-hidden'][$i-2];
			}
			else{
				$answer_img[$decrease] = '';
			}
		
			if($_POST['answer'][$i-2]=='' && $answer_img[$decrease]==''){
				$answer_error = true;
				$data['script'] .= "<script type='text/javascript'> window['emptyAnswer'](".$i."); </script>";
			}	
			else if($_POST['answer'][$i-2]!='' || $_FILES['answer_img']['name'][$i-2]!='') {
				$answer_txt[$decrease] = $_POST['answer'][$i-2];
				$data['script'] .= "<script type='text/javascript'> window['notEmptyAnswer'](".$i."); </script>";
			}
			$answer_torf[$decrease] = isset($answer_torf_array[$i-2]) ? $answer_torf_array[$i-2] : "0";
			$decrease++;
		}
	}
	$torf = true;
	for($i=0; $i<$decrease; $i++){
		$photo_path = '../img/test/';
		$photo_path = $photo_path.basename($answer_img[$i]);
		if($answer_img[$i]!='' && isset($answer_img_file_size[$i])){
			if(!checkFile($photo_path, $answer_img[$i], $answer_tmp_name[$i], $answer_img_file_size[$i])){
				$torf = false;
				$data['test'] = 'testing';
				echo json_encode($data);
				break;
			}
		}
	}
	if($answer_error){
		echo json_encode($data);
	}
	else if(!$torf){
		echo json_encode($data); 
	}
	try {
		if(!$answer_error){
			include("../connection.php");
			$question_num = '';
			if(isset($_POST['hidden_question_num'])){
				deleteAnswer($_POST['hidden_question_num']);
				$question_num = $_POST['hidden_question_num'];
				$text = isset($_POST['question-txt']) ? $_POST['question-txt'] : '';
				updateQuestion($_POST['hidden_question_num'],$text,$question_img);
			}
			else{
				$stmtT = $conn->prepare("SELECT test_num FROM test WHERE subtopic_num = :subtopic_num");
				$stmtT->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				$subtopic_num = $_GET[md5('elementNum')];
				$stmtT->execute();
				$resultT = $stmtT->fetch(PDO::FETCH_ASSOC);

				$stmtQ = $conn->prepare("INSERT INTO question (question_num, test_num, question_text, question_img) VALUES(:question_num, :test_num, :question_text, :question_img)");
	   
			    $stmtQ->bindParam(':question_num', $question_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':test_num', $test_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_text', $question_txt, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_img', $question_img, PDO::PARAM_STR);
			    // preg_replace("/\n/", "<br />", $str)
			    // nl2br()


			    $question_num = uniqid('Q', true)."_".time();
			    $test_num = $resultT['test_num'];
			    // $question_txt = str_replace("\n", "<br>", $_POST['question-txt']);
			    // $question_txt =preg_replace("/\n/", '<br />', $_POST['question-txt']);
			    // $question_txt = nl2br($_POST['question-txt']);
			    $question_txt = $_POST['question-txt'];

			    $stmtQ->execute();
			}

		    $query = "INSERT INTO answer (answer_num, question_num, answer_text, answer_img, torf) VALUES";
		    $qPart = array_fill(0, $decrease, "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<$decrease; $i++){
		    	$answer_num = uniqid('A', true)."_".time();
		    	$stmtA->bindValue($j++, $answer_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $question_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_txt[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_img[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_torf[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		    // $obj = requireToVar('ajax_adminTest.php');
			$data['text'] = '';
			$data['success']=true;
			echo json_encode($data);
		}
	} catch(PDOException $e) {
		$data['error'] .= "Error: " . $e->getMessage();
        echo json_encode($data); 
    }
}
function deleteQuestion($question_num){
	try {
		include("../connection.php");
		$stmt = $conn->prepare("DELETE FROM question WHERE question_num = :question_num");

		$stmt->bindParam(':question_num',$question_num,PDO::PARAM_STR);

		$stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
function deleteAnswer($question_num){
	try {
		include("../connection.php");
		$stmt = $conn->prepare("DELETE FROM answer WHERE question_num = :question_num");

		$stmt->bindParam(':question_num',$question_num,PDO::PARAM_STR);

		$stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
function requireToVar($file){
    ob_start();
    require($file);
    return ob_get_clean();
}
function updateQuestion($question_num,$question_txt,$question_img){
	try {
		include('../connection.php');
		$stmt = $conn->prepare("UPDATE question SET question_text = :question_text, question_img = :question_img WHERE question_num = :question_num");
   
	    $stmt->bindParam(':question_num', $question_num, PDO::PARAM_STR);
	    $stmt->bindParam(':question_img', $question_img, PDO::PARAM_INT);
	    $stmt->bindParam(':question_text', $question_txt, PDO::PARAM_INT);
	       
	    $stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
function edit_entrance_examination_test_config($conn, $eep_id, $conf_arr){
	try {
		$update_ee_data = array();
		$stmt = $conn->prepare("SELECT ee.test_num
								FROM entrance_examination ee,
									test t
								WHERE ee.eep_id = :eep_id
									AND t.test_num = ee.test_num
								ORDER BY SUBSTRING_INDEX(t.name, ' ', 1),
									CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
									CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED)");
		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_STR);
		$stmt->execute();
		$result_ee = $stmt->fetchAll();

		$stmt = $conn->prepare("UPDATE entrance_examination SET test_order = 0 WHERE eep_id = :eep_id");
		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_STR);
		$stmt->execute();

		$order = 0;
		$set = false;
		foreach ($result_ee as $value) {
			if (in_array($value['test_num'], $conf_arr)) {
				$update_ee_data[$value['test_num']] = ++$order;
				$set = $order%2 != 0 ? true : false;
			} else if ($set){
				$update_ee_data[$value['test_num']] = $order;
				$set = $order%2 != 0 ? true : false;
			}
		}

	
		if (count($conf_arr)!=0){
			$stmt = $conn->prepare("UPDATE entrance_examination SET test_order = ? WHERE test_num = ? AND eep_id = ?");
			foreach ($update_ee_data as $key => $value) {
				$stmt->execute(array($value, $key, $eep_id));
			}
		}



		// $conf_arr_count = count($conf_arr);
		// $stmt = $conn->prepare("DELETE FROM entrance_examination_test_config WHERE eep_id = :eep_id");
		// $stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
		// $stmt->execute();

		// $query = "INSERT INTO entrance_examination_test_config (eep_id, test_num, test_order) VALUES ";
		// $qPart = array_fill(0, count($conf_arr), "(?, ?, ?)");
		// $query .= implode(',', $qPart);
		// $stmt = $conn->prepare($query);

		// $test_order = array();
		// for ($i=0; $i<count($conf_arr); $i++) { 
		// 	array_push($test_order, $i+1);
		// }

		// $j = 1;
		// $cc = array();
		// $cc2_str = count($conf_arr);
		// for ($i=0; $i <$conf_arr_count; $i++) {
		// 	$stmt->bindParam($j++, $eep_id, PDO::PARAM_INT);
		// 	$stmt->bindParam($j++, $conf_arr[$i], PDO::PARAM_STR);
		// 	$stmt->bindParam($j++, $test_order[$i], PDO::PARAM_INT);
		// }
		// $stmt->execute();
		return true;
	} catch (PDOException $e) {
		return "Error: " . $e->getMessage();
	}
}


























// -------------------------browserDetect-------------------------------------------------
	function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 



function writeToLog($query, $values,$text){
	if(!isset($_SESSION)){
		session_start();
	}
	$txt = "";
	$browser = getBrowser();
	$file = fopen("logDB/log.txt", "a") or die("Unable to open file!");
	$txt .= "---------------------------------".$text."----------------------------------------------\n";
	$txt .= "Date:    ".date("d-m-Y h:i:sa")."\n";
	// "Y-m-d h:i:sa"
	$txt .= "User-->: ".$_SESSION['adminSurname']." ".$_SESSION['adminName']."\n";
	$txt .= "Query:   ".$query."\n";
	$txt .= "Values:  ".$values."\n";
	$txt .= "Browser: ".implode("...........", $browser)."\n";
	$txt .= "--------------------------------------------------------------------------------------\n\n\n";
	fwrite($file, $txt);
	fclose($file);
}
?>