$(document).on('focus', '.holiday-datepicker-input', function() {
	$(this).datepicker({
		format: 'dd.mm.yyyy',
		// daysOfWeekDisabled: "0",
		daysOfWeekHighlighted: "0",
		todayHighlight: true,
		language: "ru",
		autoclose: true,
		maxViewMode: 0,
		todayBtn: "linked",
		weekStart: 1,
	});
});

$(document).on('click', '.open-holiday-form', function() {
	$(this).hide();
	$('.holiday-form').show();
});

$(document).on('click', '.holiday-form .cancel-btn', function() {
	$('.holiday-form').hide();
	$('.open-holiday-form').show();
});