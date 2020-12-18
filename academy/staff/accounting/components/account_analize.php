<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

		include_once($root.'/common/assets/meta.php');

		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/connection.php');

		include_once($root.'/common/assets/js.php');

		if (!isset($_GET['from_date']) && !isset($_GET['to_date'])) {
			header('Location:../../index.php');
		}

		include_once($root.'/staff/accounting/views.php');

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];

		$total_amount_of_coming_and_expenditure = get_total_amount_of_coming_and_expenditure($from_date, $to_date);
		// echo json_encode($total_amount_of_coming_and_expenditure, JSON_UNESCAPED_UNICODE);
		$coming_full_info = get_coming_full_info($from_date, $to_date);
		// echo json_encode($coming_full_info, JSON_UNESCAPED_UNICODE);
		$expenditure_full_info = get_expenditure_full_info($from_date, $to_date);
		// echo json_encode($expenditure_full_info, JSON_UNESCAPED_UNICODE);
		$avans_dividend = get_avans_dividend($from_date, $to_date);
		// echo json_encode($avans_dividend, JSON_UNESCAPED_UNICODE);

		$coming_category_short_info = array();
		$expenditure_category_short_info = array();
	?>
	<title>Отчет Бухгалтерия</title>

	<style type="text/css">
		.coming-column {
			background-color: #AFF2C5;
			text-align: center;
		}
		.expenditure-column {
			background-color: #F79696;
			text-align: center;
		}
		.total-column {
			text-align: center;
		}
		.category-full-info-btn {
			cursor: pointer;
		}

		.subcategory-full-info-btn {
			cursor: pointer;
		}

		.fee-full-info-btn {
			cursor: pointer;
		}

	</style>
</head>
<body>

<div class='container'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<center>
				<h3>
					Отчет по <?php echo date_format(date_create($from_date), 'd.m.Y'); ?> 
					и <?php echo date_format(date_create($to_date), 'd.m.Y'); ?>
				</h3>
			</center>
			<input type="hidden" name="from-date" value='<?php echo $from_date; ?>'>
			<input type="hidden" name="to-date" value='<?php echo $to_date; ?>'>
			<input type='hidden' name='ab-root' value='<?php echo $ab_root; ?>'>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<table class='table table-striped table-bordered'>
				<?php
					$html = "";
					$html .= "<tr>";
					foreach ($total_amount_of_coming_and_expenditure['money_type'] as $value) {
						$html .= "<th class='coming-column' title='".$value['title_full']."'>".$value['title_short']."</th>";
					}
					foreach ($total_amount_of_coming_and_expenditure['money_type'] as $value) {
						$html .= "<th class='expenditure-column' title='".$value['title_full']."'>".$value['title_short']."</th>";
					}
					$html .= "<th class='total-column'>Прибыль</th>";
					$html .= "</tr>";

					$html .= "<tr>";
					foreach ($total_amount_of_coming_and_expenditure['coming_partial_amount'] as $amount) {
						$html .= "<td class='coming-column'>".number_format($amount, 2, '.', ' ')."</td>";
					}
					foreach ($total_amount_of_coming_and_expenditure['expenditure_partial_amount'] as $amount) {
						$html .= "<td class='expenditure-column'>".number_format($amount, 2, '.', ' ')."</td>";
					}
					$html .= "<td rowspan='2' class='total-column'>".number_format($total_amount_of_coming_and_expenditure['total_amount'], 2, '.', ' ')."</td>";
					$html .= "</tr>";

					$html .= "<tr>";
						$html .= "<td colspan='3' class='coming-column'>".number_format($total_amount_of_coming_and_expenditure['coming_amount'], 2, '.', ' ')."</td>";
						$html .= "<td colspan='3' class='expenditure-column'>".number_format($total_amount_of_coming_and_expenditure['expenditure_amount'], 2, '.', ' ')."</td>";
					$html .= "</tr>";

					echo $html;
				?>
			</table>
			<hr>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<center><h3>Доход</h3></center>
			<table class='table table-striped table-bordered'>
				<?php
					$html = "";
					$grand_total_amount = 0.0;
					foreach ($coming_full_info['static_category'] as $group_type => $category) {
						$total_sum_row = 0;
						$total_amount = 0.0;
						if ($category['category']['total_sum'] != 0) {
							$total_amount += $category['category']['total_sum'];
							$total_sum_row += 1;
						}
						foreach ($category['subcategory'] as $subject_title => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$total_amount += $subcategory['total_sum'];
								$total_sum_row += 1;
							}
						}
						$subcategory_html = "";
						$count = 0;
						foreach ($category['subcategory'] as $subject_title => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$count += 1;
								$subcategory_html .= "<tr>";
									$subcategory_html .= "<td>".$group_type." | ".$subject_title."</td>";
									$subcategory_html .= "<td><a class='subcategory-full-info-btn' data-type='coming' data-category='static' data-group-type='".$group_type."' data-subject-title='".$subject_title."' >".number_format($subcategory['total_sum'], 2, '.', ' ')."</a></td>";
									if ($count == 1 && $category['category']['total_sum'] == 0) {
										$subcategory_html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount, 2), 2, '.', ' ')."</td>";
									}
								$subcategory_html .= "</tr>";
							}
						}

						if ($category['category']['total_sum'] != 0) {
							$html .= "<tr>";
								$html .= "<td>".$group_type."</td>";
								$html .= "<td><a class='category-full-info-btn' data-type='coming' data-category='static' data-group-type='".$group_type."'>".number_format($category['category']['total_sum'], 2, '.', ' ')."</a></td>";
								$html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount, 2), 2, '.', ' ')."</td>";
							$html .= "</tr>";
						}
						$html .= $subcategory_html;

						if ($total_amount != 0) {
							array_push($coming_category_short_info, array('label' => $group_type,
																		'data' => round($total_amount, 2)));
							$grand_total_amount += $total_amount;
						}
					}

					foreach ($coming_full_info['dynamic_category'] as $category_parent_id => $category) {
						$total_sum_row = 0;
						$total_amount = 0.0;
						if ($category['category']['total_sum'] != 0) {
							$total_amount += $category['category']['total_sum'];
							$total_sum_row += 1;
						}
						foreach ($category['subcategory'] as $subject_title => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$total_amount += $subcategory['total_sum'];
								$total_sum_row += 1;
							}
						}
						$count = 0;
						$subcategory_html = "";
						foreach ($category['subcategory'] as $category_coming_id => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$count++;
								$subcategory_html .= "<tr>";
									$subcategory_html .= "<td>".$coming_full_info['category_info'][$category_parent_id]['category_title']." | ".$coming_full_info['category_info'][$category_coming_id]['category_title']."</td>";
									$subcategory_html .= "<td><a class='subcategory-full-info-btn' data-type='coming' data-category='dynamic' data-category-id='".$category_coming_id."' data-category-parent-id='".$category_parent_id."'>".number_format($subcategory['total_sum'], 2, '.', ' ')."</a></td>";
									if ($count == 1 && $category['category']['total_sum'] == 0) {
										$subcategory_html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
									}
								$subcategory_html .= "</tr>";
							}
						}

						if ($category['category']['total_sum'] != 0) {
							$html .= "<tr>";
								$html .= "<td>".$coming_full_info['category_info'][$category_parent_id]['category_title']."</td>";
								$html .= "<td><a class='category-full-info-btn' data-type='coming' data-category='dynamic' data-category-id='".$category_parent_id."'>".number_format($category['category']['total_sum'], 2, '.', ' ')."</a></td>";
								$html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
							$html .= "</tr>";
						}
						$html .= $subcategory_html;
						if ($total_amount != 0) {
							array_push($coming_category_short_info, array('label' => $coming_full_info['category_info'][$category_parent_id]['category_title'],
																		'data' => round($total_amount, 2)));
							$grand_total_amount += $total_amount;
						}
					}
					$html .= "<tr>";
						$html .= "<td colspan='2'>Барлығы</td>";
						$html .= "<td>".number_format(round($grand_total_amount, 2), 2, '.', ' ')."</td>";
					$html .= "</tr>";
					echo $html;

					foreach ($coming_category_short_info as $index => $value) {
						$percent = ($value['data'] * 100) / $grand_total_amount;
						$coming_category_short_info[$index]['data_percent'] = round($percent, 2);
					}
				?>
			</table>
			<hr>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<center><h3>Расход</h3></center>
			<table class='table table-striped table-bordered'>
				<?php 
					$grand_total_amount = 0.0;
					$html = "";
					foreach ($expenditure_full_info['data'] as $category_parent_id => $category) {
						$total_sum_row = 0;
						$total_amount = 0.0;
						if ($category['category']['total_sum'] != 0) {
							$total_amount += $category['category']['total_sum'];
							$total_sum_row += 1;
						}
						foreach ($category['subcategory'] as $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$total_amount += $subcategory['total_sum'];
								$total_sum_row += 1;
							}
						}
						$count = 0;
						$subcategory_html = "";
						foreach ($category['subcategory'] as $category_expenditure_id => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$count++;
								$subcategory_html .= "<tr>";
									$subcategory_html .= "<td>".$expenditure_full_info['category_info'][$category_parent_id]['category_title']." | ".$expenditure_full_info['category_info'][$category_expenditure_id]['category_title']."</td>";
									$subcategory_html .= "<td><a class='subcategory-full-info-btn' data-type='expenditure' data-category-id='".$category_expenditure_id."' data-category-parent-id='".$category_parent_id."'>".number_format($subcategory['total_sum'], 2, '.', ' ')."</a></td>";
								if ($count == 1 && $category['category']['total_sum'] == 0) {
									$subcategory_html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
								}
								$subcategory_html .= "</tr>";
							}
						}

						if ($category['category']['total_sum'] != 0) {
							$html .= "<tr>";
								$html .= "<td>".$expenditure_full_info['category_info'][$category_parent_id]['category_title']."</td>";
								$html .= "<td><a class='category-full-info-btn' data-type='expenditure' data-category-id='".$category_parent_id."'>".number_format($category['category']['total_sum'], 2, '.', ' ')."</a></td>";
								$html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
							$html .= "</tr>";
						}
						$html .= $subcategory_html;

						if ($total_amount != 0) {
							array_push($expenditure_category_short_info, array('label' => $expenditure_full_info['category_info'][$category_parent_id]['category_title'],
																			'data' => round($total_amount, 2)));
							$grand_total_amount += $total_amount;
						}
					}

					if ($expenditure_full_info['money_transfer']['total_sum'] != 0) {
						$html .= "<tr>";
							$html .= "<td>Комиссия от перевода</td>";
							$html .= "<td colspan='2'><a class='fee-full-info-btn'>".number_format(round($expenditure_full_info['money_transfer']['total_sum'], 2), 2, '.', ' ')."</a></td>";
						$html .= "</tr>";
					}
					$html .= "<tr>";
						$html .= "<td colspan='2'>Барлығы</td>";
						$html .= "<td>".number_format(round($grand_total_amount, 2), 2, '.', ' ')."</td>";
					$html .= "</tr>";
					echo $html;

					foreach ($expenditure_category_short_info as $index => $value) {
						$percent = ($value['data'] * 100) / $grand_total_amount;
						$expenditure_category_short_info[$index]['data_percent'] = round($percent, 2);
					}
				?>
			</table>
			<hr>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<center><h3>Аванс и Дивиденды</h3></center>
			<table class='table table-striped table-bordered'>
				<?php 
					$html = "";

					foreach ($avans_dividend['data'] as $category_parent_id => $category) {
						$total_sum_row = 0;
						$total_amount = 0.0;
						if ($category['category']['total_sum'] != 0) {
							$total_amount += $category['category']['total_sum'];
							$total_sum_row += 1;
						}
						foreach ($category['subcategory'] as $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$total_amount += $subcategory['total_sum'];
								$total_sum_row += 1;
							}
						}
						$count = 0;
						$subcategory_html = "";
						foreach ($category['subcategory'] as $category_expenditure_id => $subcategory) {
							if ($subcategory['total_sum'] != 0) {
								$count++;
								$subcategory_html .= "<tr>";
									$subcategory_html .= "<td>".$avans_dividend['category_info'][$category_parent_id]['category_title']." | ".$avans_dividend['category_info'][$category_expenditure_id]['category_title']."</td>";
									$subcategory_html .= "<td><a class='subcategory-full-info-btn' data-type='expenditure' data-category-id='".$category_expenditure_id."' data-category-parent-id='".$category_parent_id."'>".number_format($subcategory['total_sum'], 2, '.', ' ')."</a></td>";
								if ($count == 1 && $category['category']['total_sum'] == 0) {
									$subcategory_html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
								}
								$subcategory_html .= "</tr>";
							}
						}

						if ($category['category']['total_sum'] != 0) {
							$html .= "<tr>";
								$html .= "<td>".$avans_dividend['category_info'][$category_parent_id]['category_title']."</td>";
								$html .= "<td><a class='category-full-info-btn' data-type='expenditure' data-category-id='".$category_parent_id."'>".number_format($category['category']['total_sum'], 2, '.', ' ')."</a></td>";
								$html .= "<td rowspan='".$total_sum_row."'>".number_format(round($total_amount), 2, '.', ' ')."</td>";
							$html .= "</tr>";
						}
						$html .= $subcategory_html;
					}

					echo $html;
				?>
			</table>
			<hr>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php
				$chart_html = "";
				$chart_html .= "<p style='display:none;' id='coming-category-json-info'>".json_encode($coming_category_short_info, JSON_UNESCAPED_UNICODE)."</p>";
				$chart_html .= "<p style='display:none;' id='expenditure-category-json-info'>".json_encode($expenditure_category_short_info, JSON_UNESCAPED_UNICODE)."</p>";
				$chart_html .= "<h2>График дохода</h2>";
				$chart_html .= "<canvas id='coming-chart-pie' style='display: block; width: 762px; height: 381px;' width='762' height='381'></canvas>";
				$chart_html .= "<hr>";
				$chart_html .= "<canvas id='coming-chart-bar' style='display: block; width: 762px; height: 381px;' width='762' height='381'></canvas>";
				$chart_html .= "<hr>";
				$chart_html .= "<h2>График расхода</h2>";
				$chart_html .= "<canvas id='expenditure-chart-pie' style='display: block; width: 762px; height: 381px;' width='762' height='381'></canvas>";
				$chart_html .= "<hr>";
				$chart_html .= "<canvas id='expenditure-chart-bar' style='display: block; width: 762px; height: 381px;' width='762' height='381'></canvas>";
				$chart_html .= "<hr>";
				echo $chart_html;
			?>
		</div>
	</div>
</div>

<div class='modal fade' id='categories-info-modal' tabindex='-1' role='dialog'>
	<div class='modal-dialog' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title"></h4>
			</div>
			<div class='modal-body'>
			</div>
		</div>
	</div>
</div>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/common/assets/js/chart.js'; ?>"></script>
	<script type="text/javascript" src='<?php echo $ab_root; ?>/academy/staff/accounting/js/accounting_analize.js?v=0.0.0'></script>
</body>
</html>