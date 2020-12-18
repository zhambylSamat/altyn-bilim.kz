<?php
	$discount_group_student = get_discount_group_student();

	// echo json_encode($discount_group_student, JSON_UNESCAPED_UNICODE);

	$html = "<hr>";
	$html .= "<table class='table table-striped table-bordered'>";
	$std_id = 0;
	$count = 0;
	foreach ($discount_group_student as $student_id => $student_info) {

		foreach ($student_info['discounts'] as $group_student_id => $discount) {
			if ($std_id != $student_id) {
				$html .= "<tr style='border-top: 2px solid gray;'>";
					$rowspan = "";
					$html .= "<td rowspan='".count($student_info['discounts'])."'>";
						$html .= "<span>".(++$count).") ".$student_info['last_name'].' '.$student_info['first_name'].' +7 '.$student_info['phone']."</span>";
					$html .= "</td>";
				$std_id = $student_id;
			} else {
				$html .= "<tr>";
			}
			$group_type = "";
			if ($discount['is_army_group']) {
				$group_type = " (Армия)";
			} else if ($discount['is_marathon_group']) {
				$group_type = " (Марафон)";
			}
			$html .= "<td><span>".$discount['group_name'].$group_type."</span></td>";
			$html .= "<td><span>".$discount['discount_title']."</span></td>";
			$period = $discount['for_month'] == -1 ? 'Толқ курсқа' : $discount['for_month'];
			$html .= "<td><span>".$discount['used_count'].'/'.$period."</span></td>";

			$html .= "</tr>";
		}
	}
	$html .= "</table>";
	echo $html;
?>
