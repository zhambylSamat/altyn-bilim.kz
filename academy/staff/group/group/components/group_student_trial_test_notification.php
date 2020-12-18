<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/group/view.php');

	if (!isset($group_id)) {
		header('Location:index.php');
	}

	$group_student_trial_test_short_info = get_group_student_trial_test_short_info($group_id);

	if ($group_student_trial_test_short_info['total_student_trial_test_count'] > $group_student_trial_test_short_info['submitted_student_trial_test_count']) {
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='not-full-student-submit-trial-test-notification-box'>
			<center><h4 id='title'><?php echo $group_student_trial_test_short_info['total_student_trial_test_count'].' оқушыдан '.$group_student_trial_test_short_info['submitted_student_trial_test_count'].' оқушы тестті орындады'; ?></h4></center>
		</div>
	</div>
</div>
<br>

<?php } ?>
