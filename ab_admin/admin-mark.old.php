<?php
	include_once('../connection.php');
	echo "m1 ".date('H:i:s:u')."<br>";
	$group_info_num = $_GET['data_num'];
	try {
		$date_number = isset($_GET['date_number']) ? $_GET['date_number'] : $current_month_n;
		$date_text = isset($_GET['date_text']) ? $_GET['date_text'] : $current_month_s;

		$student_progress_result = array();
		echo "p1 ".date('H:i:s:u')."<br>";
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
		echo "p2 ".date('H:i:s:u')."<br>";
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
		echo "p3 ".date('H:i:s:u')."<br>";
		foreach ($sql_result as $std_val) {
			$full_name = $std_val['suraname'].' '.$std_val['name'];
			$tmp_arr = array('student_name' => $full_name,
							'student_num' => $std_val['student_num'],
							'progress' => array());
			foreach ($progress_list as $pg_num => $pg_val) {
				$tmp = array();
				if (array_key_exists($std_val['student_num'], $pg_val['students'])) {
					$tmp_arr['progess'][$pg_num] = $pg_val['students'][$std_val['student_num']];
				} else {
					$tmp_arr['progess'][$pg_num] = array();
				}
			}
			array_push($student_progress_result, $tmp_arr);
		}

		echo "<br>";
		print_r($student_progress_result);
		echo "<br>";
		echo "p4 ".date('H:i:s:u')."<br>";


 		$stmt = $conn->prepare("SELECT gs.student_num AS student_num, 
 									s.name AS name, 
 									s.surname AS surname, 
 									pg.created_date AS created_date, 
 									ps.attendance AS attendance, 
 									ps.home_work AS home_work 
								FROM group_info gi
							    	INNER JOIN group_student gs 
							        	ON gs.group_info_num = gi.group_info_num
							        		AND gs.start_date <= CURDATE()
							        		AND gs.block != 6
							        INNER JOIN student s
							        	ON s.student_num = gs.student_num
							        		AND s.block != 6
									LEFT JOIN progress_group pg
							        	ON pg.group_info_num = gi.group_info_num
							        		AND pg.created_date LIKE CONCAT(:needle, '%')
							        LEFT JOIN progress_student ps
							        	ON ps.progress_group_num = pg.progress_group_num
							            	AND ps.student_num = s.student_num
								WHERE gi.group_info_num = :group_info_num  
								ORDER BY s.surname, s.name, pg.created_date ASC");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':needle', $date_number, PDO::PARAM_STR);
		$stmt->execute();
		echo "group_info_num: ".$_GET['data_num']."<br>";
		echo "needle: ".$date_number."<br>";
		echo "m2 ".date('H:i:s:u')."<br>";
		$result_marks = $stmt->fetchAll();
		echo "m3 ".date('H:i:s:u')."<br>";
		$stmt = $conn->prepare("SELECT created_date, progress_group_num FROM progress_group WHERE group_info_num = :group_info_num AND created_date LIKE CONCAT(:needle, '%') ORDER BY created_date ASC");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':needle', $date_number, PDO::PARAM_STR);
	    $stmt->execute();
	    echo "m4 ".date('H:i:s:u')."<br>";
	    $result_date = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<table class='table table-bordered table-striped mark-table'>
	<tr>
		<td class='fix fix-left'></td>
		<?php
			$col_count = 0;
			foreach ($result_date as $value) {
				++$col_count;
		?>
		<td style='border:2px solid #999; border-bottom:none;' class='not-fix' colspan="2">
			<center>
				<p style='margin:0; padding:0;'>
				<?php 
					$current_date = substr($value['created_date'],8).".".substr($value['created_date'],5,2);
					echo $current_date;
				?>
				</p>
				<a class='change-date'>Өзгерту</a>
				<form class='form-change-date' method="post" style='display: none;'>
					<input type="hidden" name="pgn" value="<?php echo $value['progress_group_num'];?>">
					<input type="text" name="to_date" class='form-control datePicker' style='width: 100%;' placeholder="dd.mm.YY" required="" value='<?php echo substr($value['created_date'],8).".".substr($value['created_date'],5,2).".".substr($value['created_date'],0,4);?>'>
					<input type="submit" class='btn btn-xs btn-success' name="change_attendance_date" value='Сақтау'>
					<input type="reset" class='btn btn-xs btn-warning cancel-change-date' name="" value='Отмена'>
				</form>
			</center>
		</td>
		<?php }?>
		<td class='fix fix-right'><center>Қорытынды | <?php echo $date_text;?></center></td>
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
		<td style='padding:0; margin:0; border-left:2px solid #999;' class='not-fix'>
			<center><b>Қатысуы</b></center>
		</td>
		<td style='padding:0; margin:0; border-right:2px solid #999;' class='not-fix'>
			<center><b>Баға</b></center>
		</td>
		<?php } ?>
		<td class='fix fix-right'>
			<center><b>Қатысуы | Баға</b></center>
		</td>
	</tr>
	<?php
		$student_num = '';
		$open = true;
		$count = 0;
		$total_attendance = 0;
		$visited = 0;
		$total_home_work = 0;
		$done = 0.0;
		foreach ($result_marks as $value) {
			if($count==0){
				echo "<tr>";
				echo "<td class='fix fix-left'>";
				echo $value['surname'];
				echo "&nbsp;";
				echo $value['name'];
				echo "<input type='hidden' name='datas[]' value='".$value['student_num']."'>";
				echo "</td>";
				$open = false;
			}
			else{
				$open = true;
			}
			$student_num = $value['student_num'];
			++$count;
	?>
		<td class='not-fix' style='border-left:2px solid #999;'>
			<?php
				$attendance_class = ($value['attendance']==1) ? "glyphicon-plus text-success" : "glyphicon-minus text-danger";
				$home_work_element = ($value['attendance']==1) ? $value['home_work'] : "N/A";
				if($value['attendance']==1){
					$total_attendance++;
					$visited++;
					if($value['home_work']!=-0.1){
						$total_home_work++;
					}
					$done += ($value['home_work']==-0.1) ? 0 : $value['home_work'];
				}
				else if($value['attendance']!=null){
					$total_attendance++;
				}
			?>	
			<div>
				<center>
					<?php if($value['attendance']!=null){?>
					<span class='glyphicon <?php echo $attendance_class; ?>'></span>
					<?php } else {?>
					-
					<?php }?>
				</center>
			</div>
		</td>
		<td class='not-fix' style='border-right:2px solid #999;'>
			<div>
				<center>
					<?php
						if($value['attendance']!=null){
							echo "<b>".(($home_work_element==-0.1) ? "N/A" : $home_work_element)."</b>";
						}
						else{
							echo "-";
						}
					?>
				</center>
			</div>
		</td>
		<?php
			if($count==$col_count){
				echo "<td class='fix fix-right'><center>";
				if($visited==0){
					$att = 0;
					$hW = 0;
				}
				else{
					$att = (round($visited/$total_attendance,2)*100);
					$hW = $total_home_work!=0 ? (round($done/$total_home_work,2)*100) : 0;
				}
				$classAtt = '';
				$classHW = '';
				if($att<80){
					$classAtt = 'red';
				}
				else if($att>=95){
					$classAtt = 'green';
				}
				if($hW<80){
					$classHW = 'red';
				}
				else if($hW>=95){
					$classHW = 'green';
				}
				echo "<span style='color:".$classAtt.";'>".$att."%</span>"."<span> | </span><span style='color:".$classHW.";'>".$hW."%</span>";
				echo "</center></td>";
				echo "</tr>";
				$visited = 0;
				$done = 0.0;
				$total_home_work = 0;
				$total_attendance = 0;
			}
			if($count==$col_count){
				$count = 0;
			}
		} 
		echo "m5 ".date('H:i:s:u')."<br>";
	?>
</table>