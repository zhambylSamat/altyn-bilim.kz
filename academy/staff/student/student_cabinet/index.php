<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
		include_once($root.'/common/connection.php');
		include_once($root.'/common/check_authentication.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/assets/meta.php');
	    check_admin_access();

	    if (!isset($_GET['student_id'])) {
	    	header($ab_root.'/academy');
	    }

		$student_id = $_GET['student_id'];

		include_once($root.'/staff/student/student_cabinet/view.php');

		$student_info = get_student_info($student_id);

		$student_payments = $ab_root.'/academy/staff/student/student_cabinet/components/student_payments.php';
		$student_trial_test = $ab_root.'/academy/staff/student/student_cabinet/components/student_trial_test.php';
		$student_groups = $ab_root.'/academy/staff/student/student_cabinet/components/student_groups.php';

		echo "<title>".$student_info['last_name']." ".$student_info['first_name']."</title>";
	?>
</head>
<body>	
	<?php
		include_once($root.'/staff/nav.php');
	?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<p style='padding: 10px 0; font-weight: bold;'><?php echo $student_info['last_name']." ".$student_info['first_name']; ?></p>
				<ul class="nav nav-tabs">
					<li role="presentation" class="cabinet-navigation active" data-dir='<?php echo $student_payments; ?>' data-student-id='<?php echo $student_id; ?>'><a style='cursor: pointer;'>Оплатасы</a></li>
					<li role="presentation" class="cabinet-navigation" data-dir='<?php echo $student_trial_test; ?>' data-student-id='<?php echo $student_id; ?>'><a style='cursor: pointer;'>Пробный тест</a></li>
				</ul>

				<div class='student-cabinet-content'>
					<?php include($root.'/staff/student/student_cabinet/components/student_payments.php'); ?>
				</div>		
			</div>
		</div>
	</div>

	<?php
		include_once($root.'/common/assets/js.php');
	?>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/common/assets/js/moment.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/common/assets/js/chart.js'; ?>"></script>
	<script type="text/javascript" src='js/actions.js?v=0.0.1'></script>
</body>
</html>