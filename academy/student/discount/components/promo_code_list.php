<?php
	$promo_code_list = get_promo_code_list();
	// echo json_encode($promo_code_list, JSON_UNESCAPED_UNICODE);

	$html = "";
	$html .= "<div id='promo-code-list'>";
	$html .= "<h4 id='promo-code-list-title'>Сенің промокодың арқылы шақырылған достарыңның тізімі:</h4>";

	if (count($promo_code_list) == 0) {
		$html .= "<p id='no-promo-code-list-title'>Әзірше сенің промокодыңды ешкім қолданбады</p>";
	} else {
		$html .= "<ol>";
		foreach ($promo_code_list as $student_used_promo_code_id => $value) {
			$html .= "<li class='used-student-list'>";
			$html .= "<span>".$value['last_name']." ".$value['first_name']."</span>";
			if (!$value['is_friend_promo_code_use']) {
				$html .= "<span> сенің промокодыңды енгізді. Ол төлем жасаған соң сен жеңілдік аласың :)</span>";
			} else if (!$value['is_promo_code_use'] && $value['is_friend_promo_code_use']) {
				$html .= "<span> промокодың арқылы төлем жасады. Келесі төлеміңде 20% жеңілдік аласың!</span>";
			} else {
				$html .= "<span>Жеңілдік қолданылды</span>";
			}
			$html .= "</li>";
		}
		$html .= "</ol>";
	}
	$html .= "</div>";

	echo $html;
?>