<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include($root.'/common/constants.php');
	include_once($root.'/staff/accounting/views.php');

	if (isset($_GET['month'])) {
		$month_num = $_GET['month'];
	} else {
		$month_num = date('m');
	}

	if(isset($_GET['year'])) {
		$year_num = $_GET['year'];
	} else {
		$year_num = date('Y');
	}

	$days = array();
	for ($i = 1; $i <= date('t', strtotime($year_num.'-'.$month_num)); $i++) {
		if ($i < 10) {
			$day = '0'.$i;
		} else {
			$day = $i;
		}
		$days[$day] = $day.'.'.$month_num;
	}

	$money_types = get_money_type();
	// echo json_encode($money_types, JSON_UNESCAPED_UNICODE);
	$begin_of_day_balances = get_begin_of_day_balances($month_num, $year_num);
	$end_of_day_balances = get_end_of_day_balances($month_num, $year_num);
	$comings = get_comings($month_num, $year_num);
	$expenditures = get_expenditures($month_num, $year_num);

	$account_datas = array('begin_of_day_balances' => array('datas' => $begin_of_day_balances,
															'class' => 'begin-of-day-balance-column',
															'extra_class' => '',
															'title' => 'Остаток на начало дня'),
							'comings' => array('datas' => $comings,
												'class' => 'coming-column',
												'extra_class' => 'show-comings',
												'title' => 'Приход'),
							'expenditures' => array('datas' => $expenditures,
													'class' => 'expenditure-column',
													'extra_class' => 'show-expenditures',
													'title' => 'Расход'),
							'end_of_day_balances' => array('datas' => $end_of_day_balances,
															'class' => 'end-of-day-balance-column',
															'extra_class' => '',
															'title' => 'Остаток на конец дня'));

	$total_left_sum_html = "";
	
	$html = "<input type='hidden' name='selected-month' value='".$month_num."'>";
	$html .= "<input type='hidden' name='selected-year' value='".$year_num."'>";
	$html .= "<center><h3>".$month_ru[intval($month_num)]."</h3></center>";

	echo $html;

	include_once($root.'/staff/accounting/components/budget_table.php');

	$html = "";

	$html .= "<div style='width: 100%; overflow: scroll;'><table class='table table-striped table-bordered account-table'>";
	$html .= "<tr>";
		$html .= "<th style='text-align: center; vertical-align: middle;' class='column-fixed'>#</th>";
		foreach ($account_datas as $value) {
			$html .= "<th class='".$value['class']." column-flow' colspan='".count($money_types['datas'])."'>".$value['title']."</th>";
		}
	$html .= "</tr>";

	$html .= "<tr>";
		$html .= "<th style='text-align: center; vertical-align: middle;' class='column-fixed'>Дата &nbsp;</th>";
		foreach ($account_datas as $value) {
			foreach ($money_types['datas'] as $money_type_id => $money_type) {
				$html .= "<th class='".$value['class']." column-flow' title='".$money_type['title_full']."'>".$money_type['title_short']."</th>";
			}
		}
	$html .= "</tr>";
	foreach ($days as $day => $day_str) {
		$html .= "<tr>";
			$html .= "<td class='column-fixed'>".$day_str."</td>";
			foreach ($account_datas as $key => $account) {
				$sum_of_end_day_balance = 0;
				foreach ($money_types['datas'] as $money_type_id => $money_type) {
					$zero_amount_html = "<td class='".$account['class']." column-flow'>0</td>";
					if (isset($account['datas'][$day])) {
						if (isset($account['datas'][$day]['data'][$money_type_id])) {
							$html .= "<td class='".$account['class'].' '.$account['extra_class']." column-flow' data-money-type-id='".$money_type_id."' data-day-str='".$day_str."' data-month='".$month_num."' data-year='".$year_num."' data-day='".$day."'><b>".floatval($account['datas'][$day]['data'][$money_type_id]['amount'])."</b></td>";
							$sum_of_end_day_balance += floatval($account['datas'][$day]['data'][$money_type_id]['amount']);
						} else {
							$html .= $zero_amount_html;
						}
					} else {
						$html .= $zero_amount_html;
					}
				}
				$total_left_sum_html = "<tr><td class='column-fixed'>#</td>";
					$total_left_sum_html .= "<td colspan='3' class='column-flow footer-gap-td'></td>";
					$total_left_sum_html .= "<td colspan='6' class='column-flow'></td>";
					$total_left_sum_html .= "<td class='column-flow ".$account_datas['end_of_day_balances']['class']."' colspan='3'><center><b>".round($sum_of_end_day_balance, 2)."</b></center></td>";
				$total_left_sum_html .= "</tr>";
			}
		$html .= "</tr>";
	}
	$html .= $total_left_sum_html;
	$html .= "</table></div>";
	echo $html;
?>