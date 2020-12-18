<?php
	include_once('../connection.php');
	$student_num = $_GET['data_num'];
	$subject_num = $_GET['sjn'];
	$false = md5('false');
	$true = md5('true');
	$as_admin = $false;
	if(isset($_SESSION['adminNum']) && isset($_SESSION['role']) && $_SESSION['role'] == md5('admin')){
		$as_admin = isset($_GET['teacher']) && $_GET['teacher'] ? $false : $true;
	}
	else if(!isset($_SESSION['teacher_num'])){
		header('location:signin.php');
	}

	try {
		$stmt = $conn->prepare("SELECT name, surname FROM student WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$student_name = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT ttm.trial_test_mark_num,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') AS date_of_test
								FROM trial_test tt, 
									trial_test_mark ttm
								WHERE tt.subject_num = :subject_num
									AND tt.student_num = :student_num
									AND ttm.trial_test_num = tt.trial_test_num
								ORDER BY ttm.date_of_test DESC");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$trial_test_sql_result = $stmt->fetchAll();
		// print_r($trial_test_sql_result);
		$tt_result = array();
		foreach ($trial_test_sql_result as $key => $value) {
			$tt_result[$value['trial_test_mark_num']]['mark'] = $value['mark'];
			$tt_result[$value['trial_test_mark_num']]['date'] = $value['date_of_test'];
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title><?php echo $student_name['surname']." ".$student_name['name']; ?> Пробный тест</title>
	<?php include_once('style.php');?>
	<style type="text/css">
		.warn{
			background-color: #FFA500 !important;
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


<section class='container'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<h3>Пробный тест: <?php echo $student_name['surname']." ".$student_name['name']; ?></h3>
		</div>
		<div class='col-md-12 col-sm-12'>
			<table class='table table-bordered table-striped'>
				<?php 
					$count=0;
					$year = '';
					$month = '';
					$day = '';
					$chart_script = '<script>$(document).ready(function(){var chart = new CanvasJS.Chart("chartContainer", {animationEnabled: true, title:{
									text: "Пробный тест: '.$_GET['sn'].'"}, axisY :{includeZero: false, prefix: ""}, toolTip: { shared: true}, legend: {fontSize: 13}, data: [{},{type: "spline", showInLegend: true, name: "Пробный тест", yValueFormatString: "#", dataPoints: [';
					foreach($tt_result as $key => $value){
						$year = intval(substr($value['date'],6,4));
						$month = (intval(substr($value['date'],3,2))-1);
						$day = intval(substr($value['date'],0,2)); 
						$chart_script .= '{ x: new Date('.$year.', '.$month.', '.$day.'), y: '.intval($value['mark']).' },';
					?>
				<tr>
					<th><center><?php echo ++$count; ?></center></th>
					<td>
						<form onsubmit='return confirm("Подтвердите действие!");' class='form-inline trial-mark-form' method='post' action='teacher_controller.php'>
							<div class='form-group'>
								<label for='trial-data'>Дата:&nbsp;</label>
								<input type="text" class='form-control datePicker' placeholder="dd.mm.yyyy" id='trial-data' disabled value='<?php echo $value['date'];?>' name="trial_date">
							</div>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<div class='form-group'>
								<label for='trial-mark'>Балл:&nbsp;</label>
								<input type="number" min='0' max='40' step='1' class='form-control' id='trial-mark' disabled value='<?php echo $value['mark'];?>' name="trial_mark">
							</div>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<div class='form-group action' style='display:none;'>
								<input type="reset" class='btn btn-xs btn-warning' value='Отмена'>
								<input type="submit" name="edit_trial_test_mark" class='btn btn-xs btn-success' value='Сохранить'>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="submit" name="remove_trial_test_mark" class='btn btn-xs btn-danger' value='Удалить'>

								<input type="hidden" name="ttmn" value='<?php echo mt_rand().'X'.$key."X".mt_rand(); ?>'>
								<input type="hidden" name="data_num" value='<?php echo $student_num;?>'>
								<input type="hidden" name="sjn" value='<?php echo $subject_num;?>'>
								<input type="hidden" name="sn" value='<?php echo $_GET['sn'];?>'>
							</div>
							<div class='form-group edit'>
								<a class='btn btn-xs btn-info'>Өзгерту</a>
							</div>
						</form>
					</td>
				</tr>
				<?php 
					}
					$chart_script .= '{ x: new Date('.$year.', '.$month.', '.$day.'), y: '.intval('0').' }';
					// $chart_script = rtrim($chart_script, ',');
					$chart_script .= ']}]});chart.render();});</script>'; 
				?>
			</table>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<div id="chartContainer" style="height: 370px; width: 100%;"></div>
		</div>
	</div>
</section>


<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
	<center>
		<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
	</center>
</div>
<?php include_once('js.php');?>
<?php echo $chart_script; ?>
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
	

	$(document).on('click','.edit a',function(){
		$(this).parents('form').find('.edit').hide();
		$(this).parents('form').find('.action').show();
		$(this).parents('form').find('input[name=trial_date]').removeAttr('disabled');
		$(this).parents('form').find('input[name=trial_mark]').removeAttr('disabled');
	});
	$(document).on('click','input[type=reset]',function(){
		$(this).parents('form').find('.action').hide();
		$(this).parents('form').find('.edit').show();
		$(this).parents('form').find('input[name=trial_date]').attr('disabled','');
		$(this).parents('form').find('input[name=trial_mark]').attr('disabled','');
	});

	$(document).on('focus','.datePicker',function(){
		$(this).datepicker({
			format: 'dd.mm.yyyy'
		});
	});
</script>
</body>
</html>