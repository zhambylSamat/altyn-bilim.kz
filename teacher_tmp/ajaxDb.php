<?php
$data = array();
$data['script'] = "";
$data['text'] = "";
$data['success'] = false;
$data['error'] = '';

include_once('../connection.php');

if(isset($_GET[md5(md5('set_permission'))])){
	try {
		include_once('../connection.php');
		if(isset($_SESSION['teacher_num'])){
			$stmt = $conn->prepare("SELECT video_num FROM video WHERE subtopic_num = :subtopic_num");
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
		}
		else $data['success'] = false;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
if(isset($_GET[md5(md5('add_trial_test_mark'))])){
	try {
		$student_num = $_POST['stdn'];
		$subject_num = $_POST['sjn'];
		$mark = $_POST['trial_mark'];
		$date_of_test = date("Y-m-d", strtotime($_POST['trial_date']));
		$trial_test_test = '';

		$stmt = $conn->prepare("SELECT trial_test_num FROM trial_test WHERE student_num = :student_num AND subject_num = :subject_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$count = $stmt->rowCount();
		
		if($count==0){
			$trial_test_num = uniqid('TT',true)."_".time();
			$stmt = $conn->prepare("INSERT INTO trial_test (trial_test_num, subject_num, student_num) VALUES(:trial_test_num, :subject_num, :student_num)");
			$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
			$stmt->execute();
		}
		else{
			$trial_test_num = $res[0]['trial_test_num'];
		}

		$stmt = $conn->prepare("INSERT INTO trial_test_mark (trial_test_num, mark, date_of_test) VALUES(:trial_test_num, :mark, :date_of_test)");
		$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
		$stmt->bindParam(':mark', $mark, PDO::PARAM_STR);
		$stmt->bindParam(':date_of_test', $date_of_test, PDO::PARAM_STR);
		$stmt->execute();

		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('student_progress'))])){
	try {
		$progress = $_POST['progress_progress'];
		$tmp_count_subtopic = $_POST['tmp_count_subtopic'];
		$tmp_subtopic_name = $_POST['tmp_subtopic_name'];
		$student_progress_num = $_POST['id'];
		$subtopic_num = $_POST['stn'];
		$student_num = $_POST['stdnum'];
		if($student_progress_num == 'new'){
			$student_progress_num = uniqid("SP",true)."_".time();
			$stmt = $conn->prepare("INSERT INTO student_progress (student_progress_num, student_num, subtopic_num, progress) VALUES(:student_progress_num, :student_num, :subtopic_num, :progress)");
			$stmt->bindParam(':student_progress_num', $student_progress_num, PDO::PARAM_STR);
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
			$stmt->bindParam(':progress', $progress, PDO::PARAM_STR);
			$stmt->execute();
		}
		else if($student_progress_num != 'new'){
			$stmt = $conn->prepare("UPDATE student_progress SET progress = :progress WHERE student_progress_num = :student_progress_num");
			$stmt->bindParam(':student_progress_num', $student_progress_num, PDO::PARAM_STR);
			$stmt->bindParam(':progress', $progress, PDO::PARAM_STR);
			$stmt->execute();
		}
		$data['id'] = $student_progress_num;
		$data['stdNum'] = $student_num;
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('submit_review_for_student'))])){


// CREATE TRIGGER `remove_dupliates_on_review_tbl` BEFORE INSERT ON `review`
//  FOR EACH ROW 
// BEGIN
// 	DECLARE count_rows int;
// 	SELECT count(*) INTO count_rows 
//     FROM review 
//     WHERE review_info_num = NEW.review_info_num
//     	AND group_student_num = NEW.group_student_num;
   
//     IF count_rows = 0 THEN
//     	INSERT INTO review 
//         	(review_num, 
//              review_info_num, 
//              group_student_num, 
//              status) 
//         VALUES(NEW.review_num, 
//                NEW.review_info_num, 
//                NEW.group_student_num, 
//                NEW.status);
//     END IF;
    
// END


// 	BEGIN
//     IF (EXISTS(SELECT 1 FROM review WHERE review_info_num = NEW.review_info_num AND group_student_num = NEW.group_student_num)) THEN
//     	SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'INSERT failed due to duplicate record';
//     END IF;
    
// END


	try {
		$group_student_num = $_POST['gsn'];
		if(isset($_POST['new_rin'])){
			$review_info_num = $_POST['new_rin'];
			$review_status = $_POST['new_status'];
			$query = "INSERT INTO review (review_num, review_info_num, group_student_num, status) VALUES";
		    $qPart = array_fill(0, count($review_info_num), "(?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($review_info_num); $i++){
		    	$review_num = uniqid('R', true)."_".time();
		    	$stmtA->bindValue($j++, $review_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $review_info_num[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $group_student_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $review_status[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		}
		if(isset($_POST['old_status'])){
			$review_info_num = $_POST['old_rin'];
			$review_status = $_POST['old_status'];
			$stmt = $conn->prepare("UPDATE review SET status = ? WHERE group_student_num = ? AND review_info_num = ?");
			for ($i=0; $i < count($review_info_num); $i++) {
				$stmt->execute(array($review_status[$i], $group_student_num, $review_info_num[$i]));
			}
		}
		// if(isset($_POST['new_review_comment'])){
		// 	$review_info_num = $_POST['rc'];
		// 	$review_status = $_POST['new_review_comment'];
		// 	$review_num = uniqid('R', true);
		// 	$stmt = $conn->prepare("INSERT INTO review (review_num, review_info_num, group_student_num, status) VALUES(:review_num, :review_info_num, :group_student_num, :status)");
		// 	$stmt->bindParam(':review_num', $review_num, PDO::PARAM_STR);
		// 	$stmt->bindParam(':review_info_num', $review_info_num, PDO::PARAM_STR);
		// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
		// 	$stmt->bindParam(':status', $review_status, PDO::PARAM_STR);
		// 	$stmt->execute();
		// }
		// if(isset($_POST['old_review_comment'])){
		// 	$review_info_num = $_POST['rc'];
		// 	$review_status = $_POST['old_review_comment'];
		// 	$stmt = $conn->prepare("UPDATE review SET status = :status WHERE group_student_num = :group_student_num AND review_info_num = :review_info_num");
		// 	$stmt->bindParam(':status', $review_status, PDO::PARAM_STR);
		// 	$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
		// 	$stmt->bindParam(':review_info_num', $review_info_num, PDO::PARAM_STR);
		// 	$stmt->execute();
		// }
		$data['success'] = true;
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('add-new-suggestion'))])){
	try {

		$text = $_POST['suggestion_text'];
		$status = 0;
		$last_changed_date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("INSERT INTO suggestion (user_num, text, status, last_changed_date) VALUES(:user_num, :text, :status, :last_changed_date)");
		$stmt->bindParam(':user_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
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
}
?>
