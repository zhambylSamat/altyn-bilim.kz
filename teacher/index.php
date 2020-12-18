<?php 
	include('../connection.php');
	if(!isset($_SESSION['teacher_num'])){
		header('location:signin.php');
	}
	$result_groups = array();
	$week = array("", "Дүйсенбі", "Сейсенбі", "Сәрсенбі", "Бейсенбі", "Жұма", "Сенбі", "Жексенбі");
	try {
		$stmt = $conn->prepare("SELECT gi.group_info_num, 
									gi.group_name, 
									TIME_FORMAT(gi.start_lesson, '%H:%i') start_lesson,
									TIME_FORMAT(gi.finish_lesson, '%H:%i') finish_lesson,
									s.subject_name,
									(SELECT count(s.student_num) 
									FROM group_student gs, 
										student s 
									WHERE gs.group_info_num = gi.group_info_num 
										AND gs.start_date <= CURDATE()
										AND s.student_num = gs.student_num 
										AND s.block != 1
										AND s.block != 6
										AND gs.block != 6 ) student_quantity 
								FROM group_info gi, 
									teacher t, 
									subject s 
								WHERE gi.teacher_num=t.teacher_num 
									AND t.teacher_num = :teacher_num 
									AND gi.subject_num = s.subject_num 
									AND gi.block != 6
								ORDER BY gi.group_name ASC");
		// (CASE WHEN (WEEKDAY(CURDATE())+1 IN (SELECT sch.week_id 
  //                                       	FROM schedule sch 
  //                                       	WHERE sch.group_info_num = gi.group_info_num))
  //                                       AND (SELECT CURRENT_TIME) BETWEEN (SELECT SUBTIME(gi.start_lesson, '00:30:00')) AND (SELECT ADDTIME(gi.finish_lesson, '00:30:00'))
		// 						    THEN
		// 						    	'true'
		// 						    ELSE
		// 						     	'false'
		// 						    END) AS lesson,
		$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_groups = $stmt->fetchAll();


	    $stmt = $conn->prepare("SELECT gi.group_info_num, 
	    							sch.week_id
	    						FROM group_info gi,
	    							schedule sch
	    						WHERE gi.block != 6
	    							AND sch.group_info_num = gi.group_info_num
	    							AND gi.teacher_num = :teacher_num
	    						ORDER BY sch.group_info_num ASC, sch.week_id ASC");
	    $stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group_schedule = array();
	    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	    	if(array_key_exists($row['group_info_num'], $result_group_schedule)){
	    		array_push($result_group_schedule[$row['group_info_num']], $row['week_id']);
	    	}
	    	else{
	    		$result_group_schedule[$row['group_info_num']] = array($row['week_id']);
	    	}
	    }

	    for ($i = 0; $i<count($result_groups); $i++) {
	    	if(array_key_exists($result_groups[$i]['group_info_num'], $result_group_schedule)){

	    		$week_txt = "";
	    		foreach ($result_group_schedule[$result_groups[$i]['group_info_num']] as $key => $value) {
	    			$week_txt .= $week[$value].", ";
	    		}
	    		$week_txt = substr($week_txt, 0, strlen($week_txt)-2);
	    		$result_groups[$i]["schedule"] = $week_txt;
	    		array_push($result_groups[$i], $week_txt);
	    	}
	    	else{
	    		$result_groups[$i]["schedule"] = "N/A";
	    		array_push($result_groups[$i], "N/A");
	    	}
	    }


	    $active_groups = array();

	   	$stmt = $conn->prepare("SELECT gi.group_info_num
	   							FROM group_info gi,
	   								subject sj
	   							WHERE gi.teacher_num = :teacher_num
   									AND gi.block != 6
	   								AND (SELECT CURRENT_TIME) 
	   									BETWEEN 
	   										(SELECT SUBTIME(gi.start_lesson, '00:30:00')) 
	   										AND (SELECT ADDTIME(gi.finish_lesson, '00:30:00'))
	   								AND (WEEKDAY(CURDATE())+1 
											IN (SELECT sch.week_id 
                                    			FROM schedule sch 
                                    			WHERE sch.group_info_num = gi.group_info_num))
                                    AND sj.subject_num = gi.subject_num
                                ORDER BY sj.id");
	   	$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
	   	$stmt->execute();
	   	$active_groups = $stmt->fetchAll();



	   	$current_day = intval(date('d'));
		$start_day = 25;
		$end_day = 10;
		$start_date = "";
		$end_date = "";
		$is_active_period = false;

		if ($current_day >= $start_day) {
			$start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
			$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
			$is_active_period = true;
		} else if ($current_day <= $end_day) {
			$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
			$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
			$is_active_period = true;
		}
		$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));


		$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi2.group_info_num
                                    	AND tr2.student_num = gs2.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
	   	$stmt = $conn->prepare("SELECT s.surname,
	   								s.name,
	   								(SELECT count(sp.id)
									FROM student_poll sp
									WHERE sp.student_num = s.student_num
										AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
										AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')) AS is_polled,
									(SELECT count(DISTINCT gi2.teacher_num)
										FROM group_student gs2,
											group_info gi2
										WHERE gs2.student_num = s.student_num
											AND gs2.block != 6
											AND gi2.subject_num != 'S5985a7ea3d0ae721486338'
											AND gi2.group_info_num = gs2.group_info_num
											AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs2.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')) AS active_teacher_polls
	   							FROM student s,
	   								group_info gi,
	   								group_student gs
	   							WHERE gi.teacher_num = :teacher_num
   									AND gs.group_info_num = gi.group_info_num
   									AND s.student_num = gs.student_num
   									AND s.student_num != 'US5985cba14b8d3100168809'
   									AND s.block != 6
   									AND gs.block != 6
   									AND gi.block != 6
	   							GROUP BY s.student_num
	   							ORDER BY s.surname, s.name");
	   	$stmt->bindParam(":teacher_num", $_SESSION['teacher_num'], PDO::PARAM_STR);
	   	$stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
		$stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
		$stmt->bindParam(":poll_activate_days", $poll_activate_days, PDO::PARAM_STR);
	   	$stmt->execute();
	   	$result = $stmt->fetchAll();

	   	$stmt = $conn->prepare("SELECT count(id) AS poll_info_count FROM teacher_poll_info");
		$stmt->execute();
	    $total_poll_number = $stmt->fetch(PDO::FETCH_ASSOC)['poll_info_count'];

	    $not_polled_students = array();

	    if ($total_poll_number > 0 && $is_active_period) {
	    	foreach ($result as $value) {
	    		if ($value['active_teacher_polls'] != 0 && $value['is_polled'] < $value['active_teacher_polls']) {
	    			$tmp = array("surname" => $value['surname'], "name" => $value['name']);
	    			array_push($not_polled_students, $tmp);
	    		}
	    	}
	    }

	    $stmt = $conn->prepare("SELECT AVG(sps.mark) AS avg_mark
							    FROM student_poll sp,
							        student_polls sps
							    WHERE sp.teacher_num = :teacher_num
							        AND sps.student_poll_id = sp.id
							        AND sp.polled_date >= STR_TO_DATE((SELECT (
							                                    CASE
							                                        WHEN DATE_FORMAT(sp2.polled_date, '%d') <= 10 THEN DATE_FORMAT(DATE_SUB(sp2.polled_date, INTERVAL 1 MONTH), '25-%m-%Y')
							                                        WHEN DATE_FORMAT(sp2.polled_date, '%d') >= 25 THEN DATE_FORMAT(sp2.polled_date, '25-%m-%Y')
							                                    END
							                                ) AS month
							                                FROM student_poll sp2
							                                WHERE sp2.teacher_num = :teacher_num
							                                ORDER BY month DESC
							                                LIMIT 1), '%d-%m-%Y')
							                             ");
			    $stmt->bindParam(":teacher_num", $_SESSION['teacher_num'], PDO::PARAM_STR);
			    $stmt->execute();
			    $avg_mark = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_mark'], 2); 

	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Мұғалім | Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
<?php include_once('nav.php');?>
<section id='body'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<?php include_once("notifications.php"); ?>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div class="btn-group" role="group" style='margin-bottom: 20px;'>
					<button class='btn btn-primary suggestion' type='button' data-toggle='modal' data-target='.box-suggestion'>Ұсыныс</button>
					<?php if(isset($_SESSION['news_notificaiton_teacher']) ){?>
					<button class='btn btn-primary news' type='button' data-toggle='modal' data-target='.box-news'>Жаңалықтар</button>
					<?php }?>
					<button class='btn btn-primary problem-solution' type='button' data-toggle='modal' data-target='.box'>Есеп</button>
					<button class='btn btn-info teacher-poll-result' data-toggle='modal' data-target='.box-universal' data-num="<?php echo $_SESSION['teacher_num'];?>">Опрос: <?php echo $avg_mark; ?></button>
				</div>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12' style='margin-bottom: 30px;'>
				<div class='btn-group-vertical' role='group'>
					<?php if (count($not_polled_students) > 0) { ?>
					<button style='text-align: left !important;' data-toggle='modal' data-target='#not-polled-students' class='btn btn-danger'>
						Сауалнаманы толтырмаған оқушылар
						<span class='badge'><?php echo count($not_polled_students); ?></span>
					</button>
					<?php } ?>
				</div>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<ul class='nav nav-tabs' style='margin-bottom:20px;'>
					<li role="presentation" class="navigation active" data='student'>
						<a href="#">
							Оқушылар
						</a>
					</li>
					<li role="presentation" class="navigation" data='group'>
						<a href="#">
							Группалар
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<section id='student' class='nav-content'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center><h3><b>Сабақ кестесі бойынша қазыргі уақытқа сәйкес оқушылар тізімі</b></h3></center>
				<?php
					foreach ($active_groups as $value) {
						$group_info_num = $value['group_info_num'];
						$is_phone = false;
						$is_comment = true;
						include("getStudentsByGroupNum.php");
					}
				?>
			</div>
		</div>
	</div>
</section>
<section id='group' class='nav-content' style='display: none;'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12' style='overflow-x: scroll;'>
				<table class='table table-striped table-bordered'>
					<tr>
						<th colspan='4'><center>Мұғалім: <?php echo $_SESSION['teacher_name']." ".$_SESSION['teacher_surname'];?></center></th>
					</tr>
					<tr>
						<th style='width: 25%;'><center>Группа</center></th>
						<th style='width: 25%;'><center>Пән</center></th>
						<th style='width: 25%;'><center>Оқушылар саны</center></th>
						<th style='width: 25%;'><center>Сабақ кестесі</center></th>
					</tr>
					<?php 
						foreach ($result_groups as $value) {
					?>
					<tr>
						<td>
							<center>
								<a style='display: block;' href="group.php?<?php echo 'data_num='.$value['group_info_num'];?>">
									<?php echo $value['group_name'];?>
								</a>
							</center>
						</td>
						<td><center><?php echo $value['subject_name'] ; ?></center></td>
						<td><center><?php echo $value['student_quantity'];?></center></td>
						<td><center><?php echo "<b>".$value['schedule']."</b>"."<br>".$value['start_lesson']."-".$value['finish_lesson'];?></center></td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<hr>
		<div class='teacher-schedule' style='overflow-x: scroll;'>
			<?php
				$teacher_num = $_SESSION['teacher_num'];
				include_once('schedule.php'); 
			?>
		</div>
	</div>
</section>

<?php
	try {
		$stmt = $conn->prepare("SELECT DISTINCT s.student_num,
									s.surname,
									s.name,
								    s.block,
								    s.block_date
								FROM group_info gi,
									group_student gs,
								    student s
								WHERE gi.teacher_num = :teacher_num
									AND gs.group_info_num = gi.group_info_num
									AND gs.block != 6
								    AND s.student_num = gs.student_num
								    AND s.block IN (2, 3, 4, 5)
								ORDER BY s.block, s.student_num");
		$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
		$stmt->execute();
		foreach ($stmt->fetchAll() as $value) {
			$text = "";
			if ($value['block']==2 || ($value['block']==3 && date("Y-m-d")!=date('Y-m-d',strtotime($value['block_date'])))) {
				$_SESSION['notification'][$value['student_num']]['text'] = 'Оплатасы жоқ!';
				$_SESSION['notification'][$value['student_num']]['name'] = $value['surname']." ".$value['name'];
			} else if ($value['block']==4 || ($value['block']==5 && date("Y-m-d")!=date('Y-m-d',strtotime($value['block_date'])))) {
				$_SESSION['notification'][$value['student_num']]['text'] = 'Договор өткізбеген!';
				$_SESSION['notification'][$value['student_num']]['name'] = $value['surname']." ".$value['name'];
			}
		}
	} catch (PDOException $e) {
		throw $e;
		
	}
?>
<?php if (isset($_SESSION['notification']) && count($_SESSION['notification']) > 0){ ?>
<div class="modal fade box-student-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Маңызды ақпарат!</h3></center>
    	</div>
    	<div class="modal-body">
			<?php
				echo "<div style='margin-left:10%;'>";
				foreach ($_SESSION['notification'] as $value) {
					echo "<span style='font-size:25px;'>".$value['name'].": </span>";
					if (isset($value['text'])) {
						echo "<b style='color: red; padding-left:5px; font-size:20px;'>".$value['text']."</b>";
					}
					if (isset($value['count']) && $value['count']>0){
						echo "<b style='color:green; padding-left:5px; font-size:20px;'>Шоколад: (".$value['count'].")</b>";
					}
					echo "<br>";
				}
				echo "</div>";
			?>
    	</div> 
    </div>
  </div>
</div>
<?php } ?>
<!-- <a class='btn btn-sm btn-info news' data-toggle='modal' data-target='.box-news' data-type='student'>Жаңалықтар</a> -->
<div class="modal fade box-news" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Жаңалықтар</h3></center>
    	</div>
    	<div class="modal-body">
			<div class='row news-label'>
			<?php if(isset($_SESSION['news_res_teacher']['header']) && $_SESSION['news_res_teacher']['header']!=''){?>
			<div class="col-md-12 col-sm-12 col-xs-12 header">
				<center>
					<div class='news-header' style='background-color: #AFDFF7; padding:1% 0 1% 0;'>
						<h3><b><?php echo $_SESSION['news_res_teacher']['header'];?></b></h3>
					</div>
				</center>
			</div>
			<?php }?>
			<?php if(isset($_SESSION['news_res_teacher']['content']) && $_SESSION['news_res_teacher']['content']!=''){?>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div class='news-content'>
					<pre class='pre-news'><?php echo nl2br($_SESSION['news_res_teacher']['content']);?></pre>
				</div>
			</div>
			<?php }?>
			<?php if(isset($_SESSION['news_res_teacher']['img']) && $_SESSION['news_res_teacher']['img']!=''){?>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<img src="../news_img/<?php echo $_SESSION['news_res_teacher']['img'];?>" alt="teacher-image" class="img-thumbnail img-responsive">
				</center>
			</div>
			<?php } ?>
		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-trial-test-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Пробный тесттің бағалары қойылмаған оқушылар</h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<?php
    				$trial_test_notification = $_SESSION['trial_test_notification'];
    				$group_info_num = '';
    				$count = 1;
    				foreach ($trial_test_notification as $key => $value) {
    			?>
    				<?php if ($value['group_info_num'] != $group_info_num) { 
    					$group_info_num = $value['group_info_num'];
    					$count = 1;
    				?>
    				<hr>
    				<center><h4><?php echo $value['group_name']." | ".$value['subject_name']."<br>"; ?></h4></center>
    				<?php } ?>
    				<center><?php echo "<b>".($count++).")</b> ".$value['surname']." ".$value['name']."<br>"; ?></center>
    			<?php }?>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-suggestion" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<br>
    		<center><h3 class="modal-title">Ұсыныс</h3></center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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
<div class="modal fade box-comment-for-teacher" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center>
    			<h3></h3>
    		</center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade" id='not-polled-students' tabindex='-1' role='dialog'>
	<div class='modal-dialog modal-md' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden="true">X</span></button>
				<center>
					<h3>Сауалнаманы толтырмаған оқушылар тізімі</h3>
				</center>
			</div>
			<div class='modal-body'>
				<table class='table table-bordered table-striped'>
					<?php
						$count = 0;
						foreach ($not_polled_students as $value) {
							echo "<tr><td style='width: 10%;'><center>".(++$count)."</center></td><td>".$value['surname']." ".$value['name']."</td></tr>";
						}
					?>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade box-universal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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

<div id='lll'>
	<center>
		<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
	</center>
</div>
<?php 
// include_once('js.php');
?>
<?php
	if(isset($_SESSION['news_notificaiton_teacher']) && $_SESSION['news_notificaiton_teacher']=='true'){
		$_SESSION['news_notificaiton_teacher'] = 'false';
		echo '<script type="text/javascript">$(document).ready(function(){$(".box-news").modal("show");});</script>';
	}

	// if (isset($_SESSION['trial_test_notification_show']) && $_SESSION['trial_test_notification_show']=='true') {
	// 	// $_SESSION['trial_test_notification_show'] = 'false';
	// 	echo '<script type="text/javascript">$(document).ready(function(){$(".box-trial-test-notification").modal("show");});</script>';
	// }
?>
<script type="text/javascript">

	$(document).ready(function(){
		$("#lll").css('display','none');

		$(".box-student-notification").modal('show');
	});

	$(document).on('click', '.navigation', function(){
		$content_name = $(this).attr('data');
		$('.navigation').removeClass('active');
		$(this).addClass('active');
		$('.nav-content').hide();
		$('#'+$content_name).show();
	});

	$(function(){
		$('#lll').hide().ajaxStart( function() {
			$(this).css('display','block');  // show Loading Div
		} ).ajaxStop ( function(){
			$(this).css('display','none'); // hide loading div
		});
	});

	$worker = new Worker("js/alert_timer.js");
	$time_arr = [];
	$(document).ready(function(){
		$.ajax({
	    	url: "load_schedule_time.php",
			cache : false,
			success: function(dataS){
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	if(data.success){
		    		$time_arr = data.data;
		    		// console.log($time_arr);
					timeNotification();
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log("ERROR: ");
	    		console.log(dataS);
	    	} 	        
	   	});
	});

	function timeNotification(){
		if(typeof(Worker) !== "undefined"){
			// for ($i = 0; $i < $time_arr.length; $i++) {
				$i = 0;
				if($time_arr.length > 0 && $time_arr[$i]!=""){
					$worker.postMessage($time_arr[$i]);
					$time_arr[$i] = "";
					// console.log($time_arr[$i]);
					// break;
				}
			// }
		}
	}

	$worker.onmessage = function(e){
		if(e.data=='show'){
		$(".box .modal-title").html("<center><h2><b>Ұмытпа!</b></h2></center>");
		$(".box .modal-body").html("<center><h3>1. Оқушыға қол қоюға журналды бер!<br>2. Пробный тесттен жинаған балдарды жазып ал!<br>3. Порталдағы журналды белгіле, бағаларын қой!</h3></center>");
		$(".box").modal(e.data);
		// timeNotification();
		}
	}
	
	$(document).on('click','.suggestion',function(){
		$(".box-suggestion .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box-suggestion .modal-body").load("load_suggestion.php");
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

	$(document).on('click','.problem-solution', function(){
		$(".box .modal-title").html("<center><h3>Есеп</h3></center>");
		$(".box .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box .modal-body").load("load_problem_solution.php");
	});

	$(document).on('click','.subject-solution-list',function(){
		$data_num = $(this).data('num');
		$(".box .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box .modal-body").load("load_problem_solution.php?back&<?php echo md5('sn'); ?>="+$data_num);
	});

	$(document).on('click','#back-to-subject-list',function(){
		$(".box .modal-body").html("<center><h3>Loading...</h3></center>");
		$(".box .modal-body").load("load_problem_solution.php");
	});

	$(document).on('click','#implementedSuggestion',function(){
		$('#implementedSuggestionBox').fadeToggle();
	});

	$(document).on('click','.topic-name',function(){
		$(this).next().slideToggle('fast');
	});



	// --------------------------------------------------------------------------STUDENTS_ACTION_START-----------------------------------------------------
	$(document).on('click','.reset_password',function(){
		$val = $(this).next().val();
		$this = $(this);
		$data_name = $(this).attr('data-name');
		$a = '';
		if($data_name=='student'){
			$a = '12345';
			$goTo = "<?php echo md5(md5('resetThisStudent'))?>";
		}

		var formData = {
			'action':"reset",
			'reset' : $val
		};
		if(confirm("Пароль поменяется на '12345'. Подтвердите действие?")){
			$.ajax({
				type 		: 'POST',
				url 		: 'reset-pwd.php?'+$goTo, 
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
						// $this.parents('.password').addClass('pull-right');
			    		$this.parents('.password').html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на '"+$a+"'</h5>");
			    	}
			    	else{
			    		console.log(data);
			    	}
				}
			});
		}
	});
	$(document).on('click','.header-student',function(){
		$data_name  = $(this).attr('data-name');
		$data_num = $(this).attr('data-num');
		$subject_num = $(this).attr('data-subject');
		$data_load  = $(this).attr('data-load');
		$thisParent = $(this).parents('.head-student');
		if($data_load=='n'){
			$thisParent.after('<tr class="body-student" style="cursor:pointer; border:1px solid lightgray; padding:2px 20px; border-top:none"><td cospan="5">Loading...</td></div>');
			$thisParent.next().load("student_permission.php?data_num="+$data_num+"&status="+$data_name+"&extra_num="+$subject_num);
			$(this).attr('data-load','y')
		}
		else if($data_load=='y'){
			$thisParent.next().slideToggle();
		}
	});
	$(document).on('click','.topic_name, .subtopic',function(){
		$(this).next().slideToggle('fast');
	});
	$(document).ready(function(){
		$(document).on('submit','#set_permission',(function(e) {
			$thisParent = $(this);
			// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
			e.preventDefault();
			// $tmp = $(this).find('input[name=number_of_answers]').val();
			$.ajax({
	        	url: "ajaxDb.php?<?php echo md5(md5('set_permission'))?>",
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
			    		$thisParent.stop();
			    		if(data.text=="noVideo"){
			    			$thisParent.append('<b style="color:red;">Видео енгізілмеген</b>');
			    			$thisParent.stop();
			    			$thisParent.css({'background-color':"#EC9923"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000,function(){
			    				$thisParent.find("b").slideUp(500,function(){$(this).remove()});
			    			});
			    		}
			    		else{
			    			$thisParent.append('');
			    			$thisParent.stop();
			    			$thisParent.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000,function(){
			    				$thisParent.find("b").slideUp(500,function(){$(this).remove()});
			    			});
			    		}
			    	}
			    	else{
			    		$thisParent.stop();
			    		$thisParent.css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
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
	$(document).on('click','.check-timer',(function(e) {
		$thisParent = $(this).parents('form');
		$id = $(this).attr('id');
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('check_timer'))?>&id="+$id,
			type: "GET",
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

					$thisParent.stop();
		    		if(data.text=="noVideo"){
		    			$thisParent.append('<b style="color:red;">Видео енгізілмеген</b>');
		    			$thisParent.stop();
		    			$thisParent.css({'background-color':"#EC9923"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000,function(){
		    				$thisParent.find("b").slideUp(500,function(){$(this).remove()});
		    			});
		    		}
		    		else{
		    			$thisParent.append('<b style="color:green;">'+data.timer+'</b>');
		    			$thisParent.stop();
		    			$thisParent.css({'background-color':"#FFB800"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},3000,function(){
		    				$thisParent.find("b").slideUp(500,function(){$(this).remove()});
		    			});
		    		}
		    	}
		    	else{
		    		$thisParent.stop();
		    		$thisParent.css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
		    		console.log(data);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
	// ----------------------------------------------------------------------------STUDENTS_ACTION_END---------------------------------------------------------
	// ----------------------------------------------------------------------------SET_COMMENT_START-----------------------------------------------------------
	$(document).on('click','.set-comment',function(){
		$id = $(this).parents('tr').attr('id');
		$gsn = $(this).parents('.head-student').find('input[name=gsn]').val();
		$student_name = $(this).parents('.head-student').find('input[name=student_name]').val();
		$group_info_num = $(this).attr('data-num');
		$subject_num = $(this).attr('subject-num');
		$('.box-comment-for-teacher .modal-header h3').text($student_name);
		$('.box-comment-for-teacher .modal-body').text("Loading...");
		$('.box-comment-for-teacher .modal-body').load('load_comment.php?gsn='+$gsn+"&data_num="+$group_info_num+"&id="+$id+"&sj_num="+$subject_num);
	});

	$(document).on('submit','#box-comment',(function(e) {
		$this = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('submit_review_for_student'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
		    	console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	console.log(data);
		    	$elem = $this.parents('#box-comment');
		    	if(data.success){
		    		$elem = $("#"+$this.find('input[name=id]').val());
		    		$this.stop();
		    		$this.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'}, 500, function(){
		    			$(".box-comment-for-teacher").modal("hide");
		    			$elem.removeClass('warning');
		    			$elem.find('.helper').remove('.helper');
		    			console.log($elem);
		    		});
		    	}
		    	else{
		    		$this.stop();
		    		$this.css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
		    		console.log(data);
		    	}
		    	$('#lll').css('display','none');
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	}));
	// ------------------------------------------------------------------------------SET_COMMENT_END-------------------------------------------------------------
	$(document).on('click', '.teacher-poll-result', function(){
		$teacher_num = $(this).attr("data-num");
		$('.box-universal .modal-header .modal-title').text("Сауалнама қорытындысы");
		$('.box-universal .modal-body').html("<center><h3>Загрузка...</h3></center>");
		$('.box-universal .modal-body').load('../ab_admin/teacher_poll_result.php?teacher_num='+$teacher_num);
	});
	$(document).on('change', '.teacher-poll-result-date', function(){
		$date = $(this).val();
		$teacher_num = $(this).attr('data-num');
		$('#teacher-poll-result').html('<center><h3>Загрузка...</h3></center>');
		$('#teacher-poll-result').load('../ab_admin/teacher_poll_result_ajax.php?date='+$date+'&teacher_num='+$teacher_num);
	});
	// -------------------------------------------------------TEACHER-POLL-RESULT-END-------------------------------------------------------------
</script>
</body>
</html>