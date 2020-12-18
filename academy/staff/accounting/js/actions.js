$(document).on('click', '.category-coming-btn', function() {
	$.when(get_all_category_coming()).done(function($data) {
		$json = $.parseJSON($data);
		$html = "<table class='table table-striped table-bordered'>";
			$.each($json.info, function($cc_id, $val) {
				$html += "<tr>";
					$html += "<td>";
						$html += "<div class='col-md-6 col-sm-6 col-xs-8'>";
							$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$val.title+"'>";
							$html += "<input type='hidden' name='cc_id' value='"+$cc_id+"'>";
							$html += "<input type='hidden' value='parent_id' value='0'>";
						$html += "</div>";
						$html += "<div class='col-md-6 col-sm-6 col-xs-4'>";
							// $html += "<button class='btn btn-xs btn-danger remove-parent-category'><i class='fas fa-trash-alt'></i></button>";
						$html += "</div>";
					$html += "</td>";
					$html += "<td>";
						$html += "<div class='subcategory-list'>";
							$.each($val.subcategory, function($sub_cc_id, $sub_val) {
								$html += "<div class='subcategory'>";
									$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
										$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$sub_val.title+"'>";
										$html += "<input type='hidden' name='cc_id' value='"+$sub_cc_id+"'>";
										$html += "<input type='hidden' name='parent_id' value='"+$cc_id+"'>";
									$html += "</div>";
								$html += "</div>";
							});
							$html += "<div class='subcategory'><button class='btn btn-sm btn-info btn-block add-subcategory-row'>+ Подкатегория енгізу</button></div>";
						$html += "</div>";
					$html += "</td>";
				$html += "</tr>";
			});
			$html += "<tr>";
				$html += "<td colspan='2'>";
					$html += "<button class='btn btn-md btn-info btn-block add-category-coming-row'>+ Категория қосу</button>";
				$html += "</td>"
			$html += "</tr>";
		$html += "</table>";
		$('#category-coming-modal .modal-body').html($html);
	});
});

function get_all_category_coming () {
	return $.ajax({
		type: 'GET',
		url: 'accounting/controller.php?get_all_category_coming',
		cache: false,
		beforeSend: function() {
			$('#category-coming-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
		}
	});
}

$(document).on('click', '.add-category-coming-row', function() {
	$(this).parents('tr').hide();
	$html = "<tr>";
		$html += "<td>";
			$html += "<div class='col-md-6 col-sm-6 col-xs-8'>";
				$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты'>";
				$html += "<input type='hidden' name='parent_id' value='0'>";
			$html += "</div>";
			$html += "<div class='col-md-6 col-sm-6 col-xs-4'>";
				$html += "<button class='btn btn-sm btn-success add-category-coming-btn'>Сақтау</button> ";
				$html += "<button class='btn btn-xs btn-danger remove-category-row'><i class='fas fa-times'></i></button>";
			$html += "</div>";
		$html += "</td>";

		$html += "<td>";
			$html += "N/A";
		$html += "</td>";
	$html += "</tr>";
	$(this).parents('tr').before($html);
});

$(document).on('click', '.remove-category-row', function() {
	$(this).parents('tr').next().show();
	$(this).parents('tr').remove();
});


$(document).on('click', '.add-category-coming-btn', function() {
	$current_element = $(this);
	$title = $(this).parent().prev().find('input[name=title]').val();
	$parent_id = $(this).parent().prev().find('input[name=parent_id]').val();
	if ($title != '') {
		$.ajax({
			type: 'GET',
			url: 'accounting/controller.php?add-category-coming&parent_id='+$parent_id+'&title='+$title,
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('table'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					if ($parent_id == 0) {
						$html = "<td>";
							$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
								$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$json.info.title+"'>";
								$html += "<input type='hidden' name='cc_id' value='"+$json.info.category_coming_id+"'>";
								$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
							$html += "</div>";
						$html += "</td>";
						$html += "<td>";
							$html += "<div class='subcategory-list'>";
								$html += "<div class='subcategory'><button class='btn btn-sm btn-info btn-block add-subcategory-row'>+ Подкатегория енгізу</button></div>";
							$html += "</div>";
						$html += "</td>";
						$current_element.parents('tr').next().show();
						$tr_element = $current_element.parents('tr');
						$tr_element.html($html);
						lightAlert($tr_element, 'green', 0, 500);
					} else {
						$html = "<div class='subcategory'>";
							$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
								$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$json.info.title+"'>";
								$html += "<input type='hidden' name='cc_id' value='"+$json.info.category_coming_id+"'>";
								$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
							$html += "</div>";
						$html += "</div>";
						$current_element.parents('.subcategory').next().show();
						$subcategory_element = $current_element.parents('.subcategory');
						$subcategory_element.html($html);
						lightAlert($subcategory_element, 'green', 0, 500);
					}
				}
			}
		});
	} else {
		alert('Категорияның атын жаз!');
	}
});

$(document).on('click', '.add-subcategory-row', function() {
	$(this).parents('.subcategory').hide();
	$parent_id = $(this).parents('td').prev().find('input[name=cc_id]').val();

	$html = "<div class='subcategory'>"; 
		$html += "<div class='col-md-6 col-sm-6 col-xs-8'>";
			$html += "<input type='text' name='title' placeholder='Категорияның аты' class='form-control'>";
			$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
		$html += "</div>";
		$html += "<div class='col-md-6 col-sm-6 col-xs-4'>";
			$html += "<button class='btn btn-sm btn-success add-category-coming-btn'>Сақтау</button> ";
			$html += "<button class='btn btn-xs btn-danger remove-subcategory-row'><i class='fas fa-times'></i></button>";
		$html += "</div>";
	$html += "</div>";
	$(this).parents('.subcategory').before($html);
});

$(document).on('click', '.remove-subcategory-row', function() {
	$(this).parents('.subcategory').next().show();
	$(this).parents('.subcategory').remove()
});


// .. expenditure start


$(document).on('click', '.category-expenditure-btn', function() {
	render_category_expenditure_table();
});

function render_category_expenditure_table () {
	$.when(get_all_category_expenditure()).done(function($data) {
		$json = $.parseJSON($data);
		$html = "<table class='table table-striped table-bordered'>";
			$.each($json.info, function($cc_id, $val) {
				$html += "<tr>";
					$html += "<td>";
						$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
							$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$val.title+"'>";
							$html += "<input type='hidden' name='cc_id' value='"+$cc_id+"'>";
							$html += "<input type='hidden' value='parent_id' value='0'>";
						$html += "</div>";
						$html += "<div class='col-md-6 col-sm-6 col-xs-4'>";
							// $html += "<button class='btn btn-xs btn-danger remove-parent-category'><i class='fas fa-trash-alt'></i></button>";
						$html += "</div>";
					$html += "</td>";
					$html += "<td>";
						$html += "<div class='subcategory-list'>";
							$html += "<table class='table table-striped table-bordered'>";
							$.each($val.subcategory, function($sub_cc_id, $sub_val) {
								$html += "<tr class='subcategory'>";
									$html += "<td>";
										$html += "<input type='text' name='title' data-default='"+$sub_val.title+"' class='form-control' placeholder='Категорияның аты' value='"+$sub_val.title+"'>";
										$html += "<input type='hidden' name='cc_id' value='"+$sub_cc_id+"'>";
										$html += "<input type='hidden' name='parent_id' value='"+$cc_id+"'>";
									$html += "</td>";
									$html += "<td>";
										$html += "<input type='number' name='budget' class='form-control' data-default='"+$sub_val.budget+"' min='0.0' step='0.01' value='"+$sub_val.budget+"'>";
									$html += "</td>";
									$html += "<td>";
										$html += "<button class='btn btn-sm btn-success update-category-expenditure' style='display: none;'>Сақтау</button>";
										$html += "<button class='btn btn-xs btn-warning reset-update-category-expenditure' style='display: none;'>Отмена</button>";
									$html += "</td>";
								$html += "</tr>";
							});
							$html += "</table>";
							$html += "<div class='subcategory-btn'><button class='btn btn-sm btn-info btn-block add-subcategory-expenditure-row'>+ Подкатегория енгізу</button></div>";
						$html += "</div>";
					$html += "</td>";
				$html += "</tr>";
			});
			$html += "<tr>";
				$html += "<td colspan='2'>";
					$html += "<button class='btn btn-md btn-info btn-block add-category-expenditure-row'>+ Категория қосу</button>";
				$html += "</td>"
			$html += "</tr>";
		$html += "</table>";
		$('#category-expenditure-modal .modal-body').html($html);
	});
}

function get_all_category_expenditure () {
	return $.ajax({
		type: 'GET',
		url: 'accounting/controller.php?get_all_category_expenditure',
		cache: false,
		beforeSend: function() {
			$('#category-expenditure-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
		}
	});
}

$(document).on('click', '.add-category-expenditure-row', function() {
	$(this).parents('tr').hide();
	$html = "<tr>";
		$html += "<td>";
			$html += "<div class='col-md-6 col-sm-6 col-xs-8'>";
				$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты'>";
				$html += "<input type='hidden' name='parent_id' value='0'>";
			$html += "</div>";
			$html += "<div class='col-md-6 col-sm-6 col-xs-4'>";
				$html += "<button class='btn btn-sm btn-success add-category-expenditure-btn'>Сақтау</button> ";
				$html += "<button class='btn btn-xs btn-danger remove-category-expenditure-row'><i class='fas fa-times'></i></button>";
			$html += "</div>";
		$html += "</td>";

		$html += "<td>";
			$html += "N/A";
		$html += "</td>";
	$html += "</tr>";
	$(this).parents('tr').before($html);
});

$(document).on('click', '.remove-category-expenditure-row', function() {
	$(this).parents('tr').next().show();
	$(this).parents('tr').remove();
});


$(document).on('click', '.add-category-expenditure-btn', function() {
	$current_element = $(this);
	$title = $(this).parents('.subcategory').find('input[name=title]').val();
	$budget = $(this).parents('.subcategory').find('input[name=budget]').val();
	$parent_id = $(this).parents('.subcategory').find('input[name=parent_id]').val();
	if ($title != '') {
		$.ajax({
			type: 'GET',
			url: 'accounting/controller.php?add-category-expenditure&parent_id='+$parent_id+'&title='+$title+'&budget='+$budget,
			cache: false,
			beforeSend: function() {
				set_load($current_element.parents('.modal-body'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					if ($parent_id == 0) {
						$html = "<td>";
							$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
								$html += "<input type='text' name='title' class='form-control' placeholder='Категорияның аты' value='"+$json.info.title+"'>";
								$html += "<input type='hidden' name='cc_id' value='"+$json.info.category_expenditure_id+"'>";
								$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
							$html += "</div>";
						$html += "</td>";
						$html += "<td>";
							$html += "<div class='subcategory-list'>";
								$html += "<div class='subcategory'><button class='btn btn-sm btn-info btn-block add-subcategory-expenditure-row'>+ Подкатегория енгізу</button></div>";
							$html += "</div>";
						$html += "</td>";
						$current_element.parents('tr').next().show();
						$tr_element = $current_element.parents('tr');
						$tr_element.html($html);
						lightAlert($tr_element, 'green', 0, 500);
					} else {
						$html = "<td>";
								$html += "<input type='text' name='title' data-default='"+$json.info.title+"' class='form-control' placeholder='Категорияның аты' value='"+$json.info.title+"'>";
								$html += "<input type='hidden' name='cc_id' value='"+$json.info.category_expenditure_id+"'>";
								$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
						$html += "</td>";
						$html += "<td>";
							$html += "<input type='number' name='budget' data-default='"+$json.info.budget+"' class='form-control' min='0.0' step='0.01' value='"+$json.info.budget+"'>";
						$html += "</td>";
						$html += "<td>";
							$html += "<button class='btn btn-sm btn-success update-category-expenditure' style='display: none;'>Сақтау</button>";
							$html += "<button class='btn btn-xs btn-warning reset-update-category-expenditure' style='display: none;'>Отмена</button>";
						$html += "</td>";

						$subcategory_element = $current_element.parents('.subcategory');
						$subcategory_element.html($html);
						lightAlert($subcategory_element, 'green', 0, 500);
					}
				}
			}
		});
	} else {
		alert('Категорияның атын жаз!');
	}
});

$(document).on('click', '.add-subcategory-expenditure-row', function() {
	// $(this).parents('.subcategory').hide();
	$parent_id = $(this).parents('td').prev().find('input[name=cc_id]').val();

	$html = "<tr class='subcategory'>"; 
		$html += "<td>";
			$html += "<input type='text' name='title' placeholder='Категорияның аты' class='form-control'>";
			$html += "<input type='hidden' name='parent_id' value='"+$parent_id+"'>";
		$html += "</td>";
		$html += "<td>";
			$html += "<input type='number' class='form-control' min='0.0' step='0.01' name='budget' value='0.00'>";
		$html += "</td>";
		$html += "<td>";
			$html += "<button class='btn btn-sm btn-success add-category-expenditure-btn'>Сақтау</button> ";
			$html += "<button class='btn btn-xs btn-danger remove-subcategory-expenditure-row'><i class='fas fa-times'></i></button>";
		$html += "</td>";
	$html += "</tr>";
	// $(this).parents('.subcategory').before($html);
	$(this).parents('.subcategory-list').find('.subcategory').last().after($html);
});

$(document).on('click', '.remove-subcategory-expenditure-row', function() {
	// $(this).parents('.subcategory').next().show();
	$(this).parents('.subcategory-list').find('.subcategory').last().remove();
});

$(document).on('focus', '.datePicker', function() {
	$(this).datepicker({
		format: 'dd.mm.yyyy',
		language: "ru",
		todayHighlight: true,
		autoclose: true
	});
});

$(document).on('focus', '.monthPicker', function() {
	$(this).datepicker({
		format: 'mm.yyyy',
		language: 'ru',
		viewMode: "months", 
    	minViewMode: "months",
    	autoclose: true
	});
});

$(document).on('focus', '.money-transfer-datepicker', function() {
	$(this).datepicker({
		format: 'dd.mm.yyyy',
		language: "ru",
		todayHighlight: true,
		autoclose: true
	});
});

$(document).on('submit', '#add-new-amount', function($e) {
	$e.preventDefault();
	$month = $('#account-table').find('input[name=selected-month]').val();
	$year = $('#account-table').find('input[name=selected-year]').val();
	$ab_root = $('input[name=ab-root]').val();

	$category_id = $(this).find('input[name=category_id]');
	$group_type = $(this).find('input[name=group_type]');
	if ($category_id[0] !== undefined || $group_type[0] !== undefined) {
		$.ajax({
			type: 'POST',
			method: 'POST',
			url: 'accounting/controller.php?add_new_amount',
			data: new FormData(this),
			contentType: false,
		    cache: false,
			processData:false,
			beforeSend: function() {
				set_load('#account-table');
			},
			success: function($data) {
				$json = $.parseJSON($data);
				$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
					remove_load();
					lightAlert($('#account-table'), 'green', 0, 300);
					clear_add_new_amount_form();
				});
			}
		});
	} else {
		alert('Категорияны таңда!');
	}
});

$(document).on('click', '.cancel-add-new-amount', function() {
	clear_add_new_amount_form();
});

function clear_add_new_amount_form() {
	$element = $('#add-new-amount');

	$element.find('input[name=date]').val('');
	$element.find('input[name=account-amount]').val('');
	$element.find('select[name=money-type]').val('');
	$element.find('#category-list').html("");

	$accounting_type = '';
	$('input[name=account-type]').each(function() {
		if ($(this).prop('checked')) {
			$accounting_type = $(this).val();
		}
	});
	if ($accounting_type != '') {
		reset_money_type($accounting_type);
	}
}

$(document).on('change', 'input[name=account-type]', function() {
	if ($(this).prop('checked')) {
		reset_money_type($(this).val());
	}
	$('#add-new-amount').find('#category-list').html('');
});

function reset_money_type ($accounting_type) {
	$('#money-type').find('option').each(function() {
		if ($(this).val() == 2 && $accounting_type == 'coming') {
			$('#money-type').val($(this).val());
		} else if($(this).val() == 3 && $accounting_type == 'expenditure') {
			$('#money-type').val($(this).val());
		}
	});	
}

$(document).on('click', '.open-category-modal', function() {
	$account_type = '';
	$('input[name=account-type]').each(function() {
		if ($(this).prop('checked')) {
			$account_type = $(this).val()
		}
	});

	$date = '';
	$date_unformatted = $('#add-new-amount').find('input[name=date]').val();
	if ($date_unformatted != '') {
		$date_split = $date_unformatted.split('.');
		$date = $date_split[2]+'-'+$date_split[1]+'-'+$date_split[0];
	}

	$title = '';
	if ($account_type == 'coming') {
		$title = 'Категория | Приход';
	} else if ($account_type == 'expenditure') {
		$title = 'Категория | Расход';
	}

	$('#category-choose .modal-header .modal-title').text($title);
	$('#category-choose .modal-body').html("<center><h3>Загрузка...</h3></center>");

	$.when(get_categories_by_account_type($account_type, $date)).done(function($data) {
		$json = $.parseJSON($data);
		$category_html = "<div class='col-md-6 col-sm-6 col-xs-12'><div class='btn-group-vertical category-action-list'>";
		$subcategory_html = "<div class='col-md-6 col-sm-6 col-xs-12' id='subcategory-info'></div>";
		if ($json.info.static.length > 0) {
			$.each($json.info.static, function($index, $category) {
				$category_html += "<label class='btn btn-info'>";
					$category_html += "<input type='radio' name='category' class='pull-left' value='"+$category.group_type+"'>";
					$category_html += "<input type='hidden' name='category_type' value='group_type'>";
					$category_html += $category.group_type;
				$category_html += "</label>";
				if (Object.keys($category.subcategories).length > 0) {
					$subcategory_html += "<div class='col-md-6 col-sm-6 col-xs-12'><div class='btn-group-vertical subcategory-action-list' data-id='"+$category.group_type+"'>";
					$.each($category.subcategories, function($index, $subcategory) {
						$subcategory_html += "<label class='btn btn-info'>";
							$subcategory_html += "<input type='checkbox' name='subcategory' class='pull-left' value='"+$subcategory.subject_title+"'>";
							$subcategory_html += "<input type='hidden' name='category_type' value='subject_title'>";
							$subcategory_html += $subcategory.subject_title;
						$subcategory_html += "</label>";
					});
					$subcategory_html += "</div></div>";
				}
			});
		}
		if (Object.keys($json.info.dynamic).length > 0) {
			$.each($json.info.dynamic, function($index, $category) {
				$category_html += "<label class='btn btn-info'>";
					$category_html += "<input type='radio' name='category' class='pull-left' value='"+$category.id+"'>";
					$category_html += "<input type='hidden' name='category_type' value='category'>";
					if ($category.exceeded !== undefined && $category.exceeded == 1) {
						$category_html += "<i class='fas fa-exclamation-triangle' style='color:red;'></i>";
					}
					$category_html += $category.title;
				$category_html += "</label>";
				if (Object.keys($category.subcategories).length > 0) {
					$subcategory_html += "<div class='col-md-6 col-sm-6 col-xs-12'><div class='btn-group-vertical subcategory-action-list'  data-id='"+$category.id+"'>";
					$.each($category.subcategories, function($index, $subcategory) {
						$subcategory_html += "<label class='btn btn-info'>";
							$subcategory_html += "<input type='checkbox' name='subcategory' class='pull-left' value='"+$subcategory.id+"'>";
							$subcategory_html += "<input type='hidden' name='category_type' value='subject_title'>";
							if ($subcategory.exceeded !== undefined && $subcategory.exceeded == 1) {
								$subcategory_html += "<i class='fas fa-exclamation-triangle' style='color:red;'></i>";
							}
							$subcategory_html += $subcategory.title;
						$subcategory_html += "</label>";
					});
					$subcategory_html += "</div></div>";
				}
			});
		}
		$category_html += "</div></div>";
		$html = "<div class='row'>" + $category_html + $subcategory_html + "</div>";
		$('#category-choose .modal-body').html($html);
	});
});

function get_categories_by_account_type($account_type, $date) {
	return $.ajax({
		type: 'GET',
		cache: false,
		url: 'accounting/controller.php?get_categories&account_type='+$account_type+'&date='+$date
	});
}

$(document).on('change', 'input[name=category]', function() {
	if ($(this).prop('checked')) {
		$val = $(this).val();
		$text = $(this).parents('label').text();
		$exists = false;
		$('.subcategory-action-list').each(function() {
			if ($(this).data('id') == $val) {
				$(this).show();
				$exists = true;
			} else {
				$(this).hide();
			}
		});
		$html = "<center>"+$text+"</center>";
		if (!$exists) {
			// $html += "<center>Нет подкатегорий</center>";
			$('#category-choose').modal('hide');
		}
		$('#subcategory-info').html($html);

		$html = "";
		$category_type = $(this).parents('label').find('input[name=category_type]').val();
		$html += "<span id='category-title'>"+$text+"</span>";
		if ($category_type == 'group_type') {
			$html += "<input type='hidden' name='category_type' value='group_type'>";
			$html += "<input type='hidden' name='group_type' value='"+$val+"'>";
		} else if ($category_type == 'category') {
			$html += "<input type='hidden' name='category_type' value='category'>";
			$html += "<input type='hidden' name='category_id' value='"+$val+"'>";
		}
		$html += "<input type='hidden' name='has_subcategory' value='0'>";

		$('#add-new-amount').find('#category-list').html($html);
	}
});

$(document).on('change', 'input[name=subcategory]', function() {
	$category_list_element = $('#add-new-amount').find('#category-list');
	$category_list_element.find('#subcategory-title').remove();
	$category_list_element.find('input[name=subject_title]').remove();
	$category_list_element.find('input[name=subcategory_id]').remove();
	if ($(this).prop('checked')) {
		$val = $(this).val();
		$text = $(this).parents('label').text();
		$(this).parents('.subcategory-action-list').find('input[name=subcategory]').each(function() {
			if ($(this).val() != $val) {
				$(this).prop('checked', false);
			}
		});

		$category_type = $category_list_element.find('input[name=category_type]').val();
		$html = "<span id='subcategory-title'> | "+$text+"</span>";
		if ($category_type == 'group_type') {
			$html += "<input type='hidden' name='subject_title' value='"+$val+"'>";
		} else if ($category_type == 'category') {
			$html += "<input type='hidden' name='subcategory_id' value='"+$val+"'>";
		}
		$category_list_element.find('input[name=has_subcategory]').val('1');
		$category_list_element.append($html);
	} else {
		$category_list_element.find('input[name=has_subcategory]').val('0');
	}
	$('#category-choose').modal('hide');
});

$(document).on('click', '.choose-period', function() {
	choose_period();
});

$(document).on('change', 'input[name=accounting-period]', function() {
	choose_period();
});

function choose_period() {
	$val = $('input[name=accounting-period]').val().split('.');
	$month = $val[0];
	$year = $val[1];
	$ab_root = $('input[name=ab-root]').val();
	set_load('#account-table');
	$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
		remove_load();
		lightAlert($('#account-table'), 'green', 0, 300);
		clear_add_new_amount_form();
	});
}

$(document).on('click', '.show-comings, .show-expenditures', function() {
	$('#comings-expenditures-list').modal('show');
	$money_type_id = $(this).data('money-type-id');
	$day_str = $(this).data('day-str');
	$month = $(this).data('month');
	$year = $(this).data('year');
	$day = $(this).data('day');
	if ($(this).hasClass('show-comings')) {
		$('#comings-expenditures-list .modal-title').html("<center><h3>Приход "+$day_str+"</h3></center>");
		set_comings_expenditures_form($money_type_id, $year, $month, $day, 'coming');
	} else if ($(this).hasClass('show-expenditures')) {
		$('#comings-expenditures-list .modal-title').html("<center><h3>Расход "+$day_str+"</h3></center>");
		set_comings_expenditures_form($money_type_id, $year, $month, $day, 'expenditure')
	}
});

function set_comings_expenditures_form ($money_type_id, $year, $month, $day, $accounting_type) {
	$('#comings-expenditures-list .modal-body').html("<center>Загрузка...</center>");
	$.when(get_comings_expenditures_by_date($year+'-'+$month+'-'+$day, $money_type_id, $accounting_type)).done(function($data) {
		$json = $.parseJSON($data);
		if ($json.success) {
			$html = "";
			$.each($json.info, function($id, $val) {
				$html += "<form class='edit-amount form-horizontal'>";
					$html += "<input type='hidden' name='accounting_type' value='"+$accounting_type+"'>";
					$html += "<input type='hidden' name='id' value='"+$id+"'>";
					$html += "<input type='hidden' name='money-type-id' value='"+$money_type_id+"'>";
					$html += "<input type='hidden' name='day' value='"+$day+"'>";
					$html += "<input type='hidden' name='month' value='"+$month+"'>";
					$html += "<input type='hidden' name='year' value='"+$year+"'>";
					$html += "<div class='form-group'>";
						$html += "<label class='control-label col-md-4 col-sm-4 col-xs-6'>";
							$html += "Сумма: ";
						$html += "</label>";
						$html += "<div class='col-md-8 col-sm-8 col-xs-6'>";
							$html += "<input type='number' name='account-amount' class='form-control amount-input' min='0.01' step='0.01' requrired placeholder='Сумма' value='"+$val.amount+"'>";
							$html += "<span class='amount-txt'>"+parseFloat($val.amount)+"</span>";
							if ($json.has_create_edit_remove_access) {
								if ($val.money_transfer_id == null) {
									$html += " <div class='amount-action-1 pull-right'>";
										if ($accounting_type == 'coming') {
											$html += "<input type='hidden' name='group_type' value='"+$val.group_type+"'>";
											$html += "<input type='hidden' name='subject_title' value='"+$val.subject_title+"'>";
										}
										$html += "<input type='hidden' name='category_id' value='"+$val.category_id+"'>";
										$html += "<input type='hidden' name='category_title' value='"+$val.category_title+"'>";
										if ($val.subcategory_id != 0) {
											$html += "<input type='hidden' name='subcategory_id' value='"+$val.subcategory_id+"'>";
											$html += "<input type='hidden' name='subcategory_title' value='"+$val.subcategory_title+"'>";
										}
										$html += "<input type='hidden' name='amount' value='"+$val.amount+"'>";
										$html += "<button type='button' class='btn btn-xs btn-default repeat-accounting'><i class='fas fa-redo-alt'></i></button> ";
										$html += "<button type='button' class='btn btn-xs btn-info change-amount'>Өзгерту</button> ";
										$html += "<button type='button' class='btn btn-xs btn-danger remove-amount'><i class='fas fa-trash-alt'></i></button>";
									$html += "</div>";
									$html += "<div class='amount-action-2'>";
										$html += "<button type='submit' class='btn btn-xs btn-success'>Сақтау</button>";
										$html += "<button type='button' class='btn btn-xs btn-warning cancel-amount'>Отмена</button>";
									$html += "</div>"
								} else {
									$html += " <div class='cancel-money-transfer pull-right'>";
										$html += "<button type='button' class='btn btn-xs btn-danger cancel-money-transfer-btn' data-id='"+$val.money_transfer_id+"'>Отменить перевод</button>";
									$html += "</div>"
								}
							}
							$html += "<br>";
							if ($accounting_type == 'coming' && $val.money_transfer_id != null) {
								$html += "<span>Перевод<span>";
							} else if ($accounting_type == 'expenditure' && $val.money_transfer_id != null) {
								$html += "<span>Комиссия</span>";
							} else {
								$html += "<span>"+$val.title+"</span>";
							}
							
						$html += "</div>";
					$html += "</div>";
				$html += "</form>";
			});
			$('#comings-expenditures-list .modal-body').html($html);
		} else {
			$('#comings-expenditures-list .modal-body').html("<center>ERROR</center>");
		}
	});
}

$(document).on('click', '.change-amount', function() {
	$(this).parents('.amount-action-1').hide();
	$(this).parents('.edit-amount').find('input[name=account-amount]').show();
	$(this).parents('.edit-amount').find('.amount-txt').hide();
	$(this).parents('.edit-amount').find('.amount-action-2').css({'display' : 'inline-block'});
});

$(document).on('click', '.cancel-amount', function() {
	$(this).parents('.amount-action-2').hide();
	$(this).parents('.edit-amount').find('.amount-action-1').css({'display' : 'inline-block'});
	$(this).parents('.edit-amount').find('input[name=account-amount]').hide();
	$(this).parents('.edit-amount').find('.amount-txt').show();
});

function get_comings_expenditures_by_date ($date, $money_type_id, $accounting_type) {
	if ($accounting_type == 'coming') {
		return $.ajax({
			type: 'GET',
			cache: false,
			url: 'accounting/controller.php?get_comings_by_date&date='+$date+'&money_type_id='+$money_type_id
		});
	} else if ($accounting_type == 'expenditure') {
		return $.ajax({
			type: 'GET',
			cache: false,
			url: 'accounting/controller.php?get_expenditures_by_date&date='+$date+'&money_type_id='+$money_type_id
		});
	}
}

$(document).on('submit', '.edit-amount', function($e) {
	$e.preventDefault();
	$money_type_id = $(this).find('input[name=money-type-id]').val();
	$year = $(this).find('input[name=year]').val();
	$month = $(this).find('input[name=month]').val();
	$day = $(this).find('input[name=day]').val();
	$accounting_type = $(this).find('input[name=accounting_type]').val();
	$ab_root = $('input[name=ab-root]').val();
	$current_element = $(this);
	$form_data = new FormData(this);
	$('#comings-expenditures-list').modal('hide');
	// $('#comings-expenditures-list').on('hidden.bs.modal', function() {
		$.ajax({
			type: 'POST',
			method: 'POST',
			url: 'accounting/controller.php?edit-amount',
			data: $form_data,
			contentType: false, 
			cache: false,
			processData: false,
			beforeSend: function() {
				// set_load($current_element);
				$('#comings-expenditures-list').modal('hide');
				set_load($('#account-table'));
			},
			success: function($data) {
				remove_load();
				$json = $.parseJSON($data);
				if ($json.success) {
					$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
						lightAlert($('#account-table'), 'green', 0, 300);
						clear_add_new_amount_form();
					});
				}
			}
		});
	// });
});	

$(document).on('click', '.remove-amount', function(){
	$current_element = $(this).parents('.edit-amount');
	$id = $current_element.find('input[name=id]').val();
	$money_type_id = $current_element.find('input[name=money-type-id]').val();
	$year = $current_element.find('input[name=year]').val();
	$month = $current_element.find('input[name=month]').val();
	$day = $current_element.find('input[name=day]').val();
	$accounting_type = $current_element.find('input[name=accounting_type]').val();
	$ab_root = $('input[name=ab-root]').val();
	$date = $year+'-'+$month+'-'+$day;
	if (confirm('Вы точно хотите удалить?')) {
		$('#comings-expenditures-list').modal('hide');
		// $('#comings-expenditures-list').on('hidden.bs.modal', function() {
			$.ajax({
				type: 'POST',
				cache: false,
				url: 'accounting/controller.php?remove-amount&id='+$id+'&money_type_id='+$money_type_id+'&date='+$date+'&accounting_type='+$accounting_type,
				beforeSend: function() {
					// set_load($current_element);
					set_load($('#account-table'));
				},
				success: function($data) {
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						// $('#comings-expenditures-list').modal('hide');
						// set_comings_expenditures_form ($money_type_id, $year, $month, $day, $accounting_type);
						$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
							lightAlert($('#account-table'), 'green', 0, 300);
							clear_add_new_amount_form();
						});
					}
				}
			});
		// });
	}
});

$(document).on('click', '.cancel-money-transfer-btn', function() {
	$current_element = $(this).parents('.edit-amount');
	$money_transfer_id = $(this).data('id');
	$money_type_id = $current_element.find('input[name=money-type-id]').val();
	$year = $current_element.find('input[name=year]').val();
	$month = $current_element.find('input[name=month]').val();
	$day = $current_element.find('input[name=day]').val();
	$accounting_type = $current_element.find('input[name=accounting_type]').val();
	$ab_root = $('input[name=ab-root]').val();
	$date = $year+'-'+$month+'-'+$day;

	if (confirm('Вы точно хотите отменить перевод?')) {
		$('#comings-expenditures-list').modal('hide');
		// $('#comings-expenditures-list').on('hidden.bs.modal', function() {
			$.ajax({
				type: 'GET',
				cache: false,
				url: 'accounting/controller.php?remove-money-transfer&money_transfer_id='+$money_transfer_id,
				beforeSend: function() {
					// set_load($current_element);
					set_load($('#account-table'));
				},
				success: function($data) {
					remove_load();
					$json = $.parseJSON($data);
					if ($json.success) {
						$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
							lightAlert($('#account-table'), 'green', 0, 300);
							clear_add_new_amount_form();
						});
					}
				}
			});
		// });
	}

	set_comings_expenditures_form ($money_type_id, $year, $month, $day, $accounting_type);
});

$(document).on('click', '.money-transfer-btn', function() {
	clear_money_transfer_form();
});

$(document).on('click', '.cancel-money-transfer', function() {
	clear_money_transfer_form();
});

function clear_money_transfer_form() {
	$('#money-transfer-form')[0].reset();
	// $('#money-transfer-form').find('select[name=from-money-type]').val('');
	// $('#money-transfer-form').find('select[name=to-money-type]').val('');
	// $('#money-transfer-form').find('input[name=money-transfer-date]').val('');
	// $('#money-transfer-form').find('input[name=fee]').val('');
	// $('#money-transfer-form').find('input[name=amount]').val('');
}

$(document).on('submit', '#money-transfer-form', function($e) {
	$e.preventDefault();
	$month = $('input[name=selected-month]').val();
	$year = $('input[name=selected-year]').val();
	$ab_root = $('input[name=ab-root]').val();
	$current_element = $(this);
	$form_data = new FormData(this);
	$('#money-transfer-modal').modal('hide');
	// $('#money-transfer-modal').on('hidden.bs.modal', function() {
	$.ajax({
		type: 'POST',
		method: 'POST',
		url: 'accounting/controller.php?money_transfer',
		data: $form_data,
		contentType: false, 
		cache: false,
		processData: false,
		beforeSend: function() {
			set_load($current_element);
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);
			if ($json.success) {
				$('#account-table').load($ab_root+'/academy/staff/accounting/components/account_table.php?month='+$month+'&year='+$year, function() {
					lightAlert($('#account-table'), 'green', 0, 300);
					clear_money_transfer_form();
					$('#money-transfer-modal').modal('hide');
				});
			}
		}
	});
	// });
});

$(document).on('click', '.repeat-accounting', function() {
	$parent_form = $(this).parents('.edit-amount');
	$action_box = $(this).parents('.amount-action-1');
	$day = $parent_form.find('input[name=day]').val();
	$month = $parent_form.find('input[name=month]').val();
	$year = $parent_form.find('input[name=year]').val();
	$money_type_id = $parent_form.find('input[name=money-type-id]').val();
	$accounting_type = $parent_form.find('input[name=accounting_type]').val();
	$category_id = $action_box.find('input[name=category_id]').val();
	$category_title = $action_box.find('input[name=category_title]').val();
	$amount = $action_box.find('input[name=amount]').val();

	$subcategory_id = $action_box.find('input[name=subcategory_id]').val();
	$subcategory_title = $action_box.find('input[name=subcategory_title]').val();

	$form_element = $('#add-new-amount');
	$form_element.find('input[name=account-type]').each(function() {
		if ($(this).val() == $accounting_type) {
			$(this).prop('checked', true);
		}
	});

	$form_element.find('input[name=date]').val($day+'.'+$month+'.'+$year);
	$form_element.find('input[name=account-amount]').val($amount);
	$form_element.find('#money-type').val($money_type_id);

	$category_list_element = $form_element.find('#category-list');

	$html = "";
	if ($accounting_type == 'coming') {
		$group_type = $action_box.find('input[name=group_type]').val();
		$subject_title = $action_box.find('input[name=subject_title]').val();
	}

	if ($category_id == 0) {
		// group_type / subject_title
		$html += "<span id='category-title'>"+$group_type+"</span>";
		$html += "<input type='hidden' name='category_type' value='group_type'>";
		$html += "<input type='hidden' name='group_type' value='"+$group_type+"'>";
		if ($subject_title != "null") {
			$html += "<span id='subcategory_title'> | "+$subject_title+"</span>";
			$html += "<input type='hidden' name='subject_title' value='"+$subject_title+"'>";
			$html += "<input type='hidden' name='has_subcategory' value='1'>";
		} else {
			$html += "<input type='hidden' name='has_subcategory' value='0'>";
		}
	} else {
		$html += "<span id='category-title'>"+$category_title+"</span>";
		$html += "<input type='hidden' name='category_type' value='category'>";
		$html += "<input type='hidden' name='category_id' value='"+$category_id+"'>";

		if ($subcategory_id !== undefined) {
			$html += "<span id='subcategory_title'> | "+$subcategory_title+"</span>";
			$html += "<input type='hidden' name='subcategory_id' value='"+$subcategory_id+"'>";
			$html += "<input type='hidden' name='has_subcategory' value='1'>";
		} else {
			$html += "<input type='hidden' name='has_subcategory' value='0'>";
		}
	}

	$category_list_element.html($html);
	$('#comings-expenditures-list').modal('hide');
});



$(document).on('focus', '.accounting-analize-datepicker-input', function() {
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

$(document).on('click', '.set-accounting-analize', function() {
	$from_date_splitted = $('.accounting-analize-datepicker').find('input[name=from-date]').val().split('.');
	$to_date_splitted = $('.accounting-analize-datepicker').find('input[name=to-date]').val().split('.');

	$from_date = $from_date_splitted[2]+'-'+$from_date_splitted[1]+'-'+$from_date_splitted[0];
	$to_date = $to_date_splitted[2]+'-'+$to_date_splitted[1]+'-'+$to_date_splitted[0];

	window.open('accounting/components/account_analize.php?from_date='+$from_date+'&to_date='+$to_date);
});

$(document).on('click', '.open-static-category-amount-modal', function() {
	$('#static-category-amount-modal').modal('show');
	render_static_category_amount_modal();
});

function render_static_category_amount_modal () {
	$accounting_period = $('input[name=accounting-period]').val().split('.');
	$('#static-category-amount-modal .modal-body').html("<center><h4>Загрузка...</h4></center>");
	$date_month = $accounting_period[0];
	$date_year = $accounting_period[1];
	$.when(get_static_accounting_amounts($date_month, $date_year)).done(function($data) {
		$json = $.parseJSON($data);

		$html = "<table class='table table-striped table-bordered table-asca'>";

		$.each($json.data, function($id, $value) {
			$marked = "";
			if ($value.ascm_id != null) {
				$marked = "background-color: #5AB45A;";
			}
			$html += "<tr style='"+$marked+"'>";
				$html += "<td>";
					// $html += "<input type='text' name='title' class='form-control' data-default='"+$value.title+"' value='"+$value.title+"'>";
					$html += "<textarea name='title' class='form-control' data-default='"+$value.title+"' rows='2' cols='40'>";
						$html += $value.title;
					$html += "</textarea>";
					$html += "<input type='hidden' name='id' class='form-control' value='"+$id+"'>";
				$html += "</td>";
				$html += "<td><input type='text' name='amount' class='form-control' data-default='"+$value.amount+"' value='"+$value.amount+"'></td>";
				if ($value.ascm_id == null) {
					$html += "<td><center><button class='btn btn-sm btn-success set-mark' data-id='"+$id+"'><i class='fas fa-check-circle'></i></button></center></td>";
				} else {
					$html += "<td><center><button class='btn btn-sm btn-danger unset-mark' data-id='"+$id+"' data-ascm-id='"+$value.ascm_id+"'><i class='fas fa-window-close'></i></button></center></td>";
				}
				$html += "<td>";
					$html += "<button class='btn btn-sm btn-success update-asca' style='display: none;'>Сақтау</button>";
					$html += "<button class='btn btn-sm btn-warning reset-asca' style='display: none;'>Отмена</button>";
				$html += "</td>";
			$html += "</tr>";
		});

			$html += "<tr>";
				// $html += "<td><input type='text' name='title' class='form-control'></td>";
				$html += "<td>";
					$html += "<textarea name='title' class='form-control'></textarea>";
				$html += "</td>";
				$html += "<td><input type='text' name='amount' class='form-control'></td>";
				$html += "<td><button class='btn btn-sm btn-success add-asca'>Сақтау</button></td>";
			$html += "</tr>";
		$html += "</table>";
		$('#static-category-amount-modal .modal-body').html($html);
		lightAlert($('#static-category-amount-modal .modal-body'), 'green', 0, 500);
	});
}

function get_static_accounting_amounts($date_month, $date_year) {
	return $.ajax({
		type: 'GET',
		url: 'accounting/controller.php?get_static_category_amount&date_month='+$date_month+'&date_year='+$date_year
	});
}

$(document).on('click', '.add-asca', function() {
	$current_row = $(this).parents('tr');
	$title = $current_row.find('textarea[name=title]').val();
	$amount = $current_row.find('input[name=amount]').val();
	$.ajax({
		type: 'GET',
		url: 'accounting/controller.php?add_asca&title='+$title+'&amount='+$amount,
		beforeSend: function() {
			$current_row.find('.add-asca').text('Загрузка...');
		},
		success: function ($data) {
			$json = $.parseJSON($data);
			if ($json.success) {
				render_static_category_amount_modal();
			}
		}
	});
});

$(document).on('keyup', '.table-asca textarea[name=title], .table-asca input[name=amount]', function() {
	$parent_row = $(this).parents('tr');
	$parent_row.find('.update-asca').show();
	$parent_row.find('.reset-asca').show();

	$new_val_title = $parent_row.find('textarea[name=title]').val();
	$new_val_amount = $parent_row.find('input[name=amount]').val();

	$old_val_title = $parent_row.find('textarea[name=title]').data('default');
	$old_val_amount = $parent_row.find('input[name=amount]').data('default');

	if ($new_val_title == $old_val_title && $new_val_amount == $old_val_amount) {
		$parent_row.find('.update-asca').hide();
		$parent_row.find('.reset-asca').hide();
	}
});

$(document).on('click', '.table-asca .reset-asca', function() {
	$parent_row = $(this).parents('tr');

	$old_val_amount = $parent_row.find('input[name=amount]').data('default');
	$old_val_title = $parent_row.find('textarea[name=title]').data('default');

	$parent_row.find('input[name=amount]').val($old_val_amount);
	$parent_row.find('textarea[name=title]').val($old_val_title);

	$parent_row.find('.update-asca').hide();
	$parent_row.find('.reset-asca').hide();
});

$(document).on('click', '.update-asca', function() {
	$parent_row = $(this).parents('tr');
	$title = $parent_row.find('textarea[name=title]').val();
	$amount = $parent_row.find('input[name=amount]').val();
	$id = $parent_row.find('input[name=id]').val();

	$.ajax({
		type: "GET",
		url: 'accounting/controller.php?update_asca&id='+$id+'&title='+$title+'&amount='+$amount,
		beforeSend: function() {
			$parent_row.find('.update-asca').text('Загрузка...');
		},
		success: function($data) {
			$json = $.parseJSON($data);

			if ($json.success) {
				render_static_category_amount_modal();
			}
		}
	});
});




$(document).on('keyup', '.subcategory input[name=title], .subcategory input[name=budget]', function() {
	$parent_row = $(this).parents('.subcategory');
	$default_title_val = $parent_row.find('input[name=title]').data('default');
	$default_budget_val = $parent_row.find('input[name=budget]').data('default');
	$new_title_val = $parent_row.find('input[name=title]').val();
	$new_budget_val = $parent_row.find('input[name=budget]').val();

	if ($default_title_val == $new_title_val && $default_budget_val == $new_budget_val) {
		$parent_row.find('.reset-update-category-expenditure').hide();
		$parent_row.find('.update-category-expenditure').hide();
	} else {
		$parent_row.find('.reset-update-category-expenditure').show();
		$parent_row.find('.update-category-expenditure').show();
	}
});

$(document).on('click', '.reset-update-category-expenditure', function() {
	$parent_row = $(this).parents('.subcategory');
	$default_title_val = $parent_row.find('input[name=title]').data('default');
	$default_budget_val = $parent_row.find('input[name=budget]').data('default');
	$parent_row.find('input[name=title]').val($default_title_val);
	$parent_row.find('input[name=budget]').val($default_budget_val);
	$parent_row.find('.reset-update-category-expenditure').hide();
	$parent_row.find('.update-category-expenditure').hide();
});

$(document).on('click', '.update-category-expenditure', function() {
	$parent_row = $(this).parents('.subcategory');
	$default_title_val = $parent_row.find('input[name=title]').data('default');
	$default_budget_val = $parent_row.find('input[name=budget]').data('default');
	$title = $parent_row.find('input[name=title]').val();
	$budget = $parent_row.find('input[name=budget]').val();
	$category_id = $parent_row.find('input[name=cc_id]').val();

	$.ajax({
		type: 'GET',
		url: 'accounting/controller.php?update_subcategory_expenditure&category_id='+$category_id+'&title='+$title+'&budget='+$budget,
		beforeSend: function() {
			set_load($parent_row.parents('.modal-body'));
		},
		success: function($data) {
			remove_load();
			$json = $.parseJSON($data);

			if ($json.success) {
				render_category_expenditure_table();
				lightAlert($parent_row, 'green', 0, 500);
			}
		}
	});
});


$(document).on('click', '.set-mark', function() {
	$id = $(this).data('id');
	$accounting_period = $('input[name=accounting-period]').val().split('.');
	$date_month = $accounting_period[0];
	$date_year = $accounting_period[1];
	$.ajax({
		type: 'GET',
		url: 'accounting/controller.php?set_mark&id='+$id+'&date_month='+$date_month+'&date_year='+$date_year,
		success: function ($date) {
			console.log($date);
			render_static_category_amount_modal();
		}
	});
});

$(document).on('click', '.unset-mark', function() {
	$id = $(this).data('id');
	$ascm_id = $(this).data('ascm-id');
	$.ajax({
		type: 'GET',
		url: 'accounting/controller.php?unset_mark&id='+$id+'&ascm_id='+$ascm_id,
		success: function () {
			render_static_category_amount_modal();
		}
	});
});
