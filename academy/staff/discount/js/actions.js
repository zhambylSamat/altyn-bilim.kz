$(document).on('click', '#open-new-discount-form', function() {
	$('#new-discount-form').show();
	$(this).hide();
});

$(document).on("click", '#new-discount-form .cancel-new-discount', function() {
	$('#new-discount-form').hide();
	$('#open-new-discount-form').show();
});

$(document).on('click', '#open-set-discount-for-gs-form', function() {
	$(this).hide();
	$('#set-discount-for-gs-form').show();
});

$(document).on('click', '#set-discount-for-gs-form .cancel-form', function() {
	$('#set-discount-for-gs-form').hide();
	$('#open-set-discount-for-gs-form').show();
});

$(document).on('change', '#discount-type', function() {
	$val = $(this).val();

	if ($val == '') {
		$('#new-discount-form #discount-month').attr('disabled', 'disabled');
		$('#new-discount-form #discount-month').val('');

		$('#new-discount-form #amount').attr('disabled', 'disabled');
		$('#new-discount-form #amount').val('');
		$('#new-discount-form #amount').parents('.input-group').find('.input-group-addon').html('');
		$('#new-discount-form #amount').removeAttr('max');
	} else {
		$('#new-discount-form #amount').removeAttr('disabled');
		$addon = '';
		$max = 0;
		if ($val == 'percent') {
			$addon = '%';
			$max = '100';
		} else if ($val == 'money') {
			$addon = 'KZT';
			$max = '50000';
		}
		$('#new-discount-form #amount').parents('.input-group').find('.input-group-addon').html($addon);
		$('#new-discount-form #amount').attr('max', $max);
	}
});

$(document).on('keyup', '#new-discount-form #amount', function() {
	$val = $(this).val();

	if ($val == "") {
		$('#new-discount-form #discount-month').attr('disabled', 'disabled');
		$('#new-discount-form #discount-month').val('');
	} else {
		$('#new-discount-form #discount-month').removeAttr('disabled');
	}
});


$(document).on('keyup', '#set-discount-for-gs-form input[name="phone[]"]', function() {
	$phone = $(this).val();

	$current_element = $(this);
	$parent = $current_element.parents('.form-group');

	if ($phone.length == 10) {
		
		$.when(get_student_by_phone($current_element)).done(function($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				$parent.find('.phone-info').removeClass('text-warning');
				if ($json.info !== undefined) {
					$phone_count = {};
					$('.student-info').find('input[name="phone[]"]').each(function() {
						$tmp_phone = $(this).val();
						if ($phone_count[$tmp_phone] === undefined) {
							$phone_count[$tmp_phone] = 1;
						} else {
							$phone_count[$tmp_phone]++;
						}
					});
					$max = 0;
					for ($count in $phone_count) {
						$max = ($max < parseFloat($count)) ? parseFloat($count) : $max;
					}
					$already_got_phone = $phone_count[$phone] > 1 ? true : false;
					if (!$already_got_phone) {
						$text = 'Оқушы: '+$json.info.last_name+' '+$json.info.first_name;
						$parent.find('input[name="std-id[]"]').val($json.info.student_id);
						$parent.find('.phone-info').text($text);
						$parent.find('.phone-info').addClass('text-success');
						$group_element = $parent.parents('tr').find('.student-group-info');
						$.when(get_student_group_with_discount($json.info.student_id, $group_element)).done(function($data) {
							$group_json = $.parseJSON($data);
							$html = "<center><table class='table table-striped table-bordered'>";
								$.each($group_json.info, function($index, $element) {
									$group_type = "";
									if ($element.is_army_group) {
										$group_type = " (Армия)";
									} else if ($element.is_marathon_group) {
										$group_type = " (Марафон)";
									}
									if ($element.discount_title == '') {
										$html += "<tr>";
											$html += "<td><input type='checkbox' name='group_student["+$json.info.student_id+"][]' value='"+$index+"'></td>";
											$html += "<td>"+$element.group_name+$group_type+"</td>";
										$html += "</tr>";
									} else {
										$html += "<tr>";
											$html += "<td colspan='2'><center><b>"+$element.group_name+$group_type+' -> '+$element.discount_title+"</b></center></td>";
										$html += "</tr>";
									}
								});
							$html += "</table></center>";
							$group_element.html($html);
						});
					} else {
						$parent.find('.phone-info').text('Бұндай номермен оқушы уже таңдап қойдың');
						$parent.find('.phone-info').addClass('text-warning');
					}
				} else {
					$parent.find('.phone-info').text('Бұндай номермен оқушы тіркелмеген');
					$parent.find('.phone-info').addClass('text-danger');
				}
			}
		});

	} else {
		$parent.find('.phone-info').removeClass('text-danger');
		$parent.find('.phone-info').removeClass('text-warning');
		$parent.find('.phone-info').removeClass('text-success');
		$parent.find('.phone-info').text('');
		$parent.find('input[name="std-id[]"]').val('');
		$parent.parents('tr').find('.student-group-info').html('');
	}
});

function get_student_by_phone ($element) {
	return $.ajax({
		type: "GET",
		url: "discount/controller.php?get_student_info_by_phone&phone="+$phone,
		cache: false,
		beforeSend: function() {
			$element.parents('.form-group').find('.phone-info').val('Загрузка...');
			$element.parents('.form-group').find('.phone-info').addClass('text-warning');
		}
	}); 
}

function get_student_group_with_discount ($student_id, $element) {
	return $.ajax({
		type: 'GET',
		url: 'discount/controller.php?get_student_group_with_discount&student_id='+$student_id,
		cache: false,
		beforeSend: function() {
			$element.text('Загрузка...');
		}
	});
}

$(document).on("click", '.add-student-group', function() {
	$html = "<tr>";
		$html += "<td class='student-info' style='width: 46%; padding: 2%;'>";
			$html += "<div class='form-group'>";
				$html += "<label for='phone' class='control-label col-md-4 col-xs-6'>";
					$html += "Оқушының телефоны:";
				$html += "</label>";
				$html += "<div class='col-md-7 col-sm-7 col-xs-10'>";
					$html += "<div class='input-group'>";
						$html += "<div class='input-group-addon'>+7</div>";
						$html += "<input type='number' required  max='7999999999' min='7000000000' step='1' name='phone[]' class='form-control' id='phone' placeholder='Телефон нөмірін енгіз'' value=''>";
						$html += "<input type='hidden' name='std-id[]' value=''>";
					$html += "</div>"; // .input-group
					$html += "<b class='phone-info'></b>";
				$html += "</div>"; // .col-...
				$html += "<div class='col-md-1 col-sm-1 col-xs-2' style='padding: 0;'>";
					$html += "<button type='button' class='btn btn-xs btn-danger remove-current-row' title='Удалить'><i class='fas fa-trash-alt'></i></button>";
				$html += "</div>";
			$html += "</div>"; // .form-group
		$html += "</td>";
		$html += "<td class='student-group-info' style='width: 46%; padding: 2%;'></td>";
	$html += "</tr>";
	$(this).parents('tr').prev().after($html);
});

$(document).on('click', '.remove-current-row', function() {
	if (confirm('Удалить?')) {
		$(this).parents('tr').remove();
	}
});

function validate_set_discount() {

	$groups_choosen = false;
	$student_duplicate = false;
	$('input[name="std-id[]').each(function() {
		$student_id = $(this).val();
		if ($student_id != '') {
			$tmp_group_chose = false;
			$('input[name="group_student['+$student_id+'][]"]').each(function() {
				if ($(this).prop('checked') && !$tmp_group_chose) {
					console.log($(this).val(), $student_id);
					$tmp_group_chose = true;
				}
			});
			if (!$groups_choosen && $tmp_group_chose) {
				$groups_choosen = true;
			}
		} else if (!$student_duplicate) {
			$student_duplicate = true;
		}
	});

	if ($student_duplicate) {
		alert('Бірдей номермен бірнеше оқушы таңдалынды');
		return false;
	} else if (!$groups_choosen) {
		alert('Группаны міндетті түрде таңдау керек!');
		return false;
	}

	return true;
}
