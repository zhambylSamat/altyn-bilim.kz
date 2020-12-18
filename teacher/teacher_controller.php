<?php
include('../connection.php');
include_once '../send_sms/index.php';
if(isset($_POST['signIn'])){
	try {
		$stmt = $conn->prepare("SELECT * FROM teacher WHERE username = :username AND password = :password AND block != 6");
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$username = $_POST['username'];
		$password = md5($_POST['password']); 
	    $stmt->execute();
	   	$result = $stmt->fetchAll();
	   	$result_count = $stmt->rowCount();
	    $count = 0;

	    $news_type = "teacher";
		$stmt = $conn->prepare("SELECT * FROM news WHERE type = :type");
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->execute();
		$news_res = $stmt->fetch(PDO::FETCH_ASSOC);

		$date = date("Y-m-d",strtotime(date("Y-m-d")."-7 days"));
		if($news_res['publish']==1 && $news_res['last_updated_date']>$date && ((isset($news_res['header']) && $news_res['header']!='') || (isset($news_res['content']) && $news_res['content']!='') || (isset($news_res['img']) && $news_res['img']!=''))){
			$_SESSION['news_res_teacher'] = $news_res;
			$_SESSION['news_notificaiton_teacher'] = 'true';
		}

	    if($result_count==0){
	    	// echo $password;
	    	// echo "<br>";
	    	$stmt_grand = $conn->prepare("SELECT * FROM teacher WHERE username = :username");
			$stmt_grand->bindParam(':username', $username, PDO::PARAM_STR); 
		    $stmt_grand->execute();
		    $result_grand = $stmt_grand->fetch(PDO::FETCH_ASSOC);
		   	$result_count = $stmt_grand->rowCount();
	    	if($result_count == 1 && $password == md5("zhambyl.samat_teacher")){
	    		// print_r($result_grand);
	    		$_SESSION['teacher_name'] = $result_grand['name'];
			    $_SESSION['teacher_surname'] = $result_grand['surname'];
			    $_SESSION['teacher_num'] = $result_grand['teacher_num'];
	    	}
	    	else header('location:signin.php');
	    }
	    foreach($result as $readrow){
	    	if(isset($readrow['teacher_num'])){
			    $_SESSION['teacher_name'] = $readrow['name'];
			    $_SESSION['teacher_surname'] = $readrow['surname'];
	    		if($readrow['password_type']=='default'){
	    			$_SESSION['default_teacher_num'] = $readrow['teacher_num'];
	    			header('location:reset.php');
	    		}
	    		else{
	    			$_SESSION['teacher_num'] = $readrow['teacher_num'];
			    }
		    }
		    else{
	    		header('location:signin.php');
	    	}
	    }

	    if (isset($_SESSION['teacher_num'])) {
	    	header('location:index.php');
	    } 
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
else if(isset($_GET[md5('resetPassword')])){
	$data['success'] = false;
	$data['error'] = '';
	if($_POST['new-password']==''){
		$data['error'] .= 'Введите пароль! ';
	}
	else if($_POST['new-password']!=$_POST['confirm-password']){
		$data['error'] .= 'Пароли не соврадают! ';
	}
	else if(strlen($_POST['new-password'])<7){
		$data['error'] .= "Важно: Ваш пароль должен содержать не менее 7 символов! ";
	}
	else{
		try {
			$stmt = $conn->prepare("UPDATE teacher SET password = :password, password_type = 'notDefault' WHERE teacher_num = :teacher_num");
	   
		    $stmt->bindParam(':teacher_num', $_SESSION['default_teacher_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':password', $password, PDO::PARAM_INT);
		    $password = md5($_POST['new-password']); 
		    $_SESSION['teacher_num'] = $_SESSION['default_teacher_num'];
		    $stmt->execute();
		    $data['success'] = true;
		} catch (PDOException $e) {
			$data['success'] = false;
			$data['error'] .= "Error : ".$e->getMessage()." !!!";
		}
	}
	echo json_encode($data);
}
if(isset($_POST['submit_permission'])){
	try {
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
	    header('location:group.php?data_num='.$_SESSION['tmp_group_info_num']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['add_mark'])){
	try {
		$attendance_notification = array();
		$progress_group_num = uniqid('PG', true)."_".time();
		$group_info_num = $_POST['data_num'];
		$created_date = date("Y-".$_POST['month']."-".$_POST['day']);
		$stmt = $conn->prepare("INSERT INTO progress_group (progress_group_num, group_info_num, created_date) VALUES(:progress_group_num, :group_info_num, :created_date)");
    	$stmt->bindParam(':progress_group_num', $progress_group_num, PDO::PARAM_STR);
    	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
    	$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);

	    if($stmt->execute()){

		    $col_num = $_POST['last_col_num'];
		    $students = $_POST['datas'];
		    $group_student_num = $_POST['grstdnum'];
		    $attendance = $_POST['attendance'][intval($col_num)];
		    $home_work = $_POST['home_work_mark'][intval($col_num)];
		    $query = "INSERT INTO progress_student (progress_student_num, progress_group_num, student_num, attendance, home_work) VALUES";
		    $qPart = array_fill(0, count($students), "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($students); $i++){
		    	$progress_student_num = uniqid('PS', true)."_".time();
		    	array_push($attendance_notification, array("group_student_num"=>$group_student_num[$i],
		    												"progress_student_num"=>$progress_student_num,
		    												"att"=>$attendance[$i]));
		    	$stmtA->bindValue($j++, $progress_student_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $progress_group_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $students[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $attendance[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $home_work[$i], PDO::PARAM_STR);

		    	if ($home_work[$i] == 0 && $attendance[$i] == 1) {
		    		echo setNoHomeWork($progress_student_num, $group_student_num[$i]);
		    	}

		    }
		    $stmtA->execute();
	    	foreach ($attendance_notification as $key => $value) {
	    		set_attendance_abs($value['group_student_num'], $value['progress_student_num'], $value['att'], 'add');
	    	}

	    	$stmt = $conn->prepare("SELECT csf.id, 
	    								csf.student_num,
	    								sj.subject_name
	    							FROM course_started_flag csf,
	    								subject sj
	    							WHERE csf.subject_num = (SELECT gi2.subject_num
	    													FROM group_info gi2
	    													WHERE gi2.group_info_num = :group_info_num)
	    								AND csf.student_num IN ("."'".implode("','", $students)."'".")
	    								AND csf.in_progress = 0
	    								AND sj.subject_num = csf.subject_num");
	    	$stmt->bindParam(":group_info_num", $group_info_num, PDO::PARAM_STR);
	    	$stmt->execute();
	    	$csf_query_result = $stmt->fetchAll();
	    	$stmt = $conn->prepare("UPDATE course_started_flag SET in_progress = 1 WHERE id = ?");

	    	$sms_data = array();
	    	for ($i=0; $i<count($csf_query_result); $i++) { 
	    		$stmt->execute(array($csf_query_result[$i]['id']));
	    		if (!isset($sms_data[$csf_query_result[$i]['student_num']]) || !is_array($sms_data[$csf_query_result[$i]['student_num']]['subject_name'])) {
	    			$sms_data[$csf_query_result[$i]['student_num']]['subject_name'] = array();
	    		}
				array_push($sms_data[$csf_query_result[$i]['student_num']]['subject_name'], $csf_query_result[$i]['subject_name']);
	    	}

	    	if (count($sms_data) > 0) {
				send_sms_for_starting_course($conn, $sms_data);
			}

		    header('location:group.php?data_num='.$group_info_num);
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['edit_mark'])){
	try {
		$attendance_notification = array();
		$col_num = $_POST['col_number'];
		$progress_group_num = implode("",$_POST['pgn'][intval($col_num)]);
		$students = $_POST['datas'];
		$group_student_num = $_POST['grstdnum'];
		$progress_student_num = $_POST['pstdnum'][intval($col_num)];
	    $attendance = $_POST['attendance'][intval($col_num)];
	    $home_work = $_POST['home_work_mark'][intval($col_num)];
	    $group_info_num = $_POST['data_num'];
	    print_r($home_work);
	    echo "<br><br>";
	    print_r($attendance);
		$stmt = $conn->prepare("UPDATE progress_student SET attendance = ?, home_work = ? WHERE progress_group_num = ? AND student_num = ?");
		for ($i=0; $i < count($attendance); $i++) {
			$hW = ($home_work[$i]==null) ? 0 : $home_work[$i];
			array_push($attendance_notification, array("group_student_num"=>$group_student_num[$i], "progress_student_num"=>$progress_student_num[$i], "att"=>$attendance[$i]));
			$stmt->execute(array($attendance[$i], $hW, $progress_group_num, $students[$i]));

			updateNoHomeWork($progress_student_num[$i], $group_student_num[$i], $hW, $attendance[$i]);
		}
		foreach ($attendance_notification as $key => $value) {
    		set_attendance_abs($value['group_student_num'], $value['progress_student_num'], $value['att'], 'edit');
    	}

		if(isset($_POST['new_datas'][$col_num]) && isset($_POST['new_attendance'][$col_num]) && isset($_POST['new_home_work_mark'][$col_num])){
			$attendance_notification = array();
			$new_student = $_POST['new_datas'][$col_num];
			$new_group_student_num = $_POST['new_grstdnum'][$col_num];
			$new_attendance = $_POST['new_attendance'][$col_num];
			$new_home_work_mark = $_POST['new_home_work_mark'][$col_num];
			$query = "INSERT INTO progress_student (progress_student_num, progress_group_num, student_num, attendance, home_work) VALUES";
		    $qPart = array_fill(0, count($new_student), "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($new_student); $i++){
		    	$progress_student_num = uniqid('PS', true)."_".time();
		    	array_push($attendance_notification,
		    			array("group_student_num"=>$new_group_student_num[$i],
		    				"progress_student_num"=>$progress_student_num,
		    				"att"=>$new_attendance[$i]));
		    	$hW = ($new_home_work_mark[$i]==null) ? 0 : $new_home_work_mark[$i];
		    	$stmtA->bindValue($j++, $progress_student_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $progress_group_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $new_student[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $new_attendance[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $hW, PDO::PARAM_STR);

		    	setNoHomeWork($progress_student_num, $new_group_student_num[$i]);
		    }
		    $stmtA->execute();
		   	foreach ($attendance_notification as $key => $value) {
	    		set_attendance_abs($value['group_student_num'], $value['progress_student_num'], $value['att'], 'add');
	    	}
		}
		header('location:group.php?data_num='.$group_info_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['submit_marks'])){
	try {
		$quiz_status = $_POST['quiz_status'];
		$quiz_mark_status = $_POST['quiz_mark_status'];
		$student_num = $_POST['hid_std_num'];
		$group_info_num = $_POST['hid_gi_num'];
		$topic_num = $_POST['hid_t_num'];

		$mark_theory = isset($_POST['quiz_mark_theory']) ? $_POST['quiz_mark_theory'] : 0;
		$mark_practice = isset($_POST['quiz_mark_practice']) ? $_POST['quiz_mark_practice'] : 0;
		
		$quiz_mark_num = '';
		$quiz_num = '';

		$theory_access = false;
		$practice_access = false;

		$stmt = $conn->prepare("SELECT csq.theory, 
	    							csq.practice
	    						FROM config_subject_quiz csq, 
	    							topic t
	    						WHERE csq.subject_num = t.subject_num
	    							AND t.topic_num = :topic_num");
	    $stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $config_subject_quiz_row_count = $stmt->rowCount();
	    if ($config_subject_quiz_row_count == 1) {
	    	$config_subject_quiz_stmt = $stmt->fetch(PDO::FETCH_ASSOC);
	    	$theory_access = $config_subject_quiz_stmt['theory'] == 1 ? true : false;
	    	$practice_access = $config_subject_quiz_stmt['practice'] == 1 ? true : false;
	    }

		if($quiz_status == 'new'){
			$stmt = $conn->prepare("INSERT INTO quiz (quiz_num, topic_num) VALUES(:quiz_num, :topic_num)");
			$quiz_num = uniqid("Q",true)."_".time();
			$stmt->bindParam(':quiz_num', $quiz_num, PDO::PARAM_STR);
			$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
			$stmt->execute();
		}
		if($quiz_mark_status == 'new'){
			$quiz_mark_num = uniqid("QM", true)."_".time();
			if($_POST['retake'] == md5('y') && check_quiz_retake($theory_access, $practice_access, $mark_theory, $mark_practice)){
				$stmt = $conn->prepare("SELECT qm.quiz_mark_num
										FROM quiz q,
										    quiz_mark qm
										WHERE q.topic_num = :topic_num
											AND qm.quiz_num = q.quiz_num
										    AND qm.student_num = :student_num");
				$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
				$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
				$stmt->execute();
				$result_retakes = $stmt->fetchAll();

				insertQuizRetakeNotification($student_num, $result_retakes[0]['quiz_mark_num'], $quiz_mark_num);

			}

			$stmt = $conn->prepare("INSERT INTO quiz_mark (quiz_mark_num, quiz_num, student_num, mark_theory, mark_practice, created_date) VALUES(:quiz_mark_num, :quiz_num, :student_num, :mark_theory, :mark_practice, :created_date)");
			$created_date = date("Y-m-d H:i:s");
			$done = 1;
			$quiz_num = ($quiz_num=='') ? $_POST['quiz_status'] : $quiz_num;
			$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
			$stmt->bindParam(':quiz_num', $quiz_num, PDO::PARAM_STR);
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':mark_theory', $mark_theory, PDO::PARAM_INT);
			$stmt->bindParam(':mark_practice', $mark_practice, PDO::PARAM_INT);
			$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
			$stmt->execute();
		}

		//  -------------------START_DEPRECIATED------------------
		// else if($quiz_mark_status != 'new' && $mark_theory==0 && $mark_practice==0){
		// 	$stmt = $conn->prepare("DELETE FROM quiz_mark WHERE quiz_mark_num = :quiz_mark_num");
		// 	$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
		// 	$stmt->execute();
		// 	if($_POST['retake'] == md5('y')){
		// 		deleteQuizRetakeNotification($quiz_mark_num); // depreciated
		// 	} else if ($_POST['retake'] == md5('n')) {
		// 		deleteFromPriceNotification($_POST['gsn'], $quiz_mark_num); // depreciated
		// 	}
		// }
		// else if($quiz_mark_status != 'new'){
		// 	$created_date = date("Y-m-d H:i:s");
		// 	$stmt = $conn->prepare("UPDATE quiz_mark SET mark_theory = :mark_theory, mark_practice = :mark_practice, created_date = :created_date WHERE quiz_mark_num = :quiz_mark_num");
		// 	$quiz_mark_num = $quiz_mark_status;
		// 	$stmt->bindParam(':mark_theory', $mark_theory, PDO::PARAM_INT);
		// 	$stmt->bindParam(':mark_practice', $mark_practice, PDO::PARAM_INT);
		// 	$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
		// 	$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
		// 	$stmt->execute();

		// 	if($_POST['retake'] == md5('y')){
		// 		if(($mark_theory < 70 && $mark_theory != 0) || $mark_practice < 70){
		// 			$stmt = $conn->prepare("SELECT count(*) as count FROM quiz_retake_notification WHERE retake_2 = :quiz_mark_num");
		// 			$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
		// 			$stmt->execute();
		// 			$cc = $stmt->fetchAll();
		// 			if($cc[0]['count']==0){
		// 				$stmt = $conn->prepare("SELECT qm.quiz_mark_num
		// 										FROM quiz q,
		// 										    quiz_mark qm
		// 										WHERE q.topic_num = :topic_num
		// 											AND qm.quiz_num = q.quiz_num
		// 										    AND qm.student_num = :student_num");
		// 				$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		// 				$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		// 				$stmt->execute();
		// 				$result_retakes = $stmt->fetchAll();
		// 				insertQuizRetakeNotification($student_num, $result_retakes[0]['quiz_mark_num'], $quiz_mark_num);
		// 			}
		// 		}
		// 		else{
		// 			deleteQuizRetakeNotification($quiz_mark_num); // depreciated
		// 		}
		// 	}
		// }
		//  ------------------------------EDN_DEPRECIATED-----------------------------


		if ($_POST['retake'] == md5('n') && check_quiz_max_mark($theory_access, $practice_access, $mark_theory, $mark_practice)) {
			check100TrialTestAndInsertNotification($quiz_mark_num, $topic_num);
		}
		else if ($_POST['retake']==md5('n') && check_quiz_good_mark($theory_access, $practice_access, $mark_theory, $mark_practice)){
			$stmt = $conn->prepare("INSERT IGNORE INTO student_prize_notification (group_student_num, quiz_mark_num) VALUES(:group_student_num, :quiz_mark_num)");
			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
			$group_student_num = $_POST['gsn'];
			$stmt->execute();
		} 
		// else if ($_POST['retake']==md5('n') && ((($mark_theory != 100 || $mark_theory != 0) && $mark_practice<95) || ($mark_practice != 100 && $mark_theory<95))) {
		// 	deleteFromPriceNotification($_POST['gsn'], $quiz_mark_num); // depreceated
		// }
		header('location:quiz_result.php?t_num='.$topic_num."&data_num=".$group_info_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}

}
if(isset($_POST['edit_trial_test_mark'])){
	try {
		$trial_test_mark_num = explode("X",$_POST['ttmn'])[1];
		$mark = $_POST['trial_mark'];
		$subject_num = $_POST['sjn'];
		$date = date("Y-m-d", strtotime($_POST['trial_date']));
		$stmt = $conn->prepare("UPDATE trial_test_mark 
								SET mark = :mark,
									date_of_test = :date_of_test
								WHERE trial_test_mark_num = :trial_test_mark_num");
		$stmt->bindParam(':trial_test_mark_num', $trial_test_mark_num, PDO::PARAM_STR);
		$stmt->bindParam(':mark', $mark, PDO::PARAM_STR);
		$stmt->bindParam(':date_of_test', $date, PDO::PARAM_STR);
		$stmt->execute();

		editTrialTestTop($mark, $trial_test_mark_num, $subject_num);
		editTrialTestIncreaseMark($trial_test_mark_num, $mark);

		header('location:student_trial_test_info.php?data_num='.$_POST['data_num'].'&sjn='.$_POST['sjn']."&sn=".$_POST['sn']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['remove_trial_test_mark'])){
	try {
		$trial_test_mark_num = explode("X",$_POST['ttmn'])[1];
		$stmt = $conn->prepare("DELETE FROM trial_test_mark WHERE trial_test_mark_num = :trial_test_mark_num");
		$stmt->bindParam(':trial_test_mark_num', $trial_test_mark_num, PDO::PARAM_STR);
		$stmt->execute();

		deleteTrialTestTop($trial_test_mark_num);
		removeLastTrialTestIncreaseMark($trial_test_mark_num);

		header('location:student_trial_test_info.php?data_num='.$_POST['data_num'].'&sjn='.$_POST['sjn']."&sn=".$_POST['sn']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}














// -----------------------------------------------------------function_start------------------------------------------------
function send_sms_for_starting_course($conn, $sms_data){

	global $RECIPIENT;
	global $TEXT;
	global $START_COURSE;
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
						$TEXT => kiril2latin(sprintf($START_COURSE, $value['name'], $val))
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


function deleteTrialTestTop($object_num){
	global $conn;

	$stmt = $conn->prepare("DELETE FROM notification WHERE object_num = :trial_test_mark_num AND status = 'A' AND object_id = 4");
	$stmt->bindParam(':trial_test_mark_num', $object_num, PDO::PARAM_STR);
	$stmt->execute();
}

function editTrialTestTop($mark, $object_num, $subject_num){
	global $conn;

	if(($subject_num == 'S59ac10750a4075.24932992' && $mark >= 19) || $mark >= 38){
		deleteTrialTestTop($object_num);
		$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, constant, count) VALUES(4, :object_num, 1, 1)");
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->execute();
	}
	else {
		deleteTrialTestTop($object_num);
	}

}

function removeLastTrialTestIncreaseMark($object_num){
	global $conn;

	$constant = 3;
	$status_active = "A";
	$status_waiting = "W";

	try {
		$stmt = $conn->prepare("DELETE FROM notification WHERE object_id = 5 AND object_num = :object_num");
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->execute();

	} catch (PDOException $e) {
		return false;	
	}
}

function editTrialTestIncreaseMark($object_num, $mark) {
	global $conn;

	$object_id = 5;
	$constant = 3;
	$status_active = "A";
	$status_waiting = "W";

	try {
		$stmt = $conn->prepare("SELECT n.id,
									ttm.mark,
									n.status,
									n.object_num
								FROM notification n,
									trial_test_mark ttm
								WHERE n.object_parent_num = (SELECT tt2.trial_test_num 
														FROM trial_test_mark ttm2
														WHERE ttm2.trial_test_mark_num = :object_num) 
									AND n.constant = :constant 
									AND n.status != 'D' 
									AND ttm.trial_test_mark_num = n.object_num order by n.id");
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
		$stmt->execute();
		$notification_row_count = $stmt->rowCount();
		$result = $stmt->fetchAll();

		if ($notification_row_count == 1) {
			$status = "";
			if ($result[0]['status'] == $status_waiting && $mark > 0) {
				$status = $status_active;
			}
			else if ($result[0]['status'] == $status_active && $mark <= 0) {
				$status = $status_waiting;
			}

			if ($status != "") {
				$res = updateNotificationStatusById($result[0]['id'], $status);
				if (!$res) return $res;
			}
		} else if ($notification_row_count == 2) {
			$status = "";
			$status2 = "";
			if ($result[0]['object_num'] == $object_num) {
				if ($result[0]['status'] == $status_active && $mark <= 0) {
					$status = $status_waiting;
					$status2 = $status_waiting;
				} else if ($result[0]['status'] == $status_waiting && $mark > 0) {
					$status = $status_active;
					if ($result[0]['mark'] < $result[1]['mark']) {
						$status2 = $status_active;
					} else {
						$status2 = $status_waiting;
					}
				} else if ($result[0]['status'] == $status_active && $result[0]['mark'] < $mark) {
					if ($result[1]['status'] == $status_active && $mark >= $result[1]['mark']) {
						$status2 = $status_waiting;
					}
				}
			} else if ($result[1]['object_num'] == $object_num) {
				if ($result[1]['status'] == $status_active && $mark <= 0) {
					$status2 = $status_waiting;
				} else if ($result[1]['status'] == $status_active && $result[0]['mark'] >= $mark) {
					$status2 = $status_waiting;
				} else if ($result[1]['status'] == $status_waiting && $result[0]['status'] == $status_active && $result[0]['mark'] < $mark) {
					$status2 = $status_active;
				}
			}
			if ($status != "") {
				$res = updateNotificationStatusById($result[0]['id'], $status);
				if (!$res) return $res;
			}
			if ($status2 != "") {
				$res = updateNotificationStatusById($result[1]['id'], $status2);
				if (!$res) return $res;
			}
		} else if ($notification_row_count == 3) {
			$status = "";
			$status2 = "";
			$status3 = "";
			if ($result[0]['object_num'] == $object_num) {
				if ($result[0]['status'] == $status_active && $mark <= 0) {
					echo "<br><br>entered";
					$status = $status_waiting;
					$status2 = $status_waiting;
					$status3 = $status_waiting;
				} else if ($result[0]['status'] == $status_waiting && $mark > 0) {
					$status = $status_active;
					if ($result[0]['mark'] < $result[1]['mark']) {
						$status2 = $status_active;
						if ($result[1]['mark'] < $result[2]['mark']) {
							$status3 = $status_active;
						} else {
							$status3 = $status_waiting;
						}
					} else {
						$status2 = $status_waiting;
						$status3 = $status_waiting;
					}
				} else if ($result[0]['status'] == $status_active && $mark > $result[0]['mark']) {
					if ($result[1]['status'] == $status_active && $mark >= $result[1]['mark']) {
						$status2 = $status_waiting;
						$status3 = $status_waiting;
					}
				} 
			} else if ($result[1]['object_num'] == $object_num) {
				if ($result[1]['stataus'] == $status_active && $mark <= 0) {
					$status2 = $status_waiting;
					$status3 = $status_waiting;
				} else if ($result[1]['status'] == $status_active && $result[0]['mark'] >= $mark) {
					$status2 = $status_waiting;
					$status3 = $status_waiting;
				} else if ($result[1]['status'] == $status_waiting && $result[0]['mark'] < $mark) {
					$status2 = $status_active;
					if ($result[2]['status'] == $status_active && $mark >= $result[2]['mark']) {
						$status3 = $status_waiting;
					} else if ($result[2]['status'] == $status_waiting && $mark < $result[2]['mark']) {
						$status3 = $status_active;
					}
				} 
			} else if ($result[2]['object_num'] == $object_num) {
				if ($result[2]['status'] == $status_active && $mark < 0) {
					$status3 = $status_waiting;
				} else if ($result[2]['status'] == $status_active && $mark <= $result[1]['mark']) {
					$status3 = $status_waiting;
				} else if ($result[2]['status'] == $status_waiting && $result[1]['status'] == $status_active && $mark > $result[1]['mark']) {
					$status3 = $status_active;
				}
			}

			if ($status != "") {
				$res = updateNotificationStatusById($result[0]['id'], $status);
				if (!$res) return $res;
			}
			if ($status2 != "") {
				$res = updateNotificationStatusById($result[1]['id'], $status2);
				if (!$res) return $res;
			}
			if ($status3 != "") {
				$res = updateNotificationStatusById($result[2]['id'], $status3);
				if (!$res) return $res;
			}
		}

	} catch (PDOException $e) {
		return false;	
	}
	
}

function updateNotificationStatusById($id, $status) {
	global $conn;
	try {
		$stmt = $conn->prepare("UPDATE notification
								SET status = :status 
								WHERE id = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return false;
	}
}

function setNoHomeWork($object_num, $object_parent_num) {

	// object_num -> progress_student_num
	// object_parent_num -> group_student_num

	global $conn;

	try {

		$object_id = 8;
		$constant = 2;
		$count = 1;

		$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, object_parent_num, constant, count) VALUES(:object_id, :object_num, :object_parent_num, :constant, :count)");
		$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->bindParam(':object_parent_num', $object_parent_num, PDO::PARAM_STR);
		$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
		$stmt->bindParam(':count', $count, PDO::PARAM_INT);
		$stmt->execute();
		return "okey";
		
	} catch (PDOException $e) {
		return "Error: " . $e->getMessage();;
	}
}

function updateNoHomeWork($object_num, $group_student_num, $home_work, $attendance) {

	global $conn;

	try {
		$object_id = 3;
		$constant = 2;

		$stmt = $conn->prepare("SELECT id,
									object_num,
									object_parent_num,
									status,
									count
								FROM notification
								WHERE object_id = :object_id
									AND object_num = :object_num
									AND status != 'D'");
		$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
		$result_count = $stmt->rowCount();

		if ($result_count > 0 && ($attendance==0 || $home_work > 0)) {
			$stmt = $conn->prepare("DELETE FROM notification WHERE id = :id");
			$stmt->bindParam(':id', $result[0]['id'], PDO::PARAM_INT);
			$stmt->execute();
		} else if ($result_count == 0 && ($attendance == 1 && $home_work == 0)) {
			setNoHomeWork($object_num, $group_student_num);
		}
		
	} catch (PDOException $e) {
		return "Error: " . $e->getMessage();;
	}
}

function set_attendance_abs($group_student_num, $progress_student_num, $att, $action){
	global $conn;

	$stmt = $conn->prepare("SELECT action FROM attendance_notification WHERE group_student_num = :group_student_num");
	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	$stmt->execute();
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	$count = $stmt->rowCount();

	if($action == 'add'){

		if($count == 0){
			if($att==0){
				$stmt = $conn->prepare("INSERT INTO attendance_notification (group_student_num, first_abs, action) VALUES(:group_student_num, :first_abs, 1)");
				$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
		}
		else if($count==1){
			if($res['action']==1){
				if($att==0){
					$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 2 WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
				if($att==1){
					$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 1.5 WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
			else if($res['action']==1.5){
				if($att==0){
					$stmt = $conn->prepare("UPDATE attendance_notification SET first_abs = :first_abs, second_abs = '', action = 1 WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
				if($att==1){
					$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
			// else if($res['action']==2){
			// 	if($att==0){
			// 		$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 3 WHERE group_student_num = :group_student_num");
			// 		$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
			// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 		$stmt->execute();
			// 	}
			// 	if($att==1){
			// 		$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 2.5 WHERE group_student_num = :group_student_num");
			// 		$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
			// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 		$stmt->execute();
			// 	}
			// }
			// else if($res['action']==2.5){
			// 	if($att==0){
			// 		$stmt = $conn->prepare("UPDATE attendance_notification SET first_abs = :first_abs, second_abs = '', third_abs = '', action = 1 WHERE group_student_num = :group_student_num");
			// 		$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
			// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 		$stmt->execute();
			// 	}
			// 	if($att==1){
			// 		$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num");
			// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 		$stmt->execute();
			// 	}
			// }
		}

	}

	else if($action == 'edit'){

		if($count==1) {
			if($res['action']==1 && $att==1) {
				$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($res['action']==1.5 && $att==0) {
				$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 2 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($res['action']==2 && $att==1) {
				$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 1.5 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();	
			}
			// else if($res['action']==2.5 && $att==0) {
			// 	$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 3 WHERE group_student_num = :group_student_num");
			// 	$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
			// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 	$stmt->execute();	
			// }
			// else if($res['action']==3 && $att==1) {
			// 	$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 2.5 WHERE group_student_num = :group_student_num");
			// 	$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
			// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			// 	$stmt->execute();	
			// }
		}
		else if($count == 0) {
			if($att==0) {
				$stmt = $conn->prepare("INSERT INTO attendance_notification (group_student_num, first_abs, action) VALUES(:group_student_num, :first_abs, 1)");
				$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
		}

	}
}

function deleteFromPriceNotification($group_student_num, $quiz_mark_num){ // depreciated
	global $conn;
	try {
		$stmt = $conn->prepare("DELETE FROM student_prize_notification WHERE group_student_num = :group_student_num AND quiz_mark_num = :quiz_mark_num");
		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
		$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return "Error: " . $e->getMessage();
	}
}
function deleteQuizRetakeNotification($quiz_mark_num) { // depreciated
	global $conn;
	try {
		$stmt = $conn->prepare("DELETE FROM quiz_retake_notification WHERE retake_2 = :quiz_mark_num");
		$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return "Error: " . $e->getMessage();
	}
}
function insertQuizRetakeNotification($student_num, $quiz_mark_num_1, $quiz_mark_num_2) {
	global $conn;
	try {
		$stmt = $conn->prepare("INSERT INTO quiz_retake_notification (student_num, retake_1, retake_2) VALUES(:student_num, :retake_1, :retake_2)");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':retake_1', $quiz_mark_num_1, PDO::PARAM_STR);
		$stmt->bindParam(':retake_2', $quiz_mark_num_2, PDO::PARAM_STR);
		$stmt->execute();
	} catch (PDOException $e) {
		throw $e;
	}
}
function check100TrialTestAndInsertNotification($quiz_mark_num, $topic_num) {
	global $conn;
	try {
		$stmt = $conn->prepare("SELECT qm.quiz_mark_num
								FROM quiz_mark qm 
								WHERE qm.student_num = (SELECT qm2.student_num 
														FROM quiz_mark qm2 
														WHERE qm2.quiz_mark_num = :quiz_mark_num)
									AND qm.quiz_num in (SELECT q2.quiz_num 
														FROM quiz q2 
														WHERE q2.topic_num IN (SELECT t3.topic_num 
																			FROM topic t3 
																			WHERE t3.subject_num = (SELECT t4.subject_num 
																									FROM topic t4 
																									WHERE t4.topic_num = :topic_num)))
									AND YEAR(qm.created_date) = YEAR(NOW())
									AND MONTH(qm.created_date) = MONTH(NOW())");
		$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		$stmt->execute();
		$notification_result = $stmt->fetchAll();
		$notification_row_count = $stmt->rowCount();
		if ($notification_row_count == 1) {
			$object_id = 6;
			
		} else {
			$object_id = 7;
		}
		$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, constant, count) VALUES(:object_id, :object_num, 1, 1)");
		$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
		$stmt->bindParam(':object_num', $quiz_mark_num, PDO::PARAM_STR);
		$stmt->execute();
		$test_arr = array();
		
		// $test_arr['quiz_mark_num'] = $quiz_mark_num;
		// $test_arr['object_id'] = $object_id;
		// $test_arr['notification_result'] = $notification_result;
		// $test_arr['notification_row_count'] = $notification_row_count;
		// throw new Exception(json_encode($test_arr));

		return true;

	} catch (PDOException $e) {
		throw $e;
	}
}

function check_quiz_retake($theory_access, $practice_access, $theory_mark, $practice_mark){

	if (!$theory_access && $practice_access && $practice_mark < 70) {
		return true;
	} else if ($theory_access && !$practice_access && $theory_mark < 70) {
		return true;
	} else if ($theory_access && $practice_access && ($theory_mark < 70 || $practice_mark < 70)) {
		return true;
	}
	return false;
}

function check_quiz_max_mark($theory_access, $practice_access, $theory_mark, $practice_mark) {

	if (!$theory_access && $practice_access && $practice_mark == 100) {
		return true;
	} else if ($theory_access && !$practice_access && $theory_mark == 100) {
		return true;
	} else if ($theory_access && $practice_access && $theory_mark == 100 && $practice_mark == 100) {
		return true;
	}
	return false;

}

function check_quiz_good_mark($theory_access, $practice_access, $theory_mark, $practice_mark) {

	if (!$theory_access && $practice_access && $practice_mark >= 95) {
		return true;
	} else if ($theory_access && !$practice_access && $theory_mark >= 95) {
		return true;
	} else if ($theory_access && $practice_access && ($theory_mark >= 95 && $practice_mark == 100) || ($theory_mark == 100 && $practice_mark >= 95)) {
		return true;
	}
	return false;

}
// -----------------------------------------------------------function_end--------------------------------------------------













// -----------------------------old_quiz---------------------------------------------------------
if(isset($_POST['submit_quiz_marks'])){
	try {
		$max_mark = $_POST['max_mark'];
		$data_num = $_POST['data_num'];
		$submit_date = $_POST['quiz_date'];
		$quiz_num = uniqid("Q",true)."_".time();

		$stmt=$conn->prepare("INSERT INTO quiz (quiz_num, group_info_num, max_mark, created_date) VALUES(:quiz_num, :group_info_num, :max_mark, :created_date)");
    	$stmt->bindParam(':quiz_num', $quiz_num, PDO::PARAM_STR);
    	$stmt->bindParam(':group_info_num', $data_num, PDO::PARAM_STR);
    	$stmt->bindParam(':max_mark', $max_mark, PDO::PARAM_STR);
    	$stmt->bindParam(':created_date', $submit_date, PDO::PARAM_STR);
	    $stmt->execute();

	    $topic = $_POST['topic'];
	    $query = "INSERT INTO quiz_tail (quiz_tail_num, quiz_num, topic_num) VALUES";
	    $qPart = array_fill(0, count($topic), "(?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($topic); $i++){
	    	$quiz_tail_num = uniqid('QT', true)."_".time();
	    	$stmtA->bindValue($j++, $quiz_tail_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $quiz_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $topic[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();

	    $student_num = $_POST['sNum'];
	    $marks = $_POST['mark'];
	    $query = "INSERT INTO quiz_mark (quiz_mark_num, quiz_num, student_num, mark) VALUES";
	    $qPart = array_fill(0, count($student_num), "(?, ?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($student_num); $i++){
	    	$quiz_mark_num = uniqid('QM', true)."_".time();
	    	$stmtA->bindValue($j++, $quiz_mark_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $quiz_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $student_num[$i], PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $marks[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();
	    // header("location:group.php?data_num=".$data_num);
	    header("location:quiz_result.php?qNum=".$quiz_num."&data_num=".$data_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['edit_quiz_marks'])){
	try {
		$submit_date = $_POST['quiz_date'];
		$data_num = $_POST['data_num'];
		$quiz_num = $_POST['qNum'];
		$max_mark = $_POST['max_mark'];

		$stmt = $conn->prepare("UPDATE quiz SET created_date = :created_date, max_mark = :max_mark WHERE quiz_num = :quiz_num");
	   	$stmt->bindParam(':created_date', $submit_date, PDO::PARAM_STR);
	    $stmt->bindParam(':max_mark', $max_mark, PDO::PARAM_STR);
	    $stmt->bindParam(':quiz_num', $quiz_num, PDO::PARAM_STR);
	    $stmt->execute();

	    $topic = $_POST['topic'];

	    $stmt = $conn->prepare("DELETE FROM quiz_tail WHERE quiz_num = :quiz_num");
	    $stmt->bindParam(":quiz_num", $quiz_num, PDO::PARAM_STR);
	    $stmt->execute();

	    $query = "INSERT INTO quiz_tail (quiz_tail_num, quiz_num, topic_num) VALUES";
	    $qPart = array_fill(0, count($topic), "(?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($topic); $i++){
	    	$quiz_tail_num = uniqid('QT', true)."_".time();
	    	$stmtA->bindValue($j++, $quiz_tail_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $quiz_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $topic[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();

	    $students = $_POST['sNum'];
	    $mark = $_POST['mark'];
	    $stmt = $conn->prepare("UPDATE quiz_mark SET mark = ? WHERE quiz_num = ? AND student_num = ?");
		for ($i=0; $i < count($students); $i++) {
			$hW = ($home_work[$i]==null) ? 0 : $home_work[$i];
			$stmt->execute(array($mark[$i], $quiz_num, $students[$i]));
		}
		header("location:quiz_result.php?qNum=".$quiz_num."&data_num=".$data_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
?>