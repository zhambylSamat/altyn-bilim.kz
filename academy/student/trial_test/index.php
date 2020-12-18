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

<div id=''>
	<div style='text-align: right; margin-bottom: 1%;'><button class='btn btn-sm btn-default go-to-instruction-page'>Инструкция</button></div>
	<?php include_once($root.'/student/trial_test/components/index.php'); ?>
</div>