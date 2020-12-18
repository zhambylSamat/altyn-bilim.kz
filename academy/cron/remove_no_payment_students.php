<?php
	include_once('../common/connection.php');
	include_once('../common/global_controller.php');
	$n_days = 14;
	$students_with_no_payments = get_group_students_with_no_payments($n_days);
	remove_selected_group_students($students_with_no_payments);


	function get_group_students_with_no_payments($n_days) {
		GLOBAL $connect;

		try {

			$result = array();
			$group_schedules = array();

			$query = "SELECT gs.id,
							gs.group_info_id,
							(SELECT CONCAT(gsp.id, '!', DATE_FORMAT(gsp.finished_date, '%Y-%m-%d'))
							FROM group_student_payment gsp
							WHERE gsp.group_student_id = gs.id
							ORDER BY gsp.finished_date DESC
							LIMIT 1) AS gsp,
							gi.start_date,
							s.last_name,
							s.first_name,
							gi.group_name
						FROM group_student gs,
							group_info gi,
							student s
						WHERE gs.is_archive = 0	
							AND gs.status = 'inactive'
							AND gi.id = gs.group_info_id
							AND s.id = gs.student_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_group_students = $stmt->fetchAll();
			// echo json_encode($sql_group_students, JSON_UNESCAPED_UNICODE)."<br><br>";
			foreach ($sql_group_students as $gs) {
				if (!isset($group_schedules[$gs['group_info_id']])) {
					$group_schedules[$gs['group_info_id']] = get_group_schedules($gs['group_info_id']);
				}
				$group_started_date = $gs['start_date'];
				$gsp_id = '';
				$gsp_finished_date = '';
				if ($gs['gsp'] != '') {
					$gsp = explode('!', $gs['gsp']);
					$gsp_id = $gsp[0];
					$gsp_finished_date = $gsp[1];
				}

				$date_point = $gsp_id != '' ? strtotime($gsp_finished_date) : strtotime($group_started_date);
				$no_payment_days = 0;
				while (strtotime(date('Y-m-d')) >= $date_point) {
					$week_day_id = date('w', $date_point);
					$week_day_id = $week_day_id == 0 ? 7 : $week_day_id;
					if (in_array($week_day_id, $group_schedules[$gs['group_info_id']])) {
						$no_payment_days++;
					}
					if ($no_payment_days >= $n_days) {
						array_push($result, array('group_student_id' => $gs['id'],
													'group_student_payment_id' => $gsp_id,
													'group_info_id' => $gs['group_info_id'],
													'last_name' => $gs['last_name'],
													'first_name' => $gs['first_name'],
													'group_name' => $gs['group_name']));
						break;
					}
					$date_point = strtotime(date('Y-m-d', $date_point).' + 1 days');
				}
			}
			// echo json_encode($result, JSON_UNESCAPED_UNICODE);
			// return array();
			return $result;
		} catch (Exception $e) {
			throw $e;
			// return array();
		}
	}

	function remove_selected_group_students($data) {
		GLOBAL $connect;

		try {

			$remove_from_group = "DELETE FROM group_student WHERE id = :group_student_id";
			$to_archive = "UPDATE group_student SET is_archive = 1 WHERE id = :group_student_id";

			foreach ($data as $v) {
				if ($v['group_student_payment_id'] == '') {
					$stmt = $connect->prepare($remove_from_group);
					$stmt->bindParam(':group_student_id', $v['group_student_id'], PDO::PARAM_INT);
					$stmt->execute();
				} else {
					$stmt = $connect->prepare($to_archive);
					$stmt->bindParam(':group_student_id', $v['group_student_id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>