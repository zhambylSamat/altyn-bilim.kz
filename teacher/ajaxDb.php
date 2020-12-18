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
else if(isset($_GET[md5(md5('add_trial_test_mark'))])){
	try {
		$student_num = $_POST['stdn'];
		$subject_num = $_POST['sjn'];
		$mark = $_POST['trial_mark'];
		$date_of_test = date("Y-m-d", strtotime($_POST['trial_date']));
		$trial_test_test = '';
		$trial_test_num = '';

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
		$trial_test_mark_num = $conn->lastInsertId();

		if((($subject_num == 'S59ac10750a4075.24932992' || $subject_num == 'S5c49965edd7ba3.12875675_1548326494') && $mark >= 19) || $mark >= 38){
			$data['success'] = setTrialTestTopMark($trial_test_mark_num);
		}
		else {
			$data['success'] = true;	
		}

		$stmt = $conn->prepare("SELECT n1.object_num AS num
								FROM notification n1
								WHERE n1.object_parent_num = :trial_test_num 
									AND n1.status = 'D' 
								ORDER BY n1.deleted_date DESC
								LIMIT 3");
		$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
		$stmt->execute();
		$res_ttm_nums = $stmt->fetchAll();
		$res_ttm_num_count = $stmt->rowCount();
		$avg_mark = 0;
		if ($res_ttm_num_count == 3) {
			$mark_num = "'".$res_ttm_nums[0]['num']."', '".$res_ttm_nums[1]['num']."', '".$res_ttm_nums[2]['num']."'";
		
			$data['ttt'] = $mark_num;

			$stmt = $conn->prepare("SELECT AVG(ttm.mark) AS avg_mark
									FROM trial_test_mark ttm
									WHERE ttm.trial_test_mark_num IN (".$mark_num.")");
			$stmt->execute();
			$avg_mark = $stmt->fetch(PDO::FETCH_ASSOC)['avg_mark'];
		}

		$min_mark = 10;
		if ($subject_num == 'S59ac10750a4075.24932992' || $subject_num == 'S5c49965edd7ba3.12875675_1548326494') {
			$min_mark = 5;
		}
		$min_mark = $avg_mark > $min_mark ? $avg_mark : $min_mark;
		$data['success'] = setTrialTestIncreaseMark($trial_test_mark_num, $trial_test_num, $mark, $min_mark);

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


		$stmt = $conn->prepare("SELECT (CASE
											WHEN 
								    			(SELECT topic_num 
								                 FROM subtopic
								                 WHERE subtopic_num = :subtopic_num)
								    			=
								    			(SELECT topic_num
								                FROM topic
                                                WHERE subject_num = (SELECT t.subject_num 
                                                					FROM topic t, 
                                                						subtopic st 
                                                					WHERE t.topic_num = st.topic_num 
                                                						AND st.subtopic_num = :subtopic_num)
                                                	AND quiz = 'n'
								                ORDER BY topic_order DESC
								                LIMIT 1) 
								    		THEN 'true'
								    		ELSE 'false'
								       END) AS finish_lesson,

								    count(sp.student_progress_num) AS last_topic_count
								FROM student_progress sp,
									subtopic st
								WHERE sp.student_num = :student_num
									AND sp.subtopic_num = :subtopic_num
								    AND sp.progress != 0");

	// AND st.subtopic_num = sp.subtopic_num
	// 							    AND st.topic_num = (SELECT topic_num
	// 							                       FROM topic
	// 							                       WHERE quiz = 'n'
	// 							                           AND subject_num = (SELECT s3.subject_num 
	// 							                                            FROM subject s3, 
	// 							                                            	topic t3 
	// 							                                           	WHERE t3.topic_num = st.topic_num 
	// 							                                            	AND s3.subject_num = t3.subject_num)
	// 							                       ORDER BY id DESC
	// 							                       LIMIT 1)

		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_finish_lesson = $stmt->fetch(PDO::FETCH_ASSOC);
		$finish_lesson = $result_finish_lesson['finish_lesson'];
		$last_topic_count = $result_finish_lesson['last_topic_count'];

		$stmt = $conn->prepare("SELECT fcn.id AS id 
								FROM finish_course_notification fcn,
									subtopic st,
									topic t
								WHERE fcn.student_num = :student_num
									AND fcn.subject_num = t.subject_num
									AND t.topic_num = st.topic_num
									AND st.subtopic_num = :subtopic_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$stmt->execute();
		$finish_course_exists = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

		if($finish_lesson == 'true' && $last_topic_count > 0){
			if(is_null($finish_course_exists) || $finish_course_exists == ''){
				$stmt = $conn->prepare("INSERT INTO finish_course_notification (student_num, subject_num) 
											SELECT :student_num, t.subject_num 
											FROM topic t,
												subtopic st
											WHERE st.subtopic_num = :subtopic_num
												AND t.topic_num = st.topic_num");
				$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
				$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				$stmt->execute();
			}
		}
		else if(($finish_lesson == 'false' && $last_topic_count == 0) || ($finish_lesson == 'true' && $last_topic_count == 0)){
			if(!is_null($finish_course_exists) && $finish_course_exists != ''){
				$stmt = $conn->prepare("DELETE FROM finish_course_notification WHERE id = (SELECT * FROM (SELECT fcn.id 
																							FROM finish_course_notification fcn,
																								topic t,
																								subtopic st
																							WHERE fcn.student_num = :student_num
																								AND fcn.subject_num = t.subject_num
																								AND st.subtopic_num = :subtopic_num
																								AND t.topic_num = st.topic_num) as id)");
				$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
				$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				$stmt->execute();
			}
		}
		$data["finish_lesson"] = $finish_lesson;
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
		$subject_num = $_POST['sj_num'];
		$result = array();
		$stmt = $conn->prepare("SELECT DISTINCT gs.group_student_num
								FROM group_info gi,
									group_student gs
								WHERE gi.teacher_num = :teacher_num
									AND gi.subject_num = :subject_num
								    AND gs.group_info_num = gi.group_info_num
								    AND gs.student_num = (SELECT gs2.student_num 
								                          FROM group_student gs2 
								                          WHERE gs2.group_student_num = :group_student_num)
								    AND gs.group_student_num not in (SELECT group_student_num FROM review)");
		$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->bindParam(':group_student_num', $group_student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();

		if(isset($_POST['new_rin'])){
			$review_info_num = $_POST['new_rin'];
			$review_status = $_POST['new_status'];
			$data['grstnum'] = $result;
			foreach ($result as $gr_s_num) {
				addReviewToStudent($review_info_num, $gr_s_num['group_student_num'], $review_status);
			}
		}


		if(isset($_POST['old_status'])){
			$review_info_num = $_POST['old_rin'];
			$review_status = $_POST['old_status'];

			foreach ($result as $gr_s_num) {
				updateStudentReview($review_info_num, $review_status, $gr_s_num['group_student_num']);
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

function addReviewToStudent($review_info_num, $group_student_num, $review_status){
	global $conn;
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

function updateStudentReview($review_info_num, $review_status, $group_student_num){
	global $conn;
	$stmt = $conn->prepare("UPDATE review SET status = ? WHERE group_student_num = ? AND review_info_num = ?");
	for ($i=0; $i < count($review_info_num); $i++) {
		$stmt->execute(array($review_status[$i], $group_student_num, $review_info_num[$i]));
	}
}

function setTrialTestTopMark($object_num){
	try {
		$object_id = 4;
		$constant = 1;
		$count = 1;

		global $conn;

		$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, constant, count) VALUES(:object_id, :object_num, :constant, :count)");
		$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
		$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
		$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
		$stmt->bindParam(':count', $count, PDO::PARAM_INT);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return false;
	}
}
function setTrialTestIncreaseMark($object_num, $trial_test_num, $mark, $min_mark) {
	try {
		global $conn; 

		$object_id = 5;
		$constant = 3;
		$status = 'A';

		$stmt = $conn->prepare("SELECT n.id,
									ttm.mark,
									n.status
								FROM notification n,
									trial_test_mark ttm
								WHERE n.object_parent_num = :trial_test_num 
									AND n.constant = :constant 
									AND n.status != 'D' 
									AND ttm.trial_test_mark_num = n.object_num order by n.id");
		$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
		$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
		$stmt->execute();
		$notification_row_count = $stmt->rowCount();
		$result = $stmt->fetchAll();

		if($notification_row_count == 0){
			if ($mark < $min_mark) {
				$status = "W";
			}

			$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, object_parent_num, constant, count, status) VALUES(:object_id, :object_num, :trial_test_num, :constant, 1, :status)");
			$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
			$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
			$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->execute();
		}
		else if($notification_row_count == 1) {
			// if($result[0]['mark'] >= $mark && $mark <= 0){
			// 	$status = "W";
			// }
			if (($mark < $min_mark)
				|| $result[0]['status'] == "W"
				|| $result[0]['mark'] >= $mark){
				$status = "W";
			}

			$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, object_parent_num, constant, count, status) VALUES(:object_id, :object_num, :trial_test_num, :constant, 2, :status)");
			$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
			$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
			$stmt->execute();
		}
		else if($notification_row_count == 2){
			// if ( $mark <= 0
			// 	|| ($result[1]['status'] == 'W' && $result[0]['mark'] >= $mark) 
			// 	|| ($result[1]['status'] == 'A' && $result[1]['mark'] >= $mark)) {
			// 	$status = "W";
			// }
			if (($mark < $min_mark)
				|| $result[1]['status'] == 'W'
				|| $result[1]['mark'] >= $mark) {
				$status = "W";
			}

			$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, object_parent_num, constant, count, status) VALUES(:object_id, :object_num, :trial_test_num, :constant, 3, :status)");
			$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
			$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
			$stmt->execute();
		}
		else if ($notification_row_count == 3){
			$stmt = $conn->prepare("DELETE FROM notification 
									WHERE id = :id
										AND object_id = :object_id");
			$stmt->bindParam(':id', $result[0]['id'], PDO::PARAM_INT);
			$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$stmt->execute();

			if ($result[1]['mark'] < $min_mark) {
				$status = 'W';
			} else {
				$status = "A";
			}
			$stmt = $conn->prepare("UPDATE notification 
									SET count = 1, 
										status = :status
									WHERE id = :id ");
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':id', $result[1]['id'], PDO::PARAM_INT);
			$stmt->execute();

			if ($result[2]['mark'] < $min_mark) {
				$status = 'W';
			} else if ($status == 'A' && $result[1]['mark'] < $result[2]['mark']) {
				$status = "A";
			} else {
				$status = "W";
			}
			$stmt = $conn->prepare("UPDATE notification 
									SET count = 2, 
										status = :status
									WHERE id = :id ");
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':id', $result[2]['id'], PDO::PARAM_INT);
			$stmt->execute();

			if ($mark < $min_mark) {
				$status = "W";
			} else if ($status == "A" && $result[2]['mark'] < $mark) {
				$status = "A";
			} else {
				$status = "W";
			}
			$stmt = $conn->prepare("INSERT INTO notification (object_id, object_num, object_parent_num, constant, count, status) VALUES(:object_id, :object_num, :trial_test_num, :constant, 3, :status)");
			$stmt->bindParam(':object_num', $object_num, PDO::PARAM_STR);
			$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
			$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$stmt->bindParam(':constant', $constant, PDO::PARAM_INT);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->execute();
		}

		return true;
	} catch (PDOException $e) {
		return false;
	}
}

?>
