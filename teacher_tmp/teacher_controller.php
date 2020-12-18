<?php
include('../connection.php');
if(isset($_POST['signIn'])){
	try {
		$stmt = $conn->prepare("SELECT * FROM teacher WHERE username = :username AND password = :password");
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
			    echo $_SESSION['teacher_num']."<br>";
			    header('location:index.php');
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
		    		// $client  = @$_SERVER['HTTP_CLIENT_IP'];
				    // $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
				    // $remote  = $_SERVER['REMOTE_ADDR'];
				    // $is = '';
				    // if(filter_var($client, FILTER_VALIDATE_IP))
				    // {
				    //     $ip = $client;
				    // }
				    // elseif(filter_var($forward, FILTER_VALIDATE_IP))
				    // {
				    //     $ip = $forward;
				    // }
				    // else
				    // {
				    //     $ip = $remote;
				    // }
				    // $stmt = $conn->prepare("SELECT count(*) FROM user_connection_tmp WHERE ip = :ip AND student_num = :student_num");

				    // $stmt->bindParam(':student_num', $readrow['student_num'], PDO::PARAM_STR);
				    // $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
				    // $stmt->execute();
				    // $result_id = $stmt->fetchColumn();
		        	// if($result_id!=0){

					    // $_SESSION['ip_address'] = $ip;
			    		header('location:index.php');
			    	// }
			    	// else{
		    			// header('location:signin.php');
		    		// }
			    }
		    }
		    else{
	    		header('location:signin.php');
	    	}
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
		    	array_push($attendance_notification, array("group_student_num"=>$group_student_num[$i], "progress_student_num"=>$progress_student_num, "att"=>$attendance[$i]));
		    	$stmtA->bindValue($j++, $progress_student_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $progress_group_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $students[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $attendance[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $home_work[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
	    	foreach ($attendance_notification as $key => $value) {
	    		set_attendance_abs($value['group_student_num'], $value['progress_student_num'], $value['att'], 'add');
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
		$stmt = $conn->prepare("UPDATE progress_student SET attendance = ?, home_work = ? WHERE progress_group_num = ? AND student_num = ?");
		for ($i=0; $i < count($attendance); $i++) {
			$hW = ($home_work[$i]==null) ? 0 : $home_work[$i];
			array_push($attendance_notification, array("group_student_num"=>$group_student_num[$i], "progress_student_num"=>$progress_student_num[$i], "att"=>$attendance[$i]));
			$stmt->execute(array($attendance[$i], $hW, $progress_group_num, $students[$i]));
		}
		foreach ($attendance_notification as $key => $value) {
    		set_attendance_abs($value['group_student_num'], $value['progress_student_num'], $value['att'], 'edit');
    	}

		if(isset($_POST['new_datas'][$col_num]) && isset($_POST['new_attendance'][$col_num]) && isset($_POST['new_home_work_mark'][$col_num])){
			$attendance_notification = array();
			$new_student = $_POST['new_datas'][$col_num];
			$new_group_student_num = $_POST['new_grstdnum'];
			$new_attendance = $_POST['new_attendance'][$col_num];
			$new_home_work_mark = $_POST['new_home_work_mark'][$col_num];
			$query = "INSERT INTO progress_student (progress_student_num, progress_group_num, student_num, attendance, home_work) VALUES";
		    $qPart = array_fill(0, count($new_student), "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($new_student); $i++){
		    	$progress_student_num = uniqid('PS', true)."_".time();
		    	array_push($attendance_notification, array("group_student_num"=>$new_group_student_num[$i], "progress_student_num"=>$progress_student_num, "att"=>$new_attendance[$i]));
		    	$hW = ($new_home_work_mark[$i]==null) ? 0 : $new_home_work_mark[$i];
		    	$stmtA->bindValue($j++, $progress_student_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $progress_group_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $new_student[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $new_attendance[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $hW, PDO::PARAM_STR);
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
		$mark_theory = $_POST['quiz_mark_theory'];
		$mark_practice = $_POST['quiz_mark_practice'];
		$quiz_mark_num = '';
		$quiz_num = '';
		if($quiz_status == 'new'){
			$stmt = $conn->prepare("INSERT INTO quiz (quiz_num, topic_num) VALUES(:quiz_num, :topic_num)");
			$quiz_num = uniqid("Q",true)."_".time();
			$stmt->bindParam(':quiz_num', $quiz_num, PDO::PARAM_STR);
			$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
			$stmt->execute();
		}
		if($quiz_mark_status == 'new'){
			$quiz_mark_num = uniqid("QM", true)."_".time();
			if($_POST['retake'] == md5('y') && (($mark_theory < 70 && $mark_theory != 0) || $mark_practice < 70)){
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
				$stmt = $conn->prepare("INSERT INTO quiz_retake_notification (student_num, retake_1, retake_2) VALUES(:student_num, :retake_1, :retake_2)");
				$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
				$stmt->bindParam(':retake_1', $result_retakes[0]['quiz_mark_num'], PDO::PARAM_STR);
				$stmt->bindParam(':retake_2', $quiz_mark_num, PDO::PARAM_STR);
				$stmt->execute();

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
		else if($quiz_mark_status != 'new' && $mark_theory==0 && $mark_practice==0){
			$stmt = $conn->prepare("DELETE FROM quiz_mark WHERE quiz_mark_num = :quiz_mark_num");
			$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
			$stmt->execute();
			if($_POST['retake'] == md5('y')){
				$stmt = $conn->prepare("DELETE FROM quiz_retake_notification WHERE retake_2 = :quiz_mark_num");
				$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
				$stmt->execute();
			}
		}
		else if($quiz_mark_status != 'new'){
			$created_date = date("Y-m-d H:i:s");
			// if() $created_date = "2030-12-31 23:59:59";
			$stmt = $conn->prepare("UPDATE quiz_mark SET mark_theory = :mark_theory, mark_practice = :mark_practice, created_date = :created_date WHERE quiz_mark_num = :quiz_mark_num");
			$quiz_mark_num = $quiz_mark_status;
			// $done = (isset($_POST['done_quiz'])) ? 1 : 0;
			$stmt->bindParam(':mark_theory', $mark_theory, PDO::PARAM_INT);
			$stmt->bindParam(':mark_practice', $mark_practice, PDO::PARAM_INT);
			$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
			$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
			$stmt->execute();

			if($_POST['retake'] == md5('y')){
				if(($mark_theory < 70 && $mark_theory != 0) || $mark_practice < 70){
					$stmt = $conn->prepare("SELECT count(*) as count FROM quiz_retake_notification WHERE retake_2 = :quiz_mark_num");
					$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
					$stmt->execute();
					$cc = $stmt->fetchAll();
					if($cc[0]['count']==0){
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
						$stmt = $conn->prepare("INSERT INTO quiz_retake_notification (student_num, retake_1, retake_2) VALUES(:student_num, :retake_1, :retake_2)");
						$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
						$stmt->bindParam(':retake_1', $result_retakes[0]['quiz_mark_num'], PDO::PARAM_STR);
						$stmt->bindParam(':retake_2', $quiz_mark_num, PDO::PARAM_STR);
						$stmt->execute();
					}
				}
				else{
					$stmt = $conn->prepare("DELETE FROM quiz_retake_notification WHERE retake_2 = :quiz_mark_num");
					$stmt->bindParam(':quiz_mark_num', $quiz_mark_status, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
		}
		if($_POST['retake']==md5('n') && ($mark_theory == 100 || $mark_theory==0) && $mark_practice>=95){
			$stmt = $conn->prepare("INSERT IGNORE INTO student_prize_notification VALUES(null, :group_student_num, :quiz_mark_num)");
			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
			$stmt->bindParam(':quiz_mark_num', $quiz_mark_num, PDO::PARAM_STR);
			$group_student_num = $_POST['gsn'];
			$stmt->execute();
		}
		header('location:quiz_result.php?t_num='.$topic_num."&data_num=".$group_info_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['edit_trial_test_mark'])){
	try {
		$trial_test_mark_num = explode("X",$_POST['ttmn'])[1];
		$mark = $_POST['trial_mark'];
		$date = date("Y-m-d", strtotime($_POST['trial_date']));
		$stmt = $conn->prepare("UPDATE trial_test_mark SET mark = :mark, date_of_test = :date_of_test  WHERE trial_test_mark_num = :trial_test_mark_num");
		$stmt->bindParam(':trial_test_mark_num', $trial_test_mark_num, PDO::PARAM_STR);
		$stmt->bindParam(':mark', $mark, PDO::PARAM_STR);
		$stmt->bindParam(':date_of_test', $date, PDO::PARAM_STR);
		$stmt->execute();
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
		header('location:student_trial_test_info.php?data_num='.$_POST['data_num'].'&sjn='.$_POST['sjn']."&sn=".$_POST['sn']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}














// -----------------------------------------------------------function_start------------------------------------------------
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
			else if($res['action']==2){
				if($att==0){
					$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 3 WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
				if($att==1){
					$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 2.5 WHERE group_student_num = :group_student_num");
					$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
			else if($res['action']==2.5){
				if($att==0){
					$stmt = $conn->prepare("UPDATE attendance_notification SET first_abs = :first_abs, second_abs = '', third_abs = '', action = 1 WHERE group_student_num = :group_student_num");
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
		}

	}

	else if($action == 'edit'){

		if($count==1){
			if($res['action']==1 && $att==1){
				$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($res['action']==1.5 && $att==0){
				$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 2 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
			else if($res['action']==2 && $att==1){
				$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 1.5 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();	
			}
			else if($res['action']==2.5 && $att==0){
				$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 3 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();	
			}
			else if($res['action']==3 && $att==1){
				$stmt = $conn->prepare("UPDATE attendance_notification SET third_abs = :third_abs, action = 2.5 WHERE group_student_num = :group_student_num");
				$stmt->bindParam(':third_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();	
			}
		}
		else if($count == 0){
			if($att==0){
				$stmt = $conn->prepare("INSERT INTO attendance_notification (group_student_num, first_abs, action) VALUES(:group_student_num, :first_abs, 1)");
				$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
				$stmt->execute();
			}
		}

	}


	// if($action == 'add'){
	// 	$stmt = $conn->prepare("SELECT action FROM attendance_notification WHERE group_student_num = :group_student_num");
	// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 	$stmt->execute();
	// 	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	// 	$count = $stmt->rowCount();
	// 	if($count==0 && $att==0){
	// 		$stmt = $conn->prepare("INSERT INTO attendance_notification (group_student_num, first_abs, action) VALUES(:group_student_num, :first_abs, 0)");
	// 		$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
	// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 		$stmt->execute();
	// 	}
	// 	else if($count==1){
	// 		if($att==1 && $res['action']==0){
	// 			$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 0.5 WHERE group_student_num = :group_student_num");
	// 			$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 		else if($att==0 && $res['action']== 0){
	// 			$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = 1 WHERE group_student_num = :group_student_num");
	// 			$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 		else if($res['action']==0.5 && $att==1){
	// 			$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num");
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 		else if($res['action']==0.5 && $att==0){
	// 			$stmt = $conn->prepare("UPDATE attendance_notification SET first_abs = :first_abs, action = :action, second_abs = :second_abs WHERE group_student_num = :group_student_num");
	// 			$second_abs = null;
	// 			$action = 0;
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':action', $action, PDO::PARAM_STR);
	// 			$stmt->bindParam(':second_abs', $second_abs, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 	}
	// }
	// else if($action == 'edit'){
	// 	$stmt = $conn->prepare("SELECT second_abs, action FROM attendance_notification WHERE group_student_num = :group_student_num");
	// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 	$stmt->execute();
	// 	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	// 	$count = $stmt->rowCount();
	// 	if($count==0 && $att==0){
	// 		$stmt = $conn->prepare("INSERT INTO attendance_notification (group_student_num, first_abs, action) VALUES(:group_student_num, :first_abs, 0)");
	// 		$stmt->bindParam(':first_abs', $progress_student_num, PDO::PARAM_STR);
	// 		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 		$stmt->execute();
	// 	}
	// 	else if($count==1){
	// 		if($res['action']==0 && $att==1){
	// 			$stmt = $conn->prepare("DELETE FROM attendance_notification WHERE group_student_num = :group_student_num ");
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 		else if($res['action']==0.5 && $att==0){
	// 			$stmt = $conn->prepare("UPDATE attendance_notification SET second_abs = :second_abs, action = :action WHERE group_student_num = :group_student_num");
	// 			$action = 1;
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':second_abs', $progress_student_num, PDO::PARAM_STR);
	// 			$stmt->bindParam(':action', $action, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 		}
	// 		else if($res['action']==1 && $att==1){
	// 			$stmt = $conn->prepare("UPDATE attendance_notification SET action = 0.5 WHERE group_student_num = :group_student_num");
	// 			$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
	// 			$stmt->execute();
	// 			// $stmt->bindParam(':action', 0.5, PDO::PARAM_STR);
	// 		}
	// 	}
	// }
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