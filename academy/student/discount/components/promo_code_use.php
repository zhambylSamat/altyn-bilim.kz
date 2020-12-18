<?php
	$used_promo_code = get_student_used_promo_code();
	$html = "";
	$html .= "<hr>";
	$html .= "<div id='friend-promo-code-content'>";
	if ($used_promo_code['already_payment_done'] > 0 || $used_promo_code['cant_insert_promo_code']) {
		$html .= "<p id='promo-code-unavailable'>Промокодты тек жаңадан тіркелген оқушы қолдана алады</p>";
	} else if ($used_promo_code['student_used_promo_code_id'] == '') {
		$html .= "<form id='friend-promo-code-form' class='form-horizontal'>";
			$html .= "<div class='form-group'>";
				$html .= "<label for='promo-code' class='control-label col-md-3 col-sm-4 col-xs-12'>Сені шақырған досыңның промокодын енгіз:</label>";
				$html .= "<div class='col-md-3 col-sm-4 col-xs-12'>";
					$html .= "<input type='text' name='promo-code' id='promo-code' class='form-control' autocomplete='off' value='' placeholder='Промокод' required maxlength='6'>";
				$html .= "</div>";
			$html .= "</div>";
			$html .= "<div class='form-group'>";
				$html .= "<div class='col-md-3 col-md-offset-3 col-sm-4 col-sm-offset-6 col-xs-12'>";
					$html .= "<p id='error-promo-code-message' style='color: red;'></p>";
					$html .= "<input type='submit' class='btn btn-sm btn-success' value='Сақтау'>";
				$html .= "</div>";
			$html .= "</div>";
		$html .= "</form>";
	} else {
		if (!$used_promo_code['student_promo_code_log_id']) {
			$html .= "<center>";
				$html .= "<p><span id='friends-name'><b>".$used_promo_code['last_name'].' '.$used_promo_code['first_name']."</b></span> досыңның промокодын енгіздің.</p>";
				$html .= "<p>Бірінші айға 20% жеңілдік аласың.</p>";
			$html .= "</center>";
		} else {
			$html .= "<center>";
				$html .= "<p><span id='friends-name'><b>".$used_promo_code['last_name'].' '.$used_promo_code['first_name']."</b></span> досыңның промокодын <i>'".$used_promo_code['subject_title']."'</i> пәніне қолдандың. </p>";
				$html .= "<p>Достарыңды енді өзіңнің промокодың арқылы шақырып, 100%-ға дейін жеңілдікке ие бол! Әр шақырған досың үшін 20% жеңілдік аласың</p>";
			$html .= "</center>";
		}
	}


	$html .= "</div>";
	echo $html;
?>