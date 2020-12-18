<?php
	include_once('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		eader('location:signin.php');
	}
	if(!isset($_GET['data_num'])){
		header('location:index.php');
	}
	$_SESSION['tmp_group_info_num'] = $_GET['data_num'];
	$result_group = array();
	$result_group_students = array();
	try {
		$stmt = $conn->prepare("SELECT gi.group_info_num, 
									gi.group_name, 
									gi.comment, 
									t.name, 
									t.surname, 
									s.subject_num, 
									s.subject_name 
								FROM group_info gi, 
									teacher t, 
									subject s 
								WHERE gi.group_info_num = :group_info_num 
									AND gi.teacher_num = t.teacher_num 
									AND gi.subject_num = s.subject_num");
		$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group = $stmt->fetch(PDO::FETCH_ASSOC);

	    $stmt = $conn->prepare("SELECT gs.group_student_num, 
	    							s.student_num, 
	    							s.name, 
	    							s.surname, 
	    							DATE_FORMAT(gs.start_date, '%d.%m.%Y') as start_date 
	    						FROM student s, 
	    							group_student gs 
	    						WHERE gs.student_num = s.student_num
	    							AND gs.block != 6 
	    							AND gs.group_info_num = :group_info_num 
	    							AND s.block != 6 
	    						ORDER BY s.surname ASC");
	    $stmt->bindParam(":group_info_num", $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_students_group = $stmt->fetchAll();
	    $students_group_count = $stmt->rowCount();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Admin - Group - Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet/less" type='text/css' href="css/style.less">
	<style type="text/css">
		.head-student:hover{
			background-color: #eee;
		}
	</style>
</head>
<body>
<?php if(isset($_SESSION['n']) && $_SESSION['n']=='true'){?>
<section id='alert' style='position:absolute; top:3%; z-index: 100; width: 50%; left:25%;'>
	<?php
		if(isset($_GET['transfer'])){
	?>
	<div class="alert alert-success alert-dismissible" role="alert" style='margin:0; box-shadow: 0px 0px 50px black;'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  		<center><strong>Студент "<?php echo $_GET['transfer'];?>" группасына ауысты!</strong></center>
	</div>
	<?php } ?>
	<?php $_SESSION['n'] = "false"; ?>
	<!-- <div class="alert alert-success alert-dismissible" role="alert" style='margin:0; box-shadow: 0px 0px 50px black;'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  		<strong>Warning!</strong> Better check yourself, you're not looking too good.
	</div> -->
</section>
<?php } ?>
<?php include_once('nav.php');?>
<section id='group-body'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12'>
				<ul class="nav nav-tabs">
				 	<li role="presentation" class="navigation active" data='students'><a href="#">Студенттер</a></li>
				 	<li role="presentation" class='navigation' data='marks'><a href="#">Баға</a></li>
				 	<!-- <li role="presentation" class='navigation' data='transfer'><a href="#">Трансфер</a></li> -->
				</ul>
				<br>
			</div>
		</div>
	</div>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-xs-12'>
				<div class='students box' style='display: block;'>
					<table class='table table-bordered table-striped'>
						<tr>
							<td style='width: 40%;'>
								<center><b>Мұғалім: </b><span class='h3'><u><?php echo $result_group['name']." ".$result_group['surname']; ?></u></span></center>
							</td>
							<td style='width: 60%;'>
								<center><b>Пән: </b><span class='h3'><u><?php echo $result_group['subject_name']; ?></u></span></center>
							</td>
						</tr>
						<tr>
							<td>
								Группа: 
								<b><?php echo $result_group['group_name']; ?></b>
							</td>
							<td>
								<b>Түсініктеме: </b><br>
								<?php echo $result_group['comment']; ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type="button" class='btn btn-xs btn-info add-students-btn' value='Студент енгізу'>
								<!-- <b class='pull-right'>Студенттер: </b> -->
								<br>
								<br>
								<form class="form-horizontal add-students" action='admin_controller.php' method='post' style='display: none;'>
									<input class='form-control' type="text" id='search' placeholder="Поиск..." autocomplete="off">
									<select class='form-control' name='students_to_group[]' multiple required="" id='students_list'>
										<?php include("group_student_search.php"); ?>
									</select>
									<br>
									<input type="hidden" name="data_num" value='<?php echo $_GET['data_num'];?>'>
									<center>
										<input type="submit" name="add_to_group" class='btn btn-sm btn-success'>
										<input type="reset" class="btn btn-sm btn-warning reset-add-students-btn">
									</center>
								</form>
							</td>
							<td>
								<b>Студенттер:</b>
								<?php
									if($students_group_count==0){
										echo "N/A";
									}
									else{
										$count = 1;
										foreach ($result_students_group as $value) {
								?>
									<div class='head-student' style='border:1px solid lightgray; border-bottom:none; cursor: pointer;'>
										<div style='display: inline-block; width:30%'>
											<span class='count'><?php echo ($count++);?></span><span>)</span>
											<a class='header-student object-full-name' data-load='n' data-name='student_single' data-num='<?php echo $value['student_num'];?>'><?php echo $value['surname']." ".$value['name']; ?></a>
										</div>
										<div style='display: inline-block;'>
											<!-- <form style='display: inline-block;' onsubmit='return confirm("Вы уверены что хотите убрать студента с группы?");' action='admin_controller.php' method='post'>
												<input type="hidden" name="data_num" value='<?php echo $value['student_num'];?>'>
												<input type="hidden" name="extra_num" value="<?php echo $_GET['data_num'];?>">
												<button title='Студентті группадан шағару!' type='submit' name='remove_from_group' class='btn btn-xs btn-danger'><span class='glyphicon glyphicon-trash'></span></button>
											</form> -->
											<a class='btn btn-xs btn-danger to_archive' data-name='student_group' data-num = "<?php echo $value['group_student_num']; ?>" title='Архивировать'>
												<span class='glyphicon glyphicon-save-file'></span>
											</a>
											<a title='Студентті басқа группаға ауыстыру!' data-num="<?php echo $value['student_num'];?>" data-name="<?php echo $value['surname']." ".$value['name']; ?>" class='btn btn-xs btn-info transfer-student'><span class='glyphicon glyphicon-retweet'></span></a>
											<a class="btn btn-xs btn-default list-progress" data-toggle='modal' data-target='.box-list-student-progress'>Тақырыбы</a>
											<span>&nbsp;<b>|</b>&nbsp;</span>
											<?php
												$start_date = intval(date('d', strtotime($value['start_date'])));
												// if($start_date>=intval(date('d'))){
												if(date("Y-m-d") <= date("Y-m-d", strtotime($value['start_date']))){
											?>
											<form style='display: inline-block;' method='post' id='start_lesson_form'>
												<input type="text" class='form-control datePicker_start_lesson' placeholder="dd.mm.yyyy" title='Студенттің сабағы басталатын уақыты' id='trial-date' name="start_lesson" required="" value="<?php echo $value['start_date'];?>" style='display:inline-block; width: 60%;'>
												<input type="submit" class='btn btn-success btn-xs' value='Сақтау'>
												<input type="hidden" name="gsNum" value='<?php echo $value['group_student_num'];?>'>
											</form>
											<?php } else{ ?>
											<span class='text-success'>Курс басталған күн: <b><?php echo $value['start_date'];?></b></span>
											<?php } ?>
										</div>
									</div>
								<?php
										}
									}
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div class='marks box'>
					<?php if(isset($_SESSION['role']) && $_SESSION['role'] == md5('admin') ){ ?>
					<a href='../teacher/group.php?data_num=<?php echo $_GET['data_num']?>' target='_blank' class='btn btn-xs btn-info'>Группаға мұғалім ретінде кіру</a>
					<?php } ?>
					<?php
						try {
							$stmt = $conn->prepare("SELECT gi.group_name group_name, gi.comment comment, t.name name, t.surname surname, s.subject_name subject_name FROM group_info gi, teacher t, subject s WHERE gi.group_info_num = :group_info_num AND gi.teacher_num = t.teacher_num AND gi.subject_num = s.subject_num");
							$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
						    $stmt->execute();
						    $result = $stmt->fetch(PDO::FETCH_ASSOC);
						} catch (PDOException $e) {
							echo "Error ".$e->getMessage()." !!!";
						}
					?>
					<center>
						<table style='width: 60%;'>
							<tr>
								<td style='width: 50%; padding:1% 0% 1% 1%; vertical-align: top;'>
									<p>Группа: <b><u><?php echo $result['group_name'];?></u></b></p>
								</td>
								<td style='width: 50%; padding:1% 1% 1% 0; '>
									<p>Мұғалім: <b><?php echo $result['name']." ".$result['surname'];?></b></p>
								</td>
							</tr>
							<tr>
								<td style='width: 50%; padding:1% 0% 1% 1%; vertical-align: top;'>
									<p>Пән: <b><?php echo $result['subject_name'];?></b></p>
								</td>
								<td style='width: 50%; padding:1% 1% 1% 0; '>
									<p>Түсініктеме: <b><?php echo $result['comment'];?></b></p>
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
						<!-- get original code from original_for_marks.php -->
						<?php
							$i = 0;
							$current_month_n = '';
							$current_month_s = '';
							while($result_month = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$class = (++$i==$count) ? 'btn-warning' : 'btn-default';
								$current_month_n = $result_month['month'];
								$current_month_s = $month[intval(explode("-",$result_month['month'])[1])];
						?>
							<button class='btn btn-sm <?php echo $class;?> month_for_marks exists-month' date-number="<?php echo $current_month_n;?>" date-text = "<?php echo $current_month_s;?>"><?php echo $current_month_s; ?></button>
							<?php } ?>
						</div>
					</center>
					<div class='outer'>
						<div class='inner mark-info'>
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
									include_once('admin-mark.php'); 
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
											$stmt->bindParam(':subject_num', $result_group['subject_num'], PDO::PARAM_STR);
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
									include_once('trial_test_info.php');
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
	<center>
		<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
	</center>
</div>
<div class='float-box outer'>
	<div class='inner'>
		<p class='close'>X</p>
		<form onsubmit='return confirm("Таңдаған студентті басқа группаға ауыстыру!");' action='admin_controller.php' method='post'>
			<center><h3 id='name'></h3></center>
			<hr>
			<div>
				<center>
					<span class='h4'><?php echo $result_group['group_name'];?></span>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<span class='glyphicon glyphicon-arrow-right'></span>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<?php
						$group_list_for_transfer = array();
						try {
							$stmt = $conn->prepare("SELECT group_info_num, group_name FROM group_info WHERE subject_num = :subject_num AND group_info_num != :group_info_num AND block != 6");
							$stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
							$stmt->bindParam(':subject_num', $result_group['subject_num'], PDO::PARAM_STR);
							$stmt->execute();
							$group_list_for_transfer = $stmt->fetchAll();
						} catch (PDOException $e) {
							echo "Error ".$e->getMessage()." !!!";
						}
					?>
					<span class='form-inline'>
						<select required="" name='new_gr' class='form-control'>
							<option value=''>Группаны таңдаңыз!</option>
							<?php foreach ($group_list_for_transfer as $value) {?>
							<option value='<?php echo $value['group_info_num'];?>'><?php echo $value['group_name'];?></option>
							<?php } ?>
						</select>
					</span>
				</center>
			</div>
			<br>
			<center><input type="submit" name="transfer_student" class='btn btn-sm btn-success' value='Сақтау'></center>
			<input type="hidden" name="std_num" value=''>
			<input type="hidden" name="gr_num" value='<?php echo $_GET['data_num']; ?>'>
		</form>
	</div>
</div>

<?php 
// include_once('js.php');
?>
<!-- ----------------------------------------------------__MODAL-START__-------------------------------------------------------- -->
<div class="modal fade box-student-trial-test-mark" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3><span>Пробный тест: </span><b><span id='student-name'></span></b></h3></center>
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
<!-- ----------------------------------------------------__MODAL-END__---------------------------------------------------------- -->




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

	$(document).on('focus','.datePicker',function(){
		$(this).datepicker({
			format: 'dd.mm.yyyy'
		});
	});
// --------------------------------
	$(document).on('keyup',"#search",function(){
		$val = $(this).val();
		$val = $val.replace(" ","_");
		$("#students_list").load('group_student_search.php?search='+$val);
	});
	$(document).on('click',".add-students-btn, .reset-add-students-btn",function(){
		$("#students_list").load('group_student_search.php');
		$p = $(this).parents('tr');
		$p.find(".add-students").toggle();
		$p.find(".add-students-btn").toggle();
	});
	$(document).on('click','.header-student',function(){
		$data_name  = $(this).attr('data-name');
		$data_num = $(this).attr('data-num');
		$data_load  = $(this).attr('data-load');
		$thisParent = $(this).parents('.head-student');
		if($data_load=='n'){
			$thisParent.after('<div class="body-student" style="cursor:pointer; border:1px solid lightgray; padding:2px 20px; border-top:none">Loading...</div>');
			$thisParent.next().load("student_permission.php?data_num="+$data_num+"&status="+$data_name+"&extra_num=<?php echo $result_group['subject_num'];?>");
			$(this).attr('data-load','y')
		}
		else if($data_load=='y'){
			$thisParent.next().slideToggle();
		}
	});
	$(document).on('click','.topic_name, .subtopic',function(){
		$(this).next().slideToggle('fast');
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
	$(document).on('click',".month_for_marks",function(){
		$('.exists-month').removeClass('btn-warning').addClass('btn-default');
		$(this).addClass('btn-warning');
		$date_number = $(this).attr('date-number');
		$date_text = $(this).attr("date-text");
		console.log($(this).attr('data-name'));
		$('.mark-info').html("<h3>Loading...</h3>");
		$('.mark-info').load("admin-mark.php?date_number="+$date_number+"&date_text="+$date_text+"&data_num=<?php echo $_GET['data_num'];?>");

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
			   //  		$thisParent.stop();
						// $thisParent.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },2000);


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
	$(document).ready(function(){
		$(document).on('submit','.form-change-date',(function(e) {
			$thisParent = $(this).parents('td');
			$this = $(this);
			e.preventDefault();
			if(confirm("Подтвердите действие!!!")){
				$.ajax({
		        	url: "ajaxDb.php?<?php echo md5(md5('change-attendance-date'))?>",
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
							$thisParent.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },2000);
							$date_txt = $this.find('input[name=to_date]').val();
							$thisParent.find('p').text($date_txt.substr(0,5)).toggle();
							$thisParent.find('a').toggle();
							$thisParent.find('form').toggle();
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
			}
		}));
	});

	$(document).on('submit','#start_lesson_form',(function(e) {
		$thisParent = $(this).parents('.head-student');
		$this = $(this);
		e.preventDefault();
		$.ajax({
        	url: "ajaxDb.php?<?php echo md5(md5('start-lesson-date'))?>",
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
					$thisParent.css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)' },2000);
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
	// -----------------------End_Ajax------------------------
	$(document).on('click','.transfer-student, .outer .close', function(){
		$data_num = $(this).attr('data-num');
		$data_name = $(this).attr('data-name');
		$('.float-box').find('h3[id=name]').text($data_name);
		$('.float-box').find('input[name=std_num]').val($data_num);
		$('.float-box').slideToggle();
	});

	$(document).on('click','.show-modal-trial-test-mark',function(){
		$std_name = $(this).parents('tr').find('.std-name center').text();
		$data_num = $(this).attr('data-num');
		$('.box-student-trial-test-mark .modal-header #student-name').text($std_name);
		$('.box-student-trial-test-mark .modal-body').html("<center><b>Loading...</b></center>");
		console.log('student_trial_test_info.php?data_num='+$data_num);
		$('.box-student-trial-test-mark .modal-body').load('student_trial_test_info.php?data_num='+$data_num);
	});

	$(document).on('click','.list-progress',function(){
		$sn = $(this).parents('.head-student').find('input[name=data_num]').val();
		$student_name = $(this).parents('.head-student').find('.header-student').text();
		$('.box-list-student-progress .modal-header h3').html("<center><b><?php echo ucwords($result_group['subject_name']);?></b> пәні бойынша тақырыптар тізмі.<br><b>Студент: "+$student_name+"</b></center>");
		$('.box-list-student-progress .modal-body').text("Loading...");
		$('.box-list-student-progress .modal-body').load("load_list_student_progress.php?sn="+$sn+"&sjn=<?php echo $result_group['subject_num'];?>");
	});
	$(document).on('click','.st-lists',function(){
		$class = $(this).parents('.head-topic').attr('data-name');
		$("tr[data-name="+$class+"]").toggle();
	});

	$(document).on('click','.change-date, .cancel-change-date',function(){
		$(this).parents('td').find('p').toggle();
		$(this).parents('td').find('a').toggle();
		$(this).parents('td').find('form').toggle();
	});

	$(document).on('focus','.datePicker_start_lesson',function(){
		var dateToday = new Date();
		$(this).datepicker({
			format: 'dd.mm.yyyy',
            minDate: 0
		});
	});


	$(document).on('click','.to_archive',function(){
	$object_full_name = $(this).parents('.head-student').find('.object-full-name').text();
	if(confirm("Вы точно хотите архивировать? ("+$object_full_name.trim()+")")){
		$data_num = $(this).data('num');
		$data_name = $(this).data('name');
		$this = $(this);
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
		    		$elem = $this.parents(".head-student");
		    		$elem.find('.count').text("-");
		    		$elem.nextAll(".head-student").each(function(){
		    			$(this).find('.count').text(parseInt($(this).find('.count').text().trim())-1);
		    		});
		    		$this.parents(".head-student").stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},1000,function(){
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
});
</script>
</body>
</html>