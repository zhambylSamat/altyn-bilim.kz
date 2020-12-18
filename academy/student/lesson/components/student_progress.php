<?php
	$student_progress = get_students_progress();
?>

<div class='col-md-12 col-sm-12 col-xs-12'>
	<h3 style='margin-top: 0px;'>Оқу прогресің:</h3>
</div>

<?php
	$html = "";
	foreach ($student_progress as $value) {
		$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
			$html .= "<table class='table'>";
				$html .= "<tr>";
					$html .= "<td style='width: 20%;'>";
						$html .= "<h4>".$value['subject_title'].":</h4>";
					$html .= "</td>";

					$html .= "<td style='width: 80%;'>";
						$html .= "<div class='progress' style='margin-top:2%;'>";
							$progress_bar_color_class = $value['progress'] == 100 ? 'progress-bar-success' : "progress-bar-info";
							$html .= "<div class='progress-bar ".$progress_bar_color_class."' role='progressbar' aria-valuenow='".$value['progress']."' aria-valuemin='0' aria-valuemax='100' style='width: ".$value['progress']."%'>";
								$html .= $value['progress']."%";
							$html .= "</div>";
						$html .= "</div>";
					$html .= "</td>";
				$html .= "</tr>";
			$html .= "</table>";
		$html .= "</div>"; // .col-...
	}
	echo $html;
?>