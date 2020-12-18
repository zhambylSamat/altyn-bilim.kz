<?php
	$self_promo_code = get_self_promo_code();

	$html = "";
	$html .= "<center>";
		$html .= "<p id='self-promo-code'>Сенің промокодың:&nbsp;&nbsp;<b>".$self_promo_code."</b></p>";
		$html .= '<i id="self-promo-code-subtitle">Осы промокод арқылы досыңды шақырсаң, екеуің де бір айға 20% жеңілдік аласыңдар!<br>Ол үшін промокодты досыңа түсіріп жібер.<br>Шақырылған досың академияға тіркелген соң "Промокодты қолдану" вкладкасында сенің промокодыңды енгізуі керек.</i>';
	$html .= "</center>";
	echo $html;
?>