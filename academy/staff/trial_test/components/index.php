<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/trial_test/views.php');
?>
<input type='hidden' id='ab-root' value='<?php echo $ab_root; ?>'>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/trial_test/components/subject_list.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12 trial-test-list-content'>
			<?php include_once($root.'/staff/trial_test/components/trial_test_list.php'); ?>
		</div>
	</div>
</div>