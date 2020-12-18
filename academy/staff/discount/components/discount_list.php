<?php
	$discount_list = get_discount_list();
?>
<table class='table table-striped table-bordered'>
	<?php
		$html = "";
		$count = 0;
		foreach ($discount_list as $discount_id => $value) {
			$count++;
			$month = '';
			if ($value['for_month'] == '-1') {
				$month = 'Толық курсқа';
			} else {
				$month = $value['for_month'].' айға';
			}
			$promo_code_info = "";
			if ($value['cant_insert_promo_code'] == 1) {
				$promo_code_info = "Досының промокодын енгізе алмайды.";
			}
			$html .= "<tr>";
				$html .= "<td><center>".$count."</center></td>";
				$html .= "<td>".$value['title']."</td>";
				$html .= "<td>".$month."</td>";
				$html .= "<td>".$value['text']."</td>";
				$html .= "<td>".$promo_code_info."</td>";
			$html .= "</tr>";
		}
		echo $html;
	?>
</table>
<hr>