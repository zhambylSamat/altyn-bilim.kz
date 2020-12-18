$(document).on('click', '.choose-student-payment-period', function() {
	$val = $('input[name=student-payment-period]').val().split('.');
	$month = $val[0];
	$year = $val[1];
	$date = $year+'-'+$month;
	$ab_root = $('#ab-root').val();
	set_load('.student-payment-list');
	$('.student-payment-list').load($ab_root+'/academy/staff/student_payment_list/components/student_payment_list.php?date='+$date, function() {
		remove_load();
		lightAlert($('.student-payment-list'), 'green', 0, 300);
		clear_add_new_amount_form();
	});
});
