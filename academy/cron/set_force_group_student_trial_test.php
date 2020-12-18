<?php
	include_once('../common/connection.php');
	include_once('../send_sms/index.php');
	include_once('../send_sms/sms_statuses.php');

	$group_info_ids = get_all_group_info_ids();
	echo json_encode($group_info_ids, JSON_UNESCAPED_UNICODE)."<br><br>";
	$student_infos = set_group_student_trial_test($group_info_ids);
	echo json_encode($student_infos, JSON_UNESCAPED_UNICODE)."<br><br>";

	foreach ($student_infos as $student_id => $student) {
		$text = "";
		$fio = $student['last_name'].' '.$student['first_name'];
		$recipient = '7'.$student['phone'];

		$subject_arr = array();
		foreach ($student['subjects'] as $subject_id => $subject_title) {
			array_push($subject_arr, $subject_title);
		}

		if (count($subject_arr) == 1) {
			$text .= $subject_arr[0].' пәнінен ';
		} else {
			$text .= implode(', ', $subject_arr).' пәндерінен ';
		}

		$text .= 'пробный тест берілді. Жеке кабинетке кіріп тестті орындап таста ';
		$text = kiril2latin($text);

		$recipient_info = array('recipient' => $recipient,
								'text' => $text);
		// tmp_send_sms_save($recipient, $text, $fio);
		$sms_response = send_sms($recipient_info, $fio);
		save_to_db($sms_response);
	}


	function tmp_send_sms_save ($recipient, $text, $fio) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO send_sms_tmp (recipient, text, fio)
											VALUES (:recipient, :text, :fio)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':recipient', $recipient, PDO::PARAM_STR);
			$stmt->bindParam(':text', $text, PDO::PARAM_STR);
			$stmt->bindParam(':fio', $fio, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function set_group_student_trial_test ($group_info_ids) {
		GLOBAL $connect;

		try {

			$student_infos = array();

			foreach ($group_info_ids as $value) {
				$last_trial_test_time = get_last_trial_test_time($value['group_info_id']);

				$date1 = date_create($last_trial_test_time);
				$date2 = date_create(date('Y-m-d'));
				$days = date_diff($date1, $date2)->format('%a');

				if ($days >= 14) { // && date('N', strtotime(date('Y-m-d'))) == 5
					$student_info = insert_group_student_trial_test($value['group_info_id']);

					foreach ($student_info as $student_id => $val) {
						if (!isset($student_infos[$student_id])) {
							$student_infos[$student_id] = array('last_name' => $val['last_name'],
																'first_name' => $val['first_name'],
																'phone' => $val['phone'],
																'subjects' => array());
						}

						if (!isset($student_infos[$student_id]['subjects'][$val['subject_id']])) {
							$student_infos[$student_id]['subjects'][$val['subject_id']] = $val['subject_title'];
						}
					}
				}
			}
		
			return $student_infos;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student_trial_test ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id
						FROM lesson_progress lp
						WHERE lp.group_info_id = :group_info_id
						ORDER BY lp.created_date DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$lesson_progress_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			$query = "SELECT lp.group_info_id,
							gi.subject_id,
							sj.title AS subject_title
						FROM lesson_progress lp,
							group_info gi,
							subject sj
						WHERE lp.id = :lesson_progress_id
							AND gi.id = lp.group_info_id
							AND sj.id = gi.subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "SELECT tt.id,
							tt.title
						FROM trial_test tt
						WHERE tt.subject_id = :subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $group_info['subject_id'], PDO::PARAM_INT);
			$stmt->execute();
			$trial_test_result = $stmt->fetchAll();

			$trial_test_list = array();
			foreach ($trial_test_result as $value) {
				array_push($trial_test_list, $value['id']);
			}

			$student_infos = array();

			$query = "SELECT gs.student_id,
							s.last_name,
							s.first_name,
							s.phone
						FROM group_student gs,
							student s
						WHERE gs.is_archive = 0
							AND s.id = gs.student_id
							AND gs.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info['group_info_id'], PDO::PARAM_INT);
			$stmt->execute();
			$student_list = $stmt->fetchAll();

			$student_trial_test_ids = array();
			foreach ($student_list as $value) {
				$student_trial_test_id = set_student_trial_test($value['student_id'], $trial_test_list);
				array_push($student_trial_test_ids, $student_trial_test_id);
				$data['ok'][$value['student_id']] = $student_trial_test_id;
				// $student_trial_test_id = 1;
				if ($student_trial_test_id != 0) {
					$query = "INSERT INTO group_student_trial_test (student_trial_test_id, lesson_progress_id)
															VALUES (:student_trial_test_id, :lesson_progress_id)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':student_trial_test_id', $student_trial_test_id, PDO::PARAM_INT);
					$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
					$stmt->execute();

					$student_infos[$value['student_id']] = array('last_name' => $value['last_name'],
																'first_name' => $value['first_name'],
																'phone' => $value['phone'],
																'subject_id' => $group_info['subject_id'],
																'subject_title' => $group_info['subject_title']);
				}
			}

			if (count($student_trial_test_ids) > 0) {
				$query = "INSERT INTO group_trial_test (group_info_id) VALUES (:group_info_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info['group_info_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			return $student_infos;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_group_info_ids () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id,
							(SELECT ag.id
							FROM army_group ag
							WHERE ag.group_info_id = gi.id) AS army_group_id
						FROM group_info gi
						WHERE gi.status_id = 2
							AND 0 < (SELECT count(gs.id)
									FROM group_student gs
									WHERE gs.group_info_id = gi.id
										AND gs.is_archive = 0)
							AND DATE_FORMAT(gi.start_date, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, array('group_info_id' => $value['id'],
											'army_group_id' => $value['army_group_id']));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_army_group_info_ids () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id
						FROM group_info gi
						WHERE gi.status_id = 2
							AND gi.id IN (SELECT ag.group_info_id
											FROM army_group ag)";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_last_trial_test_time ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT DATE_FORMAT(gtt.appointment_date, '%Y-%m-%d') AS appointment_date
						FROM group_trial_test gtt
						WHERE gtt.group_info_id = :group_info_id
						ORDER BY gtt.appointment_date DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 1) {
				$appointment_date = $stmt->fetch(PDO::FETCH_ASSOC)['appointment_date'];
				return $appointment_date;
			}

			$query = "SELECT DISTINCT gs.transfer_from_group
						FROM group_student gs
						WHERE group_info_id = :group_info_id
							AND transfer_from_group IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "SELECT gi.start_date
							FROM group_info gi
							WHERE gi.id = :group_info_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$start_date = $stmt->fetch(PDO::FETCH_ASSOC)['start_date'];
				return $start_date;
			}

			$transfer_from_group = $stmt->fetch(PDO::FETCH_ASSOC)['transfer_from_group'];

			return get_last_trial_test_time($transfer_from_group);
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>