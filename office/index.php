<!DOCTYPE html>
<html>
<head>
	<title>Мебель</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>
<body>
	<?php
		$prefix = '_photo.jpg';
		$html = "<div class='controller'><div class='row'>";
		$img_1 = "";
		$img_2 = "";
		$img_3 = "";
		$count = 0;
		for ($i=0; $i < 143; $i++) {
			// $html .= "<div class='col-md-4 col-sm-6 col-xs-12'><img class='img-responsive' src='img/".$i.$prefix."'></div>";
			$count++;
			if ($count == 1) {
				$img_1 .= "<img style='margin: 10px 0; border: 1px solid black;' class='img-responsive' src='img/".$i.$prefix."'>";
			} else if ($count == 2) {
				$img_2 .= "<img style='margin: 10px 0; border: 1px solid black;' class='img-responsive' src='img/".$i.$prefix."'>";
			} else if ($count == 3) {
				$img_3 .= "<img style='margin: 10px 0; border: 1px solid black;' class='img-responsive' src='img/".$i.$prefix."'>";
				$count = 0;
			}
		}
		$html .= "<div class='col-md-4 col-sm-6 col-xs-12'>";
			$html .= $img_2;
		$html .= "</div>";
		$html .= "<div class='col-md-4 col-sm-6 col-xs-12'>";
			$html .= $img_3;
		$html .= "</div>";
		$html .= "<div class='col-md-4 col-sm-6 col-xs-12'>";
			$html .= $img_1;
		$html .= "</div>";
		$html .= "</div></div>";
		echo $html;
	?>
</body>
</html>