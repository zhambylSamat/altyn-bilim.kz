<?php
	if(!isset($_GET['t_num']) || !isset($_GET['data_num'])){
		header('location:index.php');
	}
	else{
		include_once('../connection.php');
		$group_info_num = $_GET['data_num'];
		$topic_num = $_GET['t_num'];
	}
	$result_list = array();
	$result_group_info = array('asdf');
	try {
		$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name subject_name, gi.group_name group_name, tp.topic_name topic_name, t.name teacher_name, t.surname teacher_surname FROM subject s, group_info gi, teacher t, topic tp WHERE gi.group_info_num = :group_info_num AND s.subject_num = gi.subject_num AND t.teacher_num = gi.teacher_num AND tp.topic_num=:topic_num");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group_info = $stmt->fetch(PDO::FETCH_ASSOC); 

		$sql = "SELECT q.quiz_num quiz_num, qm.quiz_mark_num quiz_mark_num, s.student_num student_num, s.name name, s.surname surname, qm.mark_theory mark_theory, qm.mark_practice mark_practice
					FROM group_info gi
				    	INNER JOIN group_student gs 
							ON gs.group_info_num = gi.group_info_num
								AND gs.start_date <= CURDATE()
						INNER JOIN student s
							ON s.student_num = gs.student_num
				        LEFT JOIN quiz q
				        	ON q.topic_num = :topic_num
				        LEFT JOIN quiz_mark qm
				        	ON qm.quiz_num = q.quiz_num
				        		AND qm.student_num = s.student_num
				    WHERE gi.group_info_num = :group_info_num
				    	ORDER BY name, qm.created_date asc";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->execute();
		$result_list = $stmt->fetchAll();
		// print_r($result_list);
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
<?php include_once('nav.php');?>

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
								<p>Мұғалім: <b><?php echo $result_group_info['teacher_name']." ".$result_group_info['teacher_surname'];?></b></p>
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
						$count = 0;
						$last_mark = -1;
						$last_mark_practice = -1;
						foreach ($result_list as $value) {
							if($value['student_num']!=$student_num){
					?>
					<tr>
						<th><center><?php echo ++$student_count;?></center></th>
						<td><?php echo $value['name']." ".$value['surname'];?></td>
						<td>
					<?php
							}
							$student_num = $value['student_num'];
							if($value['mark_practice']>0) {
					?>
							<div>
								<span>
								<?php echo ($value['mark_theory']!=null && $value['mark_theory']!=0) ? "<span>Теория: ".$value['mark_theory']."%</span>" : "<span style='color:lightgray;'>Теория: N/A</span>"; ?>
								</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span>
									<?php echo ($value['mark_practice']!=null && $value['mark_practice']!=0) ? "<span>Есеп: ".$value['mark_practice']."%</span>" : "<span style='color:lightgray;'>Теория: N/A</span>"; ?>
								</span>
							</div>
					<?php
						} 
					} ?>
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
</script>
</body>
</html>