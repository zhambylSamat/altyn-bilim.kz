<?php

	if (!isset($_GET['group']) || !isset($_GET['page'])) {
		header('Location:index.php');
	} else {
		$group_id = $_GET['group'];
		include_once($root.'/staff/group/view.php');
	}

	$subject_id = 0;
	$topic_id = 0;
	$subtopic_id = 0;

	$lesson_progress_id = 0;

	$result = get_group_info_with_lesson_progress($group_id);

	if (count($result['lesson_progress']) > 0) {
		$lesson_progress_id = end(end(end($result['lesson_progress'])['topics'])['subtopics'])['lp_id'];
	}
?>

<?php
	include_once('components/group_student_trial_test_notification.php');
	include_once('components/time_to_trial_test_notification.php');
	include_once('components/add_student_to_group.php');
	include_once('components/magic_button.php');
	include_once($root.'/common/global_controller.php');
?>

<table class='table table-bordered table-striped'>
	<?php

		$html = '';
		$html .= '<tr>';
			$html .= "<td><i>Группаның аты:</i> <b>".$result['group_name']."</b></td>";
			$html .= "<td colspan='2'><i>Группаның алғашқы сабағы:</i> <b>".$result['start_date']."</b></td>";
		$html .= '</tr>';
		$html .= "<tr><td colspan='3'><i>Сабақ кестесі:</i> <b>";
		foreach ($result['schedule'] as $week_day_id) {
			$html .= $day_name[$week_day_id].' ';
		}
		$html .= "</b></td></tr>";

		$html .= "<tr><td colspan='3'></td></tr>";

		$html .= "<tr>";
			$html .= "<td><i>Пән: </i>";
				$subject = end($result['lesson_progress']);
				$subject_id = $subject['id'];
				$html .= "<b>".$subject['title']."</b>";
			$html .= "</td>";

			$html .= "<td><i>Тарау: </i>";
			if (count($result['lesson_progress']) == 0) {
				$html .= "<b>Сабақ басталмады</b>";
			} else {
				if ($result['lesson_type'] == 'topic') {
					$topic = reset(reset($result['lesson_progress'])['topics']);
					$topic_id = $topic['id'];
					$html .= "<b>".$topic['title']."</b>";

				} else if ($result['lesson_type'] == 'subject') {
					$html .= "<select class='form-control' id='select-topic'>";
					$topics = end($result['lesson_progress'])['topics'];
					for ($i=1; $i <= count($topics); $i++) {
						$topic_id = $topics[$i]['id'];
						$html .= "<option value='".$topic_id."' ".($i == count($topics)) ? 'selected' : ''.">";
							$html .= $topics[$i]['title'];
						$html .= "</option>";
					}
					$html .= "</select>";
				}
			}
			$html .= "</td>";

			$html .= "<td><i>Тақырып: </i>";
			if (count($result['lesson_progress']) == 0) {
				$html .= "<b>Сабақ басталмады</b>";
			} else {
				$html .= "<select class='form-control' id='select-subtopic'>";
				$subtopics = reset(reset($result['lesson_progress'])['topics'])['subtopics'];
				for ($i=1; $i <= count($subtopics); $i++) { 
					$subtopic_id = $subtopics[$i]['id'];
					$selected = ($i == count($subtopics)) ? 'selected' : '';
					$html .= "<option value='".$subtopics[$i]['lp_id']."' ".$selected.">";
						$html .= $subtopics[$i]['title'];
					$html .= "</option>";
				}
				$html .= "</select>";
			}
			$html .= "</td>";

		$html .= "</tr>";

		echo $html;
	?>
</table>

<div id='students-table'>
	<?php
		include_once('components/student_list.php');
	?>
</div>
<?php
	if (get_is_army_group($group_id)) {
		include_once($root.'/staff/group/group/components/student_army_medal.php');
	}
?>