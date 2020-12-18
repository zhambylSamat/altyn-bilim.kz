<!DOCTYPE html>
<html>
<head>
	<?php include_once('common/assets/meta.php');?>
	<title>Тіркелу - Онлайн Академия. Altyn Bilim</title>
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

		.form-horizontal {
			background-color: rgba(255, 255, 255, 0.5);
			border-radius: 50px;
			padding: 5% 10%;
		}

		.reserve-topic {
			font-size: 14px;
		}

		#registartion-info {
			margin-top: 2%;
			text-align: center;
			font-weight: bold;
		}

		#registartion-info-title {
			font-size: 18px;
			color: #991818;
		}

		@media (max-width: 625px) {
			body {
				background-image: url('common/assets/img/bg1.jpg');
				background-repeat: repeat;
				background-size: 100%;
			}
			#notification-box {
				margin-bottom: 5% !important;
			}
		}

		#notification-box {
			border-radius: 10px;
			padding: 2% 2%;
			background-color: #337AB7;
			margin-bottom: 2%;
		}
		#notification-title {
			font-size: 16px;
			color: white;
		}
	</style>
</head>

<?php
	$subjects = get_available_subjects();
	$reserve_subjects = get_reserve_subjects();
	if (!isset($_SESSION['registration'])) {
		$_SESSION['registration'] = Array('first_name' => Array('err_display' => 'none', 
																'value' => ''),
										'last_name' => Array('err_display' => 'none',
																'value' => ''),
										'school' => Array('err_display' => 'none', 
															'value' => ''),
										'class' => Array('err_display' => 'none',
															'value' => ''),
										'city' => Array('err_display' => 'none',
															'value' => ''),
										'phone' => Array('err_display' => 'none',
															'extra_err_text' => '',
															'value' => ''),
										'parent_phone' => Array('err_display' => 'none',
																'value' => ''),
										'courses' => Array('err_display' => 'none',
															'value' => ''),
										'has_error' => false);
	}
	if (!isset($_SESSION['registration']['phone']['exists'])) {
		$_SESSION['registration']['phone']['exists'] = '';
	}

	$display = Array('first_name'	=> $_SESSION['registration']['first_name']['err_display'],
					'last_name'		=> $_SESSION['registration']['last_name']['err_display'],
					'school'		=> $_SESSION['registration']['school']['err_display'],
					'class'			=> $_SESSION['registration']['class']['err_display'],
					'city'			=> $_SESSION['registration']['city']['err_display'],
					'phone'			=> $_SESSION['registration']['phone']['err_display'],
					'courses' 		=> $_SESSION['registration']['courses']['err_display']);
?>

<body>
	<div class='container'>
		<div class='row'>
			<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
			<br><br><br><br><br><br>
			<center>
				<h3 style="font-family: 'Times New Roman'; font-weight: 900; color:#222423;">Altyn Bilim Онлайн академиясына тіркелу сауалнамасы</h3>
			</center>
			
			<?php 	
				if (isset($_GET['recomendation_text']) && $_GET['recomendation_text'] != '') {
					$html = "<div id='notification-box'> <center>";
						$html .= "<span id='notification-title'>";
							$html .= 'Тест қорытындысы бойынша сізге Алгебра пәнінен <b>"'.$_GET['recomendation_text'].'"</b> тарауынан бастауыңызға кеңес берілді. Төмендегі сауалнаманы толтырып, керек пәнді және модульді таңдаңыз.';
						$html .= "</span>";
					$html .= "</center></div>";
					echo $html;
				}
			?>
			<form class="form-horizontal" action='controller.php' method='post' autocomplete='off'>
			  	<div class='form-group'>
			  		<label for='last-name' class='col-sm-3 control-label'>
			  			<span>Тегі</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="last_name" id='last-name' placeholder="Тегіңіз" value='<?php echo $_SESSION['registration']['last_name']['value']; ?>'>
			  			<p class="text-danger" style='display: <?php echo $display['last_name']; ?>;'>Тегіңізді енгізу міндетті! Кем дегенде 3 әріп болы керек</p>
			  		</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="first-name" class="col-sm-3 control-label">
			    		<span>Аты</span>
			    		<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			    	</label>
			    	<div class="col-sm-9">
			      		<input type="text" required class="form-control" name='first_name' id="first-name" placeholder="Атыңыз" value='<?php echo $_SESSION['registration']['first_name']['value']; ?>'>
			      		<p class="text-danger" style='display: <?php echo $display['first_name']; ?>;'>Есіміңізді енгізу міндетті! Кем дегенде 3 әріп болы керек</p>
			    	</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='school' class='col-sm-3 control-label'>
			  			<span>Мектеп</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="school" id='school' placeholder="Мектеп" value='<?php echo $_SESSION['registration']['school']['value']; ?>'>
			  			<p class="text-danger" style='display: <?php echo $display['school']; ?>;'>Мектебіңізді енгізу міндетті!</p>
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='class' class='col-sm-3 control-label'>
			  			<span>Сынып</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="class" id='class' placeholder="Сынып" value='<?php echo $_SESSION['registration']['class']['value']; ?>'>
			  			<p class="text-danger" style='display: <?php echo $display['class']; ?>;'>Сыныбыңызды енгізу міндетті!</p>
			  		</div>
			  	</div>
			  	<div class='form-group'>
			  		<label for='city' class='col-sm-3 control-label'>
			  			<span>Қала</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<input type="text" required class='form-control' name="city" id='city' placeholder="Қала" value='<?php echo $_SESSION['registration']['city']['value']; ?>'>
			  			<p class="text-danger" style='display: <?php echo $display['city']; ?>;'>Қаланы енгізу міндетті!</p>
			  		</div>
			  	</div>
			  	<div class='form-group' style='margin-bottom: 4%;'>
			  		<label for='phone' class='col-sm-3 control-label'>
			  			<span>Телефон</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<div class='input-group'>
			  				<div class='input-group-addon'>+7</div>
			      			<input type="number" required max='7999999999' min='7000000000' step='1' name='phone' class="form-control" id="phone" placeholder="Телефон нөмірін енгізіңіз" value='<?php echo $_SESSION['registration']['phone']['value']; ?>'>
			  			</div>
			  			<p class="text-danger" style='display: <?php echo $display['phone']; ?>;'>Телефоныңызды енгізу міндетті! Ұзындығы 10 саннан тұру керек. <?php echo $_SESSION['registration']['phone']['extra_err_text']; ?></p>
			  		</div>
			  	</div>
			  	<!-- <div class='form-group' style='margin-bottom: 4%;'>
			  		<label for='parent-phone' class='col-sm-3 control-label'>
			  			<span>Ата-анаңның телефоны</span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<div class='input-group'>
			  				<div class='input-group-addon'>+7</div>
			      			<input type="number" required max='7999999999' min='7000000000' step='1' name='parent-phone' class="form-control" id="parent-phone" placeholder="Ата-анаңыздың телефон нөмірін енгізіңіз" value='<?php echo $_SESSION['registration']['parent_phone']['value']; ?>'>
			  			</div>
			  		</div>
			  	</div> -->
			  	<div class='col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 hidden-xs'>
			  		<b>* Бір немесе бірнеше пәнге тіркелсең болады</b>
			  	</div>
			  	<div class='form_group'>
			  		<label class='col-sm-3 control-label'>
			  			<span>Оқитын пәнді және тарауды таңда</span>
			  			<span style='font-size: 10px; color: #E52C38;' class='glyphicon glyphicon-asterisk'></span>
			  			<br>
			  			<span class='hidden-lg hidden-md hidden-sm' style='font-weight: normal !important;'>* Бір немесе бірнеше пәнге тіркелсең болады</span>
			  		</label>
			  		<div class='col-sm-9'>
			  			<?php
			  				foreach ($reserve_subjects as $value) {
			  			?>
			  			<button type='button' class='btn btn-default btn-sm btn-info btn-block reserve-topic' data-toggle='modal' data-target='#reserve-topic' style='text-shadow: none;' data-subject='<?php echo $value['id']; ?>'><?php echo $value['title']; ?></button>
			  			<?php } ?>
			  			<p class="text-danger" style='display: <?php echo $display['courses']; ?>;'>Кем дегенде бір пәнді таңдаңыз!</p>
			  		</div>
			  		<div class='col-md-12 '>
			  			<div class='choosen-reserves hidden' style='border: 1px solid lightgray; border-radius: 5px; padding: 5px 10px; margin-top: 24px; box-shadow: 0px 0px 11px gray; background-color: white;'>
			  				<p style='color: green; font-weight: bold; font-size: 16px;'>Таңдаған курстарың:</p>
			  				<div class='reserve-content'></div>
			  				<i><b style='color: #5cb85c;'>Қосымша тағы бірнеше пәнді таңдаңыз болады</b></i>
			  			</div>
			  			<input type="hidden" name="reserves">
			  		</div>
			  	</div>
			  	<div class="form-group">
			    	<div class="col-md-12">
			      		<center>
			      			<button type="submit" name='signUp' style='margin-top: 5%;' class="btn btn-lg btn-success">Тіркелу</button>
			      		</center>
			    	</div>
			  	</div>
			  	<div class='form-group'>
			  		<div id='registartion-info'>
				  		<p id='registartion-info-title'>Ұсыныс!</p>
				  		<p class='registartion-info-subtitle'>Алгебра пәнінен дайындықты өзің білмейтін тараудан, ал <br> Физика және Геометрияны ең бірінші тараудан бастағаның дұрыс.</p>
				  		<p class='registartion-info-subtitle'>Алгебрадан оқуды қай тақырыптан бастайтыныңды білмесең, төмендегі батырма арқылы тестті орындап шық. Тест соңында біз саған оқуды қай тараудан бастау керек екендігі бойынша ұсыныс береміз:</p>
				  	</div>
			  	</div>
			  	<div class='form-group'>
			  		<button type='button' class='btn btn-info btn-sm btn-block entrance-examination-btn' style='font-size: 15px;'>Математикадан деңгейіңді <br class='hidden-lg hidden-md hidden-sm'> анықтайтын тест</button>
			  	</div>
			</form>
		</div>
	</div>
	<input type="hidden" name="error-alert" class='alert-point' value='<?php echo $_SESSION['registration']['phone']['exists'] == 'true' ? 1 : 0; ?>'>

	<?php session_unset(); ?>


	<div class='modal fade' id='error-alert' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
		<div class='modal-dialog' role='document'>
			<div class='modal-content'>
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				</div>
				<div class='modal-body'>
					Сіз осыған дейін тіркеліп қойғансыз. Өз 'құпия сөзіңізбен' жеке кабинетіңізге кіріңіз. 'Құпия сөзді' ұмытып калсаңыз менеджерге хабарласыңыз. +7 777 389 0099
				</div>
				<div class="modal-footer">
			        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
	  			</div>
	  		</div>
		</div>
	</div>

	<div class="modal fade" id="choose-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  		<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Группаны таңда | <span class='title'></span>
	      		</div>
	      		<div class="modal-body">
	        		
	      		</div>

	    	</div>
  		</div>
	</div>

	<div class="modal fade" id="choose-topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  		<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Тарауды таңда | <span class='title'></span></h4>
	      		</div>
	      		<div class="modal-body">
	        		
	      		</div>
	    	</div>
  		</div>
	</div>

	<div class="modal fade" id="choose-subtopic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  		<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Қай тақырыптан бастайтыныңды таңда | <span class='title'></span></h4>
	      		</div>
	      		<div class="modal-body">
	        		
	      		</div>
	      		<!-- <div class="modal-footer">
			        <button type="button" class="btn btn-success" data-dismiss="modal">Сақтау</button>
      			</div> -->
	    	</div>
  		</div>
	</div>

	<div class="modal fade" id="reserve-topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  		<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Тарауды таңдаңыз | <span class='title'></span></h4>
	      		</div>
	      		<div class="modal-body">
	        		
	      		</div>
	      		<!-- <div class="modal-footer">
			        <button type="button" class="btn btn-success" data-dismiss="modal">Сақтау</button>
      			</div> -->
	    	</div>
  		</div>
	</div>

	<?php include_once('common/assets/js.php');?>
	<script type="text/javascript">

		$selected_courses = [];
		$selected_courses_content = {};
		$selected_reserve = [];
		$selected_reserve_content = {};

		$(document).ready(function() {
			$.each($('.alert-point'), function(i, j) {
				if ($(this).val() == 1) {
					$name = $(this).attr('name');
					$('#'+$name).modal('show');
				}
			});
			if (typeof(Storage) !== "undefined") {
				$selected_reserve = JSON.parse(localStorage.getItem('selected_reserve'));
				$selected_reserve_content = JSON.parse(localStorage.getItem('selected_reserve_content'));
				if ($.isArray($selected_reserve) && $.isPlainObject($selected_reserve_content)) {
					set_reserve_content($selected_reserve, $selected_reserve_content);
				} else {
					$selected_reserve = [];
					$selected_reserve_content = {};
				}
			}
		});
		$(document).on('click', '.choose-group', function() {
			$subject_id = $(this).data('subject');
			$subject_title = $(this).text();
			$.ajax({
		    	url: "controller.php?get_groups_by_subject="+$subject_id,
				beforeSend:function(){
					$('#choose-group .modal-body').html("<center><b>Загрузка...</b></center>");
				},
				success: function(dataS){
			    	data = $.parseJSON(dataS);
			    	if(data.success){
			    		$groups = data.groups;
			    		$html = "";
			    		$.each($groups, function(id, item) {
			    			$target_elem = "choose-topic";
			    			if (item.lesson_type == 'topic') {
			    				$target_elem = "choose-subtopic";
			    			}
			    			$html += "<button type='button' data-toggle='modal' data-target='#"+$target_elem+"' data-group='"+item.id+"' class='btn btn-md btn-info btn-block "+$target_elem+"' data-subject='"+$subject_title+"'>"+item.group_name+"</button>";
			    		});
			    		$('#choose-group .title').html($subject_title);
			    		$('#choose-group .modal-body').html($html);
			    	} else {
			    		$('#choose-group .modal-body').html("<center><b>ERROR</b></center>");
			    	}
			    },
			  	error: function(dataS) 
		    	{
		    		alert("Қате. Программистпен жолығыңыз. "+dataS);
		    	} 	        
		   	});
		});

		$(document).on('click', '.choose-topic', function() {
			$group_id = $(this).data('group');
			$group_name = $(this).text();
			$subject_title = $(this).data('subject');
			$.ajax({
				url: "controller.php?get_topics_by_group="+$group_id,
				beforeSend:function() {
					$('#choose-topic .modal-body').html("<center><b>Загрузка...</b></center>");
				},
				success: function(dataS) {
					data = $.parseJSON(dataS);
					if (data.success) {
						$topics = data.topics;
						$selected_courses_content[$group_id] = {'subject_title': $subject_title, 'group_name': $group_name, 'topic_title': '', 'subtopic_title': ''};
			    		$html = "";
			    		$.each($topics, function(id, item) {
			    			$html += "<button type='button' data-toggle='modal' data-target='#choose-subtopic' data-group='"+$group_id+"' data-topic='"+item.id+"' class='btn btn-md btn-info btn-block choose-subtopic'>"+item.title+"</button>";
			    		});
			    		$('#choose-topic .title').html($group_name);
			    		$('#choose-topic .modal-body').html($html);
					} else {
						$('#choose-topic .modal-body').html("<center><b>ERROR</b></center>");
					}
				}
			});
		});

		$(document).on('click', '.choose-subtopic', function() {
			$topic_id = $(this).data('topic');
			$group_id = $(this).data('group');
			$topic_title = $(this).text();
			$url = "controller.php?get_subtopics_by_topic="+$topic_id+"&group="+$group_id;
			if ($topic_id === undefined) {
				$url = "controller.php?get_subtopics_by_group="+$group_id;	
			}
			$.ajax({
				url: $url,
				beforeSend: function() {
					$('#choose-subtopic .modal-body').html("<center><b>Загрузка...</b></center>");
				},
				success: function(dataS) {
					data = $.parseJSON(dataS);
					if (data.success) {
						if ($selected_courses_content[$group_id] == undefined) {
							$selected_courses_content[$group_id] = {'subject_title': $subject_title, 'group_name': $topic_title, 'topic_title': '', 'subtopic_title': ''};
						}
						$subtopics = data.subtopics;
						$html = "<form><table class='table table-condensed'>";
						$prev_learned_date = null;
						$current_subtopic = null;
						$count = 0;
						$.each($subtopics, function(id, item) {
							$text_color = "";
							$content_disabled = "choose-subtopic-radio";
							$content_color = "";
							$checked = "";

							if (item.learned2 == 1) {
			    				$date = item.learned_date;
			    			} else {
			    				$date = item.will_learn_date;
			    			}
			    			$disabled = false;
			    			if (item.learned == '1' && item.learned2 == '1') {
			    				$disabled = true;
			    				$text_color = '#777';
			    				$content_disabled = 'active';
			    			}
			    			$extra_html = '';
			    			if (item.learned == '0' && item.learned2 == '1') {
			    				$extra_html = "<i style='color:#4B974B; display:block;'>Қазір группа осы тақырыпта</i>";
			    			}

			    			$value = $group_id+"-"+item.id;
							if ($selected_courses.includes($value)) {
								$checked = 'checked';
								$content_color = 'success';
							}
			    			$html += "<tr style='color:"+$text_color+"; cursor:pointer;' class='"+$content_disabled+" "+$content_color+"'>";
			    				$html += "<td>";
			    				if (!$disabled) {
			    					$html += "<input type='radio' class='subtopic-radio' data-topic='"+$topic_title+"' data-subtopic='"+item.title+"' name='subtopic' value='"+$value+"' "+$checked+">";
			    				}
			    				$html += "</td>";
				    			$html += "<td>"+item.title+$extra_html+"</td>";
				    			$html += "<td>"+$date+"</td>";
				    		$html += "</tr>";
						});
						$html += "</table></form>";
						$('#choose-subtopic .title').html($topic_title);
						$('#choose-subtopic .modal-body').html($html);
					} else {
						$('#choose-subtopic .modal-body').html('<center><b>ERROR</b></center>');
					}
				}
			});
		});

		$(document).on('click', '.reserve-topic', function() {
			$subject_id = $(this).data('subject');
			$subject_title = $(this).text();
			$.ajax({
				url: 'controller.php?get_topic_by_subject='+$subject_id,
				beforeSend: function() {
					$('#reserve-topic .modal-body').html("<center><b>Загрузка...</b></center>");
				},
				success: function(dataS) {
					data = $.parseJSON(dataS);
					if (data.success) {
						$html = "<form><table class='table table-condensed'>";
						$.each(data.topics, function(i, item) {
							$checked = '';
							$content_color = "";
							$value = $subject_id+'-'+item.id;
							if ($selected_reserve.includes($value)) {
								$checked = 'checked';
								$content_color = 'success';
							}
							$html += "<tr style='cursor: pointer;' class='reserve-topic-radio "+$content_color+"'>";
								$html += "<td style='width: 5%;'><input type='radio' "+$checked+" class='topic-radio' name='topic' value='"+$value+"' data-subject='"+$subject_title+"' data-topic='"+item.title+"'></td>";
								$html += "<td style='width: 70%;'>"+item.title+"</td>";
								$html += "<td style='width: 25%;'>Ұзақтығы: "+item.subtopic_count+" сабақ</td>";
							$html += "</tr>";
						});
						$html += "</form></table>";
						$('#reserve-topic .title').html($subject_title);
						$('#reserve-topic .modal-body').html($html);
					} else {
						$('#reserve-topic .modal-body').html('<center><b>ERROR</b></center>');
					}
				}
			});
		});

		$(document).on('click', '.choose-subtopic-radio', function(){
			$(this).find('.subtopic-radio').prop('checked', true);
			$(this).parent().find('.choose-subtopic-radio').removeClass('success');
			$(this).addClass('success');
			$topic_title = $(this).find('.subtopic-radio').data('topic');
			$subtopic_title = $(this).find('.subtopic-radio').data('subtopic');
			$group_subtopic = $(this).find('.subtopic-radio').val();
			$.each($(this).parent().find('.choose-subtopic-radio .subtopic-radio'), function(i, elem) {
				$removeItem = $(this).val().split('-')[0];
				$selected_courses = jQuery.grep($selected_courses, function(value) {
  					return value.split('-')[0] != $removeItem;
				});
			});
			$selected_courses.push($(this).find('.subtopic-radio').val());
			$group_id = $(this).find('.subtopic-radio').val().split('-')[0];
			$selected_courses_content[$group_id]['topic_title'] = $topic_title;
			$selected_courses_content[$group_id]['subtopic_title'] = $subtopic_title;
			set_course_content($selected_courses, $selected_courses_content);
			$('#choose-subtopic').modal('hide');
			$('#choose-group').modal('hide');
		});

		$(document).on('click', '.reserve-topic-radio', function() {
			$(this).find('.topic-radio').prop('checked', true);
			$(this).parent().find('.reserve-topic-radio').removeClass('success');
			$(this).addClass('success');
			$value = $(this).find('.topic-radio').val();
			$subject_title = $(this).find('.topic-radio').data('subject');
			$topic_title = $(this).find('.topic-radio').data('topic');
			$.each($(this).parent().find('.reserve-topic-radio .topic-radio'), function(i, elem) {
				$removeItem = $(this).val().split('-')[0];
				$selected_reserve = jQuery.grep($selected_reserve, function(value) {
					return value.split('-')[0] != $removeItem;
				});
			});
			$selected_reserve.push($value);
			$selected_reserve_content[$value] = {'subject_title': $subject_title, 'topic_title': $topic_title};
			set_reserve_content($selected_reserve, $selected_reserve_content);
			$('#reserve-topic').modal('hide');
		});

		function set_course_content($courses_id, $courses) {
			if ($courses_id.length > 0) {
				$('.choosen-courses').removeClass('hidden');
			} else {
				$('.choosen-courses').addClass('hidden');
			}
			$html = "<ol>";
			$.each($courses_id, function(i, elem) {
				$id = parseInt(elem.split('-')[0]);
				$subject = $courses[$id]['subject_title'];
				$group = $courses[$id]['group_name'];
				$topic = $courses[$id]['topic_title'];
				$subtopic = $courses[$id]['subtopic_title'];
				$html += "<li>";
					$html += $subject + " | ";
					$html += $group + " | ";
					if ($group != $topic) {
						$html += $topic + " | ";
					}
					$html += $subtopic;
				$html += "</li>";
			});
			$html += "</ol>"
			$('.choosen-courses .course-content').html($html);
			$('input[name=courses]').val($courses_id.join('|'));
		}

		function set_reserve_content($reserves_id, $reserves) {
			if ($reserves_id.length > 0) {
				$('.choosen-reserves').removeClass('hidden');
			} else {
				$('.choosen-reserves').addClass('hidden');
			}
			$html = "<ol style='list-style: none;'>";
			$.each($reserves_id, function(i, elem) {
				$html += "<li style='font-size: 15px;'>";
					$html += "<table class='table' style='margin: 0; padding: 0;'><tr>";
						$html += "<td style='border: none;'>"+(i+1)+".</td>";
						$html += "<td style='border: none;'>"+$reserves[elem]['subject_title']+" | "+$reserves[elem]['topic_title']+"</td>";
						$html += "<td style='border: none;'><button type='button' class='btn btn-xs btn-danger pull-right remove-selected-topic' data-id='"+elem+"'><i class='fas fa-trash-alt'></i></button></td>";
					$html += "</tr></table>";
				$html += "</li>";
				// $remove_html = "";
				// $html += "<li style='font-size: 15px;'>"" "+$remove_html+"</li>";
			});
			$html += "</ol>";
			$('.choosen-reserves .reserve-content').html($html);
			$('input[name=reserves]').val($reserves_id.join('|'));
		}

		$(document).on('click', '.entrance-examination-btn', function() {
			// if (confirm('Математика пәнінен деңдейіңді анықтайтын тестті бастауға келісесінба?')) {
				$.ajax({
					type: "GET",
					url: 'controller.php?create_entrance_examination_object',
					beforeSend: function() {
						set_load('body');
					},
					success: function($data) {
						remove_load();
						$json = $.parseJSON($data);
						if ($json.success) {
							// window.open('http://localhost/altynbilim/test/index.php', '_blank');
							window.open('https://old.altyn-bilim.kz/test/force_sign_in.php?ees_id='+$json.data.ees_id
																						+'&ees_code='+$json.data.ees_code
																						+'&ees_surname='+$json.data.ees_surname
																						+'&ees_name='+$json.data.ees_name
																						+'&test_result='+JSON.stringify($json.data.test_result)
																						+'&finish='+($json.data.finish ? 1 : 0), '_blank');
							// window.open('http://localhost/altynbilim/test/force_sign_in.php?ees_id='+$json.data.ees_id
							// 															+'&ees_code='+$json.data.ees_code
							// 															+'&ees_surname='+$json.data.ees_surname
							// 															+'&ees_name='+$json.data.ees_name
							// 															+'&test_result='+JSON.stringify($json.data.test_result)
							// 															+'&finish='+($json.data.finish ? 1 : 0));
						}
					}
				});
			// }
		});

		$(document).on('click', '.remove-selected-topic', function() {
			$element_id = $(this).data('id');
			$removeItem = $element_id.split('-')[0];
			$selected_reserve = jQuery.grep($selected_reserve, function(value) {
				return value.split('-')[0] != $removeItem;
			});
			$(this).parents('li').remove();

			if ($selected_reserve.length == 0) {
				$('.choosen-reserves').addClass('hidden');
			}
		});
	</script>
</body>
</html>