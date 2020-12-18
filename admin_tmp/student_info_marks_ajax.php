<?php
	include_once('../connection.php');

	$student_num = (isset($_GET['stn'])) ? $_GET['stn'] : $student_num;
	$subject_num = (isset($_GET['sn'])) ? $_GET['sn'] : $first_subject_num;
	$group_info_num = (isset($_GET['gin'])) ? $_GET['gin'] : $first_group_info_num;
	$group_info_nums = "'";
	$marks = false;
	try {
		$stmt = $conn->prepare('SELECT t.old_group_info_num group_info_num
								                             FROM transfer t, group_info gi 
								                             WHERE t.student_num = :student_num
								                             	AND gi.group_info_num = t.old_group_info_num 
								                             	AND gi.subject_num = :subject_num');
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		foreach ($stmt->fetchAll() as $value) {
			$group_info_nums .= $value['group_info_num']."','";
		}
		$group_info_nums.=$group_info_num."'";
		$stmt = $conn->prepare("SELECT pg.created_date created_date, 
									ps.attendance attendance, 
								    ps.home_work home_work,
								    ri.reason_text
								FROM progress_group pg
								    INNER JOIN progress_student ps
								        ON pg.progress_group_num = ps.progress_group_num
								            AND ps.student_num = :student_num
									LEFT JOIN student_reason sr
								    	ON ps.progress_student_num = sr.progress_student_num
								    LEFT JOIN reason_info ri
								    	ON ri.reason_info_num = sr.reason_info_num
								WHERE pg.group_info_num in ($group_info_nums)
								ORDER BY YEAR(pg.created_date) DESC, MONTH(pg.created_date) DESC, pg.created_date ASC");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_marks = $stmt->fetchAll();
		if($stmt->rowCount()==0){
			$marks = false;
		}
		else {
			$marks = true;
		}


		$stmt = $conn->prepare("SELECT t.topic_num topic_num, t.topic_name topic_name, qm.mark_theory mark_theory, qm.mark_practice mark_practice, qm.created_date created_date
								FROM topic t
								INNER JOIN subject s
								ON s.subject_num = t.subject_num
							    LEFT JOIN quiz q
							    ON q.topic_num = t.topic_num 
							    LEFT JOIN quiz_mark qm
							    ON qm.quiz_num = q.quiz_num
							    	AND qm.student_num = :student_num
							    WHERE t.subject_num = :subject_num
							    	AND t.quiz = 'y'
							    ORDER BY t.topic_order, qm.created_date asc");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_quiz = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT t.name name, t.surname surname, s.subject_name subject_name FROM teacher t, subject s, group_info gi WHERE gi.group_info_num = :group_info_num AND gi.teacher_num = t.teacher_num AND gi.subject_num = s.subject_num");
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->execute();
		$group_info = $stmt->fetch(PDO::FETCH_ASSOC);




		$month_txt = array("","Қаңтар","Ақпан","Наурыз","Сәуір","Мамыр","Мусым","Шілде","Тамыз","Қыркүйек","Қазан","Қараша","Желтоқсан");
		$stmt = $conn->prepare("SELECT name, surname FROM student WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$student_name = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT ttm.trial_test_mark_num,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%m%d%Y') AS date_of_test
								FROM trial_test tt, 
									trial_test_mark ttm
								WHERE tt.student_num = :student_num
									AND tt.subject_num = :subject_num
									AND ttm.trial_test_num = tt.trial_test_num
								ORDER BY ttm.date_of_test DESC");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->execute();
		$trial_test_sql_result = $stmt->fetchAll();
		$tt_result = '';
		foreach ($trial_test_sql_result as $key => $value) {
			$tt_result[$value['trial_test_mark_num']]['mark'] = $value['mark'];
			$tt_result[$value['trial_test_mark_num']]['date'] = $value['date_of_test'];
		}

		$stmt_student_progress = $conn->prepare("SELECT (SELECT st.subtopic_order + (CASE
													                                  WHEN t.topic_order = 1 THEN 0
													                                  ELSE (SELECT SUM((SELECT st2.subtopic_order 
													                                              FROM subtopic st2 
													                                              WHERE st2.topic_num = t2.topic_num
													                                              ORDER BY st2.subtopic_order DESC
													                                              LIMIT 1))
													                                  FROM topic t2
													                                  WHERE t2.subject_num = t.subject_num
													                                  		AND t2.topic_order < t.topic_order)
													                                  END)
													    FROM subtopic st,
													     	student_progress sp,
													     	topic t
													    WHERE sp.student_num = :student_num
													     	AND sp.progress != 0
													     	AND st.subtopic_num = sp.subtopic_num
													     	AND st.topic_num = t.topic_num 
													     	AND t.subject_num = :subject_num
													    ORDER BY t.topic_order DESC, st.subtopic_order DESC
													    LIMIT 1) * 100 / (SELECT count(st.subtopic_num) 
													    FROM subtopic st,
													     	topic t
													    WHERE t.subject_num = :subject_num
													     	AND st.topic_num = t.topic_num) as percentage_result
													ORDER BY percentage_result DESC");

			$stmt_student_progress->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt_student_progress->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
	    	$stmt_student_progress->execute();
	    	$student_progress = $stmt_student_progress->fetch(PDO::FETCH_ASSOC);
	    	$progress_percent = round($student_progress['percentage_result'],1);
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div class='row'>
	<center>
		<input type="hidden" name="gin" value='<?php echo $group_info_num;?>'>
		<input type="hidden" name="sn" value='<?php echo $student_num;?>'>
		<input type="hidden" name="review-header" value='<?php echo $group_info['subject_name'];?>'>
		<input type="hidden" name="sjn" value='<?php echo $subject_num;?>'>
		<?php if($subject_num != 'S5985a7ea3d0ae721486338'){?>
		<a style='cursor: pointer;' class='open-review btn btn-lg btn-info' data-toggle='modal' data-target='.box-comment-for-teacher'><?php echo $group_info['subject_name'];?> пәні мұғалімінің оқушыны бағалауы<b>!</b></a>
		<?php } ?>
		<a class="btn btn-lg btn-primary list-progress" subject-name='<?php echo ucwords($group_info['subject_name']);?>' data-toggle='modal' data-target='.box-list-student-progress'><?php echo $group_info['subject_name'];?> пәнінің тақырыптары</a>
	</center>
	<br>
	<center>
		<div class='subject_progress'>
			<div class="progress">
			  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="<?php echo $progress_percent; ?>" aria-valuemax="100" style="<?php echo "width: ".$progress_percent."%"; ?>">
			    <b><?php echo $progress_percent; ?>%</b>
			  </div>
			  <div class="progress-bar progress-bar-danger" style="background-image: -webkit-linear-gradient(top,#ddd 0,#aaa 100%) !important; background-image: -o-linear-gradient(top,#ddd 0,#aaa 100%) !important; background-image: linear-gradient(to bottom,#ddd 0,#aaa 100%) !important; <?php echo "width: ".(100.0 - $progress_percent)."%"; ?>">
			  	<?php if($progress_percent==0 || $progress_percent=='') {?>
			    <b style='color:#333; '>0%</b>
			    <?php } ?>
			  </div>
			</div>
		</div>
	</center>
	<hr>
	<div class='col-md-6 col-sm-6 col-xs-6'>
		<center>
			<h4>Мұғалім: <?php echo $group_info['surname']." ".$group_info['name'];?></h4>
		</center>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-6'>
		<center>
			<h4>Пән: <?php echo $group_info['subject_name'];?></h4>
		</center>
	</div>
	<?php if($marks){ ?>
	<div class='col-md-6 col-sm-6 col-sm-12'>
		<table class='table table-bordered table-striped'>
			<tr>
				<th><center>#</center></th>
				<th><center>Күні</center></th>
				<th><center>Сабаққа қатысуы</center></th>
				<th><center>Үй жұмысы</center></th>
			</tr>
		<?php
			$month = array("","Қаңтар","Ақпан","Наурыз","Сәуір","Мамыр","Мусым","Шілде","Тамыз","Қыркүйек","Қазан","Қараша","Желтоқсан");
			$count = 0;
			$total_attendance = 0.0;
			$total_home_work = 0.0;
			$attendance = 0.0;
			$home_work = 0.0;
			$home_work_availability = 0;
			$current_month = '';
			$date = '';
			$month_count = 0;
			foreach ($result_marks as $value) {
				if($count==0) $class='success';
				else $class='warning'; 
				if(date("m")==date("m", strtotime($value['created_date']))){
					$display = "";
				}
				else{
					$class='warning';
					$display = 'none';
				}
				if($date!=date("m", strtotime($value['created_date']))){
					$month_count = 0;
		?>
			<?php 
				if($count!=0){
					$result_attendance = ($attendance==0.0) ? 0 : round(($attendance/$total_attendance)*100,2); 
					$result_marks = ($home_work==0.0) ? 0 : round(($home_work/$total_home_work)*100,2);
					// $result_marks = ($home_work==0.0) ? 0 : round(($home_work/$total_home_work)*100,2);
			?>
			<tr>
				<th><center>Қорытынды</center></th>
				<th><center><?php echo $month[intval($date)]; ?></center></th>
				<th><center><?php echo $result_attendance; ?>%</center></th>
				<th><center><?php echo ($home_work_availability==0) ? "N/A" : $result_marks."%"; ?></center></th>
			</tr>
			<?php
				$total_attendance = 0.0;
				$total_home_work = 0.0;
				$attendance = 0.0;
				$home_work = 0.0; 
				$home_work_availability = 0;
				} 
			?>
			<tr>
				<td colspan='4'>
					<center>
						<button class='mnth btn btn-xs btn-<?php echo $class;?>' style='width: 100%;'>
							<h4><?php echo $month[intval(date("m", strtotime($value['created_date'])))]; ?></h4>
						</button>
					</center>
				</td>
			</tr>
		<?php
				}
				$total_attendance++;
				$attendance = ($value['attendance']==1) ? $attendance+1 : $attendance;
				if($value['home_work']!=-0.1){
					
					if($value['attendance']==1){
						$home_work_availability++;
						$total_home_work++;	
					}
					if($value['home_work']!=0){
						$home_work = $home_work+$value['home_work'];
					}
				}
				$date = date("m", strtotime($value['created_date']));
		?>
			<tr style="display:<?php echo $display;?>;" class='<?php echo $month[intval(date("m", strtotime($value['created_date'])))]; ?>'>
				<th><center><?php echo ++$month_count;?></center></th>
				<td><center><?php echo $month[intval(date("m", strtotime($value['created_date'])))]." ".date("d", strtotime($value['created_date'])); ?></center></td>
				<td>
					<center>
						<?php echo ($value['attendance']==1) ? "<span class='glyphicon glyphicon-plus text-success'></span>" : "<span class='glyphicon glyphicon-minus text-danger'></span>"; ?>
						<?php echo ($value['reason_text']!='') ? "<p style='margin:0;'>".substr($value['reason_text'], 2)."</p>" : "" ;?>
					</center>
				</td>
				<td><center><?php echo ($value['attendance']==0) ? "<span class='glyphicon glyphicon-minus text-warning'></span>" : (($value['home_work']==-0.1) ? "<b>N/A</b>" : $value['home_work']); ?></center></td>
			</tr>
		<?php
			$count++; 
			}
			$result_attendance = ($attendance==0.0) ? 0 : round(($attendance/$total_attendance)*100,2); 
			// echo $home_work_availability;
			$result_marks = ($home_work==0.0) ? 0 : round(($home_work/$total_home_work)*100,2);
		?>
		<tr>
			<th><center>Қорытынды</center></th>
			<th><center><?php echo $month[intval($date)]; ?></center></th>
			<th><center><?php echo $result_attendance; ?>%</center></th>
			<th><center><?php echo ($home_work_availability==0) ? "N/A" : $result_marks."%"; ?></center></th>
		</tr>
		</table>
	</div>
	<?php } ?>
	<div class='<?php echo ($marks) ? "col-md-6 col-sm-6 col-xs-12" : "col-md-12 col-sm-12 col-xs-12";?>'>
		<table class='table table-bordered table-striped'>
			<tr>
				<th colspan="3"><center>Аралық бақылау қорытындылары</center></th>
				<?php
					$count = 0; 
					$mark_count = 0;
					$topic_num = '';
					foreach ($result_quiz as $value) {
					if($topic_num!=$value['topic_num']){
						$mark_count = 0;
				?>
				<tr>
					<td style='width:5%;'><center><?php echo ++$count;?></center></td>
					<td style='width:40%;'><?php echo nl2br($value['topic_name']); ?></td>
					<td style='width:55%;'><center>
				<?php } 
					if($value['topic_num']==$topic_num && $value['mark_practice']!=0){
						echo ($value['created_date']!=null) ? "<b>".date("d.m.Y", strtotime($value['created_date']))."</b><br>" : "";
						if($mark_count>0){
							echo "<b style='color:red;'>Пересдача:</b><br>";
						}
				?>
					<span>
					<?php echo ($value['mark_theory']!=null && $value['mark_theory']!=0) ? "<b><span class='".((floatval($value['mark_theory']) >= 95.0) ? "text-success" : ((floatval($value['mark_theory']) < 70.0) ? "text-danger" : "text-default" ))."'>Теория: ".$value['mark_theory']."%</span></b>" : ""; ?>
					</span>
					&nbsp;&nbsp;
					<span>
					<?php echo ($value['mark_practice']!=null && $value['mark_practice']!=0) ? "<b><span class='".((floatval($value['mark_practice']) >= 95.0) ? "text-success" : ((floatval($value['mark_practice']) < 70.0) ? "text-danger" : "text-default" ))."'>Есеп: ".$value['mark_practice']."%</span></b>" : ""; ?>
					</span>
					<br>
				<?php
					}
					else if($value['topic_num']!=$topic_num){
						echo ($value['created_date']!=null) ? "<b>".date("d.m.Y", strtotime($value['created_date']))."</b><br>" : "";
						if($mark_count>0){
							echo "<b style='color:red;'>Пересдача:</b><br>";
						}
				?>
				<span>
					<?php echo ($value['mark_theory']!=null && $value['mark_theory']!=0) ? "<b><span class='".((floatval($value['mark_theory']) >= 95.0) ? "text-success" : ((floatval($value['mark_theory']) < 70.0) ? "text-danger" : "text-default" ))."'>Теория: ".$value['mark_theory']."%</span></b>" : ""; ?>
					</span>
					&nbsp;&nbsp;
					<span>
					<?php echo ($value['mark_practice']!=null && $value['mark_practice']!=0) ? "<b><span class='".((floatval($value['mark_practice']) >= 95.0) ? "text-success" : ((floatval($value['mark_practice']) < 70.0) ? "text-danger" : "text-default" ))."'>Есеп: ".$value['mark_practice']."%</span></b>" : ""; ?>
					</span>
					<br>
				<?php
					}
					echo "<hr style='margin:1px; padding:7px;'>";
					$topic_num = $value['topic_num']; 
					$mark_count++;
					} 
				?>
			</tr>
		</table>
	</div>
</div>
<hr>
<div class='row'>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<table class='table table-bordered table-striped'>
			<tr>
				<th><center>#</center></th>
				<th><center>Балл</center></th>
				<th><center>Дата</center></th>
			</tr>
			<?php 
				$count=0; 
				$chart_script = '';
				if($tt_result!=''){
					$year = '';
					$month = '';
					$day = '';
					$chart_script = '<script>$(document).ready(function(){var chart = new CanvasJS.Chart("chartContainer", {animationEnabled: true, title:{
									text: "Пробный тест: '.$group_info['subject_name'].'"}, axisY :{includeZero: false, prefix: ""}, toolTip: { shared: true}, legend: {fontSize: 13}, data: [{},{type: "spline", showInLegend: true, name: "Пробный тест", yValueFormatString: "#", dataPoints: [';
					foreach($tt_result as $key => $value){
						$chart_script .= '{ x: new Date('.intval(substr($value['date'],4,4)).', '.(intval(substr($value['date'],0,2))-1).','.intval(substr($value['date'],2,2)).'), y: '.intval($value['mark']).' },';
						$year = intval(substr($value['date'],4,4));
						$month = (intval(substr($value['date'],0,2))-1);
						$day = intval(substr($value['date'],2,2));
						// echo intval(substr($value['date'],4,4)).', '.intval(substr($value['date'],0,2)).','.intval(substr($value['date'],2,2))."<br>";
			?>
			<tr>
				<th><center><?php echo ++$count; ?></center></th>
				<th>
					<center><?php echo $value['mark'];?></center>
				</th>
				<th>
					<center><?php echo $month_txt[intval(substr($value['date'],0,2))]." ".substr($value['date'],2,2)." ".substr($value['date'],4,4);?></center>
				</th>
			</tr>
			<?php 
					} 
					// echo $year." ".$month." ".$day;
					$chart_script .= '{ x: new Date('.$year.', '.$month.', '.$day.'), y: '.intval('0').' },';
					$chart_script = rtrim($chart_script, ',');
					$chart_script .= ']}]});chart.render();});</script>';
				}
			?>
		</table>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<div id="chartContainer" style="height: 370px; width: 100%;"></div>
	</div>
</div>
<?php echo $chart_script; ?>