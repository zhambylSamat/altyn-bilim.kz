<?php
	include_once('../connection.php');

	$avg = 'avg';
	$last_result = 'last_result';
	
	$result = array();
	$search_attr_subject = isset($_GET['search_attr_subject']) ? $_GET['search_attr_subject'] : '';
	$search_attr_school = isset($_GET['search_attr_school']) ? $_GET['search_attr_school'] : '';
	$search_order_type = isset($search_order_type) && $search_order_type != '' ? $search_order_type : (isset($_GET['search_order_type']) ? $_GET['search_order_type'] : $avg);
	try {
		$result_stmt = array();
		if($search_order_type == $avg){
			$stmt = $conn->prepare("SELECT REPLACE(TRIM(sj.subject_name),' ','_') element_name,
										sj.subject_num element_num,
										s.student_num,
									    TRIM(s.surname) as surname,
									    TRIM(s.name) as name,
									    s.school,
									    s.class,
									    ttm.trial_test_mark_num,
									    ttm.mark,
									    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date_of_test,
									    FORMAT((SELECT AVG(ttm2.mark)
									    FROM trial_test_mark ttm2
									    WHERE ttm2.trial_test_num = tt.trial_test_num), 2) as order_type
									FROM student s,
										subject sj,
									    trial_test tt,
									    trial_test_mark ttm,
									    group_info gi,
									    group_student gs
									WHERE s.block != 6
										AND tt.student_num = s.student_num
										AND IF(:subject_num != '', tt.subject_num, '' ) = :subject_num
										AND IF(:school != '', s.school, '') = :school
										AND tt.subject_num = sj.subject_num
									    AND ttm.trial_test_num = tt.trial_test_num
									    AND gi.subject_num = sj.subject_num
									    ANd gs.group_info_num = gi.group_info_num
									    AND gs.student_num = s.student_num
									    AND gs.block != 6
									ORDER BY sj.subject_name ASC, 
										order_type DESC,
										s.surname ASC, 
									    s.name ASC,
									    ttm.date_of_test DESC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		else if($search_order_type == $last_result){
			$stmt = $conn->prepare("SELECT REPLACE(TRIM(sj.subject_name),' ','_') element_name,
										sj.subject_num element_num,
										s.student_num,
									    TRIM(s.surname) as surname,
									    TRIM(s.name) as name,
									    s.school,
									    s.class,
									    ttm.trial_test_mark_num,
									    ttm.mark,
									    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date_of_test,
									    (SELECT ttm2.mark
									    FROM trial_test_mark ttm2
									    WHERE ttm2.trial_test_num = tt.trial_test_num
									    ORDER BY ttm2.date_of_test DESC
									    LIMIT 1) as order_type
									FROM student s,
										subject sj,
									    trial_test tt,
									    trial_test_mark ttm,
									    group_info gi,
									    group_student gs
									WHERE s.block != 6
										AND tt.student_num = s.student_num
										AND IF(:subject_num != '', tt.subject_num, '' ) = :subject_num
										AND IF(:school != '', s.school, '') = :school
										AND tt.subject_num = sj.subject_num
									    AND ttm.trial_test_num = tt.trial_test_num
									    AND gi.subject_num = sj.subject_num
									    AND gs.group_info_num = gi.group_info_num
									    AND gs.block != 6
									    AND gs.student_num = s.student_num
									ORDER BY sj.subject_name ASC, 
										order_type DESC,
										s.surname ASC, 
									    s.name ASC,
									    ttm.date_of_test DESC");
			$stmt->bindValue(':subject_num', $search_attr_subject, PDO::PARAM_STR);
			$stmt->bindValue(':school', $search_attr_school, PDO::PARAM_STR);
			$stmt->execute();
			$result_stmt = $stmt->fetchAll();
		}
		// print_r($result_stmt);
		foreach ($result_stmt as $val) {
			$result[$val['element_num']]['element_name'] = $val['element_name'];
			$result[$val['element_num']]['student'][$val['student_num']]['sname'] = $val['name'];
			$result[$val['element_num']]['student'][$val['student_num']]['ssurname'] = $val['surname'];
			$result[$val['element_num']]['student'][$val['student_num']]['school'] = $val['school'];
			$result[$val['element_num']]['student'][$val['student_num']]['class'] = $val['class'];
			$result[$val['element_num']]['student'][$val['student_num']]['order_type'] = $val['order_type'];
			$result[$val['element_num']]['student'][$val['student_num']]['chart_count'] = 0;
			$result[$val['element_num']]['student'][$val['student_num']]['ttm'][$val['trial_test_mark_num']]['mark'] = $val['mark'];
			$result[$val['element_num']]['student'][$val['student_num']]['ttm'][$val['trial_test_mark_num']]['date_of_test'] = $val['date_of_test'];
		}
		// foreach ($result as $key => $value) {
		// 	echo $key."<br>";
		// 	echo json_encode($result[$key]);
		// 	echo "<br><br>";
		// }
		// echo json_encode($result);
		// echo "<br><br><br><br><br><br><br><br><br>";
		// print_r($result);
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
	<span>Пән: <b class='h4 text-success'><?php echo $sj_val['element_name']; ?></b></span>
	<table class='table table-bordered'>
		<tr>
			<th><center>#</center></th>
			<th>Аты-жөні</th>
			<th>Mектебі</th>
			<th>Сыныбы</th>
			<th><?php echo $search_order_type==$avg ? 'Орташа балл' : 'Соңғы алған балл'; ?></th>
			<th></th>
		</tr>
	<?php 
		$student_count = 1;
		foreach ($sj_val['student'] as $s_key => $s_val) {
	?>
		<tr class='success'>
			<td><center><?php echo $student_count++; ?></center></td>
			<td>
				<a href="student_info_marks.php?data_num=<?php echo $s_key; ?>" target="_blank">
					<?php echo $s_val['ssurname']." ".$s_val['sname']; ?>
				</a>
			</td>
			<td><?php echo $s_val['school'];?></td>
			<td><?php echo $s_val['class']; ?></td>
			<td><?php echo $s_val['order_type']; ?></td>
			<td><button class='btn btn-xs btn-info progress_result_trial_test_action_btn' data-clicked='f'>Толығырақ +<?php echo count($s_val['ttm']); ?></button></td>
		</tr>
		<tr class='progress_result_trail_test_result' style='display: none;'>
			<td colspan="6" class='progress_result_more_info' style="padding:0;">
				<p data-name='<?php echo $sj_val['element_name']; ?>'><?php
					$s_val['chart_count'] = $chart_count++;
					echo json_encode($s_val);
				?></p>
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