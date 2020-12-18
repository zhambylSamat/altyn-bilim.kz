$(document).on('click', '.show-student-info', function() {
	$elem = $(this).parents('tr').find('.full-info');
	if ($elem.hasClass('hide')) {
		$elem.removeClass('hide');
	} else {
		$elem.addClass('hide');
	}
});

$(document).on('click', '.start-lesson', function() {
	$data_type = $(this).data('type');
	$data_id = $(this).data('id');
	$current_element = $(this);
	if ($data_type == 'course') {
		if (confirm('Оплатасын өткіздіма? Чекті жібердіма?')) {
			$.ajax({
				url:'student/controller.php?add_to_group&data_id='+$data_id,
				beforeSend:function() {
					$current_element.parent().find('.start-lesson').addClass('hide');
					$current_element.parent().find('.remove-lesson').addClass('hide');
					$current_element.parent().find('.tmp').html("Загрузка...");
					set_load($current_element.parents('ol'));
				},
				success:function($data) {
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parent(), 'green', 0, 500, function() {
							$current_element.parent().find('.tmp').load('student/active_student/components/extra_payments.php?group_student_id='+$data_id);
							$current_element.parent().find('.partial_payment').remove();
							$current_element.parent().find('.remove-lesson').attr('data-id', $json.group_student_id);
							$current_element.parent().find('.remove-lesson').removeClass('hide');
							$current_element.parent().find('.remove-lesson').attr('data-type', 'group');
							$payment_content = $current_element.parents('td').find('.payment-count');
							$payment_count = $payment_content.text() - 1;
							$payment_count = $payment_count == 0 ? '' : $payment_count;
							$payment_content.text($payment_count);
						});
						// $.when(set_student_lesson_access_after_payment($current_element.parents('table'))).done(function($result){
						// 	// $json = $.parseJSON($result);
						// 	console.log($result);
						// 	console.log('cron-runned');
						// });
					}
				}
			});
		}
	} else if ($data_type == 'group') {
		if (confirm('Оплатасын өткіздіма? Чекті жібердіма?')) {
			$student_id = $(this).data('student');
			$group_id = $(this).data('group');
			$.ajax({
				url: 'student/controller.php?done_payment_group&data_id='+$data_id+'&student_id='+$student_id+'&group_id='+$group_id,
				beforeSend: function() {
					$current_element.parent().find('.start-lesson').addClass('hide');
					$current_element.parent().find('.remove-lesson').addClass('hide');
					$current_element.parent().find('.tmp').html("Загрузка...");
					set_load($current_element.parents('ol'));
				},
				success: function($data) {
					console.log($data);
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parent(), 'green', 0, 500, function() {
							// $current_element.parent().find('.tmp').html('<span style="color: #5cb85c; ">Оплатасы өткізілді<span>');
							$current_element.parent().find('.tmp').load('student/active_student/components/extra_payments.php?group_student_id='+$data_id);
							$current_element.parent().find('.remove-lesson').attr('data-id', $json.group_student_id);
							$current_element.parent().find('.remove-lesson').removeClass('hide');
							$current_element.parent().find('.remove-lesson').attr('data-type', 'group');
							$payment_content = $current_element.parents('td').find('.payment-count');
							$payment_count = $payment_content.text() - 1;
							$payment_count = $payment_count == 0 ? '' : $payment_count;
							$payment_content.text($payment_count);
						});
						$.when(set_student_lesson_access_after_payment($current_element.parents('table'), $data_id)).done(function($result){
							// $json = $.parseJSON($result);
							console.log($result);
							console.log('cron-runned');
						});
					}
				}
			});
		}
	} else if ($data_type == 'reserve') {
		if (confirm('Оплатасын өткіздіма? Чекті жібердіма?')) {
			$.ajax({
				url: 'student/controller.php?student_reserve_payment&registration_reserve_id='+$data_id,
				beforeSend: function() {
					$current_element.parent().find('.start-lesson').addClass('hide');
					$current_element.parent().find('.remove-lesson').addClass('hide');
					$current_element.parent().find('.tmp').html("Загрузка...");
					set_load($current_element.parents('p'));
				},
				success: function($data) {
					remove_load();
					console.log($data);
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parent(), 'green', 0, 500, function() {
							$payed_date = $json.payed_date;
							$current_element.parent().find('.tmp').html('<span style="color: #5cb85c;">Төлемі өткізілген уақыт: '+$payed_date+'</span>');
						});
						// $.when(set_student_lesson_access_after_payment($current_element.parents('table'))).done(function($result){
						// 	// $json = $.parseJSON($result);
						// 	console.log($result);
						// 	console.log('cron-runned');
						// });
					}
				}
			})
		}
	}
});

$(document).on('click', '.remove-lesson', function() {
	$data_type = $(this).data('type');
	$data_id = $(this).data('id');
	$current_element = $(this);
	if ($data_type == 'course') {
		if (confirm('Оқушыны группадан шығарайынба?')) {
			$.ajax({
				url: 'student/controller.php?remove_from_course&data_id='+$data_id,
				beforeSend:function() {
					$current_element.parent().find('.start-lesson').addClass('hide');
					$current_element.parent().find('.remove-lesson').addClass('hide');
					$current_element.parent().find('.tmp').html("Загрузка...");
					set_load($current_element.parents('ol'));
				},
				success:function($data) {
					console.log($data);
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parent(), 'red', 0, 500, function() {
							$current_element.parent().remove();
						});
					}
				}
			});
		}
	}
	if ($data_type == 'group') {
		if (confirm('Оқушыны группадан шығарайынба?')) {
			$.ajax({
				url: 'student/controller.php?remove_from_group&data_id='+$data_id,
				beforeSend:function() {
					$current_element.parent().find('.start-lesson').addClass('hide');
					$current_element.parent().find('.remove-lesson').addClass('hide');
					$current_element.parent().find('.tmp').html("Загрузка...");
					set_load($current_element.parents('ol'));
				},
				success:function($data) {
					console.log($data);
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						lightAlert($current_element.parent(), 'red', 0, 500, function() {
							$current_element.parents('li').remove();
						});
					}
				}
			});
		}
	}
});

$(document).on('change', '.partial_payment_days', function() {
	$days = $(this).val();
	$price = $(this).data('price');
	change_partial_payment_price($(this), $days, $price);
});

$(document).on('keyup', '.partial_payment_days', function() {
	$days = $(this).val();
	$price = $(this).data('price');
	change_partial_payment_price($(this), $days, $price);
});

function change_partial_payment_price($elem, $days, $price) {
	$elem.parents('form').find('.partial_payment_price').text(' '+($days*$price)+' тг. ');
}

$(document).on("click", '.partial_payment_form_btn', function() {
	$(this).parents('.partial_payment').find('form').show();
	$(this).hide();
});

$(document).on('click', '.partial_payment_form_cancel', function() {
	$(this).parents('form').hide();
	$(this).parents('.partial_payment').find('.partial_payment_form_btn').show();
});

$(document).on('submit', '.partial_payment_form', function($e) {
	$e.preventDefault();
	$current_element = $(this);
	$parents = $current_element.parents('li');
	$group_student_id = $(this).find('input[name=group_student_id]').val();
	if (confirm('Подтвердите действие!')) {
		$.ajax({
			url: 'student/controller.php?set_partial_payment',
			type: 'POST',
			data: new FormData(this),
			contentType: false,
		    cache: false,
			processData:false,
			beforeSend: function() {
				$parents.find('.start-lesson').addClass('hide');
				$parents.find('.tmp').html("Загрузка...");
				set_load($parents.parents('ol'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					$.when(set_student_lesson_access_after_payment($parents.parents('table'), $group_student_id)).done(function($result){
						// $json = $.parseJSON($result);
						console.log($result);
						console.log('cron-runned');
					});
					lightAlert($parents, 'green', 0, 500, function() {
						// $current_element.parents('li').find('.tmp').html('<span style="color: #5cb85c; ">Оплатасы өткізілді<span>');
						$parents.find('.tmp').load('student/active_student/components/extra_payments.php?group_student_id='+$group_student_id);
						$payment_content = $parents.parents('td').find('.payment-count');
						$payment_count = $payment_content.text() - 1;
						$payment_count = $payment_count == 0 ? '' : $payment_count;
						$payment_content.text($payment_count);
					});
				}
			}
		});
	}
});




function set_student_lesson_access_after_payment($content, $group_student_id) {
	return $.ajax({
		url: 'student/controller.php?set_student_lesson_access_after_payment&group_student_id='+$group_student_id,
		type: "GET",
		beforeSend: function(){
			set_load($content);
		}, success: function($data) {
			remove_load();
		}
	});
}

$(document).on('click', '.next-payment-btn', function() {
	$(this).hide();
	$(this).parents('.extra-pay-box').find('.next-full-payment-btn').show();
	$(this).parents('.extra-pay-box').find('.next-partial-payment-btn').show();
	$(this).parents('.extra-pay-box').find('.cancel-next-payment').show();
});

$(document).on('click', '.cancel-next-payment', function() {
	$(this).hide();
	$(this).parents('.extra-pay-box').find('.next-full-payment-btn').hide();
	$(this).parents('.extra-pay-box').find('.next-partial-payment-btn').hide();
	$(this).parents('.extra-pay-box').find('.next-payment-btn').show();
});

$(document).on('click', '.next-partial-payment-btn', function() {
	$(this).hide();
	$(this).parents('.extra-pay-box').find('.next-full-payment-btn').hide();
	$(this).parents('.extra-pay-box').find('.next-partial-payment-btn').hide();
	$(this).parents('.extra-pay-box').find('.cancel-next-payment').hide();
	$(this).parents('.extra-pay-box').find('.next-partial-payment-form').show();
});

$(document).on('click', '.next-partial-payment-form-cancel', function() {
	$(this).parents('.next-partial-payment-form').hide();
	$(this).parents('.extra-pay-box').find('.next-full-payment-btn').show();
	$(this).parents('.extra-pay-box').find('.next-partial-payment-btn').show();
	$(this).parents('.extra-pay-box').find('.cancel-next-payment').show();
});

$(document).on('click', '.next-full-payment-btn.btn', function() {
	$group_student_id = $(this).data('id');
	$current_element = $(this);
	$parents = $(this).parents('li');
	if (confirm('Оплатасын өткіздіма? Чекті жібердіма?')) {
		$.ajax({
			url: 'student/controller.php?next_full_payment&group_student_id='+$group_student_id,
			beforeSend: function() {
				$parents.find('.tmp').html('Загрузка...');
				set_load($parents.parents('ol'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					lightAlert($parents.find('.tmp'), 'green', 0, 500, function() {
						$parents.find('.tmp').load('student/active_student/components/extra_payments.php?group_student_id='+$group_student_id);
					});
				}
			}
		});
	}
});

$(document).on('submit', '.next-partial-payment-form', function() {
	$group_student_id = $(this).find('input[name=group_student_id]').val();
	$current_element = $(this);
	$parents = $(this).parents('li');

	if (confirm('Оплатасын өткіздіма? Чекті жібердіма?')) {
		$.ajax({
			url: 'student/controller.php?next_partial_payment',
			type: 'POST',
			data: new FormData(this),
			contentType: false,
		    cache: false,
			processData:false,
			beforeSend: function() {
				$parents.find('.tmp').html('Загрузка...');
				set_load($parents.parents('ol'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					lightAlert($parents.find('.tmp'), 'green', 0, 500, function() {
						$parents.find('.tmp').load('student/active_student/components/extra_payments.php?group_student_id='+$group_student_id);
					});
				}
			}
		});
	}
});

$(document).on('click', '#open-add-new-student-form', function() {
	$(this).hide();
	$('#add-new-student').show();
});

$(document).on('click', '.cancel-add-new-student-form', function() {
	$(this).parents('form').hide();
	$('#open-add-new-student-form').show();
});

$(document).on('keyup', '#add-new-student #phone', function() {
	$phone = $(this).val();
	$current_element = $(this);
	if ($phone.length == 10) {
		$.ajax({
			url: 'student/controller.php?check_phone_exists&phone='+$phone,
			beforeSend:function() {
				$current_element.parents('.form-group').find('#phone-err').text('Загрузка...');
			},
			success:function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					$current_element.parents('.form-group').find('#phone-err').text($json.message);
				}
			}
		});
	} else {
		$current_element.parents('.form-group').find('#phone-err').text("");
	}
});

$(document).on('keyup', '#add-new-student #promo-code', function() {
	$(this).val($(this).val().toUpperCase());
	$promo_code = $(this).val();
	$current_element = $(this);

	if ($promo_code.length == 6) {
		$.ajax({
			url: 'student/controller.php?check_promo_code&promo_code='+$promo_code,
			beforeSend:function() {
				$current_element.parents('.form-group').find('#promo-code-info').text('Загрузка...');
			},
			success:function($data) {
				console.log($data);
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					$current_element.parents('.form-group').find('#promo-code-info').text($json.message);
				}
			}
		});
	} else {
		$current_element.parents('.form-group').find('#promo-code-info').text("");
	}
});

$(document).on('click', '.copy-phone-number-btn', function() {
	$phone_number = $(this).parents('.short_info').find('.phone-number').text();

	$text_area = document.createElement('textarea');
	$text_area.value = $phone_number;
	document.body.appendChild($text_area);
	$text_area.select();
	document.execCommand('Copy');
	$text_area.remove();
	
	lightAlert($(this).parents('td'), 'lightBlue', 0, 500);
});
