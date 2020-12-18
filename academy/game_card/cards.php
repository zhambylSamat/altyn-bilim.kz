<?php
	include_once('views.php');
	$cards = get_cards();
?>
<div id='choose-subject'>
	<?php
		$html = "<div class='col-xs-12 hidden-lg hidden-md hidden-sm' style='width: 100%; height: 20vh;'></div>";
		foreach ($cards as $card) {
			$html .= "<div class='col-md-4 col-sm-4 col-xs-4'>";
				$class = '';
				if ($card['status'] == 'continue') {
					$html .= "<center><img src='".$card['cover']."' class='subject-cover-img choose-subject-img' data-dir='".$card['dir']."' data-status='".$card['status']."'></center>";
				} else if ($card['status'] == 'stop') {
					$html .= "<center><img src='".$card['cover']."' class='topic-cover-img choose-topic-img' data-dir='".$card['dir']."'></center>";
				}
				
			$html .= "</div>";
		}
		echo $html;
	?>
</div>