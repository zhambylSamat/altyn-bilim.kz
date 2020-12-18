<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/group/view.php');

	if (!isset($lesson_progress_id)) {
		header('Location:index.php');
	}

	$group_student_trial_test_list = get_group_student_trial_test_list($lesson_progress_id);

	// print_r($group_student_trial_test_list);
	// echo json_encode($group_student_trial_test_list, JSON_UNESCAPED_UNICODE);

	$html = "<div class='row'><div class='col-md-12 col-sm-12 col-xs-12'>";
	if (count($group_student_trial_test_list) == 0) {
		$html .= "<div class='set-trial-test-box'>";
			$html .= "<center>";
				$html .= "<p id='title'>Группадағы оқушыларға пробный тестке доступ беру</p>";
				$html .= "<button class='btn btn-sm btn-success set-group-student-trial-test' data-lp-id='".$lesson_progress_id."'>Пробный тестті оқушыларға жіберу</button>";
			$html .= "</center>";
		$html .= "</div>";
	} else {
		$html .= "<table class='table table-striped table-bordered'>";
		$html .= "<tr>";
			$html .= "<th>#</th>";
			$html .= "<th>Оқушының аты-жөні</th>";
			$html .= "<th>Берілген нұсқа</th>";
			$html .= "<th>Берілген уақыт</th>";
			$html .= "<th>Баллы</th>";
			$html .= "<th>Тестті бітірген уықыты</th>";
		$html .= "</tr>";
		$count = 0;
		foreach ($group_student_trial_test_list as $student_trial_test_id => $value) {
			$html .= "<tr>";
				$html .= "<td><center>".(++$count)."</center></td>";
				$html .= "<td>".$value['last_name'].' '.$value['first_name']."<br>+7 ".$value['phone']."</td>";
				$html .= "<td>".$value['trial_test_title']."</td>";
				$html .= "<td>".$value['appointment_date']."</td>";
				$result_link = $value['result']['actual_result'].'/'.$value['result']['total_result'];
				if ($value['result']['actual_result'] != '') {
					$result_link = "<a target='_blank' href='".$ab_root."/academy/student/trial_test/components/testing.php?student_trial_test_id=".$student_trial_test_id."'>".$value['result']['actual_result'].'/'.$value['result']['total_result']."</a>";
				}
				$html .= "<td>".$result_link."</td>";
				$html .= "<td>".($value['submit_date'] == '' ? '-' : $value['submit_date'])."</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
	}
	$html .= "</div></div>";

	echo $html;
?>