<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/trial_test/view.php');

	$available_trial_tests = get_available_trail_tests();

	// print_r($available_trial_tests);

	$html = "<div class='row'><div class='col-md-12 col-sm-12 col-xs-12'>";
		foreach ($available_trial_tests as $student_trial_test_id => $value) {
			$html .= "<div class='available-trial-test-box'>";
				$html .= "<center>";
					$html .= "<p class='title'>".$value['subject_title']." пәнінен міндетті пробный тест</p>";
					$html .= "<a href='".$ab_root."/academy/student/trial_test/components/testing.php?student_trial_test_id=".$student_trial_test_id."' class='btn btn-sm btn-warning'>Тестті бастау</a>";
				$html .= "</center>";
			$html .= "</div>";
		}
	$html .= "</div></div>";
	$html .= "<hr>";
	echo $html;
?>