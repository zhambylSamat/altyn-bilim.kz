<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/intensive/view.php');
	include($root.'/common/constants.php');

	$group_student_list = get_group_student_list();

	// echo json_encode($group_student_list, JSON_UNESCAPED_UNICODE);

	$html = "";
	$html .= "<table class='table table-striped table-bordered'>";
		$html .= "<tr>";
			$html .= "<th>Қазіргі сабақтар</th>";
			$html .= "<th>Сабақ кестесі</th>";
			$html .= "<th></th>";
		$html .= "</tr>";
	foreach ($group_student_list['data'] as $value) {
		$row_color_class = "";
		if (count($value['schedules']) == 6) {
			$row_color_class = 'success';
		}
		$html .= "<tr class='".$row_color_class."'>";
			$html .= "<td>".$value['subject_title'].' | '.$value['group_name']."</td>";
			$html .= "<td>";
				foreach ($value['schedules'] as $schedule) {
					$html .= $day_name[$schedule].' ';
				}
			$html .= "</td>";
			$html .= "<td style='vertical-align: middle;'>";
				if (count($value['schedules']) == 6) {
					$html .= "<button class='btn btn-warning btn-sm unset-intensive-course' data-group-student-id='".$value['group_student_id']."'>Қалыпты сабақ <br class='hidden-lg hidden-md hidden-sm'>кестесіне ауысу</button>";
				} else {
					$html .= "<button class='btn btn-success btn-sm set-intensive-course' data-group-student-id='".$value['group_student_id']."'>Интенсивке <br class='hidden-lg hidden-md hidden-sm'>ауысу</button>";
				}
			$html .= "</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";

	echo $html;
?>