<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_group_subject_schedules() {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT gi.id AS group_info_id,
							sj.title AS subject_title,
							gsh.week_day_id,
							gi.group_name
						FROM group_student gs,
							group_info gi,
							subject sj,
							group_schedule gsh
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND gi.is_archive = 0
							AND gs.is_archive = 0
							AND gsh.group_info_id = gi.id
							AND sj.id = gi.subject_id
							ORDER BY sj.title, gi.group_name, gsh.week_day_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('groups' => array(),
							'week_ids' => array());

			foreach ($query_result as $value) {
				if (!isset($result['groups'][$value['group_info_id']])) {
					$result['groups'][$value['group_info_id']] = array('subject_title' => $value['subject_title'],
																'group_name' => $value['group_name'],
																'schedules' => array());
				}
				if (!in_array($value['week_day_id'], $result['groups'][$value['group_info_id']]['schedules'])) {
					array_push($result['groups'][$value['group_info_id']]['schedules'], $value['week_day_id']);
				}
				if (!in_array($value['week_day_id'], $result['week_ids'])) {
					array_push($result['week_ids'], $value['week_day_id']);
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_holiday_by_date ($date) {
		GLOBAL $connect;

		try {

			$query = "SELECT h.title
						FROM holidays h
						WHERE DATE_FORMAT(:holiday_date, '%Y-%m-%d') BETWEEN DATE_FORMAT(h.from_date, '%Y-%m-%d') AND DATE_FORMAT(h.to_date, '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':holiday_date', $date, PDO::PARAM_STR);
			$stmt->execute();
			$holiday_comment = $stmt->fetch(PDO::FETCH_ASSOC)['title'];
			
			return $holiday_comment;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_this_week_dates () {
		$current_date = date('Y-m-d');
		$current_week_day_id = date('w') == 0 ? 7 : date('w');

		$result = array();
		for ($i = 1; $i < $current_week_day_id; $i++) {
			$date = date('Y-m-d', strtotime('-'.($current_week_day_id - $i).' days'));
			$result[$i] = $date;
		}

		$result[$current_week_day_id] = $current_date;

		for ($i = 7; $i > $current_week_day_id; $i--) {
			$date = date('Y-m-d', strtotime('-'.($current_week_day_id - $i).' days'));
			$result[$i] = $date;	
		}
		return $result;
	}
?>