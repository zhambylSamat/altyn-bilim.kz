<?php 
	include('../connection.php');
	if(!$_SESSION['archive_load_page']){
		$_SESSION['archive_page'] = 'student';
	}

	$is_study_session = false;
	$study_session_min = "";
	$study_session_max = "";

	$is_school = false;
	$is_subject = false;
	$is_finish_course = false;

	if (isset($_SESSION['filter_study_session']) && $_SESSION['filter_study_session'] != "") {
		$is_study_session = true;
		$study_session_min = strtotime((intval($_SESSION['filter_study_session'])-1)."-07-16");
		$study_session_max = strtotime($_SESSION['filter_study_session']."-07-15");
	}
	if (isset($_SESSION['filter_school'])) {
		$is_school = $_SESSION['filter_school'];
	}
	if (isset($_SESSION['filter_subject'])) {
		$is_subject = $_SESSION['filter_subject'];
	}
	if (isset($_SESSION['filter_finish_course']) && isset($_SESSION['filter_subject'])) {
		$is_finish_course = $_SESSION['filter_finish_course'] && $_SESSION['filter_subject'];
	}

	$result_student = array();
	try {

		$query = "SELECT s.name,
				    s.surname,
				    s.school,
				    s.phone,
				    s.block,
				    DATE_FORMAT(s.block_date, '%d.%m.%Y') as s_block_date,
				    s.student_num,
				    sj.subject_num,
				    sj.subject_name, 
				    DATE_FORMAT(gs.block_date, '%d.%m.%Y') as gs_block_date
				FROM subject sj,
				    group_student gs,
				    group_info gi,
				    student s
				WHERE gs.student_num = s.student_num
					AND gs.block = 6
				    AND gi.group_info_num = gs.group_info_num
				    AND sj.subject_num = gi.subject_num
				    AND 0 = (SELECT count(gs2.group_student_num)
				    		FROM group_student gs2
				    		WHERE gs2.group_info_num IN (SELECT gi3.group_info_num
				    									FROM group_info gi3
				    									WHERE gi3.subject_num = sj.subject_num)
				    			AND gs2.block != 6
				    			AND gs2.student_num = s.student_num)
				GROUP BY s.student_num, sj.subject_name, s.school
				ORDER BY gs.block_date DESC, s.block_date DESC, s.surname, s.name, sj.subject_name";

		$stmt = $conn->prepare($query);
		$stmt->execute();
		$students_query_result = $stmt->fetchAll();
		
		$result = $students_query_result;
		if($is_subject && $is_school) {
			$tmp_filter_by_subject_school = array();
			$filter_by_subject_school = array();
			foreach ($students_query_result as $key => $value) {
				$tmp_filter_by_subject_school[$value['subject_name']][$value['school']][$key] = $value;
			}
			foreach ($tmp_filter_by_subject_school as $subject) {
				foreach ($subject as  $school) {
					foreach ($school as $val) {
						array_push($filter_by_subject_school, $val);
					}
				}
			}
			$result = $filter_by_subject_school;
		} else if ($is_subject) {
			$tmp_filter_by_subject = array();
			$filter_by_subject = array();
			foreach ($students_query_result as $key => $value) {
				$tmp_filter_by_subject[$value['subject_name']][$key] = $value;
			}
			foreach ($tmp_filter_by_subject as $value) {
				foreach ($value as $val) {
					array_push($filter_by_subject, $val);
				}
			}
			$result = $filter_by_subject;
		} else if ($is_school) {
			$tmp_filter_by_school = array();
			$filter_by_school = array();
			foreach ($students_query_result as $key => $value) {
				$tmp_filter_by_school[$value['school']][$key] = $value;
			}
			foreach ($tmp_filter_by_school as $value) {
				foreach ($value as $val) {
					array_push($filter_by_school, $val);
				}
			}
			$result = $filter_by_school;
		}

		$query = "SELECT s.student_num,
					gi.subject_num,
				    (SELECT sp2.subtopic_num
				    FROM topic t2,
				        subtopic st2,
				        student_progress sp2
				    WHERE sp2.student_num = gs.student_num
				        AND st2.subtopic_num = sp2.subtopic_num
				        AND t2.topic_num = st2.topic_num
				    	AND t2.subject_num = gi.subject_num
				    GROUP BY t2.topic_num, st2.subtopic_num
				    ORDER BY t2.topic_order DESC, st2.subtopic_order DESC
				    LIMIT 1) AS subtopic_num
				FROM student s, 
					group_info gi,
				    group_student gs
				WHERE s.block = 6
					AND gs.student_num = s.student_num
				    AND gi.group_info_num = gs.group_info_num";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$student_last_subtopic_query_result = $stmt->fetchAll();

		$student_last_subtopic_result = array();
		foreach ($student_last_subtopic_query_result as $value) {
			$student_last_subtopic_result[$value['student_num']][$value['subject_num']] = $value['subtopic_num'];
		}

		$query = "SELECT sj.subject_num, count(st.subtopic_num) AS c
				FROM subject sj,
					topic t,
					subtopic st
				WHERE st.topic_num = t.topic_num
					AND t.subject_num = sj.subject_num
				GROUP BY sj.subject_num";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$subtopic_count_query_result = $stmt->fetchAll();

		$subtopic_count_result = array();
		foreach ($subtopic_count_query_result as $value) {
			$subtopic_count_result[$value['subject_num']] = $value['c'];
		}

		$query = "SELECT st.subtopic_order, 
						st.subtopic_num,
						t.topic_num,
						sj.subject_num
				FROM subtopic st,
					topic t,
				    subject sj
				WHERE st.topic_num = t.topic_num
					AND sj.subject_num = t.subject_num
				ORDER BY sj.subject_num, t.topic_order, st.subtopic_order ASC";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$subtopic_order_query_result = $stmt->fetchAll();

		$subtopic_progress = array();

		$step_progress = 0;
		$subject_num = "";
		foreach ($subtopic_order_query_result as $value) {
			if ($subject_num != $value['subject_num']) {
				$step_progress = 0;
				$subject_num = $value['subject_num'];
			}
			$step_progress++;
			$subtopic_progress[$value['subtopic_num']] = round($step_progress * 100 / $subtopic_count_result[$subject_num], 1);
		}

		function define_study_session($date) {
			$d = strtotime($date);
			if ($d >= change_year($d, "-1 year", "16.07.") && $d < change_year($d, "", "15.07.")) {
				return strtodate($d, "-1 year")."-".strtodate($d, "");	
			} else if ($d >= change_year($d, "", "16.07.") && $d < change_year($d, "+1 year", "15.07.")) { 
				return strtodate($d, "")."-".strtodate($d, "+1 year");
			}
		}
		function change_year($date, $pattern, $day_month) {
			if ($pattern == "") {
				return strtotime($day_month.(date("Y", $date)));
			} 
			return strtotime($day_month.(date("Y", strtotime($pattern, $date))));
		}
		function strtodate($date, $pattern) {
			if ($pattern == "") {
				return date("Y", $date);
			}
			return date("Y", strtotime($pattern, $date));
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}

	$student_list = "<table id='student-list'>";
?>
<button class='btn btn-info btn-xs pull-right copy-students'>Скопировать список студентов</button>
<br>
<table class='table table-bordered table-striped'>
	<tr>
		<th><center>#</center></th>
		<th><center>Оқушы</center></th>
		<th style="<?php echo $is_school ? 'display:none;' : ''; ?>"><center>Мектеп</center></th>
		<th><center>Архивке шыққан уақыты</center></th>
		<th><center>Оқу жылы</center></th>
		<th style="<?php echo $is_finish_course ? "" : "display:none"; ?>"><center>Прогресс</center></th>
		<th><center>Әрекет</center></th>
	</tr>
	<?php

		$student_count = 0; 
		$subject_name = "";
		$school = "";
		$student_num = "";
		foreach ($result as $value) { 
			$block_date = "";
			if (isset($student_last_subtopic_result[$value['student_num']]) && isset($subtopic_progress[$student_last_subtopic_result[$value['student_num']][$value['subject_num']]])) {
				$student_progress = $subtopic_progress[$student_last_subtopic_result[$value['student_num']][$value['subject_num']]];
			} else {
				$student_progress = 0;
			}
			if ($is_subject) {
				$block_date = $value['gs_block_date'];
			} else if (!$is_subject && $value['block'] != 6) {
				continue;
			} else {
				$block_date = $value['s_block_date'];
			}

			if ($is_study_session && !(strtotime($block_date) >= $study_session_min && strtotime($block_date) < $study_session_max)) {
				continue;
			}
			if ($is_finish_course && floatval($student_progress) < 90) {
				continue;
			}
			if ($is_subject && $subject_name != $value['subject_name']) {
				$student_count = 0;
				$subject_name = $value['subject_name'];
				echo "<tr><td colspan='7' style='background-color: #D9EDF7; '><center><h4><b>".$subject_name."</b></h4></center></td></tr>";
			}
			if ($is_school && $school != $value['school']) {
				$student_count = 0;
				$school = $value['school'];
				echo "<tr><td colspan='6' style='background-color: #5CB85C;'><center><b>Мектебі: ".($school == "" ? "N/A" : $school)."</b>".($is_subject ? "(".$subject_name.")" : "")."</center></td></tr>";
			}
			$continue = true;
			if ($student_num == $value['student_num']) {
				if ($is_school) {
					if ($school == $value['school']) {
						$continue = true;
					} else {
						$continue = false;
					}
				}
				if ($is_subject) {
					if ($subject_name == $value['subject_name']) {
						$continue = true;
					} else {
						$continue = false;
					}
				}	
			} else {
				$student_num = $value['student_num'];
				$continue = false;
			}

			if ($continue) {
				continue;
			}
	?>
	<tr class='head'>
		<td>
			<center>
				<h4><i class='count	'><?php echo ++$student_count; ?></i></h4>
			</center>
		</td>
		<td>
			<h4 style='display: inline-block;'>
				<a style='cursor: pointer;' class='object_full_name' href="../ab_admin/student_info_marks.php?data_num=<?php echo $value['student_num']; ?>" target="_blank">
					<?php echo $value['surname']?>&nbsp;<?php echo $value['name']?>
					<?php $student_list .= "<tr><td>".$value['surname']." ".$value['name']."</td><td>".$value['school']."</td><td>".$value['phone']."</td></tr>"; ?>
				</a> 
			</h4> 
			<a data-toggle='modal' style='cursor: pointer;' class='student-modal' data-target='.box-pop-up' data-num="<?php echo $value['student_num'];?>">[инфо]</a>
		</td>
		<td style="<?php echo $is_school ? 'display:none;' : ''; ?>">
			<h4><?php echo $value['school'] != '' ? $value['school'] : "-";?></h4>
		</td>
		<td>
			<h4><?php echo $block_date; ?></h4>
		</td>
		<td>
			<?php
				echo define_study_session($block_date);
			?>
		</td>
		<td style='<?php echo $is_finish_course ? "" : "display:none;"; ?>'>
			<h4><?php echo $student_progress."%"; ?></h4>
		</td>
		<td>
			<a class='btn btn-default btn-xs more_info' data-name='student' data_toggle='false' data_num = "<?php echo $value['student_num']; ?>" title='Толығырақ'>
				<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
			<a class='btn btn-xs btn-success from_archive' data-name='student' data-num="<?php echo $value['student_num'];?>" title='Восстановить'>
				<span class='glyphicon glyphicon-open-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
		</td>
	</tr>
	<tr class='body' style='display: none;'></tr>
	<?php }
		if($student_count==0){
	?>
	<tr>
		<td>
			<center>N/A</center>
		</td>
	</tr>
	<?php } ?>
</table>
<?php $student_list .= "</table>"; ?>
<div id='copy-students-to-clipboard'>
	<div style='font-size:0;'><?php echo $student_list; ?></div>
</div>