<input type='hidden' id='ab-root' value='<?php echo $ab_root; ?>'>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/student_payment_list/components/student_payment_filter.php'); ?>
		</div>

		<div class='col-md-12 col-sm-12 col-xs-12 student-payment-list'>
			<?php include_once($root.'/staff/student_payment_list/components/student_payment_list.php'); ?>
		</div>
	</div>
</div>