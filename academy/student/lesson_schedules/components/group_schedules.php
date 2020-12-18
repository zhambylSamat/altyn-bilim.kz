<style type="text/css">
	#schedules-table {
		text-align: center;
	}
</style>
<?php
	$week_day_str = array('', 'Дүйсенбі', 'Сейсенбі', 'Сәрсенбі', 'Бейсенбі', 'Жұма', 'Сенбі');	
	$schedules = get_group_subject_schedules();
	// $holidays = get_holiday_by_date($date);
	$this_week_dates = get_this_week_dates();
	// echo json_encode($this_week_dates, JSON_UNESCAPED_UNICODE);

	$html = "<center><p id='lesson-schedule-title'>Сабақ кестесі</p></center><br>";
	$html .= "<table class='table table-bordered table-striped' id='schedules-table'>";
		$html .= "<tr>";
		foreach ($week_day_str as $key => $value) {
			if ($value == '') {
				$html .= "<td>#</td>";
			} else {
				$class = '';
				if (in_array($key, $schedules['week_ids'])) {
					$class = '';
				}
				$html .= "<td class='".$class."'>".$value."</td>";
			}
		}
		$html .= "</tr>";

		foreach ($schedules['groups'] as $value) {
			$html .= "<tr>";
				$html .= "<td>".$value['subject_title']."<br>".$value['group_name']."</td>";
				for ($i = 1; $i <= 6; $i++) {
					$holiday = get_holiday_by_date($this_week_dates[$i]);
					if ($holiday != '') {
						$html .= "<td class='warning'>".$holiday." мейрамы</td>";
					} else if (in_array($i, $value['schedules'])) {
						$html .= "<td class='info'>7:00:00 де жаңа тақырып ашылады</td>";
					} else {
						$html .= "<td></td>";
					}
				}
			$html .= "</tr>";
		}
	$html .= "</table>";
	echo $html;
?>
