<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/group/view.php');

	if (!isset($_GET['group_info_id'])) {
		header('Location:../');
	}

	$group_info_id = $_GET['group_info_id'];
?>

<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($root.'/common/check_authentication.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/assets/meta.php');
	    check_admin_access();
	?>
	<title>Пробный тест жауаптары</title>
</head>
<body>
	<input type="hidden" id="ab_root" value='<?php echo $ab_root; ?>'>

	<div class='container'>
		<div class='row'>
			<div id='group-student-trial-test-container'>
				
			</div>
		</div>
	</div>

	<?php include_once($root.'/common/assets/js.php'); ?>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/common/assets/js/chart.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/staff/group/group/js/action.js?v=0.0.0'; ?>"></script>
	<script type="text/javascript">
		render_trial_test_results(get_url_params('group_info_id'));
	</script>
</body>
</html>