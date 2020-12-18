<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/controller_functions.php');
	include_once($root.'/common/global_controller.php');

	$subject_topics_order = array();

	function get_student_reserve_info() {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];
			$group_result = array();

			$query = "SELECT rr.id AS rr_id,
							rr.topic_id,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.title AS topic_title,
							srp.id AS srp_id,
							srp.payed_date
						FROM registration_reserve rr
						INNER JOIN topic t
							ON t.id = rr.topic_id
						INNER JOIN subject sj
							ON sj.id = t.subject_id
						LEFT JOIN student_reserve_payment srp
							ON srp.registration_reserve_id = rr.id
						WHERE rr.student_id = :student_id
							AND rr.is_done = 0
						ORDER BY sj.title, t.title";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			foreach ($sql_result as $value) {
				$has_payment = false;
				if ($value['srp_id'] != '') {
					$has_payment = true;
				}
				$start_date = get_future_group_start_date_by_topic_id($value['topic_id']);
				$is_free_trial_lesson = check_is_first_subject_group($student_id, $value['subject_id']);
				array_push($group_result, array('registration_reserve_id' => $value['rr_id'],
												'subject_title' => $value['subject_title'],
												'topic_title' => $value['topic_title'],
												'has_payment' => $has_payment,
												'is_free_trial_lesson' => $is_free_trial_lesson,
												'start_date' => $start_date));
			}
			return $group_result;
		} catch(Exception $e) {
			throw $e;
		}
	}

	function get_future_group_start_date_by_topic_id($topic_id) {
		GLOBAL $connect;
		try {
			$result_date = '';
			$query = "SELECT sj.id AS subject_id,
					sj.title AS subject_title,
					gi.id AS group_id,
					gi.group_name,
					DATE_FORMAT(gi.start_date, '%d.%m.%Y') AS start_date
				FROM group_info gi,
					subject sj
				WHERE sj.id = gi.subject_id
					AND gi.is_archive = 0
					AND DATE_FORMAT(gi.start_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d')
					AND gi.status_id
					AND gi.topic_id = :topic_id
					AND 1 = (SELECT s2.is_active FROM status s2 WHERE s2.id = gi.status_id)
				ORDER BY gi.start_date ASC 
				LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() == 1) {
				$result_date = $sql_result['start_date'];
			}

			$future_groups = get_starting_groups(5, $topic_id);

			foreach ($future_groups as $subject_id => $subject) {
				foreach ($subject['groups'] as $value) {
					if ($result_date == '' || strtotime($result_date) > strtotime($value['start_date'])) {
						$result_date = $value['start_date'];
					}
				}
			}
			return $result_date;
		} catch (Exception $e) {
			// return '';
			throw $e;
		}
	}

	function get_full_groups_info_for_student() {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$group_result = array();
			$stmt = $connect->prepare("SELECT lp.id AS lp_id,
											st.title AS subtopic_title
										FROM lesson_progress lp,
											group_student gs,
											subtopic st
										WHERE gs.student_id = :student_id
											AND gs.is_archive = 0
											AND st.id = lp.subtopic_id
										   	AND lp.group_info_id = gs.group_info_id
										ORDER BY lp.id DESC");
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$lp_result = $stmt->fetchAll();

			// $last_lesson_subtitle = "";
			$lp_ids = array();
			foreach ($lp_result as $value) {
				array_push($lp_ids, $value['lp_id']);
				// if ($last_lesson_subtitle == '') {
				// 	$last_lesson_subtitle = $value['subtopic_title'];
				// }
			}

			$stmt = $connect->prepare("SELECT gi.id AS group_id,
											gi.group_name,
											gi.status_id AS group_status_id,
											gi.subject_id,
											sj.title AS subject_title,
											t.id AS topic_id,
											t.topic_order,
											t.title AS topic_title,
											gi.lesson_type,
											st.id AS subtopic_id,
											st.subtopic_order,
											st.title AS subtopic_title,
											gsh.week_day_id,
											gs.id AS group_student_id,
											gs.start_from,
											gi.is_freeze,
											fl.id AS freeze_lesson_id,
											DATE_FORMAT(fl.from_date, '%d.%m.%Y') AS freeze_lesson_from_date,
											DATE_FORMAT(fl.to_date, '%d.%m.%Y') AS freeze_lesson_to_date,
											(SELECT DATE_FORMAT(gsp2.access_until, '%d.%m.%Y')
											FROM group_student_payment gsp2
											WHERE gsp2.group_student_id = gs.id
												AND gsp2.is_used = 1
												AND gsp2.finished_date IS NULL
											ORDER BY gsp2.payed_date
											LIMIT 1) AS access_until,
											(SELECT st2.title
												FROM subtopic st2
												WHERE st2.id = gs.start_from) AS subtopic_title2,
											(SELECT st2.subtopic_order
												FROM subtopic st2
												WHERE st2.id = gs.start_from) AS subtopic_order,
											gs.has_payment,
											gs.status,
											gi.start_date AS group_start_date,
											lp.id AS lesson_progress_id,
											(SELECT IF(TIME(DATE_FORMAT(NOW(), '%H:%i:%s')) > TIME('07:00:00'), 
												(fma2.created_date < TIMESTAMP(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00')
													AND fma2.created_date >= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')), 
        										(fma2.created_date < TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')
        											AND fma2.created_date >= TIMESTAMP(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00'))) AS is_active
        									FROM forced_material_access fma2
        									WHERE fma2.lesson_progress_id = lp.id
        										AND fma2.group_student_id = gs.id
        									ORDER BY fma2.created_date DESC
        									LIMIT 1) AS is_forced_access
									FROM group_info gi
									INNER JOIN group_student gs
										ON gs.student_id = :student_id
											AND gs.is_archive = 0
									INNER JOIN group_schedule gsh
										ON gsh.group_info_id = gi.id
									INNER JOIN status
										ON status.id = gi.status_id
											AND status.is_active = 1
									LEFT JOIN freeze_lesson fl
										ON fl.group_info_id = gi.id
											AND fl.from_date <= DATE_FORMAT(NOW(), '%Y-%m-%d')
											AND fl.to_date >= DATE_FORMAT(NOW(), '%Y-%m-%d')
									LEFT JOIN lesson_progress lp
										ON lp.id in (".(count($lp_ids) == 0 ? 0 : implode(',', $lp_ids)).")
									INNER JOIN subject sj
										ON sj.id = gi.subject_id
									LEFT JOIN subtopic st
										ON st.id = lp.subtopic_id
									LEFT JOIN topic t
										ON (gi.lesson_type = 'topic' AND t.id = gi.topic_id)
											OR (gi.lesson_type = 'subject' AND t.id = st.topic_id)
									WHERE gi.id = gs.group_info_id
									ORDER BY gi.group_name, t.topic_order, st.subtopic_order, gsh.week_day_id");
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$result = $stmt->fetchAll();
			// ON lp.id = (SELECT lp2.id 
			// 										FROM lesson_progress lp2
			// 										WHERE lp2.group_info_id = gi.id
			// 										ORDER BY lp2.id DESC
			// 										LIMIT 1)

			$subject_ids = array();

			foreach ($result as $value) {
				if (!isset($group_result[$value['group_id']])) {
					if (!isset($subject_topics_order[$value['subject_id']])) {
						$subjects_order[$value['subject_id']] = get_subtopics_total_order($value['subject_id']);
					}
					$payment = get_next_payment_price($value['group_id'], $value['group_student_id'],
													$subjects_order[$value['subject_id']], $value['group_start_date'], $value['start_from']);

					$start_date = start_date($value['group_id'], $value['start_from'], $value['subtopic_order'], $value['lesson_type'], $value['group_start_date']);
					$start_date_format = strtotime($start_date);
					$remine_days = ($start_date_format - strtotime(date('Y-m-d')))/60/60/24;
					$has_access = $value['start_from'] == 0 ? true : has_access($value['group_id'], $value['start_from']);

					if (!$has_access && $value['is_forced_access'] == 1) {
						$has_access = true;
					}

					// $has_group_force_access = get_group_force_access_lessons($value['group_id'], $value['group_student_id']);
					$access_until_with_extra_payments = union_of_all_group_payments($value['group_student_id'], $value['access_until']);
					$group_result[$value['group_id']] = array('group_info_id' => $value['group_id'],
															'group_student_id' => $value['group_student_id'],
															'group_name' => $value['group_name'],
															'subject_id' => $value['subject_id'],
															'subject_title' => $value['subject_title'],
															'topic_id' => $value['topic_id'],
															'topic_order' => $value['topic_order'],
															'topic_title' => $value['topic_title'],
															'subtopic_id' => $value['subtopic_id'],
															'subtopic_order' => $value['subtopic_order'],
															'subtopic_title' => $value['subtopic_title'],
															'subtopic_title2' => $value['subtopic_title2'],
															'lesson_type' => $value['lesson_type'],
															'lp_id' => $value['lesson_progress_id'],
															'has_payment' => $value['has_payment'],
															'status' => $value['status'],
															'access_until' => $value['access_until'],
															'access_until_with_extra_payments' => $access_until_with_extra_payments,
															'start_date' => $start_date,
															'remine_days' => $remine_days,
															'progress' => 0,
															'group_status_id' => $value['group_status_id'],
															'schedule' => array(),
															'is_forced_access' => $value['is_forced_access'],
															'has_access' => $has_access,
															'payment' => $payment,
															'is_army_group' => get_is_army_group($value['group_id']),
															'is_marathon_group' => get_is_marathon_group($value['group_id']),
															'freeze_lesson_id' => $value['freeze_lesson_id'],
															'freeze_lesson_from_date' => $value['freeze_lesson_from_date'],
															'freeze_lesson_to_date' => $value['freeze_lesson_to_date'],
															'is_freeze' => $value['is_freeze'],
															'last_lesson_subtitle' => $last_lesson_subtitle);
				}
				if ($value['is_forced_access'] == '1') {
					$group_result[$value['group_id']]['is_forced_access'] = $value['is_forced_access'];
				}
				array_push($group_result[$value['group_id']]['schedule'], $value['week_day_id']);

				if (!in_array($value['subject_id'], $subject_ids)) {
					array_push($subject_ids, $value['subject_id']);
				} 
			}

			return $group_result;
			
		} catch (PDOException $e) {
			// print_r($e);
			// return array();
			throw $e;
		}
	}

	function union_of_all_group_payments($group_student_id, $access_until) {
		GLOBAL $connect;

		try {

			$query = 'SELECT gsp.partial_payment_days
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
							AND gsp.is_used = 0';
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				if ($value['partial_payment_days'] == '') {
					$access_until = date('d.m.Y', strtotime(" + 1 months", strtotime($access_until)));
				} else {
					$access_until = date('d.m.Y', strtotime(" + ".$value['partial_payment_days']." days", strtotime($access_until)));
				}
			}
			return $access_until;
		} catch (Exception $e) {
			return $access_until;
			// throw $e;
		}
	}

	// function get_group_force_access_lessons($group_info_id, $group_student_id) {
	// 	GLOBAL $connect;

	// 	try {

	// 		$query = "SELECT
	// 					FROM forced_material_access fma";
			
	// 	} catch (Exception $e) {
	// 		return false;
	// 	}
	// }

	function get_student_balances() {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];
			$result = array();
			$query = "SELECT gi.group_name,
							gi.id AS group_id,
							sb.id AS sb_id,
							sb.days, 
							sb.comment
						FROM student_balance sb,
							group_info gi
						WHERE sb.is_used = 0
							AND gi.id = sb.group_id
							AND sb.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			foreach ($sql_result as $value) {
				array_push($result, array('group_name' => $value['group_name'],
											'days' => $value['days'],
											'comment' => $value['comment']));
			}

			return $result;
			
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function start_date($group_info_id, $start_from, $subtopic_order, $lesson_type, $group_start_date) {
		GLOBAL $connect; 

		try {
			$stmt = $connect->prepare("SELECT gsh.week_day_id
										FROM group_schedule gsh
										WHERE gsh.group_info_id = :group_info_id");
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$schedules = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($schedules, intval($value['week_day_id']));
			}
			sort($schedules);

			if ($lesson_type='topic') {
				$stmt = $connect->prepare("SELECT lp.id,
												lp.subtopic_id,
												(SELECT st2.subtopic_order FROM subtopic st2 WHERE st2.id = lp.subtopic_id) AS subtopic_order,
												DATE_FORMAT(created_date, '%Y-%m-%d') AS created_date
											FROM lesson_progress lp
											WHERE lp.group_info_id = :group_info_id
											ORDER BY lp.created_date DESC
											LIMIT 1");
				$stmt->bindParam(':group_info_id', $group_info_id);
				$stmt->execute();
				if ($stmt->rowCount() == 1) {
					$lesson_progress = $stmt->fetch(PDO::FETCH_ASSOC);
					$lp_subtopic_id = $lesson_progress['subtopic_id'];
					$lp_created_date = $lesson_progress['created_date'];
					$lp_subtopic_order = $lesson_progress['subtopic_order'];

					if ($lp_subtopic_order < $start_from) {
						$return_date = date('Y-m-d', strtotime($lp_created_date.' + 1 days'));
						for ($i = $lp_subtopic_order; $i < $subtopic_order; $i++) {
							$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
							while (!in_array($week_day_id, $schedules)) {
								$return_date = date('Y-m-d', strtotime($return_date.' + 1 days'));
								$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
								break;
							}
							$return_date = date('Y-m-d', strtotime($return_date.' + 1 days'));
						}
						return date('d.m.Y', strtotime($return_date));
					} else if ($lp_subtopic_order > $start_from) {
						return date('d.m.Y');
					} else {
						return date('d.m.Y', strtotime($lp_created_date));
					}
				} else {
					return date('d.m.Y', strtotime($group_start_date));
				}
			}
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_subtopic_siblings_by_subtopic($subtopic_id) {
		GLOBAL $connect;

		try {
			$stmt = $connect->prepare('SELECT st.id
										FROM subtopic st
										WHERE st.topic_id = (SELECT st2.topic_id FROM subtopic st2 WHERE st2.id = :subtopic_id)
										ORDER BY st.subtopic_order');
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subjects_hierarchy($subject_id_arr) {
		GLOBAL $connect;
		try {
			$subject_ids = implode(',', $subject_id_arr);
			$subject_result = array();
			$stmt = $connect->prepare("SELECT sj.id AS subject_id,
											t.id AS topic_id,
											t.topic_order,
											st.id AS subtopic_id,
											st.subtopic_order
										FROM subject sj,
											topic t,
											subtopic st
										WHERE sj.id in ($subject_ids)
											AND t.subject_id = sj.id
											AND st.topic_id = t.id
										ORDER BY sj.id, t.topic_order, st.subtopic_order");
			$stmt->execute();
			$result = $stmt->fetchAll();

			foreach ($result as $value) {
				if (!isset($subject_result[$value['subject_id']])) {
					$subject_result[$value['subject_id']] = array('subject_id' => $value['subject_id'],
																'topics' => array());
				}
				if (!isset($subject_result[$value['subject_id']]['topics'][$value['topic_id']])) {
					$subject_result[$value['subject_id']]['topics'][$value['topic_id']] = array('topic_id' => $value['topic_id'],
																									'topic_order' => $value['topic_order'],
																									'subtopics' => array());
				}

				$subject_result[$value['subject_id']]['topics'][$value['topic_id']]['subtopics'][$value['subtopic_id']]['subtopic_id'] = $value['subtopic_id'];
				$subject_result[$value['subject_id']]['topics'][$value['topic_id']]['subtopics'][$value['subtopic_id']]['subtopic_order'] = $value['subtopic_order'];
			}

			return $subject_result;
			
		} catch (Exception $e) {
			return array();
			throw $e;
		}
	}

	function get_material_test_answers($subtopic_id, $material_test_action_id) {
		GLOBAL $connect;

		try {

			$result = array();

			$query = "SELECT mt.link
						FROM material_test mt
						WHERE mt.subtopic_id = :subtopic_id
						ORDER BY mt.test_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$test_result = $stmt->fetchAll();

			$result['test'] = array();
			$result['has_finished'] = has_test_submitted($subtopic_id, $material_test_action_id);
			$result['test_result_json'] = get_test_result_json($subtopic_id, $material_test_action_id);
			foreach ($test_result as $value) {
				array_push($result['test'], $value['link']);
			}

			$query = "SELECT ans.id,
							ans.numeration,
							ans.prefix
						FROM answers ans
						WHERE ans.subtopic_id = :subtopic_id
						ORDER BY ans.numeration, ans.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();


			foreach ($sql_result as $value) {
				if (!isset($result['answers'][$value['numeration']])) {
					$result['answers'][$value['numeration']] = array();
				}
				$result['answers'][$value['numeration']][$value['id']] = $value['prefix'];
			}

			create_initial_material_test_result($subtopic_id, $material_test_action_id);

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_test_result_json ($subtopic_id, $material_test_action_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mtr.result_json
						FROM material_test_result mtr
						WHERE mtr.subtopic_id = :subtopic_id
							AND mtr.material_test_action_id = :material_test_action_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_STR);
			$stmt->execute();

			return json_decode($stmt->fetch(PDO::FETCH_ASSOC)['result_json'], true);

			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function has_test_submitted($subtopic_id, $material_test_action_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(mtr.id) AS c
						FROM material_test_result mtr 
						WHERE mtr.subtopic_id = :subtopic_id
							AND mtr.material_test_action_id = :material_test_action_id
							AND mtr.actual_result IS NOT NULL
							AND mtr.total_result IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC)['c'] >= 1 ? true : false;
			
		} catch (Exception $e) {
			return false;
		}
	}

	function create_initial_material_test_result($subtopic_id, $material_test_action_id) {
		GLOBAL $connect; 

		try {

			$query = "SELECT count(mtr.id) AS count
						FROM material_test_result mtr
						WHERE mtr.subtopic_id = :subtopic_id
							AND mtr.material_test_action_id = :material_test_action_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

			if ($count == 0) {
				$query = "INSERT INTO material_test_result (subtopic_id, material_test_action_id, start_date)
									VALUES (:subtopic_id, :material_test_action_id, NOW())";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
				$stmt->execute();
			} else {
				$query = "UPDATE material_test_result
							SET start_date = NOW()
							WHERE subtopic_id = :subtopic_id
								AND material_test_action_id = :material_test_action_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_student_plans() {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$result = array();
			$group_student_infos = get_group_student_infos($student_id);

			$subject_id = 0;
			$prev_group_stuatus = '';
			foreach ($group_student_infos as $v) {
				if (!isset($result[$v['subject_id']])) {
					$result[$v['subject_id']] = array('subject_title' => $v['subject_title'],
														'subject_id' => $v['subject_id'],
														'topics_and_groups' => array());
				}
				$group_unique_lesson_progress = get_group_unique_lesson_progress($v['group_id'], $v['group_student_id']);
				$last_elem = end($group_unique_lesson_progress);
				if ($v['status_id'] == 2) {
					$last_subtopic_order = $last_elem['subtopic_order'];
					$last_subtopic_date = $last_elem['lp_created_date'];
					$not_studied_subtopics = not_studied_subtopics($v['topic_id'], $last_subtopic_order, 
																	$last_subtopic_date, $v['group_id']);

					if (count($not_studied_subtopics) > 0) {
						$group_unique_lesson_progress = array_merge($group_unique_lesson_progress, $not_studied_subtopics);
					}
				}
				if ($subject_id != $v['subject_id'] || $v['topic_order'] - $topic_order == 1) {
					$subject_id = $v['subject_id'];
					$topic_order = $v['topic_order'];
					$prev_group_status = $v['status_id'];
					$last_elem = end($group_unique_lesson_progress);
					$last_group_name = $v['group_name'];
					if ($last_elem['type'] == 'lesson_progress') {
						$last_subtopic_date = $last_elem['lp_created_date'];
					} else if ($last_elem['type'] == 'subtopic') {
						$last_subtopic_date = date('Y-m-d', strtotime($last_elem['learn_date']));
					}
				} else if ($v['topic_order'] - $topic_order > 1) {
					$max_topic_order = $subject_id == $v['subject_id'] ? $v['topic_order'] : 999;
					$topics = get_topics_by_order_range($subject_id, $last_subtopic_date, $v['group_id'], $prev_group_status,
														$last_group_name, $topic_order, $v['topic_order']);
					$topic_order = $v['topic_order'];
					$result[$v['subject_id']]['topics_and_groups'] = array_merge($result[$v['subject_id']]['topics_and_groups'],
																				$topics);
				}
				$result[$v['subject_id']]['topics_and_groups'][$v['topic_order']] 
											= array('is_group' => true,
													'topic_order' => $v['topic_order'],
													'group_id' => $v['group_id'],
													'start_date' => $v['start_date'],
													'group_status' => $v['status_id'],
													'group_name' => $v['group_name'],
													'topic_title' => $v['topic_title'],
													'transfer_from_group' => $v['transfer_from_group'],
													'subtopics' => $group_unique_lesson_progress);
			}

			foreach ($result as $subject_id => $subject) {
				$topic_and_group = end($subject['topics_and_groups']);
				$last_lesson = end($topic_and_group['subtopics']);
				$last_lesson_date = '';
				if ($last_lesson['type'] == 'lesson_prgoress') {
					$last_lesson_date = $last_lesson['learned_date'];
				} else if ($last_lesson['type'] == 'subtopic') {
					$last_lesson_date = $last_lesson['learn_date'];
				}
				$group_id = $topic_and_group['group_id'];
				$last_group_status = $topic_and_group['group_status'];
				$last_group_name = $topic_and_group['group_name'];
				$topic_order = $topic_and_group['topic_order'];
				$topics = get_topics_by_order_range($subject_id, $last_lesson_date, $group_id, $last_group_status,
														$last_group_name, $topic_order, 999);
				if (count($topics) > 0) {
					$result[$subject_id]['topics_and_groups'] = array_merge($result[$subject_id]['topics_and_groups'], $topics);
				}
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topics_by_order_range($subject_id, $last_subtopic_date, $group_id, $status_id, $last_group_name, 
										$min_topic_order, $max_topic_order) {
		GLOBAL $connect;

		try {

			$query = "SELECT t.id AS topic_id,
							t.title AS topic_title,
							t.topic_order,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order
						FROM topic t,
							subtopic st
						WHERE t.topic_order > :min_topic_order
							AND t.topic_order < :max_topic_order
							AND t.subject_id = :subject_id
							AND st.topic_id = t.id
						ORDER BY t.topic_order, st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(':min_topic_order', $min_topic_order, PDO::PARAM_INT);
			$stmt->bindParam(':max_topic_order', $max_topic_order, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			if ($status_id == 2) {
				$query = "SELECT t.id AS topic_id, 
								count(st.id) AS st_count
							FROM subtopic st,
								topic t
							WHERE t.subject_id = :subject_id
								AND st.topic_id = t.id
							    AND t.topic_order > :min_topic_order
							    AND t.topic_order < :max_topic_order
							GROUP BY t.id
							ORDER BY topic_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
				$stmt->bindParam(':min_topic_order', $min_topic_order, PDO::PARAM_INT);
				$stmt->bindParam(':max_topic_order', $max_topic_order, PDO::PARAM_INT);
				$stmt->execute();
				$query_subtopics_count = $stmt->fetchAll();

				$subtopics_count = array();
				foreach ($query_subtopics_count as $value) {
					$subtopics_count[$value['topic_id']] = $value['st_count'];
				}
				$group_schedules = get_group_schedule($group_id);
			}

			$result = array();
			$learn_date = '';
			foreach ($sql_result as $v) {
				if ($status_id == 2 && $v['subtopic_order'] != 2 && $v['subtopic_order'] != $subtopics_count[$v['topic_id']]) {
					$learn_date = get_learn_date($last_subtopic_date, 1, $group_schedules);
					$last_subtopic_date = date('Y-m-d', strtotime($learn_date));
				}

				if (!isset($result[$v['topic_order']])) {
					$group_name = "";
					if ($status_id == 2) {
						// $group_name = explode('-', $last_group_name)[0].'-'.explode('-', $v['topic_title'])[1];
						$group_name = $v['topic_title'];
					}
					$result[$v['topic_order']] = array('is_group' => false,
														'topic_order' => $v['topic_order'],
														'start_date' => $learn_date,
														'group_name' => $group_name,
														'topic_title' => $v['topic_title'],
														'subtopics' => array());
				}

				$result[$v['topic_order']]['subtopics'][$v['subtopic_order']] = array('type' => 'subtopic',
																					'learn_date' => $learn_date,
																					'subtopic_id' => $v['subtopic_id'],
																					'subtopic_title' => $v['subtopic_title'],
																					'subtopic_order' => $v['subtopic_order']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_infos($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_id,
							gi.group_name,
							gi.status_id,
							gi.start_date,
							gi.is_archive is_group_archive,
							gs.transfer_from_group,
							gs.id AS group_student_id,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							t.topic_order
						FROM group_student gs,
							group_info gi,
							subject sj,
							topic t
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id
							AND gi.lesson_type = 'topic'
							AND t.id = gi.topic_id
							AND (SELECT mg.id
								FROM marathon_group mg
								WHERE mg.group_info_id = gi.id) IS NULL
						ORDER BY sj.title, t.topic_order, gs.created_date ASC";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			return $sql_result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_unique_lesson_progress($group_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id AS lesson_progress_id,
							DATE_FORMAT(lp.created_date, '%d.%m.%Y') AS learned_date,
							DATE_FORMAT(lp.created_date, '%Y-%m-%d') AS lp_created_date,
							(DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_today,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order,
							mtr.actual_result, 
							mtr.total_result
						FROM lesson_progress lp
						INNER JOIN subtopic st
							ON st.id = lp.subtopic_id
						LEFT JOIN material_test_action mta
							ON mta.lesson_progress_id = lp.id
								AND mta.group_student_id = :group_student_id
						LEFT JOIN material_test_result mtr
							ON mtr.material_test_action_id = mta.id
						WHERE lp.group_info_id = :group_info_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();

			foreach ($sql_result as $value) {
				$result[$value['subtopic_order']] = array('type' => 'lesson_progress',
															'lesson_progress_id' => $value['lesson_progress_id'],
															'learned_date' => $value['learned_date'],
															'is_today' => $value['is_today'],
															'lp_created_date' => $value['lp_created_date'],
															'subtopic_id' => $value['subtopic_id'],
															'subtopic_title' => $value['subtopic_title'],
															'subtopic_order' => $value['subtopic_order'],
															'actual_result' => $value['actual_result'],
															'total_result' => $value['total_result']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function not_studied_subtopics($topic_id, $last_subtopic_order, $last_subtopic_date, $group_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT st.id,
							st.title,
							st.subtopic_order
						FROM subtopic st
						WHERE st.subtopic_order > :last_subtopic_order
							AND st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':last_subtopic_order', $last_subtopic_order, PDO::PARAM_INT);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$query = "SELECT count(st.id) AS st_count
						FROM subtopic st
						WHERE st.topic_id = :topic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$st_count = $stmt->fetch(PDO::FETCH_ASSOC)['st_count'];
			$result = array();
			$group_schedules = get_group_schedule($group_id);
			$learn_date = '';
			foreach ($sql_result as $value) {
				if ($value['subtopic_order'] != $st_count) {
					$days = $value['subtopic_order'] - $last_subtopic_order;
					$learn_date = get_learn_date($last_subtopic_date, $days, $group_schedules);
				}
				$result[$value['subtopic_order']] = array('type' => 'subtopic',
															'learn_date' => $learn_date,
															'subtopic_id' => $value['id'],
															'subtopic_title' => $value['title'],
															'subtopic_order' => $value['subtopic_order']);
			}

			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_learn_date($start_date, $days, $schedules) {
		
		$d = new DateTime($start_date);
	    $t = $d->getTimestamp();
	    for($i = 0; $i < $days; $i++){
	        $addDay = 86400;
	        $nextDay = date('w', ($t+$addDay));

	        $week_day_id = ($nextDay == 0) ? 7 : $nextDay;
	        if(count($schedules) > 0 && !in_array($week_day_id, $schedules)) {
	            $i--;
	        }
	        $t = $t+$addDay;
	    }

	    $d->setTimestamp($t);

	    return $d->format('d.m.Y');
	}

	function get_group_schedule($group_id) {
		GLOBAL $connect;

		try {
			$stmt = $connect->prepare("SELECT gsh.week_day_id
										FROM group_schedule gsh
										WHERE gsh.group_info_id = :group_info_id");
			$stmt->bindParam(':group_info_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$schedules = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($schedules, intval($value['week_day_id']));
			}
			sort($schedules);
			return $schedules;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_students_progress() {
		GLOBAL $connect; 

		try {

			$student_id = $_SESSION['user_id'];

			$group_student_infos = get_group_student_infos($student_id);

			$result = array();

			$topic_subtopic_order = array();

			foreach ($group_student_infos as $info) {
				if (!isset($topic_subtopic_order[$info['subject_id']])) {
					$topic_subtopic_order[$info['subject_id']] = get_subtopics_total_order($info['subject_id']);
				}

				$subtopics_count = count($topic_subtopic_order[$info['subject_id']]['content'][$info['topic_id']]['subtopic']);
				$student_last_lesson_info  = get_group_student_last_lesson($info['group_id']);

				$percent = intval(($student_last_lesson_info['subtopic_order'] / $subtopics_count) * 100);

				if (!isset($result[$info['subject_id']])) {
					$result[$info['subject_id']] = array('subject_title' => $info['subject_title'],
														'topic' => array(),
														'progress' => 0.0);
				}
				if (isset($result[$info['subject_id']]['topic'][$info['topic_id']])) {
					if (!$info['is_group_archive']) {
						$result[$info['subject_id']]['topic'][$info['topic_id']] = array('topic_title' => $info['topic_title'],
																						'topic_order' => $info['topic_order'],
																						'progress' => $percent,
																						'status' => 'active');
					}
				}

				$result[$info['subject_id']]['topic'][$info['topic_id']] = array('topic_title' => $info['topic_title'],
																				'topic_order' => $info['topic_order'],
																				'progress' => $percent,
																				'status' => ($info['is_group_archive'] ? 'finish' : 'active'));

			}

			foreach ($result as $subject_id => $subject_info) {
				$actual_topic_progress = 0.0;
				foreach ($subject_info['topic'] as $topic_info) {
					$actual_topic_progress += $topic_info['progress'] / 100.0;
				}

				$started_student_topic = get_started_student_topic($student_id, $subject_id);
				$total_topic_count = count($topic_subtopic_order[$subject_id]['content']) - ($started_student_topic - 1);
				// echo $total_topic_count.' '.($started_student_topic - 1)."<br>";
				// echo $subject_id."<br><br>";
				// echo json_encode($topic_subtopic_order[$subject_id]['content'], JSON_UNESCAPED_UNICODE);
				// echo "<br><br><br>";

				$percent = intval(($actual_topic_progress / $total_topic_count) * 100);

				$result[$subject_id]['progress'] = $percent;
			}

			return $result;

		} catch (Exception $e) {
			return array();
		}
	}

	function get_started_student_topic ($student_id, $subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT t.topic_order
						FROM topic t,
							group_info gi,
							group_student gs
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND gi.subject_id = :subject_id
							AND t.id = gi.topic_id
						GROUP BY t.id
						ORDER BY t.topic_order
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$first_topic_order = $stmt->fetch(PDO::FETCH_ASSOC)['topic_order'];

			return $first_topic_order;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_last_lesson($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id,
							st.id,
							st.subtopic_order
						FROM lesson_progress lp,
							subtopic st
						WHERE lp.group_info_id = :group_info_id
							AND st.id = lp.subtopic_id
						ORDER BY st.subtopic_order DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			return $result;
			
		} catch (Exception $e) {
			return array();
		}
	}


	function get_left_coins () {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT sc.total_coins
						FROM student_coins sc
						WHERE sc.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$total_coins = $stmt->fetch(PDO::FETCH_ASSOC)['total_coins'];

			return $total_coins;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_freeze_lesson_exist() {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT fl.id AS freeze_lesson_id
						FROM freeze_lesson fl
						WHERE fl.student_id = :student_id
							AND fl.to_date >= DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			return $stmt->rowCount() == 0 ? false : true;
		} catch (Exception $e) {
			throw $e;
		}
	}
?>