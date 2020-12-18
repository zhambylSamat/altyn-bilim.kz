<?php
	include_once('../connection.php');
	try {
		$group_info_num = $_GET['data_num'];
		$date_number = isset($_GET['date_number']) ? $_GET['date_number'] : $current_month_n;
		$date_text = isset($_GET['date_text']) ? $_GET['date_text'] : $current_month_s;
		$edit_datas = false;
		$new_marks = isset($_GET['new_marks']) ? true : false;




	    $student_progress_result = array();
		$progress_list = array();
		$stmt = $conn->prepare("SELECT pg.progress_group_num, 
									pg.created_date,
									ps.progress_student_num,
								    ps.student_num,
								    ps.attendance,
								    (pg.created_date = CURDATE()) AS today_exists,
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

		$add_for_today = $stmt->rowCount() == 0 ? true : false;

		foreach ($sql_result as $value) {
			$add_for_today = !$value['today_exists'];
			$progress_list[$value['progress_group_num']]['created_date'] = $value['created_date'];
			$progress_list[$value['progress_group_num']]['students'][$value['student_num']] = array('attendance' => $value['attendance'],
																									'home_work' => $value['home_work'],
																									'progress_student_num' => $value['progress_student_num'],
																									'created_date' => $value['created_date']);
		}

		if ($add_for_today && intval(substr($date_number,5,2)) != intval(date('m'))) {
			$add_for_today = false;
		}

		$student_list = array();
		$stmt = $conn->prepare("SELECT gs.group_student_num,
										gs.student_num,
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
							'group_student_num' => $std_val['group_student_num'],
							'progress' => array());
			foreach ($progress_list as $pg_num => $pg_val) {
				$tmp = array();
				if (array_key_exists($std_val['student_num'], $pg_val['students'])) {
					$tmp_arr['progress'][$pg_num] = $pg_val['students'][$std_val['student_num']];
				} else {
					$tmp_arr['progress'][$pg_num] = array('created_date' => $pg_val['created_date']);
				}
			}
			array_push($student_progress_result, $tmp_arr);
		}
		// echo json_encode($student_progress_result);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<form class='form-inline adding_mark' onsubmit="return check_home_work()" action='teacher_controller.php' method='post'>
	<table class='table table-bordered table-striped mark-table'>
		<tr>
			<td class='fix fix-left'>
				<input type="hidden" name="data_num" value='<?php echo $_GET['data_num']; ?>'>
				<input type="hidden" id='col_num' name="col_number" value="">
			</td>
			<?php
				$html = "";
				$col_count = 0;
				foreach ($progress_list as $pg_num => $pg_val) {
					$col_count++;
					$html .= "<td class='not-fix' colspan='2' date='".$col_count."'>";
						$html .= "<input type='hidden' name='pgn[".$col_count."][]' value='".$pg_num."'>";
						$current_date = substr($pg_val['created_date'],8).".".substr($pg_val['created_date'],5,2); 
						$html .= "<center>".$current_date."</center>";
					$html .= "</td>";
				}

				if ($add_for_today) {
					$col_count++;
					$html .= "<td class='not-fix' colspan='2' data='".$col_count."'>";
						$html .= "<center><div class='form-group'>";
							$html .= "<div class='input-group'>";
								$html .= "<p>".date('d.m')."</p>";
								$html .= "<input type='hidden' name='day' value='".date('d')."'>";
								$html .= "<input type='hidden' name='month' value='".date('m')."'>";
							$html .= "</div>";
						$html .= "</div></center>";
					$html .= "</td>";
				}
				echo $html;
			?>
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
			$html = '';
			$row_count = 0;
			$student_progress_js = array();
			foreach ($student_progress_result as $sp_val) {
				$html .= '<tr>';
					$html .= "<td class='fix fix-left' id='".(++$row_count)."'>";
						$html .= $sp_val['student_name'];
						$html .= "<input type='hidden' name='datas[]' value='".$sp_val['student_num']."'>";
						$html .= "<input type='hidden' name='grstdnum[]' value='".$sp_val['group_student_num']."'>";
					$html .= "</td>";
					$count = 0;
					foreach ($sp_val['progress'] as $pg_num => $pg_val) {
						$count++;
						$html .= "<td class='not-fix' data='".$count."'>";
						$is_progress_edit = $pg_val['created_date'] == date('Y-m-d');
						if (isset($pg_val['progress_student_num'])) {
							$edit_html = '';
							if ($is_progress_edit) {
								$edit_html .= "<div class='new-data' style='display: none;'>";
									$edit_html .= "<center>";
										$css_display = $pg_val['attendance'] == 1 ? 'display: block;' : 'display: none;';
										$edit_html .= "<span class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus' style='".$css_display."'></span>";

										$css_display = $pg_val['attendance'] == 0 ? 'display: block;' : 'display: none;';
										$edit_html .= "<span class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus' style='".$css_display."'></span>";

										$edit_html .= "<p id='row_count' row_count='".$row_count."'></p>";
										$edit_html .= "<input type='hidden' name='attendance[".$col_count."][]' value='".$pg_val['attendance']."'>";
									$edit_html .= "</center>";
									$edit_html .= "<input type='hidden' name='pstdnum[".$col_count."][]' value='".$pg_val['progress_student_num']."'>";
								$edit_html .= "</div>";

							}

							$html .= "<div class='".($is_progress_edit ? 'last-data' : '')."'><center>";
							if ($pg_val['attendance'] == 1) {
								$html .= "<span class='glyphicon glyphicon-plus text-success'></span>";
								if ($is_progress_edit) {
									$student_progress_js[$sp_val['student_num']]['name'] = $sp_val['student_name'];
									$student_progress_js[$sp_val['student_num']]['att'] = 1;
									$student_progress_js[$sp_val['student_num']]['changed'] = true;
								}

							} else if ($pg_val['attendance'] == 0) {
								$html .= "<span class='glyphicon glyphicon-minus text-danger'></span>";
							} 
							$html .= "</center></div>";
							$html .= $edit_html;
							
						} else {
							$edit_html = '';
							if ($is_progress_edit) {
								$edit_html .= "<div class='new-data' style='display: none;'>";
									$edit_html .= "<center>";
										$edit_html .= "<span style='display: none;' class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>";
										$edit_html .= "<span style='display: block;' class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>";
										$edit_html .= "<p id='row_count' row_count='".$row_count."'></p>";
										$edit_html .= "<input type='hidden' name='new_attendance[".$col_count."][]' value='0'>";
									$edit_html .= "</center>";
								$edit_html .= "</div>";
							}
							$html .= "<div class='".($is_progress_edit ? 'last-data' : '')."'><center>";
								$html .= '-';
							$html .= "</center></div>";
							$html .= $edit_html;
						}
						$html .= "</td>";

						$html .= "<td class='not-fix' data='".$count."'><center>";
						if (isset($pg_val['progress_student_num'])) {
							$edit_html = '';
							if ($is_progress_edit) {
								$edit_html .= "<div class='new-data' style='display: none;'><div class='form-group'>";
									$edit_html .= "<input type='number' style='width: 100%;' type='number' name='home_work_mark[".$col_count."][]' min='-0.1' max='1' step='0.1' value='".$pg_val['home_work']."'>";
								$edit_html .= "</div></div>";
							}

							$html .= "<div class='".($is_progress_edit ? 'last-data' : '')."'><center>";
							if ($pg_val['home_work'] == '-0.1' || $pg_val['attendance'] == 0) {
								$html .= "<b>N/A</b>";
							} else {
								$html .= "<b>".$pg_val['home_work']."</b>";
							}
							$html .= "</center></div>";
							$html .= $edit_html;
						} else {
							$edit_html = '';
							if ($is_progress_edit) {
								$edit_html .= "<div class='new-data' style='display: none;'><div class='form-group'>";
									$edit_html .= "<input style='width: 100%;' type='number' name='new_home_work_mark[".$col_count."][]' min='-0.1' max='1' step='0.1' value='0'>";
									$edit_html .= "<input type='hidden' name='new_datas[".$col_count."][]' value='".$sp_val['student_num']."'>";
									$edit_html .= "<input type='hidden' name='new_grstdnum[".$col_count."][]' value='".$sp_val['group_student_num']."'>";
								$edit_html .= "</div></div>";
							}
							$html .= "<div class='".($is_progress_edit ? 'last-data' : '')."'><center>";
								$html .= '-';
							$html .= "</center></div>";
							$html .= $edit_html;
						}
						$html .= "</center></td>";
					}

					if ($add_for_today) {
						$count++;
						$html .= "<td class='not-fix'>";
							$html .= "<center>";
								$html .= "<span class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>";
								$html .= "<span class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>";
								$html .= "<p id='row_count' row_count='".$row_count."'></p>";
								$html .= "<input type='hidden' name='attendance[".$col_count."][]' value='0'>";
							$html .= "</center>";
						$html .= "</td>";

						$html .= "<td class='not-fix'>";
							$html .= "<div class='form-group'>";
								$html .= "<input style='width: 100%;' type='number' name='home_work_mark[".$col_count."][]' min='-0.1' max='1' step='0.1' value='0'></div></td>";
							$html .= "</div>";
						$html .= "</td>";

					}
					$html .= "<td class='fix fix-right'></td>";

				$html .= "</tr>";
			}
			if ($add_for_today) {
				$html .= '<tr>';
					$html .= "<td class='fix fix-left' style='position: absolute; bottom: -30%; width: 100%; background-color: white;'>";
						$html .= "<center>";
							$html .= "<a class='btn btn-sm btn-primary' id='progress' data-toggle='modal' data-target='.box-list-student-progress' style='display: none;'>Тақырыбы</a>";
							$html .= "&nbsp;&nbsp;<input type='submit' id='save' class='btn btn-success btn-sm' name='add_mark' data='".$col_count."' value='Сохранить'>";
							$html .= "<input type='hidden' name='last_col_num' value='".$col_count."'>";
						$html .= "</center>";
					$html .= "</td>";
					$html .= "<td class='fix fix-right'></td>";
				$html .= "</tr>";
			} else if ($is_progress_edit) {
				$html .= '<tr>';
					$html .= "<td class='fix fix-left' style='position: absolute; bottom: -30%; width: 100%; background-color: white;'><center>";
				$html .= "<a class='btn btn-sm btn-primary' id='progress' data-toggle='modal' data-target='.box-list-student-progress' style='display: none;'>Тақырыбы</a>";
				$html .= "<a class='edit-marks btn btn-sm btn-info' col-number='".$col_count."'>Изменить</a>";
				$html .= "<input type='submit' id='save' class='btn btn-sm btn-success' style='display: none;' name='edit_mark' col-number='".$col_count."' value='Сохранить'>";
				$html .= "<a class='btn btn-sm btn-warning cancel-edit-marks' id='reset' style='display: none;' col-number='".$col_count."'>Отмена</a>";
				$html .= "";
					$html .= "</center></td>";
					$html .= "<td class='fix fix-right'></td>";
				$html .= "</tr>";			
			}
			echo $html;
		?>
	</table>
</form>
<script type="text/javascript">
	$obj = $.parseJSON('<?php echo json_encode($student_progress_js);?>');
	console.log($obj);
	if ($obj.length == 0) {
		$required_progress = {};
	} else {
		$.each($obj, function(index, el) {
			$required_progress[index] = {'changed' : el['changed'], 'att' : el['att'], 'name' : el['name']};
		});
	}
	console.log($required_progress);
</script>