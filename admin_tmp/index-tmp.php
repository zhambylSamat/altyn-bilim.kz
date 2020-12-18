
<?php 
	include('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		header('location:signin.php');
	}
	$list = array();
	try {
		$stmt = $conn->prepare("SELECT s.subject_num sNum, s.subject_name sName, t.topic_num tNum, t.topic_name tName, st.subtopic_num stNum, st.subtopic_name stName FROM subject s, topic t, subtopic st WHERE s.subject_num = t.subject_num AND t.topic_num = st.topic_num order by s.created_date, t.created_date, st.created_date asc");
	    $stmt->execute();
	    $result_list = $stmt->fetchAll();
	    foreach ($result_list as $value) {
	    	$list[$value['sNum']]['name'] = $value['sName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['name'] = $value['tName'];
	    	$list[$value['sNum']]['topic'][$value['tNum']]['subtopic'][$value['stNum']]['name'] = $value['stName'];
	    }
	    $_SESSION['list-subject-topic-subtopic'] = $list;
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
</head>
<body>
<?php include_once('nav.php');?>
	<section id='body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<ul class="nav nav-tabs">
					 	<li role="presentation" class="navigation active" data='student'><a href="#">Студент</a></li>
					 	<li role="presentation" class='navigation' data='subject'><a href="#">Пән</a></li>
					</ul>
					<br>
					<div class='student box'>
						<button class='btn btn-success btn-sm new-student' id='new-student-btn'>Жаңа оқушыны енгізу</button>
						<div id='new-student'>
							<!-- <h5 style='color:gray;'>* Пароль по умолчанию "Altynbilim"</h5> -->
							<form class="form-inline" id="create_student" action='admin_controller.php' method='post'>

								<div class="form-group">
									<label for="id-name">Аты</label>
							    	<input type="text" name='name' class="form-control" id="id-name" placeholder="Аты" required="">
							  	</div>
							  	<div class="form-group">
							    	<label for="id-surname">Тегі</label>
							    	<input type="text" name='surname' class="form-control" id="id-surname" placeholder="Тегі" required="">
							  	</div>
							  	<div class='form-group'>
							  		<input type="text" name="username" class='form-control' title='"name.surname" и все буквы должны в нижнем регисте' placeholder='name.surname' required="" pattern='[a-z]+[0-9]*(\.[a-z]+)[0-9]*'>
							  		
							  		<!-- <div class="input-group-addon">.@altyn-bilim.kz</div> -->
							  		<!-- <input type="hidden" name="addon-email" value='.id@altyn-bilim.kz'> -->
							  	</div>
							  	<div class='radio'>
							  		<label>
							  			Ер
							  			<input type="radio" name="gender" value='1' checked>
							  		</label>
							  	</div>
							  	<div class='radio'>
							  		<label>
							  			Әйел
							  			<input type="radio" name="gender" value='0'>
							  		</label>
							  	</div>
							  	<input type="submit" class="btn btn-info" value='Жіберу'>
							  	<a class='btn close-add-new-student' title='Отмена'><span class='glyphicon glyphicon-remove text-danger' style="font-size: 18px;"></span></a>
							</form>
						</div>
						<input type="text" name="search" class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
						<hr>
						<div class='students'>
							<?php include_once('index_students.php');?>
						</div>
					</div>


					<div class='subject box'>
						<center>
							<form class='form-inline create_subject' method="post" action="admin_controller.php">
								<div class="form-group">
									<label for="id-new-subject-name">Жаңа паннің атауы </label>
							    	<input type="text" name='new-subject-name' class="form-control" id="id-new-subject-name" placeholder="Пәннің атауы"  required="">
							  	</div>
							  	<button type='submit' name='create-new-subject' class='btn btn-info'>Жаңа пән енгізу</button>
							</form>
						</center>
						<hr>
						<center>
							<form class='form-inline create_subject' method='post' action="admin_controller.php">
								<div class='form-group'>
									<label for='id-new-topic-name'>Тақырып</label>
									<input type="text" name="new-topic-name" class='form-control' id='new-topic-name' placeholder='Тақырып атауы' required="">
								</div>
								<div class='form-group'>
									<label>Тәуелді пән</label>
									<select name='subject-num' class='form-control' required="">
										<option value=''>Таңдаңыз</option>
										<?php
											try {
												$stmt_subject = $conn->prepare("SELECT * FROM subject");

											    $stmt_subject->execute();
											    $result_subject = $stmt_subject->fetchAll();
											} catch (ExcPDOExceptioneption $e) {
												echo "Error: " . $e->getMessage();
											}
										?>
										<?php foreach($result_subject as $readrow){?>
										<option value='<?php echo $readrow['subject_num'];?>'><?php echo $readrow['subject_name'];?></option>
										<?php }?>
									</select>
								</div>
								<button type='submit' name='create-new-topic' class='btn btn-info'>Енгізу</button>
							</form>
						</center>
						<hr>
						<center>
							<form class='form-inline create_subject' action='admin_controller.php' method="post">
								<div class='form-group'>
									<label for='id-new-subtopic-name'>Тақырыпша</label>
									<input type="text" name="new-subtopic-name" class='form-control' id='new-topic-name' placeholder='Тақырыпша атауы'  required="">
								</div>
								<div class='form-group'>
									<label>Тәуелді тақырып</label>
									<select name='topic-num' class='form-control' required="">
										<option value=''>Таңдаңыз</option>
										<?php
											try {
												$stmt_topic = $conn->prepare("SELECT * FROM topic");

											    $stmt_topic->execute();
											    $result_topic = $stmt_topic->fetchAll();
											} catch (ExcPDOExceptioneption $e) {
												echo "Error: " . $e->getMessage();
											}
										?>
										<?php foreach($result_topic as $readrow){?>
										<option value='<?php echo $readrow['topic_num']?>'><?php echo $readrow['topic_name']?></option>
										<?php }?>
									</select>
								</div>
								<button type='submit' name='create-new-subtopic' class='btn btn-info'>Енгізу</button>
							</form>
						</center>
						<hr>
						<div class='subjects'>
							<?php include('index_subjects.php');?>
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
		// ----------------------
		$(document).on('click','.edit_user, .cancel_edit',function(){
			$(this).parents('.head').find('.user_info').toggle();
		});
		$(document).on('click','.more_info',function(){
			$data_toggle = $(this).attr('data_toggle');
			$data_num = $(this).attr('data_num');
			if($data_toggle=='false'){
				$(this).parents('.head').next().load("student-info.php?<?php echo md5('student_num')?>="+$data_num);
				$(this).attr('data_toggle','true');
			}
			$(this).parents('.head').next().toggle();
		});
		$(document).on('click','.close_body',function(){
			$(this).parents('.body').hide();
		});
		$(document).on('click','.info-list, .new-student',function(){
			$(this).next().toggle();
		});
		$(document).on('click','.close-add-new-student',function(){
			$(this).parents('#new-student').hide();
		});
		// ----------------------------------------------------
		$('.navigation').on('click',function(){
			if(!$(this).hasClass('active')){
				$('.navigation').removeClass('active');
				$(this).addClass('active');
				$attr = $(this).attr('data');
				$('.box').css('display',"none");
				$('.'+$attr).css('display','block');
			}
		});
		function hide(objHide){
			$(function(){
				console.log(objHide+" --function hide(obj)");
				$(objHide).css('display','none');
			});
		}
	</script>
	<script type="text/javascript">
		$(document).on('click','.reset_password',function(){
			$val = $(this).next().val();
			$this = $(this);
			var formData = {
				'action':"reset",
				'reset' : $val
			};
			if(confirm("Пароль поменяется на 'Altynbilim'. Подтвердите действие?")){
				$.ajax({
					type 		: 'POST',
					url 		: 'reset.php?<?php echo md5(md5('resetThisStudent'))?>', 
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
							$this.parents('.password').addClass('pull-right');
				    		$this.parents('.password').html("<h5 class='text-warning' style='text-decoration:underline; font-weight:bolder;'>- Пароль изменен на 'Altynbilim'</h5>");
				    	}
				    	else{
				    		console.log(data);
				    	}
					}
				});
			}
		});
		$(document).ready(function(){
			$(document).on('submit','#create_student',(function(e) {
				thisParent = $(this);
				// $elemNum = $(this).children(":last-child").find('input').attr('data_num');
				e.preventDefault();
				// $tmp = $(this).find('input[name=number_of_answers]').val();
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
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		// $('.students').html(data.text);
				    		$('.students').load('index_students.php');
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
		$(document).on('keyup','#search',function(){
			$val = $(this).val();
			$val = $val.replace(" ","_");
			$('.students').load('index_students.php?search='+$val);
		});
	</script>
</body>
</html>