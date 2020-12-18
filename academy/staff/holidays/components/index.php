<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/holidays/views.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/holidays/components/holiday_form.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<br>
			<?php include_once($root.'/staff/holidays/components/holiday_list.php'); ?>
		</div>
	</div>
</div>
