<?php
	include_once('../connection.php');	

	if (isset($_GET['date'])) {
		$date = $_GET['date'];
	}
	if (isset($_GET['teacher_num'])) {
		$teacher_num = $_GET['teacher_num'];
	}
	
	$month_arr = array(1 => 'Қаңтар',
					2 => 'Ақпан',
					3 => 'Наурыз',
					4 => 'Сәуір',
					5 => 'Мамыр',
					6 => 'Маусым',
					7 => 'Шілде',
					8 => 'Тамыз',
					9 => 'Қыркүйек',
					10 => 'Қазан',
					11 => 'Қараша',
					12 => 'Желтоқсан');

	$start_day = 25;
	$end_day = 10;

	$start_date = $start_day."-".$date;
	$tmp_end_date = strtotime($end_day."-".$date);
	$end_date = date('d-m-Y', strtotime("+1 month", $tmp_end_date));
	$current_month = substr($date, 0, 2);
	$current_month_str = $month_arr[intval($current_month)];

	try {

		$stmt = $conn->prepare("SELECT s.student_num,
									s.surname s_surname,
									s.name s_name,
									s.phone,
									t.teacher_num,
									t.surname t_surname,
									t.name t_name,
									sp.id sp_id,
									sps.id sps_id,
									sps.mark,
									tpi.id tpi_id,
									tpi.text
								FROM student_poll sp,
									student_polls sps,
									student s,
									teacher t,
									teacher_poll_info tpi
								WHERE t.teacher_num = :teacher_num
									AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
									AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')
									AND sps.student_poll_id = sp.id
									AND tpi.id = sps.teacher_poll_info_id
									AND t.teacher_num = sp.teacher_num
									AND s.student_num = sp.student_num
								ORDER BY s.student_num, tpi.text, sp.id, sps.id");

		$stmt->bindParam(":teacher_num", $teacher_num, PDO::PARAM_STR);
		$stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
		$stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();


		$total_avg_mark = 0.0;
		$avg_result_arr = array();
		$tpi_text = array();
		$students_poll = array();

		$count = 0;
		foreach ($result as $value) {
			$count++;
			$total_avg_mark += $value['mark'];

			if (!array_key_exists($value['tpi_id'], $avg_result_arr)) {
				$avg_result_arr[$value['tpi_id']] = array("text" => $value['text'], 
															"short" => substr($value['text'], 0, 1),
															"sum_mark" => 0,
															"count" => 0);

			}
			$avg_result_arr[$value['tpi_id']]['sum_mark'] += $value['mark'];
			$avg_result_arr[$value['tpi_id']]['count'] += 1;

			if (!array_key_exists($value['tpi_id'], $tpi_text)) {
				$tpi_text[$value['tpi_id']] = array('full' => $value['text'],
													'short' => substr($value['text'], 0, 1));
			}

			if (!array_key_exists($value['student_num'], $students_poll)) {
				$students_poll[$value['student_num']] = array("surname" => $value['s_surname'],
															"name" => $value['s_name'],
															"polls" => array());
			}
			$students_poll[$value['student_num']]['polls'][$value['tpi_id']] = $value['mark'];

		}
		$total_avg_mark = round($total_avg_mark / $count, 2);

	} catch (PDOException $e) {
		throw $e;
	}
?>

<center><h3><?php echo $current_month_str; ?> айындағы сауалнаманың толық орташа бағасы: <?php echo $total_avg_mark; ?></h3></center>
<table class='table table-striped table-bordered'>
	<tr>
		<th>№</th>
		<th>Сауалнама сұрағы</th>
		<th>Орташа бағасы</th>
	</tr>
	<?php foreach ($avg_result_arr as $value) { ?>
	<tr>
		<td><?php echo $value['short']; ?></td>
		<td><?php echo $value['text']; ?></td>
		<td><?php echo round(($value['sum_mark'] / $value['count']), 2); ?></td>
	</tr>
	<?php } ?>
</table>
<?php if (isset($_SESSION['role']) && $_SESSION['role']==md5('admin')) { ?>
<hr>
<table class='table table-striped table-bordered'>
	<tr>
		<th><center>Аты-жөні</center></th>
		<?php foreach ($tpi_text as $value) { ?>
		<th><center><?php echo $value['short']; ?></center></th>
		<?php } ?>
	</tr>
	<?php foreach ($students_poll as $value) { ?>
	<tr>
		<td><center><?php echo $value['surname']." ".$value['name']; ?></center></td>
		<?php foreach ($value['polls'] as $val) { ?>
		<td><center><?php echo $val; ?></center></td>
		<?php } ?>
	</tr>
	<?php } ?>
</table>
<?php } ?>