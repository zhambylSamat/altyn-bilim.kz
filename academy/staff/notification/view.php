<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_no_payment_and_started_students() {
		GLOBAL $connect;

		try {
			$result = array('datas' => array(),
							'not_seleceted_count' => 0);

			$query = "SELECT s.id AS student_id,
							s.last_name,
							s.first_name,
							s.phone,
							gi.id AS group_id,
							gi.group_name,
							gi.start_date AS group_start_date,
							gs.id AS group_student_id,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order,
							ns.id AS ns_id,
							(SELECT DATE_FORMAT(gsp.finished_date, '%Y-%m-%d')
							FROM group_student_payment gsp
							WHERE gsp.group_student_id = gs.id
							ORDER BY gsp.finished_date DESC
							LIMIT 1) AS last_payment_date
						FROM group_student gs
						INNER JOIN student s
							ON s.id = gs.student_id
						INNER JOIN subtopic st
							ON st.id = gs.start_from
						INNER JOIN group_info gi
							ON gi.status_id = 2
								AND gi.id = gs.group_info_id
						LEFT JOIN notification_selection ns
							ON ns.object_id = gs.id
								AND ns.object_type_id = 3
						WHERE gs.start_from != 0
							AND gs.status = 'inactive'
							AND gs.is_archive = 0
							AND gs.start_from IN (SELECT lp2.subtopic_id
														FROM lesson_progress lp2
														WHERE lp2.group_info_id = gs.group_info_id)
							ORDER BY s.last_name, s.first_name";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();
			foreach ($sql_result as $val) {
				if ($val['ns_id'] == '' || $val['ns_id'] == null) {
					$result['not_seleceted_count']++;
				}

				if ($val['last_payment_date'] != '') {
					$start_date = strtotime($val['last_payment_date']);
				} else {
					// $start_date = strtotime(get_lesson_progress_by_group_and_subtopic($val['group_id'], $val['subtopic_id'])['created_date']);	
					$start_date = strtotime($val['group_start_date']);
				}
				
				$start_date = date('d.m.Y', $start_date);
				array_push($result['datas'], array('student_id' => $val['student_id'],
										'last_name' => $val['last_name'],
										'first_name' => $val['first_name'],
										'phone' => $val['phone'],
										'group_id' => $val['group_id'],
										'group_student_id' => $val['group_student_id'],
										'group_name' => $val['group_name'],
										'subtopic_title' => $val['subtopic_title'],
										'subtopic_id' => $val['subtopic_id'],
										'start_date' => $start_date,
										'ns_id' => $val['ns_id']));
			}
			// $last_name = array_column($result['datas'], 'last_name');
			// array_multisort($last_name, SORT_ASC, $result['datas']);
			function sortFunction_no_payment( $a, $b ) {
			    return strtotime($b["start_date"]) - strtotime($a["start_date"]);
			}
			usort($result['datas'], "sortFunction_no_payment");
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_no_payment_and_started_students_from_registration_course() {
		GLOBAL $connect;

		try {

			$result = array('datas' => array(),
							'not_seleceted_count' => 0);
			$query = "SELECT s.id AS student_id,
							s.last_name, 
							s.first_name,
							s.phone,
							gi.id AS group_id,
							gi.group_name,
							rc.id AS course_id,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order,
							ns.id AS ns_id
						FROM registration_course rc
						INNER JOIN group_info gi
							ON gi.status_id = 2
								AND gi.id = rc.group_info_id
						INNER JOIN subtopic st
							ON st.id = rc.subtopic_id
						INNER JOIN student s
							ON s.id = rc.student_id
						LEFT JOIN notification_selection ns
							ON ns.object_id = rc.id
								AND ns.object_type_id = 4
						WHERE rc.is_done = 0
							AND rc.subtopic_id IN (SELECT lp2.subtopic_id
													FROM lesson_progress lp2
													WHERE lp2.group_info_id = rc.group_info_id)
						ORDER BY s.last_name, s.first_name";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$res = $stmt->fetchAll();
			foreach ($res as $val) {
				if ($val['ns_id'] == '' || $val['ns_id'] == null) {
					$result['not_seleceted_count']++;
				}
				$start_date = date('d.m.Y', strtotime(get_lesson_progress_by_group_and_subtopic($val['group_id'], $val['subtopic_id'])['created_date']));
				array_push($result['datas'], array('student_id' => $val['student_id'],
													'last_name' => $val['last_name'],
													'first_name' => $val['first_name'],
													'phone' => $val['phone'],
													'course_id' => $val['course_id'],
													'group_name' => $val['group_name'],
													'subtopic_title' => $val['subtopic_title'],
													'subtopic_id' => $val['subtopic_id'],
													'start_date' => $start_date,
													'ns_id' => $val['ns_id']));
			}
			$start_date = array_column($result['datas'], 'last_name');
			array_multisort($start_date, SORT_ASC, $result['datas']);
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_starting_students($n) {
		GLOBAL $connect;

		try {
			$result = array('no_payment_count' => 0,
							'datas' => array(),
							'datas2' => array());

			$query = "SELECT s.id AS student_id,
							s.last_name, 
							s.first_name,
							s.phone,
							gi.id AS group_id,
							gi.group_name,
							gi.start_date AS group_start_date,
							gs.id AS group_student_id,
							gs.start_date,
							gs.has_payment,
							gs.status,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order,
							ns.id AS ns_id,
							gs.transfer_from_group
						FROM group_student gs
						INNER JOIN student s
							ON s.id = gs.student_id
						INNER JOIN subtopic st
							ON st.id = gs.start_from
						INNER JOIN group_info gi
							ON gi.status_id = 2
								AND gi.id = gs.group_info_id
						LEFT JOIN notification_selection ns
							ON ns.object_id = gs.id
								AND ns.object_type_id = 1
						WHERE gs.start_from != 0
						ORDER BY s.last_name, s.first_name";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$res = $stmt->fetchAll();
			foreach ($res as $val) {
				$last_lesson_progress = get_last_lesson_progress_by_group($val['group_id']);
				$is_set = false;
				if (count($last_lesson_progress) == 0) {
					$start_date = date('Y-m-d', strtotime($val['group_start_date']));
					if ($val['subtopic_order'] > 1) {
						$order = 3;
						$schedules = get_schedule_of_group($val['group_id']);
						while ($order <= $val['subtopic_order']) {
							$start_date = date('Y-m-d', strtotime($start_date.' + 1 days'));
							$week_day_id = date('w', strtotime($start_date)) == 7 ? 0 : intval(date('w', strtotime($start_date)));
							if (in_array($week_day_id, $schedules)) {
								$order++;
							}
						}
					}

					$date1 = date_create(date('Y-m-d'));
					$date2 = date_create($start_date);
					$diff = date_diff($date1, $date2);
					if ($diff->format('%a') <= $n) {
						$is_set = true;
						$learned = false;
						$start_date = date('d.m.Y', strtotime($start_date));
					}
				} else if (intval($val['subtopic_order']) > intval($last_lesson_progress['subtopic_order'])
					&& (intval($val['subtopic_order']) - $last_lesson_progress['subtopic_order']) <= $n) {
					$is_set = true;
					$learned = false;
					$start_date = date('d.m.Y', strtotime($last_lesson_progress['created_date']));
					for ($i = intval($last_lesson_progress['subtopic_order']); $i < $val['subtopic_order']; $i++) { 
						$start_date = learn_date($val['group_id'], $start_date);
					}
				} else if ($val['transfer_from_group'] == '' && $val['ns_id'] == ''
						&& intval($val['subtopic_order']) <= intval($last_lesson_progress['subtopic_order'])) {
					$is_set = true;
					$learned = true;
					$start_date = date('d.m.Y', strtotime($val['group_start_date']));
					// for ($i = intval($last_lesson_progress['subtopic_order']); $i < $val['subtopic_order']; $i++) { 
					// 	$start_date = learn_date($val['group_id'], $start_date);
					// }
				}
				if ($is_set) {
					if ($val['status'] == 'inactive') {
						$result['no_payment_count']++;
					}
					if (!isset($result[$val['student_id']])) {
						$result['datas'][$val['student_id']] = array('last_name' => $val['last_name'],
																	'first_name' => $val['first_name'],
																	'phone' => $val['phone'],
																	'groups' => array());
					}
					$result['datas'][$val['student_id']]['groups'][$val['group_id']] = array('group_student_id' => $val['group_student_id'],
																								'group_name' => $val['group_name'],
																								'subtopic_title' => $val['subtopic_title'],
																								'subtopic_id' => $val['subtopic_id'],
																								'start_date' => $start_date,
																								'learned' => $learned,
																								'ns_id' => $val['ns_id'],
																								'status' => $val['status'],
																								'has_payment' => $val['has_payment'] == 0 ? false : true);
					array_push($result['datas2'], array('student_id' => $val['student_id'],
														'last_name' => $val['last_name'],
														'first_name' => $val['first_name'],
														'phone' => $val['phone'],
														'group_id' => $val['group_id'],
														'group_student_id' => $val['group_student_id'],
														'group_name' => $val['group_name'],
														'subtopic_title' => $val['subtopic_title'],
														'subtopic_id' => $val['subtopic_id'],
														'start_date' => $start_date,
														'learned' => $learned,
														'ns_id' => $val['ns_id'],
														'status' => $val['status'],
														'has_payment' => $val['has_payment'] == 0 ? false : true));
				}
			}
			// $start_date = array_column($result['datas2'], 'start_date');
			// array_multisort($start_date, SORT_ASC, $result['datas2']);
			function sortFunction( $a, $b ) {
			    return strtotime($a["start_date"]) - strtotime($b["start_date"]);
			}
			usort($result['datas2'], "sortFunction");
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_reserve_students($n) {
		GLOBAL $connect;

		try {

			$result = array('no_payment_count' => 0,
							'datas' => array(),
							'datas2' => array());

			$query = "SELECT s.id AS student_id,
							s.last_name, 
							s.first_name,
							s.phone,
							gi.id AS group_id,
							gi.group_name,
							rc.id AS course_id,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order,
							ns.id AS ns_id
						FROM registration_course rc
						INNER JOIN group_info gi
							ON gi.status_id = 2
								AND gi.id = rc.group_info_id
						INNER JOIN subtopic st
							ON st.id = rc.subtopic_id
						INNER JOIN student s
							ON s.id = rc.student_id
						LEFT JOIN notification_selection ns
							ON ns.object_id = rc.id
								AND ns.object_type_id = 2
						WHERE rc.is_done = 0
							AND rc.subtopic_id NOT IN (SELECT lp2.subtopic_id
														FROM lesson_progress lp2
														WHERE lp2.group_info_id = rc.group_info_id)
						ORDER BY s.last_name, s.first_name";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$res = $stmt->fetchAll();
			foreach ($res as $val) {
				$is_set = false;
				$last_lesson_progress = get_last_lesson_progress_by_group($val['group_id']);
				if (count($last_lesson_progress) == 0) {
					$date1 = date_create(date('Y-m-d'));
					$date2 = date_create($val['start_date']);
					$diff = date_diff($date1, $date2);
					if ($diff->format('%a') <= $n) {
						$is_set = true;
						$learned = false;
						$start_date = date('d.m.Y', strtotime($val['group_start_date']));
					}
				} else if (intval($val['subtopic_order']) > intval($last_lesson_progress['subtopic_order'])
					&& ($val['subtopic_order'] - $last_lesson_progress['subtopic_order']) <= $n) {
					$is_set = true;
					$learned = false;
					$start_date = date('d.m.Y', strtotime($last_lesson_progress['created_date']));
					for ($i=$last_lesson_progress['subtopic_order']; $i < $val['subtopic_order']; $i++) { 
						$start_date = learn_date($val['group_id'], $start_date);
					}
				}
				if ($is_set) {
					$result['no_payment_count']++;
					if (!isset($result[$val['student_id']])) {
						$result['datas'][$val['student_id']] = array('last_name' => $val['last_name'],
																	'first_name' => $val['first_name'],
																	'phone' => $val['phone'],
																	'groups' => array());
					}
					$start_date = date('d.m.Y', strtotime($last_lesson_progress['created_date']));
					$result['datas'][$val['student_id']]['groups'][$val['group_id']] = array('course_id' => $val['course_id'],
																							'group_name' => $val['group_name'],
																							'subtopic_title' => $val['subtopic_title'],
																							'subtopic_id' => $val['subtopic_id'],
																							'start_date' => $start_date,
																							'has_payment' => false,
																							'ns_id' => $val['ns_id'],
																							'learned' => $learned);
					array_push($result['datas2'], array('student_id' => $val['student_id'],
														'last_name' => $val['last_name'],
														'first_name' => $val['first_name'],
														'phone' => $val['phone'],
														'course_id' => $val['course_id'],
														'group_name' => $val['group_name'],
														'subtopic_title' => $val['subtopic_title'],
														'subtopic_id' => $val['subtopic_id'],
														'start_date' => $start_date,
														'has_payment' => false,
														'ns_id' => $val['ns_id'],
														'learned' => $learned));
				}
			}
			$start_date = array_column($result['datas2'], 'start_date');
			array_multisort($start_date, SORT_ASC, $result['datas2']);
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_last_lesson_progress_by_group($group_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id,
							lp.subtopic_id,
							lp.created_date,
							st.subtopic_order
						FROM lesson_progress lp,
							subtopic st
						WHERE lp.group_info_id = :group_id
							AND st.id = lp.subtopic_id
						ORDER BY lp.created_date DESC, st.subtopic_order DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetch(PDO::FETCH_ASSOC);
			$row_num = $stmt->rowCount();

			if ($row_num == 0) {
				return array();
			}
			return $sql_res;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_lesson_progress_by_group_and_subtopic($group_id, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id,
							lp.subtopic_id,
							lp.created_date,
							st.subtopic_order
						FROM lesson_progress lp,
							subtopic st
						WHERE lp.group_info_id = :group_id
							AND lp.subtopic_id = :subtopic_id
							AND st.id = lp.subtopic_id
						ORDER BY lp.created_date ASC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetch(PDO::FETCH_ASSOC);
			$row_num = $stmt->rowCount();

			if ($row_num == 0) {
				return array();
			}
			return $sql_res;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function learn_date($group_id, $prev_date) {
		GLOBAL $connect;

		try {
			$schedules = get_schedule_of_group($group_id);
			$return_date = date('Y-m-d', strtotime($prev_date.' + 1 days'));
			$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
			while (!in_array(strval($week_day_id), $schedules)) {
				$return_date = date('Y-m-d', strtotime($return_date.' + 1 days'));
				$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
			}
			return date('d.m.Y', strtotime($return_date));
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_schedule_of_group($group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT gs.week_day_id
						FROM group_schedule gs
						WHERE gs.group_info_id = :group_id
						ORDER BY gs.week_day_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, $value['week_day_id']);
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_reserve_students_info() {
		GLOBAL $connect;

		try {

			$query = "SELECT rr.id AS rr_id,
							DATE_FORMAT(rr.created_date, '%Y-%m-%d') AS created_date,
							t.id AS topic_id,
							t.title AS topic_title,
							sj.id AS subject_id,
							sj.title AS subject_title,
							CONCAT(s.last_name, ' ', s.first_name) AS fio
						FROM registration_reserve rr,
							topic t,
							subject sj,
							student s
						WHERE rr.is_done = 0
							AND t.id = rr.topic_id
							AND sj.id = t.subject_id
							AND s.id = rr.student_id
						ORDER BY sj.title, t.title";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();
			return $sql_result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_reserve_notification_data() {
		GLOBAL $connect;

		try {

			$registration_reserve = get_reserve_students_info();
			$starting_groups = get_starting_groups(5);

			$result = array('danger' => array(),
							'warning' => array(),
							'ok' => array());
			foreach ($registration_reserve as $value) {
				$start_date = exist_in_starting_groups($starting_groups, $value);
				if ($start_date == '') {
					$date1 = date_create($value['created_date']);
					$date2 = date_create(date('Y-m-d'));
					$diff = date_diff($date1, $date2)->format('%a');

					if ($diff > 7) {
						if (!isset($result['danger'][$value['subject_id']])) {
							$result['danger'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																			'topics' => array());
						}
						if (!isset($result['danger'][$value['subject_id']]['topics'][$value['topic_id']])) {
							$result['danger'][$value['subject_id']]['topics'][$value['topic_id']] = array('topic_title' => $value['topic_title'],
																											'students_count' => 0,
																											'student_fio' => array());
						}
						$result['danger'][$value['subject_id']]['topics'][$value['topic_id']]['students_count']++;
						array_push($result['danger'][$value['subject_id']]['topics'][$value['topic_id']]['student_fio'], $value['fio']);
					} else {
							if (!isset($result['warning'][$value['subject_id']])) {
							$result['warning'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																			'topics' => array());
						}
						if (!isset($result['warning'][$value['subject_id']]['topics'][$value['topic_id']])) {
							$result['warning'][$value['subject_id']]['topics'][$value['topic_id']] = array('topic_title' => $value['topic_title'],
																											'students_count' => 0,
																											'student_fio' => array());
						}
						$result['warning'][$value['subject_id']]['topics'][$value['topic_id']]['students_count']++;
						array_push($result['warning'][$value['subject_id']]['topics'][$value['topic_id']]['student_fio'], $value['fio']);
					}
				} else {
					if (!isset($result['ok'][$value['subject_id']])) {
						$result['ok'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																	'topics' => array());
					}
					if (!isset($result['ok'][$value['subject_id']]['topics'][$value['topic_id']])) {
						$result['ok'][$value['subject_id']]['topics'][$value['topic_id']] = array('start_date' => $start_date,
																									'topic_title' => $value['topic_title'],
																									'students_count' => 0,
																									'student_fio' => array());
					}
					$result['ok'][$value['subject_id']]['topics'][$value['topic_id']]['students_count']++;
					array_push($result['ok'][$value['subject_id']]['topics'][$value['topic_id']]['student_fio'], $value['fio']);
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function exist_in_starting_groups($starting_groups, $registration_reserve) {
		$start_date = '';
		if (isset($starting_groups[$registration_reserve['subject_id']])) {
			foreach ($starting_groups[$registration_reserve['subject_id']]['groups'] as $value) {
				if ($value['topic_id'] == $registration_reserve['topic_id']) {
					if ($start_date == '' || strtotime($value['start_date']) < strtotime($start_date)) {
						$start_date = $value['start_date'];
					}
				}
			}
		}
		return $start_date;
	}

	function get_no_progress_students_notification_info() {
		GLOBAL $connect;

		try {

			$query = "SELECT npsn.id AS npsn_id,
							s.last_name,
							s.first_name,
							gi.group_name,
							gi.id AS group_info_id,
							st.title AS subtopic_title
						FROM no_progress_student_notification npsn,
							lesson_progress lp,
							subtopic st,
							group_student gs,
							student s,
							group_info gi
						WHERE lp.id = npsn.lesson_progress_id
							AND st.id = lp.subtopic_id
							AND gs.id = npsn.group_student_id
							AND gi.id = gs.group_info_id
							AND s.id = gs.student_id
						ORDER BY s.last_name, s.first_name, gi.group_name";
			$stmt = $connect->prepare($query);
			$stmt->execute();

			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}
?>