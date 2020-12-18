<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_student_info($student_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT s.last_name, s.first_name
										FROM student s
										WHERE s.id = :student_id");
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_payment_history($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id,
							gi.group_name,
							DATE_FORMAT(gi.start_date, '%d.%m.%Y') AS start_date,
							gi.status_id AS group_status_id,
							gi.is_archive AS group_is_archive,
							gi.status_change_date,
							gs.id AS group_student_id,
							gs.is_archive AS group_student_is_archive,
							gs.created_date AS group_student_created_date,
							gs.start_from,
							(SELECT t2.title FROM topic t2 WHERE t2.id = gs.start_from) AS start_topic_title,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							gsp.id AS group_student_payment_id,
							DATE_FORMAT(gsp.payed_date, '%d.%m.%Y') AS payed_date,
							gsp.access_until,
							gsp.is_used,
							DATE_FORMAT(gsp.used_date, '%d.%m.%Y') AS used_date,
							gsp.full_finished,
							gsp.payment_type,
							gsp.partial_payment_days,
							gsp.finished_date,
							DATE_FORMAT(gsp.start_date, '%d.%m.%Y') AS payment_start_date
						FROM group_student gs
						INNER JOIN group_info gi
							ON gi.id = gs.group_info_id
						INNER JOIN group_student_payment gsp
							ON gsp.group_student_id = gs.id
						INNER JOIN subject sj
							ON sj.id = gi.subject_id
						INNER JOIN topic t
							ON t.id = gi.topic_id
						WHERE gs.student_id = :student_id
						ORDER BY gsp.payed_date, sj.title, t.topic_order ASC";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$payment_result = $stmt->fetchAll();

			$result = array();

			foreach ($payment_result as $value) {
				if (!isset($result[$value['group_info_id']])){
					$result[$value['group_info_id']] = array('group_id' => $value['group_info_id'],
															'group_student_id' => $value['group_student_id'],
															'group_name' => $value['group_name'],
															'subject_id' => $value['subject_id'],
															'subject_title' => $value['subject_title'],
															'topic_id' => $value['topic_id'],
															'topic_title' => $value['topic_title'],
															'start_date' => $value['start_date'],
															'group_status_id' => $value['group_status_id'],
															'group_is_archive' => $value['group_is_archive'],
															'status_change_date' => $value['status_change_date'],
															'group_student_is_archive' => $value['group_student_is_archive'],
															'created_date' => $value['group_student_created_date'],
															'start_from' => $value['start_from'],
															'start_topic_title' => $value['start_topic_title'],
															'left_days' => 0,
															'payment' => array(),
															'payment_start_date' => $value['payment_start_date']);
				}

				$result[$value['group_info_id']]['payment'][$value['group_student_payment_id']] = 
														array('payed_date' => $value['payed_date'],
																'access_until' => $value['access_until'],
																'is_used' => $value['is_used'],
																'used_date' => $value['used_date'],
																'start_date' => $value['start_date'],
																'full_finished' => $value['full_finished'],
																'payment_type' => $value['payment_type'],
																'partial_payment_days' => $value['partial_payment_days'],
																'finished_date' => $value['finished_date'],
																'payment_start_date' => $value['payment_start_date'],
																'cause' => array());
			}

			$query = "SELECT sb.group_id,
							sb.used_for_group,
							sb.comment,
							sb.created_date,
							DATE_FORMAT(sb.used_date, '%d.%m.%Y') AS used_date,
							sb.days,
							sb.is_used,
							gi.group_name
						FROM student_balance sb
						LEFT JOIN group_info gi
							ON gi.id = sb.group_id
						WHERE sb.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_balance_result = $stmt->fetchAll();

			$free_available_days = array();
			foreach ($student_balance_result as $value) {
				if (isset($result[$value['group_id']])) {
					$result[$value['group_id']]['left_days'] = $value['days'];
				}
				if ($value['is_used'] == 1) {
					foreach ($result[$value['used_for_group']]['payment'] as $group_student_payment_id => $val) {
						$payed_date = $val['payed_date'];
						$used_date = $value['used_date'];

						if ($payed_date == $used_date && $val['partial_payment_days'] >= $value['days']) {
							if ($value['group_id'] != '') {
								array_push($result[$value['used_for_group']]['payment'][$group_student_payment_id]['cause'],
																	array('type' => 'group_info',
																			'group_id' => $value['group_id'],
																			'group_name' => $value['group_name'],
																			'days' => $value['days']));
							} else {
								array_push($result[$value['used_for_group']]['payment'][$group_student_payment_id]['cause'],
																	array('type' => 'bonus',
																			'comment' => $value['comment'],
																			'days' => $value['days']));
							}
						}
					}
				} else {
					if ($value['group_name'] == '') {
						array_push($free_available_days, array('comment' => $value['comment'],
																'days' => $value['days']));
					} else {
						array_push($free_available_days, array('comment' => $value['group_name'],
																'days' => $value['days']));
					}
				}
			}

			$total_result = array();

			foreach ($result as $group_info_id => $value) {
				if (!isset($total_result[$value['subject_id']])) {
					$total_result[$value['subject_id']] = array('subject_title' => $value['subject_title'],
																'groups' => array());
				}

				$total_result[$value['subject_id']]['groups'][$group_info_id] = $value;
			}

			$total_result = array('payment_infos' => $total_result,
								'free_available_days' => $free_available_days);
			return $total_result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_trial_teset_results ($student_id) {
		GLOBAL $connect;

		try {

			$result = array('test' => array(),
							'student_info' => array());

			$query = "SELECT s.last_name,
							s.first_name
						FROM student s
						WHERE s.id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result['student_info'] = array('last_name' => $student_result['last_name'],
											'first_name' => $student_result['first_name']);


			$query = "SELECT stt.id AS student_trial_test_id,
							stt.result,
							tt.subject_id,
							DATE_FORMAT(stt.submit_date, '%d.%m.%Y') AS submit_date,
							sj.title AS subject_title
						FROM student_trial_test stt,
							trial_test tt,
							subject sj
						WHERE stt.student_id = :student_id
							AND tt.id = stt.trial_test_id
							AND sj.id = tt.subject_id
						ORDER BY stt.submit_date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				if (!isset($result['test'][$value['subject_id']])) {
					$result['test'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																	'results' => array());
				}
				$res = json_decode($value['result'], true);
				array_push($result['test'][$value['subject_id']]['results'], array('student_trial_test_id' => $value['student_trial_test_id'],
																					'total_result' => $res['total_result'],
																					'actual_result' => $res['actual_result'],
																					'submit_date' => $value['submit_date']));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_free_coins ($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT sc.total_coins
						FROM student_coins sc
						WHERE sc.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			$total_coins = $row_count == 0 ? 0 : $stmt->fetch(PDO::FETCH_ASSOC)['total_coins'];

			return $total_coins;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>