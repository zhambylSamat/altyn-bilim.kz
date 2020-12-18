<?php
	$LEVEL = 1;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');
    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);
?>
<style type="text/css">
	.go-to-instruction-page-on-notification {
		margin-top: -0.5%;
	}

	@media (max-width: 768px) {
		.go-to-instruction-page-on-notification {
			margin-top: -3% !important;
		}		
	}
</style>
<button class='btn btn-sm btn-default pull-right go-to-instruction-page go-to-instruction-page-on-notification'>Инструкция</button>
<center><h4 style='margin-bottom: 2%;'>Ескертулер парағы</h4></center>
<div id='notification-body'>
	<?php include_once($root.'/student/notification/success_notifications/index.php'); ?>
	<?php include_once($root.'/student/notification/error_notifications/index.php'); ?>
</div>