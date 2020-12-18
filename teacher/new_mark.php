<?php
	include_once('../connection.php');
	try {
		$date_number = isset($_GET['date_number']) ? $_GET['date_number'] : $current_month_n;
		$date_text = isset($_GET['date_text']) ? $_GET['date_text'] : $current_month_s;

	    $min_day = 1;
	    $edit_marks = false;

	    $stmt = $conn->prepare("SELECT s.student_num student_num, s.name name, s.surname surname FROM student s, group_student gs WHERE gs.student_num = s.student_num AND gs.group_info_num = :group_info_num");
	    $stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_students = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<form onsubmit="return confirm('Подтвердите действие!')" class='form-inline' action='teacher_controller.php' method='post'>
	<table class='table table-bordered table-striped mark-table'>
		<tr>
			<td class='fix fix-left'></td>
			<?php
				$col_count = 0;
				$number = 0;
				foreach ($result_students as $value) {
					++$col_count;
			?>
			<td style='border:2px solid #999; border-bottom:none;' class='not-fix' colspan="2" data="<?php echo $col_count;?>">
				<input type="hidden" name="pgn[<?php echo $col_count; ?>][]" value="<?php echo $value['progress_group_num']; ?>">
				<center>
				<?php 
					echo str_replace("-", ".", substr($value['created_date'],5)); 
					$min_day = intval(substr($value['created_date'],8));
					$number = cal_days_in_month(CAL_GREGORIAN, date(substr($value['created_date'],5,2)), date(substr($value['created_date'],0,4)));
					if($min_day == $number) $add = false;
					if($min_day >= intval(date('d'))) $add = false;
					$last_day = intval(substr($value['created_date'],8,2));
					$edit_marks = ($last_day<=intval($number)) ? true : false;
				?>
				</center>
			</td>
			<?php }?>
			<?php if($add){ ?>
			<td style='border:2px solid #999; border-bottom:none;' class='not-fix' colspan="2" data="<?php echo ++$col_count;?>">
				<center>
					<div class='form-group'>
						<div class='input-group'>
							<?php date("d").".".substr($value['created_date'],5,2); ?>
							<input type="number" min='<?php echo $min_day+1;?>' max="<?php echo $number; ?>" step='1' class='form-control' name="day" value='<?php echo date("d"); ?>'>
							<div class="input-group-addon" style='padding:0;'><?php echo ".".substr($value['created_date'],5,2);?></div>
							<!-- <input type="hidden" name="day" value="<?php echo intval(date('d'))?>"> -->
							<input type="hidden" name="month" value="<?php echo intval(substr($date_number,5));?>">
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
			foreach ($result_marks as $value) {
				if($count==0){
					echo "<tr>";
					echo "<td class='fix fix-left'>";
					echo $value['name'];
					echo "&nbsp;";
					echo $value['surname'];
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
			<td class='not-fix' data='<?php echo $count;?>' style='border-left:2px solid #999;'>
				<?php
					// $edit = false;
					$attendance_class = ($value['attendance']==1) ? "glyphicon-plus text-success" : "glyphicon-minus text-danger";
					$home_work_element = ($value['attendance']==1) ? $value['home_work'] : "N/A";
					// $edit = ($value['attendance']==null) ? false : true;
					if($value['attendance']==1){
						$total_attendance++;
						$visited++;
						$total_home_work++;
						$done += $value['home_work'];
					}
					else if($value['attendance']!=null){
						$total_attendance++;
					}
				?>	
				<!-- <div <?php echo ($edit_datas && $value['created_date']==date('Y-m-d')) ? "class='last-data'" : '' ;?>> -->
				<div <?php echo (true) ? "class='last-data'" : '' ;?>>
					<center>
						<?php if($value['attendance']!=null){?>
						<span class='glyphicon <?php echo $attendance_class; ?>'></span>
						<?php } else{?>
						NULL
						<?php }?>
					</center>
				</div>
				<?php 
					// if($edit_datas && $value['created_date']==date('Y-m-d')){ 
					if(true){
				?>
				<div class='new-data' style='display: none;'>
					<center>
						<span <?php echo ($value['attendance']==1) ? "style='display:block;'" : "style='display:none;'"?> class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>
						<span <?php echo ($value['attendance']==1) ? "style='display:none;'" : "style='display:block;'"?> class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>
						<input type='hidden' name='attendance[<?php echo $count;?>][]' value='<?php echo $value['attendance']; ?>'>
					</center>
				</div>
				<?php } ?>
			</td>
			<td class='not-fix' data='<?php echo $count;?>' style='border-right:2px solid #999;'>
				<!-- <div <?php echo ($edit_datas && $value['created_date']==date('Y-m-d')) ? "class='last-data'" : "" ;?>> -->
				<div <?php echo (true) ? "class='last-data'" : "" ;?>>
					<center>
						<?php 
							echo ($value['attendance']!=null) ? "<b>".$home_work_element."</b>" : "NULL"; 
						?>
					</center>
				</div>
				<?php 
					// if($edit_datas && $value['created_date']==date('Y-m-d')){
					if(true){
				?>
				<div class='new-data' style='display:none;'>
					<div class='form-group'>
						<input style='width:100%' type='number' name='home_work_mark[<?php echo $count; ?>][]' class='' min='0' max='1' step='0.5' value='<?php echo $home_work_element;?>'>
					</div>
				</div>
				<?php } ?>
			</td>
		<?php
				if($add && $count==($col_count-1)){	
					++$count;
					echo "<td style='border-left:2px solid #999;' class='not-fix'>
							<center>
								<span class='glyphicon glyphicon-plus text-success plus-attendance big-attendance-sign' data-sign='plus'></span>
								<span class='glyphicon glyphicon-minus text-danger minus-attendance big-attendance-sign' data-sign='minus'></span>
								<input type='hidden' name='attendance[".$count."][]' value='0'>
							</center>
						</td>";
					echo "<td style='border-right:2px solid #999;' class='not-fix'><div class='form-group'><input style='width:100%' type='number' name='home_work_mark[".$count."][]' class='' min='0' max='1' step='0.5' value='0'></div></td>";
				}
				if($count==($col_count)){
					echo "<td class='fix fix-right'><center>";
					if($visited==0){
						$att = 0;
						$hW = 0;
					}
					else{
						$att = (round($visited/$total_attendance,2)*100);
						$hW = (round($done/$total_home_work,2)*100);
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
		?>
		<?php 
			// if($edit_datas){ 
			if(true){
		?>
		<tr>
		<td class='fix fix-left'></td>
		<?php for ($i=1; $i <= $col_count; $i++) { ?>
		<td style='border:2px solid #999; border-top:none' class='not-fix' colspan='2'>
			<center>
				<?php if($i==$col_count && $add){ ?>
				<input type="submit" class='btn btn-success btn-sm' name="add_mark" data="<?php echo $i; ?>" value="Сохранить">
				<?php 
					// }else if($edit_marks && $i==$col_count){
				}else{
				?>
				<a class='edit-marks btn btn-sm btn-info' col-number='<?php echo $i; ?>'>Изменить</a>
				<input class='btn btn-sm btn-success' style='display:none;' type="submit" name="edit_mark" col-number='<?php echo $i;?>' value='Сохранить'>
				<a class='btn btn-sm btn-warning cancel-edit-marks' style='display:none;' col-number='<?php echo $i;?>'>Отмена</a>				
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