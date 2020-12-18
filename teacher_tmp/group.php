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
	if(!isset($_GET['data_num'])){
		header('location:index.php');
	}

	$lesson = isset($_GET[md5('lesson')]) ? $_GET[md5('lesson')] : md5('false');

	$result_group_info = array();
	$result_students_group = array();
	$result_queue_student = array();
	$students_group_count = 0;
	$_SESSION['tmp_group_info_num'] = '';;
	$subject_num = '';
	try {
		$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name subject_name, gi.group_name group_name, gi.comment comment FROM subject s, group_info gi WHERE gi.group_info_num = :group_info_num AND s.subject_num = gi.subject_num");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group_info = $stmt->fetch(PDO::FETCH_ASSOC); 
	    $subject_num = $result_group_info['subject_num'];
	    $_SESSION['tmp_group_info_num'] = $_GET['data_num'];

	    $stmt = $conn->prepare("SELECT gs.group_student_num group_student_num, 
	    							s.student_num student_num, 
	    							s.name name, 
	    							s.surname surname, 
	    							s.password_type password_type, 
	    							s.username username,
	    							s.block block,
	    							(select count(group_student_num) from review r where r.group_student_num = gs.group_student_num) - (select count(review_info_num) from review_info ri where ri.description != 'comment') as c 
	    						FROM student s, 
	    							group_student gs 
	    						WHERE gs.student_num = s.student_num
	    							AND gs.start_date <= CURDATE() 
	    							AND gs.group_info_num = :group_info_num 
	    							AND s.block != 1 
	    							AND gs.block !=6
	    						ORDER BY surname, name ASC");
	    $stmt->bindParam(":group_info_num", $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_students_group = $stmt->fetchAll();
	    $students_group_count = $stmt->rowCount();

	    $stmt = $conn->prepare("SELECT DATE_FORMAT(gs.start_date, '%d.%m.%Y') as start_date,
	    							s.name,
	    							s.surname
	    						FROM student s,
	    							group_student gs 
	    						WHERE gs.student_num = s.student_num
	    							AND gs.start_date > CURDATE() 
	    							AND gs.group_info_num = :group_info_num
	    						ORDER BY surname, name ASC");
	   	$stmt->bindParam(":group_info_num", $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_queue_student = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Группа | Altyn Bilim</title>
	<?php include_once('style.php');?>
	<style type="text/css">
		.head-student:hover{
			background-color: #eee;
		}
		.table-progress .odd-tr{
			background-color: white;
		}
		.table-progress .even-tr{
			background-color: #eee;
		}
		.wrong-format{
			border:2px solid red;
			box-shadow: 0px 0px 5px red;
		}
	</style>
</head>
<body>
<?php 
	if($as_admin == $true){
		include_once('../admin/nav.php');
	}
	else{
		include_once('nav.php');
	}
?>
<?php 
// include_once('js.php');
?>
<script type="text/javascript">
	$required_progress = {};
</script>
<div id='group-body'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<ul class="nav nav-tabs">
				 	<?php if($as_admin == $false){ ?>
				 	<li role="presentation" class="navigation active" data='students'><a href="#">Студенттер</a></li>
				 	<?php } ?>
				 	<li role="presentation" class='navigation <?php echo $as_admin == $true ? "active" : "" ; ?>' data='mark'><a href="#">Баға қою</a></li>
				</ul>
				<br>
			</div>
		</div>
	</div>
	<?php if($as_admin == $false){ ?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<div class='students box' style='overflow-x: scroll;'>
					<table class='table table-striped table-bordered'>
						<tr>
							<th style='width: 25%;'>
								Группа: <?php echo $result_group_info['group_name'];?>
							</th>
							<th style='width: 25%;'>
								Мұғалім: <?php echo $_SESSION['teacher_name']." ".$_SESSION['teacher_surname'];?>
							</th>
							<th style='width: 25%;'>
								Пән: <?php echo $result_group_info['subject_name'];?>
							</th>
							<th style='width: 25%;'>
								Түсініктеме: <br><?php echo $result_group_info['comment'];?>
							</th>
						</tr>
						<tr>
							<td colspan='4'>
								<b>Студенттер:</b>
								<table class='table'>
								<?php
									if($students_group_count==0){
										echo "N/A";
									}
									else{
										$count = 1;
										foreach ($result_students_group as $value) {
								?>
									<tr id='<?php echo $count;?>_tr' class='head-student <?php echo ($value['c']!=0 && $subject_num!='S5985a7ea3d0ae721486338' && $value['student_num'] != 'US5985cba14b8d3100168809') ? "warning" : "";?>' style='border:1px solid lightgray; border-bottom:none; cursor: pointer;'>
										<!-- <div class='row'> -->
											<td>
												<?php echo ($value['block']==2) ? "<p class='helper'><b style='color:red;'>Оплатасы жоқ</b></p>" : "" ;?>
												<?php echo ($value['c']!=0 && $subject_num!='S5985a7ea3d0ae721486338' && $value['student_num'] != 'US5985cba14b8d3100168809') ? "<p class='helper'><b style='color:red;'>Коммент жоқ</b></p>" : "" ;?>
												<span><?php echo ($count++).") ";?></span>
												<a class='header-student' data-load='n' data-name='student_single' data-num='<?php echo $value['student_num'];?>'><?php echo $value['surname']." ".$value['name']; ?></a>
												&nbsp;
												<a href="../parent/student_info.php?data_num=<?php echo $value['student_num'];?>&user=<?php echo md5('tch');?>" target="_blank">[анкетасы]</a>
											</td>
											<td>
												Login: <b><?php echo $value['username'];?></b>
											</td>
											<td class='hidden-datas' style='display:none;'>
												<input type="hidden" name="sn" value='<?php echo $value['student_num']?>'>
												<input type="hidden" name="gsn" gsn='<?php echo $value['group_student_num'];?>' value='<?php echo $value['group_student_num'];?>'>
												<input type="hidden" name="student_name" value='<?php echo $value['surname']." ".$value['name'];?>'>
											</td>
											<td>
												<!-- <button class='btn btn-info btn-xs reset_password' data-name='student'>Сбросить пароль</button>
												<input type="hidden" name="reset" value='<?php echo $value['student_num']?>'> -->
												<div class='password' style='display:inline-block;'>
													<h5 style='display: inline-block;'>Пароль: </h5>
													<?php if($value['password_type']!='default'){?>
													<button class='btn btn-info btn-xs reset_password' data-name='student' style='display: inline-block;'>Сбросить пароль</button>
													<input type="hidden" name="reset" value='<?php echo $value['student_num']?>'>
													<?php }else{?>
													<span><b><u><i>'12345'</i></u></b></span>
													<?php }?>
												</div>
											<!-- </td>						
											<td class='comment-for-students'> -->
												<?php if($result_group_info['subject_num']!='S5985a7ea3d0ae721486338'){ ?>
												<a class='btn btn-success btn-xs set-comment' data-toggle='modal' data-target='.box-comment-for-teacher'>
													<!-- <span class='glyphicon glyphicon-th-list'></span> -->
													Коммент
												</a>
												<?php } ?>
											</td>
										<!-- </div> -->
									</tr>
								<?php
										}
									}
								?>
								</table>
							</td>
						</tr>
						<tr>
							<hr>
							<?php if(!empty($result_queue_student)){ ?>
							<td colspan='4'>
								<b>Курсқа жақында келетін студенттер.</b>
								<table class='table'>
									<?php 
										$count = 1;
										foreach ($result_queue_student as $value) {
									?>
									<tr>
										<td><?php echo ($count++).") ".$value['surname']." ".$value['name'];?></td>
										<td>Курсқа келетін уақыты: <?php echo $value['start_date'];?></td>
									</tr>
									<?php } ?>
								</table>
							</td>
							<?php } ?>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } //class .container .students end ?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<div class='mark box' style='background-color: white; <?php echo $as_admin == $true ? "display:block !important;" : "" ; ;?>'>
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
									<p>Түсініктеме: <b><?php echo $result_group_info['comment'];?></b></p>
								</td>
							</tr>
						</table>
					</center>
					<?php
						$month = array("","Қаңтар","Ақпан","Наурыз","Сәуір","Мамыр","Мусым","Шілде","Тамыз","Қыркүйек","Қазан","Қараша","Желтоқсан");
						try {
							$stmt = $conn->prepare("SELECT DISTINCT DATE_FORMAT(created_date,'%Y-%m') as month FROM progress_group WHERE group_info_num = :group_info_num ORDER BY month ASC");
							$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
						    $stmt->execute();
						    $count = $stmt->rowCount();
						} catch (PDOException $e) {
							echo "Error ".$e->getMessage()." !!!";
						}
					?>
					<center>
						<div class='month'>
						<?php
							$i = 0;
							$current_month_n = date('Y-m');
							while($result_month = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$class = (++$i==$count) ? 'btn-warning' : 'btn-default';
								$current_month_n = $result_month['month'];
								$current_month_s = $month[intval(explode("-",$result_month['month'])[1])];
								$next_month_s = $month[intval(explode("-",$result_month['month'])[1])];
						?>
						<button class='btn btn-sm <?php echo $class;?> month_for_marks exists-month' date-number="<?php echo $current_month_n;?>" date-text = "<?php echo $current_month_s;?>"><?php echo $current_month_s; ?></button>
						<?php } ?>
						<?php
							if(intval(date('m'))!=intval(substr($current_month_n,5,2)) || $i==0){
						?>
						<button class='btn btn-sm btn-default month_for_marks' data-name='new' date-number="<?php echo date('Y-m');?>" date-text="<?php echo $month[intval(date('m'))];?>"><?php echo $month[intval(date('m'))]; ?></button>
						<?php }?>
						</div>
					</center>
					<div class='outer'>
						<div class='inner marks'>
							<?php
								try {
									$stmt = $conn->prepare("SELECT count(*) c FROM group_student WHERE group_info_num = :group_info_num");
									$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
						    		$stmt->execute();
								} catch (PDOException $e) {
									echo "Error ".$e->getMessage()." !!!";
								}
							?>
							<?php 
								if($count!=0 && $stmt->fetch(PDO::FETCH_ASSOC)!=0){
									// $edit_datas = 'true'; 
									include_once('mark.php'); 
								}
							?>
						</div>
					</div>
					<hr>
					<div class='quiz'>
						<div class='row'>
							<div class='col-md-6 col-sm-6'>
								<table class='table table-bordered table-striped'>
									<tr>
										<th><center>Аралық бақылау</center></th>
									</tr>
									<tr>
										<th><center>Тақырыптардың атауы</center></th>
									</tr>
									<?php
										$topic_list = array();
										try {
											$stmt = $conn->prepare("SELECT t.topic_num topic_num, t.topic_name topic_name FROM topic t, subject s WHERE s.subject_num = t.subject_num AND s.subject_num = :subject_num AND t.quiz = 'y' ORDER BY t.topic_order ASC");
											$stmt->bindParam(':subject_num', $result_group_info['subject_num'], PDO::PARAM_STR);
											$stmt->execute();
											$topic_list = $stmt->fetchAll();
										} catch (PDOException $e) {
											echo "Error ".$e->getMessage()." !!!";
										}
										foreach ($topic_list as $value) {
									?>
									<tr>
										<td>
											<center><a href="quiz_result.php?t_num=<?php echo $value['topic_num'];?>&data_num=<?php echo $_GET['data_num'];?>" target="_blank"><?php echo nl2br($value['topic_name']);?></a></center>
										</td>
									</tr>
									<?php } ?>
								</table>
							</div>
							<div class='col-md-6 col-sm-6'>
								<?php
									$group_info_num = $_GET['data_num'];
									$subject_name = $result_group_info['subject_name'];
									include_once('trial_test.php');
								?>
							</div>
						</div>
					</div>
				</div>
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
<div class="modal fade box-list-student-progress" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center>
    			<h3></h3>
    		</center>
    	</div>
    	<div class="modal-body" style="overflow-x: scroll;">
    	</div> 
    </div>
  </div>
</div>
<div class="modal fade box-time-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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
<script type="text/javascript">

	// ----------------------------review-start--------------------------------
	$(document).on('click','.set-comment',function(){
		$id = $(this).parents('tr').attr('id');
		$gsn = $(this).parents('.head-student').find('input[name=gsn]').val();
		$student_name = $(this).parents('.head-student').find('input[name=student_name]').val();
		$('.box-comment-for-teacher .modal-header h3').text($student_name);
		$('.box-comment-for-teacher .modal-body').text("Loading...");
		$('.box-comment-for-teacher .modal-body').load('load_comment.php?gsn='+$gsn+"&data_num=<?php echo $_GET['data_num'];?>&id="+$id);
	});
	// ----------------------------review-end----------------------------------

	$(document).on("click",'.mark-table .edit-marks, .mark-table .cancel-edit-marks',function(){
		$col_number = $(this).attr('col-number');
		$("#col_num").val($col_number);
		$(this).parents('.mark-table').find('tr').each(function(){
			$(this).find("td[data="+$col_number+"]").each(function(){
				$(this).find('.last-data').toggle();
				$(this).find(".new-data").toggle();
			});
		});
		if($(this).hasClass('edit-marks')){
			$required_progress_changed_count = 0;
			$.each($required_progress,function(index, el) {
				if(el['changed']==false && el['att']==1){
					$required_progress_changed_count++;
				}
			});
			if($required_progress_changed_count==0){
				$('#save').show();
				$('#reset').show();
				$('#progress').show();
			}
			else {
				$('#progress').show();
			}
			$(this).hide();
		}
		else if($(this).hasClass('cancel-edit-marks')){
			$('.mark-table .edit-marks').show();
			$('#progress').hide();
			$('#save').hide();
			$('#reset').hide();
			$(this).hide();
		}
		// $(this).parent().find('.btn').toggle();
	});
	$(document).on('click',".month_for_marks",function(){
		$('.month_for_marks').removeClass('btn-warning').addClass('btn-default');
		$(this).addClass('btn-warning');
		$date_number = $(this).attr('date-number');
		$date_text = $(this).attr("date-text");
		if($(this).attr('data-name')=='new'){
			$('.marks').html("<h3>Loading...</h3>");
			$('.marks').load("mark.php?date_number="+$date_number+"&date_text="+$date_text+"&data_num=<?php echo $_GET['data_num'];?>&new_marks");
		}
		else{
			$('.marks').html("<h3>Loading...</h3>");
			$('.marks').load("mark.php?date_number="+$date_number+"&date_text="+$date_text+"&data_num=<?php echo $_GET['data_num'];?>");
		}
	});
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
	$('.navigation').on('click',function(){
		if(!$(this).hasClass('active')){
			$('.navigation').removeClass('active');
			$(this).addClass('active');
			$attr = $(this).attr('data');
			$('.box').css('display',"none");
			$('.'+$attr).css('display','block');
		}
	});
	// --------------------------------
	$(document).on('click','.header-student',function(){
		$data_name  = $(this).attr('data-name');
		$data_num = $(this).attr('data-num');
		$data_load  = $(this).attr('data-load');
		$thisParent = $(this).parents('.head-student');
		if($data_load=='n'){
			$thisParent.after('<tr class="body-student" style="cursor:pointer; border:1px solid lightgray; padding:2px 20px; border-top:none"><td cospan="5">Loading...</td></div>');
			$thisParent.next().load("student_permission.php?data_num="+$data_num+"&status="+$data_name+"&extra_num=<?php echo $result_group_info['subject_num'];?>&<?php echo md5('lesson').'='.$lesson;?>");
			$(this).attr('data-load','y')
		}
		else if($data_load=='y'){
			$thisParent.next().slideToggle();
		}
	});
	$(document).on('click','.topic_name, .subtopic',function(){
		$(this).next().slideToggle('fast');
	});

	$(document).on('click','.big-attendance-sign',function(){
		$data_sign = $(this).attr('data-sign');
		$row_count = $(this).parent().find('p[id=row_count]').attr('row_count');
		$std_num = $('#'+$row_count).find("input[name='datas[]']").val();
		$name = $('#'+$row_count).text();
		if($required_progress[$std_num]==undefined){
			$required_progress[$std_num] = {'changed' : false, 'att' : 0, 'name' : $name};
		}
		if($data_sign=='plus'){
			// delete $required_progress[$std_num];
			$required_progress[$std_num]['att'] = 0;
			$(this).parent().find('input').val(0);
			$(this).parent().find(".minus-attendance").show();
			$(this).hide();
		}
		else{
			$required_progress[$std_num]['att'] = 1;
			$(this).parent().find('input').val(1);	
			$(this).parent().find(".plus-attendance").show();
			$(this).hide();
		}
		$required_progress_changed_count = 0;
		$.each($required_progress,function(index, el) {
			if(el['changed']==false && el['att']==1){
				$required_progress_changed_count++;
			}
		});
		if($required_progress_changed_count==0){
			// $('#progress').hide();
			$('#save').show();
			$('#reset').show();
		}
		else {
			$('#progress').show();
			$('#save').hide();
			$('#reset').hide();
		}
		// if(Object.keys($required_progress).length==0){
		// 	$('#progress').hide();
		// 	$('#save').show();
		// 	$('#reset').show();
		// }
		// else{
		// 	$('#progress').show();
		// 	$('#save').hide();
		// 	$('#reset').hide();
		// }
	});
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

	// -----------------------Start_Ajax----------------------
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
			    			$thisParent.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
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
	// -----------------------End_Ajax------------------------


	// ------------------------------TRIAL-TEST-START---------------------------------------------------
	$(document).on('click','.add-trial-test-mark',function(){
		$(this).hide('fast');
		$(this).parents('td').find('form').show('fast');
	});
	$(document).on('click','.reset-trial-test',function(){
		$(this).parents('form').hide('fast');
		$(this).parents('td').find('.add-trial-test-mark').show('fast');
	});
	$(document).on('focus','.datePicker',function(){
		$(this).datepicker({
			format: 'dd.mm.yyyy'
		});
	});

	$(document).on('submit','#trial_test_form',(function(e) {
		$thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('add_trial_test_mark'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(dataS){
				
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// // console.log(data);
		    	$elem = $thisParent.parent();
		    	if(data.success){
		    		$data_num = $thisParent.find('input[name=stdn]').val();
		    		$sjn = $thisParent.find('input[name=sjn]').val();
		    		$sn = $thisParent.find('input[name=sn]').val();
		    		$elem.html("<center>Loading...</center>");
		    		$elem.load("load_ajax_trial_test_mark.php?data_num="+$data_num+"&sjn="+$sjn+"&sn="+$sn,  function( response, status, xhr ){
		    			if(status=="error"){
		    				$elem.parent().stop();
		    				$elem.parent().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
		    			}
		    			else if(status=='success'){
		    				$elem.parent().stop();
		    				$elem.parent().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
		    			}
		    		});
		    	}
		    	else{
		    		$elem.parent().stop();
		    		$elem.parent().css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
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


	// ------------------------------TRIAL-TEST-END-----------------------------------------------------
	// ------------------------------START-STUDENT-PROGRESS--------------------------------------------
	//  ----------------------------START_STATIC_ALGEBRA_&_GEOMETRY---------------------------------------------------
	$(document).on('click','.static-subject-btn',function(){
		console.log($(this));
		$sn = $(this).data('sn');
		$sjn = $(this).data('sjn');
		$name = $(this).data('name').replace(/\s/g, "_");
		console.log("load_student_progress.php?sn="+$sn+"&sjn="+$sjn+"&name="+$name);
		$('.box-list-student-progress .modal-header h3').html("<center><b><?php echo ucwords($result_group_info['subject_name']);?></b> пәні бойынша тақырыптар тізмі.<br><b>Студент: "+$name+"</b></center>");
		$('.box-list-student-progress .modal-body').text("Loading...");
		$('.box-list-student-progress .modal-body').load("load_student_progress.php?sn="+$sn+"&sjn="+$sjn+"&name="+$name);	
	});
	//  ----------------------------END_STATIC_ALGEBRA_&_GEOMETRY-----------------------------------------------------
	$(document).on('click','#progress, .ok, .back',function(){
		// setAccess();
		if($(this).attr('data-num')){
			$required_progress[$(this).attr('data-num')]['changed'] = true;
		}
		$required_progress_changed_count = 0;
		$html_part = '';
		$html_part +='<ol class="breadcrumb"><li class="active">Студенттер тізмі</li></ol>';
		$html_part += '<table class="table table-bordered table-hover">';
		$.each($required_progress,function(index, el) {
			$class = '';
			$color = '';
			if(el['changed']==false && el['att']==1){
				$required_progress_changed_count++;
				$class = 'warning';
				$color = '#FFB564';
			}
			$html_part += '<tr>';
			$html_part += '<td class="'+$class+' single-student-progress" style="border: 2px solid '+$color+';" std_num="'+index+'" style="cursor:pointer"><a style="cursor:pointer">'+el['name']+'</a></td>';
			$html_part += '</tr>';
		});
		$html_part += '</table>';
		if($required_progress_changed_count==0){
			$('#save').show();
			$('#reset').show();
		}
		$('.box-list-student-progress .modal-body').html($html_part);
	});

	$(document).on('click','.single-student-progress',function(){
		$sn = $(this).attr('std_num');
		$name = $(this).text().replace(/\s/g, "_");
		$('.box-list-student-progress .modal-header h3').html("<center><b><?php echo ucwords($result_group_info['subject_name']);?></b> пәні бойынша тақырыптар тізмі.<br><b>Студент: "+$name+"</b></center>");
		$('.box-list-student-progress .modal-body').text("Loading...");
		$('.box-list-student-progress .modal-body').load("load_student_progress.php?sn="+$sn+"&sjn=<?php echo $subject_num;?>&name="+$name,function(response, status, xhr){
			// $(this).html(response);
		});
	});

	$(document).on('click','.st-lists',function(){
		$class = $(this).parents('.head-topic').attr('data-name');
		$("tr[data-name="+$class+"]").toggle();
	});
	$(document).on('click','.progress-btn',function(){
		$data_name = $(this).attr('data-name');
		$data_type = $(this).attr('data-type');
		// $inputElem = $(this).parents('tr').find('input[name=progress_'+$data_name+']');
		$inputElem = $(this).parent().find('input[name=progress_'+$data_name+']');
		if($data_type==0){
			$(this).attr('data-type','0.5');
			// $(this).text(0.5);
			$(this).val(0.5);
			$inputElem.val(0.5);
			$(this).removeClass('btn-danger').addClass('btn-warning');
		}
		else if($data_type==0.5){
			$(this).attr('data-type','1');
			// $(this).text(1);
			$(this).val(1);
			$inputElem.val(1);
			$(this).removeClass('btn-warning').addClass('btn-success');
		}
		else if($data_type==1){
			$(this).attr('data-type','0');
			// $(this).text(0);
			$(this).val(0);
			$inputElem.val(0);
			$(this).removeClass('btn-success').addClass('btn-danger');
		}
	});

	


	$(document).on('submit','.student_progress_list',(function(e) {
		$this = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('student_progress'))?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend:function(){
				$('#lll').css('display','block');
				$('.save-btn-disable').attr('disabled','disabled');
				console.log('save-btn-disable-start');
			},
			success: function(dataS){
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
		    	$elem = $this.parents('.body-topic');
		    	if(data.success){
		    		// $elem.html(data.html);
		    		$elem.find('input[name=id]').val(data.id);
		    		$this.find('input[type=submit]').hide();
		    		$this.find('input[type=image]').show();
		    		$elem.stop();
		    		$elem.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},500);
		    		if($required_progress[data.stdNum]){
		    			$required_progress[data.stdNum]['changed'] = true;
		    		}
		    		$this.find('input[type=submit]').show();
		    		$this.find('input[type=image]').hide();
		    		$('.save-btn-disable').removeAttr('disabled');
		    		console.log('save-btn-disable-end');
		    	}
		    	else{
		    		$elem.stop();
		    		$elem.css({'background-color':"#D9534F"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},2000);
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
	// ------------------------------END-STUDENT-PROGRESS-----------------------------------------------

	//-------------------------------START-COMMENT-FOR-TEACHER-------------------------------------------
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
		    	// console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	// console.log(data);
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
	//-------------------------------END-COMMENT-FOR-TEACHER--------------------------------------------- 
	<?php if($as_admin == $false){?>
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
		    		console.log($time_arr);
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
					console.log($time_arr[$i]);
					// break;
				}
			// }
		}
	}

	$worker.onmessage = function(e){
		if(e.data=='show'){
		$(".box-time-notification .modal-title").html("<center><h2><b>Ұмытпа!</b></h2></center>");
		$(".box-time-notification .modal-body").html("<center><h3>1. Оқушыға қол қоюға журналды бер!<br>2. Пробный тесттен жинаған балдарды жазып ал!<br>3. Порталдағы журналды белгіле, бағаларын қой!</h3></center>");
		$(".box-time-notification").modal(e.data);
		// timeNotification();
		}
	}
	<?php } //workder js end?>
</script>
</body>
</html>