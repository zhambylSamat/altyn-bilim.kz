$colors = ['#108CC8', '#80B13C', '#EACE31', '#E59A01', '#D23A31', '#304B9F', 
			'#8BE78B', '#A8D1DF', '#E42C38', '#F3624C', '#EA4567',
			'#C9B8A0', '#CA1A52', '#FEBDC3', '#417447', '#5F6142', '#5D5E62'];

$(document).on('click', '.category-full-info-btn', function() {
	$type = $(this).data('type');
	$from_date = $('input[name=from-date]').val();
	$to_date = $('input[name=to-date]').val();
	$ab_root = $('input[name=ab-root]').val();
	if ($type == 'coming') {
		$category = $(this).data('category');
		if ($category == 'static') {
			$group_type = $(this).data('group-type');
			$('#categories-info-modal .modal-title').html("<b>Приход</b>: "+$group_type);
			$('#categories-info-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
			$('#categories-info-modal').modal('show');
			$.when(get_coming_group_type_category_info($from_date, $to_date, $group_type, $ab_root)).done(function($data) {
				$json = $.parseJSON($data);
				$('#categories-info-modal .modal-body').html(render_category_table($json.data));
			});
		} else if ($category == 'dynamic') {
			$category_id = $(this).data('category-id');
			$('#categories-info-modal .modal-title').html('<b>Приход<b>: загрузка...');
			$('#categories-info-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
			$('#categories-info-modal').modal('show');
			$.when(get_coming_category_info($from_date, $to_date, $category_id, $ab_root)).done(function($data){
				$json = $.parseJSON($data);
				$('#categories-info-modal .modal-title').html('<b>Приход</b>: '+$json.category_title);
				$('#categories-info-modal .modal-body').html(render_category_table($json.data));
			});
		}
	} else if ($type == 'expenditure') {
		$category_id = $(this).data('category-id');
		$('#categories-info-modal .modal-title').html("<b>Расход</b>: загрузка...");
		$('#categories-info-modal .modal-body').html('<center><h3>Загрузка...</h3></center>');
		$('#categories-info-modal').modal('show');
		$.when(get_expenditure_category_info($from_date, $to_date, $category_id, $ab_root)).done(function($data) {
			$json = $.parseJSON($data);
			$('#categories-info-modal .modal-title').html("<b>Расход</b>: "+$json.category_title);
			
			$('#categories-info-modal .modal-body').html(render_category_table($json.data));
		});
	}
});

$(document).on('click', '.subcategory-full-info-btn', function() {
	$type = $(this).data('type');
	$from_date = $('input[name=from-date]').val();
	$to_date = $('input[name=to-date]').val();
	$ab_root = $('input[name=ab-root]').val();

	if ($type == 'coming') {
		$category = $(this).data('category');
		if ($category == 'static') {
			$group_type = $(this).data("group-type");
			$subject_title = $(this).data('subject-title');
			$('#categories-info-modal .modal-title').html("<b>Приход</b>: "+$group_type+' | '+$subject_title);
			$('#categories-info-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
			$('#categories-info-modal').modal('show');
			$.when(get_coming_group_type_subcategory_info($from_date, $to_date, $group_type, $subject_title, $ab_root)).done(function($data) {
				$json = $.parseJSON($data);
				$('#categories-info-modal .modal-body').html(render_category_table($json.data));
			});
		} else if ($category == 'dynamic') {
			$category_id = $(this).data('category-id');
			$category_parent_id = $(this).data('category-parent-id');
			$('#categories-info-modal .modal-title').html('<b>Приход<b>: загрузка...');
			$('#categories-info-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
			$('#categories-info-modal').modal('show');
			$.when(get_coming_subcategory_info($from_date, $to_date, $category_id, $category_parent_id, $ab_root)).done(function($data) {
				$json = $.parseJSON($data);
				$('#categories-info-modal .modal-title').html('<b>Приход<b>:'+$json.category_parent_title+' | '+$json.category_title);
				$('#categories-info-modal .modal-body').html(render_category_table($json.data));
			});
		}
	} else if ($type == 'expenditure') {
		$category_id = $(this).data('category-id');
		$category_parent_id = $(this).data('category-parent-id');

		$('#categories-info-modal .modal-title').html("<b>Расход</b>: загрузка...");
		$('#categories-info-modal .modal-body').html('<center><h3>Загрузка...</h3></center>');
		$('#categories-info-modal').modal('show');
		$.when(get_expenditure_subcategory_info($from_date, $to_date, $category_id, $category_parent_id, $ab_root)).done(function($data) {
			$json = $.parseJSON($data);
			$('#categories-info-modal .modal-title').html("<b>Расход</b>: "+$json.category_parent_title+' | '+$json.category_title);
			$('#categories-info-modal .modal-body').html(render_category_table($json.data));
		});
	}
});

$(document).on('click', '.fee-full-info-btn', function() {
	$from_date = $('input[name=from-date]').val();
	$to_date = $('input[name=to-date]').val();
	$ab_root = $('input[name=ab-root]').val();
	$('#categories-info-modal .modal-title').html('Комиссия');
	$('#categories-info-modal .modal-body').html("<center><h3>Загрузка...</h3></center>");
	$('#categories-info-modal').modal('show');
	$.when(get_fee_info($from_date, $to_date, $ab_root)).done(function($data) {
		$json = $.parseJSON($data);
		$html = "<table class='table table-striped table-bordered'>";
		$.each($json.data, function($index, $value) {
			$html += "<tr>";
				$html += "<td title='"+$value.from.title_full+"'>"+$value.from.title_short+": "+$value.from.amount+"</td>";
				$html += "<td title='"+$value.to.title_full+"'>"+$value.to.title_short+" : "+$value.to.amount+"</td>";
				$html += "<td title='Комиссия'>К: "+$value.fee_amount+"</td>";
				$html += "<td>"+$value.date+"</td>";
			$html += "</tr>";
		});
		$html += "</table>";
		$('#categories-info-modal .modal-body').html($html);
	});
});

function render_category_table ($data) {
	$html = "<table class='table table-striped table-bordered'>";
	$.each($data, function($index, $value) {
		$html += "<tr>";
			$html += "<td>"+formatMoney($value.amount)+"</td>";
			$html += "<td>"+$value.date+"</td>";
			$html += "<td title='"+$value.title_full+"'>"+$value.title_short+"</td>";
		$html += "</tr>";
	});
	$html += "</table>";
	return $html;
}

function get_coming_group_type_category_info ($from_date, $to_date, $group_type, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_coming_group_type_category_info&group_type='+$group_type+'&from_date='+$from_date+'&to_date='+$to_date
	});
}

function get_coming_group_type_subcategory_info ($from_date, $to_date, $group_type, $subject_title, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_coming_group_type_subcategory_info&group_type='+$group_type+'&subject_title='+$subject_title+'&from_date='+$from_date+'&to_date='+$to_date
	});
}

function get_coming_category_info ($from_date, $to_date, $category_id, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_coming_category_info&from_date='+$from_date+'&to_date='+$to_date+'&category_id='+$category_id
	});
}

function get_coming_subcategory_info ($from_date, $to_date, $category_id, $category_parent_id, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_coming_subcategory_info&from_date='+$from_date+'&to_date='+$to_date+'&category_id='+$category_id+'&category_parent_id='+$category_parent_id
	});
}

function get_expenditure_category_info ($from_date, $to_date, $category_id, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_expenditure_category_info&from_date='+$from_date+'&to_date='+$to_date+'&category_id='+$category_id
	});
}

function get_expenditure_subcategory_info ($from_date, $to_date, $category_id, $category_parent_id, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_expenditure_subcategory_info&from_date='+$from_date+'&to_date='+$to_date+'&category_id='+$category_id+'&category_parent_id='+$category_parent_id
	});
}

function get_fee_info ($from_date, $to_date, $ab_root) {
	return $.ajax({
		type: 'GET',
		url: $ab_root+'/academy/staff/accounting/controller.php?get_fee_info&from_date='+$from_date+'&to_date='+$to_date
	});
}

function formatMoney(number, decPlaces, decSep, thouSep) {
	decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
	decSep = typeof decSep === "undefined" ? "." : decSep;
	thouSep = typeof thouSep === "undefined" ? " " : thouSep;
	var sign = number < 0 ? "-" : "";
	var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
	var j = (j = i.length) > 3 ? j % 3 : 0;

	return sign +
		(j ? i.substr(0, j) + thouSep : "") +
		i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
		(decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}


$(document).ready(function() {
	$json = $.parseJSON($('#coming-category-json-info').text());
	$ctx = $('#coming-chart-pie');
	chart_pie($ctx, $json);
	$ctx = $('#coming-chart-bar');
	chart_bar($ctx, $json);

	$json = $.parseJSON($('#expenditure-category-json-info').text());
	$ctx = $('#expenditure-chart-pie');
	chart_pie($ctx, $json);
	$ctx = $('#expenditure-chart-bar');
	chart_bar($ctx, $json);
});

function chart_pie ($ctx, $json) {

	$data = [];
	$background_color = [];
	$labels = [];
	$.each($json, function($index, $value) {
		// $data.push($value.data);
		$data.push($value.data_percent);
		$labels.push($value.label);
		$background_color.push($colors[$index]);
	});

	$config = {
		type: 'pie',
		data: {
			datasets: [{
				data: $data,
				backgroundColor: $background_color
			}],
			labels: $labels
		},
		option: {
			responsive: true
		}
	};
	window.myPie = new Chart($ctx, $config);
}

function chart_bar ($ctx, $json) {

	$datas = {'labels': [1],
				'datasets': []};
	$.each($json, function($index, $value) {
		// $datas.labels.push($index + 1);
		$datas.datasets.push({'label': $value.label,
								'backgroundColor': $colors[$index],
								'data': [$value.data_percent]});
	});

	window.myBar = new Chart($ctx, {
		type: 'bar',
		data: $datas,
		options: {
			responsive: true,
			legend: {
				position: 'top'
			}
		}
	});
}
