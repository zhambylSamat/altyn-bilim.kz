<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include($root.'/common/constants.php');
	include_once($root.'/staff/student_payment_list/views.php');

	if (!isset($_GET['date'])) {
		$date = date('Y-m');
	} else {
		$date = $_GET['date'];
	}

	$student_payment_list = get_student_payment_list($date);
	// print_r($student_payment_list);

	$html = "<center><h3>".$student_payment_list['year'].' '.$month[intval($student_payment_list['month'])]."</h3></center>";
	foreach ($student_payment_list['data'] as $day => $infos) {
		$html .= "<table class='table table-striped table-bordered'>";
			$html .= "<tr class='info'><th colspan='8'><center>".intval($day)." ".$month[intval($student_payment_list['month'])]."</center></th></tr>";
			$count = 1;
			foreach ($infos as $group_student_payment_id => $value) {
				$extra_payment_stretch_class = "";
				if ($value['partial_payment_days'] == '') {
					$extra_payment_stretch_class = 'success';
				} else {
					$extra_payment_stretch_class = 'info';
				}
				$html .= "<tr>";
					$html .= "<td>".($count++)."</td>";
					$html .= "<td>".$value['last_name']." ".$value['first_name']."</td>";
					$html .= "<td>".$value['phone']."</td>";
					$html .= "<td>".$value['subject_title']."</td>";
					$html .= "<td>".$value['group_name'].($value['is_army_group'] ? ' | <b>Армия</b>' : '')."</td>";
					$html .= "<td class='".$extra_payment_stretch_class."'>".($value['partial_payment_days'] == '' ? '1 айға төлем жасады' : $value['partial_payment_days'].' күнге төлем жасады')."</td>";
					if ($value['payed_amount'] != '') {
						$html .= "<td>".$value['payed_amount']."</td>";
						// $html .= "<td>".number_format($value['payed_amount'], 2, '.', ' ')."</td>";
					} else {
						$html .= "<td>-</td>";
					}
					$html .= "<td>".$value['payed_date']."</td>";
				$html .= "</tr>";
			}
		$html .= "</table>";
	}
	echo $html;
?>