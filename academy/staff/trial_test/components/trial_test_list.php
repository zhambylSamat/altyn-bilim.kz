<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/trial_test/views.php');

	if (!isset($_GET['subject_id'])) {
		$html = "<center><h3>Пәнді таңда</h3></center>";
	} else {
		$subject_id = $_GET['subject_id'];

		$trial_test_list = get_trial_tests($subject_id);

		$count = 0;
		$html = "";
		$html .= "<table class='table table-striped table-bordered'>";
		foreach ($trial_test_list as $trial_test_id => $value) {
			$html .= "<tr>";
				$html .= "<td>".(++$count)."</td>";
				$html .= "<td>";
					$html .= "<div class='trial-test-title-box'>";
						$html .= "<a href='#' class='trial-test-title-link' data-trial-test-id='".$trial_test_id."'>".$value['title']."</a>";
						if ($value['files_count'] == 0 && $value['answers_count'] == 0) {
							$html .= "<button style='margin-left: 5%;' class='btn btn-xs btn-danger pull-right remove-trial-test' data-trial-test-id='".$trial_test_id."'><i class='fas fa-trash-alt'></i></button> ";
						}
						$html .= " <button class='btn btn-xs btn-info pull-right edit-trial-test-title-btn'><i class='fas fa-pen'></i></button> ";
					$html .= "</div>";
				$html .= "</td>";
				$no_true_ans_numerations_html = $value['no_true_ans_numerations'] != "" ? "<span class='text-danger pull-right'>".$value['no_true_ans_numerations'].";</span>" : "";
				$html .= "<td><b>Cуреттер: ".$value['files_count']."; Жауаптар: ".$value['answers_count']."; ".$no_true_ans_numerations_html."</b></td>";
			$html .= "</tr>";
		}
			$html .= "<tr>";
				$html .= "<td colspan='3'><button class='btn btn-info btn-sm btn-block open-trial-test-form' data-subject-id='".$subject_id."'>+ Пробный тест енгізу</button></td>";
			$html .= "</tr>";
		$html .= "</table>";
	}
	$html .= "<hr>";
	$html .= "<div id='trial-test-content'>";
		$html .= "<center><h4>Пробный тестті таңда</h4></center>";	
	$html .= "</div>";
	echo $html;
?>