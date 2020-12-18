
<?php 
	include('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		header('location:signin.php');
	}
	if(!isset($_SESSION['load_page']) || !$_SESSION['load_page']){
		$_SESSION['load_page'] = true;
	}
	$pages = array('student','teacher','subject','group','parent','schedule','dob','progress_result');
	if(isset($_SESSION['page'])){
		if(!in_array($_SESSION['page'], $pages)){
			$_SESSION['page'] = $pages[0];
		}
	}
	else{
		$_SESSION['page'] = $pages[0];
	}
	$list = array();
	try {
		$prize_count = 0;
		$stmt = $conn->prepare("SELECT s.subject_num sNum, s.subject_name sName, t.topic_num tNum, t.topic_name tName, st.subtopic_num stNum, st.subtopic_name stName FROM subject s, topic t, subtopic st WHERE s.subject_num = t.subject_num AND t.topic_num = st.topic_num order by s.created_date, t.created_date, st.created_date asc");
	    $stmt->execute();
	    $result_list = $stmt->fetchAll();
	    foreach ($result_list as $value) {
	    	$list[$value['sNum']]['name'] = $value['sName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['name'] = $value['tName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['subtopic'][$value['stNum']]['name'] = $value['stName'];
	    }
	    $_SESSION['list-subject-topic-subtopic'] = $list;


	    $stmt = $conn->prepare("SELECT spn.id, 
									s.surname,
							        s.name,
									gi.group_name,
							        sj.subject_name,
							        t.topic_name,
							        qm.mark_theory,
							        qm.mark_practice,
							        DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
								FROM student_prize_notification spn,
									group_info gi,
						            group_student gs,
						            student s,
						            subject sj,
						            topic t,
						            quiz q,
						            quiz_mark qm
						        WHERE spn.quiz_mark_num = qm.quiz_mark_num
					         		AND qm.mark_theory in (100, 0)
					                AND qm.mark_practice > 94
					                AND spn.group_student_num = gs.group_student_num
					                AND gs.group_info_num = gi.group_info_num
					                AND qm.quiz_num = q.quiz_num
					                AND t.topic_num = q.topic_num
					                AND sj.subject_num = t.subject_num
					                AND s.student_num = gs.student_num
						        ORDER BY spn.id ASC");
	    $stmt->execute();
	    $prize_notification_result = $stmt->fetchAll();
	    $prize_count = $stmt->rowCount();

	    $stmt = $conn->prepare("SELECT an.attendance_notification_num, 
	    							s.surname,
									s.name, 
								    gi.group_name,
								    DATE_FORMAT(pg.created_date, '%d.%m.%Y') AS created_date
								FROM attendance_notification an,
									student s,
								    group_info gi,
								    group_student gs,
								    progress_group pg,
								    progress_student ps
								WHERE an.action = 3
									AND an.group_student_num = gs.group_student_num
								    AND gs.group_info_num = gi.group_info_num
								    AND ps.progress_student_num in (an.first_abs, an.second_abs, an.third_abs)
								    AND pg.progress_group_num = ps.progress_group_num
								    AND ps.student_num = s.student_num
								ORDER BY s.surname, s.name, gi.group_name, created_date ASC");

	  	$stmt->execute();
	  	$result_an = $stmt->fetchAll();
	  	$attendance_notification_count = $stmt->rowCount()/3;
	  	// print_r($result_an);
	  	$stmt = $conn->prepare("SELECT qrn.id,
	  								st.student_num,
									st.name,
								    st.surname,
								    sj.subject_name,
								    t.topic_name,
								    qm.mark_theory,
								    qm.mark_practice,
								    DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
								FROM subject sj,
									topic t,
								    student st,
								    quiz q,
								    quiz_mark qm,
								    quiz_retake_notification qrn
								WHERE qm.quiz_mark_num in (qrn.retake_1, qrn.retake_2)
								    AND q.quiz_num = qm.quiz_num
								    AND t.topic_num = q.topic_num
								    AND sj.subject_num = t.subject_num
								    AND st.student_num = qm.student_num");
	  	$stmt->execute();
	  	$result_qrn = $stmt->fetchAll();
	  	$quiz_retake_notification_count = $stmt->rowCount()/2;
	  	// $quiz_retake_notification_count = 10;

	  	$stmt = $conn->prepare("SELECT count(student_num) as coming_dob_count 
						FROM student
						WHERE DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%m-%d')
							AND block != 6
							AND dob != '0000-00-00'
						    AND DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '%m-%d') >= DATE_FORMAT(dob, '%m-%d')");
	  	$stmt->execute();
	  	$dob_count = $stmt->fetch(PDO::FETCH_ASSOC);
	  	$coming_dob_count = $dob_count['coming_dob_count'];
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admin - Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet/less" type='text/css' href="css/style.less">
	<link href="../new_year/css/new_year.css" rel="stylesheet">
</head>
<body>
	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>
	<center>
		<div id='alert'>
			
		</div>
	</center>
	
<?php include_once('nav.php');?>
	<section id='body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<ul class="nav nav-tabs">
					 	<li role="presentation" class="navigation <?php echo ($_SESSION['page']==$pages[0]) ? "active" : "" ;?>" data='student'><a href="#">Студент <span class="label label-success"><?php echo ($prize_count>0) ? $prize_count : ""; ?></span><span class="label label-danger"><?php echo ($attendance_notification_count>0) ? $attendance_notification_count : ""; ?></span><span class="label label-danger"><?php echo ($quiz_retake_notification_count>0) ? $quiz_retake_notification_count : ""; ?></span></a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[1]) ? "active" : "" ;?>' data='teacher'><a href="#">Мұғалім</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[2]) ? "active" : "" ;?>' data='subject'><a href="#">Пән</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[3]) ? "active" : "" ;?>' data='group'><a href="#">Группа</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[4]) ? "active" : "" ;?>' data='parent'><a href="#">Ата-ана</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[5]) ? "active" : "" ;?>' data='schedule'><a href="#">Сабақ кестесі</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[7]) ? "active" : "" ;?>' data='progress_result'><a href="#">Прогресс. Қорытынды</a></li>
					 	<li role="presentation" class='navigation <?php echo ($_SESSION['page']==$pages[6]) ? "active" : "" ;?>' data='dob'><a href="#">Туған күн <span class="label label-success"><?php echo ($coming_dob_count>0) ? $coming_dob_count : ""; ?></a></li>
					</ul>
					<br>
					<div class='student box' data-test="<?php echo $_SESSION['page'];?>" style='<?php echo ($_SESSION['page']==$pages[0]) ? "display:block;" : "display:none;"?>'>
						<button class='btn btn-success btn-sm student-modal' data-toggle='modal' data-target='.box-student-form' data-action='new-student' at='new-student' id='new-student-btn'>Жаңа оқушыны енгізу</button>
						<a class='btn btn-sm btn-default news' data-toggle='modal' data-target='.box-news' data-type='student'>Жаңалықтар (Студент)</a>
						<a class='btn btn-sm btn-default abs-reason' data-toggle='modal' data-target='.box-abs-reason' data-type='abs-reason'>Сабаққа келмеу себептері</a>
						<?php if($prize_count>0){ ?>
							<a class='btn btn-success btn-sm prize-notification' data-toggle='modal' data-target='.box-prize-notification' data-type='prize-notification'>
								95 баллдан асқандар
								<span class='badge'><?php echo $prize_count; ?></span>
							</a>
						<?php } ?>
						<?php if($attendance_notification_count>0){ ?>
							<a class='btn btn-danger btn-sm attendance-notification' data-toggle='modal' data-target='.box-attendance-notification' data-type='attendance-notification'>
								3 күн қатарынан келмегендер
								<span class='badge'><?php echo $attendance_notification_count; ?></span>
							</a>
						<?php } ?>
						<?php if($quiz_retake_notification_count>0){ ?>
							<a class='btn btn-danger btn-sm quiz-retake-notification' data-toggle='modal' data-target='.box-quiz-retake-notification' data-type='attendance-notification'>
								Пересдачадан құлағандар
								<span class='badge'><?php echo $quiz_retake_notification_count; ?></span>
							</a>
						<?php } ?>
						<br>
						<span class='search_box'>
							<?php include('load_search_box.php');?>
						</span>
						<select class='form-control pull-right search_type' style="width: 20%;">
							<option value='default'>Әдепкі қалпы</option>
							<option value='school'>Мектеп бойынша</option>
							<option value='teacher'>Мұғалім бойынша</option>
							<option value='subject'>Пән бойынша</option>
						</select>
						<hr>
						<div class='students'>
							<?php 
								// if(!isset($search_type) || !in_array($search_type, $search_type_arr)){
									include_once('index_students.php');
								// }
								// else if(isset($search_type) && in_array($search_type, $search_type_arr)){
									// include_once('index_students_selective_search.php');	
								// }
							?>
						</div>
					</div>

					<div class='teacher box' style='<?php echo ($_SESSION['page']==$pages[1]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[1]) ? include_once('admin_'.$pages[1].'.php') : "";?>
					</div>

					<div class='subject box' style='<?php echo ($_SESSION['page']==$pages[2]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[2]) ? include_once('admin_'.$pages[2].'.php') : "";?>
					</div>

					<div class='group box' style='<?php echo ($_SESSION['page']==$pages[3]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[3]) ? include_once('admin_'.$pages[3].'.php') : "";?>
					</div>

					<div class='parent box' style='<?php echo ($_SESSION['page']==$pages[4]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[4]) ? include_once('admin_'.$pages[4].'.php') : "";?>
					</div>

					<div class='schedule box' style='<?php echo ($_SESSION['page']==$pages[5]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[5]) ? include_once('admin_'.$pages[5].'.php') : "";?>
					</div>

					<div class='progress_result box' style='<?php echo ($_SESSION['page']==$pages[7]) ? "display:block;" : "display:none;";?>'>
						<?php ($_SESSION['page']==$pages[7]) ? include_once('admin_'.$pages[7].'.php') : "";?>
					</div>

					<div class='dob box' style='<?php echo ($_SESSION['page']==$pages[6]) ? "display:block;" : "display:none;"?>'>
						<?php ($_SESSION['page']==$pages[6]) ? include_once('admin_'.$pages[6].'.php') : "";?>
					</div>
				</div>
			</div>
		</div>
	</section>
<!-- <center><a data-toggle='modal' class='btn' data-target='.box-data'><u>Өзгерту</u></a></center> -->
	<div class='edit-data'>
		<div class='box'>
		</div>
	</div>

<!-- Large modal -->
<?php 
	try {
		$stmt = $conn->prepare("SELECT subject_num, subject_name FROM subject order by subject_name asc");
	    $stmt->execute();
	    $result_subject_modal = $stmt->fetchAll();
	    $for_topic = '';
	    $subject_name = '';
	    if($stmt->rowCount()>0){
	    	$for_topic = $result_subject_modal[0][0];
	    	$subject_name = $result_subject_modal[0][1];
	    }
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<div class="modal fade box-data" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
			<div class='row'>
				<div class='col-md-4 col-sm-4'>
					<select class='edit-modal form-control' data-name='subject'>
						<?php foreach ($result_subject_modal as $value) { ?>
							<option value="<?php echo $value['subject_num'];?>"><?php echo $value['subject_name'];?></option>
						<?php } ?>
					</select>
					<form class='form-inline edit-subject' action='admin_controller.php' method="post">
						<div class='form-group'>
							<input type="hidden" name="data_num" value='<?php echo $for_topic;?>'>
							<input type="text" class='form-control' name="data_name" value='<?php echo $subject_name;?>'>
							<input type="submit" class='btn btn-xs btn-success' name="edit_subject">
						</div>
					</form>
				</div>
				<div class='col-md-4 col-sm-4'>
					<div class='edit-modal topic-list' data-name='topic'>
						<?php 
							$part = 'header-part';
							include("edit_modal.php");
						?>
					</div>
				</div>
				<div class='col-md-4 col-sm-4' style='display: none;'>
					<form class='delete_subject' onsubmit='return beforeSubmit();' action='admin_controller.php' method='post'>
						<input type="hidden" name="data_num" value='<?php echo $for_topic;?>'>
						<input type="submit" class='btn btn-xs btn-default delete-btn' name="delete_subject" value='Удалить "<?php echo $subject_name;?>"'>
					</form>
				</div>
			</div>
    	</div>
    	<div class="modal-body">
    		<?php
    			$part = 'body-part';
    			$subpart = 'topic';
    			include("edit_modal.php");
    		?>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-comment-for-teacher" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<center>
    					<form class='form-inline' method='post' action='admin_controller.php'>
    						<div class='form-class'>
    							<?php
    								$count = 0;
    								try {
    									$description = 'review';
    									$stmt = $conn->prepare("SELECT * FROM review_info where description = :description");
    									$stmt->bindParam(':description', $description, PDO::PARAM_STR);
    									$stmt->execute();
    									$result_reviews = $stmt->fetchAll();
    								} catch (PDOException $e) {
										echo "Error: " . $e->getMessage();
									}
    							?>
    							<?php foreach($result_reviews as $value){ ?>
    							<div class='form-group' style='display: block;'>
    								<label><b><?php ++$count; ?></b></label>
    								<input type="text" name="review" class='form-control input_comment' data-toggle="tooltip" data-placement="left" title="<?php echo $value['review_text']; ?>" value='<?php echo $value['review_text']; ?>' required="" placeholder="М: Сабақ үлгерімі.">
    								&nbsp;&nbsp;
    								<a class="btn btn-sm btn-danger" data-action="remove" name="">Удалить</a>
    								<a style="display:none;" class="btn btn-sm btn-primary" data-action="restore" name="">Восстановить</a>
    								&nbsp;&nbsp;
    								<a style="display:none;" class="btn btn-sm btn-warning" data-action="reset" name="">Отмена</a>
    								<input type="hidden" name="rin[]" value='<?php echo $value['review_info_num'];?>'>
    							</div>
    							<?php } ?>
    						</div>
    						<br>
    						<a style='width: 10%; border:1px solid lightgray' class='btn btn-sm add-row-review'><b>+</b></a>
    						<hr>
    						<?php
    							try {
    								$description = 'comment';
									$stmt = $conn->prepare("SELECT * FROM review_info where description = :description");
									$stmt->bindParam(':description', $description, PDO::PARAM_STR);
									$stmt->execute();
									$result_comment = $stmt->fetch(PDO::FETCH_ASSOC);
    							} catch (PDOException $e) {
									echo "Error: " . $e->getMessage();
								}
    						?>
    						<div class='form-group'>
    							<table class='table table-bordered'>
    								<tr>
    									<td style='width: 50%'><?php echo ($result_comment['review_text']=='') ? "N/A" : nl2br($result_comment['review_text']);?></td>
    									<td style='width: 50%'><textarea cols='50' rows='10' class='form-control' name='review_comment'><?php echo $result_comment['review_text'];?></textarea></td>
    								</tr>
    							</table>
    						</div>
    						<hr>
    						<input type="hidden" name="review_comment_num" value='<?php echo $result_comment['review_info_num'];?>'>
    						<input type="submit" class='btn btn-sm btn-success' name="comment_for_teacher" value='Сақтау'>
    					</form>
    				</center>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-prize-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<!-- <h3>Алтын білім үздіктері</h3> -->
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    					$count = 0; 
	    					foreach ($prize_notification_result as $key => $value) { 
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count;?></center></td>
	    						<td>
	    							<center>
			    						<span>
			    							<span class='h4'><b><?php echo $value['surname']." ".$value['name'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $value['group_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<?php echo ($value['mark_theory']!=0) ? "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>" : "";?>
			    							&nbsp;&nbsp;
			    							<span class='h4 text-success'><b><?php echo "Есеп: ".$value['mark_practice'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
			    						</span>
			    					</center>
		    					</td>
		    					<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $value['id'];?>'>
		    							<a class='btn btn-sm btn-danger' data-action='remove-prize-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-action='restore-prize-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_prize_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-attendance-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>3 күн қатарынан келмеген оқушылар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    						$count = 0;
	    						for ($i=0; $i < count($result_an); $i=$i+3) {
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count; ?></center></td>
	    						<td>
	    							<center>
	    								<span>
			    							<span class='h4'><b><?php echo $result_an[$i]['surname']." ".$result_an[$i]['name'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h4'><b><?php echo $result_an[$i]['group_name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4' style='color:#999;'>Дата 1:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i]['created_date']; ?></b></span>
			    							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							<span class='h4' style='color:#999;'>Дата 2:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i+1]['created_date']; ?></b></span>
			    							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			    							<span class='h4' style='color:#999;'>Дата 3:</span> <span class='h4 text-danger'><b><?php echo $result_an[$i+2]['created_date']; ?></b></span>
			    						</span>
	    							</center>
	    						</td>
	    						<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $result_an[$i]['attendance_notification_num'];?>'>
		    							<a class='btn btn-sm btn-danger' data-item='ann' data-action='remove-attendance-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-item='ann' data-action='restore-attendance-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_attendance_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-quiz-retake-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пересдачадан құлағандар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<form method='post' action='admin_controller.php'>
	    				<table class='table table-bordered'>
	    					<?php
	    						$count = 0;
	    						for ($i=0; $i < count($result_qrn); $i=$i+2) {
	    					?>
	    					<tr>
	    						<td><center><?php echo ++$count; ?></center></td>
	    						<td>
	    							<center>
	    								<span>
			    							<span class='h4'><b><?php echo $result_qrn[$i]['surname']." ".$result_qrn[$i]['name'];?></b></span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h5'>[<?php echo $result_qrn[$i]['subject_name'].", ".$result_qrn[$i]['topic_name']; ?>]</span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4'>1:</span>
			    							<?php echo ($result_qrn[$i]['mark_theory']!=0) ? "<span class='h4 ".(($result_qrn[$i]['mark_theory']<70) ? 'text-danger' : '')."'><b>Теория: ".$result_qrn[$i]['mark_theory']."</b></span>&nbsp;&nbsp;" : "";?>
			    							<span class='h4 <?php echo ($result_qrn[$i]['mark_practice']<70) ? "text-danger" : "" ; ?>'><b><?php echo "Есеп: ".$result_qrn[$i]['mark_practice'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $result_qrn[$i]["created_date"]; ?>]</span>
			    						</span>
			    						<br>
			    						<span>
			    							<span class='h4'>2:</span>
			    							<?php echo ($result_qrn[$i+1]['mark_theory']!=0) ? "<span class='h4 ".(($result_qrn[$i+1]['mark_theory']<70) ? 'text-danger' : '')."'><b>Теория: ".$result_qrn[$i+1]['mark_theory']."</b></span>&nbsp;&nbsp;" : "";?>
			    							<span class='h4 <?php echo ($result_qrn[$i]['mark_practice']<70) ? "text-danger" : "" ; ?>'><b><?php echo "Есеп: ".$result_qrn[$i+1]['mark_practice'];?></b></span>
			    							&nbsp;&nbsp;
			    							<span class='h5'>[<?php echo $result_qrn[$i+1]["created_date"]; ?>]</span>
			    						</span>
	    							</center>
	    						</td>
	    						<td>
		    						<center>
		    							<input type="hidden" name="" value='<?php echo $result_qrn[$i]['id'];?>'>
		    							<a class='btn btn-sm btn-danger' data-item='quizRetakeNot' data-action='remove-quiz-retake-notification'>Тізімнен өшіру</a>
		    							<a class='btn btn-sm btn-warning' data-item='quizRetakeNot' data-action='restore-quiz-retake-notification' style='display: none;'>Отмена</a>
		    						</center>
		    					</td>
	    					</tr>
	    					<?php } ?>
	    				</table>
    					<center>
    						<input type="submit" class='btn btn-sm btn-success' name="submit_quiz_retake_notification" value='Сақтау'>
    					</center>
    				</form>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-group-schedule" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Сабақ кестесі</h3></center>
    		<center><h4 class="modal-title" id='group_name'></h4></center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-news box-suggestion box-abs-reason box-student-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title"></h3></center>
    	</div>
    	<div class="modal-body">
    		
    	</div> 
    </div>
  </div>
</div>
	
	

	<!-- <script src="js/index.js"></script> -->
	<script type="text/javascript">
		// ---------------------------------------review_start------------------------------------
$(document).on('click','.add-row-review',function(){
	$('.modal-body .form-class').append('<div class="form-group" style="display:block;"><label><b></b></label><input type="text" class="form-control" required="" name="new_review[]" placeholder="М: Сабақ үлгерімі.">&nbsp;&nbsp;<a class="btn btn-sm btn-danger" data-action="remove" name="">Удалить</a><a style="display:none;" class="btn btn-sm btn-primary" data-action="restore" name="">Восстановить</a>&nbsp;&nbsp;<a style="display:none;" class="btn btn-sm btn-warning" data-action="reset" name="">Отмена</a></div>');
	// console.log($('.modal-body .form-class').find('.form-group').length);
});
$(document).on('click','.add-row-reason',function(){
	$('.box-abs-reason .modal-body .form-class').append('<div class="form-group" style="display:block;"><label><b></b></label><input type="text" class="form-control" required="" name="new_reason[]" placeholder="М: Ауырып калдым.">&nbsp;&nbsp;<a class="btn btn-sm btn-danger" data-action-reason="remove" name="">Удалить</a><a style="display:none;" class="btn btn-sm btn-primary" data-action-reason="restore" name="">Восстановить</a>&nbsp;&nbsp;<a style="display:none;" class="btn btn-sm btn-warning" data-action-reason="reset" name="">Отмена</a></div>');
	// console.log($('.modal-body .form-class').find('.form-group').length);
});
$(document).on('click','a[data-action=remove]',function(){
	// console.log($(this).parent().find('input').attr('name'));
	if($(this).parent().find('input').attr('name')=='new_review[]'){
		$(this).parent().remove();
	}
	else if($(this).parent().find('input').attr('name')=='review'){
		lightAlert($(this).parent(), '#d9534f', 0.3, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin_remove[]');
		$(this).parent().find('a[data-action=restore]').show();
		$(this).parent().find('input[name=review]').prop( "disabled", true );
		$(this).parent().find('input[name=review]').attr('name','remove_review[]');
	}
});
$(document).on('click','a[data-action-reason=remove]',function(){
	if($(this).parent().find('input').attr('name')=='new_reason[]'){
		$(this).parent().remove();
	}
	else if($(this).parent().find('input').attr('name')=='reason'){
		lightAlert($(this).parent(), '#d9534f', 0.3, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin_remove[]');
		$(this).parent().find('a[data-action-reason=restore]').show();
		$(this).parent().find('input[name=reason]').prop( "disabled", true );
		$(this).parent().find('input[name=reason]').attr('name','remove_reason[]');
	}
});
$(document).on('click','a[data-action=restore]',function(){
	if($(this).parent().find('input').attr('name')=='remove_review[]'){
		lightAlert($(this).parent(), '#5cb85c', 0, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		$(this).parent().find('a[data-action=remove]').show();
		$(this).parent().find('input[name="remove_review[]"]').prop( "disabled", false );
		$(this).parent().find('input[name="remove_review[]"]').attr('name','review');
	}
});
$(document).on('click','a[data-action-reason=restore]',function(){
	if($(this).parent().find('input').attr('name')=='remove_reason[]'){
		lightAlert($(this).parent(), '#5cb85c', 0, 300);
		$(this).hide();
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		$(this).parent().find('a[data-action-reason=remove]').show();
		$(this).parent().find('input[name="remove_reason[]"]').prop( "disabled", false );
		$(this).parent().find('input[name="remove_reason[]"]').attr('name','reason');
	}
});

$(document).on('keyup','.box-comment-for-teacher .modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action=reset]').show();
		if($(this).parent().find('input').attr('name')=='review'){
			$(this).parent().find('input[name=review]').attr('name','update_review[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_review[]'){
			$(this).parent().find('input[name="update_review[]"]').attr('name','review');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});
$(document).on('keyup','.box-abs-reason .modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action-reason=reset]').show();
		if($(this).parent().find('input').attr('name')=='reason'){
			$(this).parent().find('input[name=reason]').attr('name','update_reason[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action-reason=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_reason[]'){
			$(this).parent().find('input[name="update_reason[]"]').attr('name','reason');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});

$(document).on('click','.box-comment-for-teacher .modal-body .form-class a[data-action=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_review[]'){
		$(this).parent().find('input[name="update_review[]"]').attr('name','review');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});

$(document).on('click','.box-abs-reason .modal-body .form-class a[data-action-reason=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_reason[]'){
		$(this).parent().find('input[name="update_reason[]"]').attr('name','reason');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});

$(document).on('click', 'a[data-action=remove-prize-notification]',function(){
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name','spn[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-prize-notification]',function(){
	console.log($(this).prev().prev().attr('name'));
	if($(this).prev().prev().attr('name')=='spn[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('click', 'a[data-action=remove-attendance-notification], a[data-action=remove-quiz-retake-notification]',function(){
	$item = $(this).data('item');
	if($(this).prev().attr('name')==''){
		$(this).prev().attr('name',$item+'[]')
		$(this).next().show();
		$(this).hide();
		lightAlert($(this).parents('tr'), '#d9534f', 0.3, 300);
	}
});
$(document).on('click', 'a[data-action=restore-attendance-notification], a[data-action=restore-quiz-retake-notification]',function(){
	console.log($item);
	if($(this).prev().prev().attr('name')==$item+'[]'){
		$(this).prev().show();
		$(this).prev().prev().attr('name','');
		$(this).hide();
		lightAlert($(this).parents('tr'), '#5cb85c', 0, 300);
	}
});

$(document).on('keyup','.modal-body .form-class input[type=text]',function(){
	if($(this).val()!=$(this).prop('defaultValue')){
		$(this).css({'border':"1px solid #F0AD4E","box-shadow":"0px 0px 10px #F0AD4E"});
		$(this).parent().find('a[data-action=reset]').show();
		if($(this).parent().find('input').attr('name')=='review'){
			$(this).parent().find('input[name=review]').attr('name','update_review[]');
			$(this).parent().find('input[type=hidden]').attr('name','rin_update[]');
		}
	}
	else if($(this).prop('defaultValue')==$(this).val()){
		$(this).css({"border":"1px solid #ccc","box-shadow":"none"});
		$(this).parent().find('a[data-action=reset]').hide();
		if($(this).parent().find('input').attr('name')=='update_review[]'){
			$(this).parent().find('input[name="update_review[]"]').attr('name','review');
			$(this).parent().find('input[type=hidden]').attr('name','rin[]');
		}
	}
});
$(document).on('click','.modal-body .form-class a[data-action=reset]',function(){
	$(this).parent().find('input[type=text]').css({"border":"1px solid #ccc","box-shadow":"none"});
	$(this).parent().find('input[type=text]').val($(this).parent().find('input[type=text]').prop('defaultValue'));
	$(this).hide();	
	if($(this).parent().find('input').attr('name')=='update_review[]'){
		$(this).parent().find('input[name="update_review[]"]').attr('name','review');
		$(this).parent().find('input[type=hidden]').attr('name','rin[]');
	}
});
// ---------------------------------------review_end--------------------------------------
$(document).on('keyup','.topic-input input',function(){
	if($(this).val().toLowerCase()=='бақылау'){
		$(this).val('');
		$(this).removeAttr('name','');
		$(this).parent().slideUp('fast');
		$(this).parent().next().find('textarea').val("Аралық бақылау: ");
		$(this).parent().next().find('textarea').attr('name','new-quiz-name');
		$(this).parent().next().slideDown('fast');
	}
});
$(document).on('keyup','.quiz-input textarea',function(){
	if($(this).val().toLowerCase().substr(0,15)!="аралық бақылау:"){
		$(this).val('');
		$(this).removeAttr('name');
		$(this).parent().slideUp('fast');
		$(this).parent().prev().find('input').val('');
		$(this).parent().prev().find('input').attr('name','new-topic-name');
		$(this).parent().prev().slideDown('fast');
	}
});

$globalSubjectName = '<?php echo $subject_name;?>';
$(document).on('mouseover','.edit-input, .input_comment',function(){
	$('[data-toggle="tooltip"]').tooltip();
});
function lightAlert($element, $color, $opacity, $time){
	$element.css({'background-color':$color});
	$res = $element.css( "background-color" )
	$bgColor = $res.substring(4,$res.length-1);
	$element.stop();
	$element.animate({backgroundColor: 'rgba('+$bgColor+', '+$opacity+')' },$time);
}
function beforeSubmit(){
	if(confirm("Вы точно хотите удалить предмет \""+$globalSubjectName+"\". Все данные включая все темы и подтемы будут удалены!")){
		if(confirm("Подтвердите действие!")){
			return true;
		}
		else return false;
	}
	return false;
}
// -------------------------------------start-edit-modal--------------------------------
$(document).on('change','.edit-modal',function(){
	$data_name = $(this).attr('data-name');
	console.log($data_name);
	if($data_name=='subject'){
		$val = $(this).find('option:selected').val();
		$text = $(this).find('option:selected').text();
		$(this).parents('.modal-header').find('.delete-btn').val('Удалить "'+$text+'"');
		$(this).parents('.modal-header').find('.delete-btn').prev().val($val);
		$globalSubjectName = $text;
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=hidden]').val($val);
		$(this).parents('.modal-header').find('.edit-subject').find('input[type=text]').val($text);
		// $(this).parents('.modal-header').find('.delete_subject').attr('onsubmit','return confirm("Вы точно хотите удалить предмет \"'+$text+'\"")');
		// $('.topic-list').text("Loading...");
		$('.topic-list').html("<center><img src='../img/loading.gif' style='width:100%;'></center>");
		$('.topic-list').load("edit_modal.php?part=header-part&data_num="+$val);
		$('.box-data .modal-body').text('Loading...');
		$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
	}
	else if($data_name='topic_list'){
		$val = $(this).find('option:selected').val();
		$data = $(this).find('option:selected').attr('data');
		if($data=='all'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=topic&data_num="+$val);
		}
		else if($data=='single'){
			$('.box-data .modal-body').text('Loading...');
			$('.box-data .modal-body').load("edit_modal.php?part=body-part&subpart=subtopic&data_num="+$val);
		}
	}
});
$(document).on('keyup','.edit-input',function(){
	$(this).css({'box-shadow':'0px 0px 10px #f0ad4e',"border-color":'#f0ad4e'});
	$(this).attr('data-original-title',$(this).val());
	$(this).parents('.form-group').find('.cancel-edit').show();
});
$(document).on('click','.cancel-edit',function(){
	$val = $(this).attr('data');
	$child = $(this).parents('.form-group').find('.edit-input');
	$child.val($val);
	$child.attr('data-original-title',$val);
	$child.css({'box-shadow':'none',"border-color":'#ccc'});
	$(this).hide();
});
$(document).on('click','.remove-data',function(){
	lightAlert($(this).parents('.form-group'),"#d9534f", 0.3, 800);
	$(this).parents('.form-group').find('.edit-input').attr('name','null');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','deleted[]');
	$(this).parent().prev().hide();
	$(this).next().show();
	$(this).hide();
});
$(document).on('click','.restore',function(){
	lightAlert($(this).parents('.form-group'),"#5cb85c",0, 1000);
	$(this).parents('.form-group').find('.edit-input').attr('name','data_name[]');
	$(this).parents('.form-group').find('.edit-input').prev().attr('name','data_num[]');
	$(this).parent().prev().show();
	$(this).prev().show();
	$(this).hide();
});
$(document).on('click','.move',function(){
	$direction = $(this).attr('direction');
	$classUp = 'glyphicon-chevron-up';
	$classDown = 'glyphicon-chevron-down';
	$classStop = 'glyphicon-record';
	$parent = $(this).parents('.form-group');
	if($direction!='none'){
		$parent.slideUp(200,function(){
			if($direction=='up'){
				$down = $parent.find('.move-down').attr('direction');
				$up = $parent.prev().find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','down');
					$parent.find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
					$parent.prev().find('.move-down').attr('direction','none');
					$parent.prev().find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','none');
					$parent.find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
					$parent.prev().find('.move-up').attr('direction','up');
					$parent.prev().find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
				}
				$parent.prev().before($parent);
			}
			if($direction=='down'){
				$down = $parent.next().find('.move-down').attr('direction');
				$up = $parent.find('.move-up').attr('direction');
				if($down == 'none'){
					$parent.find('.move-down').attr('direction','none');
					$parent.find('.move-down span').removeClass('glyphicon-chevron-down').addClass('glyphicon-record');
					$parent.next().find('.move-down').attr('direction','down');
					$parent.next().find('.move-down span').removeClass('glyphicon-record').addClass('glyphicon-chevron-down');
				}
				else if($up == 'none'){
					$parent.find('.move-up').attr('direction','up');
					$parent.find('.move-up span').removeClass('glyphicon-record').addClass('glyphicon-chevron-up');
					$parent.next().find('.move-up').attr('direction','none');
					$parent.next().find('.move-up span').removeClass('glyphicon-chevron-up').addClass('glyphicon-record');
				}
				$parent.next().after($parent);
			}
			$parent.slideDown(200,function(){
				lightAlert($parent,'#f0ad4e',0,1000);
			});
			
		});
	}
});
// -------------------------------------end-edit-modal----------------------------------
// ----------------------------------
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
// ----------------------
$(document).on('click','.edit_user, .cancel_edit',function(){
	$(this).parents('.head').find('.user_info').toggle();
});
$(document).on('click','.more_info',function(){
	// $data_toggle = $(this).attr('data_toggle');
	$data_num = $(this).attr('data_num');
	$data_name = $(this).attr('data-name');
	if($data_name == 'student'){
		// if($data_toggle=='false'){
			$(this).parents('.head').next().html("<b>Loading...</b>");
			$(this).parents('.head').next().load("students_in_group.php?data_num="+$data_num);
			// $(this).attr('data_toggle','true');
		// }
	}
	$(this).parents('.head').next().toggle();
});

$(document).on('click','.to_archive',function(){

	$object_full_name = $(this).parents('tr').find('.object-full-name').text();
	$data_num = $(this).data('num');
	$data_name = $(this).data('name');
	if($data_name == 'student' || $data_name == 'group'){
		checkGroupActivation($data_num, $object_full_name, $data_name, $(this));
	}
	else {
		archive($data_num, $object_full_name, $data_name, $(this));
	}
});

function checkGroupActivation(data_num, object_full_name, data_name, this_obj){
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('checkGroupActivation'))?>&data_num="+data_num+"&data_name="+data_name,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success && data.count==0){
	    		archive(data_num, object_full_name, data_name, this_obj);
	    	}
	    	else{
	    		if(data_name == 'student'){
	    			alert("Оқушының жаңадан бастайтын курстары бар. Архивке салу мүмкін емес!");
	    		}
	    		else if(data_name == 'group'){
	    			alert("Группада жақында курсты бастайтын оқушылар бар. Архивке салу мүмкін емес!");
	    		}
	    	}
	    },
	  	error: function(dataS) 
    	{
    		alert("Қате. Программистпен жолығыңыз. "+dataS);
    		console.log(dataS);
    	} 	        
   	});
}

function archive(data_num, object_full_name, data_name, this_obj){

	$object_full_name = object_full_name;
	$data_num = data_num;
	$data_name = data_name;
	$this = this_obj;

	if(confirm("Вы точно хотите архивировать? ("+$object_full_name.trim()+")")){
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('toArchive'))?>&data_num="+$data_num+"&data_name="+$data_name,
	    	contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	console.log(data);
		    	if(data.success){
		    		$elem = $this.parents("tr");
		    		$elem.find('.count').text("-");
		    		$elem.nextAll(".head").each(function(){
		    			$(this).find('.count').text(parseInt($(this).find('.count').text().trim())-1);
		    		});
		    		$this.parents("tr").stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$elem.next().remove()
		    			$elem.remove();
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}

}

$(document).on('click','.close_body',function(){
	$(this).parents('.body').hide();
});
$(document).on('click','.info-list, .new-teacher, .new-group',function(){
	$at = $(this).attr('at');
	$("#"+$at).slideToggle('fast');
});
$(document).on('click','.close-add-new-student',function(){
	$(this).parents('#new-student').hide();
});
$(document).on('click','.close-add-new-teacher',function(){
	$(this).parents('#new-teacher').hide();
});
$(document).on('click','.close-add-new-group',function(){
	$(this).parents('#new-group').hide();
});
$(document).on('click','.news',function(){
	$data_type = $(this).attr('data-type');
	if($data_type=='student'){
		$('.box-news .modal-header .modal-title').text('Студенттерге арналған жаңалықтар');
	}
	else if($data_type=='teacher'){
		$('.box-news .modal-header .modal-title').text('Мұғалімдерге арналған жаңалықтар');
	}
	$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>");
	$('.box-news .modal-body').load('index_news.php?data_type='+$data_type);
});

$(document).on('click','.single-student-news',function(){
	$data_num = $(this).data('num');
	$name = $(this).data('name');
	$('.box-news .modal-header .modal-title').html("Хабарлама.<br><b>"+$name+'.</b>');
	$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>");
	$('.box-news .modal-body').load('index_single_news.php?data_num='+$data_num);
});

$(document).on('click','.row-groups-info .btn',function(){
	if($(this).attr('btn-info')=='edit'){
		$(this).parents('.row-groups-info').find('.group-info').toggle();
		$(this).parents('.row-groups-info').find(".group-form").toggle();
	}
	if($(this).attr('btn-info')=='schedule'){
		$data_name = $(this).parents('.row-groups-info').find('form').find('input[name=group_name]').val();
		$data_num = $(this).parents('.row-groups-info').find('form').find('input[name=data_num]').val();
		$(".box-group-schedule .modal-header #group_name").text($data_name);
		$(".box-group-schedule .modal-body").text("Loading");
		$(".box-group-schedule .modal-body").load("load_group_schedule.php?data_num="+$data_num);
	}
});
$(document).on('click','.row-parents-info .btn',function(){
	if($(this).attr('type')!='submit'){
		$(this).parents('.row-parents-info').find('.parent-info').toggle();
		$(this).parents('.row-parents-info').find(".parent-form").toggle();
	}
});

// ----------------------------------------------------
$('.navigation').on('click',function(){
	if(!$(this).hasClass('active')){
		$('.navigation').removeClass('active');
		$(this).addClass('active');
		$attr = $(this).attr('data');
		$('.box').css('display',"none");
		$('.'+$attr).css('display','block');
		if($attr=='student'){
			$('.'+$attr).append("<p id='tmp'></p>");
			$('#tmp').load('admin_student.php');
			// $('.'+$attr).load('admin_student.php');
		}
		else {
			loadPageAdmin_($attr);
		}
	}
});
function loadPageAdmin_(attr){
	$('.'+attr).html("<center><h1>Loading...</h1></center>");
	$('.'+attr).load('admin_'+attr+'.php');
}
$(document).on('click','.sub_navigation',function(){
	console.log("ok"+$(this).attr('data'));
	if(!$(this).hasClass('active')){
		$('.sub_navigation').removeClass('active');
		$(this).addClass('active');
		$attr = $(this).attr('data');
		$('.progress_result_box').hide();
		$('.'+$attr).show();
		loadPageProgressResult_($attr);
	}
});
function loadPageProgressResult_(attr){
	$("."+attr).html("<center><h1>Loading...</h1></center>");
	$("."+attr).load("progress_result_"+attr+".php");
}
function hide(objHide){
	$(function(){
		// console.log(objHide+" --function hide(obj)");
		$(objHide).css('display','none');
	});
}
$(document).on('click','.reset_password',function(){
	$val = $(this).next().val();
	$this = $(this);
	$data_name = $(this).attr('data-name');
	$a = '';
	if($data_name=='student'){
		$a = '12345';
		$goTo = "<?php echo md5(md5('resetThisStudent'))?>";
	}
	else if ($data_name == 'teacher'){
		$a = '123456';
		$goTo = "<?php echo md5(md5('resetThisTeacher'))?>";
	}
	// console.log($data_name);

	var formData = {
		'action':"reset",
		'reset' : $val
	};
	// if($data_name=='teacher'){
		if(confirm("Пароль поменяется на '"+$a+"'. Подтвердите действие?")){
			$.ajax({
				type 		: 'POST',
				url 		: 'reset.php?'+$goTo, 
				data 		: formData, 
				cache		: false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
					console.log(dataS);
					data = $.parseJSON(dataS);
					console.log(data);
					if(data.success){
						// $this.parents('.user_info').addClass('pull-right');
						$this.parents('.user_info').find('table tr td').last().html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
			    		// $this.parents('.user_info').html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
			    	}
			    	else{
			    		console.log(data);
			    	}
				}
			});
		}
	// }
});
$(document).ready(function(){
	$(document).on('submit','#create_student',(function(e) {
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createStudent'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
				// console.log("asdf");
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.students').load('index_students.php');
		    		document.getElementById("create_student").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-teacher',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createTeacher'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.teachers').load('index_teachers.php');
		    		document.getElementById("create-teacher").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#create-group',(function(e) {
		thisParent = $(this);
		// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
		e.preventDefault();
		// $tmp = $(this).find('input[name=number_of_answers]').val();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('createGroup'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $('.students').html(data.text);
		    		$('.groups').load('index_groups.php');
		    		document.getElementById("create-group").reset();
		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});
$(document).ready(function(){
	$(document).on('submit','#schedule-form',(function(e) {
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('schedule'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		// $(".box-group-schedule").css('display','none').removeClass('in');
		    		// $(".modal-backdrop").remove();
		    		// $('body').removeAttr("class");
		    		// $('body').removeAttr('style');
					$(".box-group-schedule").modal('hide');
		    		$("#alert").html('<div class="alert alert-success alert-dismissible" role="alert" style="position: fixed; z-index: 10000; top:5%; width: 80%; left:10%; box-shadow: 0px 0px 10px green;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><center><strong>'+$(".box-group-schedule #group_name").text()+'</strong> группасының сабақ кестесі өзгерді</center></div>');

		    	}
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
});

$(document).on('submit','#form-single-student-news',(function(e) {
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('singelStudentNews'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$data_num = $this.find('input[name=data_num]').val();
	    		$("#delete_single_student_news").show();
	    		$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>").load('index_single_news.php?data_num='+$data_num);
				$('.box-news .modal-body').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
				if(data.context=='notEmpty'){
					$('.single-student-news').each(function(){
						if($(this).data('num')==$data_num){
							$(this).find('span').css('color',"orange");
						}
						else{
							console.log("fail");
						}
					});
				}
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
}));

$(document).on("click",'#delete-single-student-news',function(){
	$data_num = $(this).parent().find('input[name=data_num]').val();
	if(confirm("Вы точно хотите удалить?")){
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('removeSingelStudentNews'))?>&data_num="+$data_num,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		$("#delete_single_student_news").show();
		    		$('.box-news .modal-body').html("<center><h3>Loading...</h3></center>").load('index_single_news.php?data_num='+$data_num);
					$('.box-news .modal-body').stop().css({'background-color':"#EC9923"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
					$('.single-student-news').each(function(){
						if($(this).data('num')==$data_num){
							$(this).find('span').css('color',"black");
						}
						else{
							console.log("fail");
						}
					});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}
});

$(document).on('keyup','#search',function(){
	$data_name = $(this).attr('data-name');
	$val = $(this).val();
	$val = $val.replace(" ","_");
	if($data_name=='student'){
		$('.students').load('index_students.php?search='+$val);
	}
	else if($data_name=='teacher'){
		$('.teachers').load('index_teachers.php?search='+$val);
	}
	else if($data_name=='group'){
		$('.groups').load('index_groups.php?search='+$val);
	}
	else if($data_name=='parent'){
		$('.parents').load('index_parents.php?search='+$val);
	}
});
$(document).on("click",'.data-list',function(){
	$(this).parent().next().slideToggle('fast');
});
// --------------------------------------------modal-group-schedule-start-----------------------------------
$(document).on('click','.schedules .btn-week',function(){
	$week_id = $(this).attr('week-id');
	if($(this).hasClass('active')){
		$(this).removeClass('active');
		$('.schedules .hidden-datas').find('input[value='+$week_id+']').remove();
	}
	else{
		$(this).addClass('active');
		$('.schedules .hidden-datas').append('<input type="hidden" name="week_id[]" value="'+$week_id+'">');
	}
});
// --------------------------------------------modal-group-schedule-end-------------------------------------
// --------------------------------------------START-SUGGESTION-MODAL---------------------------------------
<?php if(isset($_SESSION['role']) & $_SESSION['role']==md5('admin')){ ?>
$(document).on('click','.btn-suggestion',function(){
	$('.box-suggestion .modal-header .modal-title').html("<center><b>Ұсыныстар</b></center>");
	$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
	$('.box-suggestion .modal-body').load('load_suggestion.php');
});
$(document).on('change','.suggestion-checkbox',function(){
	if($(this).parents('tr').hasClass('select-box')){
		$(this).parents('tr').removeClass('select-box');
	}
	else {
		$(this).parents('tr').addClass('select-box');
	}
});
$(document).on('submit','#suggestion-wating-form',function(e){
	// #5CB85C --- green
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('acceptSuggestions'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$('.select-box').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},200,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','.btn-suggestion-waiting-reject',function(){
	$arr = [];
	$("#suggestion-wating-form .select-box").each(function(){
		$arr.push($(this).find('input[name="sid[]"]').val());
	});
	var formData = {
		'sid':$arr,
	};
	console.log(formData);
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('rejectSuggestions'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$('#suggestion-wating-form .select-box').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('submit','#suggestion-accepted-form',function(e){
	// #5CB85C --- green
	$this = $(this);
	e.preventDefault();
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('implementSuggestions'))?>",
		type: "POST",
		data:  new FormData(this),
		contentType: false,
	    cache: false,
		processData:false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$('#suggestion-accepted-form .select-box').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},200,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','.btn-suggestion-accepted-reject',function(){
	$arr = [];
	$("#suggestion-accepted-form .select-box").each(function(){
		$arr.push($(this).find('input[name="sid[]"]').val());
	});
	var formData = {
		'sid':$arr,
	};
	console.log(formData);
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('rejectSuggestions'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$('#suggestion-accepted-form .select-box').stop().css({'background-color':"#d9534f"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
    				// $(".box-suggestion").modal('hide');
    				$('.box-suggestion .modal-body').html("<h3>Loading...</h3>");
					$('.box-suggestion .modal-body').load('load_suggestion.php');
    			});
	    	} 
	    	else{
	    		console.log(data);
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	        
   	});
});

$(document).on('click','#implementedSuggestion',function(){
	$('#implementedSuggestionBox').fadeToggle();
});
<?php } ?>
// --------------------------------------------END-SUGGESTION-MODAL-----------------------------------------

function newsValidation(){
	console.log('asdf');
	var ext = $('#news_img').val();
	console.log(ext);
	if(ext!=''){
		$img_size = $('#news_img')[0].files[0].size;
		console.log($img_size);
		ext = ext.split('.').pop().toLowerCase();
		console.log(ext);
		if($.inArray(ext, ['gif','png','jpg','jpeg','GIF','PNG','JPG','JPEG']) == -1) {
	    	alert('Не правильный формат картинки. Доступный форматы : ".jpg , .png , .jpeg , .gif, .JPG , .PNG , .JPEG , .GIF"');
	    	return false;
		}
		else if($img_size>=1572864){
			alert('Ошибка! Максимальный размер изображении 1.5MБ ~ (1572864 байт). Размер загруженного изображения = '+$img_size+' байт.');
			return false;
		}
		// else if(confirm("Подтвердите действие!")){
		// 	return true;
		// }
		else {
			return false;
		}
	}
	// else if(confirm("Подтвердите действие!")){
	// 	return true;
	// }
	else{
		return true;
	}
}
$(document).on('click','#remove_img',function(){
	if(confirm("Вы точно хотите удалить изображение?")){
		$(this).parents('.form-group').find('input[name=uploaded_photo]').val('');
		$(this).parents("b").html('<p class="text-danger">Изображение удалено!</p>');
	}
});
$(document).on('click','.open-access',function(){
	$data_num = $(this).data('num');
	$data_block = $(this).data('block');
	$this = $(this);
	var formData = {
		'block':$data_block,
		'sn':$data_num
	};
	$.ajax({
    	url: "ajaxDb.php?<?php echo md5(md5('openAccess'))?>",
		type: "POST",
		data:  formData,
	    cache: false,
		beforeSend:function(){
			$('#lll').css('display','block');
		},
		success: function(dataS){
			$('#lll').css('display','none');
	    	// console.log(dataS);
	    	data = $.parseJSON(dataS);
	    	// console.log(data);
	    	if(data.success){
	    		$this.parents('tr').css({'border':'2px solid red'});
	    		$this.parents('tr').find('.warned').html('<b style="color:#f00;">Ескертілген</b>');
	    		$this.remove();
	    	} 
	    	else{
	    		console.log(data);
	    		alert("Ошибка!");
	    	}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    		alert("Ошибка!");
    	} 	        
   	});
});

// -------------------------------------------------------------START-BOX-ABS-REASON--------------------------------------------------------------------------------
$(document).on('click','.abs-reason',function(){
	$('.box-abs-reason .modal-header .modal-title').html("<center><b>Сабаққа келмеген кездегі себептер.</b></center>");
	$('.box-abs-reason .modal-body').html("<h3>Loading...</h3>");
	$('.box-abs-reason .modal-body').load('load_reason.php');
});
// -------------------------------------------------------------END-BOX-ABS-REASON---------------------------------------------------------------------------------- 


// ------------------------------------------------------------------START-SUGGESTION-INPUT-------------------------------------------------------------------------
<?php if(isset($_SESSION['role']) && $_SESSION['role']==md5('moderator')){ ?>
	$(document).on('click','.suggestion',function(){
		$('.box-suggestion .modal-header .modal-title').html("<center><b>Ұсыныс</b></center>");
		$(".box-suggestion .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box-suggestion .modal-body").load("load_suggestion_input.php");
	});

	$(document).on('click','#suggestion, #suggestion-cancel',function(){
		$('#suggestion').toggle();
		$("#suggestion-form").toggle();
	});

	$(document).on('submit','#suggestion-form',function(e){
		$this = $(this);
		e.preventDefault();
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('add-new-suggestion'))?>",
	    	type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		$this.stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    		$(".box-suggestion").modal('hide');		
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	$(document).on('click','.suggestion-edit, .suggestion-edit-cancel',function(){
		$(this).parents('tr').find('.suggestion-text').toggle();
		$(this).parents('tr').find('.suggestion-form-edit').toggle();
	});

	$(document).on('submit','.suggestion-form-edit',function(e){
		$this = $(this);
		e.preventDefault(e);
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('edit-suggestion'))?>",
	    	type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$(".box-suggestion").modal('hide');	
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
	$(document).on('click','.suggestion-delete',function(){
		$this = $(this);
		$sid = $this.parents('tr').find('input[name=sid]').val();
		$.ajax({
	    	url: "ajaxDb.php?<?php echo md5(md5('remove-suggestion'))?>&sid="+$sid,
			cache : false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				$('#lll').css('display','none');
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		$this.parents('tr').stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
		    			$(this).remove();
		    		});
		    	} 
		    	else{
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
<?php } ?>
// ------------------------------------------------------------------END-SUGGESTION-INPUT---------------------------------------------------------------------------
// ------------------------------------------------------------------START-STUDENT-FORM-----------------------------------------------------------------------------
	$(document).on('click','.student-modal',function(){
		$action = $(this).data('action');
		console.log($action);
		if($action=='new-student'){
			$('.box-student-form .modal-title').html('<center><h1>Жаңа оқушы енгізу</h1></center>');
			$('.box-student-form .modal-body').html("<center><h3>Loading...</h3></center>");
			$('.box-student-form .modal-body').load('student_form.php?action=new_student');
		}
		if($action=='view'){
			$data_num = $(this).data('num');
			$('.box-student-form .modal-title').html('<center><h1>Оқушы</h1></center>');
			$('.box-student-form .modal-body').html("<center><h3>Loading...</h3></center>");
			$('.box-student-form .modal-body').load('student_form.php?action=view&data_num='+$data_num);
		}
	});

	// $timer = '';
	$access = [0,0,0,0]; // [student-username, student-phone, parent-phone-1, parent-phone-2]

	function checkAccessibility(i, v, t){
		console.log(t.prop('defaultValue'));
		if(i == "student-username"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccd','box-shadow':'none'});
	    		t.parents('.form-group').find('.student-username').html("");
	    		studentSaveButton(0,0,true);
			}
			else if(v.indexOf(".") >= 0){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('studentUsername'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						$(".check-required").not(t).addClass('disabled');
						studentSaveButton("","",false);
					},
					success: function(dataS){
						$(".check-required").removeClass("disabled");
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// // console.log(data);
				    	if(data.success && data.count==1){
				    		t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				    		t.parents('.form-group').find('.student-username').html('"<b>username</b>" уже существует!');
				    		studentSaveButton(0, 2, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.student-username').html("");
				    		studentSaveButton(0,0,true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.student-username').html('Формат:"аты.тегі"');
				studentSaveButton(0, 1, true);
			}
		}
		else if(i == "student-phone"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.student-phone').html("");
	    		studentSaveButton(1, 0, true);
			}
			else if(v.length==10){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('studentPhone'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						$(".check-required").not(t).addClass('disabled');
						studentSaveButton('','',false);
					},
					success: function(dataS){
						$(".check-required").removeClass("disabled");
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// // console.log(data);
				    	if(data.success && data.count==1){
				    		t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				    		t.parents('.form-group').find('.student-phone').html('<b>Номер</b> уже существует!');
				    		studentSaveButton(1, 2, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.student-phone').html("");
				    		studentSaveButton(1, 0, true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.student-phone').html('Длина: 10');
				studentSaveButton(1, 1, true);
			}
		}
		else if(i == "parent-phone-1" || i == "parent-phone-2"){
			if(t.prop('defaultValue') == t.val()){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.'+i).html("");
	    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
			}
			else if(v.length==10){
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('parentPhone'))?>&value="+v,
					cache : false,
					beforeSend:function(){
						studentSaveButton('','',false);
					},
					success: function(dataS){
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// // console.log(data);
				    	if(data.success && data.count>=1){
				    		t.css({'border-color':'orange','box-shadow':'0px 0px 10px orange'});
				    		$html = "<p style='margin:0;'><i><b>Номер</b> уже существует!</i></p>";
				    		$parent_num = "";
				    		$.each(data.data, function(key, value){
				    			if(data.data[key]['parent_num']!=$parent_num){
				    				$parent_num = data.data[key]['parent_num'];
				    				$html += "<br><p style='margin:0;'><b>Ата-ана:</b> "+data.data[key]['parent_surname']+" "+data.data[key]['parent_name']+"</p>";
				    			}
				    			$html += "<p style='margin:0;'><b>- Студент:</b> "+data.data[key]['student_surname']+" "+data.data[key]['student_name']+"</p>";
				    		});
				    		t.parents('.form-group').find('.'+i).html($html);
				    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
				    	} 
				    	else if(data.success && data.count==0){
				    		t.css({'border-color':'green','box-shadow':'0px 0px 5px green'});
				    		t.parents('.form-group').find('.'+i).html("");
				    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
			else if(v.length==0){
				t.css({'border-color':'#ccc','box-shadow':'none'});
	    		t.parents('.form-group').find('.'+i).html("");
	    		studentSaveButton(i=="parent-phone-1" ? 2 : 3, 0, true);
			}
			else{
				t.css({'border-color':'red','box-shadow':'0px 0px 10px red'});
				t.parents('.form-group').find('.'+i).html('Длина: 10');
				studentSaveButton(i=="parent-phone-1" ? 2 : 3, 1, true);
			}
		}
	}

	$(document).on("keyup", ".check-for-existing", function(){
		// console.log($(this));
		checkAccessibility($(this).attr('id'), $(this).val(), $(this), false);
	});
	function studentSaveButton(i, v, torf){
		$e = $('.student-form').find('.student-save-btn');
		if(torf){
			$e.val('Сақтау');
			$access[i] = v;
			if($access.every(checkSame)){
				switch(v){
					case 0:
						$e.removeClass('disabled');
						break;
					case 1:
						if(!$e.hasClass('disabled')) $e.addClass('disabled');
						$e.val("Сақтау");
						break;
					case 2:
						if(!$e.hasClass('disabled')) $e.addClass('disabled');
						$e.val("Сақтау");
						break;
				}
			}
		}
		else{
			$e.addClass("disabled").val("Loading...");
		}
		// console.log($access);
		// console.log('studentSaveButton');
	}
	function checkSame(v){
		return v==0;
	}

	$(document).on('submit','.student-form',function(e){
		$this = $(this);
		// console.log($this);
		// console.log($this.attr('class'));
		e.preventDefault(e);
		formData = new FormData(this);
		$def = [];
		$elem = $(this).find('.check-for-existing');
		$go = 0;
		$getPath = '';
		if($this.find('input[type=submit]').attr('name')=='new-student'){
			$getPath = '<?php echo md5(md5('addNewStudent'))?>';
		}
		else if($this.find('input[type=submit]').attr('name')=='edit-student'){
			$getPath = '<?php echo md5(md5('editStudent'))?>'
		}
		if($access.every(checkSame) && $getPath!=''){	
			$.ajax({
		    	url: "ajaxDb.php?"+$getPath,
		    	type: "POST",
				data:  new FormData(this),
				contentType: false,
	    	    cache: false,
				processData:false,
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function(dataS){
					$('#lll').css('display','none');
			    	// console.log(dataS);
			    	data = $.parseJSON(dataS);
			    	// console.log(data);
			    	if(data.success){
			    		$('.students').load('index_students.php');
			    		$this.parent().stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500,function(){
			    			$(".box-student-form").modal('hide');	
			    		});
			    	} 
			    	else{
			    		console.log(data);
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		console.log(dataS);
		    	} 	        
		   	});
		}
	});
	$(document).on('click','.student-edit-btn',function(){
		$('.student-form').find('input').each(function(key, value){
			if(value.hasAttribute('disabled')){
				$(this).removeAttr('disabled');
			}
		});
		$(this).parents('.btns').find('input[type=submit]').show();
		$(this).parents('.btns').find('input[type=reset]').show();
		$(this).hide();
	});
	$(document).on('click','.student-form .btns input[type=reset]', function(){
		$('.student-form').find('input').each(function(key,value){
			$(this).attr('disabled','disabled');
		});
		$('.student-form').find('input[type=submit]').hide();
		$('.student-edit-btn').show();
		$(".check-for-existing").css({'border-color':'#ccc','box-shadow':'none'});
		$(this).hide();
	});
// ------------------------------------------------------------------END-STUDENT-FORM-------------------------------------------------------------------------------
	
	$(document).on('change','.search_type',function(){
		// window.location.replace(window.location.pathname+"?search_type="+$(this).val());
		$search_type = $(this).val();
		$('.students').html("<h3>Loading...</h3>");
		if($search_type=='default' || $search_type == ''){
			$('.students').load("index_students.php");
		}
		else {
			$('.students').load("index_students_selective_search.php?search_type="+$search_type);
		}
		$('.search_box').html("<select class='form-control pull-right' style='width:20%;'><option>Loading...</option></select>");
		$('.search_box').load("load_search_box.php?search_type="+$search_type);
	});

	$(document).on('change','.select_search',function(){
		$search_type=$(this).attr('search-type');
		$search_attr=$(this).val();
		$('.students').html("<h3>Loading...</h3>");
		$('.students').load("index_students_selective_search.php?search_type="+$search_type+"&search_attr="+$search_attr);
	});

	$(document).on('click','.progress_result_trial_test_action_btn',function(){
		$this = $(this).parents('tr').next();
		$(this).parents("tr").next().toggle();
		if($(this).attr('data-clicked')=='f' && $(this).attr('data-clicked')!='t'){
			console.log($(this).attr('data-clicked'));
			$(this).attr('data-clicked','t')
			$data = $this.find('p').text();
			$data_name = $this.find('p').data('name');
			console.log($data_name.replace(' ', '_'));
			$this.find('.progress_result_more_info').html("<center><h3>Loading...</h3></center>");
			$this.find('.progress_result_more_info').load('progress_result_trial_test_list_ajax.php?data='+$data+"&data_name="+$data_name);
		}
	});
	$(document).on('click','.progress_result_quiz_action_btn', function(){
		$(this).parents('tr').next().toggle();
	});
	$(document).on('change','.trial_test_search_order_type',function(){
		$search_attr_subject = $('.trial_test_select_search_subject').val();
		$search_attr_school = $('.trial_test_select_search_school').val();
		$search_order_type = $(this).val();
		$('.progress_result_trial_test_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_trial_test_container').load("progress_result_trial_test_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.trial_test_select_search_subject',function(){
		$search_attr_subject = $(this).val();
		$search_attr_school = $('.trial_test_select_search_school').val();
		$search_order_type = $('.trial_test_search_order_type').val();
		$('.progress_result_trial_test_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_trial_test_container').load("progress_result_trial_test_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.trial_test_select_search_school',function(){
		$search_attr_school = $(this).val();
		$search_attr_subject = $('.trial_test_select_search_subject').val();
		$search_order_type = $('.trial_test_search_order_type').val();
		$('.progress_result_trial_test_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_trial_test_container').load("progress_result_trial_test_list.php?search_attr_school="+$search_attr_school+"&search_attr_subject="+$search_attr_subject+"&search_order_type="+$search_order_type);
	});

	$(document).on('change','.quiz_search_order_type',function(){
		$search_attr_subject = $('.quiz_select_search_subject').val();
		$search_attr_school = $('.quiz_select_search_school').val();
		$search_order_type = $(this).val();
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.quiz_select_search_subject',function(){
		$search_attr_subject = $(this).val();
		$search_attr_school = $('.quiz_select_search_school').val();
		$search_order_type = $('.quiz_search_order_type').val();
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_subject="+$search_attr_subject+"&search_attr_school="+$search_attr_school+"&search_order_type="+$search_order_type);
	});
	$(document).on('change','.quiz_select_search_school',function(){
		$search_attr_school = $(this).val();
		$search_attr_subject = $('.quiz_select_search_subject').val();
		$search_order_type = $('.quiz_search_order_type').val();
		$('.progress_result_quiz_container').html("<center><h3>Loading...</h3></center>");
		$('.progress_result_quiz_container').load("progress_result_quiz_list.php?search_attr_school="+$search_attr_school+"&search_attr_subject="+$search_attr_subject+"&search_order_type="+$search_order_type);
	});
</script>
</body>
<?php $_SESSION['load_page'] = false; ?>
</html>
