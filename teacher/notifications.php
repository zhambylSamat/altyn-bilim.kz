<?php
	include_once("../connection.php");

	try {

		$teacher_num = $_SESSION['teacher_num'];
		$chocolate = array();
		$config_subject_quiz = array();

		$stmt = $conn->prepare("SELECT * FROM config_subject_quiz");
		$stmt->execute();
		foreach ($stmt->fetchAll() as $value) {
			$config_subject_quiz[$value['subject_num']]['practice'] = $value['practice'];
			$config_subject_quiz[$value['subject_num']]['theory'] = $value['theory'];
		}

		$stmt = $conn->prepare("SELECT spn.id, 
									spn.created_date,
									spn.status,
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
						        	AND spn.status IN ('A', 'D')
					                AND DATE_ADD(spn.created_date, INTERVAL 7 DAY) >= NOW()
					                AND spn.group_student_num = gs.group_student_num
					                AND gs.group_info_num = gi.group_info_num
					                AND qm.quiz_num = q.quiz_num
					                AND t.topic_num = q.topic_num
					                AND sj.subject_num = t.subject_num
					                AND s.student_num = gs.student_num
					                AND gi.teacher_num = :teacher_num
						        ORDER BY spn.id ASC");
		$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $prize_notification_result = $stmt->fetchAll();
	    $prize_count = $stmt->rowCount();

	    foreach ($prize_notification_result as $value) {
	    	if ($value['status'] == "A") {	
		    	if (!isset($chocolate[$value['student_num']]['count'])) {
		    		$chocolate[$value['student_num']]['count'] = 0;
		    		$chocolate[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
		    	}
		    	$chocolate[$value['student_num']]['count']++;
		    }
	    }

	    $stmt = $conn->prepare("SELECT n.id, 
	  								s.student_num,
	  								s.surname, 
									s.name, 
									n.status,
								    sj.subject_name,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date_of_test
								FROM notification n,
									trial_test_mark ttm,
								    trial_test tt,
								    subject sj,
								    student s
								WHERE n.status in ('A')
									AND DATE_ADD(n.created_date, INTERVAL 7 DAY) >= NOW()
									AND n.object_id = 4
									AND n.object_num = ttm.trial_test_mark_num
								    AND ttm.trial_test_num = tt.trial_test_num
								    AND tt.subject_num = sj.subject_num
								    AND tt.student_num = s.student_num
								    AND s.student_num in (SELECT gs2.student_num 
								    					FROM group_info gi2, group_student gs2 
								    					WHERE gi2.teacher_num = :teacher_num 
								    						AND gi2.subject_num = sj.subject_num 
								    						AND gi2.block != 6
								    						AND gs2.group_info_num = gi2.group_info_num)
								    AND s.block != 6");
	    $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_trial_test_top_notification = $stmt->fetchAll();
	  	$trial_test_top_notification_count = $stmt->rowCount();

	  	foreach ($result_trial_test_top_notification as $value) {
	  		if ($value['status'] == "A") {
		    	if (!isset($chocolate[$value['student_num']]['count'])) {
		    		$chocolate[$value['student_num']]['count'] = 0;
		    		$chocolate[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
		    	}
		    	$chocolate[$value['student_num']]['count']++;
		    }
	    }

	  	$stmt = $conn->prepare("SELECT n.id,
	  								n.object_parent_num,
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
  									AND n.status in ('A', 'DA')
  									AND 3 = (SELECT count(n1.object_parent_num)
		  											FROM notification n1
		  											WHERE n1.object_parent_num = n.object_parent_num
		  												AND n1.status in ('A', 'DA'))
  									AND NOW() <= DATE_ADD((SELECT n1.created_date
  												FROM notification n1
  												WHERE n1.object_parent_num = n.object_parent_num
  													AND n1.status in ('A', 'DA')
  												ORDER BY n1.created_date DESC
  												LIMIT 1), INTERVAL 7 DAY)
  									AND s.student_num in (SELECT gs2.student_num 
								    					FROM group_info gi2, 
								    						group_student gs2 
								    					WHERE gi2.teacher_num = :teacher_num 
								    						AND gi2.subject_num = sj.subject_num 
								    						AND gi2.block != 6
								    						AND gs2.group_info_num = gi2.group_info_num)
								    AND s.block != 6
  								ORDER BY n.object_parent_num, n.id");
	  	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_trial_test_increase_notification = $stmt->fetchAll();
	  	$trial_test_increase_notification_count = $stmt->rowCount()/3;

	  	for ($i=0; $i < count($result_trial_test_increase_notification); $i=$i+3) { 
	  		$value = $result_trial_test_increase_notification[$i];
	  		if ($value['status'] == "A" || $value['status'] == "DA") {
		    	if (!isset($chocolate[$value['student_num']]['count'])) {
		    		$chocolate[$value['student_num']]['count'] = 0;
		    		$chocolate[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
		    	}
		    	$chocolate[$value['student_num']]['count']++;
	    	}
	    }

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
	  							WHERE n.object_id = 6
  									AND n.status in ('A', 'D')
  									AND DATE_ADD(n.created_date, INTERVAL 7 DAY) >= NOW()
  									AND qm.quiz_mark_num = n.object_num
  									AND q.quiz_num = qm.quiz_num
  									AND t.topic_num = q.topic_num
  									AND sj.subject_num = t.subject_num
  									AND s.student_num = qm.student_num
  									AND s.block != 1
  									AND s.block != 6 
  									AND s.student_num in (SELECT gs2.student_num 
								    					FROM group_info gi2, group_student gs2 
								    					WHERE gi2.teacher_num = :teacher_num 
								    						AND gi2.subject_num = sj.subject_num
								    						AND gi2.block != 6 
								    						AND gs2.group_info_num = gi2.group_info_num)");
	  	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_quiz_max_mark_notification = $stmt->fetchAll();
	  	$quiz_max_mark_notification_count = $stmt->rowCount();

	  	foreach ($result_quiz_max_mark_notification as $value) {
	  		if ($value['status'] == "A") {
		    	if (!isset($chocolate[$value['student_num']]['count'])) {
		    		$chocolate[$value['student_num']]['count'] = 0;
		    		$chocolate[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
		    	}
		    	$chocolate[$value['student_num']]['count']++;
		    }
	    }
		

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
	  								AND n.status in ('A', 'D')
	  								AND DATE_ADD(n.created_date, INTERVAL 7 DAY) >= NOW()
	  								AND qm.quiz_mark_num = n.object_num 
	  								AND q.quiz_num = qm.quiz_num 
	  								AND t.topic_num = q.topic_num 
	  								AND sj.subject_num = t.subject_num 
	  								AND s.student_num = qm.student_num 
	  								AND s.block != 1
	  								AND s.block != 6 
	  								AND s.student_num in (SELECT gs2.student_num 
								    					FROM group_info gi2, group_student gs2 
								    					WHERE gi2.teacher_num = :teacher_num 
								    						AND gi2.subject_num = sj.subject_num
								    						AND gi2.block != 6 
								    						AND gs2.group_info_num = gi2.group_info_num)");
		$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_quiz_max_mark_2_notification = $stmt->fetchAll();
	  	$quiz_max_mark_2_notification_count = $stmt->rowCount();

	  	foreach ($result_quiz_max_mark_2_notification as $value) {
	  		if ($value['status'] == "A") {
		    	if (!isset($chocolate[$value['student_num']]['count'])) {
		    		$chocolate[$value['student_num']]['count'] = 0;
		    		$chocolate[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
		    	}
		    	$chocolate[$value['student_num']]['count']++;
	    	}
	    }


	    $stmt = $conn->prepare("SELECT quantity FROM chocolate_history WHERE id = 1");
	    $stmt->execute();
	    $chocolate_left = $stmt->fetch(PDO::FETCH_ASSOC)['quantity'];

	    if ($chocolate_left == 0){
	    	$chocolate = array();
	    }
	    $_SESSION['notification'] = $chocolate;

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
                                                          	AND gi.teacher_num = :teacher_num 
                                                          	AND gs.group_info_num = gi.group_info_num 
                                                          	AND gs.student_num = s.student_num 
                                                          	AND gs.block != 6 )");
	  	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_finish_course_notification = $stmt->fetchAll();
	  	$finish_course_notification_count = $stmt->rowCount();


	  	$stmt = $conn->prepare("SELECT an.attendance_notification_num, 
	  								s.student_num,
	    							s.surname,
									s.name, 
								    gi.group_name,
								    DATE_FORMAT(pg.created_date, '%d.%m.%Y') AS created_date
								FROM attendance_notification an,
									student s,
								    group_info gi,
								    group_student gs,
								    progress_group pg,
								    progress_student ps
								WHERE an.action = 3
									AND an.group_student_num = gs.group_student_num
								    AND gs.group_info_num = gi.group_info_num
								    AND gi.teacher_num = :teacher_num
								    AND ps.progress_student_num in (an.first_abs, an.second_abs, an.third_abs)
								    AND pg.progress_group_num = ps.progress_group_num
								    AND ps.student_num = s.student_num
								ORDER BY s.surname, s.name, gi.group_name, created_date ASC");
		$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_an = $stmt->fetchAll();
	  	$attendance_notification_count = $stmt->rowCount()/3;


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
								    AND st.student_num = qm.student_num
								    AND st.student_num in (SELECT gs2.student_num 
								    					FROM group_info gi2, group_student gs2 
								    					WHERE gi2.teacher_num = :teacher_num 
								    						AND gi2.subject_num = sj.subject_num
								    						AND gi2.block != 6 
								    						AND gs2.group_info_num = gi2.group_info_num)");
	  	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$result_qrn = $stmt->fetchAll();
	  	$quiz_retake_notification_count = $stmt->rowCount()/2;

	  	// $stmt = $conn->prepare("SELECT n.id,
				// 				    n.object_parent_num,
				// 				    n.object_num,
				// 				    s.student_num,
				// 				    s.surname,
				// 				    s.name,
				// 				    gi.group_name,
				// 				    DATE_FORMAT(pg.created_date, '%d.%m.%Y') as created_date
				// 				FROM notification n,
				// 				    student s,
				// 				    group_info gi,
				// 				    progress_group pg,
				// 				    progress_student ps
				// 				WHERE n.object_id = 8
				// 				    AND n.status in ('A', 'D') 
				// 				    AND ps.progress_student_num = n.object_num
				// 				    AND ps.progress_group_num = pg.progress_group_num
				// 				    AND gi.group_info_num = pg.group_info_num
				// 				    AND gi.teacher_num = :teacher_num
				// 				    AND s.student_num = ps.student_num
				// 				    AND NOW() <= DATE_ADD((SELECT n1.created_date
  		// 										FROM notification n1
  		// 										WHERE n1.object_parent_num = n.object_parent_num
  		// 											AND n1.status in ('A', 'D')
  		// 										ORDER BY n1.created_date DESC
  		// 										LIMIT 1), INTERVAL 7 DAY)
				// 				ORDER BY n.id");
	  	// $stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	// $stmt->execute();
	  	// $result_no_home_work_notification = $stmt->fetchAll();
	  	// $no_home_work_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT DISTINCT s.student_num,
	  								s.surname,
	  								s.name, 
	  								DATE_FORMAT(s.dob, '%d.%m.%Y') as dob
	  							FROM student s,
	  								group_info gi,
	  								group_student gs
	  							WHERE DATE_FORMAT(s.dob, '%d.%m') = DATE_FORMAT(NOW(), '%d.%m')
	  								AND s.block != 6
	  								AND gs.student_num = s.student_num
	  								AND gs.block != 6
	  								AND gs.group_info_num = gi.group_info_num
	  								AND gi.teacher_num = :teacher_num");
	  	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	  	$stmt->execute();
	  	$student_dob_count = $stmt->rowCount();
	  	$result_student_dob = $stmt->fetchAll();
	  	
	} catch (PDOException $e) {
		throw $e;
	}
?>
<div class="btn-group-vertical" role="group">
	<?php if ($prize_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-prize-notification' data-type='prize-notification' class="btn btn-success prize-notification">
			95 баллдан асқан оқушы(лар)
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
			Аралық бақылаудан 100% баллды 1 айда 2 рет жинаған оқушы(лар)
			<span class='badge'><?php echo $quiz_max_mark_2_notification_count; ?></span>
		</button>
		<?php } ?>
		<?php if ($finish_course_notification_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-finish-course-notification' class="btn btn-warning finish_course_notification">
			Жақында бітіретін оқушылар
			<span class='badge'><?php echo $finish_course_notification_count; ?></span>
		</button>
		<?php } ?>
		<?php if ($attendance_notification_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-attendance-notification' class="btn btn-danger attendance-notification">
			3 күн қатарынан келмегендер
			<span class='badge'><?php echo $attendance_notification_count; ?></span>
		</button>
		<?php } ?>
		<?php if ($quiz_retake_notification_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-quiz-retake-notification'  class="btn btn-danger quiz-retake-notification">
			Пересдачадан құлағандар
			<span class='badge'><?php echo $quiz_retake_notification_count; ?></span>
		</button>
		<?php } ?>
		<!-- <?php if ($no_home_work_notification_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-no-home-work-notification' class="btn btn-danger no-home-work-notification">
			Үй жұмысын орындымағандар
			<span class='badge'><?php echo $no_home_work_notification_count; ?></span>
		</button>
		<?php } ?> -->
		<?php if ($student_dob_count>0) {?>
		<button style='text-align: left !important;' data-toggle='modal' data-target='.box-student-dob-notification' class="btn btn-info student-dob-notification">
			Оқушы(лар)дың туылған күні
			<span class='badge'><?php echo $student_dob_count; ?></span>
		</button>
		<?php } ?>
</div>
<br><br>

<div class="modal fade box-quiz-max-mark-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Аралық бақылаудан 100% балл алған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_quiz_max_mark_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo '<a href="../parent/student_info.php?data_num='.$value['student_num'].'&user='.md5('tch').'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
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
    					</tr>
    					<?php } ?>
    				</table>
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
    		<center><h3>Аралық бақылаудан 100% баллды 1 айда 2 рет алған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + қосымша 5% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_quiz_max_mark_2_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo '<a href="../parent/student_info.php?data_num='.$value['student_num'].'&user='.md5('tch').'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
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
    					</tr>
    					<?php } ?>
    				</table>
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
    		<center><h3>95 баллдан асқан оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
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
		    							<span class='h4'><b><?php echo '<a href="../parent/student_info.php?data_num='.$value['student_num'].'&user='.md5('tch').'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
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
    					</tr>
    					<?php } ?>
    				</table>
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
    		<center><h3>Пробный тесттен жоғарғы балл жинаған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + 10% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_trial_test_top_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo '<a href="../parent/student_info.php?data_num='.$value['student_num'].'&user='.md5('tch').'" target="_blank">'.$value['surname']." ".$value['name'].'</a>';?></b></span>
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
    					</tr>
    					<?php } ?>
    				</table>
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
    		<center><h3>Пробный тесттен қатарынан 3 рет балын көтерген оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + 10% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					for ($i=0; $i < count($result_trial_test_increase_notification); $i=$i+3) {
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'>
		    								<b>
		    									<?php 
			    									echo '<a href="../parent/student_info.php?data_num='.$result_trial_test_increase_notification[$i]['student_num'].'&user='.md5('tch').'" target="_blank">'.$result_trial_test_increase_notification[$i]['surname'];
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
    					</tr>
    					<?php } ?>
    				</table>
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
    		<center><h3>3 күн қатарынан келмеген оқушылар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    						$count = 0;
    						for ($i=0; $i < count($result_an); $i=$i+3) {
    					?>
    					<tr>
    						<td><center><?php echo ++$count; ?></center></td>
    						<td>
    							<center>
    								<span>
		    							<span class='h4'>
		    								<b>
		    									<a href="../parent/student_info.php?data_num=<?php echo $result_an[$i]['student_num'].'&user='.md5('tch'); ?>" target="_blank">
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
		    							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
		    							<span class='h4' style='color:#999;'>Дата 3:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i+2]['created_date']; ?></b></span>
		    						</span>
    							</center>
    						</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<!-- <div class="modal fade box-no-home-work-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Үй жұмысын орындымағандар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
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
		    							<span class='h4'>
		    								<b>
		    									<a href="../parent/student_info.php?data_num=<?php echo $result_no_home_work_notification[$i]['student_num'].'&user='.md5('tch'); ?>" target="_blank">
		    										<?php echo $result_no_home_work_notification[$i]['surname']." ".$result_no_home_work_notification[$i]['name'];?>
		    									</a>
		    								</b>
		    							</span>
		    							&nbsp;&nbsp;
		    							<span class='h4'><b><?php echo $result_no_home_work_notification[$i]['group_name'];?></b></span>
		    						</span>
		    						<br>
		    						<span>
		    							<span class='h4' style='color:#999;'>Дата: </span> <span class='h4 text-danger'><b><?php echo $result_no_home_work_notification[$i]['created_date']; ?></b></span>
		    						</span>
    							</center>
    						</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div> -->
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
		    							<span class='h4'>
		    								<b>
		    									<a href="../parent/student_info.php?data_num=<?php echo $result_qrn[$i]['student_num'].'&user='.md5('tch'); ?>" target="_blank">
		    										<?php echo $result_qrn[$i]['surname']." ".$result_qrn[$i]['name'];?>
		    									</a>
		    								</b>
		    							</span>
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
		    										echo "<span class='h4 text-danger'><b>Теория: ".$result_qrn[$i]['mark_theory']."</b></span>";
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
		    										echo "<span class='h4 text-danger'><b>Теория: ".$result_qrn[$i+1]['mark_theory']."</b></span>";
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
    					</tr>
    					<?php } ?>
    				</table>
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
		    							<span class='h4'><b><a href="../parent/student_info.php?data_num=<?php echo $value['student_num'].'&user='.md5('tch'); ?>" target="_blank"><?php echo $value['surname']." ".$value['name'];?></a></b></span>
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