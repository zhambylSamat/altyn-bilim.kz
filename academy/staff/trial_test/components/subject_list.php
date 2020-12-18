<?php
	$subject_list = get_active_subjects();

	$html = "";
	$html .= "<div class='row'>";
	foreach ($subject_list as $subject_id => $title) {
		$html .= "<div class='col-md-4 col-sm-4 col-xs-12'>";
			$html .= "<button class='btn btn-sm btn-block btn-default choose-subject-to-trial-test' choosen='0' data-subject-id='".$subject_id."'>".$title."</button>";
		$html .= "</div>";
	}
	$html .= "</div>";
	$html .= "<hr>";
	echo $html;
?>