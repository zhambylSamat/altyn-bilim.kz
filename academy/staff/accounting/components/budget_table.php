<?php
	
	$expenditure_budget_list = get_expenditure_budget_list($month_num, $year_num);

	$html = "";
	if (count($expenditure_budget_list) > 0) {
		$html .= "<table class='table table-strped table-bordered'>";
			$html .= "<tr>";
				$html .= "<th>#</th>";
				$html .= "<th>Категория</th>";
				$html .= "<th>Бюджет</th>";
				$html .= "<th>Құртылған сумма</th>";
				$html .= "<th>Қалған сумма</th>";
			$html .= "</tr>";
			$count = 0;
			foreach ($expenditure_budget_list as $category_expenditure_id => $value) {
				$html .= "<tr>";
					$html .= "<td>".(++$count)."</td>";
					$html .= "<td>".$value['category_title'].' | '.$value['subcategory_title']."</td>";
					$html .= "<td>".$value['budget']."</td>";
					$html .= "<td>".$value['sum_amount']."</td>";
					$html .= "<td>".$value['left']."</td>";
				$html .= "</tr>";
			}
		$html .= "</table>";
	}

	echo $html;
?>