<?php
	include_once('../connection.php');

	$max_practice = 'max_practice';
	$max_theory = 'max_theory';
	$last_result_practice = 'last_result_practice';
	$last_result_theory = 'last_result_theory';
	
	$result = array();
	$search_attr_subject = isset($_GET['search_subject']) ? $_GET['search_subject'] : '';
	$search_attr_school = isset($_GET['search_school']) ? $_GET['search_school'] : '';
	$search_archive = isset($_GET['search_archive']) ? $_GET['search_archive'] : '';
	$search_order_type = isset($search_order_type) && $search_order_type != '' ? $search_order_type : (isset($_GET['search_order_type']) ? $_GET['search_order_type'] : $max_practice);

	$start_current_semester_year = (date('n')>8) ? date("Y") : date("Y", strtotime("-1 year"));
	$start_current_semester = $start_current_semester_year.'-08-01';
	try {
		$result_stmt = array();
		if($search_order_type == $max_practice){
			$stmt = $conn->prepare("SELECT sj.subject_name,
										sj.subject_num,
										t.topic_num,
										t.topic_name,
										s.student_num,
									    TRIM(s.surname) as surname, 
									    TRIM(s.name) as name, 
									    s.school,
									    s.class,
									    gs.block,
									    DATE_FORMAT(gs.block_date, '%Y-%m-%d') as block_date,
									    qm.quiz_mark_num,
									    qm.mark_theory, 
									    qm.mark_practice,
									    DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date,
									    (SELECT count(qm2.mark_practice)
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num) as order_type_count,
									    (SELECT MAX(qm2.mark_practice)
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num) as order_type
									FROM quiz_mark qm,
										student s,
									    quiz q,
									    subject sj,
									    topic t,
									    group_info gi,
									    group_student gs
									WHERE qm.student_num = s.student_num
										AND (s.block = :archive OR s.block != 6)
										AND IF(:school != '', s.school, '') = :school
									    AND gs.student_num = s.student_num
									    AND (gs.block = :archive OR gs.block != 6)
									    AND gi.group_info_num = gs.group_info_num
										AND qm.quiz_num = q.quiz_num
									    AND q.topic_num = t.topic_num
									    AND sj.subject_num = t.subject_num
									    AND IF(:subject_num != '', sj.subject_num, '' ) = :subject_num
									-- GROUP BY qm.quiz_mark_num
									ORDER BY sj.subject_name ASC,
										order_type DESC,
										order_type_count DESC,
										s.surname ASC, 
									    s.name ASC,
										t.topic_order ASC,
										qm.created_date ASC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->bindValue(':archive', $search_archive, PDO::PARAM_INT);
			$stmt->bindValue(':block_date', $start_current_semester, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		else if($search_order_type == $max_theory){
			$stmt = $conn->prepare("SELECT sj.subject_name,
										sj.subject_num,
										t.topic_num,
										t.topic_name,
										s.student_num,
									    TRIM(s.surname) as surname, 
									    TRIM(s.name) as name, 
									    s.school,
									    s.class,
									    gs.block,
									    DATE_FORMAT(gs.block_date, '%Y-%m-%d') as block_date,
									    qm.quiz_mark_num,
									    qm.mark_theory, 
									    qm.mark_practice,
									    DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date,
									    (SELECT count(qm2.mark_theory)
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num) as order_type_count,
									    (SELECT MAX(qm2.mark_theory)
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num) as order_type
									FROM quiz_mark qm,
										student s,
									    quiz q,
									    subject sj,
									    topic t,
									    group_info gi,
									    group_student gs
									WHERE qm.student_num = s.student_num
										AND (s.block = :archive OR s.block != 6)
										AND IF(:school != '', s.school, '') = :school
									    AND gs.student_num = s.student_num
									    AND (gs.block = :archive OR gs.block != 6)
									    AND gi.group_info_num = gs.group_info_num
										AND qm.quiz_num = q.quiz_num
									    AND q.topic_num = t.topic_num
									    AND sj.subject_num = t.subject_num
									    AND IF(:subject_num != '', sj.subject_num, '' ) = :subject_num
									-- GROUP BY qm.quiz_mark_num
									ORDER BY sj.subject_name ASC,
										order_type DESC,
										order_type_count DESC,
										s.surname ASC, 
									    s.name ASC,
										t.topic_order ASC,
										qm.created_date ASC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->bindValue(':archive', $search_archive, PDO::PARAM_INT);
			$stmt->bindValue(':block_date', $start_current_semester, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		else if($search_order_type == $last_result_practice){
			$stmt = $conn->prepare("SELECT sj.subject_name,
										sj.subject_num,
										t.topic_num,
										t.topic_name,
										s.student_num,
									    TRIM(s.surname) as surname, 
									    TRIM(s.name) as name, 
									    s.school,
									    s.class,
									    gs.block,
									    DATE_FORMAT(gs.block_date, '%Y-%m-%d') as block_date,
									    qm.quiz_mark_num,
									    qm.mark_theory, 
									    qm.mark_practice,
									    DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date,
									    (SELECT qm2.mark_practice
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num
									    ORDER BY qm2.created_date DESC
									    LIMIT 1) as order_type
									FROM quiz_mark qm,
										student s,
									    quiz q,
									    subject sj,
									    topic t,
									    group_info gi,
									    group_student gs
									WHERE qm.student_num = s.student_num
										AND (s.block = :archive OR s.block != 6)
										AND IF(:school != '', s.school, '') = :school
									    AND gs.student_num = s.student_num
									    AND (gs.block = :archive OR gs.block != 6)
									    AND gi.group_info_num = gs.group_info_num
										AND qm.quiz_num = q.quiz_num
									    AND q.topic_num = t.topic_num
									    AND sj.subject_num = t.subject_num
									    AND IF(:subject_num != '', sj.subject_num, '' ) = :subject_num
									-- GROUP BY qm.quiz_mark_num
									ORDER BY sj.subject_name ASC,
										order_type DESC,
										s.surname ASC, 
									    s.name ASC,
										t.topic_order ASC,
										qm.created_date ASC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->bindValue(':archive', $search_archive, PDO::PARAM_INT);
			$stmt->bindValue(':block_date', $start_current_semester, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		else if($search_order_type == $last_result_theory){
			$stmt = $conn->prepare("SELECT sj.subject_name,
										sj.subject_num,
										t.topic_num,
										t.topic_name,
										s.student_num,
									    TRIM(s.surname) as surname, 
									    TRIM(s.name) as name, 
									    s.school,
									    s.class,
									    gs.block,
									    DATE_FORMAT(gs.block_date, '%Y-%m-%d') as block_date,
									    qm.quiz_mark_num,
									    qm.mark_theory, 
									    qm.mark_practice,
									    DATE_FORMAT(qm.created_date, '%d.%m.%Y') as created_date,
									    (SELECT qm2.mark_theory
									    FROM quiz_mark qm2,
									     	quiz q2,
									     	topic t2
									    WHERE qm2.quiz_num = q2.quiz_num
									    	AND q2.topic_num = t2.topic_num
									   		AND t2.subject_num = sj.subject_num
									    	AND qm2.student_num = s.student_num
									    ORDER BY qm2.created_date DESC
									    LIMIT 1) as order_type
									FROM quiz_mark qm,
										student s,
									    quiz q,
									    subject sj,
									    topic t,
									    group_info gi,
									    group_student gs
									WHERE qm.student_num = s.student_num
										AND (s.block = :archive OR s.block != 6)
										AND IF(:school != '', s.school, '') = :school
									    AND gs.student_num = s.student_num
									    AND (gs.block = :archive OR gs.block != 6)
									    AND gi.group_info_num = gs.group_info_num
										AND qm.quiz_num = q.quiz_num
									    AND q.topic_num = t.topic_num
									    AND sj.subject_num = t.subject_num
									    AND IF(:subject_num != '', sj.subject_num, '' ) = :subject_num
									-- GROUP BY qm.quiz_mark_num
									ORDER BY sj.subject_name ASC,
										order_type DESC,
										s.surname ASC, 
									    s.name ASC,
										t.topic_order ASC,
										qm.created_date ASC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->bindValue(':archive', $search_archive, PDO::PARAM_INT);
			$stmt->bindValue(':block_date', $start_current_semester, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		foreach ($result_stmt as $val) {
			if ($val['block_date'] >= $start_current_semester || $val['block_date']=='0000-00-00' || $val['block_date']=='') {
				$result[$val['subject_num']]['subject_name'] = $val['subject_name'];
				$result[$val['subject_num']]['student'][$val['student_num']]['sname'] = $val['name'];
				$result[$val['subject_num']]['student'][$val['student_num']]['ssurname'] = $val['surname'];
				$archive = isset($result[$val['subject_num']]['student'][$val['student_num']]['archive']) 
								? $result[$val['subject_num']]['student'][$val['student_num']]['archive'] 
								: true;
				if ($archive && $val['block'] != 6) {
					$archive = false;
				}
				$result[$val['subject_num']]['student'][$val['student_num']]['archive'] = $archive;
				$result[$val['subject_num']]['student'][$val['student_num']]['school'] = $val['school'];
				$result[$val['subject_num']]['student'][$val['student_num']]['class'] = $val['class'];
				$result[$val['subject_num']]['student'][$val['student_num']]['order_type'] = $val['order_type'];
				$result[$val['subject_num']]['student'][$val['student_num']]['quiz'][$val['quiz_mark_num']]['topic_num'] = $val['topic_num'];
				$result[$val['subject_num']]['student'][$val['student_num']]['quiz'][$val['quiz_mark_num']]['topic_name'] = $val['topic_name'];
				$result[$val['subject_num']]['student'][$val['student_num']]['quiz'][$val['quiz_mark_num']]['mark_practice'] = $val['mark_practice'];
				$result[$val['subject_num']]['student'][$val['student_num']]['quiz'][$val['quiz_mark_num']]['mark_theory'] = $val['mark_theory'];
				$result[$val['subject_num']]['student'][$val['student_num']]['quiz'][$val['quiz_mark_num']]['created_date'] = $val['created_date'];
			}
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!-- <table class='table table-bordered table-striped'> -->

<?php
	$count = 0;
	$chart_count = 0;
	$student_count = 1;
	foreach ($result as $sj_key => $sj_val) {
?>
	<span>Пән: <b class='h4 text-success'><?php echo $sj_val['subject_name']; ?></b></span>
	<table class='table table-bordered'>
		<tr style='position: sticky; top:0px; z-index:10; background-color: #fff;'>
			<th><center>#</center></th>
			<th>Аты-жөні</th>
			<th>Mектебі</th>
			<th>Сыныбы</th>
			<th>
			<?php
				if($search_order_type == $max_practice){
					echo "Есеп. Жоғарғы балл";
				}
				else if($search_order_type == $max_theory){
					echo "Теория Жоғарғы балл";
				}
				else if($search_order_type == $last_result_practice){
					echo "Есеп. Соңғы алған балл";
				}
				else if($search_order_type == $last_result_theory){
					echo "Теория. Соңғы алған балл";
				}
			?>
			</th>
			<th></th>
		</tr>
	<?php 
		$student_count = 1;
		foreach ($sj_val['student'] as $s_key => $s_val) {
	?>
		<tr class='success'>
			<td><center><?php echo $student_count++; echo $s_val['archive'] ? "&nbsp;<span style='font-size:10px;'>A</span>" : ""; ?></center></td>
			<td>
				<a href="student_info_marks.php?data_num=<?php echo $s_key; ?>" target="_blank">
					<?php echo $s_val['ssurname']." ".$s_val['sname']; ?>
				</a>
			</td>
			<td><?php echo $s_val['school'];?></td>
			<td><?php echo $s_val['class']; ?></td>
			<td><?php echo $s_val['order_type']; ?></td>
			<td><button class='btn btn-xs btn-info progress_result_quiz_action_btn' data-clicked='f'>Толығырақ +<?php echo count($s_val['quiz']); ?></button></td>
		</tr>
		<tr class='progress_result_trail_test_result' style='display: none;'>
			<td colspan="6" class='progress_result_more_info' style="padding:0;">
				<table class='table table-bordered' style="margin:0;">
					<tr>
						<th>Тақырып</th>
						<th>Есеп</th>
						<th>Теория</th>
						<th>Күні</th>
					</tr>
					<?php 
						$topic_num = '';
						$retake_txt = '';
						foreach ($s_val['quiz'] as $key => $m_val) {
							$retake_txt = '';
							if($m_val['topic_num'] == $topic_num){
								$retake_txt = '<span class="pull-right" style="color:red;"><b>Пересдача</b></span>';
							}
							$topic_num = $m_val['topic_num'];
					?>
					<tr>
						<td><?php echo $m_val['topic_name'].$retake_txt; ?></td>
						<td><?php echo $m_val['mark_practice']; ?></td>
						<td><?php echo $m_val['mark_theory']; ?></td>
						<td><?php echo $m_val['created_date']; ?></td>
					</tr>
					<?php }?>
				</table>
			</td>
		</tr>
	<?php } ?>
	</table>
<?php $count++; 
	}
	if($count==0){
		echo "<center><h2>N/A</h2></center>";
	} 
?>