<!DOCTYPE html>
<html>
<head>
	<?php include_once('common/assets/meta.php');?>
	<title>Тіркелу - Марафон. Altyn Bilim</title>
	<?php include_once('common/assets/style.php');?>
	<?php include_once('common/connection.php'); ?>
	<?php include_once('controller_functions.php'); ?>
	<style type="text/css">
		body {
			background-image: url('lending_img/registration.webp');
			background-repeat: no-repeat;
			background-position: bottom;
			background-attachment: fixed;
			background-size: cover;
			height: 100vh;
		}

		.content-box {
			background-color: rgba(255, 255, 255, 0.5);
			border-radius: 50px;
			padding: 5% 10%;
		}

		.reserve-topic {
			font-size: 14px;
		}

		#registartion-info {
			text-align: center;
			font-weight: bold;
		}

		#registartion-info-title {
			font-size: 18px;
			color: #991818;
		}
		#registration-done-info {
			text-align: center;
			color: green;
			font-weight: bold;
			font-size: 16px;
		}

		/*@media (max-width: 625px) {
			body {
				background-image: url('common/assets/img/bg1.jpg');
				background-repeat: repeat;
				background-size: 100%;
			}
		}*/
	</style>
</head>

<?php
	$matsau = array('id' => 20,
				'title' => 'Матсауаттылық');
	$fizika = array('id' => 21,
					'title' => 'Физика');
	$algebra = array('id' => 16,
					'title' => 'Алгебра');
	$subjects = array($fizika);
?>

<body>
	<div class='container'>
		<div class='row'>
			<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
			<br><br><br><br><br><br>
			<center>
				<h3 style="font-family: 'Times New Roman'; font-weight: 900; color:#222423;">Altyn Bilim Онлайн <i>"Мастер калсына"</i> тіркелу сауалнамасы</h3>
			</center>
			<?php 
				if (isset($_GET['registration']) && $_GET['registration'] == true) {
			?>
				<div id='registration-done-info' class='content-box'>
					<p>Тіркелу сәтті аяқталды. Администратордың жауабын күтіңіз</p>
				</div>
			<?php
				} else {
			?>
			<form onsubmit='return validation()' class="form-horizontal content-box" action='controller.php' method='post' autocomplete='off'>
				<div class='form-group'>
			  		<label for='phone' class='col-sm-3 control-label'>
			  			<span>Телефон</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<div class='input-group'>
			  				<div class='input-group-addon'>+7</div>
			      			<input type="number" required max='7999999999' min='7000000000' step='1' name='phone' class="form-control" id="phone" placeholder="Телефон нөмірін енгізіңіз">
			      			<p id='phone-info'></p>
			  			</div>
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='last-name' class='col-sm-3 control-label'>
			  			<span>Тегі</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="last_name" id='last-name' placeholder="Тегіңіз" value=''>
			  		</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="first-name" class="col-sm-3 control-label">
			    		<span>Аты</span>
			    		<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			    	</label>
			    	<div class="col-sm-9">
			      		<input type="text" required class="form-control" name='first_name' id="first-name" placeholder="Атыңыз" value=''>
			    	</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='school' class='col-sm-3 control-label'>
			  			<span>Мектеп</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="school" id='school' placeholder="Мектеп">
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='class' class='col-sm-3 control-label'>
			  			<span>Сынып</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="class" id='class' placeholder="Сынып">
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='city' class='col-sm-3 control-label'>
			  			<span>Қала</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="city" id='city' placeholder="Қала">
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='instagram' class='col-sm-3 control-label'>
			  			<span>Инстаграм</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<div class='input-group'>
			  				<div class='input-group-addon'>@</div>
			      			<input type="text" required name='instagram' class="form-control" id="instagram" placeholder="Инстаграм">
			  			</div>
			  		</div>
			  	</div>
			  	<!-- <div class='col-md-12 col-sm-12 col-xs-12' style='background-color: rgba(255, 255, 255, 0.7); border-radius: 10px; margin-top: 2%;'>
			  		<center>
			  			<span style="font-size: 14px; font-weight: bold;">
			  				<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  				Марафонға қатысқың келетін ПӘНДІ таңда
			  				<br>
			  				(екеуін де таңдасаң болады)
			  			</span>
			  		</center>
			  	</div> -->
			  	<?php
			  		$html = "";
			  		foreach ($subjects as $value) {
			  			$html .= "<div class='form-group col-md-6 col-sm-6 col-xs-6'>";
			  				$html .= "<center><label class='checkbox-inline control-label'>";
			  					$html .= "<input type='checkbox' name='subject[]' value='".$value['id']."' checked><b style='font-size: 16px;'>".$value['title']."</b>";
			  				$html .= "</label></center>";
			  			$html .= "</div>";
			  		}
			  		$html .= "<b class='text-danger' id='subject-info'></b>";
			  		echo $html;
			  	?>
			  	<!-- <div class="form-group"> -->
			    	<!-- <div class="col-sm-offset-3 col-sm-9"> -->
			      		<center><button type="submit" name='marathon-registration-submit' style='margin-top: 5%;' class="btn btn-success">Тіркелу</button></center>
			    	<!-- </div> -->
			  	<!-- </div> -->
			</form>
			<?php } ?>
		</div>
	</div>

	<?php include_once('common/assets/js.php');?>
	<script type="text/javascript">
		$(document).on('keyup', 'input[name=phone]', function() {
			$phone = $(this).val();
			if ($phone.length == 10) {
				$.ajax({
		    	url: "controller.php?get_student_info_by_phone&phone="+$phone,
				beforeSend:function(){
					$('#phone-info').val('Загрузка...');
				},
				success: function(data){
					console.log(data);
			    	$json = $.parseJSON(data);
			    	if ($json.success) {
			    		if ($json.data.length != 0) {
			    			$student_id = $json.data.student_id;
			    			$last_name = $json.data.last_name;
			    			$first_name = $json.data.first_name;
			    			$school = $json.data.school;
			    			$class = $json.data.class;
			    			$city = $json.data.city;
			    			$instagram = $json.data.instagram;
			    			$phone = $json.phone;

			    			$('input[name=last_name]').val($last_name);
			    			$('input[name=first_name]').val($first_name);
			    			$('input[name=school]').val($school);
			    			$('input[name=class]').val($class);
			    			$('input[name=city]').val($city);
			    			$('input[name=instagram]').val($instagram);
			    		}
			    	}
			    } 	        
		   	});
			} else {
				$('#phone-info').val('');
			}
		});

		function validation() {
			$selected_subjects = [];
			$('input[name="subject[]"]').each(function() {
				$selected_subjects.push($(this).val());
			});
			if ($selected_subjects.length > 0) {
				return true;
			}
			$('#subject-info').text('Кем дегенде бір сабақты таңдауың керек');
			return false;
		}
	</script>
</body>
</html>