<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/discount/views.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/discount/components/create_discount.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/discount/components/discount_list.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/discount/components/set_discount_for_group_student.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/discount/components/discount_group_student_list.php'); ?>
		</div>
	</div>
</div>