<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/lesson/view.php');
	include_once($root.'/common/global_controller.php');
	include($root.'/common/constants.php');

	if (!isset($_SESSION['group_id'])) {
		$_SESSION['group_id'] = 0;
	}
	$group_id = $_SESSION['group_id'];

	if ($group_id == 0) {
		$total_coins = get_left_coins();
		include_once($root.'/student/lesson/components/choose_lessons.php');

		$html = '';
		// $html = "<div class='container'>";
			// $html .= "<div class='row'>";
				$html .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
					$html .= "<div id='total-coins' class='pull-right'>";
						$html .= "<span id='total-coins-title'>Монеталарың: </span>";
						$html .= "<span id='total-coins-coin'>".$total_coins."</span>";
						$html .= "&nbsp;&nbsp;";
						$html .= "<span id='total-coins-img'><img src='img/coin.png'></span>";
					$html .= "</div>";
				$html .= "</div>";
			// $html .= "</div>";
		// $html .= "</div>";

		echo $html;
		include($root.'/student/lesson/components/freeze_lesson.php');
		include_once($root.'/student/lesson/components/full_lesson_prices.php');
		include_once($root.'/student/lesson/components/student_progress.php');
		include_once($root.'/student/lesson/components/student_plans.php');
	} else {
		if (isset($_SESSION['student_trial_test_info']) && count($_SESSION['student_trial_test_info'])) {
			include_once($root.'/student/lesson/components/trial_test_exist_info.php');
		} else {
			include_once($root.'/student/lesson/components/lesson.php');
		}
	}
?>