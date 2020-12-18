<?php
include_once '../connection.php';
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
		$values .= "        [Password]: ".md5($_POST['password']);
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
	    	writeToLog($query, $values, "Admin login");
	    	header('location:index.php');
	    }
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
if(isset($_POST['remove_student'])){
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
		echo "Error ".$e->getMessge()." !!!";
	}
}
if(isset($_POST['remove_teacher'])){
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
if(isset($_POST['edit_user'])){
	try {
		$stmt = $conn->prepare("UPDATE student SET name = :name, surname = :surname, username = :username WHERE student_num = :student_num");
   
	    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_INT);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_INT);
	    $stmt->bindParam(':username', $username, PDO::PARAM_INT);

	    $student_num = $_POST['edit-student-num'];
	    $name = $_POST['name'];
	    $surname = $_POST['surname'];
	    $username = mb_strtolower($_POST['username']);
	       
	    $stmt->execute();
	    header('location:index.php');
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if(isset($_POST['edit_teacher'])){
	try {
		$stmt = $conn->prepare("UPDATE teacher SET name = :name, surname = :surname, username = :username WHERE teacher_num = :teacher_num");
   
	    $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	    $stmt->bindParam(':name', $name, PDO::PARAM_INT);
	    $stmt->bindParam(':surname', $surname, PDO::PARAM_INT);
	    $stmt->bindParam(':username', $username, PDO::PARAM_INT);

	    $teacher_num = $_POST['edit-teacher-num'];
	    $name = $_POST['name'];
	    $surname = $_POST['surname'];
	    $username = $_POST['username'];
	       
	    $stmt->execute();
	    header('location:index.php');
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if(isset($_POST['edit_parent'])){
	try {
		$parent_num = $_POST['edit-parent'];
		$student_num = $_POST['students'];
		$parent_name = $_POST['parent_name'];
		$parent_surname = $_POST['parent_surname'];
		$phone = $_POST['phone'];
		$stmt = $conn->prepare("UPDATE parent SET name = :name, surname = :surname, phone = :phone WHERE parent_num = :parent_num");
		$stmt->bindParam(':parent_num', $parent_num, PDO::PARAM_STR);
		$stmt->bindParam(':name', $parent_name, PDO::PARAM_STR);
		$stmt->bindParam(':surname', $parent_surname, PDO::PARAM_STR);
		$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM child WHERE parent_num = :parent_num");
		$stmt->bindParam(':parent_num', $parent_num, PDO::PARAM_STR);
		$stmt->execute();

		$query = "INSERT INTO child (child_num, parent_num, student_num) VALUES";
	    $qPart = array_fill(0, count($student_num), "(?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($student_num); $i++){
	    	$child_num = uniqid('CH',true);
	    	$stmtA->bindValue($j++, $child_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $parent_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $student_num[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();
	    header('Location:index.php');
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if(isset($_POST['remove_parent'])){
	try {
		$parent_num = $_POST['remove-parent-num'];

		$stmt = $conn->prepare("DELETE FROM parent WHERE parent_num = :parent_num");
		$stmt->bindParam(':parent_num',$parent_num,PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $conn->prepare("DELETE FROM child WHERE parent_num = :parent_num");
		$stmt->bindParam(':parent_num',$parent_num,PDO::PARAM_STR);
		$stmt->execute();

		header('Location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if (isset($_POST['create-new-subject'])) {
	try {
		$stmt = $conn->prepare("INSERT INTO subject (subject_num, subject_name, created_date) VALUES(:subject_num, :subject_name, :created_date)");
   
	    $stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
	    $stmt->bindParam(':subject_name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);

	    $created_date = date("Y-m-d H:i:s");
	    $subject_num = uniqid('S', true);
	    $name = $_POST['new-subject-name'];
	       
	    $stmt->execute();
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if (isset($_POST['create-new-topic'])) {
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
		    $topic_num = uniqid('TQ', true);
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
		    $topic_num = uniqid('T', true);
		    $name = $_POST['new-topic-name'];
		       
		    $stmt->execute();
		}
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if (isset($_POST['create-new-subtopic'])) {
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
	    $subtopic_num = uniqid('S_T', true);
	    $name = $_POST['new-subtopic-name'];
	       
	    $stmt->execute();
	    header('location:index.php');
		
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if(isset($_POST['student-test-permission'])){
	try {
		$stmt = $conn->prepare("INSERT IGNORE INTO student_permission (student_permission_num, student_num) VALUES(:student_permission_num, :student_num) ");
 
	    $stmt->bindParam(':student_permission_num', $student_permission_num, PDO::PARAM_STR);
	    $stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);

	    $student_permission_num = uniqid('S_P', true);
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
if(isset($_POST['edit_group_info'])){
	try {
		$stmt = $conn->prepare("UPDATE group_info SET subject_num = :subject_num, teacher_num = :teacher_num, group_name = :group_name, comment = :comment WHERE group_info_num = :group_info_num");

	   	$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
	   	$stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
	   	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	   	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	   	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);

	   	$subject_num = $_POST['group_subject'];
	   	$group_name = $_POST['group_name'];
	   	$teacher_num = $_POST['group_teacher'];
	   	$comment = $_POST['group_comment'];
	   	$group_info_num = $_POST['data_num'];
		       
	    $stmt->execute();
	    header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['delet_group_info'])){
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
if(isset($_POST['add_to_group'])){
	try {
		$data_num = $_POST['data_num'];
		$students = $_POST['students_to_group'];

		$query = "INSERT INTO group_student (group_student_num, group_info_num, student_num) VALUES";
	    $qPart = array_fill(0, count($students), "(?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    $dataMessage1 = '';
	    $dataMessage2 = '';
	    for($i = 0; $i<count($students); $i++){
	    	$group_student_num = uniqid('GS', true);
	    	$stmtA->bindValue($j++, $group_student_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $data_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $students[$i], PDO::PARAM_STR);
	    	$dataMessage1 .= "[".$group_student_num."] ";
	    	$dataMessage2 .= "[".$students[$i]."] ";
	    }
	    $stmtA->execute();
	    
	    header('location:group.php?data_num='.$data_num);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['remove_from_group'])){
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
if(isset($_POST['delete_subject'])){
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
if(isset($_POST['submit_data_modal_topic'])){
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
if(isset($_POST['submit_data_modal_subtopic'])){
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
if(isset($_POST['edit_subject'])){
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
if(isset($_POST['block_student'])){
	try {
		$student_num = $_POST['data_num'];
		$block = 1;
		$stmt = $conn->prepare("UPDATE student SET block = :block WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':block', $block, PDO::PARAM_INT);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['unblock_student'])){
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
if(isset($_POST['transfer_student'])){
	try {
		echo "1";
		$new_group_num = $_POST['new_gr'];
		$student_num = $_POST['std_num'];
		$old_group_num = $_POST['gr_num'];
		$transfer_num = uniqid("TR",true);
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
if(isset($_POST['comment_for_teacher'])){
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
		    	$review_info_num = uniqid('RI', true);
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
if(isset($_POST['submit_prize_notification'])){
	try {
		if(isset($_POST['spn'])){
			$id = $_POST['spn'];
			$query = 'DELETE FROM student_prize_notification WHERE id = ';
			$qPart = array_fill(0, count($id), "?");
		    $query .= implode(" OR id = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($id); $i++){
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		}
	    header("location:index.php");
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
if(isset($_POST['submit_attendance_notification'])){
	try {
		if(isset($_POST['ann'])){
			$id = $_POST['ann'];
			$query = 'DELETE FROM attendance_notification WHERE attendance_notification_num = ';
			$qPart = array_fill(0, count($id), "?");
		    $query .= implode(" OR attendance_notification_num = ",$qPart);
		    $stmt = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($id); $i++){
		    	$stmt->bindValue($j++, $id[$i], PDO::PARAM_STR);
		    }
		    $stmt->execute();
		}
	    header("location:index.php");
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if(isset($_POST['submit_news'])){
	try {
		$header = $_POST['news_header'];
		$context = $_POST['news_context'];
		$news_type = $_POST['news_type'];
		echo ($_FILES["news_img"]['name']!='')?"true<br>":"false<br>";
		echo $_FILES['news_img']['name']."<br>";
		echo $_POST['uploaded_photo']."<br>";
		if(isset($_FILES["news_img"]) && $_FILES["news_img"]['name']!=''){
			foreach (glob("../news_img/".$news_type.'_news.*') as $filename) {
			    unlink($filename);
			}

			$photo_path = '../news_img/';
			$temp = explode(".", $_FILES["news_img"]["name"]);
			$newfilename = $news_type."_news". '.' . end($temp);
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