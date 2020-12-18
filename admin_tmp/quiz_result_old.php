<?php
	if(!isset($_GET['data_num']) && !isset($_SESSION['teacher_num'])){
		header('location:index.php');
	}
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT t.name name, t.surname surname, s.subject_num subject_num, s.subject_name subject_name, gi.group_name group_name, gi.comment comment FROM subject s, group_info gi, teacher t WHERE gi.group_info_num = :group_info_num AND s.subject_num = gi.subject_num AND gi.teacher_num = t.teacher_num");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group_info = $stmt->fetch(PDO::FETCH_ASSOC);

	    $topic_list = array();
	    $topic_list_selected = array();
		$stmt = $conn->prepare("SELECT topic_num, topic_name FROM topic WHERE subject_num = :subject_num");
		$stmt->bindParam(':subject_num', $result_group_info['subject_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	    	$topic_list[$result['topic_num']] = $result['topic_name'];
	    }

	    $stmt = $conn->prepare("SELECT q.quiz_num quiz_num, q.created_date created_date, t.topic_num topic_num, t.topic_name topic_name, s.student_num student_num, s.surname surname, s.name name, qm.mark mark, q.max_mark max_mark FROM quiz q, quiz_tail qt, quiz_mark qm, topic t, student s WHERE q.quiz_num = :quiz_num AND q.quiz_num = qt.quiz_num AND qt.topic_num = t.topic_num AND q.quiz_num = qm.quiz_num AND qm.student_num = s.student_num ORDER BY s.surname");
	    $stmt->bindParam(':quiz_num',$_GET['qNum'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result = $stmt->fetchAll();

	    $quiz_num = '';
	    $submit_date = '';
	    $max_mark = 0.0;
	    $topic = array();
	    $student_data = array();
	    foreach ($result as $value) {
	    	$quiz_num = $value['quiz_num'];
	    	$submit_date = $value['created_date'];
	    	$topic[$value['topic_num']] = $value['topic_name'];
	    	$student_data[$value['student_num']] = array($value['surname'], $value['name'], $value['mark']);
	    	$student_data[$value['student_num']]['surname'] = $value['surname'];
	    	$student_data[$value['student_num']]['name'] = $value['name'];
	    	$student_data[$value['student_num']]['mark'] = $value['mark'];
	    	$max_mark = $value['max_mark'];
	    }
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Бақылау | Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
	<?php include_once('nav.php');?>

	<section class='quiz-body'>
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
									<p>Мұғалім: <b><?php echo $result_group_info['name']." ".$result_group_info['surname'];?></b></p>
								</td>
							</tr>
							<tr>
								<td style='width: 50%; padding:1% 0% 1% 1%; vertical-align: top;'>
									<p>Пән: <b><?php echo $result_group_info['subject_name'];?></b></p>
								</td>
								<td style='width: 50%; padding:1% 1% 1% 0; '>
									<p>Түсініктеме: <b><?php echo $result_group_info['comment'];?></b></p>
								</td>
							</tr>
						</table>
					</center>
					<hr>
					<form class='form-inline' onsubmit='return checkBeforeSubmit();' action='teacher_controller.php' method='post'>
						<div style='width: 50%; vertical-align: top;' class='form-group'>
							<center><p>Бақылау жазған күн: <b><?php echo $submit_date;?></b></p></center>
						</div>
						<div style='width: 49%;' class='form-group'>
							<div class='tpc'>
								<i><h4>Қарастырылған тақырып(тар)</h4></i>
								<?php foreach ($topic as $key => $value) {?>
								<div class='single-topic' style='border:1px solid lightgray; border-radius: 5px; padding:2% 4%;'>
									<span style='overflow:hidden;'><?php echo $value;?></span>
								</div>
								<?php } ?>
							</div>
						</div>
						<hr>
						<table class='table table-bordered table-striped'>
							<tr>
								<th>Аты-жөні</th>
								<th>
									<p>Максималды балл: <b><?php echo $max_mark;?></b></p>
								</th>
							</tr>
							<?php
								$mark_sum = 0.0;
								$mark_quantity = 0;  
								foreach ($student_data as $key => $value) { 
							?>
							<tr>
								<td>
									<h4><?php echo $value['surname']." ".$value['name'];?></h4>
								</td>
								<td>
									<h4><?php echo $value['mark'];?> балл</h4>
								</td>
							</tr>
							<?php
									$mark_sum += $value['mark'];
									$mark_quantity ++;
								} 
							?>
							<tr>
								<td colspan='2'>
									<h4 class='pull-right'>Орташа балл: <i><b><?php echo ($mark_quantity!=0) ? round($mark_sum/$mark_quantity,2) : "N/A";?></b></i></h4>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</section>

	<?php include_once('js.php');?>
</body>
</html>









<div class='quiz'>
						<div class='row'>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<table class='table table-bordered table-striped'>
									<tr>
										<th colspan='3'>
											<center>Аралық бақылау</center>
										</th>
									</tr>
									<tr>
										<th><center>Бақылау жазған уақыт</center></th>
										<th><center>Қарастырылған тақырып(тар)</center></th>
										<th><center>Ортақ балл</center></th>
									</tr>
									<?php
										$quiz_list = array(); 
										try {
											$stmt = $conn->prepare("SELECT q.quiz_num quiz_num, DATE_FORMAT(q.created_date, '%d.%m.%Y') created_date, t.topic_name topic_name, AVG(qm.mark) average, q.max_mark max_marl FROM quiz q, quiz_tail qt, quiz_mark qm, topic t WHERE q.group_info_num = :group_info_num AND qt.quiz_num = q.quiz_num AND qt.topic_num = t.topic_num AND qm.quiz_num = q.quiz_num GROUP BY t.topic_name, q.quiz_num ORDER BY q.created_date asc");
											$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
						    				$stmt->execute();
						    				$quiz_list = $stmt->fetchAll();
										} catch (PDOException $e) {
											echo "Error ".$e->getMessage()." !!!";
										}
									?>
									<?php
										$quiz_num = '';
										for ($i = 0; $i<count($quiz_list); $i++) { 
											$quiz_num = $quiz_list[$i][0];
									?>
									<tr>
										<td><center><h3><a href='quiz_result.php?qNum=<?php echo $quiz_num;?>&data_num=<?php echo $_GET['data_num'];?>'><?php echo $quiz_list[$i][1];?></a></h3></center></td>
										<td>
											<center>
												<?php
													while($i<count($quiz_list) && $quiz_num==$quiz_list[$i][0]){
														echo "<div style='width:96%; border:2px solid lightgray; border-radius:5px; padding:1%; margin:2%'><i>".$quiz_list[$i][2]."</i></div>";
														$i++;
													}
													--$i;
												?>
											</center>
										</td>
										<td>
											<center>
												<h3>
													<?php
														$class=''; 
														echo round($quiz_list[$i][3],2)." / ".$quiz_list[$i][4];
													?>
												</h3>
											</center>
										</td>
									</tr>
									<?php } ?>
								</table>
							</div>
						</div>
					</div>