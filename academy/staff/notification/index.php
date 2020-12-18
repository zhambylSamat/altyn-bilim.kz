<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
?>
<div class='container'>
	<div class='row'>
		<?php include_once($root.'/staff/notification/no_progress_student_notification.php'); ?>
		<?php include_once($root.'/staff/notification/going_to_start.php'); ?>
		<?php include_once($root.'/staff/notification/no_payment.php'); ?>
		<?php include_once($root.'/staff/notification/reserve.php');?>
	</div>
	<hr>
</div>