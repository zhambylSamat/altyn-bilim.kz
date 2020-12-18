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

    include_once($root.'/common/check_authentication.php');
    check_admin_access();

?>

<div>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<div class='material-links'>
				<?php
					include_once('components/index.php');
				?>
			</div>
		</div>
	</div>
<div>
