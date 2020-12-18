<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/discount/view.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<?php include_once($root.'/student/discount/components/self_promo_code.php'); ?>
		</div>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<?php include_once($root.'/student/discount/components/promo_code_list.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/student/discount/components/promo_code_use.php'); ?>
		</div>
	</div>
</div>