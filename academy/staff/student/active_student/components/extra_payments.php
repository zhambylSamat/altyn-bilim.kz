<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include($root.'/common/constants.php');

	include_once($root.'/staff/student/view.php');
	$extra_payment_html = '';
	$error_html = "error";
	if (isset($_GET['group_student_id']) || isset($group_student_id)) {
		$extra_payment_html = "";
		$group_student_id = isset($_GET['group_student_id']) ? $_GET['group_student_id'] : $group_student_id;
		$next_payed_payment = get_next_payed_payment($group_student_id);
		
		$extra_payment_html .= "<span style='color: #5cb85c;'>Оплатасы өткізілді</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>Келесі төлем уақыты: ".$access_until."</span>";
		if (count($next_payed_payment) == 0) {
			$next_payment_details = get_next_payment_details($group_student_id);
			if ($next_payment_details['may_payment']) {
				$extra_payment_html .= "<div class='extra-pay-box'>";
				$extra_payment_html .= "<button class='btn btn-xs btn-info next-payment-btn'>Келесы айға оплатасын енгізу</button>";
				if ($next_payment_details['payment_days'] == 'full') {
					$extra_payment_html .= "&nbsp;&nbsp;<button class='btn btn-xs btn-default next-full-payment-btn' data-id='".$group_student_id."' style='display:none;'>Толық бір айға төлем</button>";
				} else {
					$extra_payment_html .= "&nbsp;&nbsp;<span class='next-full-payment-btn' style='display:none;'>Толық курсқа төлеудің қажеті жоқ.</span>";
				}
				$extra_payment_html .= "&nbsp;&nbsp;<button class='btn btn-xs btn-default next-partial-payment-btn' style='display:none;'>Кундер</button>";
				$extra_payment_html .= "&nbsp;&nbsp;<button class='btn btn-xs btn-warning cancel-next-payment' style='display: none;'>Отмена</button>";
				$extra_payment_html .= "<form class='form-inline next-partial-payment-form' style='display: none;'>";
					$extra_payment_html .= "<div class='form-group'>";
						if ($next_payment_details['payment_days'] == 'full') {
							$extra_payment_html .= "<input type='number' class='form-control partial_payment_days' data-price='".$price."' name='next-partial-days' min='1' step='1' value='1' />";
						} else {
							$extra_payment_html .= "<input type='number' class='form-control partial_payment_days' data-price='".$price."' name='next-partial-days' min='1' max='".$next_payment_details['payment_days']."' step='1' value='".$next_payment_details['payment_days']."' />";
						}
						$extra_payment_html .= "<input type='hidden' name='group_student_id' value='".$group_student_id."'/>";
					$extra_payment_html .= "</div>";
					$extra_payment_html .= "<span class='partial_payment_price'>".($next_payment_details['payment_days'] == 'full' ? $price : $next_payment_details['payment_days'] * $price)." тг.</span>";
					$extra_payment_html .= "<button type='submit' class='btn btn-success btn-xs'>Төлемін сақтау</button>";
					$extra_payment_html .= "<button type='button' class='btn btn-warning btn-xs next-partial-payment-form-cancel'>Отмена</button>";
				$extra_payment_html .= "</form>";
				$extra_payment_html .= "</div>";
			} else {
				$extra_payment_html .= "no payment needs";
			}
		} else {
			if ($next_payed_payment['days'] == 'full') {
				$extra_payment_html .= "&nbsp;&nbsp;<span style='color: green;'>Келесі айға, ".$next_payed_payment['price']."тг, толық төлемі төленді</span>";
			} else {
				$extra_payment_html .= "&nbsp;&nbsp;<span style='color: green;'>Келесі айға ".$next_payed_payment['days']." күнге, төленді</span>";
				// ".$next_payed_payment['price']."
			}
		}
	} else {
		$extra_payment_html = $error_html;
	}

	echo $extra_payment_html;
?>