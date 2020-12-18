<div id='no_schedule' style='background-color:#FFB564; padding:0.1% 0.1%; color:#555; margin:0.5% 0; font-size: 11px; display: none;'><b>*Сабақ кестесі енгізілмеген</b></div>
<?php
	$result_group_info = array();
	include('../connection.php');
	if(!isset($_GET['search']) || $_GET['search']==''){ 
		try {
			$stmt = $conn->prepare("SELECT gi.group_info_num,
										gi.group_name, 
										gi.comment, 
										t.name, 
										t.surname, 
										s.subject_name,
										DATE_FORMAT(gi.start_lesson, '%H:%i') as start_lesson,
										DATE_FORMAT(gi.finish_lesson, '%H:%i') as finish_lesson, 
										(SELECT count(*) 
										FROM group_student gs, 
											student s 
										WHERE gs.group_info_num = gi.group_info_num 
											AND gs.start_date <= CURDATE() 
											AND s.student_num = gs.student_num 
											AND s.block != 1
											AND gs.block != 6 ) student_quantity,
										(SELECT count(sh.schedule_id)
									    FROM schedule sh
									    WHERE sh.group_info_num in (gi.group_info_num)) schedule_count
									FROM group_info gi, 
										teacher t, 
										subject s 
									WHERE gi.teacher_num=t.teacher_num 
										AND gi.subject_num = s.subject_num 
										AND gi.block != 6
									ORDER BY t.surname, t.name, gi.group_name ASC");
			
		    $stmt->execute();
		    $result_group_info = $stmt->fetchAll();
		    $_SESSION['result_group_info'] = $result_group_info;
		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}	
	}	
	else{
		$q = $_GET['search'];
		foreach ($_SESSION['result_group_info'] as $val) {
			if (strpos(mb_strtolower($val['name']), mb_strtolower($q)) !== false 
				|| strpos(mb_strtolower($val['surname']), mb_strtolower($q)) !== false 
				|| strpos(mb_strtolower($val['subject_name']), mb_strtolower($q)) !== false 
				|| strpos(mb_strtolower($val['group_name']), mb_strtolower($q)) !== false
				|| strpos((mb_strtolower($val['surname'])."_".mb_strtolower($val['name'])), mb_strtolower($q)) !== false 
				|| strpos((mb_strtolower($val['name'])."_".mb_strtolower($val['surname'])), mb_strtolower($q)) !== false) {
				array_push($result_group_info, $val);
			}
		}
	}
?>
<table class="table table-bordered table-hover table-groups-info">
	<?php
		$no_schedule_count = 0;
		foreach ($result_group_info as $value) {
			$bg_color = '';
			if($value['start_lesson']=="00:00" || $value['finish_lesson']=="00:00" || $value['schedule_count'] == 0){
				$bg_color = "#FFB564";
				$no_schedule_count++;
			}
	?>
	<tr class='row-groups-info head'>
		<td style='border-color:black; padding:0;'>
			<div class='group-info'>
				<table class='table table-bordered' style='background-color: <?php echo $bg_color;?>;'>
					<tr>
						<td colspan='4'>
							<a href="group.php?data_num=<?php echo $value['group_info_num'];?>">
								<center><h3 style='margin:0;' class='object-full-name'><?php echo $value['group_name']?></h3></center>
							</a>
						</td>
					</tr>
					<tr>
						<td>
							<center>
								<b><?php echo $value['surname']." ".$value['name'];?></b>
							</center>
						</td>
						<td>
							<center>
								<b><?php echo $value['subject_name'];?></b>
							</center>
						</td>
						<td>
							<center>
								<b>Оқушылар саны:</b> <?php echo $value['student_quantity'];?>
							</center>
						</td>
						<td>
							<center>
								<b>Түсініктеме:<br></b> <?php echo ($value['comment']!='') ? $value['comment'] : "N/A";?>
							</center>
						</td>
					</tr>
				</table>
				
			</div>
			<form class="form-inline group-form" onsubmit="return confirm('Подтвердите действие!!!');" action='admin_controller.php' method='post' style='display: none;'>
				<div class='form-group'>
					<input type="text" name="group_name" class='form-control' value='<?php echo $value['group_name']; ?>'>
				</div>
				<div class='form-group'>
					<?php
						try {
							$stmt = $conn->prepare("SELECT * FROM teacher order by surname asc");
				     
						    $stmt->execute();
						    $result_group_teacher = $stmt->fetchAll();
						} catch (PDOException $e) {
							echo "Error ".$e->getMessage()." !!!";
						}
					?>
					<select name='group_teacher' class='form-control' required="">
						<option value=''>Мұғалім</option>
						<?php 
							foreach ($result_group_teacher as $tValue) {
						?>
						<option value='<?php echo $tValue['teacher_num'];?>' <?php echo ($tValue['name']==$value['name'] && $tValue['surname']==$value['surname']) ? "selected" : ''; ?>><?php echo $tValue['surname']." ".$tValue['name'];?></option>
						<?php } ?>
					</select>
				</div>
				<div class='form-group'>
					<?php
						try {
							$stmt = $conn->prepare("SELECT * FROM subject order by subject_name asc");
				     
						    $stmt->execute();
						    $result_group_subject = $stmt->fetchAll();
						} catch (PDOException $e) {
							echo "Error ".$e->getMessage()." !!!";
						}
					?>
					<textarea name='group_comment' class='form-control' rows='1' cols='30'><?php echo $value['comment'];?></textarea>
					<input type="hidden" name="data_num" value='<?php echo $value['group_info_num'];?>'>
				</div>
				<input type="submit" class='btn btn-sm btn-success' name="edit_group_info" value='Сохранить'>
				<input type="reset" class='btn btn-warning btn-sm' value='Отмена'>
				<!-- <input type="submit" class='btn btn-xs btn-danger' name="delet_group_info" value='Удалить'> -->
				<a class='btn btn-xs btn-danger to_archive' data-name='group' data-num = "<?php echo $value['group_info_num'];?>" title='Архивировать'>
					Архив
				</a>
			</form>
		</td>
		<td style='border-color:black;'>
			<center>
				<button class='btn btn-sm btn-default' btn-info='edit'>&nbsp;&nbsp;&nbsp;&nbsp;<span class='glyphicon glyphicon-option-horizontal'></span>&nbsp;&nbsp;&nbsp;&nbsp;</button>
			</center>
			<center>
				<button class='btn btn-sm schedule-btn' data-toggle='modal' data-target='.box-group-schedule' btn-info='schedule'>&nbsp;&nbsp;&nbsp;&nbsp;<span class='glyphicon glyphicon-calendar'></span>&nbsp;&nbsp;&nbsp;&nbsp;</button>
			</center>
		</td>
	</tr>
	<tr></tr>
	<?php } ?>
</table>
<script type="text/javascript">
	$(document).ready(function(){
		if(<?php echo $no_schedule_count;?>>0){
			$("#no_schedule").show();
		}
	});
</script>