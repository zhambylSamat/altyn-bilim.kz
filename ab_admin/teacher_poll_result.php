<?php
	include_once("../connection.php");
	$teacher_info = array("teacher_num" => $_GET['teacher_num'],
							"surname" => "",
							"name" => "");


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
	try {
		$stmt = $conn->prepare("SELECT t.teacher_num,
										t.surname,
										t.name
								FROM teacher t
								WHERE t.teacher_num = :teacher_num");
		$stmt->bindParam(':teacher_num', $teacher_info['teacher_num'], PDO::PARAM_STR);
		$stmt->execute();
		$teacher_result = $stmt->fetch(PDO::FETCH_ASSOC);
		$teacher_info['surname'] = $teacher_result['surname'];
		$teacher_info['name'] = $teacher_result['name'];


		$stmt = $conn->prepare("SELECT DISTINCT (
									CASE
										WHEN DATE_FORMAT(sp.polled_date, '%d') <= 10 THEN DATE_FORMAT(DATE_SUB(sp.polled_date, INTERVAL 1 MONTH), '%m-%Y')
										WHEN DATE_FORMAT(sp.polled_date, '%d') >= 25 THEN DATE_FORMAT(sp.polled_date, '%m-%Y')
									END
								) AS month
								FROM student_poll sp
								WHERE sp.teacher_num = :teacher_num
								ORDER BY month DESC");
		$stmt->bindParam(':teacher_num', $teacher_info['teacher_num'], PDO::PARAM_STR);
		$stmt->execute();
		$polled_date_result = $stmt->fetchAll();

		$datas = array();
		foreach ($polled_date_result as $value) {
			$month_year = explode('-', $value['month']);
			$month = $month_arr[intval($month_year[0])];
			$tmp = array('month' => $month, 'year' => $month_year[1], 'full' => $value['month']);
			array_push($datas, $tmp);
		}

		if (count($datas)>0) {
			$date = $datas[0]['full'];
			$teacher_num = $teacher_info['teacher_num'];
		}

	} catch (PDOException $e) {
		throw $e;
	}
?>
<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<center><h3><?php echo $teacher_info['surname']." ".$teacher_info['name']; ?></h3></center>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<?php
			if (count($datas) > 0) {
		?>
		<select class='form-control teacher-poll-result-date' data-num='<?php echo $teacher_info['teacher_num']; ?>'>
			<?php foreach ($datas as $value) {
				echo "<option value='".$value['full']."'>".$value['month']." ".$value['year']."</option>";
			} ?>
		</select>
		<?php } else {
			echo "<center></center>";
		} ?>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='teacher-poll-result'>
			<?php
				if (count($datas)) {
					include_once('teacher_poll_result_ajax.php');
				}
			?>
		</div>
	</div>
</div>