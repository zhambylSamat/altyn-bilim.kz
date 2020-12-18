<?php
	$holidays = get_holidays_list();

	$count = 0;
	$html = "<table class='table table-striped table-bordered'>";
	foreach ($holidays as $holiday_id => $value) {
		$html .= "<tr>";
			$html .= "<td><center>".(++$count)."</center></td>";
			$html .= "<td><center>".$value['holiday_from_date'].'-'.$value['holiday_to_date']."</center></td>";
			$html .= "<td>";
				$html .= $value['title'];
				// $html .= "<div class='panel panel-default'>";
				// 	$html .= "<div class='panel-heading'>".$value['title']."</div>";
				// 	$html .= "<div class='panel-body'>".$value['comment']."</div>";
				// $html .= "</div>"; // .panel .panel-default
			$html .= "</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";
	echo $html;
?>