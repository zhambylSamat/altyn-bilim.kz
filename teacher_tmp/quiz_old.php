<?php
	if(!isset($_GET['data_num']) && !isset($_SESSION['teacher_num'])){
		header('location:index.php');
	}
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name subject_name, gi.group_name group_name, gi.comment comment FROM subject s, group_info gi WHERE gi.group_info_num = :group_info_num AND s.subject_num = gi.subject_num");
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

	    $stmt = $conn->prepare("SELECT s.student_num student_num, s.name name, s.surname surname FROM student s, group_info gi, group_student gs WHERE gi.group_info_num = :group_info_num AND gs.group_info_num = gi.group_info_num AND s.student_num = gs.student_num order by surname");
	    $stmt->bindParam(':group_info_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $student_list = $stmt->fetchAll();
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
									<p>Мұғалім: <b><?php echo $_SESSION['teacher_name']." ".$_SESSION['teacher_surname'];?></b></p>
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
						<input type="hidden" name="data_num" value='<?php echo $_GET['data_num']; ?>'>
						<div style='width: 20%; vertical-align: top;' class='form-group'>
							<input type="date" class='form-control' name="quiz_date" required="">
						</div>
						<div style='width:40%; vertical-align: top;' class='form-group'>
							<select class='form-control' id='subject-list'>
								<option value=''>Тақырып таңдаңыз</option>
								<?php foreach ($topic_list as $key => $value) { ?>
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<div style='width: 39%;' class='form-group'>
							<div class='tpc'>
								<i><h4>Қарастырылған тақырып(тар)</h4></i>
							</div>
						</div>
						<hr>
						<table class='table table-bordered table-striped'>
							<tr>
								<th>Аты-жөні</th>
								<th><input type="number" class='form-control max-mark' min='0.5' step='0.5' name="max_mark" placeholder="Максималды балл" required=""></th>
							</tr>
							<?php foreach ($student_list as $value) { ?>
							<tr>
								<td>
									<?php echo $value['surname']." ".$value['name'];?>
									<input type="hidden" name="sNum[]" value='<?php echo $value['student_num']; ?>'>
								</td>
								<td>
									<input type="number" class='form-control marks' name="mark[]" min='0' max='0' step='0.5' value='0' required="">
								</td>
							</tr>
							<?php } ?>
						</table>
						<input type="submit" name="submit_quiz_marks" class='btn btn-sm btn-success pull-right' value='Сақтау'>
					</form>
				</div>
			</div>
		</div>
	</section>

	<?php include_once('js.php');?>
	<script type="text/javascript">
		$(document).on('click keyup','.max-mark',function(){
			$max_mark = ($(this).val()=='') ? 0 : $(this).val();
			$(".marks").attr('max',$max_mark);
		});
		$selected_topic = [];
		$(document).on('change',"#subject-list",function(){
			$val = $(this).val();
			if($.inArray($val, $selected_topic)){
				$selected_topic.push($val);
				$text = $(this).find('option[value='+$val+']').text();
				$('.tpc').append("<div class='single-topic' style='border:1px solid lightgray; border-radius: 5px; padding:2% 4%;'><span style='overflow:hidden;'>"+$text+"</span><a class='btn btn-xs pull-right remove-topic'><span class='glyphicon glyphicon-remove text-danger'></span></a><input type='hidden' name='topic[]'' value='"+$val+"'></div>");
			}
		});
		$(document).on('click','.remove-topic',function(){
			$val = $(this).next().val();
			$(this).parent().remove();
			$selected_topic = jQuery.grep($selected_topic, function(value) {
			  return value != $val;
			});
		});

		function checkBeforeSubmit(){
			if($selected_topic.length==0){
				alert("Бақылаудың тақырыбын таңдаңыз!");
				return false;
			}
			else{
				if(confirm('Подтвердите действие!')){
					return true;
				}
				return false;
			}
		}
	</script>
</body>
</html>