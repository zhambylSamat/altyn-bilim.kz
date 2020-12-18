<?php
	include_once('../connection.php');
	$group_info_num = $_GET['data_num'];
	try {
		$date_number = isset($_GET['date_number']) ? $_GET['date_number'] : $current_month_n;
		$date_text = isset($_GET['date_text']) ? $_GET['date_text'] : $current_month_s;

		$student_progress_result = array();
		$progress_list = array();
		$stmt = $conn->prepare("SELECT pg.progress_group_num, 
									pg.created_date,
									ps.progress_student_num,
								    ps.student_num,
								    ps.attendance,
								    ps.home_work
								FROM progress_group pg
								    LEFT JOIN progress_student ps
								        ON ps.progress_group_num = pg.progress_group_num
								WHERE pg.group_info_num = :group_info_num
									AND pg.created_date LIKE CONCAT(:needle, '%')
								ORDER BY pg.created_date, ps.student_num");
		$stmt->bindParam(":group_info_num", $group_info_num, PDO::PARAM_STR);
		$stmt->bindParam(':needle', $date_number, PDO::PARAM_STR);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		foreach ($sql_result as $value) {
			$progress_list[$value['progress_group_num']]['created_date'] = $value['created_date'];
			$progress_list[$value['progress_group_num']]['students'][$value['student_num']] = array('attendance' => $value['attendance'],
																									'home_work' => $value['home_work'],
																									'progress_student_num' => $value['progress_student_num']);
		}

		$student_list = array();
		$stmt = $conn->prepare("SELECT gs.student_num,
										s.name,
										s.surname
									FROM group_student gs,
										student s
									WHERE gs.group_info_num = :group_info_num
										AND gs.start_date <= CURDATE()
										AND gs.block != 6
										AND s.student_num = gs.student_num
										AND s.block != 6
									ORDER BY s.surname, s.name");
		$stmt->bindParam(":group_info_num", $group_info_num, PDO::PARAM_STR);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		foreach ($sql_result as $std_val) {
			$full_name = $std_val['surname'].' '.$std_val['name'];
			$tmp_arr = array('student_name' => $full_name,
							'student_num' => $std_val['student_num'],
							'progress' => array());
			foreach ($progress_list as $pg_num => $pg_val) {
				$tmp = array();
				if (array_key_exists($std_val['student_num'], $pg_val['students'])) {
					$tmp_arr['progress'][$pg_num] = $pg_val['students'][$std_val['student_num']];
				} else {
					$tmp_arr['progress'][$pg_num] = array();
				}
			}
			array_push($student_progress_result, $tmp_arr);
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<table class='table table-bordered table-striped mark-table'>
	<tr>
		<td class='fix fix-left'></td>
		<?php
			$col_count = 0;
			foreach ($progress_list as $pg_num => $pg_val) {
				++$col_count;
		?>
		<td class='not-fix' colspan="2">
			<center>
				<p style='margin:0; padding:0;'>
				<?php 
					$current_date = substr($pg_val['created_date'],8).".".substr($pg_val['created_date'],5,2);
					echo $current_date;
				?>
				</p>
				<a class='change-date'>Өзгерту</a>
				<form class='form-change-date' method="post" style='display: none;'>
					<input type="hidden" name="pgn" value="<?php echo $pg_num;?>">
					<input type="text" name="to_date" class='form-control datePicker' style='width: 100%;' placeholder="dd.mm.YY" required="" value='<?php echo substr($pg_val['created_date'],8).".".substr($pg_val['created_date'],5,2).".".substr($pg_val['created_date'],0,4);?>'>
					<input type="submit" class='btn btn-xs btn-success' name="change_attendance_date" value='Сақтау'>
					<input type="reset" class='btn btn-xs btn-warning cancel-change-date' name="" value='Отмена'>
				</form>
			</center>
		</td>
		<?php }?>
		<td class='fix fix-right'></td>
	</tr>
	<tr>
		<td class='fix fix-left'>
			<center>
				<b>Аты - жөні</b>
			</center>
		</td>
		<?php
			for($i = 0; $i<$col_count; $i++){
		?>
		<td class='not-fix'>
			<center><b>Қатысуы</b></center>
		</td>
		<td class='not-fix'>
			<center><b>Баға</b></center>
		</td>
		<?php } ?>
		<td class='fix fix-right'></td>
	</tr>

	<?php
		$html = "";
		foreach ($student_progress_result as $sp_val) {
			$html .= "<tr>";
				$html .= "<td class='fix fix-left'>";
					$html .= $sp_val['student_name'];
					$html .= "<input type='hidden' name='datas[]' vlaue='".$sp_val['student_num']."'>";
				$html .= "</td>";

				foreach ($sp_val['progress'] as $pg_num => $pg_val) {
					$html .= "<td class='not-fix'><center>";
					if (count($pg_val) != 0) {
						if ($pg_val['attendance'] == '1') {
							$html .= "<span class='glyphicon glyphicon-plus text-success'></span>";
						} else if ($pg_val['attendance'] == 0) {
							$html .= "<span class='glyphicon glyphicon-minus text-danger'></span>";
						} 
					} else {
						$html .= '-';
					}
					$html .= "</center></td>";

					$html .= "<td class='not-fix'><center>";
					if (count($pg_val) != 0) {
						if ($pg_val['home_work'] == '-0.1' || $pg_val['attendance'] == '0') {
							$html .= "<b>N/A</b>";
						} else {
							$html .= "<b>".$pg_val['home_work']."</b>";
						}
					} else {
						$html .= '-';
					}
					$html .= "</center></td>";
				}
				$html .= "<td class='fix fix-right'></td>";
			$html .= "</tr>";
		}
		echo $html;
	?>
</table>