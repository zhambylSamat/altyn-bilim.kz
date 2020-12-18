
<?php 
	include('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		header('location:signin.php');
	}
	if(!isset($_SESSION['load_page']) || !$_SESSION['load_page']){
		$_SESSION['load_page'] = true;
	}
	$pages = array('student','teacher','subject','group','parent','schedule','dob','progress_result','entrance_examination','chocolate', 'ent_result', 'sms_history', 'statistics', 'calculator', 'course_price');
	if(isset($_SESSION['page'])){
		if(!in_array($_SESSION['page'], $pages)){
			$_SESSION['page'] = $pages[0];
		}
	}
	else{
		$_SESSION['page'] = $pages[0];
	}
	$list = array();

	$config_subject_quiz = array();

	try {
		$prize_count = 0;

		$stmt = $conn->prepare("SELECT * FROM config_subject_quiz");
		$stmt->execute();
		foreach ($stmt->fetchAll() as $value) {
			$config_subject_quiz[$value['subject_num']]['practice'] = $value['practice'];
			$config_subject_quiz[$value['subject_num']]['theory'] = $value['theory'];
		}

		$stmt = $conn->prepare("SELECT s.subject_num sNum, s.subject_name sName, t.topic_num tNum, t.topic_name tName, st.subtopic_num stNum, st.subtopic_name stName FROM subject s, topic t, subtopic st WHERE s.subject_num = t.subject_num AND t.topic_num = st.topic_num order by s.created_date, t.created_date, st.created_date asc");
	    $stmt->execute();
	    $result_list = $stmt->fetchAll();
	    foreach ($result_list as $value) {
	    	$list[$value['sNum']]['name'] = $value['sName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['name'] = $value['tName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['subtopic'][$value['stNum']]['name'] = $value['stName'];
	    }
	    $_SESSION['list-subject-topic-subtopic'] = $list;


	    $stmt = $conn->prepare("SELECT spn.id, 
	    							s.student_num,
									s.surname,
							        s.name,
									gi.group_name,
							        sj.subject_name,
							        sj.subject_num,
							        t.topic_name,
							        qm.mark_theory,
							        qm.mark_practice,
							        DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
								FROM student_prize_notification spn,
									group_info gi,
						            group_student gs,
						            student s,
						            subject sj,
						            topic t,
						            quiz q,
						            quiz_mark qm
						        WHERE spn.quiz_mark_num = qm.quiz_mark_num
					                AND spn.group_student_num = gs.group_student_num
					                AND gs.group_info_num = gi.group_info_num
					                AND qm.quiz_num = q.quiz_num
					                AND t.topic_num = q.topic_num
					                AND sj.subject_num = t.subject_num
					                AND s.student_num = gs.student_num
					                AND spn.status != 'D'
						        ORDER BY spn.id ASC");
	    // AND (qm.mark_theory in (100, 0) OR qm.mark_theory > 94)
					                // AND qm.mark_practice > 94
	    $stmt->execute();
	    $prize_notification_result = $stmt->fetchAll();
	    $prize_count = $stmt->rowCount();

	    $stmt = $conn->prepare("SELECT an.attendance_notification_num, 
	    							s.surname,
	    							s.student_num,
									s.name, 
								    gi.group_name,
								    DATE_FORMAT(pg.created_date, '%d.%m.%Y') AS created_date
								FROM attendance_notification an,
									student s,
								    group_info gi,
								    group_student gs,
								    progress_group pg,
								    progress_student ps
								WHERE an.action = 2
									AND an.group_student_num = gs.group_student_num
								    AND gs.group_info_num = gi.group_info_num
								    AND ps.progress_student_num in (an.first_abs, an.second_abs, an.third_abs)
								    AND pg.progress_group_num = ps.progress_group_num
								    AND ps.student_num = s.student_num
								    AND s.block != 6
								    AND gs.block != 6
								ORDER BY s.surname, s.name, gi.group_name, pg.created_date ASC");

	  	$stmt->execute();
	  	$result_an = $stmt->fetchAll();
	  	$attendance_notification_count = $stmt->rowCount()/2;

	  	$stmt = $conn->prepare("SELECT qrn.id,
	  								st.student_num,
									st.name,
								    st.surname,
								    sj.subject_name,
								    sj.subject_num,
								    t.topic_name,
								    qm.mark_theory,
								    qm.mark_practice,
								    DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
								FROM subject sj,
									topic t,
								    student st,
								    quiz q,
								    quiz_mark qm,
								    quiz_retake_notification qrn
								WHERE qm.quiz_mark_num in (qrn.retake_1, qrn.retake_2)
								    AND q.quiz_num = qm.quiz_num
								    AND t.topic_num = q.topic_num
								    AND sj.subject_num = t.subject_num
								    AND st.student_num = qm.student_num");
	  	$stmt->execute();
	  	$result_qrn = $stmt->fetchAll();
	  	$quiz_retake_notification_count = $stmt->rowCount()/2;

	  	$stmt = $conn->prepare("SELECT fcn.id id, 
	  								s.name name, 
	  								s.surname surname, 
	  								sj.subject_name subject_name,
	  								s.student_num
	  							FROM finish_course_notification fcn,
	  								student s,
	  								subject sj
	  							WHERE s.student_num = fcn.student_num
	  								AND sj.subject_num = fcn.subject_num
	  								AND s.block != 6
  									AND s.student_num IN (SELECT gs.student_num 
                                                          FROM group_info gi, 
                                                          	group_student gs 
                                                          WHERE gi.subject_num = sj.subject_num 
                                                          	AND gs.group_info_num = gi.group_info_num 
                                                          	AND gs.student_num = s.student_num 
                                                          	AND gs.block != 6 )");
	  	$stmt->execute();
	  	$result_finish_course_notification = $stmt->fetchAll();
	  	$finish_course_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT n.id,
	  								n.status,
	  								s.student_num,
	  								s.surname, 
									s.name, 
								    sj.subject_name,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date_of_test
								FROM notification n,
									trial_test_mark ttm,
								    trial_test tt,
								    subject sj,
								    student s
								WHERE n.status IN ('A', 'DA', 'AD') 
									AND s.block != 6
									AND n.object_id = 4
									AND n.object_num = ttm.trial_test_mark_num
								    AND ttm.trial_test_num = tt.trial_test_num
								    AND tt.subject_num = sj.subject_num
								    AND tt.student_num = s.student_num");
	  	$stmt->execute();
	  	$result_trial_test_top_notification = $stmt->fetchAll();
	  	$trial_test_top_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT n.id,
	  								n.object_parent_num,
	  								n.status,
	  								s.student_num,
	  								s.surname,
	  								s.name,
	  								sj.subject_name,
	  								ttm.mark,
	  								n.status,
	  								DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date_of_test
	  							FROM notification n,
	  								trial_test tt,
	  								trial_test_mark ttm,
	  								subject sj,
	  								student s
	  							WHERE n.object_id = 5
	  								AND ttm.trial_test_mark_num = n.object_num
  									AND tt.trial_test_num = n.object_parent_num
  									AND sj.subject_num = tt.subject_num
  									AND s.student_num = tt.student_num
  									AND n.status IN ('A', 'DA', 'AD')
  									AND s.block != 6
  									AND 3 = (SELECT count(n1.object_parent_num) 
  											FROM notification n1
  											WHERE n1.object_parent_num = n.object_parent_num
												AND n1.status = n.status)
  								ORDER BY n.object_parent_num, n.id");
	  	$stmt->execute();
	  	$result_trial_test_increase_notification = $stmt->fetchAll();
	  	$trial_test_increase_notification_count = $stmt->rowCount()/3;

	  	$stmt = $conn->prepare("SELECT n.id,
								    n.object_parent_num,
								    n.object_num,
								    s.surname,
								    s.name,
								    gi.group_name,
								    DATE_FORMAT(pg.created_date, '%d.%m.%Y') as created_date
								FROM notification n,
								    student s,
								    group_info gi,
								    progress_group pg,
								    progress_student ps
								WHERE n.object_id = 8
								    AND n.status = 'A' 
								    AND ps.progress_student_num = n.object_num
								    AND ps.progress_group_num = pg.progress_group_num
								    AND gi.group_info_num = pg.group_info_num
								    AND s.student_num = ps.student_num
								ORDER BY s.surname, s.name, n.id");
	  	$stmt->execute();
	  	$result_no_home_work_notification = $stmt->fetchAll();
	  	$no_home_work_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT n.id, 
	  								n.object_num,
	  								s.student_num,
	  								s.name, 
	  								s.surname,
	  								n.status,
	  								t.topic_name,
	  								sj.subject_name,
	  								sj.subject_num,
	  								qm.mark_theory,
	  								qm.mark_practice,
	  								DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date
	  							FROM notification n,
	  								student s,
	  								topic t,
	  								subject sj,
	  								quiz q,
	  								quiz_mark qm
	  							WHERE n.object_id = 6
  									AND n.status IN ('A', 'AD', 'DA') 
  									AND qm.quiz_mark_num = n.object_num
  									AND q.quiz_num = qm.quiz_num
  									AND t.topic_num = q.topic_num
  									AND sj.subject_num = t.subject_num
  									AND s.student_num = qm.student_num
  									AND s.block != 6");
	  	$stmt->execute();
	  	$result_quiz_max_mark_notification = $stmt->fetchAll();
	  	$quiz_max_mark_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT n.id, 
	  								n.object_num,
	  								n.status,
	  								s.student_num,
	  								s.name, 
	  								s.surname,
	  								t.topic_name,
	  								sj.subject_name,
	  								sj.subject_num,
	  								qm.mark_theory,
	  								qm.mark_practice,
	  								DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date
	  							FROM notification n,
	  								student s,
	  								topic t,
	  								subject sj,
	  								quiz q,
	  								quiz_mark qm
	  							WHERE n.object_id = 7
	  								AND n.status IN ('A', 'DA', 'AD') 
	  								AND qm.quiz_mark_num = n.object_num 
	  								AND q.quiz_num = qm.quiz_num 
	  								AND t.topic_num = q.topic_num 
	  								AND sj.subject_num = t.subject_num 
	  								AND s.student_num = qm.student_num 
	  								AND s.block != 6");
	  	$stmt->execute();
	  	$result_quiz_max_mark_2_notification = $stmt->fetchAll();
	  	$quiz_max_mark_2_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT s.student_num,
	  								s.surname,
	  								s.name, 
	  								DATE_FORMAT(s.dob, '%d.%m.%Y') as dob
	  							FROM student s
	  							WHERE DATE_FORMAT(s.dob, '%d.%m') = DATE_FORMAT(NOW(), '%d.%m')
	  								AND s.block != 6");
	  	$stmt->execute();
	  	$student_dob_count = $stmt->rowCount();
	  	$result_student_dob = $stmt->fetchAll();

	  	$stmt = $conn->prepare("SELECT count(student_num) as coming_dob_count 
						FROM student
						WHERE DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%m-%d')
							AND block != 6
							AND dob != '0000-00-00'
						    AND DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '%m-%d') >= DATE_FORMAT(dob, '%m-%d')");
	  	$stmt->execute();
	  	$dob_count = $stmt->fetch(PDO::FETCH_ASSOC);
	  	$coming_dob_count = $dob_count['coming_dob_count'];

	  	$stmt = $conn->prepare("SELECT count(*) as emp_coming_dob_count 
						FROM (SELECT name, surname, dob, block FROM admin 
								UNION
							SELECT name, surname, dob, block FROM teacher) A
						WHERE DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%m-%d')
							AND block != 6
							AND dob != '0000-00-00'
						    AND DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '%m-%d') >= DATE_FORMAT(dob, '%m-%d')");
	  	$stmt->execute();
	  	$dob_count = $stmt->fetch(PDO::FETCH_ASSOC);
	  	$emp_coming_dob_count = $dob_count['emp_coming_dob_count'];

	  	$stmt = $conn->prepare("SELECT quantity 
	  							FROM chocolate_history 
	  							WHERE id = 1;");
	  	$stmt->execute();
	  	$chocolate_count = $stmt->fetch(PDO::FETCH_ASSOC)['quantity'];

	  	$stmt = $conn->prepare("SELECT count(s.student_num) AS c
	  							FROM student s 
	  							WHERE s.student_num NOT IN (SELECT p.student_num 
	  														FROM parent p) 
	  								AND s.student_num != 'US5985cba14b8d3100168809' 
	  								AND s.block != 6");
		$stmt->execute();
		$no_parent_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

		$stmt = $conn->prepare("SELECT count(sh.id) AS c
								FROM sms_history sh
								WHERE sh.status = 'waiting_for_send'");
		$stmt->execute();
		$waiting_for_send_sms_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

		$stmt = $conn->prepare("SELECT count(p.parent_num) AS c
								FROM parent p,
									student s
								WHERE p.student_num = s.student_num
									AND s.block != 6
									AND p.checked = 0
									AND p.parent_order = 1");
		$stmt->execute();
		$parent_not_checked = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

		$stmt = $conn->prepare("SELECT scn.id,
									scn.student_num,
									scn.notification_status,
									s.surname, 
									s.name,
									s.phone
								FROM student_call_notification scn,
									student s
								WHERE s.student_num = scn.student_num
									AND scn.status = 0");
		$stmt->execute();
		$student_call_notification = $stmt->fetchAll();
		$student_call_notification_count = $stmt->rowCount();

		// $current_day = intval(date('d'));
		// $start_day = 25;
		// $end_day = 10;
		// $start_date = "";
		// $end_date = "";
		// $is_active_period = false;

		// if ($current_day >= $start_day) {
		// 	$start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
		// 	$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
		// 	$is_active_period = true;
		// } else if ($current_day <= $end_day) {
		// 	$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
		// 	$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
		// 	$is_active_period = true;
		// }

		// $stmt = $conn->prepare("SELECT s.surname,
	 //   								s.name,
	 //   								(SELECT count(sp.id)
		// 							FROM student_poll sp
		// 							WHERE sp.student_num = s.student_num
		// 								AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
		// 								AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')) AS is_polled
	 //   							FROM student s
	 //   							WHERE s.student_num != 'US5985cba14b8d3100168809'
  //  									AND s.block != 6
	 //   							GROUP BY s.student_num
	 //   							ORDER BY s.surname, s.name");
	 //   	$stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
		// $stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
	 //   	$stmt->execute();
	 //   	$not_polled_students_query = $stmt->fetchAll();

	 //   	$stmt = $conn->prepare("SELECT count(id) AS poll_info_count FROM teacher_poll_info");
		// $stmt->execute();
	 //    $total_poll_number = $stmt->fetch(PDO::FETCH_ASSOC)['poll_info_count'];

	 //   	$not_polled_students = array();
	 //    if ($total_poll_number > 0 && $is_active_period) {
	 //    	foreach ($not_polled_students_query as $value) {
	 //    		if ($value['is_polled'] == 0) {
	 //    			$tmp = array("surname" => $value['surname'], "name" => $value['name']);
	 //    			array_push($not_polled_students, $tmp);
	 //    		}
	 //    	}
	 //    }
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admin - Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet/less" type='text/css' href="css/style.less">
</head>
<body>
	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>
	<center>
		<div id='alert'>
			
		</div>
	</center>
<?php include_once('nav.php');?>
	<section id='body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<ul class="nav nav-tabs">
					 	<li role="presentation" class="navigation <?php echo ($_SESSION['page']==$pages[0]) ? "active" : "" ;?>" data='student'>
					 		<a href="#">Студент 
					 			<?php
					 				$student_total_success_notification_count = $prize_count
					 													+ $trial_test_top_notification_count
					 													+ $trial_test_increase_notification_count
					 													+ $quiz_max_mark_notification_count
					 													+ $quiz_max_mark_2_notification_count;

					 				$student_total_warning_notification_count = $finish_course_notification_count 
					 															+ $student_call_notification_count;

					 				$student_total_danger_notification_count = $attendance_notification_count
					 													+ $quiz_retake_notification_count
					 													+ $no_home_work_notification_count;

					 				$student_total_info_notification_count = $student_dob_count;
					 			?>
					 			<span class="label label-success"><?php echo ($student_total_success_notification_count > 0) ? $student_total_success_notification_count : ""; ?></span>
					 			<span class="label label-warning"><?php echo ($student_total_warning_notification_count > 0) ? $student_total_warning_notification_count : ""; ?></span>
					 			<span class="label label-danger"><?php echo ($student_total_danger_notification_count > 0) ? $student_total_danger_notification_count : ""; ?></span>
					 			<span class="label label-info"><?php echo ($student_total_info_notification_count > 0) ? $student_total_info_notification_count : ""; ?></span>
					 		</a>
					 	</li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[1]) ? "active" : "" ;?>' data='teacher'>
					 		<a href="#">
					 			Мұғалім
					 			<?php
					 				// if (count($not_polled_students) > 0) {
					 				// 	echo "<span class='label label-danger'>".count($not_polled_students)."</span>";
					 				// }
					 			?>
					 		</a>
					 	</li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[2]) ? "active" : "" ;?>' data='subject'><a href="#">Пән</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[3]) ? "active" : "" ;?>' data='group'><a href="#">Группа</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[4]) ? "active" : "" ;?>' data='parent'>
					 		<a href="#">Ата-ана
					 			<span class='label label-default'><?php echo ($no_parent_count > 0) ? $no_parent_count : ""; ?></span>
					 			<span class='label label-danger'><?php echo ($parent_not_checked > 0) ? $parent_not_checked : ""; ?></span>
					 		</a>
					 	</li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[5]) ? "active" : "" ;?>' data='schedule'><a href="#">Сабақ кестесі</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[7]) ? "active" : "" ;?>' data='progress_result'><a href="#">Прогресс. Қорытынды</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[6]) ? "active" : "" ;?>' data='dob'>
					 		<a href="#">Туған күн 
					 			<span class="label label-success"><?php echo ($coming_dob_count>0) ? $coming_dob_count : ""; ?></span>
					 			<span class="label label-success"><?php echo ($emp_coming_dob_count>0) ? $emp_coming_dob_count : ""; ?></span>
					 		</a>
					 	</li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[8]) ? "active" : "" ;?>' data='entrance_examination'><a href="#">Вступительный тест</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[9]) ? "active" : "" ;?>' data='chocolate'>
					 		<a href="#">Шоколад
					 			<?php if ($chocolate_count <= 2 && $chocolate_count > 0) { ?>
					 				<span class='label label-warning'><?php echo $chocolate_count; ?></span>
					 			<?php } else if ($chocolate_count <= 0) { ?>
					 				<span class='label label-danger'><?php echo $chocolate_count; ?></span>
					 			<?php } ?>
					 		</a>
					 	</li>
					 	<li role='presentation' class='navigation <?php echo ($_SESSION['page']==$pages[10]) ? 'active' : ''; ?>' data='ent_result'><a href='#'>ҰБТ қорытындылары</a></li>
					 	<li role='presentation' class='navigation <?php echo ($_SESSION['page']==$pages[11]) ? 'active' : ''; ?>' data='sms_history'>
					 		<a href="#">
					 			<?php echo ($waiting_for_send_sms_count > 0) ? "<b style='color:red;'>SMS</b>" : "SMS"; ?>
					 			<span class='label label-warning'><?php echo ($waiting_for_send_sms_count > 0) ? $waiting_for_send_sms_count : ""; ?></span>
					 		</a>
					 	</li>
					 	<li role='presentation' class='navigation <?php echo ($_SESSION['page']==$pages[12]) ? 'active' : ''; ?>' data='statistics'>
					 		<a href="#">Статистика</a>
					 	</li>
					 	<li role='presentation' class='navigation <?php echo ($_SESSION['page']==$pages[13]) ? 'active' : ''; ?>' data='calculator'>
					 		<a href="#">Калькулятор</a>
					 	</li>
					 	<li role='presentation' class='navigation <?php echo ($_SESSION['page']==$pages[14]) ? 'active' : ''; ?>' data='course_price'>
					 		<a href="#">Курс бағасы</a>
					 	</li>
					</ul>
					<br>
					<div class='student box' data-test="<?php echo $_SESSION['page'];?>" style='<?php echo ($_SESSION['page']==$pages[0]) ? "display:block;" : "display:none;"?>'>
						<button class='btn btn-success btn-sm student-modal' data-toggle='modal' data-target='.box-student-form' data-action='new-student' at='new-student' id='new-student-btn'>Жаңа оқушыны енгізу</button>
						<a class='btn btn-sm btn-default news' data-toggle='modal' data-target='.box-news' data-type='student'>Жаңалықтар (Студент)</a>
						<a class='btn btn-sm btn-default abs-reason' data-toggle='modal' data-target='.box-abs-reason' data-type='abs-reason'>Сабаққа келмеу себептері</a>
						<br>
						<br>
						<div class="btn-group-vertical" role="group">
							<?php if ($prize_count>0) {?>
					  		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-prize-notification' data-type='prize-notification' class="btn btn-success prize-notification">
					  			95 баллдан асқандар
					  			<span class='badge'><?php echo $prize_count; ?></span>
					  		</button>
					  		<?php } ?>
					  		<?php if ($trial_test_top_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-trial-test-top-notification' class="btn btn-success trial_test_top_notification">
					 			Пробный тесттен жоғарғы балл жинаған оқушы(лар)
					 			<span class='badge'><?php echo $trial_test_top_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($trial_test_increase_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-trial-test-increase-notification' class="btn btn-success trial_test_increase_notification">
					 			Пробный тесттен қатарынан 3 рет балын көтерген оқушы(лар)
					 			<span class='badge'><?php echo $trial_test_increase_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($quiz_max_mark_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-quiz-max-mark-notification'  class="btn btn-success quiz_max_mark_notification">
					 			Аралық бақылаудан 100% балл жинаған оқушы(лар)
					 			<span class='badge'><?php echo $quiz_max_mark_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($quiz_max_mark_2_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-quiz-max-mark-2-notification'  class="btn btn-success quiz_max_mark_2_notification">
					 			Аралық бақылаудан 100% баллды 1 айда 2-ші рет жинаған оқушы(лар)
					 			<span class='badge'><?php echo $quiz_max_mark_2_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($finish_course_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-finish-course-notification' class="btn btn-warning finish_course_notification">
					 			Жақында бітіретін оқушылар
					 			<span class='badge'><?php echo $finish_course_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($student_call_notification_count > 0) { ?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-student-call-notification' class='btn btn-warning'>
					 			Оқушыларға хабарласу
					 			<span class='badge'><?php echo $student_call_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($attendance_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-attendance-notification' class="btn btn-danger attendance-notification">
					 			2 күн қатарынан келмегендер
					 			<span class='badge'><?php echo $attendance_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($quiz_retake_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-quiz-retake-notification'  class="btn btn-danger quiz-retake-notification">
					 			Пересдачадан құлағандар
					 			<span class='badge'><?php echo $quiz_retake_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($no_home_work_notification_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-no-home-work-notification' class="btn btn-danger no-home-work-notification">
					 			Үй жұмысын орындамағандар
					 			<span class='badge'><?php echo $no_home_work_notification_count; ?></span>
					 		</button>
					 		<?php } ?>
					 		<?php if ($student_dob_count>0) {?>
					 		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-student-dob-notification' class="btn btn-info student-dob-notification">
					 			Оқушы(лар)дың туылған күні
					 			<span class='badge'><?php echo $student_dob_count; ?></span>
					 		</button>
					 		<?php } ?>
						</div>
						<br>
						<span class='search_box'>
							<?php include('load_search_box.php');?>
						</span>
						<select class='form-control pull-right search_type' style="width: 20%;">
							<option value='default'>Әдепкі қалпы</option>
							<option value='school'>Мектеп бойынша</option>
							<option value='teacher'>Мұғалім бойынша</option>
							<option value='subject'>Пән бойынша</option>
							<option value='group'>Группа бойынша</option>
						</select>
						<hr>
						<div class='students'>
							<?php 
								// if(!isset($search_type) || !in_array($search_type, $search_type_arr)){
									include_once('index_students.php');
								// }
								// else if(isset($search_type) && in_array($search_type, $search_type_arr)){
									// include_once('index_students_selective_search.php');	
								// }
							?>
						</div>
					</div>

					<div class='teacher box' style='<?php echo ($_SESSION['page']==$pages[1]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[1]) ? include_once('admin_'.$pages[1].'.php') : "";?>
					</div>

					<div class='subject box' style='<?php echo ($_SESSION['page']==$pages[2]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[2]) ? include_once('admin_'.$pages[2].'.php') : "";?>
					</div>

					<div class='group box' style='<?php echo ($_SESSION['page']==$pages[3]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[3]) ? include_once('admin_'.$pages[3].'.php') : "";?>
					</div>

					<div class='parent box' style='<?php echo ($_SESSION['page']==$pages[4]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[4]) ? include_once('admin_'.$pages[4].'.php') : "";?>
					</div>

					<div class='schedule box' style='<?php echo ($_SESSION['page']==$pages[5]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[5]) ? include_once('admin_'.$pages[5].'.php') : "";?>
					</div>

					<div class='progress_result box' style='<?php echo ($_SESSION['page']==$pages[7]) ? "display:block;" : "display:none;";?>'>
						<?php ($_SESSION['page']==$pages[7]) ? include_once('admin_'.$pages[7].'.php') : "";?>
					</div>

					<div class='dob box' style='<?php echo ($_SESSION['page']==$pages[6]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[6]) ? include_once('admin_'.$pages[6].'.php') : "";?>
					</div>

					<div class='entrance_examination box' style='<?php echo ($_SESSION['page']==$pages[8]) ? "display:block;" : "display:none"; ?>'>
						<?php ($_SESSION['page']==$pages[8]) ? include_once('admin_'.$pages[8].'.php') : ""; ?>
					</div>

					<div class='chocolate box' style='<?php echo ($_SESSION['page']==$pages[9]) ? "display:block;" : "display:none"; ?>'>
						<?php ($_SESSION['page']==$pages[9]) ? include_once('admin_'.$pages[9].'.php') : ""; ?>
					</div>
					<div class='ent_result box' style='<?php echo ($_SESSION['page']==$pages[10]) ? "display:block;" : "display:none"; ?>'>
						<?php ($_SESSION['page']==$pages[10]) ? include_once('admin_'.$pages[10].'.php') : ""; ?>
					</div>
					<div class='sms_history box' style='<?php echo ($_SESSION['page']==$pages[11]) ? "display:block;" : "display:none"; ?>'>
						<?php ($_SESSION['page']==$pages[11]) ? include_once('admin_'.$pages[11].'.php') : ""; ?>					
					</div>
					<div class='statistics box' style='<?php echo ($_SESSION['page']==$pages[12]) ? "display:block;" : "display:none;"; ?>'>
						<?php ($_SESSION['page']==$pages[12]) ? include_once('admin_'.$pages[12].'.php') : ""; ?>
					</div>
					<div class='calculator box' style='<?php echo ($_SESSION['page']==$pages[13]) ? "display: block;" : "display: none;"; ?>'>
						<?php ($_SESSION['page']==$pages[13]) ? include_once('admin_'.$pages[13].'.php') : ""; ?>
					</div>
					<div class='course_price box' style='<?php echo ($_SESSION['page']==$pages[14]) ? "display: block;" : "display: none;"; ?>'>
						<?php ($_SESSION['page']==$pages[14]) ? include_once('admin_'.$pages[14].'.php') : ""; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<!-- <center><a data-toggle='modal' class='btn' data-target='.box-data'><u>Өзгерту</u></a></center> -->
	<div class='edit-data'>
		<div class='box'>
		</div>
	</div>

<!-- Large modal -->
<?php 
	try {
		$stmt = $conn->prepare("SELECT subject_num, subject_name FROM subject order by subject_name asc");
	    $stmt->execute();
	    $result_subject_modal = $stmt->fetchAll();
	    $for_topic = '';
	    $subject_name = '';
	    if($stmt->rowCount()>0){
	    	$for_topic = $result_subject_modal[0][0];
	    	$subject_name = $result_subject_modal[0][1];
	    }
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>

<div class="modal fade" id='config-box' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
			<center><h4 class='modal-title'>Пәндердің конфигурациясы</h4></center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-data" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
			<div class='row'>
				<div class='col-md-4 col-sm-4'>
					<select class='edit-modal form-control' data-name='subject'>
						<?php foreach ($result_subject_modal as $value) { ?>
							<option value="<?php echo $value['subject_num'];?>"><?php echo $value['subject_name'];?></option>
						<?php } ?>
					</select>
					<form class='form-inline edit-subject' action='admin_controller.php' method="post">
						<div class='form-group'>
							<input type="hidden" name="data_num" value='<?php echo $for_topic;?>'>
							<input type="text" class='form-control' name="data_name" value='<?php echo $subject_name;?>'>
							<input type="submit" class='btn btn-xs btn-success' name="edit_subject">
						</div>
					</form>
				</div>
				<div class='col-md-4 col-sm-4'>
					<div class='edit-modal topic-list' data-name='topic'>
						<?php 
							$part = 'header-part';
							include("edit_modal.php");
						?>
					</div>
				</div>
				<div class='col-md-4 col-sm-4' style='display: none;'>
					<form class='delete_subject' onsubmit='return beforeSubmit();' action='admin_controller.php' method='post'>
						<input type="hidden" name="data_num" value='<?php echo $for_topic;?>'>
						<input type="submit" class='btn btn-xs btn-default delete-btn' name="delete_subject" value='Удалить "<?php echo $subject_name;?>"'>
					</form>
				</div>
			</div>
    	</div>
    	<div class="modal-body">
    		<?php
    			$part = 'body-part';
    			$subpart = 'topic';
    			include("edit_modal.php");
    		?>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-comment-for-teacher" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<center>
    					<form class='form-inline' method='post' action='admin_controller.php'>
    						<div class='form-class'>
    							<?php
    								$count = 0;
    								try {
    									$description = 'review';
    									$stmt = $conn->prepare("SELECT * FROM review_info where description = :description");
    									$stmt->bindParam(':description', $description, PDO::PARAM_STR);
    									$stmt->execute();
    									$result_reviews = $stmt->fetchAll();
    								} catch (PDOException $e) {
										echo "Error: " . $e->getMessage();
									}
    							?>
    							<?php foreach($result_reviews as $value){ ?>
    							<div class='form-group' style='display: block;'>
    								<label><b><?php ++$count; ?></b></label>
    								<input type="text" name="review" class='form-control input_comment' data-toggle="tooltip" data-placement="left" title="<?php echo $value['review_text']; ?>" value='<?php echo $value['review_text']; ?>' required="" placeholder="М: Сабақ үлгерімі.">
    								&nbsp;&nbsp;
    								<a class="btn btn-sm btn-danger" data-action="remove" name="">Удалить</a>
    								<a style="display:none;" class="btn btn-sm btn-primary" data-action="restore" name="">Восстановить</a>
    								&nbsp;&nbsp;
    								<a style="display:none;" class="btn btn-sm btn-warning" data-action="reset" name="">Отмена</a>
    								<input type="hidden" name="rin[]" value='<?php echo $value['review_info_num'];?>'>
    							</div>
    							<?php } ?>
    						</div>
    						<br>
    						<a style='width: 10%; border:1px solid lightgray' class='btn btn-sm add-row-review'><b>+</b></a>
    						<hr>
    						<?php
    							try {
    								$description = 'comment';
									$stmt = $conn->prepare("SELECT * FROM review_info where description = :description");
									$stmt->bindParam(':description', $description, PDO::PARAM_STR);
									$stmt->execute();
									$result_comment = $stmt->fetch(PDO::FETCH_ASSOC);
    							} catch (PDOException $e) {
									echo "Error: " . $e->getMessage();
								}
    						?>
    						<div class='form-group'>
    							<table class='table table-bordered'>
    								<tr>
    									<td style='width: 50%'><?php echo ($result_comment['review_text']=='') ? "N/A" : nl2br($result_comment['review_text']);?></td>
    									<td style='width: 50%'><textarea cols='50' rows='10' class='form-control' name='review_comment'><?php echo $result_comment['review_text'];?></textarea></td>
    								</tr>
    							</table>
    						</div>
    						<hr>
    						<input type="hidden" name="review_comment_num" value='<?php echo $result_comment['review_info_num'];?>'>
    						<input type="submit" class='btn btn-sm btn-success' name="comment_for_teacher" value='Сақтау'>
    					</form>
    				</center>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-quiz-max-mark-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Аралық бақылаудан 100% балл алған оқушы(лар)</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					foreach ($result_quiz_max_mark_notification as $key => $value) { 
	    					?>
	    					<tr>
	    						<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
	    						<td style='width: 80%;'>
	    							<center>
			    						<span>
			    							<span class='h4'><b><?php echo '<a href="student_info_marks.php?data_num='.$value["student_num"].'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $value['subject_name']." ".$value['topic_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php
			    								if (array_key_exists($value['subject_num'], $config_subject_quiz) && $config_subject_quiz[$value['subject_num']]) {
			    									if ($config_subject_quiz[$value['subject_num']]['theory'] == 1) {
			    										echo "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>&nbsp;&nbsp;";
			    									}
			    									if ($config_subject_quiz[$value['subject_num']]['practice'] == 1) {
			    										echo "<span class='h4 text-success'><b>Есеп: ".$value['mark_practice']."</b></span>&nbsp;&nbsp;";
			    									}
			    								}
			    							?>
			    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
			    						</span>
			    					</center>
		    					</td>
		    					<td style='width: 15%;'>
		    						<center>
		    							<input type="hidden" name="edit[]" value="0">
		    							<input type="hidden" name="nid[]" value='<?php echo $value['id'];?>'>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == 'AD') {
		    							?>
		    							<span>
		    								<input type="hidden" name="discount[]" value="">
		    								<a class='btn btn-sm btn-danger give-discount'>10% Скидка</a>
		    								<a class='btn btn-sm btn-warning restore-discount' style='display: none;'>Отмена. 10% скидка</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>10% скидка берілді</b>
		    							<input type="hidden" name="discount[]" value="Deleted">
		    							<?php } ?>
		    							<br>
		    							<br>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == "DA") {
		    							?>
		    							<span>
		    								<input type="hidden" name="chocolate[]" value="">
		    								<a class='btn btn-sm btn-danger give-chocolate'>Шоколад</a>
		    								<a class='btn btn-sm btn-warning restore-chocolate' style='display: none;'>Отмена. Шоколад</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>Шоколад берілді</b>
		    							<input type="hidden" name="chocolate[]" value="Deleted">
		    							<?php } ?>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_quiz_max_mark_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-quiz-max-mark-2-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Аралық бақылаудан 100% баллды 1 айда 2-ші рет алған оқушы(лар)</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					foreach ($result_quiz_max_mark_2_notification as $key => $value) { 
	    					?>
	    					<tr>
	    						<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
	    						<td style='width: 80%;'>
	    							<center>
			    						<span>
			    							<span class='h4'><b><?php echo '<a href="student_info_marks.php?data_num='.$value["student_num"].'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $value['subject_name']." ".$value['topic_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php
			    								if (array_key_exists($value['subject_num'], $config_subject_quiz) && $config_subject_quiz[$value['subject_num']]) {
			    									if ($config_subject_quiz[$value['subject_num']]['theory'] == 1) {
			    										echo "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>&nbsp;&nbsp;";
			    									}
			    									if ($config_subject_quiz[$value['subject_num']]['practice'] == 1) {
			    										echo "<span class='h4 text-success'><b>Есеп: ".$value['mark_practice']."</b></span>&nbsp;&nbsp;";
			    									}
			    								}
			    							?>
			    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
			    						</span>
			    					</center>
		    					</td>
		    					<td style='width: 15%;'>
		    						<center>
		    							<input type="hidden" name="edit[]" value="0">
		    							<input type="hidden" name="nid[]" value='<?php echo $value['id'];?>'>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == 'AD') {
		    							?>
		    							<span>
		    								<input type="hidden" name="discount[]" value="">
		    								<a class='btn btn-sm btn-danger give-discount'>5% Скидка</a>
		    								<a class='btn btn-sm btn-warning restore-discount' style='display: none;'>Отмена. 5% скидка</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>10% скидка берілді</b>
		    							<input type="hidden" name="discount[]" value="Deleted">
		    							<?php } ?>
		    							<br>
		    							<br>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == "DA") {
		    							?>
		    							<span>
		    								<input type="hidden" name="chocolate[]" value="">
		    								<a class='btn btn-sm btn-danger give-chocolate'>Шоколад</a>
		    								<a class='btn btn-sm btn-warning restore-chocolate' style='display: none;'>Отмена. Шоколад</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>Шоколад берілді</b>
		    							<input type="hidden" name="chocolate[]" value="Deleted">
		    							<?php } ?>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_quiz_max_mark_2_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-prize-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<!-- <h3>Алтын білім үздіктері</h3> -->
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					foreach ($prize_notification_result as $key => $value) { 
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count;?></center></td>
	    						<td>
	    							<center>
			    						<span>
			    							<span class='h4'><b><?php echo '<a href="student_info_marks.php?data_num='.$value["student_num"].'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $value['group_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php
			    								if (array_key_exists($value['subject_num'], $config_subject_quiz) && $config_subject_quiz[$value['subject_num']]) {
			    									if ($config_subject_quiz[$value['subject_num']]['theory'] == 1) {
			    										echo "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>&nbsp;&nbsp;";
			    									}
			    									if ($config_subject_quiz[$value['subject_num']]['practice'] == 1) {
			    										echo "<span class='h4 text-success'><b>Есеп: ".$value['mark_practice']."</b></span>&nbsp;&nbsp;";
			    									}
			    								}
			    							?>
			    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
			    						</span>
			    					</center>
		    					</td>
		    					<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $value['id'];?>'>
		    							<a class='btn btn-sm btn-danger' data-action='remove-prize-notification'>Шоколад беру</a>
		    							<a class='btn btn-sm btn-warning' data-action='restore-prize-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_prize_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-trial-test-top-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пробный тесттен жоғарғы балл жинаған оқушы(лар)</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					foreach ($result_trial_test_top_notification as $key => $value) { 
	    					?>
	    					<tr>
	    						<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
	    						<td style='width: 80%;'>
	    							<center>
			    						<span>
			    							<span class='h4'><b><?php echo '<a href="student_info_marks.php?data_num='.$value["student_num"].'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4 text-success'><b><?php echo "Балл: ".$value['mark'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value['subject_name']; ?>]</span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value["date_of_test"]; ?>]</span>
			    						</span>
			    					</center>
		    					</td>
		    					<td style='width: 15%;'>
		    						<center>
		    							<input type="hidden" name="edit[]" value="0">
		    							<input type="hidden" name="nid[]" value='<?php echo $value['id'];?>'>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == 'AD') {
		    							?>
		    							<span>
		    								<input type="hidden" name="discount[]" value="">
		    								<a class='btn btn-sm btn-danger give-discount'>10% Скидка</a>
		    								<a class='btn btn-sm btn-warning restore-discount' style='display: none;'>Отмена. 10% скидка</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>10% скидка берілді</b>
		    							<input type="hidden" name="discount[]" value="Deleted">
		    							<?php } ?>
		    							<br>
		    							<br>
		    							<?php
		    								if ($value['status'] == "A" || $value['status'] == "DA") {
		    							?>
		    							<span>
		    								<input type="hidden" name="chocolate[]" value="">
		    								<a class='btn btn-sm btn-danger give-chocolate'>Шоколад</a>
		    								<a class='btn btn-sm btn-warning restore-chocolate' style='display: none;'>Отмена. Шоколад</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>Шоколад берілді</b>
		    							<input type="hidden" name="chocolate[]" value="Deleted">
		    							<?php } ?>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_trial_test_top_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-trial-test-increase-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пробный тесттен қатарынан 3 рет балын көтерген оқушы(лар)</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					for ($i=0; $i < count($result_trial_test_increase_notification); $i=$i+3) {
	    					?>
	    					<tr>
	    						<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
	    						<td style='width: 80%;'>
	    							<center>
			    						<span>
			    							<span class='h4'>
			    								<b>
			    									<?php 
				    									echo '<a href="student_info_marks.php?data_num='.$result_trial_test_increase_notification[$i]["student_num"].'" target="_blank">'.$result_trial_test_increase_notification[$i]['surname'];
				    									echo " ";
				    									echo $result_trial_test_increase_notification[$i]['name'].'</a>';
				    									echo " | ";
				    									echo $result_trial_test_increase_notification[$i]['subject_name'];
			    									?>
			    								</b>
			    							</span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4' style='color:#999;'>
			    								<?php echo $result_trial_test_increase_notification[$i]['date_of_test']; ?>
			    							</span>
			    							<b>:</b> 
			    							<span class='h4 text-success'>
			    								<b><?php echo $result_trial_test_increase_notification[$i]['mark']; ?></b>
			    							</span> &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							
			    							<span class='h4' style='color:#999;'>
			    								<?php echo $result_trial_test_increase_notification[$i+1]['date_of_test']; ?>
			    							</span>
			    							<b>:</b>
			    							<span class='h4 text-success'>
			    								<b>
			    									<?php echo $result_trial_test_increase_notification[$i+1]['mark']; ?>
			    								</b>
			    							</span> &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							
			    							<span class='h4' style='color:#999;'>
			    								<?php echo $result_trial_test_increase_notification[$i+2]['date_of_test']; ?>
			    							</span>
			    							<b>:</b>
			    							<span class='h4 text-success'>
			    								<b>
			    									<?php echo $result_trial_test_increase_notification[$i+2]['mark']; ?>
			    								</b>
			    							</span> 
			    						</span>
			    					</center>
		    					</td>
		    					<td style='width: 15%;'>
		    						<center>
		    							<input type="hidden" name="edit[]" value="0">
		    							<input type="hidden" name="nid[]" value='<?php echo $result_trial_test_increase_notification[$i]['id']; ?>'>
		    							<input type="hidden" name="opn[]" value='<?php echo $result_trial_test_increase_notification[$i]['object_parent_num']; ?>'>
		    							<?php
		    								if ($result_trial_test_increase_notification[$i]['status'] == "A" || $result_trial_test_increase_notification[$i]['status'] == 'AD') {
		    							?>
		    							<span>
		    								<input type="hidden" name="discount[]" value="">
		    								<a class='btn btn-sm btn-danger give-discount'>10% скидка</a>
		    								<a class='btn btn-sm btn-warning restore-discount' style='display: none;'>Отмена. 10% скидка</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>10% скидка берілді</b>
		    							<input type="hidden" name="discount[]" value="Deleted">
		    							<?php } ?>
		    							<br>
		    							<br>
		    							<?php
		    								if ($result_trial_test_increase_notification[$i]['status'] == "A" || $result_trial_test_increase_notification[$i]['status'] == "DA") {
		    							?>
		    							<span>
		    								<input type="hidden" name="chocolate[]" value="">
		    								<a class='btn btn-sm btn-danger give-chocolate'>Шоколад</a>
		    								<a class='btn btn-sm btn-warning restore-chocolate' style='display: none;'>Отмена. Шоколад</a>
		    							</span>
		    							<?php } else { ?>
		    							<b class='text-success'>Шоколад берілді</b>
		    							<input type="hidden" name="chocolate[]" value="Deleted">
		    							<?php } ?>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_trial_test_increase_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-attendance-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>2 күн қатарынан келмеген оқушылар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    						$count = 0;
	    						for ($i=0; $i < count($result_an); $i=$i+2) {
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count; ?></center></td>
	    						<td>
	    							<center>
	    								<span>
			    							<span class='h4'>
			    								<b>
			    									<a href="student_info_marks.php?data_num=<?php echo $result_an[$i]["student_num"]; ?>" target="_blank">
			    										<?php echo $result_an[$i]['surname']." ".$result_an[$i]['name'];?>
			    									</a>
			    								</b>
			    							</span>
			    								&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $result_an[$i]['group_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4' style='color:#999;'>Дата 1:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i]['created_date']; ?></b></span>
			    							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							<span class='h4' style='color:#999;'>Дата 2:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i+1]['created_date']; ?></b></span>
			    							<!-- &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							<span class='h4' style='color:#999;'>Дата 3:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i+2]['created_date']; ?></b></span> -->
			    						</span>
	    							</center>
	    						</td>
	    						<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $result_an[$i]['attendance_notification_num'];?>'>
		    							<a class='btn btn-sm btn-danger' data-item='ann' data-action='remove-attendance-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-item='ann' data-action='restore-attendance-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_attendance_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-no-home-work-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Үй жұмысын орындымағандар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    						$count = 0;
	    						for ($i=0; $i < count($result_no_home_work_notification); $i++) {
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count; ?></center></td>
	    						<td>
	    							<center>
	    								<span>
			    							<span class='h4'><b><?php echo $result_no_home_work_notification[$i]['surname']." ".$result_no_home_work_notification[$i]['name'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $result_no_home_work_notification[$i]['group_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4' style='color:#999;'>Дата: </span> <span class='h4 text-danger'><b><?php echo $result_no_home_work_notification[$i]['created_date']; ?></b></span>
			    						</span>
	    							</center>
	    						</td>
	    						<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $result_no_home_work_notification[$i]['id']."|".$result_no_home_work_notification[$i]['object_parent_num']; ?>'>
		    							<a class='btn btn-sm btn-danger' data-item='ann' data-action='remove-no-home-work-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-item='ann' data-action='restore-no-home-work-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_no_home_work_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-quiz-retake-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пересдачадан құлағандар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    						$count = 0;
	    						for ($i=0; $i < count($result_qrn); $i=$i+2) {
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count; ?></center></td>
	    						<td>
	    							<center>
	    								<span>
			    							<span class='h4'><b><?php echo $result_qrn[$i]['surname']." ".$result_qrn[$i]['name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h5'>[<?php echo $result_qrn[$i]['subject_name'].", ".$result_qrn[$i]['topic_name']; ?>]</span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php
			    								if (array_key_exists($result_qrn[$i]['subject_num'], $config_subject_quiz) && $config_subject_quiz[$result_qrn[$i]['subject_num']]) {
			    									if ($config_subject_quiz[$result_qrn[$i]['subject_num']]['theory'] == 1) {
			    										echo "<span class='h4 text-danger'><b>Теория: ".$result_qrn[$i]['mark_theory']."</b></span>&nbsp;&nbsp;";
			    									}
			    									if ($config_subject_quiz[$result_qrn[$i]['subject_num']]['practice'] == 1) {
			    										echo "<span class='h4 text-danger'><b>Есеп: ".$result_qrn[$i]['mark_practice']."</b></span>";
			    									}
			    								}
			    							?>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $result_qrn[$i]["created_date"]; ?>]</span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php
			    								if (array_key_exists($result_qrn[$i+1]['subject_num'], $config_subject_quiz) && $config_subject_quiz[$result_qrn[$i+1]['subject_num']]) {
			    									if ($config_subject_quiz[$result_qrn[$i+1]['subject_num']]['theory'] == 1) {
			    										echo "<span class='h4 text-danger'><b>Теория: ".$result_qrn[$i+1]['mark_theory']."</b></span>&nbsp;&nbsp;";
			    									}
			    									if ($config_subject_quiz[$result_qrn[$i+1]['subject_num']]['practice'] == 1) {
			    										echo "<span class='h4 text-danger'><b>Есеп: ".$result_qrn[$i+1]['mark_practice']."</b></span>";
			    									}
			    								}
			    							?>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $result_qrn[$i+1]["created_date"]; ?>]</span>
			    						</span>
	    							</center>
	    						</td>
	    						<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $result_qrn[$i]['id'];?>'>
		    							<a class='btn btn-sm btn-danger' data-item='quizRetakeNot' data-action='remove-quiz-retake-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-item='quizRetakeNot' data-action='restore-quiz-retake-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_quiz_retake_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-finish-course-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Жақында бітіретін оқушылар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    						$count = 0;
							foreach ($result_finish_course_notification as $value) { 					
    					?>
    					<tr>
    						<td><center><?php echo ++$count; ?></center></td>
    						<td>
    							<center>
    								<span>
		    							<span class='h4'><b><a href="student_info_marks.php?data_num=<?php echo $value['student_num'];?>" target="_blank"><?php echo $value['surname']." ".$value['name'];?></a></b></span>
		    						</span>
		    						<span>
		    							<span class='h5'>[<?php echo $value['subject_name']; ?>]</span>
		    						</span>
		    						<br>
    							</center>
    						</td>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class='modal fade box-student-call-notification' tabindex='-1' role='dialog'>
	<div class='modal-dialog modal-lg' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>X</span></button>
				<center><h3>Оқушыларға хабарласып, жағдайын сұрау</h3></center>
			</div>
			<div class='modal-body'>
				<div class='row'>
					<div class='col-md-12 col-sm-12'>
						<form action='admin_controller.php' method='POST'>
							<table class='table table-bordered'>
								<tr>
									<td style="width: 50%;" rowspan='<?php echo $student_call_notification_count + 1; ?>'>
										Оқушымен диалог:
										<br><br>
										1. Алло, ..., саламатсың ба. Бұл Altyn Bilim оқу орталығының менеджері - Тоғжан. Сенің оқуың жайлы сұрайын деп едім.
										<br><br>
										2. Сабағың қалай? Біздің орталықта оқуда қиыншылықтар болып жатқан жоқ па?
										<br><br>
										3. Тақырыптардың барлығы түсінікті болып жүр ме?
										<br><br>
										4. Мұғалімің сабақты дұрыс түсіндіріп, үйретіп жүр ме?
										<br><br>
										5. Егер қандай да бір сұрақ немесе оқуда проблема болып жатса, маған келіп айтсаң болады немесе осы номерге ватсапқа жазсаң болады.
										<br><br>
										Рақмет, сау бол!
									</td>
								</tr>
								<?php
									$count = 0;
									foreach ($student_call_notification as $value) {
								?>
								<tr>
									<td style='width: 50%;'>
										<a href="student_info_marks.php?data_num=<?php echo $value['student_num'];?>" target="_blank">
											<span>
												<b><?php echo (++$count).') '.$value['surname'].' '.$value['name']; ?></b>
												<?php echo " (".($value['notification_status'] == 1 ? "14 күн бұрын" : "2 ай сайын").")"; ?>
											</span>
										</a>
										<button class='btn btn-success btn-xs pull-right select-scn' type='button'>Хабарласылды</button>
										<button class='btn btn-warning btn-xs pull-right cancel-scn' type='button' style='display: none;'>Отмена</button>
										<input type="hidden" name="" data-name='id[]' value='<?php echo $value['id']; ?>'>
									</td>
								</tr>
								<?php } ?>
							</table>
							<center>
								<button type='submit' name='save-scn-form' class='btn btn-success'>Сақтау</button>
							</center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade box-student-dob-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Оқушы(лар)дың туылған күні</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    						$count = 0;
							foreach ($result_student_dob as $value) { 					
    					?>
    					<tr>
    						<td><center><?php echo ++$count; ?></center></td>
    						<td>
    							<center>
    								<span>
		    							<span class='h4'><b><a href="student_info_marks.php?data_num=<?php echo $value['student_num'];?>" target="_blank"><?php echo $value['surname']." ".$value['name'];?></a> <span><?php echo $value['dob']; ?></span></b></span>
		    						</span>
    							</center>
    						</td>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-group-schedule" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Сабақ кестесі</h3></center>
    		<center><h4 class="modal-title" id='group_name'></h4></center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-universal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title"></h3></center>
    	</div>
    	<div class="modal-body">

    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-news box-suggestion box-abs-reason box-student-form box-collect-data entrance-examination-student" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title"></h3></center>
    	</div>
    	<div class="modal-body">

    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-chocolate-history" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Шоколадтарды енгізген уақыттары</h3></center>
    	</div>
    	<div class="modal-body">
    		<?php
    			$chocolate_result = array();
    			$total_chocolate = 0;
    			try {
    				
    				$stmt = $conn->prepare("SELECT quantity, DATE_FORMAT(date, '%d.%m.%Y') as d FROM chocolate_history WHERE id != 1 ORDER BY date DESC");
    				$stmt->execute();
    				$chocolate_result = $stmt->fetchAll();

    				$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM chocolate_history WHERE id != 1");
    				$stmt->execute();
    				$total_chocolate = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    			} catch (PDOException $e) {
					echo "Error ".$e->getMessage()." !!!";
				}
				echo "<center><h2>Барлығы: ".$total_chocolate."шт. </h2></center><hr>";
				foreach ($chocolate_result as $key => $value) {
    		?>
    		<center>
    			<h2 style='color:#555;'><?php echo $value['quantity']; ?>шт : <?php echo $value['d']; ?></h2>
    		</center>
    		<?php } ?>
    	</div> 
    </div>
  </div>
</div>

<div class='modal fade box-ent-result' tabindex='-1' role='dialog'>
	<div class='modal-dialog modal-lg' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
			</div>
			<div class='modal-body'></div>
		</div>
	</div>
</div>
	

	<!-- <script src="js/index.js"></script> -->
	<script type="text/javascript">
		// ---------------------------------------review_start------------------------------------
$(document).on('click','.add-row-review',function(){
	$('.modal-body .form-class').append('<div class="form-group" style="display:block;"><label><b></b></label><input type="text" class="form-control" required="" name="new_review[]" placeholder="М: Сабақ үлгерімі.">&nbsp;&nbsp;<a class="btn btn-sm btn-danger" data-action="remove" name="">Удалить</a><a style="display:none;" class="btn btn-sm btn-primary" data-action="restore" name="">Восстановить</a>&nbsp;&nbsp;<a style="display:none;" class="btn btn-sm btn-warning" data-action="reset" name="">Отмена</a></div>');
});
$(document).on('click', '.add-row-reason', function() {
	$('.box-abs-reason .modal-body .form-class').append('<div class="form-group" style="display:block;"><label><b></b></label><input type="text" class="form-control" required="" name="new_reason[]" placeholder="М: Ауырып калдым.">&nbsp;&nbsp;<a class="btn btn-sm btn-danger" data-action-reason="remove" name="">Удалить</a><a style="display:none;" class="btn btn-sm btn-primary" data-action-reason="restore" name="">Восстановить</a>&nbsp;&nbsp;<a style="display:none;" class="btn btn-sm btn-warning" data-action-reason="reset" name="">Отмена</a></div>');
});
$(document).on('click', 'a[data-action=remove]', function() {
	if ($(this).parent().find('input').attr('name') == 'new_review[]') {
		$(this).parent().remove();
	} else if ($(this).parent().find('input').attr('name') == 'review') {
		lightAlert($(this).parent(), '#d9534f', 0.3, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name', 'rin_remove[]');
		$(this).parent().find('a[data-action=restore]').show();
		$(this).parent().find('input[name=review]').prop("disabled", true);
		$(this).parent().find('input[name=review]').attr('name', 'remove_review[]');
	}
});
$(document).on('click','a[data-action-reason=remove]',function(){
	if($(this).parent().find('input').attr('name')=='new_reason[]'){
		$(this).parent().remove();
	}
	else if($(this).parent().find('input').attr('name')=='reason'){
		lightAlert($(this).parent(), '#d9534f', 0.3, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin_remove[]');
		$(this).parent().find('a[data-action-reason=restore]').show();
		$(this).parent().find('input[name=reason]').prop( "disabled", true );
		$(this).parent().find('input[name=reason]').attr('name','remove_reason[]');
	}
});
$(document).on('click','a[data-action=restore]',function(){
	if($(this).parent().find('input').attr('name')=='remove_review[]'){
		lightAlert($(this).parent(), '#5cb85c', 0, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		$(this).parent().find('a[data-action=remove]').show();
		$(this).parent().find('input[name="remove_review[]"]').prop( "disabled", false );
		$(this).parent().find('input[name="remove_review[]"]').attr('name','review');
	}
});
$(document).on('click','a[data-action-reason=restore]',function(){
	if($(this).parent().find('input').attr('name')=='remove_reason[]'){
		lightAlert($(this).parent(), '#5cb85c', 0, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		$(this).parent().find('a[data-action-reason=remove]').show();
		$(this).parent().find('input[name="remove_reason[]"]').prop( "disabled", false );
		$(this).parent().find('input[name="remove_reason[]"]').attr('name','reason');
	}
});

$(document).on('keyup','.box-comment-for-teacher .modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action=reset]').show();
		if($(this).parent().find('input').attr('name')=='review'){
			$(this).parent().find('input[name=review]').attr('name','update_review[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_review[]'){
			$(this).parent().find('input[name="update_review[]"]').attr('name','review');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});
$(document).on('keyup','.box-abs-reason .modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action-reason=reset]').show();
		if($(this).parent().find('input').attr('name')=='reason'){
			$(this).parent().find('input[name=reason]').attr('name','update_reason[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action-reason=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_reason[]'){
			$(this).parent().find('input[name="update_reason[]"]').attr('name','reason');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});

$(document).on('click','.box-comment-for-teacher .modal-body .form-class a[data-action=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_review[]'){
		$(this).parent().find('input[name="update_review[]"]').attr('name','review');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});

$(document).on('click','.box-abs-reason .modal-body .form-class a[data-action-reason=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_reason[]'){
		$(this).parent().find('input[name="update_reason[]"]').attr('name','reason');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});

$(document).on('click', 'a[data-action=remove-prize-notification], a[data-action=remove-trial-test-top-notification], a[data-action=remove-trial-test-increase-notification], a[data-action=remove-no-home-work-notification], a[data-action=remove-quiz-max-mark-notification],  a[data-action=remove-quiz-max-mark-2-notification]',function(){
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name','spn[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-prize-notification], a[data-action=restore-trial-test-top-notification], a[data-action=restore-trial-test-increase-notification], a[data-action=restore-no-home-work-notification], a[data-action=restore-quiz-max-mark-notification], a[data-action=restore-quiz-max-mark-2-notification]',function(){
	if($(this).prev().prev().attr('name')=='spn[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('click', 'a[data-action=remove-attendance-notification], a[data-action=remove-quiz-retake-notification]',function(){
	$item = $(this).data('item');
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name',$item+'[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-attendance-notification], a[data-action=restore-quiz-retake-notification]',function(){
	if($(this).prev().prev().attr('name')==$item+'[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('keyup','.modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action=reset]').show();
		if($(this).parent().find('input').attr('name')=='review'){
			$(this).parent().find('input[name=review]').attr('name','update_review[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_review[]'){
			$(this).parent().find('input[name="update_review[]"]').attr('name','review');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});
$(document).on('click','.modal-body .form-class a[data-action=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_review[]'){
		$(this).parent().find('input[name="update_review[]"]').attr('name','review');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});


$(document).on('click', '.give-chocolate', function(){
	$(this).parents('td').find('input[name="chocolate[]"]').val("D");
	$edit_count = parseInt($(this).parents('td').find('input[name="edit[]"]').val());
	$(this).parents('td').find('input[name="edit[]"]').val($edit_count+1);

	$(this).hide();
	$(this).parents("tr").find('.restore-chocolate').show();
});
$(document).on('click', '.restore-chocolate', function(){
	$(this).parents('td').find('input[name="chocolate[]"]').val("");
	$edit_count = parseInt($(this).parents('td').find('input[name="edit[]"]').val());
	$(this).parents('td').find('input[name="edit[]"]').val($edit_count-1);

	$(this).hide();
	$(this).parents("tr").find('.give-chocolate').show();
});

$(document).on('click', '.give-discount', function(){
	$(this).parents('td').find('input[name="discount[]"]').val("D");
	$edit_count = parseInt($(this).parents('td').find('input[name="edit[]"]').val());
	$(this).parents('td').find('input[name="edit[]"]').val($edit_count+1);

	$(this).hide();
	$(this).parents("tr").find('.restore-discount').show();
});
$(document).on('click', '.restore-discount', function(){
	$(this).parents('td').find('input[name="discount[]"]').val("");
	$edit_count = parseInt($(this).parents('td').find('input[name="edit[]"]').val());
	$(this).parents('td').find('input[name="edit[]"]').val($edit_count-1);

	$(this).hide();
	$(this).parents("tr").find('.give-discount').show();
});

// ---------------------------------------review_end--------------------------------------
$(document).on('keyup','.topic-input input',function(){
	if($(this).val().toLowerCase()=='бақылау'){
		$(this).val('');
		$(this).removeAttr('name','');
		$(this).parent().slideUp('fast');
		$(this).parent().next().find('textarea').val("Аралық бақылау: ");
		$(this).parent().next().find('textarea').attr('name','new-quiz-name');
		$(this).parent().next().slideDown('fast');
	}
});
$(document).on('keyup','.quiz-input textarea',function(){
	if($(this).val().toLowerCase().substr(0,15)!="аралық бақылау:"){
		$(this).val('');
		$(this).removeAttr('name');
		$(this).parent().slideUp('fast');
		$(this).parent().prev().find('input').val('');
		$(this).parent().prev().find('input').attr('name','new-topic-name');
		$(this).parent().prev().slideDown('fast');
	}
});

$globalSubjectName = '<?php echo $subject_name;?>';
$(document).on('mouseover','.edit-input, .input_comment, .schedule-student, .notification-sign-detail',function(){
	$('[data-toggle="tooltip"]').tooltip();
});
function lightAlert($element, $color, $opacity, $time){
	$element.css({'background-color':$color});
	$res = $element.css( "background-color" )
	$bgColor = $res.substring(4,$res.length-1);
	$element.stop();
	$element.animate({backgroundColor: 'rgba('+$bgColor+', '+$opacity+')' },$time);
}
function beforeSubmit(){
	if(confirm("Вы точно хотите удалить предмет \""+$globalSubjectName+"\". Все данные включая все темы и подтемы будут удалены!")){
		if(confirm("Подтвердите действие!")){
			return true;
		}
		else return false;
	}
	return false;
}
// -------------------------------------start-edit-modal--------------------------------
$(document).on('change','.edit-modal',function(){
	$data_name = $(this).attr('data-name');
	if($data_name=='subject'){
		$val = $(this).find('option:selected').val();
		$text = $(this).find('option:selected').text();
		$(this).parents('.modal-header').find('.delete-btn').val('Удалить "'+$text+'"');
		$(this).parents('.modal-header').find('.delete-btn').prev().val($val);
		$globalSubjectName = $text;
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=hidden]').val($val);
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=text]').val($text);
		// $(this).parents('.modal-header').find('.delete_subject').attr('onsubmit','return confirm("Вы точно хотите удалить предмет \"'+$text+'\"")');
		// $('.topic-list').text("Loading...");
		$('.topic-list').html("<center><img src='../img/loading.gif' style='width:100%;'></center>");
		$('.topic-list').load("edit_modal.php?part=header-part&data_num="+$val);
		$('.box-data .modal-body').text('Loading...');
		$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
	}
	else if($data_name='topic_list'){
		$val = $(this).find('option:selected').val();
		$data = $(this).find('option:selected').attr('data');
		if($data=='all'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
		}
		else if($data=='single'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=subtopic&data_num="+$val);
		}
	}
});
$(document).on('keyup','.edit-input',function(){
	$(this).css({'box-shadow':'0px 0px 10px #f0ad4e',"border-color":'#f0ad4e'});
	$(this).attr('data-original-title',$(this).val());
	$(this).parents('.form-group').find('.cancel-edit').show();
});
$(document).on('click','.cancel-edit',function(){
	$val = $(this).attr('data');
	$child = $(this).parents('.form-group').find('.edit-input');
	$child.val($val);
	$child.attr('data-original-title',$val);
	$child.css({'box-shadow':'none',"border-color":'#ccc'});
	$(this).hide();
});
$(document).on('click','.remove-data',function(){
	lightAlert($(this).parents('.form-group'),"#d9534f", 0.3, 800);
	$(this).parents('.form-group').find('.edit-input').attr('name','null');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','deleted[]');
	$(this).parent().prev().hide();
	$(this).next().show();
	$(this).hide();
});
$(document).on('click','.restore',function(){
	lightAlert($(this).parents('.form-group'),"#5cb85c",0, 1000);
	$(this).parents('.form-group').find('.edit-input').attr('name','data_name[]');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','data_num[]');
	$(this).parent().prev().show();
	$(this).prev().show();
	$(this).hide();
});
$(document).on('click','.move',function(){
	$direction = $(this).attr('direction');
	$classUp = 'glyphicon-chevron-up';
	$classDown = 'glyphicon-chevron-down';
	$classStop = 'glyphicon-record';
	$parent = $(this).parents('.form-group');
	if($direction!='none'){
		$parent.slideUp(200,function(){
			if($direction=='up'){
				$down = $parent.find('.move-down').attr('direction');
				$up = $parent.prev().find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','down');
					$parent.find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
					$parent.prev().find('.move-down').attr('direction','none');
					$parent.prev().find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','none');
					$parent.find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
					$parent.prev().find('.move-up').attr('direction','up');
					$parent.prev().find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
				}
				$parent.prev().before($parent);
			}
			if($direction=='down'){
				$down = $parent.next().find('.move-down').attr('direction');
				$up = $parent.find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','none');
					$parent.find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
					$parent.next().find('.move-down').attr('direction','down');
					$parent.next().find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','up');
					$parent.find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
					$parent.next().find('.move-up').attr('direction','none');
					$parent.next().find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
				}
				$parent.next().after($parent);
			}
			$parent.slideDown(200,function(){
				lightAlert($parent,'#f0ad4e',0,1000);
			});
			
		});
	}
});
// -------------------------------------end-edit-modal----------------------------------
// ----------------------------------
$(document).ready(function(){
	$("#lll").css('display','none');
	$('[data-toggle="tooltip"]').tooltip();
});
$(function(){
	$('#lll').hide().ajaxStart( function() {
		$(this).css('display','block');  // show Loading Div
	} ).ajaxStop ( function(){
		$(this).css('display','none'); // hide loading div
	});
});
// ----------------------
$(document).on('click','.edit_user, .cancel_edit',function(){
	$(this).parents('.head').find('.user_info').toggle();
});
$(document).on('click','.more_info',function(){
	// $data_toggle = $(this).attr('data_toggle');
	$data_num = $(this).attr('data_num');
	$data_name = $(this).attr('data-name');
	if($data_name == 'student'){
		// if($data_toggle=='false'){
			$(this).parents('.head').next().html("<b>Loading...</b>");
			$(this).parents('.head').next().load("students_in_group.php?data_num="+$data_num);
			// $(this).attr('data_toggle','true');
		// }
	}
	$(this).parents('.head').next().toggle();
});

$(document).on('click','.to_archive',function(){

	$object_full_name = $(this).parents('tr').find('.object-full-name').text();
	$data_num = $(this).data('num');
	$data_name = $(this).data('name');
	if($data_name == 'student' || $data_name == 'group' || $data_name == 'teacher'){
		checkGroupActivation($data_num, $object_full_name, $data_name, $(this));
	}
	else {
		archive($data_num, $object_full_name, $data_name, $(this));
	}
});

function checkGroupActivation(data_num, object_full_name, data_name, this_obj){
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('checkGroupActivation'))?>&data_num="+data_num+"&data_name="+data_name,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success && data.count==0){
	    		archive(data_num, object_full_name, data_name, this_obj);
	    	}
	    	else{
	    		if(data_name == 'student'){
	    			alert("Оқушының жаңадан бастайтын курстары бар. Архивке салу мүмкін емес!");
	    		}
	    		else if(data_name == 'group'){
	    			alert("Группада жақында курсты бастайтын оқушылар бар. Архивке салу мүмкін емес!");
	    		}
	    		else if(data_name == 'teacher'){
	    			alert("Мұғалімді архивке жіберу мүмкын емес. Активный группалары бар!");
	    		}
	    	}
	    },
	  	error: function(dataS) 
    	{
    		alert("Қате. Программистпен жолығыңыз. "+dataS);
    		console.log(dataS);
    	} 	        
   	});
}

function archive(data_num, object_full_name, data_name, this_obj){

	$object_full_name = object_full_name;
	$data_num = data_num;
	$data_name = data_name;
	$this = this_obj;

	if(confirm("Вы точно хотите архивировать? ("+$object_full_name.trim()+")")){
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('toArchive'))?>&data_num="+$data_num+"&data_name="+$data_name,
	    	contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$elem = $this.parents("tr");
		    		$elem.find('.count').text("-");
		    		$elem.nextAll(".head").each(function(){
		    			$(this).find('.count').text(parseInt($(this).find('.count').text().trim())-1);
		    		});
		    		$this.parents("tr").stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$elem.next().remove()
		    			$elem.remove();
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}

}

$(document).on('click','.close_body',function(){
	$(this).parents('.body').hide();
});
$(document).on('click','.info-list, .new-teacher, .new-group',function(){
	$at = $(this).attr('at');
	$("#"+$at).slideToggle('fast');
});
$(document).on('click','.close-add-new-student',function(){
	$(this).parents('#new-student').hide();
});
$(document).on('click','.close-add-new-teacher',function(){
	$(this).parents('#new-teacher').hide();
});
$(document).on('click','.close-add-new-group',function(){
	$(this).parents('#new-group').hide();
});
$(document).on('click','.news',function(){
	$data_type = $(this).attr('data-type');
	if($data_type=='student'){
		$('.box-news .modal-header .modal-title').text('Студенттерге арналған жаңалықтар');
	}
	else if($data_type=='teacher'){
		$('.box-news .modal-header .modal-title').text('Мұғалімдерге арналған жаңалықтар');
	}
	$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>");
	$('.box-news .modal-body').load('index_news.php?data_type='+$data_type);
});

$(document).on('click','.single-student-news',function(){
	$data_num = $(this).data('num');
	$name = $(this).data('name');
	$('.box-news .modal-header .modal-title').html("Хабарлама.<br><b>"+$name+'.</b>');
	$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>");
	$('.box-news .modal-body').load('index_single_news.php?data_num='+$data_num);
});

$(document).on('click','.row-groups-info .btn',function(){
	if($(this).attr('btn-info')=='edit'){
		$(this).parents('.row-groups-info').find('.group-info').toggle();
		$(this).parents('.row-groups-info').find(".group-form").toggle();
	}
	if($(this).attr('btn-info')=='schedule'){
		$data_name = $(this).parents('.row-groups-info').find('form').find('input[name=group_name]').val();
		$data_num = $(this).parents('.row-groups-info').find('form').find('input[name=data_num]').val();
		$(".box-group-schedule .modal-header #group_name").text($data_name);
		$(".box-group-schedule .modal-body").text("Loading");
		$(".box-group-schedule .modal-body").load("load_group_schedule.php?data_num="+$data_num);
	}
});
// $(document).on('click','.row-parents-info .btn',function(){
// 	if($(this).attr('type')!='submit'){
// 		$(this).parents('.row-parents-info').find('.parent-info').toggle();
// 		$(this).parents('.row-parents-info').find(".parent-form").toggle();
// 	}
// });

// ----------------------------------------------------
$('.navigation').on('click',function(){
	if(!$(this).hasClass('active')){
		$('.navigation').removeClass('active');
		$(this).addClass('active');
		$attr = $(this).attr('data');
		$('.box').css('display',"none");
		$('.'+$attr).css('display','block');
		if($attr=='student'){
			$('.'+$attr).append("<p id='tmp'></p>");
			$('#tmp').load('admin_student.php');
			// $('.'+$attr).load('admin_student.php');
		} else {
			loadPageAdmin_($attr);
		}
	}
});
function loadPageAdmin_(attr){
	$('.'+attr).html("<center><h1>Loading...</h1></center>");
	$('.'+attr).load('admin_'+attr+'.php');
}
$(document).on('click','.sub_navigation',function(){
	$parent = $(this).parents('.row').attr('data');
	if(!$(this).hasClass('active')){
		$('.sub_navigation').removeClass('active');
		$(this).addClass('active');
		$attr = $(this).attr('data');
		$('.'+$parent+'_box').hide();
		$('.'+$parent).find('.'+$attr).show();
		loadPageProgressResult_($parent, $attr);
	}
});
function loadPageProgressResult_(parent, attr){
	$("."+parent).find("."+attr).html("<center><h1>Loading...</h1></center>");
	$("."+parent).find("."+attr).load(parent+"_"+attr+".php");
}
function hide(objHide){
	$(function(){
		$(objHide).css('display','none');
	});
}
$(document).on('click','.reset_password',function(){
	$val = $(this).next().val();
	$this = $(this);
	$data_name = $(this).attr('data-name');
	$a = '';
	if($data_name=='student'){
		$a = '12345';
		$goTo = "<?php echo md5(md5('resetThisStudent'))?>";
	}
	else if ($data_name == 'teacher'){
		$a = '123456';
		$goTo = "<?php echo md5(md5('resetThisTeacher'))?>";
	}

	var formData = {
		'action':"reset",
		'reset' : $val
	};
	// if($data_name=='teacher'){
		if(confirm("Пароль поменяется на '"+$a+"'. Подтвердите действие?")){
			$.ajax({
				type 		: 'POST',
				url 		: 'reset.php?'+$goTo, 
				data 		: formData, 
				cache		: false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
					data = $.parseJSON(dataS);
					if(data.success){
						// $this.parents('.user_info').addClass('pull-right');
						$this.parents('.user_info').find('table tr td').last().html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
			    		// $this.parents('.user_info').html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
			    	}
			    	else{
			    		console.log(data);
			    	}
				}
			});
		}
	// }
});
$(document).ready(function(){
	$(document).on('submit','#create_student',(function(e) {
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createStudent'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.students').load('index_students.php');
		    		document.getElementById("create_student").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-teacher',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createTeacher'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.teachers').load('index_teachers.php');
		    		document.getElementById("create-teacher").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-group',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createGroup'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.groups').load('index_groups.php');
		    		document.getElementById("create-group").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#schedule-form',(function(e) {
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('schedule'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		// $(".box-group-schedule").css('display','none').removeClass('in');
		    		// $(".modal-backdrop").remove();
		    		// $('body').removeAttr("class");
		    		// $('body').removeAttr('style');
					$(".box-group-schedule").modal('hide');
		    		$("#alert").html('<div class="alert alert-success alert-dismissible" role="alert" style="position: fixed; z-index: 10000; top:5%; width: 80%; left:10%; box-shadow: 0px 0px 10px green;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><center><strong>'+$(".box-group-schedule #group_name").text()+'</strong> группасының сабақ кестесі өзгерді</center></div>');

		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});

$(document).on('submit','#form-single-student-news',(function(e) {
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('singelStudentNews'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$data_num = $this.find('input[name=data_num]').val();
	    		$("#delete_single_student_news").show();
	    		$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>").load('index_single_news.php?data_num='+$data_num);
				$('.box-news .modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
				if(data.context=='notEmpty'){
					$('.single-student-news').each(function(){
						if($(this).data('num')==$data_num){
							$(this).find('span').css('color',"orange");
						}
						else{
							console.log("fail");
						}
					});
				}
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
}));

$(document).on("click",'#delete-single-student-news',function(){
	$data_num = $(this).parent().find('input[name=data_num]').val();
	if(confirm("Вы точно хотите удалить?")){
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('removeSingelStudentNews'))?>&data_num="+$data_num,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$("#delete_single_student_news").show();
		    		$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>").load('index_single_news.php?data_num='+$data_num);
					$('.box-news .modal-body').stop().css({'background-color':"#EC9923"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
					$('.single-student-news').each(function(){
						if($(this).data('num')==$data_num){
							$(this).find('span').css('color',"black");
						}
						else{
							console.log("fail");
						}
					});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}
});

$(document).on('keyup','#search',function(){
	$data_name = $(this).attr('data-name');
	$val = $(this).val();
	$val = $val.replace(" ","_");
	if($data_name=='student'){
		$('.students').load('index_students.php?search='+$val);
	}
	else if($data_name=='teacher'){
		$('.teachers').load('index_teachers.php?search='+$val);
	}
	else if($data_name=='group'){
		$('.groups').load('index_groups.php?search='+$val);
	}
	else if($data_name=='parent'){
		$('.parents').load('index_parents.php?search='+$val);
	}
});
$(document).on("click",'.data-list',function(){
	$(this).parent().next().slideToggle('fast');
});
// --------------------------------------------modal-group-schedule-start-----------------------------------
$(document).on('click','.schedules .btn-week',function(){
	$week_id = $(this).attr('week-id');
	if($(this).hasClass('active')){
		$(this).removeClass('active');
		$('.schedules .hidden-datas').find('input[value='+$week_id+']').remove();
	}
	else{
		$(this).addClass('active');
		$('.schedules .hidden-datas').append('<input type="hidden" name="week_id[]" value="'+$week_id+'">');
	}
});
// --------------------------------------------modal-group-schedule-end-------------------------------------
// --------------------------------------------START-SUGGESTION-MODAL---------------------------------------
<?php if(isset($_SESSION['role']) & $_SESSION['role']==md5('admin')){ ?>
$(document).on('click','.btn-suggestion',function(){
	$('.box-suggestion .modal-header .modal-title').html("<center><b>Ұсыныстар</b></center>");
	$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
	$('.box-suggestion .modal-body').load('load_suggestion.php');
});
$(document).on('change','.suggestion-checkbox',function(){
	if($(this).parents('tr').hasClass('select-box')){
		$(this).parents('tr').removeClass('select-box');
	}
	else {
		$(this).parents('tr').addClass('select-box');
	}
});
$(document).on('submit','#suggestion-wating-form',function(e){
	// #5CB85C --- green
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('acceptSuggestions'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$('.select-box').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},200,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','.btn-suggestion-waiting-reject',function(){
	$arr = [];
	$("#suggestion-wating-form .select-box").each(function(){
		$arr.push($(this).find('input[name="sid[]"]').val());
	});
	var formData = {
		'sid':$arr,
	};
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('rejectSuggestions'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$('#suggestion-wating-form .select-box').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('submit','#suggestion-accepted-form',function(e){
	// #5CB85C --- green
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('implementSuggestions'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$('#suggestion-accepted-form .select-box').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},200,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','.btn-suggestion-accepted-reject',function(){
	$arr = [];
	$("#suggestion-accepted-form .select-box").each(function(){
		$arr.push($(this).find('input[name="sid[]"]').val());
	});
	var formData = {
		'sid':$arr,
	};
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('rejectSuggestions'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$('#suggestion-accepted-form .select-box').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','#implementedSuggestion',function(){
	$('#implementedSuggestionBox').fadeToggle();
});
<?php } ?>
// --------------------------------------------END-SUGGESTION-MODAL-----------------------------------------

function newsValidation(){
	var ext = $('#news_img').val();
	if(ext!=''){
		$img_size = $('#news_img')[0].files[0].size;
		ext = ext.split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg','GIF','PNG','JPG','JPEG']) == -1) {
	    	alert('Не правильный формат картинки. Доступные форматы : ".jpg , .png , .jpeg , .gif, .JPG , .PNG , .JPEG , .GIF"');
	    	return false;
		}
		else if($img_size>=1572864){
			alert('Ошибка! Максимальный размер изображении 1.5MБ ~ (1572864 байт). Размер загруженного изображения = '+$img_size+' байт.');
			return false;
		}
		else if(confirm("Подтвердите действие!")){
			return true;
		}
		else {
			return false;
		}
	}
	else if(confirm("Подтвердите действие!")){
		return true;
	}
	else{
		return true;
	}
}
$(document).on('click','#remove_img',function(){
	if(confirm("Вы точно хотите удалить изображение?")){
		$(this).parents('.form-group').find('input[name=uploaded_photo]').val('');
		$(this).parents("b").html('<p class="text-danger">Изображение удалено!</p>');
	}
});
$(document).on('click','.open-access',function(){
	$data_num = $(this).data('num');
	$data_block = $(this).data('block');
	$this = $(this);
	var formData = {
		'block':$data_block,
		'sn':$data_num
	};
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('openAccess'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	data = $.parseJSON(dataS);
	    	if(data.success){
	    		$this.parents('tr').css({'border':'2px solid red'});
	    		$this.parents('tr').find('.warned').html('<b style="color:#f00;">Ескертілген</b>');
	    		$this.remove();
	    	} 
	    	else{
	    		console.log(data);
	    		alert("Ошибка!");
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    		alert("Ошибка!");
    	} 	        
   	});
});

// -------------------------------------------------------------START-BOX-ABS-REASON--------------------------------------------------------------------------------
$(document).on('click','.abs-reason',function(){
	$('.box-abs-reason .modal-header .modal-title').html("<center><b>Сабаққа келмеген кездегі себептер.</b></center>");
	$('.box-abs-reason .modal-body').html("<h3>Loading...</h3>");
	$('.box-abs-reason .modal-body').load('load_reason.php');
});
// -------------------------------------------------------------END-BOX-ABS-REASON---------------------------------------------------------------------------------- 


// ------------------------------------------------------------------START-SUGGESTION-INPUT-------------------------------------------------------------------------
<?php if(isset($_SESSION['role']) && $_SESSION['role']==md5('moderator')){ ?>
	$(document).on('click','.suggestion',function(){
		$('.box-suggestion .modal-header .modal-title').html("<center><b>Ұсыныс</b></center>");
		$(".box-suggestion .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box-suggestion .modal-body").load("load_suggestion_input.php");
	});

	$(document).on('click','#suggestion, #suggestion-cancel',function(){
		$('#suggestion').toggle();
		$("#suggestion-form").toggle();
	});

	$(document).on('submit','#suggestion-form',function(e){
		$this = $(this);
		e.preventDefault();
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('add-new-suggestion'))?>",
	    	type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    		$(".box-suggestion").modal('hide');		
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click','.suggestion-edit, .suggestion-edit-cancel',function(){
		$(this).parents('tr').find('.suggestion-text').toggle();
		$(this).parents('tr').find('.suggestion-form-edit').toggle();
	});

	$(document).on('submit','.suggestion-form-edit',function(e){
		$this = $(this);
		e.preventDefault(e);
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('edit-suggestion'))?>",
	    	type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$(".box-suggestion").modal('hide');	
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
	$(document).on('click','.suggestion-delete',function(){
		$this = $(this);
		$sid = $this.parents('tr').find('input[name=sid]').val();
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('remove-suggestion'))?>&sid="+$sid,
			cache : false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$(this).remove();
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
<?php } ?>
// ------------------------------------------------------------------END-SUGGESTION-INPUT---------------------------------------------------------------------------
// ------------------------------------------------------------------START-STUDENT-FORM-----------------------------------------------------------------------------
	$(document).on('click','.student-modal',function(){
		$action = $(this).data('action');
		if($action=='new-student'){
			$('.box-student-form .modal-title').html('<center><h1>Жаңа оқушы енгізу</h1></center>');
			$('.box-student-form .modal-body').html("<center><h3>Loading...</h3></center>");
			$('.box-student-form .modal-body').load('student_form.php?action=new_student');
		}
		if($action=='view'){
			$data_num = $(this).data('num');
			$('.box-student-form .modal-title').html('<center><h1>Оқушы</h1></center>');
			$('.box-student-form .modal-body').html("<center><h3>Loading...</h3></center>");
			$('.box-student-form .modal-body').load('student_form.php?action=view&data_num='+$data_num);
		}
	});

	// $timer = '';
	$access = [0,0,0,0]; // [student-username, student-phone, parent-phone-1, parent-phone-2]

	function checkAccessibility(i, v, t){
		if(i == "student-username"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccd','box-shadow':'none'});
	    		t.parents('.form-group').find('.student-username').html("");
	    		studentSaveButton(0,0,true);
			}
			else if(v.indexOf(".") >= 0){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('studentUsername'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						$(".check-required").not(t).addClass('disabled');
						studentSaveButton("","",false);
					},
					success: function(dataS){
						$(".check-required").removeClass("disabled");
				    	data = $.parseJSON(dataS);
				    	if(data.success && data.count==1){
				    		t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				    		t.parents('.form-group').find('.student-username').html('"<b>username</b>" уже существует!');
				    		studentSaveButton(0, 2, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.student-username').html("");
				    		studentSaveButton(0,0,true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.student-username').html('Формат:"аты.тегі"');
				studentSaveButton(0, 1, true);
			}
		}
		else if(i == "student-phone"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.student-phone').html("");
	    		studentSaveButton(1, 0, true);
			}
			else if(v.length==10){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('studentPhone'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						$(".check-required").not(t).addClass('disabled');
						studentSaveButton('','',false);
					},
					success: function(dataS){
						$(".check-required").removeClass("disabled");
				    	data = $.parseJSON(dataS);
				    	if(data.success && data.count==1){
				    		t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				    		t.parents('.form-group').find('.student-phone').html('<b>Номер</b> уже существует!');
				    		studentSaveButton(1, 2, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.student-phone').html("");
				    		studentSaveButton(1, 0, true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.student-phone').html('Длина: 10');
				studentSaveButton(1, 1, true);
			}
		}
		else if(i == "parent-phone-1" || i == "parent-phone-2"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.'+i).html("");
	    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
			}
			else if(v.length==10){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('parentPhone'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						studentSaveButton('','',false);
					},
					success: function(dataS){
				    	data = $.parseJSON(dataS);
				    	if(data.success && data.count>=1){
				    		t.css({'border-color':'orange','box-shadow':'0px 0px 10px orange'});
				    		$html = "<p style='margin:0;'><i><b>Номер</b> уже существует!</i></p>";
				    		$parent_num = "";
				    		$.each(data.data, function(key, value){
				    			if(data.data[key]['parent_num']!=$parent_num){
				    				$parent_num = data.data[key]['parent_num'];
				    				$html += "<br><p style='margin:0;'><b>Ата-ана:</b> "+data.data[key]['parent_surname']+" "+data.data[key]['parent_name']+"</p>";
				    			}
				    			$html += "<p style='margin:0;'><b>- Студент:</b> "+data.data[key]['student_surname']+" "+data.data[key]['student_name']+"</p>";
				    		});
				    		t.parents('.form-group').find('.'+i).html($html);
				    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.'+i).html("");
				    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else if(v.length==0){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.'+i).html("");
	    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.'+i).html('Длина: 10');
				studentSaveButton(i=="parent-phone-1" ? 2 : 3, 1, true);
			}
		}
	}

	$(document).on("keyup", ".check-for-existing", function(){
		checkAccessibility($(this).attr('id'), $(this).val(), $(this), false);
	});
	function studentSaveButton(i, v, torf){
		$e = $('.student-form').find('.student-save-btn');
		if(torf){
			$e.val('Сақтау');
			$access[i] = v;
			if($access.every(checkSame)){
				switch(v){
					case 0:
						$e.removeClass('disabled');
						break;
					case 1:
						if(!$e.hasClass('disabled')) $e.addClass('disabled');
						$e.val("Сақтау");
						break;
					case 2:
						if(!$e.hasClass('disabled')) $e.addClass('disabled');
						$e.val("Сақтау");
						break;
				}
			}
		}
		else{
			$e.addClass("disabled").val("Loading...");
		}
	}
	function checkSame(v){
		return v==0;
	}

	$(document).on('submit','.student-form',function(e){
		$this = $(this);
		e.preventDefault(e);
		formData = new FormData(this);
		$def = [];
		$elem = $(this).find('.check-for-existing');
		$go = 0;
		$getPath = '';
		if($this.find('input[type=submit]').attr('name')=='new-student'){
			$getPath = '<?php echo md5(md5('addNewStudent'))?>';
		}
		else if($this.find('input[type=submit]').attr('name')=='edit-student'){
			$getPath = '<?php echo md5(md5('editStudent'))?>'
		}
		if($access.every(checkSame) && $getPath!=''){	
			$.ajax({
		    	url: "ajaxDb.php?"+$getPath,
		    	type: "POST",
				data:  new FormData(this),
				contentType: false,
	    	    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
			    	data = $.parseJSON(dataS);
			    	if(data.success){
			    		$('.students').load('index_students.php');
			    		$this.parent().stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    			$(".box-student-form").modal('hide');	
			    		});
			    	} 
			    	else{
			    		console.log(data);
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		}
	});
	$(document).on('click','.student-edit-btn',function(){
		$('.student-form').find('input').each(function(key, value){
			if(value.hasAttribute('disabled')){
				$(this).removeAttr('disabled');
			}
		});
		$(this).parents('.btns').find('input[type=submit]').show();
		$(this).parents('.btns').find('input[type=reset]').show();
		$(this).hide();
	});
	$(document).on('click','.student-form .btns input[type=reset]', function(){
		$('.student-form').find('input').each(function(key,value){
			$(this).attr('disabled','disabled');
		});
		$('.student-form').find('input[type=submit]').hide();
		$('.student-edit-btn').show();
		$(".check-for-existing").css({'border-color':'#ccc','box-shadow':'none'});
		$(this).hide();
	});
// ------------------------------------------------------------------END-STUDENT-FORM-------------------------------------------------------------------------------
	
	$(document).on('change','.search_type',function(){
		// window.location.replace(window.location.pathname+"?search_type="+$(this).val());
		$search_type = $(this).val();
		$('.students').html("<h3>Loading...</h3>");
		if($search_type=='default' || $search_type == ''){
			$('.students').load("index_students.php");
		}
		else {
			$('.students').load("index_students_selective_search.php?search_type="+$search_type);
		}
		$('.search_box').html("<select class='form-control pull-right' style='width:20%;'><option>Loading...</option></select>");
		$('.search_box').load("load_search_box.php?search_type="+$search_type);
	});

	$(document).on('change','.select_search',function(){
		$search_type=$(this).attr('search-type');
		$search_attr=$(this).val();
		$('.students').html("<h3>Loading...</h3>");
		$('.students').load("index_students_selective_search.php?search_type="+$search_type+"&search_attr="+$search_attr);
	});

	$(document).on('click','.progress_result_trial_test_action_btn',function(){
		$this = $(this).parents('tr').next();
		$(this).parents("tr").next().toggle();
		if($(this).attr('data-clicked')=='f' && $(this).attr('data-clicked')!='t'){
			$(this).attr('data-clicked','t')
			$data = $this.find('p').text();
			$data_name = $this.find('p').data('name');
			$this.find('.progress_result_more_info').html("<center><h3>Loading...</h3></center>");
			$this.find('.progress_result_more_info').load('progress_result_trial_test_list_ajax.php?data='+$data+"&data_name="+$data_name);
		}
	});
	$(document).on('click','.progress_result_quiz_action_btn', function(){
		$(this).parents('tr').next().toggle();
	});
	$(document).on("change", '.trial_test_serach_order', function(){
		$search_subject = $('#trial_test_select_search_subject').val();
		$search_school = $('#trial_test_select_search_school').val();
		$search_archive = $("#trial_test_search_archive").is(":checked") ? $("#trial_test_search_archive").val() : "";
		$search_order_type = $('#trial_test_search_order_type').val();
		$('.progress_result_trial_test_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_trial_test_container').load("progress_result_trial_test_list.php"
														+"?search_subject="+$search_subject
														+"&search_school="+$search_school
														+"&search_order_type="+$search_order_type
														+"&search_archive="+$search_archive);
	});

	$(document).on('change', '.quiz_serach_order', function(){
		$search_subject = $('#quiz_select_search_subject').val();
		$search_school = $('#quiz_select_search_school').val();
		$search_order_type = $('#quiz_search_order_type').val();
		$search_archive = $('#quiz_search_archive').is(":checked") ? $('#quiz_search_archive').val() : "";
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php"
												+"?search_subject="+$search_subject
												+"&search_school="+$search_school
												+"&search_order_type="+$search_order_type
												+"&search_archive="+$search_archive);
	});

	$(document).on('change','.quiz_search_order_type',function(){

		$search_attr_subject = $('.quiz_select_search_subject').val();
		$search_attr_school = $('.quiz_select_search_school').val();
		$search_order_type = $(this).val();
		
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.quiz_select_search_subject',function(){
		$search_attr_subject = $(this).val();
		$search_attr_school = $('.quiz_select_search_school').val();
		$search_order_type = $('.quiz_search_order_type').val();
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.quiz_select_search_school',function(){
		$search_attr_school = $(this).val();
		$search_attr_subject = $('.quiz_select_search_subject').val();
		$search_order_type = $('.quiz_search_order_type').val();
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_school="+$search_attr_school+"&search_attr_subject="+$search_attr_subject+"&search_order_type="+$search_order_type);
	});



	$(document).on('click','.delete-test',function(){
		$data_num = $(this).data('num');
		$this = $(this);
		if(confirm("Вы точно хотите удалить тест?")){
			$.ajax({
		    	url: "ajaxDb.php?delete_test&data_num="+$data_num,
		    	contentType: false,
			    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
			    	data = $.parseJSON(dataS);
			    	
			    	if(data.success){
			    		$elem = $this.parents(".test-list");
			    		$elem.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    			$elem.remove();
			    		});
			    	} 
			    	else{
			    		console.log(data);
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		}
	});

	$(document).on('click','.delete-pocket',function(){
		$data_num = $(this).data('num');
		$this = $(this);
		if(confirm("Вы точно хотите удалить покет?")){
			$.ajax({
		    	url: "ajaxDb.php?delete_pocket&data_num="+$data_num,
		    	contentType: false,
			    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
			    	data = $.parseJSON(dataS);
			    	
			    	if(data.success){
			    		$elem = $this.parents(".test-pocket-list");
			    		$elem.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    			$elem.remove();
			    		});
			    	} 
			    	else{
			    		console.log(data);
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		}
	});


	$(document).on('click','.delete-test-from-pocket',function(){
		$data_num = $(this).data('num');
		$pocket_num = $(this).data('pocket-num');
		$this = $(this);
		if(confirm("Вы точно хотите убрать тест с покета?")){
			$.ajax({
		    	url: "ajaxDb.php?delete_pocket_test&data_num="+$data_num,
		    	contentType: false,
			    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
			    	data = $.parseJSON(dataS);
			    	
			    	if(data.success){
			    		$elem = $this.parents(".test-pocket-test");
			    		$elem.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    			$elem.remove();
			    			$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
							$('.box-collect-data .modal-body').load('collect_pocket_ajax.php?data_num='+$pocket_num);
			    		});
			    	} 
			    	else{
			    		console.log(data);
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		}
	});

	$(document).on('click','.collect-test', function(){
		$data_num = $(this).data('num');
		$('.box-collect-data .modal-title').html('<center><h1>Тесттер жиынтығын құрастыру.</h1></center>');
		$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
		$('.box-collect-data .modal-body').load('collect_pocket_ajax.php?data_num='+$data_num);
	});

	$(document).on('click', '.set-coefficient', function(){
		$eep_id = $(this).data('num');
		$('.box-collect-data .modal-title').html('<center><h1>Коэффициент.</h1></center>');
		$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
		$('.box-collect-data .modal-body').load('coefficient_pocket_ajax.php?eep_id='+$eep_id);
	});

	$(document).on('submit','#test-list',(function(e) {
		$this = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('add_to_pocket'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$('.box-collect-data .modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
	    				$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
						$('.box-collect-data .modal-body').load('collect_pocket_ajax.php?data_num='+data.pocket_num);
    				});
    				refresh_page();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));


	$(document).on("click",'.entrance-examination-test-refresh',function(){
		$(".entrance_examination").find(".test").html("<center><h3>Loading...</h3></center>");
		$(".entrance_examination").find(".test").load("entrance_examination_test.php");
	});
	function refresh_page(){
		$(".entrance_examination").find(".test").html("<center><h3>Loading...</h3></center>");
		$(".entrance_examination").find(".test").load("entrance_examination_test.php");
	}


	// -----------------------------------------------------entrance_examitaion_student---START-----------------------------------------------------------
	$(document).on('click', '.create-ee-student', function(){
		$(".entrance-examination-student .modal-header .modal-title").text("Вступительный тест. Жаңа оқушы.");
		$(".entrance-examination-student .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".entrance-examination-student .modal-body").load("entrance_examination_student_form.php");
	});

	$(document).on('submit', "#new-student-ee", function(e){
		$this = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('creat_student_ee'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000,function(){
		    			$this.parents('.modal').modal('hide');
    				});
    				$("#entrance-examination-students").html("<center><h3>Loading...</h3></center>");
		    		$("#entrance-examination-students").load("entrance_examination_student_table.php");
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click','#delete-ees', function(){
		$this = $(this);
		$data_num = $this.data('num');
		if (confirm("Вы точно хотите удалить?")) {
			$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('delete_student_ee'))?>&data_num="+$data_num,
			type: "GET",
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
    				$("#entrance-examination-students").html("<center><h3>Loading...</h3></center>");
		    		$("#entrance-examination-students").load("entrance_examination_student_table.php");
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
		}
	});
	// -----------------------------------------------------entrance_examitaion_student---END-------------------------------------------------------------

	$(document).on('change', '#chocolate_select', function(){
		$month_year = $(this).val().split(".");
		$month = $month_year[0];
		$year = $month_year[1];
		$('#lll').css('display','block');
		$("#chocolate").html("<center><h1>Loading...</h1></center>");
		$("#chocolate").load("index_chocolate.php?select_chocolate&year="+$year+"&month="+$month, function(){
			$('#lll').css('display','none');
		});
	});

	$("#config-box").on('show.bs.modal', function(e){
		$('#lll').css('display','block');
		$modal = $(this);
		$modal.find('.modal-body').load("config/choose_subject.php");
		$('#lll').css('display','none');
	});

	$global_object_num = "";
	$(document).on('click', '.config', function(){
		$action_type = $(this).data('type')
;		$('#lll').css('display','block');
		if ($action_type=='start') {
			$global_object_num = "";
			// $("#config-box .modal-body").load("config/choose_subject.php?start=true");
			config_start("subject_config");
		}
		else if ($action_type=='subject') {
			$subject_num = $(this).data('num');
			$global_object_num = $subject_num;
			$("#config-box .modal-body").load("config/choose_config.php?subject_num="+$subject_num);	
		} else if ($action_type=='config' && $global_object_num != ''){
			$config_type = $(this).data('val');
			if ($config_type=='config-quiz') {
				$("#config-box .modal-body").load('config/config_quiz.php?subject_num='+$global_object_num);
			}
		}
		$('#lll').css('display','none');
	});

	function config_start($config_type) {
		if ($config_type == 'subject_config'){
			$("#config-box .modal-body").load("config/choose_subject.php");
		}
	}

	$(document).on('submit', '.config_form', function ($e) {
		$e.preventDefault();
		$parent = $(this);
		$config_type = $parent.data('type');
		$.ajax({
        	url: "ajaxDb.php?"+$config_type,
			type: "POST",
			data: new FormData(this), 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$parent.parents('.modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('submit', '#test-config', function($e){
		$e.preventDefault();
		$parent = $(this);
		$pocket_id = $(this).find("input[name='eep_id']").val();
		$.ajax({
        	url: "ajaxDb.php?update_entrance_examination_test_config",
			type: "POST",
			data: new FormData(this), 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
					$('.box-collect-data .modal-body').load('collect_pocket_ajax.php?data_num='+$pocket_id);
		    		$parent.parents('.modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
		    	}
		    	else{
		    		$parent.parents('.modal-body').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});

	});

	$(document).on('change', '.test_pocket_coeff_radio', function(){
		$('.test_pocket_coeff_content').toggle();
	});

	$(document).on('submit', "#set-test-pocket-coefficient", function($e){
		$e.preventDefault();
		$parent = $(this);
		$eep_id = $(this).find('input[type=hidden]').val();
		$.ajax({
        	url: "ajaxDb.php?set_test_pocket_coefficient",
			type: "POST",
			data: new FormData(this), 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$parent.parents('.modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
					$('.box-collect-data .modal-title').html('<center><h1>Коэффициент.</h1></center>');
					$('.box-collect-data .modal-body').html("<center><h3>Loading...</h3></center>");
					$('.box-collect-data .modal-body').load('coefficient_pocket_ajax.php?eep_id='+$eep_id);
		    	}
		    	else{
		    		$parent.parents('.modal-body').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000);
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	function checkUrl() {
		var formData = new FormData();
		formData.append("username", "zhambyl");
		formData.append("password", "Password_123");
		$.ajax({
        	url: "http://127.0.0.1:8000/auth/token/",
			type: "POST",
			data: formData, 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	};

	// --------------------------------------------------------------------ENT_RESULT_PAGE_START-------------------------------------------------------
	function refresh_ent_result() {
		$("#ent_result").html("<center><h3>Loading...</h3></center>");
		$("#ent_result").load("index_ent_result.php");
	}

	$(document).on('click', '#refresh_ent_result', function() {
		refresh_ent_result();
	});

	$(document).on('submit', ".express_changing_school_class", function($e){
		$e.preventDefault();
		$parent = $(this);
		$.ajax({
        	url: "ajaxDb.php?change_student_school_class",
			type: "POST",
			data: new FormData(this), 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$parent.parents('tr')
		    			.stop()
		    			.css({'background-color':"#5CB85C"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    	}
		    	else{
		    		$parent.parents('tr')
		    			.stop()
		    			.css({'background-color':"#d9534f"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click', '.show-form', function(){
		$(this).parents('.student-info').next().show();
		$(this).parents('.student-info').hide();
	});

	$(document).on('click', '.cancel', function(){
		$(this).parents('.student-form').prev().show();
		$(this).parents('.student-form').hide();
	});

	$(document).on('submit', '.student-form', function($e){
		$e.preventDefault();
		$parent = $(this);
		$.ajax({
        	url: "ajaxDb.php?change_student_ent_info",
			type: "POST",
			data: new FormData(this), 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$parent.parents('tr')
		    			.stop()
		    			.css({'background-color':"#5CB85C"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    		refresh_ent_result();
		    	}
		    	else{
		    		$parent.parents('tr')
		    			.stop()
		    			.css({'background-color':"#d9534f"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
	// --------------------------------------------------------------------ENT_RESULT_PAGE_END---------------------------------------------------------


	// --------------------------------------------------------------------SMS_HISTORY_START-----------------------------------------------------------

	$(document).on('click', '#refresh_sms_history', function() {
		refresh_sms_history();
	});

	$(document).on('click', '#refresh-sms-balance', function() {
		refresh_sms_balance();
	})

	function refresh_sms_history() {
		$("#sms_history").html("<center><h3>Loading...</h3></center>");
		$("#sms_history").load("index_sms_history.php");
	}

	function refresh_sms_balance() {
		$("#sms_balance").html("<span>Loading...</span>");
		$.ajax({
			url: "load_sms_history.php?balance=get",
			type: "GET",success: function(dataS){
				data = $.parseJSON(dataS);
				$("#sms_balance").html(data.get_balance);
				$("#sms_balance").stop().css({"background-color" : "#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
			},
			error: function (dataS){
				console.log(dataS);
			}
		});
	}

	function refresh_sms_in_order() {
		$sms_in_order = $("#sms_in_order").html();
		$("#sms_in_order").html("<center><h3>Loading...</h3></center>");
		$.ajax({
			url: "load_sms_history.php?waiting_for_send=show",
			type: "GET",success: function(dataS){
				data = $.parseJSON(dataS);
				$("#sms_in_order").html($sms_in_order);
				$("#sms_in_order").find("#count").html(data.waiting_for_send.data);
				$("#sms_in_order").parent().stop().css({"background-color" : "#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
			},
			error: function (dataS){
				console.log(dataS);
			}
		});
	}

	$(document).on('click', '#load_sms_history', function(){
		$this = $(this);
		$count = $this.attr('data-count');
		$.ajax({
        	url: "load_sms_history.php?load="+$count,
			type: "GET",
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.load_sms_history.success){
		    		if (data.load_sms_history.last) {
		    			$this.hide();
		    		}
		    		$('.sms_history_table').append(data.load_sms_history.html);
		    		$this.attr("data-count", parseInt($count)+1);
		    		$('.sms_history_table tr')
		    			.stop()
		    			.css({'background-color':"#5CB85C"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    		check_sms_statuses();
		    	}
		    	else{
		    		$('.sms_history_table')
		    			.stop()
		    			.css({'background-color':"#d9534f"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click', '.send_manually', function() {
		$parent = $(this).parents("tr");
		$id = $(this).parent().find('input[name=id]').val();
		$action = $(this).attr('data-action');
		$recipient_phone = $(this).parent().find('input[name=recipient]').val();
		$sms_text = $(this).parents("tr").find('.sms_text').html();
		var formData = new FormData();
		formData.append("id", $id);
		formData.append('recipient', $recipient_phone);
		formData.append("text", $sms_text);
		$(this).parent().html("<center><h3>Loading...</h3></center>");
		$.ajax({
			url: "../send_sms/index.php?send_manually="+$action,
			type: "POST",
			data: formData, 
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
				data = $.parseJSON(dataS);
				$text_color = "text-success";
				if(data.success) {
					$html = "";
					if (data.status.is_finish_step && $action=='reject') {
						$text_color = "text-danger";
					} else if (!data.status.is_finish_step) {
						$html = "<button class='btn btn-xs btn-info pull-right check_status_manually' message_id='"+data.message_id+"'><span class='glyphicon glyphicon-refresh'></span></button>";
					}
					$parent.find('.status').html("<center><b class='"+$text_color+"'>"+data.status.description+"</b></center>"+$html);
					$parent.find('.sent_time').html(data.sent_time);
					refresh_sms_in_order();
					refresh_sms_balance();
					$parent
						.stop()
		    			.css({'background-color':"#5CB85C"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
				} else {
					$parent.find('.status').html("<center><b class='text-danger'>"+data.message+"</b></center>");
					$parent
		    			.stop()
		    			.css({'background-color':"#d9534f"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
				}
			},
			error: function (dataS){
				console.log(dataS);
			}
		});
	});

	$(document).on('click', ".check_status_manually", function() {
		$message_id = $(this).attr('message_id');
		$id = $(this).attr("id");
		$parent = $(this).parents('td');
		check_status_manually($parent, $message_id, $id);
	});

	function check_status_manually($parent, $message_id, $id){
		$parent.html("<center><h3>Loading...</h3></center>");
		var formData = new FormData();
		formData.append("message_id", $message_id);
		formData.append("id", $id);
		$.ajax({
			url: "../send_sms/index.php?check_status_manually="+$message_id,
			type: "POST",
			data: formData, 
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(dataS){
				data = $.parseJSON(dataS);
				$text_color = "text-warning";
				$html = "";
				if (data.status.is_finish_step && data.fail) {
					$text_color = "text-danger";
				} else if (!data.status.is_finish_step) {
					$html = "<button class='btn btn-xs btn-info pull-right check_status_manually' message_id='"+data.message_id+"'><span class='glyphicon glyphicon-refresh'></span></button>";
				} else if (data.status.is_finish_step && !data.fail) {
					$text_color = "text-success"
				}
				$parent.html("<center><b class='"+$text_color+"'>"+data.status.description+"</b></center>"+$html);
				if(data.success) {
					$parent
						.stop()
		    			.css({'background-color':"#5CB85C"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
				} else {
					$parent
		    			.stop()
		    			.css({'background-color':"#d9534f"})
		    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
				}
			},
			error: function (dataS){
				console.log(dataS);
			}
		});
	}

	function check_sms_statuses() {
		$('.loading-status').each(function (index, listItem) {
			$message_id = $(this).attr('message-id');
			$id = $(this).val();
			if ($(this).attr('load_status')=='load') {
				$(this).attr('load_status', 'loading');
				check_status_manually($(this).parents('td'), $message_id, $id);
			}
		});
	}
	// --------------------------------------------------------------------SMS_HiSTORY_END------------------------------------------------------------


	// --------------------------------------------------------------------STATISTICS_STUDENT_START---------------------------------------------------
	$(document).on('change', '#statistics-student-period-select', function(){
		$year = $(this).val();
		$('#lll').css('display','block');
		$("#statistics").html("<center><h1>Loading...</h1></center>");
		$("#statistics").load("index_statistics_student.php?select_statistics_student&year="+$year, function(){
			$('#lll').css('display','none');
		});
	});


	$(document).on('click', '.statistics_student_more', function(){
		$('.box-universal').modal('show');
		$html = "<table class='table table-bordered'><tr>";
		$html += "<tr><th style='color:red;'><center>Шыққан оқушылар</center></th><th style='color:green;'><center>Келген оқушылар</center></th></tr>";
		$html += "<td><ul>";
		$out = "";
		$(this).find('.statistics-student-out span').each(function(){
			$surname = $(this).find('input[name=surname]').val();
			$name = $(this).find('input[name=name]').val();
			$student_num = $(this).find('input[name=student_num]').val();
			$out += "<li>";
			$out += "<a class='object-full-name' href='student_info_marks.php?data_num="+$student_num+"' target='_blank'>"
			$out += $surname+" "+$name;
			$out += "</a>";
			$out += "&nbsp;&nbsp;";
			$out += "<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num='"+$student_num+"'>[инфо]</a>";
			$out += "</li>";
		});
		if ($out == "") $out = "<center>N/A</center>";
		$html += $out+"</ul></td>";
		$html += "<td><ul>";
		$in = "";
		$(this).find('.statistics-student-in span').each(function(){
			$surname = $(this).find('input[name=surname]').val();
			$name = $(this).find('input[name=name]').val();
			$student_num = $(this).find('input[name=student_num]').val();
			$in += "<li>";
			$in += "<a class='object-full-name' href='student_info_marks.php?data_num="+$student_num+"' target='_blank'>"
			$in += $surname+" "+$name;
			$in += "</a>";
			$in += "&nbsp;&nbsp;";
			$in += "<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num='"+$student_num+"'>[инфо]</a>";
			$in += "</li>";
		});
		if ($in == "") $in = "<center>N/A</center>";
		$html += $in+"</ul></td>";
		$html += "</tr></table>";
		$('.box-universal .modal-body').html($html);
	});
	// --------------------------------------------------------------------STATISTICS_STUDENT_END---------------------------------------------------

	$(document).on('click', '.copy-students', function(){
		// var el = $('#student-list');
		var el = document.getElementById('student-list');
		var body = document.body, range, sel;
		if (document.createRange && window.getSelection) {
			range = document.createRange();
			sel = window.getSelection();
			sel.removeAllRanges();
			try {
				range.selectNodeContents(el);
				sel.addRange(range);
			} catch (e) {
				range.selectNode(el);
				sel.addRange(range);
			}
			document.execCommand("copy");
		} else if (body.createTextRange) {
			range = body.createTextRange();
			range.moveToElementText(el);
			range.select();
			range.execCommand("Copy");
		}
		alert("Скопировано");
	});
	// -------------------------------------------------------CALCULATOR_START---------------------------------------------------------------------
	$(document).on('focus','.start-date',function(){
		$('.end-date').datepicker({
			format: 'dd.mm.yyyy',
			daysOfWeekDisabled: "0",
			daysOfWeekHighlighted: "0",
			todayHighlight: true,
			language: "ru",
			autoclose: true,
			maxViewMode: 0,
			todayBtn: "linked"
		});
		$(this).datepicker({
			format: 'dd.mm.yyyy',
			daysOfWeekDisabled: "0",
			daysOfWeekHighlighted: "0",
			todayHighlight: true,
			language: "ru",
			autoclose: true,
			maxViewMode: 0,
			todayBtn: "linked"
		}).on('changeDate', function(selected) {
			var minDate = new Date(selected.date.valueOf());
			$('.end-date').datepicker('setStartDate', minDate);
		});
		$('.end-date').attr('disabled', false);
	});
	$(document).on('focus','.end-date',function(){
		$(this).datepicker().on('changeDate', function(selected){
			var maxDate = new Date(selected.date.valueOf());
			$(".start-date").datepicker('setEndDate', maxDate);
		});
	});
	$(document).on('change', '#lesson-days', function(){
		if ($(this).val() == '1-2-3-4-5-6') {
			$('#intensive').prop('checked', false);
			$("#intensive").attr("disabled", true);
		} else {
			$("#intensive").attr("disabled", false);
		}
	});
	$(document).on('change', "#calculator-price #price", function(){
		if ($(this).val() == '-1') {
			$('#calculator-price #price-extra').show();
		} else {
			$('#calculator-price #price-extra').hide();
		}
	});
	$(document).on('submit', '#calculate-price', function(e){
		e.preventDefault();
		$price = parseFloat($("#calculator-price #price").val());
		$extra_price = parseFloat($("#calculator-price #price-extra input").val());
		if ($price == -1) {
			$price = $extra_price;
		}
		$discount = parseFloat($("#calculator-price #discount").val());
		$start_lesson = $("#calculator-price #start-date").val();
		$finish_lesson = $("#calculator-price #end-date").val();
		$lesson_days = $("#calculator-price #lesson-days").val().split('-');
		$cancelled_days = parseFloat($("#calculator-price #lesson-cancel").val());
		$fixed_lesson_days = 4 * $lesson_days.length;

		$start_date = convertToDate($start_lesson);
		$finish_date = convertToDate($finish_lesson);
		$days_in_period = 0;
		$pointer_date = $start_date;
		while ($pointer_date <= $finish_date) {
			if ($lesson_days.indexOf($pointer_date.getDay().toString()) !== -1) {
				$days_in_period++;
			}
			$pointer_date = increaseDays($pointer_date);
		}
		$max_days_in_period = (($days_in_period > $fixed_lesson_days) ? $fixed_lesson_days : $days_in_period);

		$taked_days = $max_days_in_period - $cancelled_days;
		$taked_days = $taked_days < 0 ? 0 : $taked_days;
		$taked_days = (($taked_days > $fixed_lesson_days) ? $fixed_lesson_days : $taked_days);
		$result = Math.ceil($price / $fixed_lesson_days * $taked_days * (1 - ($discount/100.00)));

		$result_calculator_html = "";
		$result_calculator_html += "<span title='Бір айдағы курс бағасы'>"+$price+"</span>";
		$result_calculator_html += " / <span title='бір айдағы сабақ саны'>"+$fixed_lesson_days+"</span>";
		$result_calculator_html += " * <span title='оқушының оқыған сабақ саны'>"+$taked_days+"</span>";
		$result_calculator_html += " * <span title='жеңілдік пайызы'>"+(1-($discount/100.00))+"</span>";
		$result_calculator_html += " = <span title='оқушының төлеу керек суммасы'>"+$result+" тг.</span>";
		$result_total_html = $result + " тг.";
		$result_price = $price + " теңге";
		$result_discount = $discount + '%';
		// $result_lessons_in_week = $lesson_days.length;
		$result_total_days = $days_in_period + (($days_in_period > $fixed_lesson_days) ? "("+$fixed_lesson_days+")" : "");
		$result_cancelled_days = $cancelled_days;
		$result_taked_days = $taked_days; 

		$('#calculator-price #price-result').html($result_price);
		$('#calculator-price #discount-result').html($result_discount);
		// $('#calculator-price #lessons-in-week-result').html($result_lessons_in_week);
		$('#calculator-price #total-days-result').html($result_total_days);
		$('#calculator-price #cancelled-days-result').html($result_cancelled_days);
		$('#calculator-price #taked-days-result').html($result_taked_days);
		$("#calculator-price #result-calculator").html($result_calculator_html);
		$("#calculator-price #result-total").html($result_total_html);
		$("#calculator-price #result").show();
	});
	function convertToDate($d){
		$data = $d.split('.');
		$data_str = $data[1]+" "+$data[0]+" "+$data[2];
		$dd = new Date($data_str);
		return $dd;
	}
	function increaseDays($d) {
		return new Date($d.setDate($d.getDate()+1));
	}
	// -------------------------------------------------------CALCULATOR_END---------------------------------------------------------------------

	// -------------------------------------------------------COURSE-PRICE_START-------------------------------------------------------------------
	$(document).on('change', '#calculate-course-price .subject', function(){
		courseSubjectChanged($(this), false);
	});

	$(document).on('change', '#calculate-course-price .intensive', function(){
		$intensive = $(this).prop("checked");
		$(this).parents('tr').find('input[type=radio]')[1].checked = true;
		if ($intensive) {
			$(this).parents('tr').find('.3-lessons').parents('td').find('span').text('6 рет');
		} else {
			$(this).parents('tr').find('.3-lessons').parents('td').find('span').text('3 рет');
		}
		courseSubjectChanged($(this).parents('td').find('.subject').prop('checked', true), $intensive);
	});

	function courseSubjectChanged($elem, $intensive) {
		$checked_subjects_count = getCoursesSelectedCount();
		$prices_arr = {};
		if (!$elem.prop('checked')) {
			$elem.parents('tr').find('input[type=radio]').prop('checked', false);
			clearCoursePrice($elem.parents('tr'));
		}
		
		if ($checked_subjects_count + 1 >= 5) {
			$('#calculate-course-price .subject').each(function(){
				if (!$(this).prop("checked")) {
					$(this).attr('disabled', true);
					$(this).parents('tr').find('input[class=intensive]').attr('disabled', true);
					$(this).parents('tr').find('input[class=intensive]').prop('checked', false);
					$(this).parents('tr').find('.3-lessons').parents('td').find('span').text('3 рет');
					$(this).parents('tr').find('input[type=radio]').attr('disabled', true);
				} else {
					$radio_one = $(this).parents('tr').find('.2-lessons');
					$radio_two = $(this).parents('tr').find('.3-lessons');
					$elem_intensive = $(this).parents('td').find('.intensive').prop('checked');
					if ($elem_intensive) {
						$radio_one.attr('disabled', true);
						$radio_two.prop('checked', true);
						setCoursePrices($radio_two, $elem_intensive);
					} else {
						$radio_one.prop('checked', true);
						$radio_one.attr('disabled', false);
						setCoursePrices($radio_one, $elem_intensive);
					}
				}
			});
		} else {
			$('#calculate-course-price .subject').each(function(){
				if (!$(this).prop("checked")) {
					$(this).attr('disabled', false);
					$(this).parents('tr').find('input[class=intensive]').attr('disabled', false);
					$(this).parents('tr').find('input[class=intensive]').prop('checked', false);
					$(this).parents('tr').find('.3-lessons').parents('td').find('span').text('3 рет');
					$(this).parents('tr').find('input[type=radio]').attr('disabled', false);
				} else {
					$radio_one = $(this).parents('tr').find('.2-lessons');
					$radio_two = $(this).parents('tr').find('.3-lessons');
					$elem_intensive = $(this).parents('td').find('.intensive').prop('checked');
					if ($elem_intensive) {
						$radio_one.attr('disabled', true);
						$radio_two.prop('ckecked', true);
						setCoursePrices($radio_two, $elem_intensive);
					} else {
						$radio_one.attr('disabled', false);
						if ($radio_one.prop('checked')) {
							setCoursePrices($radio_one, $elem_intensive);
						} else if ($radio_two.prop('checked')) {
							setCoursePrices($radio_two, $elem_intensive);
						} else {
							$radio_two.prop('checked', true);
							setCoursePrices($radio_two, $elem_intensive);
						}
					}
				}
			});
		}
	}

	function getCoursesSelectedCount() {
		$checked_subjects_count = 0;
		$('#calculate-course-price .subject').each(function(){
			if ($(this).prop("checked")) {
				$checked_subjects_count++;
			}
		});
		return $checked_subjects_count;
	}

	function hasAtLeastOneIntensiveCourse(){
		$intensive = false;
		$('#calculate-course-price .intensive').each(function(){
			if ($(this).prop('checked')) {
				$intensive = true;
			}
		});
		return $intensive;
	}

	$(document).on('change', '#calculate-course-price input[type=radio]', function(){
		$subject_input = $(this).parents('tr').find('.subject');
		
		if (!$subject_input.prop('checked')) {
			$subject_input.prop('checked', true);
			courseSubjectChanged($subject_input, false);
		} else {
			$radio_one = $(this).parents('tr').find('.2-lessons');
			$radio_two = $(this).parents('tr').find('.3-lessons');
			if ($radio_one.prop('checked')) {
				setCoursePrices($radio_one, false);
			} else {
				setCoursePrices($radio_two, false);
			}
		}
	});

	function setCoursePrices($radio_elem, $intensive) {
		$checked_subjects_count = $intensive ? 2 : getCoursesSelectedCount();
		$prices = $radio_elem.val().split('-');
		for ($i = 0; $i < $prices.length; $i++) {
			$price = $prices[$i].split(':');
			if (parseInt($price[0]) >= $checked_subjects_count) {
				$price_subtotal = $intensive ? parseInt($price[1]) * 2 : $price[1];
				$radio_elem.parents('tr').find('.course-price').html($price_subtotal);
				setCoursePriceResult();
				break;
			}
		}
	}

	function clearCoursePrice($elem) {
		$elem.find('.course-price').html('-');
		setCoursePriceResult();
	}

	function setCoursePriceResult() {
		$total = 0;
		$extra = 0;
		$("#calculate-course-price .course-price").each(function(){
			if ($(this).text() != '-') {
				$price = $(this).text().split('+');
				if ($price.length == 2) {
					$extra += parseInt($price[1]);
				}
				$total += parseInt($price[0]);
			}
		});
		$selected_course_count = getCoursesSelectedCount();
		$total_result_html = "<center>";
		if ($extra != 0) {
			$total_result_html += "<b>"+$total+"</b> + <b>"+$extra+"</b> = <b>"+($total+$extra)+" тг.</b>";
		} else {
			$total_result_html += "<b>"+$total+" тг.</b>";
		}
		if ($selected_course_count == 4) {
			$total_result_html += "<br><b class='text-info'>Комплекс</b>";
		}
		$total_result_html += "</center>";
		$('#calculate-course-price #total-result').html($total_result_html);
	}
	// -------------------------------------------------------COURSE-PRICE_END-------------------------------------------------------------------
	// -------------------------------------------------------STUDENT-PARENT-START---------------------------------------------------------------
	$(document).on('submit', '.parent-checked', function(e){
		e.preventDefault();
		$parent = $(this).parents('tr');
		$this = $(this);
		if(confirm("Подтвердите действие!")) {
			$.ajax({
				url: "ajaxDb.php?confirm_parent=true",
				type: "POST",
				data: new FormData(this), 
				contentType: false,
	    	    cache: false,
				processData:false,
				success: function(dataS){
					data = $.parseJSON(dataS);
					if(data.success) {
						$this.parents('td').html("<center><p class='text-success'>Тексерілді</p></center>");
						$parent
							.stop()
			    			.css({'background-color':"#5CB85C"})
			    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
					} else {
						$parent
			    			.stop()
			    			.css({'background-color':"#d9534f"})
			    			.animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
					}
				},
				error: function (dataS){
					console.log(dataS);
				}
			});
		}
	});
	// -------------------------------------------------------STUDENT-PARENT-END-----------------------------------------------------------------


	//  -------------------------------------------------------ENT-START--------------------------------------------------------------------------
	$(document).on('click', '#start-parsing', function(){
		$this = $(this);
		$.ajax({
	    	url: "ajaxDb.php?start_parsing",
	    	contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.hide();
		    		$("#stop-parsing").show();
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
	$(document).on('click', '#stop-parsing', function(){
		$this = $(this);
		$.ajax({
	    	url: "ajaxDb.php?stop_parsing",
	    	contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success){
		    		$this.hide();
		    		$("#start-parsing").show();
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click', '.open-ent-result', function() {
		$content = $(this).parents('tr').find('.ent-result-content').html();
		$('.box-ent-result .modal-body').html($content);
	});
	// --------------------------------------------------------ENT-END----------------------------------------------------------------------------
	// --------------------------------------------------------STUDENT_CALL_NOTIFICATION_START----------------------------------------------------
	$(document).on('click', '.select-scn', function() {
		$data_name = $(this).parents('td').find('input[type=hidden]').attr('data-name');
		$(this).parents('td').find('input[type=hidden]').attr('name', $data_name);
		$(this).hide();
		$(this).parents('td').find('.cancel-scn').show();
		$(this).parents('td').addClass('bg-success');
	});
	$(document).on('click', '.cancel-scn', function() {
		$data_name = "";
		$(this).parents('td').find('input[type=hidden]').attr('name', $data_name);
		$(this).hide();
		$(this).parents('td').find('.select-scn').show();
		$(this).parents('td').removeClass('bg-success');
	});
	// --------------------------------------------------------STUDENT_CALL_NOTIFICATION_END------------------------------------------------------

	// -------------------------------------------------------STUDENT-POLL-START------------------------------------------------------------------
	$(document).on('click', '.poll-info', function(){
		$('.box-universal .modal-header .modal-title').text("Оқушылардың мұғалімдерді бағалау сұрақтары");
		$('.box-universal .modal-body').html("<center><h3>Загрузка...</h3></center>");
		$('.box-universal .modal-body').load('load_poll_info.php');
	});

	$(document).on('click', '#add-poll-info', function() {
		$this = $(this);
		$(this).parents('form')
			.find('table')
			.append("<tr style='display:none;'><td><input type='text' name='new-poll-info[]' class='form-control' value=''></td><td><button type='button' data-action='new' class='btn btn-danger btn-xs poll-info-delete'>Delete</button></td></tr>")
			.promise().done(function(){
				$(this).find('tr').last().fadeIn(200);
			});
	});

	$(document).on('click', '.poll-delete', function() {
		$action = $(this).attr('data-action');
		if ($action='new') {
			$(this).parents('tr').fadeOut(200, function(){
				$(this).remove();
			});
		}
	});

	$(document).on('submit', '#poll-info-form', function(e){
		e.preventDefault();
		$this = $(this);
		$.ajax({
	    	url: "ajaxDb.php?poll_info_form",
	    	data:  new FormData(this),
	    	contentType: false,
    	    cache: false,
			processData:false,
			type: 'POST',
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	data = $.parseJSON(dataS);
		    	if(data.success) {
		    		$this.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500, function(){
		    			$('.box-universal .modal-body').html("<center><h3>Загрузка...</h3></center>");
						$('.box-universal .modal-body').load('load_poll_info.php');
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS)
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('keyup', '#poll-info-form input[type=text]', function(){
		if ($(this).val()!=$(this).prop('defaultValue')) {
			$(this).css({'border':'1px solid #f0ad4e', 'box-shadow':'0px 0px 10px #f0ad4e'});
			$(this).parents('tr').find('.poll-info-restore').show();
			$elem = $(this).parents('tr').find('input[data-name="edit-poll-info[]"]')
			$elem_attr = $elem.attr('data-name');
			
			if ($elem_attr !== undefined) {
				$(this).parents('tr').find('input[type=hidden]').attr('name','');
				$elem.attr('name', $elem_attr);
			}

			$elem = $(this).parents('tr').find('input[data-name="edit-poll-info-text[]"]');
			$elemAttr = $elem.attr('data-name');
			if ($elemAttr !== undefined){
				$elem.attr('name', $elemAttr);
			}
		} else {
			$(this).css({'border':'1px solid #ccc', 'box-shadow':'none'});
			$(this).parents('tr').find('.poll-info-restore').hide();
			$(this).parents('tr').find('input[type=hidden]').attr('name','');
		}
	});
	$(document).on('click', '.poll-info-restore', function(){
		$(this).parents('tr').find('input[type=hidden]').attr('name','');
		$(this).parents('tr').find('input[data-name="edit-poll-info-text[]"]').attr('name','');
		$(this).parents('tr').find('input[type=text]').css({'border':'1px solid #ccc', 'box-shadow':'none'});
		$(this).parents('tr').find('.poll-info-restore').hide();
		$defaultVal = $(this).parents('tr').find('input[type=text]').prop('defaultValue');
		$(this).parents('tr').find('input[type=text]').val($defaultVal);
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	});
	$(document).on('click', '.poll-info-delete', function() {
		$(this).parents('tr').find('input[type=hidden]').attr('name','');
		$(this).parents('tr').find('input[data-name="edit-poll-info-text[]"]').attr('name','');
		$elem = $(this).parents('tr').find('input[data-name="delete-poll-info[]"]');
		$elem_attr = $elem.attr('data-name');
		if ($elem_attr !== undefined) {
			lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
			$(this).hide();
			$(this).parent().find('.poll-info-cancel').show();
			$(this).parents('tr').find('input[data-name="delete-poll-info[]"]').attr('name', $elem_attr);
		} else {
			$(this).parents('tr').remove();
		}
	});
	$(document).on('click', '.poll-info-cancel', function(){
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
		$(this).parents('tr').find('input[type=hidden]').attr('name','');
		$(this).parents('tr').find('input[data-name="edit-poll-info-text[]"]').attr('name','');
		$(this).hide();
		$(this).parent().find('.poll-info-delete').show();
		$(this).parents('tr').find('.poll-info-restore').hide();
		$defaultVal = $(this).parents('tr').find('input[type=text]').prop('defaultValue');
		$(this).parents('tr').find('input[type=text]').val($defaultVal);
	});
	// -------------------------------------------------------STUDENT-POLL-END--------------------------------------------------------------------
	// -------------------------------------------------------TEACHER-POLL-RESULT-START-----------------------------------------------------------
	$(document).on('click', '.teacher-poll-result', function(){
		$teacher_num = $(this).attr("data-num");
		$('.box-universal .modal-header .modal-title').text("Сауалнама қорытындысы");
		$('.box-universal .modal-body').html("<center><h3>Загрузка...</h3></center>");
		$('.box-universal .modal-body').load('teacher_poll_result.php?teacher_num='+$teacher_num);
	});
	$(document).on('change', '.teacher-poll-result-date', function(){
		$date = $(this).val();
		$teacher_num = $(this).attr('data-num');
		$('#teacher-poll-result').html('<center><h3>Загрузка...</h3></center>');
		$('#teacher-poll-result').load('teacher_poll_result_ajax.php?date='+$date+'&teacher_num='+$teacher_num);
	});
	// -------------------------------------------------------TEACHER-POLL-RESULT-END-------------------------------------------------------------
</script>
</body>
<?php $_SESSION['load_page'] = false; ?>
</html>
