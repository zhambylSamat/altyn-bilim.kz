<?php

	include_once('../connection.php');
	$false = md5('false');
	$true = md5('true');
	$as_admin = $false;
	if(isset($_SESSION['adminNum']) && isset($_SESSION['role']) && $_SESSION['role'] == md5('admin')){
		$as_admin = isset($_GET['teacher']) && $_GET['teacher'] ? $false : $true;
	}
	else if(!isset($_SESSION['teacher_num'])){
		header('location:signin.php');
	}
	if(!isset($_GET['t_num']) || !isset($_GET['data_num'])){
		header('location:index.php');
	}
	else{
		$group_info_num = $_GET['data_num'];
		$topic_num = $_GET['t_num'];
	}
	$result_list = array();
	$result_group_info = array();
	try {
		$stmt = $conn->prepare("SELECT s.subject_num subject_num, 
									s.subject_name subject_name, 
									gi.group_name group_name, 
									tp.topic_name topic_name 
								FROM subject s, 
									group_info gi, 
									topic tp 
								WHERE gi.group_info_num = :group_info_num 
									AND s.subject_num = gi.subject_num 
									AND tp.topic_num = :topic_num");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group_info = $stmt->fetch(PDO::FETCH_ASSOC); 

		$sql = "SELECT q.quiz_num quiz_num, 
					qm.quiz_mark_num quiz_mark_num, 
					s.student_num student_num, 
					gs.group_student_num group_student_num, 
					s.name name, 
					s.surname surname,
					qm.mark_theory mark_theory, 
					qm.mark_practice mark_practice
					FROM group_info gi
				    	INNER JOIN group_student gs 
							ON gs.group_info_num = gi.group_info_num
								AND gs.start_date <= CURDATE()
								AND gs.block != 6
						INNER JOIN student s
							ON s.student_num = gs.student_num
								AND s.block != 1
								AND s.block != 6
				        LEFT JOIN quiz q
				        	ON q.topic_num = :topic_num
				        LEFT JOIN quiz_mark qm
				        	ON qm.quiz_num = q.quiz_num
				        		AND qm.student_num = s.student_num
				    WHERE gi.group_info_num = :group_info_num
				    ORDER BY name, qm.created_date ASC";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_list = $stmt->fetchAll();
		print_r($result_list);
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Группа | Altyn Bilim</title>
	<?php include_once('style.php');?>
	<style type="text/css">
		.changed{
			background-color: #F0AD4E !important;
		}
	</style>
</head>
<body>
<?php 
	if($as_admin == $true){
		include_once('../ab_admin/nav.php');
	}
	else{
		include_once('nav.php');
	}
?>

<section id='quiz-result'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<center>
					<table style='width: 60%;'>
						<tr>
							<td style='width: 50%; padding:1% 0% 1% 1%; vertical-align: top;'>
								<p>Группа: <b><?php echo $result_group_info['group_name'];?></b></p>
							</td>
							<td style='width: 50%; padding:1% 1% 1% 0; '>
								<?php if($as_admin == $false){?>
									<p>Мұғалім: <b><?php echo $_SESSION['teacher_name']." ".$_SESSION['teacher_surname'];?></b></p>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td style='width: 50%; padding:1% 0% 1% 1%; vertical-align: top;'>
								<p>Пән: <b><?php echo $result_group_info['subject_name'];?></b></p>
							</td>
							<td style='width: 50%; padding:1% 1% 1% 0; '>
								<p>Тақырып: <b><?php echo $result_group_info['topic_name'];?></b></p>
							</td>
						</tr>
					</table>
				</center>
				<hr>
				<table class='table table-bordered table-striped'>
					<tr>
						<th style='width:5%;'><center>#</center></th>
						<th style='width:30%;'>Студент</th>
						<th style='width:65%;'>Баға</th>
					</tr>
					<?php
						$student_count = 0;
						$student_num = '';
						$open = false;
						$last_mark = -1;
						$last_mark_practice = -1;
						$count = 0;
						$quiz_status = '';
						$total_count = 0;
						foreach ($result_list as $value) {
							if($last_mark==0 && $last_mark_practice==0 && $student_num==$value['student_num']) {
								$total_count++;
								continue;
							}
							$quiz_status = ($value['quiz_num']==null) ? "new" : $value['quiz_num'];
							if($student_num!=$value['student_num']){
								if($count!=0 && $count<2 && (($last_mark<70.0 && $last_mark>0) || ($last_mark_practice<70.0 && $last_mark_practice>0))){
									if($count>0){
										echo "<b style='color:red;'>Пересдача:</b><br>";
									}
					?>
							<div class='info-mark'>
								<span>Теория: <span style='color:lightgray;'>N/A</span></span>
								&nbsp;&nbsp;&nbsp;
								<span>Есеп: <span style='color:lightgray;'>N/A</span></span>
								<a class='btn btn-xs btn-default pull-right edit-btn'>Өзгерту</a>
							</div>
							<form method='post' action='teacher_controller.php' class='edit-mark' style='display: none;'>
								<span class='form-inline'>
									<label>&nbsp;&nbsp;&nbsp;&nbsp;Теория:</label>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_theory" min='0' max='100' step='1' required="" value='0'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<span class='form-inline'>
									<label>&nbsp;&nbsp;&nbsp;&nbsp;Есеп:</label>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_practice" min='0' max='100' step='1' required="" value='<?php echo ($value['mark_practice']!=null) ? $value['mark_practice'] : "0" ?>'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<input type="hidden" name="gsn" value="<?php echo $value['group_student_num'];?>">
								<input type="hidden" name="retake" value="<?php echo ($count>0) ? md5('y') : md5('n'); ?>">
								<input type="hidden" name="quiz_status" value="<?php echo $quiz_status;?>">
								<input type="hidden" name="quiz_mark_status" value="new">
								<input type="hidden" name="hid_std_num" value='<?php echo $student_num;?>'>
								<input type="hidden" name="hid_t_num" value="<?php echo $topic_num;?>">
								<input type="hidden" name="hid_gi_num" value="<?php echo $group_info_num;?>">
								<input type="submit" name="submit_marks" class='btn btn-sm btn-success' value='Сақтау'>
								<input type="reset" class='btn btn-sm btn-warning cancel-btn' value='Отмена'>
							</form>
							<hr style='padding:1px; margin:1px;'>
					<?php
								}
								$count=0;
								$open = true;
					?>
					<tr>
						<th><center><?php echo ++$student_count;?></center></th>
						<td>
							<?php echo $value['name']." ".$value['surname'];?>
						</td>
						<td>
					<?php 
						} 
						$student_num = $value['student_num']; 
						if($count>0){
							echo "<b style='color:red;'>Пересдача:</b><br>";
						}
					?>
						<!-- <td> -->
							<div class='info-mark'>
								<!-- <?php print_r($result_list[$total_count+2]); ?> -->
								<span class='<?php echo ($value['mark_theory']!=null && $value['mark_theory']<70 && $value['mark_theory']!=0) ? 'text-danger' : (($value['mark_theory']!=null && $value['mark_theory']>=95) ? "text-success" : 'text-default'); ?>'>Теория: <?php echo ($value['mark_theory']!=null && $value['mark_theory']!=0) ? $value['mark_theory']."%" : "<span style='color:lightgray;'>N/A</span>"; ?></span>
								<span class='<?php echo ($value['mark_practice']!=null && $value['mark_practice']<70 && $value['mark_practice']!=0) ? 'text-danger' : (($value['mark_practice']!=null && $value['mark_practice']>=95) ? "text-success" : 'text-default'); ?>'>&nbsp;&nbsp;&nbsp;&nbsp;Есеп: <?php echo ($value['mark_practice']!=null && $value['mark_practice']!=0) ? $value['mark_practice']."%" : "<span style='color:lightgray;'>N/A</span>"; ?></span>
								<?php 
									print_r($result_list);
									if ((isset($result_list[$total_count+1]) 
											&& ($result_list[$total_count+1]['student_num']!=$student_num 
												|| ($result_list[$total_count+1]['student_num']==$student_num 
													&& $result_list[$total_count+1]['mark_theory']==0 
													&& $result_list[$total_count+1]['mark_practice']==0))
											&& !($result_list[$total_count+1]['student_num']==$student_num 
													&& $result_list[$total_count+1]['mark_theory']==0 
													&& $result_list[$total_count+1]['mark_practice']==0)) 
										|| !isset($result_list[$total_count+1])){  ?>
									<a class='btn btn-xs btn-default pull-right edit-btn'>Өзгерту</a>
							</div>
							<form method='post' action='teacher_controller.php' class='edit-mark' style='display: none;'>
								<span class='form-inline'>
									<label>Теория: </label>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_theory" min='0' max='100' step='1' required="" value='<?php echo ($value['mark_theory']!=null) ? $value['mark_theory'] : "0" ?>'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<span class='form-inline'>
									<label>&nbsp;&nbsp;&nbsp;&nbsp;Есеп:</label>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_practice" min='0' max='100' step='1' required="" value='<?php echo ($value['mark_practice']!=null) ? $value['mark_practice'] : "0" ?>'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<input type="hidden" name="gsn" value="<?php echo $value['group_student_num'];?>">
								<input type="hidden" name="retake" value="<?php echo ($count>0) ? md5('y') : md5('n'); ?>">
								<input type="hidden" name="quiz_status" value="<?php echo $quiz_status;?>">
								<input type="hidden" name="quiz_mark_status" value="<?php echo ($value['quiz_mark_num']==null) ? "new" : $value['quiz_mark_num'] ;?>">
								<input type="hidden" name="hid_std_num" value='<?php echo $value['student_num'];?>'>
								<input type="hidden" name="hid_t_num" value="<?php echo $topic_num;?>">
								<input type="hidden" name="hid_gi_num" value="<?php echo $group_info_num;?>">
								<input type="submit" name="submit_marks" class='btn btn-sm btn-success' value='Сақтау'>
								<input type="reset" class='btn btn-sm btn-warning cancel-btn' value='Отмена'>
							</form>
							<?php }else{ echo "</div>"; } ?>
							<hr style='padding:1px; margin:1px;'>
						<!-- </td> -->
					<!-- </tr> -->
					<?php		
						$last_mark = ($value['mark_theory']==null && $value['mark_theory']==0) ? -1 : $value['mark_theory'];
						$last_mark_practice = ($value['mark_practice']==null && $value['mark_practice']==0) ? -1 : $value['mark_practice'];
						$count++;
						$total_count++;
						} 
					?>
					<?php 
						if($count!=0 && $count<2 && (($last_mark<70.0 && $last_mark>0) || ($last_mark_practice<70.0 && $last_mark_practice>0))){ 
							if($count>0){
								echo "<b style='color:red;'>Пересдача:</b><br>";
							}
					?>
					<div class='info-mark'>
								<span>Теория: <span style='color:lightgray;'>N/A</span></span>
								&nbsp;&nbsp;&nbsp;
								<span>Есеп: <span style='color:lightgray;'>N/A</span></span>
								<a class='btn btn-xs btn-default pull-right edit-btn'>Өзгерту</a>
							</div>
							<form method='post' action='teacher_controller.php' class='edit-mark' style='display: none;'>
								<span class='form-inline'>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_theory" min='0' max='100' step='1' required="" value='0'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<span class='form-inline'>
									<label>&nbsp;&nbsp;&nbsp;&nbsp;Есеп:</label>
									<div class='input-group'>
										<input type="number" class='form-control mark-label' name="quiz_mark_practice" min='0' max='100' step='1' required="" value='<?php echo ($value['mark_practice']!=null) ? $value['mark_practice'] : "0" ?>'>
										<div class="input-group-addon">%</div>
									</div>
								</span>
								<input type="hidden" name="gsn" value="<?php echo $value['group_student_num'];?>">
								<input type="hidden" name="retake" value="<?php echo ($count>0) ? md5('y') : md5('n'); ?>">
								<input type="hidden" name="quiz_status" value="<?php echo $quiz_status;?>">
								<input type="hidden" name="quiz_mark_status" value="new">
								<input type="hidden" name="hid_std_num" value='<?php echo $student_num;?>'>
								<input type="hidden" name="hid_t_num" value="<?php echo $topic_num;?>">
								<input type="hidden" name="hid_gi_num" value="<?php echo $group_info_num;?>">
								<input type="submit" name="submit_marks" class='btn btn-sm btn-success' value='Сақтау'>
								<input type="reset" class='btn btn-sm btn-warning cancel-btn' value='Отмена'>
							</form>
							<hr style='padding:1px; margin:1px;'>
						<?php } ?>
				</table>
			</div>
		</div>
	</div>
</section>

<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
	<center>
		<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
	</center>
</div>
<?php include_once('js.php');?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#lll").css('display','none');
	});
	$(function(){
		$('#lll').hide().ajaxStart( function() {
			$(this).css('display','block');  // show Loading Div
		} ).ajaxStop ( function(){
			$(this).css('display','none'); // hide loading div
		});
	});
	$(document).on('click','.edit-btn',function(){
		$(this).parents('.info-mark').slideToggle('fast',function(){
			$(this).next().slideToggle('fast');
		});
		// $(this).parents('tr').css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
	});
	$(document).on('click','.cancel-btn',function(){
		$(this).parents('.edit-mark').slideToggle('fast',function(){
			$(this).prev().slideToggle('fast');
		});
	});
</script>
</body>
</html>