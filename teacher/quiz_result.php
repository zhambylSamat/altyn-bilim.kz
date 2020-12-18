<?php
	include_once('../connection.php');
	$false = md5('false');
	$true = md5('true');
	$as_admin = $false;

	$theory_access = false;
	$practice_access = false;

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

	    $stmt = $conn->prepare("SELECT csq.theory, 
	    							csq.practice
	    						FROM config_subject_quiz csq, 
	    							topic t
	    						WHERE csq.subject_num = t.subject_num
	    							AND t.topic_num = :topic_num");
	    $stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $config_subject_quiz_row_count = $stmt->rowCount();
	    if ($config_subject_quiz_row_count == 1) {
	    	$config_subject_quiz_stmt = $stmt->fetch(PDO::FETCH_ASSOC);
	    	$theory_access = $config_subject_quiz_stmt['theory'] == 1 ? true : false;
	    	$practice_access = $config_subject_quiz_stmt['practice'] == 1 ? true : false;
	    }

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

		$result = array();
		foreach ($result_list as $key => $value) {
			$result[$value['student_num']]['quiz_num'] = $value['quiz_num'];
			$result[$value['student_num']]['name'] = $value['name'];
			$result[$value['student_num']]['surname'] = $value['surname'];
			$result[$value['student_num']]['group_student_num'] = $value['group_student_num'];
			if (!isset($result[$value['student_num']]['marks']) && $value['quiz_mark_num'] != '') {
				$result[$value['student_num']]['marks'] = array();
			}
			if ($value['quiz_mark_num'] != '') {
				if (!isset($result[$value['student_num']]['marks'])) {
					$result[$value['student_num']]['marks'] = array();
				}
				$tmp_quiz_mark = array();
				$tmp_quiz_mark['quiz_mark_num'] = $value['quiz_mark_num'];
				$tmp_quiz_mark['mark_theory'] = $value['mark_theory'];
				$tmp_quiz_mark['mark_practice'] = $value['mark_practice'];
				array_push($result[$value['student_num']]['marks'] , $tmp_quiz_mark);
			}
		}

	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php 
	// include_once('../meta.php');
	?>
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
						foreach ($result as $key => $value) {
					?>	
						<tr>
							<th><center><?php echo ++$student_count;?></center></th>
							<td>
								<?php echo $value['surname']." ".$value['name'];?>
							</td>
							<td>
								<?php
									$html = "";
									$quiz_count = isset($value['marks']) ? count($value['marks']) : 0;
									$form = true;
									$retake = false;

									for ($i=0; $i < $quiz_count; $i++) { 
										$theory = $value['marks'][$i]['mark_theory'];
										$practice = $value['marks'][$i]['mark_practice'];
										
										$html .= createInfoAndForm($theory, 
														$practice, 
														$retake, 
														false, 
														$value['group_student_num'], 
														$value['quiz_num'], 
														$value['marks'][$i]['quiz_mark_num'], 
														$key, 
														$topic_num, 
														$group_info_num);

										if ($i == 0) {	
											$retake = ($theory > 0 && $theory < 70.0) || ($practice > 0 && $practice < 70.0);
										}
									}

									if ($quiz_count == 0 || ($quiz_count == 1 && $retake)) {
										$html .= createInfoAndForm("", // theory
														"", // practice
														$retake, 
														true, 
														$value['group_student_num'], 
														$value['quiz_num'], 
														"", // quiz_mark_num
														$key, 
														$topic_num, 
														$group_info_num);
									}
									echo $html;
								?>
							</td>
						</tr>
					<?php } ?>


					<?php 
						function createInfoAndForm($theory, $practice, $retake, $form, $group_student_num, $quiz_num, $quiz_mark_num, $student_num, $topic_num, $group_info_num) {
							global $theory_access;
							global $practice_access;
							$html = "<div class='info-mark'>";
							if ($retake) {
								$html .= "<b style='color:red; display:block;'>Пересдача:</b>";
							}
							if ($theory_access) {
								if ($theory != "") {
									$html .= "<span class='".(($theory>0 && $theory<70) ? 'text-danger' : ($theory>=95 ? 'text-success' : 'text-default') )."'>Теория: ".$theory."%</span>";
								} else {
									$html .= "<span style='color:lightgray;'>Теория: N/A</span>";
								}
							}
							if ($practice_access) {
								if ($practice != "") {
									$html .= "<span class='".(($practice>0 && $practice<70) ? 'text-danger' : ($practice>=95 ? 'text-success' : 'text-default') )."'>&nbsp;&nbsp;&nbsp;&nbsp;Есеп: ".$practice."%</span>";
								} else {
									$html .= "<span style='color:lightgray;'>&nbsp;&nbsp;&nbsp;&nbsp;Есеп: N/A</span>";
								}
							}
							if ($form && ($theory_access || $practice_access)) {
								$html .= "<a class='btn btn-xs btn-default pull-right edit-btn'>Өзгерту</a>";
								$html .= "</div>";

								$html .= "<form method='post' action='teacher_controller.php' class='edit-mark' style='display: none;'>";

								if ($theory_access) {
									$html .= 	"<span class='form-inline'>";
									$html .= 		"<label>Теория: </label>";
									$html .= 		"<div class='input-group'>";
									$html .= 			"<input type='number' class='form-control mark-label' name='quiz_mark_theory' min='0' max='100' step='1' required='' value='".($theory!="" ? $theory : 0)."'>";
									$html .= 			"<div class='input-group-addon'>%</div>";
									$html .= 		"</div>";
									$html .= 	"</span>";
								}
								if ($practice_access) {
									$html .=	"<span class='form-inline'>";
									$html .= 		"<label>&nbsp;&nbsp;&nbsp;&nbsp;Есеп:</label>";
									$html .= 		"<div class='input-group'>";
									$html .= 			"<input type='number' class='form-control mark-label' name='quiz_mark_practice' min='0' step='1' required='' value='".($practice!="" ? $practice : 0)."'>";
									$html .= 			"<div class='input-group-addon'>%</div>";
									$html .= 		"</div>";
									$html .= 	"</span>";
								}

								$html .= 	"<input type='hidden' name='gsn' value='".$group_student_num."'>";
								$html .= 	"<input type='hidden' name='retake' value='".($retake ? md5('y') : md5('n'))."'>";
								$html .= 	"<input type='hidden' name='quiz_status' value='".($quiz_num=="" ? "new" : $quiz_num)."'>";
								$html .= 	"<input type='hidden' name='quiz_mark_status' value='".($quiz_mark_num=="" ? "new" : $quiz_mark_num)."'>";
								$html .= 	"<input type='hidden' name='hid_std_num' value='".$student_num."'>";
								$html .= 	"<input type='hidden' name='hid_t_num' value='".$topic_num."'>";
								$html .= 	"<input type='hidden' name='hid_gi_num' value='".$group_info_num."'>";
								$html .= 	"<input type='submit' name='submit_marks' class='btn btn-sm btn-success' value='Сақтау'>";
								$html .= 	"<input type='reset' class='btn btn-sm btn-warning cancel-btn' value='Отмена'>";
								$html .= "</form>";
							} else {
								$html .= "</div>";
							}
							$html .= "<hr style='padding:1px; margin:1px;'>";
							return $html;
						}
					?>
					
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