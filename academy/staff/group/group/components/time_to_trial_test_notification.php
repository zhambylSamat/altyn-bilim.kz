<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/group/view.php');

	if (!isset($group_id)) {
		header('Location:index.php');
	}

	$is_time_to_trial_test = get_is_time_to_trial_test($group_id);

	if ($is_time_to_trial_test) {
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='is-time-to-trial-test-notification-box' class='pulse-red'>
			<center><h4 id='title' class='set-group-student-trial-test-notification-btn' style='cursor: pointer;' data-lp-id='<?php echo $lesson_progress_id; ?>'>Оқушылардың пробный тест жазатын уақыттары келді!</h4></center>
		</div>
	</div>
</div>
<br>
<?php } ?>

<div class='row	'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<a class='btn btn-sm btn-info btn-block' target="_blank" href='<?php echo $ab_root.'/academy/staff/group/group/components/group_student_trial_test_result.php?group_info_id='.$group_id; ?>'>Группадағы оқушылардың пробный тесттері</a>
	</div>
</div>