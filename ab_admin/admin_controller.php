<?php
include_once '../connection.php';
include_once '../send_sms/index.php';
if(isset($_POST['signIn'])){
	try {
		$query = "";
		$values = "";
		$stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username AND password = :password");
		$query .= "SELECT * FROM admin WHERE username = :username AND password = :password";
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$username = mb_strtolower($_POST['username']);
		$password = md5($_POST['password']);
		$values .= "[Username]: ".$_POST['username'];
		$values .= "[Password]: ".md5($_POST['password']);
	    $stmt->execute();
	   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	   	$result_count = $stmt->rowCount();
	    $count = 0;
	    if($result_count==0){
	    	header('location:signin.php');
	    }
	    else if($result_count==1){
	    	$_SESSION['adminName'] = $result['name'];
	    	$_SESSION['adminSurname'] = $result['surname'];
	    	$_SESSION['adminNum'] = $result['admin_num'];
	    	$_SESSION['role'] = md5($result['role']);
	    	writeToLog($query, $values, "Admin login");
	    	header('location:index.php');
	    }
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
else if(isset($_POST['remove_student'])){
	try {
		$query = "";
		$values = "";
		$stmt = $conn->prepare("DELETE FROM student WHERE student_num = :student_num");
		$stmt2 = $conn->prepare("DELETE FROM student_test_permission WHERE student_permission_num = (SELECT student_permission_num FROM student_permission WHERE student_num = :student_num)");
		$stmt3 = $conn->prepare("DELETE FROM student_permission WHERE student_num = :student_num");

		$query .= "DELETE FROM student WHERE student_num = :student_num"."\n";
		$query .= "DELETE FROM student_test_permission WHERE student_permission_num = (SELECT student_permission_num FROM student_permission WHERE student_num = :student_num)"."\n";
		$query .= "DELETE FROM student_permission WHERE student_num = :student_num"."\n";
		
		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt2->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt3->bindParam(':student_num',$student_num,PDO::PARAM_STR);

		$student_num = $_POST['remove-student-num'];

		$stmt->execute();
		$stmt2->execute();
		$stmt3->execute();

		$stmt = $conn->prepare("DELETE FROM progress_student WHERE student_num = :student_num");

		$query .= "DELETE FROM progress_student WHERE student_num = :student_num"."\n";

		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM quiz_mark WHERE student_num = :student_num");

		$query .= "DELETE FROM quiz_mark WHERE student_num = :student_num"."\n";

		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM review WHERE group_student_num = (SELECT group_student_num FROM group_student WHERE student_num = :student_num) ");

		$query .= "DELETE FROM review WHERE group_student_num = (SELECT group_student_num FROM group_student WHERE student_num = :student_num) "."\n";

		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->execute();
		$values = $student_num;
		writeToLog($query, $values, "Remove student");

		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
else if(isset($_POST['remove_teacher'])){
	try {
		$stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_num = :teacher_num");
		
		$stmt->bindParam(':teacher_num',$teacher_num,PDO::PARAM_STR);

		$teacher_num = $_POST['remove-teacher-num'];

		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error ".$e->getMessge()." !!!";
	}
}
else if(isset($_POST['edit_teacher'])){
	try {
		$stmt = $conn->prepare("UPDATE teacher 
								SET name = :name, 
									surname = :surname, 
									username = :username,
									dob = :dob
								WHERE teacher_num = :teacher_num");
	    $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_INT);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_INT);
	    $stmt->bindParam(':username', $username, PDO::PARAM_INT);
	    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
	    $teacher_num = $_POST['edit-teacher-num'];
	    $name = $_POST['name'];
	    $surname = $_POST['surname'];
	    $username = $_POST['username'];
	    $dob = $_POST['dob'];
	       
	    $stmt->execute();
	    header('location:index.php');
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
else if (isset($_POST['create-new-subject'])) {
	try {
		$stmt = $conn->prepare("INSERT INTO subject (subject_num, subject_name, created_date) VALUES(:subject_num, :subject_name, :created_date)");
   
	    $stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
	    $stmt->bindParam(':subject_name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);

	    $created_date = date("Y-m-d H:i:s");
	    $subject_num = uniqid('S', true)."_".time();
	    $name = $_POST['new-subject-name'];
	       
	    $stmt->execute();
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
else if (isset($_POST['create-new-topic'])) {
	try {
		$count = '1';
		$subject_num = $_POST['subject-num'];
		$stmt = $conn->prepare("SELECT topic_order FROM topic WHERE subject_num = :subject_num order by topic_order desc");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		if($stmt->rowCount()!=0){
			$res = $stmt->fetchAll();
			$count = strval(intval($res[0][0])+1);
		}
		
		if(isset($_POST['new-quiz-name'])){
			$stmt = $conn->prepare("INSERT INTO topic (topic_num, subject_num, topic_name, created_date, topic_order, quiz) VALUES(:topic_num, :subject_num, :topic_name, :created_date, :topic_order, 'y')");
	   
		    $stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		    $stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		    $stmt->bindParam(':topic_name', $name, PDO::PARAM_STR);
		    $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
		    $stmt->bindParam(':topic_order', $count, PDO::PARAM_STR);
		    $created_date = date("Y-m-d H:i:s");
		    $topic_num = uniqid('TQ', true)."_".time();
		    $name = substr_replace($_POST['new-quiz-name'], '', 0,28);
		       
		    $stmt->execute();
		}
		else{
			$stmt = $conn->prepare("INSERT INTO topic (topic_num, subject_num, topic_name, created_date, topic_order) VALUES(:topic_num, :subject_num, :topic_name, :created_date, :topic_order)");
	   
		    $stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		    $stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		    $stmt->bindParam(':topic_name', $name, PDO::PARAM_STR);
		    $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
		    $stmt->bindParam(':topic_order', $count, PDO::PARAM_STR);

		    $created_date = date("Y-m-d H:i:s");
		    $topic_num = uniqid('T', true)."_".time();
		    $name = $_POST['new-topic-name'];
		       
		    $stmt->execute();
		}
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
else if (isset($_POST['create-new-subtopic'])) {
	try {
		$count = '1';
		$topic_num = $_POST['topic-num'];
		$stmt = $conn->prepare("SELECT subtopic_order FROM subtopic WHERE topic_num = :topic_num order by subtopic_order desc");
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		$stmt->execute();
		if($stmt->rowCount()!=0){
			$res = $stmt->fetchAll();
			$count = strval(intval($res[0][0])+1);
		}

		$stmt = $conn->prepare("INSERT INTO subtopic (subtopic_num, topic_num, subtopic_name, created_date, subtopic_order) VALUES(:subtopic_num, :topic_num, :subtopic_name, :created_date, :subtopic_order)");
   
	    $stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
	    $stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
	    $stmt->bindParam(':subtopic_name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
	    $stmt->bindParam(':subtopic_order', $count, PDO::PARAM_STR);

	    $created_date = date("Y-m-d H:i:s");
	    $subtopic_num = uniqid('S_T', true)."_".time();
	    $name = $_POST['new-subtopic-name'];
	       
	    $stmt->execute();
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
else if(isset($_POST['student-test-permission'])){
	try {
		$stmt = $conn->prepare("INSERT IGNORE INTO student_permission (student_permission_num, student_num) VALUES(:student_permission_num, :student_num) ");
 
	    $stmt->bindParam(':student_permission_num', $student_permission_num, PDO::PARAM_STR);
	    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);

	    $student_permission_num = uniqid('S_P', true)."_".time();
	    $student_num = $_POST['student-test-permission-student-num'];

	    $stmt->execute();
	    echo $student_num;

	    $stmt_check = $conn->prepare("SELECT stp.student_permission_num studentPermissionNum FROM student_test_permission stp, student_permission sp WHERE sp.student_num = :student_num AND stp.student_permission_num = sp.student_permission_num AND stp.subtopic_num = :subtopic_num");

	    $stmt_check->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	    $stmt_check->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);

	    $subtopic_num = $_POST['student-test-permission-subtopic-num'];
	    $video_permission = isset($_POST['video-subtopic']) ? "t" : "f";
	    if($video_permission == 'f'){
	    	$test_permission = 'f';	
	    }
	    else{
	    	$test_permission = isset($_POST['test-subtopic']) ? "t" : "f";
		}

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
	    header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['edit_group_info'])){
	try {
		$stmt = $conn->prepare("UPDATE group_info SET group_name = :group_name, comment = :comment WHERE group_info_num = :group_info_num");

	   	$stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
	   	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	   	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);

	   	$group_name = $_POST['group_name'];
	   	// $teacher_num = $_POST['group_teacher'];
	   	$comment = $_POST['group_comment'];
	   	$group_info_num = $_POST['data_num'];
		       
	    $stmt->execute();
	    header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['delet_group_info'])){
	try {
		$stmt = $conn->prepare("DELETE FROM group_info WHERE group_info_num = :group_info_num");
		
		$stmt->bindParam(':group_info_num',$group_info_num,PDO::PARAM_STR);

		$group_info_num = $_POST['data_num'];

		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM group_student WHERE group_info_num = :group_info_num");
		
		$stmt->bindParam(':group_info_num',$group_info_num,PDO::PARAM_STR);

		$stmt->execute();

		$stmt = $conn->prepare("DELETE pg, ps FROM progress_group pg JOIN progress_student ps ON pg.progress_group_num = ps.progress_group_num WHERE pg.group_info_num = :group_info_num");
		$stmt->bindParam(':group_info_num',$group_info_num,PDO::PARAM_STR);
		$stmt->execute();		

		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['add_to_group'])){
	try {
		$data_num = $_POST['data_num'];
		$students = $_POST['students_to_group'];
		$start_date = date("Y-m-d");

		$query = "INSERT INTO group_student (group_student_num, group_info_num, student_num, start_date) VALUES";
	    $qPart = array_fill(0, count($students), "(?, ?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    $dataMessage1 = '';
	    $dataMessage2 = '';

	    $student_and_group_student_nums = array();

	    for($i = 0; $i<count($students); $i++){
	    	$group_student_num = uniqid('GS', true)."_".time();
	    	$stmtA->bindValue($j++, $group_student_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $data_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $students[$i], PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $start_date, PDO::PARAM_STR);
	    	$dataMessage1 .= "[".$group_student_num."] ";
	    	$dataMessage2 .= "[".$students[$i]."] ";

	    	$student_and_group_student_nums[$students[$i]] = $group_student_num;
	    }
	    $stmtA->execute();

	    $stmt = $conn->prepare("SELECT gi.subject_num FROM group_info gi WHERE gi.group_info_num = :group_info_num");
	    $stmt->bindParam(":group_info_num", $data_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $subject_num = $stmt->fetch(PDO::FETCH_ASSOC)['subject_num'];

	    $period = date("Y-m-").'01';
	    $status = "in";
	    $query = "INSERT INTO statistics_student_frequency (student_num, subject_num, group_student_num, status, period) VALUES";
	    $qPart = array_fill(0, count($student_and_group_student_nums), "(?, ?, ?, ?, ?)");
	    $query .= implode(",", $qPart);
	    $stmt = $conn->prepare($query);
	    $j = 1;
	    foreach ($student_and_group_student_nums as $key => $value) {
	    	$stmt->bindParam($j++, $key, PDO::PARAM_STR);
	    	$stmt->bindParam($j++, $subject_num, PDO::PARAM_STR);
	    	$stmt->bindParam($j++, $value, PDO::PARAM_STR);
	    	$stmt->bindParam($j++, $status, PDO::PARAM_STR);
	    	$stmt->bindParam($j++, $period, PDO::PARAM_STR);
	    }
	    $stmt->execute();
	    print_r($student_and_group_student_nums);

	    $stmt = $conn->prepare("SELECT count(csf.id) AS c,
	    							gs.student_num,
	    							csf.in_progress
	    						FROM group_info gi,
	    							group_student gs,
	    							course_started_flag csf
	    						WHERE csf.subject_num = gi.subject_num
	    							AND csf.student_num = gs.student_num
	    							AND gi.group_info_num = gs.group_info_num
	    							AND gi.group_info_num = :group_info_num
	    							AND gs.student_num IN ("."'".implode("','", $students)."'".")
	    						GROUP BY gs.student_num");
	    $stmt->bindParam(":group_info_num", $data_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $csf_query_result = $stmt->fetchAll();

	    $csf_insert_students = $students;

	    foreach ($csf_query_result as $value) {
	    	if (($key = array_search($value['student_num'], $csf_insert_students)) !== false) {
	    		unset($csf_insert_students[$key]);
	    	}
	    }

	    if (count($csf_insert_students) > 0) {
	    	$stmt = $conn->prepare("INSERT INTO course_started_flag (subject_num, student_num, in_progress)
		    							SELECT gi.subject_num, s.student_num, '0'
		    							FROM group_info gi,
		    								student s
		    							WHERE gi.group_info_num = :group_info_num
		    								AND s.student_num IN ("."'".implode("','", $csf_insert_students)."'".")");
		    $stmt->bindParam(":group_info_num", $data_num, PDO::PARAM_STR);
		    $stmt->execute();
	    }
	    
	    header('location:group.php?data_num='.$data_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['remove_from_group'])){
	try {
		$student_num = $_POST['data_num'];
		$group_info_num = $_POST['extra_num'];
		$stmt = $conn->prepare("DELETE FROM group_student WHERE student_num = :student_num AND group_info_num = :group_info_num");
		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->bindParam(':group_info_num',$group_info_num,PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE sp, stp FROM student_permission sp JOIN student_test_permission stp ON sp.student_permission_num = stp.student_permission_num WHERE sp.student_num = :student_num");
		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE ps FROM progress_student ps JOIN progress_group pg ON pg.progress_group_num = ps.progress_group_num WHERE pg.group_info_num = :group_info_num AND ps.student_num = :student_num");
		$stmt->bindParam(':student_num',$student_num,PDO::PARAM_STR);
		$stmt->bindParam(':group_info_num',$group_info_num,PDO::PARAM_STR);
		$stmt->execute();

		header('location:group.php?data_num='.$_POST['extra_num']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['delete_subject'])){
	try {
		$data_num = $_POST['data_num'];
		$stmt = $conn->prepare("DELETE s, t, st FROM subject s JOIN topic t ON t.subject_num = s.subject_num JOIN subtopic st ON st.topic_num = t.topic_num WHERE s.subject_num = :subject_num");
		$stmt->bindParam(':subject_num',$data_num,PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_data_modal_topic'])){
	try {
		$deleted = isset($_POST['deleted']) ? $_POST['deleted'] : 'false';
		if($deleted!='false'){
			$query = 'DELETE FROM subtopic WHERE topic_num = ';
			$qPart = array_fill(0, count($deleted), "?");
		    $query .= implode(" OR topic_num = ",$qPart);
		    print_r($query);
		    echo "<br>";
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    $dataMessage = '';
		    for($i = 0; $i<count($deleted); $i++){
		    	$stmt->bindValue($j++, $deleted[$i], PDO::PARAM_STR);
		    	$dataMessage .= "[".$deleted[$i]."] ";
		    	// echo $deleted[$i]."<br>";
		    }
		    $stmt->execute();

		    $query = 'DELETE FROM topic WHERE topic_num = ';
			$qPart = array_fill(0, count($deleted), "?");
		    $query .= implode(" OR topic_num = ",$qPart);
		    print_r($query);
		    echo "<br>";
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    $dataMessage = '';
		    for($i = 0; $i<count($deleted); $i++){
		    	$stmt->bindValue($j++, $deleted[$i], PDO::PARAM_STR);
		    	$dataMessage .= "[".$deleted[$i]."] ";
		    	// echo $deleted[$i]."<br>";
		    }
		    $stmt->execute();
		}
		$data_num = isset($_POST['data_num']) ? $_POST['data_num'] : 'false';
		$data_name = isset($_POST['data_name']) ? $_POST['data_name'] : 'false';
		if($data_num!='false' && $data_name!='false'){
			$stmt = $conn->prepare("UPDATE topic SET topic_name = ?, topic_order = ? WHERE topic_num = ?");
			for ($i=0; $i < count($data_num); $i++) {
				$stmt->execute(array($data_name[$i], ($i+1), $data_num[$i]));
			}
		}
		header("location:index.php");
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_data_modal_subtopic'])){
	try {
		$deleted = isset($_POST['deleted']) ? $_POST['deleted'] : 'false';
		if($deleted!='false'){
			$query = 'DELETE FROM subtopic WHERE subtopic_num = ';
			$qPart = array_fill(0, count($deleted), "?");
		    $query .= implode(" OR subtopic_num = ",$qPart);
		    print_r($query);
		    echo "<br>";
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($deleted); $i++){
		    	$stmt->bindValue($j++, $deleted[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		}
		$data_num = isset($_POST['data_num']) ? $_POST['data_num'] : 'false';
		$data_name = isset($_POST['data_name']) ? $_POST['data_name'] : 'false';
		if($data_num!='false' && $data_name!='false'){
			$stmt = $conn->prepare("UPDATE subtopic SET subtopic_name = ?, subtopic_order = ? WHERE subtopic_num = ?");
			for ($i=0; $i < count($data_num); $i++) {
				$stmt->execute(array($data_name[$i], ($i+1), $data_num[$i]));
			}
		}
		header("location:index.php");
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['edit_subject'])){
	try {
		$stmt = $conn->prepare("UPDATE subject SET subject_name = :subject_name WHERE subject_num = :subject_num");
		$stmt->bindParam(':subject_name',$_POST['data_name'],PDO::PARAM_STR);
		$stmt->bindParam(':subject_num',$_POST['data_num'],PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['unblock_student'])){
	try {
		$student_num = $_POST['data_num'];
		$block = 0;
		$stmt = $conn->prepare("UPDATE student SET block = :block WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':block', $block, PDO::PARAM_INT);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['transfer_student'])){
	try {
		echo "1";
		$new_group_num = $_POST['new_gr'];
		$student_num = $_POST['std_num'];
		$old_group_num = $_POST['gr_num'];
		$transfer_num = uniqid("TR",true)."_".time();
		echo '1.5';
		$count = 0;
		$stmt = $conn->prepare("SELECT count(*) n FROM transfer WHERE new_group_info_num = :new_group_info_num AND old_group_info_num = :old_group_info_num AND student_num = :student_num");
		$stmt->bindParam(':new_group_info_num', $new_group_num, PDO::PARAM_STR);
		$stmt->bindParam(':old_group_info_num', $old_group_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$count = $stmt->fetch(PDO::FETCH_ASSOC)['n'];
		echo "<br>";
		echo $count;
		echo "<br>";
		if($count==0){
			$stmt = $conn->prepare("INSERT INTO transfer (transfer_num, new_group_info_num, old_group_info_num, student_num) VALUES(:transfer_num, :new_group_num, :old_group_num, :student_num) ");
			$stmt->bindParam(':transfer_num', $transfer_num, PDO::PARAM_STR);
			$stmt->bindParam(':new_group_num', $new_group_num, PDO::PARAM_STR);
			$stmt->bindParam(':old_group_num', $old_group_num, PDO::PARAM_STR);
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();
		}
		$stmt = $conn->prepare("UPDATE group_student SET group_info_num = :new_group_info_num WHERE student_num = :student_num AND group_info_num = :old_group_info_num");
		$stmt->bindParam(':new_group_info_num', $new_group_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':old_group_info_num', $old_group_num, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("UPDATE quiz SET group_info_num = :group_info_num WHERE group_info_num");

		echo "3";
		$stmt = $conn->prepare('SELECT group_name FROM group_info WHERE group_info_num = :group_info_num');
		$stmt->bindParam(':group_info_num', $new_group_num, PDO::PARAM_STR);
		$stmt->execute();
		$new_group_name = $stmt->fetch(PDO::FETCH_ASSOC);
		$_SESSION['n'] = 'true';
		header("location:group.php?data_num=".$old_group_num."&transfer=".$new_group_name['group_name']);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['comment_for_teacher'])){
	try {
		if(isset($_POST['new_review'])){
			$review_text = $_POST['new_review'];
			$query = "INSERT INTO review_info (review_info_num, review_text) VALUES";
		    $qPart = array_fill(0, count($review_text), "(?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    $dataMessage1 = '';
		    $dataMessage2 = '';
		    for($i = 0; $i<count($review_text); $i++){
		    	$review_info_num = uniqid('RI', true)."_".time();
		    	$stmtA->bindValue($j++, $review_info_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $review_text[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		}
		if(isset($_POST['review_comment'])){
			$comment_text = $_POST['review_comment'];
			$comment_num = $_POST['review_comment_num'];
			$stmt = $conn->prepare("UPDATE review_info SET review_text = :review_text WHERE review_info_num = :review_info_num");
			$stmt->bindParam(':review_text', $comment_text, PDO::PARAM_STR);
			$stmt->bindParam(':review_info_num', $comment_num, PDO::PARAM_STR);
			$stmt->execute();
		}
		if(isset($_POST['rin_remove'])){
			$review_info_num = $_POST['rin_remove'];

			$query = 'DELETE FROM review WHERE review_info_num = ';
			$qPart = array_fill(0, count($review_info_num), "?");
		    $query .= implode(" OR review_info_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($review_info_num); $i++){
		    	$stmt->bindValue($j++, $review_info_num[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();

		    $query = 'DELETE FROM review_info WHERE review_info_num = ';
			$qPart = array_fill(0, count($review_info_num), "?");
		    $query .= implode(" OR review_info_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($review_info_num); $i++){
		    	$stmt->bindValue($j++, $review_info_num[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		}
		if(isset($_POST['update_review'])){
			$review_text = $_POST['update_review'];
			$review_num = $_POST['rin_update'];
			$stmt = $conn->prepare("UPDATE review_info SET review_text = ? WHERE review_info_num = ?");
			for ($i=0; $i < count($review_num); $i++) {
				$stmt->execute(array($review_text[$i], $review_num[$i]));
			}
		}
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}

else if(isset($_POST['reason_for_student'])){
	try {
		if(isset($_POST['new_reason'])){
			$reason_text = $_POST['new_reason'];
			$query = "INSERT INTO reason_info (reason_info_num, reason_text) VALUES";
		    $qPart = array_fill(0, count($reason_text), "(?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    $dataMessage1 = '';
		    $dataMessage2 = '';
		    for($i = 0; $i<count($reason_text); $i++){
		    	$reason_info_num = uniqid('RI', true)."_".time();
		    	$stmtA->bindValue($j++, $reason_info_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $reason_text[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		}
		if(isset($_POST['rin_remove'])){
			$reason_info_num = $_POST['rin_remove'];

			$query = 'DELETE FROM student_reason WHERE reason_info_num = ';
			$qPart = array_fill(0, count($reason_info_num), "?");
		    $query .= implode(" OR reason_info_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($reason_info_num); $i++){
		    	$stmt->bindValue($j++, $reason_info_num[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();

		    $query = 'DELETE FROM reason_info WHERE reason_info_num = ';
			$qPart = array_fill(0, count($reason_info_num), "?");
		    $query .= implode(" OR reason_info_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($reason_info_num); $i++){
		    	$stmt->bindValue($j++, $reason_info_num[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		}
		if(isset($_POST['update_reason'])){
			$reason_text = $_POST['update_reason'];
			$reason_num = $_POST['rin_update'];
			$stmt = $conn->prepare("UPDATE reason_info SET reason_text = ? WHERE reason_info_num = ?");
			for ($i=0; $i < count($reason_num); $i++) {
				$stmt->execute(array($reason_text[$i], $reason_num[$i]));
			}
		}
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}

else if(isset($_POST['submit_prize_notification'])){
	try {
		if(isset($_POST['spn'])){
			$id = $_POST['spn'];
			$query = 'UPDATE student_prize_notification SET status = "D", deleted_date = NOW() WHERE id = ';
			$qPart = array_fill(0, count($id), "?");
		    $query .= implode(" OR id = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($id); $i++){
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();

		    $query = "INSERT INTO chocolate (object_id, object_num) VALUES ";
		    $qPart = array_fill(0, count($id), "(?, ?)");
		    $query .= implode(",",$qPart);
		    $stmt = $conn->prepare($query);
		    $object_id = 1;
		    $j = 1;
		    for($i = 0; $i<count($id); $i++) {
		    	$stmt->bindValue($j++, $object_id, PDO::PARAM_INT);
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();

		    $chocolate_quantity = count($id);
		    $stmt = $conn->prepare("UPDATE chocolate_history ch1 
	    							INNER JOIN chocolate_history ch2
	    								ON ch2.id = ch1.id
	    							SET ch1.quantity = ch2.quantity-:quantity
		    						WHERE ch1.id = 1");
		    $stmt->bindValue(":quantity", $chocolate_quantity, PDO::PARAM_INT);
		    $stmt->execute();

		}
	    header("location:index.php");
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
} else if (isset($_POST['submit_quiz_max_mark_notification'])) {
	try {
		$notification_ids = array();
		$chocolate_ids = array();

		$ids = $_POST['nid'];
		$edit = $_POST['edit'];
		$discount = $_POST['discount'];
		$chocolate = $_POST['chocolate'];
		$stmt = $conn->prepare("UPDATE notification SET status = ?, deleted_date = NOW() WHERE status != 'D' AND id = ? ");
		for ($i = 0; $i < count($edit); $i++) {
			if ($edit[$i] > 0) {
				$res_status = '';
				if ($discount[$i] == 'D' || $discount[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}

				if ($chocolate[$i] == 'D' || $chocolate[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}
				$res_status = $res_status == "DD" ? "D" : $res_status;
				if ($discount[$i] == "D") {
					array_push($notification_ids, $ids[$i]);
				}
				if ($chocolate[$i] == "D") {
					array_push($chocolate_ids, $ids[$i]);
				}
				$stmt->execute(array($res_status, $ids[$i]));
			}
		}

		if (count($chocolate_ids) > 0) {
			$query = "INSERT INTO chocolate (object_id, object_num) VALUES ";
			$qPart = array_fill(0, count($chocolate_ids), "(?, ?)");
			$query .= implode(",", $qPart);
			$stmt = $conn->prepare($query);
			$object_id = 6;
			$j = 1;
			for ($i = 0; $i<count($chocolate_ids); $i++) {
				$stmt->bindValue($j++, $object_id, PDO::PARAM_INT);
				$stmt->bindValue($j++, $chocolate_ids[$i], PDO::PARAM_STR);
			}
			$stmt->execute();

			$chocolate_quantity = count($chocolate_ids);
			$stmt = $conn->prepare("UPDATE chocolate_history ch1
									INNER JOIN chocolate_history ch2
										ON ch2.id = ch1.id 
									SET ch1.quantity = ch2.quantity-:quantity
									WHERE ch1.id = 1");
			$stmt->bindValue(":quantity", $chocolate_quantity, PDO::PARAM_INT);
			$stmt->execute();
		}

		if (count($notification_ids) > 0) {
		    $query = "SELECT s.name, 
		    			s.surname,
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						quiz_mark qm,
						notification n,
						quiz q,
						topic t,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = qm.student_num  
						AND qm.quiz_mark_num = n.object_num
						AND q.quiz_num = qm.quiz_num
						AND t.topic_num = q.topic_num
						AND sj.subject_num = t.subject_num
						AND n.id IN (";
			$query .= implode(",", $notification_ids);
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($QUIZ_MAX_MARK, $value['name'], $value['subject_name'], "10%"))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}
			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo "ERROR ".$save_sms_res;
			}
		} else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		throw $e;
	}
} else if (isset($_POST['submit_quiz_max_mark_2_notification'])) {
	try {
		$notification_ids = array();
		$chocolate_ids = array();

		$ids = $_POST['nid'];
		$edit = $_POST['edit'];
		$discount = $_POST['discount'];
		$chocolate = $_POST['chocolate'];
		$stmt = $conn->prepare("UPDATE notification SET status = ?, deleted_date = NOW() WHERE status != 'D' AND id = ? ");
		for ($i = 0; $i < count($edit); $i++) {
			if ($edit[$i] > 0) {
				$res_status = '';
				if ($discount[$i] == 'D' || $discount[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}

				if ($chocolate[$i] == 'D' || $chocolate[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}
				$res_status = $res_status == "DD" ? "D" : $res_status;
				if ($discount[$i] == "D") {
					array_push($notification_ids, $ids[$i]);
				}
				if ($chocolate[$i] == "D") {
					array_push($chocolate_ids, $ids[$i]);
				}
				$stmt->execute(array($res_status, $ids[$i]));
			}
		}

		if (count($chocolate_ids) > 0) {
			$query = "INSERT INTO chocolate (object_id, object_num) VALUES ";
			$qPart = array_fill(0, count($chocolate_ids), "(?, ?)");
			$query .= implode(",", $qPart);
			$stmt = $conn->prepare($query);
			$object_id = 7;
			$j = 1;
			for ($i = 0; $i<count($chocolate_ids); $i++) {
				$stmt->bindValue($j++, $object_id, PDO::PARAM_INT);
				$stmt->bindValue($j++, $chocolate_ids[$i], PDO::PARAM_STR);
			}
			$stmt->execute();

			$chocolate_quantity = count($chocolate_ids);
			$stmt = $conn->prepare("UPDATE chocolate_history ch1
									INNER JOIN chocolate_history ch2
										ON ch2.id = ch1.id 
									SET ch1.quantity = ch2.quantity-:quantity
									WHERE ch1.id = 1");
			$stmt->bindValue(":quantity", $chocolate_quantity, PDO::PARAM_INT);
			$stmt->execute();
		}

		if (count($notification_ids) > 0) {
		   $query = "SELECT s.name,
		   				s.surname, 
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						quiz_mark qm,
						notification n,
						quiz q,
						topic t,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = qm.student_num  
						AND qm.quiz_mark_num = n.object_num
						AND q.quiz_num = qm.quiz_num
						AND t.topic_num = q.topic_num
						AND sj.subject_num = t.subject_num
						AND n.id IN (";
			$query .= implode(",", $notification_ids);
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($QUIZ_MAX_MARK_2, $value['name'], $value['subject_name'], "5%"))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}
			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}
		} else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		throw $e;
	}
} else if(isset($_POST['submit_trial_test_top_notification'])) {
	try {
		$notification_ids = array();
		$chocolate_ids = array();

		$ids = $_POST['nid'];
		$edit = $_POST['edit'];
		$discount = $_POST['discount'];
		$chocolate = $_POST['chocolate'];
		$stmt = $conn->prepare("UPDATE notification SET status = ?, deleted_date = NOW() WHERE status != 'D' AND id = ? ");
		for ($i = 0; $i < count($edit); $i++) {
			if ($edit[$i] > 0) {
				$res_status = '';
				if ($discount[$i] == 'D' || $discount[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}

				if ($chocolate[$i] == 'D' || $chocolate[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}
				$res_status = $res_status == "DD" ? "D" : $res_status;
				if ($discount[$i] == "D") {
					array_push($notification_ids, $ids[$i]);
				}
				if ($chocolate[$i] == "D") {
					array_push($chocolate_ids, $ids[$i]);
				}
				$stmt->execute(array($res_status, $ids[$i]));
			}
		}

		if (count($chocolate_ids) > 0) {

			$query = "INSERT INTO chocolate (object_id, object_num) VALUES ";
			$qPart = array_fill(0, count($chocolate_ids), "(?, ?)");
			$query .= implode(",", $qPart);
			$stmt = $conn->prepare($query);
			$object_id = 4;
			$j = 1;
			for ($i = 0; $i<count($chocolate_ids); $i++) {
				$stmt->bindValue($j++, $object_id, PDO::PARAM_INT);
				$stmt->bindValue($j++, $chocolate_ids[$i], PDO::PARAM_STR);
			}
			$stmt->execute();

			$chocolate_quantity = count($chocolate_ids);
			$stmt = $conn->prepare("UPDATE chocolate_history ch1
									INNER JOIN chocolate_history ch2
										ON ch2.id = ch1.id 
									SET ch1.quantity = ch2.quantity-:quantity
									WHERE ch1.id = 1");
			$stmt->bindValue(":quantity", $chocolate_quantity, PDO::PARAM_INT);
			$stmt->execute();
		}

		if (count($notification_ids) > 0) {
		    $query = "SELECT s.name,
		    			s.surname, 
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						trial_test tt,
						trial_test_mark ttm,
						notification n,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = tt.student_num 
						AND tt.trial_test_num = ttm.trial_test_num 
						AND ttm.trial_test_mark_num = n.object_num
						AND sj.subject_num = tt.subject_num
						AND n.id IN (";
			$query .= implode(",", $notification_ids);
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($TRIAL_TEST_MAX_MARK, $value['name'], $value['subject_name'], "10%"))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}
			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}
		} else {
			header("location:index.php");
		}

	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if (isset($_POST['submit_trial_test_increase_notification'])) {
	try {
		$notification_ids = array();
		$chocolate_ids = array();

		$ids = $_POST['nid'];
		$object_parent_num = $_POST['opn'];
		$edit = $_POST['edit'];
		$discount = $_POST['discount'];
		$chocolate = $_POST['chocolate'];
		$stmt = $conn->prepare("UPDATE notification SET status = ?, deleted_date = NOW() WHERE status != 'D' AND object_parent_num = ? ");
		for ($i = 0; $i < count($edit); $i++) {
			if ($edit[$i] > 0) {
				$res_status = '';
				if ($discount[$i] == 'D' || $discount[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}

				if ($chocolate[$i] == 'D' || $chocolate[$i] == 'Deleted') {
					$res_status .= 'D';
				} else {
					$res_status .= 'A';
				}
				$res_status = $res_status == "DD" ? "D" : $res_status;
				if ($discount[$i] == "D") {
					array_push($notification_ids, $ids[$i]);
				}
				if ($chocolate[$i] == "D") {
					array_push($chocolate_ids, $ids[$i]);
				}
				$stmt->execute(array($res_status, $object_parent_num[$i]));
			}
		}

		if (count($chocolate_ids) > 0) {

			$query = "INSERT INTO chocolate (object_id, object_num) VALUES ";
			$qPart = array_fill(0, count($chocolate_ids), "(?, ?)");
			$query .= implode(",", $qPart);
			$stmt = $conn->prepare($query);
			$object_id = 5;
			$j = 1;
			for ($i = 0; $i<count($chocolate_ids); $i++) {
				$stmt->bindValue($j++, $object_id, PDO::PARAM_INT);
				$stmt->bindValue($j++, $chocolate_ids[$i], PDO::PARAM_STR);
			}
			$stmt->execute();

			$chocolate_quantity = count($chocolate_ids);
			$stmt = $conn->prepare("UPDATE chocolate_history ch1
									INNER JOIN chocolate_history ch2
										ON ch2.id = ch1.id 
									SET ch1.quantity = ch2.quantity-:quantity
									WHERE ch1.id = 1");
			$stmt->bindValue(":quantity", $chocolate_quantity, PDO::PARAM_INT);
			$stmt->execute();
		}

		if (count($notification_ids) > 0) {
			$query = "SELECT s.name,
						s.surname, 
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						trial_test tt,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = tt.student_num 
						AND sj.subject_num = tt.subject_num
						AND tt.trial_test_num IN (";
			$query .= "'".implode("','", $notification_ids)."'";
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($TRIAL_TEST_INCREASE_MARK_3_TIMES, $value['name'], $value['subject_name'], "10%"))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}

			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}

		}
		else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if (isset($_POST['submit_no_home_work_notification'])) {
	try {
		if(isset($_POST['spn'])){
			$arr = $_POST['spn'];

			$query = "SELECT ps.student_num,
						MONTH(pg.created_date) AS month,
						DAY(pg.created_date) AS day
					FROM notification n,
						progress_student ps,
						progress_group pg
					WHERE n.object_num = ps.progress_student_num
						AND pg.progress_group_num = ps.progress_group_num
						AND n.id IN (";

			for ($i = 0; $i < count($arr); $i++) {
				$ipArr = explode('|', $arr[$i]);
				$tmp = $i==0 ? "" : ", ";
				$query .= $tmp.$ipArr[0];
			}
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$student_progress_dates_query_result = $stmt->fetchAll();

			$student_progress_dates = array();

			foreach ($student_progress_dates_query_result as $value) {
				$student_progress_dates[$value['student_num']] = array("month" => $value['month'], "day" => $value['day']);
			}

			$stmt = $conn->prepare("UPDATE notification SET status = 'D', deleted_date = NOW() WHERE id = ? AND object_parent_num = ? AND object_id = 8");
			for ($i = 0; $i < count($arr); $i++) {
				$ipArr = explode('|', $arr[$i]);
				$stmt->execute(array($ipArr[0], $ipArr[1]));
			}
			
			$group_student_nums = array();
		    foreach ($arr as $value) {
		    	array_push($group_student_nums, explode('|', $value)[1]);
		    }
			$query = "SELECT s.student_num, 
						s.name, 
						s.surname,
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						group_student gs,
						group_info gi,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = gs.student_num
						AND gi.group_info_num = gs.group_info_num
						AND sj.subject_num = gi.subject_num 
						AND gs.group_student_num IN (";
			$query .= "'".implode("','", $group_student_nums)."'";
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$date = $student_progress_dates[$value['student_num']]['day']." ".$MONTHS[intval($student_progress_dates[$value['student_num']]['month'])];
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($NO_HOME_WORK, $value['name'], $value['subject_name'], $date))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}

			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}
		} else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_attendance_notification'])){
	try {
		if(isset($_POST['ann'])){
			$id = $_POST['ann'];

			$attendance_notification_nums = implode(",", $id);
			$stmt = $conn->prepare("SELECT an.group_student_num,
										gs.student_num,
										(SELECT DATE_FORMAT(pg.created_date, '%d.%m')
										FROM progress_group pg,
											progress_student ps
										WHERE pg.progress_group_num = ps.progress_group_num
											AND ps.progress_student_num = an.first_abs) AS first_abs,
										(SELECT DATE_FORMAT(pg.created_date, '%d.%m')
											FROM progress_group pg,
												progress_student ps
											WHERE pg.progress_group_num = ps.progress_group_num
												AND ps.progress_student_num = an.second_abs) AS second_abs,
										(SELECT DATE_FORMAT(pg.created_date, '%d.%m')
											FROM progress_group pg,
												progress_student ps
											WHERE pg.progress_group_num = ps.progress_group_num
												AND ps.progress_student_num = an.third_abs) AS third_abs
									FROM attendance_notification an,
										group_student gs
									WHERE gs.group_student_num = an.group_student_num
										AND an.attendance_notification_num IN (".$attendance_notification_nums.")");
			$stmt->execute();
			$group_student_nums = array();
			$progress_students = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($group_student_nums, $value['group_student_num']);
				$first_abs = explode('.', $value['first_abs']);
				$second_abs = explode('.', $value['second_abs']);
				// $third_abs = explode('.', $value['third_abs']);
				$progress_students[$value['student_num']] = array(
					$first_abs[0]." ".$MONTHS[intval($first_abs[1])],
					$second_abs[0]." ".$MONTHS[intval($second_abs[1])]
					// $third_abs[0]." ".$MONTHS[intval($third_abs[1])]
				);
			}

			$query = 'DELETE FROM attendance_notification WHERE attendance_notification_num = ';
			$qPart = array_fill(0, count($id), "?");
		    $query .= implode(" OR attendance_notification_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($id); $i++){
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();



		    $query = "SELECT s.student_num, 
		    			s.name, 
		    			s.surname,
						p.phone,
						sj.subject_name
					FROM student s,
						parent p,
						group_student gs,
						group_info gi,
						subject sj
					WHERE p.parent_order = 1
						AND p.student_num = s.student_num
						AND s.student_num = gs.student_num 
						AND gi.group_info_num = gs.group_info_num
						AND sj.subject_num = gi.subject_num
						AND gs.group_student_num IN (";
			$query .= "'".implode("','", $group_student_nums)."'";
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();
			$sms_result = array();
			foreach ($query_data as $value) {
				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($ABSENT_2_TIMES, $value['name'], $value['subject_name'],
						$progress_students[$value['student_num']][0], 
						$progress_students[$value['student_num']][1]
						// $progress_students[$value['student_num']][2]
					))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}
			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}
		} else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_quiz_retake_notification'])){
	try {
		if(isset($_POST['quizRetakeNot'])){
			$id = $_POST['quizRetakeNot'];

			$query = "SELECT qzn.student_num,
						sj.subject_name,
						q.quiz_num
					FROM quiz_retake_notification qzn,
						subject sj,
						topic t,
						quiz_mark qm,
						quiz q
					WHERE qm.quiz_mark_num = qzn.retake_1
						AND q.quiz_num = qm.quiz_num
						AND t.topic_num = q.topic_num
						AND sj.subject_num = t.subject_num 
						AND qzn.id IN (".implode(",", $id).")";
			$stmt = $conn->prepare($query);
			$stmt->execute();

			$student_nums = array();
			$sms_text_data = array();
		    foreach ($stmt->fetchAll() as $value) {
		    	array_push($student_nums, $value['student_num']);
		    	$sms_text_data[$value['student_num']]['quiz_num'] = $value['quiz_num'];
		    	$sms_text_data[$value['student_num']]['subject_name'] = $value['subject_name'];
		    	$sms_text_data[$value['student_num']]['practice'] = array();
		    	$sms_text_data[$value['student_num']]['theory'] = array();
		    	$sms_text_data[$value['student_num']]['is_theory'] = false;
		    	$sms_text_data[$value['student_num']]['is_practice'] = false;
		    }

		    $query = "SELECT qm.student_num, 
		    				qm.quiz_num, 
		    				qm.mark_practice,
		    				qm.mark_theory,
		    				csq.practice,
		    				csq.theory
		    		FROM quiz_mark qm,
		    			config_subject_quiz csq,
		    			quiz q,
		    			topic t
		    		WHERE ";

		    $query_helper = array();
		    foreach ($sms_text_data as $key => $value) {
		    	$tmp = "(q.quiz_num = '".$value['quiz_num']."' AND qm.quiz_num = q.quiz_num AND qm.student_num = '".$key."' AND t.topic_num = q.topic_num AND csq.subject_num = t.subject_num)";
		    	array_push($query_helper, $tmp);
		    }
		    $query .= implode(" OR ", $query_helper)."ORDER BY qm.created_date ASC";
		    $stmt = $conn->prepare($query);
		    $stmt->execute();
		    $quiz_marks_res = $stmt->fetchAll();

		    foreach ($quiz_marks_res as $value) {
		    	array_push($sms_text_data[$value['student_num']]['practice'], $value['mark_practice']);
		    	array_push($sms_text_data[$value['student_num']]['theory'], $value['mark_theory']);
		    	$sms_text_data[$value['student_num']]['is_theory'] = $value['theory'] == 1 ? true : false;
		    	$sms_text_data[$value['student_num']]['is_practice'] = $value['practice'] == 1 ? true : false;
		    }


			$query = 'DELETE FROM quiz_retake_notification WHERE id = ';
			$qPart = array_fill(0, count($id), "?");
		    $query .= implode(" OR id = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($id); $i++){
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		

			$query = "SELECT s.student_num, 
						s.name,
						s.surname, 
						p.phone
					FROM student s,
						parent p
					WHERE p.parent_order = 1 
						AND p.student_num = s.student_num
						AND s.student_num IN (";
			$query .= "'".implode("','", $student_nums)."'";
			$query .= ")";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$query_data = $stmt->fetchAll();

			$sms_result = array();
			foreach ($query_data as $value) {
				$data_tmp = $sms_text_data[$value['student_num']];
				$mark_1_txt = "";
				$mark_2_txt = "";
				if ($data_tmp['is_theory'] && $data_tmp['is_practice']) {
					$mark_1_txt = "теориясы ".$data_tmp['theory'][0]."%, есебі ".$data_tmp['practice'][0]."%";
					$mark_2_txt = "теориясы ".$data_tmp['theory'][1]."%, есебі ".$data_tmp['practice'][1]."%";
				} else if ($data_tmp['is_theory'] && !$data_tmp['is_practice']) {
					$mark_1_txt = $data_tmp['theory'][0]."%";
					$mark_2_txt = $data_tmp['theory'][1]."%";
				} else if (!$data_tmp['is_theory'] && $data_tmp['is_practice']) {
					$mark_1_txt = $data_tmp['practice'][0]."%";
					$mark_2_txt = $data_tmp['practice'][1]."%";
				}

				$data = array(
					$RECIPIENT => "7".$value['phone'],
					$TEXT => kiril2latin(sprintf($QUIZ_RETAKE, $value['name'], $data_tmp['subject_name'], $mark_1_txt, $mark_2_txt))
				);
				$res = send_sms($data, $RECIPIENT_TYPE_P, $value['surname']." ".$value['name']);
				array_push($sms_result, $res['manual_sms_response']);
			}

			$save_sms_res = save_sms($conn, $sms_result);
			if ($save_sms_res == 'true') {
				header("location:index.php");
			} else {
				echo $save_sms_res;
			}

		}
		else {
			header("location:index.php");
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_news'])){
	try {
		$header = $_POST['news_header'];
		$context = $_POST['news_context'];
		echo "<br>".$context."<br>";
		$news_type = $_POST['news_type'];
		echo ($_FILES["news_img"]['name']!='')?"true<br>":"false<br>";
		echo $_FILES['news_img']['name']."<br>";
		echo $_POST['uploaded_photo']."<br>";
		if(isset($_FILES["news_img"]) && $_FILES["news_img"]['name']!=''){
			foreach (glob("../news_img/".$news_type.'_news_*') as $filename) {
			    unlink($filename);
			}

			$photo_path = '../news_img/';
			$temp = explode(".", $_FILES["news_img"]["name"]);
			$newfilename = $news_type."_news_".uniqid('NW', true).'.'.end($temp);
			$photo_path = $photo_path.basename($newfilename);
			$tmp_name = $_FILES['news_img']['tmp_name'];
			move_uploaded_file($tmp_name, $photo_path);
		}
		else{
			$newfilename = $_POST['uploaded_photo'];
		}

		$last_updated_date = date("Y-m-d");
		$publish = 0;
		$stmt = $conn->prepare("UPDATE news SET header = :header, content = :content, img = :img, last_updated_date = :last_updated_date, publish = :publish WHERE type = :type");
		$stmt->bindParam(':header', $header, PDO::PARAM_STR);
		$stmt->bindParam(':content', $context, PDO::PARAM_STR);
		$stmt->bindParam(':img', $newfilename, PDO::PARAM_STR);
		$stmt->bindParam(':last_updated_date', $last_updated_date, PDO::PARAM_STR);
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->bindParam(':publish', $publish, PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['news_publish'])){
	try {
		$news_type = $_POST['news_type'];
		$publish = 1;
		$last_updated_date = date("Y-m-d");
		$stmt = $conn->prepare("UPDATE news SET last_updated_date = :last_updated_date, publish = :publish WHERE type = :type");
		$stmt->bindParam(':last_updated_date', $last_updated_date, PDO::PARAM_STR);
		$stmt->bindParam(':publish', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['news_unpublish'])){
	try {
		$news_type = $_POST['news_type'];
		$publish = 0;
		$last_updated_date = date("Y-m-d");
		$stmt = $conn->prepare("UPDATE news SET publish = :publish WHERE type = :type");
		$stmt->bindParam(':publish', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['student_no_payment'])){
	try {
		$student_num = $_POST['data_num'];
		$payment_block = 2;
		$stmt = $conn->prepare("UPDATE student SET block = :block WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':block', $payment_block, PDO::PARAM_INT);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['student_no_contract'])){
	try {
		$student_num = $_POST['data_num'];
		$payment_block = 4;
		$stmt = $conn->prepare("UPDATE student SET block = :block WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':block', $payment_block, PDO::PARAM_INT);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if (isset($_POST['add_chocolate'])){
	try {
		$quantity = $_POST['quantity'];
		$stmt = $conn->prepare("INSERT INTO chocolate_history (quantity) VALUES(:quantity)");
		$stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
		$stmt->execute();

	    $stmt = $conn->prepare("UPDATE chocolate_history ch1 
    							INNER JOIN chocolate_history ch2
    								ON ch2.id = ch1.id
    							SET ch1.quantity = ch2.quantity+:quantity
	    						WHERE ch1.id = 1");
	    $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
	    $stmt->execute();

		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
} else if (isset($_POST['save-scn-form'])) {
	try {
		$id_list = $_POST['id'];
		$stmt = $conn->prepare("UPDATE student_call_notification SET status = 1, called_date = NOW() WHERE id IN ('".implode("','", $id_list)."')");
		$stmt->execute();

		$stmt = $conn->prepare("SELECT scn.id,
									s.surname,
									s.name,
									s.phone
								FROM student_call_notification scn,
									student s
								WHERE scn.status = 1
									AND scn.is_sent_to_admin = 0
									AND s.student_num = scn.student_num");
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		if ($rowCount >= 10) {
			$results = $stmt->fetchAll();
			$ids = array();
			$html = "<table>";
			$count = 0;
			foreach ($results as $v) {
				array_push($ids, $v['id']);
				$html .= "<tr>";
				$html .= "<td style='border:1px solid gray;'>".(++$count)."</td>";
				$html .= "<td style='border:1px solid gray;'>".$v['surname'].' '.$v['name']."</td>";
				$html .= "<td style='border:1px solid gray;'>+7".$v['phone']."</td>";
				$html .= "</tr>";
			}
			$html .= "</table>";

			$to = "zhambyl.9670@gmail.com, almat.myrzabek@gmail.com"; 
		    $subject = "Администратор хабарласқан оқушылар тізімі (".$rowCount.")";
		    $headers = "MIME-Version: 1.0" . "\r\n";
		    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		        // More headers
		    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
		    // echo $html;
	    	if(mail($to,$subject,$html,$headers)){
	    		// echo "<br>Message sent successfully";
	    		$stmt = $conn->prepare("UPDATE student_call_notification SET is_sent_to_admin = 1 WHERE id IN ('".implode("','", $ids)."')");
	    		$stmt->execute();
	    	}
		}
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();	
	}
}
else{
	echo "Error";
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
	$txt = "";
	$browser = getBrowser();
	$file = fopen("logDB/log.txt", "a") or die("Unable to open file!");
	$txt .= "---------------------------------".$text."----------------------------------------------\n";
	$txt .= "Date:    ".date("d-m-Y h:i:sa")."\n";
	// "Y-m-d h:i:sa"
	$txt .= "Query:   ".$query."\n";
	$txt .= "Values:  ".$values."\n";
	$txt .= "Browser: ".implode("...........", $browser)."\n";
	$txt .= "--------------------------------------------------------------------------------------\n\n\n";
	fwrite($file, $txt);
	fclose($file);
}

?>