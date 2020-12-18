<?php
	include_once('../connection.php');
	try {

		$date_number = isset($_GET['date_number']) ? $_GET['date_number'] : $current_month_n;
		$date_text = isset($_GET['date_text']) ? $_GET['date_text'] : $current_month_s;
		$edit_datas = false;
		$new_marks = isset($_GET['new_marks']) ? true : false;
		$student_progress = array();
 		$stmt = $conn->prepare("SELECT gs.student_num student_num, 
 									s.name name, 
 									s.surname surname, 
 									ps.progress_student_num progress_student_num,
 									pg.created_date created_date, 
 									ps.attendance attendance, 
 									ps.home_work home_work, 
 									gs.group_student_num group_student_num
								FROM group_info gi
							    	INNER JOIN group_student gs 
							        	ON gs.group_info_num = gi.group_info_num
							        		AND gs.start_date <= CURDATE()
							        		AND gs.block != 6
							        INNER JOIN student s
							        	ON s.student_num = gs.student_num
							        		AND s.block != 1
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
		$result_marks = $stmt->fetchAll();
		$stmt = $conn->prepare("SELECT created_date, progress_group_num FROM progress_group WHERE group_info_num = :group_info_num AND created_date LIKE CONCAT(:needle, '%') ORDER BY created_date ASC");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':needle', $date_number, PDO::PARAM_STR);
	    $stmt->execute();
	    $min_day = 1;
	    $edit_marks = false;
	    $result_date = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<form class='form-inline' action='teacher_controller.php' method='post'>
	<table class='table table-bordered table-striped mark-table'>
		<tr>
			<td class='fix fix-left'></td>
			<?php
				$col_count = 0;
				$add = ($new_marks) ? true : false;
				$number = 0;
				if(!empty($result_date)){
					$number = cal_days_in_month(CAL_GREGORIAN, date('m'), date("Y"));
					$min_day = 0;
				foreach ($result_date as $value) {
					$edit_datas = (date('m-d')==substr($value['created_date'],5)) ? true : false;
					++$col_count;
			?>
			<td style='border:2px solid #999; border-bottom:none;' class='not-fix' colspan="2" data="<?php echo $col_count;?>">
				<input type="hidden" name="pgn[<?php echo $col_count; ?>][]" value="<?php echo $value['progress_group_num']; ?>">
				<center>
				<?php 
					$current_date = substr($value['created_date'],8).".".substr($value['created_date'],5,2); 
					echo $current_date; 
					$last_month = substr($value['created_date'],5,2);
					$last_day = intval(substr($value['created_date'],8,2));
					$add = ($last_month==date('m') && $last_day<intval(date("d"))) ? true : false;
					$edit_marks = ($last_day<=intval($number)) ? true : false;
				?>
				</center>
			</td>
			<?php }}?>
			<?php if($add){ ?>
			<td style='border:2px solid #999; border-bottom:none;' class='not-fix' colspan="2" data="<?php echo ++$col_count;?>">
				<center>
					<div class='form-group'>
						<div class='input-group'>
							<p><?php echo date('d.m');?></p>
							<input type="hidden" name="day" value="<?php echo intval(date('d'))?>">
							<input type="hidden" name="month" value="<?php echo intval(substr($date_number,5,2));?>">
						</div>
					</div>
				</center>
			</td>
			<?php } ?>
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
			$row_count = 0;
			foreach ($result_marks as $value) {
				if($count==0){
					echo "<tr>";
					echo "<td class='fix fix-left' id='".(++$row_count)."'>";
					echo $value['surname'];
					echo "&nbsp;";
					echo $value['name'];
					echo "<input type='hidden' name='datas[]' value='".$value['student_num']."'>";
					echo "<input type='hidden' name='grstdnum[]' value='".$value['group_student_num']."'>";
					echo "</td>";
					$open = false;
				}
				else{
					$open = true;
				}
				$student_num = $value['student_num'];
				++$count;
		?>
		<?php if(!$new_marks){?>
			<td class='not-fix' data='<?php echo $count;?>' style='border-left:2px solid #999;'>
				<?php
					$attendance_class = ($value['attendance']==1) ? "glyphicon-plus text-success" : "glyphicon-minus text-danger";
					$home_work_element = ($value['attendance']==1) ? $value['home_work'] : "N/A";
					// $edit = ($value['attendance']==null) ? false : true;
					
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
				<div <?php echo ($edit_datas && $value['created_date']==date('Y-m-d')) ? "class='last-data'" : '' ;?>>
				<!-- <div class='last-data'>  -->
					<center>
						<?php if($value['attendance']!=null){?>
						<span class='glyphicon <?php echo $attendance_class; ?>'></span>
						<?php } else {?>
						-
						<?php }?>
					</center>
				</div>
				<?php 
					if($edit_datas && $value['created_date']==date('Y-m-d')){ 
						if($value['attendance']!=null){
							if($value['attendance']==1){
								$name = $value['surname']." ".$value['name'];
								$student_progress[$value['student_num']]['name'] = $name;
								$student_progress[$value['student_num']]['att'] = 1;
								$student_progress[$value['student_num']]['changed'] = true;
							}
				?>
				<div class='new-data' style='display: none;'>
					<center>
						<span <?php echo ($value['attendance']==1) ? "style='display:block;'" : "style='display:none;'"?> class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>
						<span <?php echo ($value['attendance']==1) ? "style='display:none;'" : "style='display:block;'"?> class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>
						<p id='row_count' row_count="<?php echo $row_count;?>"></p>
						<input type='hidden' name='attendance[<?php echo $count;?>][]' value='<?php echo $value['attendance']; ?>'>
					</center>
					<input type="hidden" name="pstdnum[<?php echo $count;?>][]" value="<?php echo $value['progress_student_num']; ?>">
				</div>
				<?php 
						} else if($value['attendance']==null){
				?>
				<div class='new-data' style='display: none;'>
					<center>
						<span <?php echo ($value['attendance']==1) ? "style='display:block;'" : "style='display:none;'"?> class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>
						<span <?php echo ($value['attendance']==1) ? "style='display:none;'" : "style='display:block;'"?> class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>
						<p id='row_count' row_count="<?php echo $row_count;?>"></p>
						<input type='hidden' name='new_attendance[<?php echo $count;?>][]' value='<?php echo $value['attendance']; ?>'>
					</center>
				</div>
				<?php }} ?>
			</td>
			<td class='not-fix' data='<?php echo $count;?>' style='border-right:2px solid #999;'>
			<div <?php echo ($edit_datas && $value['created_date']==date('Y-m-d')) ? "class='last-data'" : '' ;?>>
				<!-- <div class='last-data'> -->
					<center>
						<?php
							if($value['attendance']!=null){
								echo "<b>".(($home_work_element==-0.1) ? "N/A" : $home_work_element)."</b>";
							}
							else if(!$new_marks){
								echo "-";
							}
						?>
					</center>
				</div>
				<?php 
					if($edit_datas && $value['created_date']==date('Y-m-d')){
						if($value['attendance']!=null){
				?>
				<div class='new-data' style='display: none;'>
					<div class='form-group'>
						<input style='width:100%' type='number' name='home_work_mark[<?php echo $count; ?>][]' class='' min='-0.1' max='1' step='0.1' value='<?php echo $home_work_element;?>'>
					</div>
				</div>
				<?php }else if($value['attendance']==null){ ?>
				<div class='new-data' style='display: none;'>
					<div class='form-group'>
						<input style='width:100%' type='number' name='new_home_work_mark[<?php echo $count; ?>][]' class='' min='-0.1' max='1' step='0.1' value='<?php echo $home_work_element;?>'>
						<input type="hidden" name="new_datas[<?php echo $count; ?>][]" value="<?php echo $value['student_num'];?>">
						<input type="hidden" name="new_grstdnum[<?php echo $count; ?>][]" value="<?php echo $value['group_student_num'];?>">
					</div>
				</div>
				<?php }} ?>
			</td>
			<?php }?>
		<?php
				if(($add && $count==($col_count-1)) || $new_marks){
					if(!$new_marks) ++$count;
					echo "<td style='border-left:2px solid #999;' class='not-fix'>
							<center>
								<span class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>
								<span class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>
								<p id='row_count' row_count='".$row_count."'></p>
								<input type='hidden' name='attendance[".$count."][]' value='0'>
							</center>
						</td>";
					echo "<td style='border-right:2px solid #999;' class='not-fix'><div class='form-group'><input style='width:100%' type='number' name='home_work_mark[".$count."][]' class='' min='-0.1' max='1' step='0.1' value='0'></div></td>";
				}
				if($count==($col_count) || $new_marks){
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
				if($count==$col_count || $new_marks){
					$count = 0;
				}
			} 
		?>
		<?php 
			// echo ($edit_datas) ? 'true' : 'false';
			if($edit_datas || $new_marks || $add){
		?>
		<tr>
		<td class='fix fix-left'></td>
		<?php for ($i=1; $i <= $col_count; $i++) { ?>
		<td style='border:2px solid #999; border-top:none' class='not-fix' colspan='2'>
			<center>
				<?php 
					if(($i==$col_count && $add)){ 
				?>
				<a class='btn btn-sm btn-primary' id='progress' data-toggle='modal' data-target='.box-list-student-progress' style='display: none;'>Тақырыбы</a>
				<input type="submit" id='save' class='btn btn-success btn-sm' name="add_mark" data="<?php echo $i; ?>" value="Сохранить">
				<input type="hidden" name="last_col_num" value="<?php echo $col_count; ?>">
				<?php 
					}else if($edit_marks && $i==$col_count){
					// }else{
				?>
				<a class='btn btn-sm btn-primary' id='progress' data-toggle='modal' data-target='.box-list-student-progress' style='display: none;'>Тақырыбы</a>
				<a class='edit-marks btn btn-sm btn-info' col-number='<?php echo $i; ?>'>Изменить</a>
				<input type="submit" id='save' class='btn btn-sm btn-success' style='display: none;' name="edit_mark" col-number='<?php echo $i;?>' value='Сохранить'>
				<a class='btn btn-sm btn-warning cancel-edit-marks' id='reset' style='display:none;' col-number='<?php echo $i;?>'>Отмена</a>				
				<?php } ?>
			</center>
		</td>
		<?php } ?>
		<td class='fix fix-right'>
			<input type="hidden" id='col_num' name="col_number" value="">
			<input type="hidden" name="data_num" value='<?php echo $_GET['data_num']; ?>'>
		</td>
		</tr>
		<?php } ?>
	</table>
</form>
<script type="text/javascript">
	$obj = $.parseJSON('<?php echo json_encode($student_progress);?>');
	$.each($obj, function(index, el) {
		$required_progress[index] = {'changed' : el['changed'], 'att' : el['att'], 'name' : el['name']};
	});
</script>