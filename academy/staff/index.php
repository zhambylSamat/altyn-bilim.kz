<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

		include_once($root.'/common/assets/meta.php');
	?>
	<title>Admin - Online Academy</title>
	<?php 
		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/connection.php');
		include_once('staffs_style.php');


		include_once($root.'/common/assets/js.php');
		include_once('staffs_js.php');
		$new_page = '';
		if (isset($_GET['page']) && isset($_GET['group'])) {
			$new_page = $_GET['page'];
		}

	?>
</head>
<body>
	<?php
		check_admin_access();
		include_once('nav.php');
		if ($new_page == '') {
			include_once($root.'/common/set_navigations.php');
		}
		$LEVEL = 0;

		// include_once($root.'/staff/notification/index.php');
	?>
	<section id='body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12'>
					<?php
						if ($new_page != '') {
							if ($new_page == "group") {
								include_once($root.'/staff/group/group/index.php');
							}
						} else {
							set_navigation($LEVEL);
						}
					?>
				</div>
			</div>
		</div>
	</section>

	<?php
		// include_once($root.'/common/assets/js.php');
		// // if ($new_page == '') {
		// 	include_once('staffs_js.php');
		// // } else if ($new_page == "group") {
		// 	$script_html = "<script type='text/javascript' src='".($ab_root.'/academy/staff/group/group/js/action.js?v=1.0.3')."'></script>";
		// 	echo $script_html;
		// // } 
	?>
</body>
</html>