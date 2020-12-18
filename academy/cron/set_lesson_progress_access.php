<?php
	include_once('../common/connection.php');

	$group_info_with_lesson_progress = get_group_info_with_lesson_progress();
	$next_group_lesson = get_next_group_lesson($group_result);
	$lesson_progress_and_group_students = set_lesson_progress($next_group_lesson);
	set_material_acces_by_lesson_progress();

	function get_group_info_with_lesson_progress () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id,
							gi.group_name,
							gi.lesson_type,
							gi.start_date,
							gi.created_date AS group_info_created_date,
							gi.subject_id,
							gi.topic_id,
							t.topic_order,
							lp.id AS lesson_progress_id,
							lp.subtopic_id,
							st.subtopic_order,
							DATE_ADD(DATE_FORMAT(lp.created_date, '%Y-%m-%d %H:%i:%s'), INTERVAL 24 HOUR) AS compare_date
						FROM group_info gi
						INNER JOIN group_schedule gsch
							ON gsch.group_info_id = gi.id
								AND gsch.week_day_id = (WEEKDAY(NOW()) + 1)
						INNER JOIN status s
							ON s.id = gi.status_id
								AND s.is_active = 1
						LEFT JOIN lesson_progress lp
							ON lp.id = (SELECT lp2.id
										FROM lesson_progress lp2,
											subtopic st2
										WHERE lp2.group_info_id = gi.id
											AND st2.id = lp2.subtopic_id
										ORDER BY st2.subtopic_order DESC
										LIMIT 1)
						LEFT JOIN topic t
							ON t.id = gi.topic_id
						LEFT JOIN subtopic st
							ON st.id = lp.subtopic_id
						WHERE gi.start_date <= DATE_FORMAT(NOW(), '%Y-%m-%d')
						ORDER BY gi.start_date DESC, gi.id, gsch.week_day_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['group_info']] = array('group_info_id' => intval($value['group_info_id']),
														'group_name' => $value['group_name'],
														'lesson_type' => $value['lesson_type'],
														'subject_id' => $value['subject_id'],
														'topic_id' => $value['topic_id'],
														'topic_order' => intval($value['topic_order']),
														'start_date' => $value['start_date'],
														'created_date' => $value['group_info_created_date'],
														'lp_id' => $value['lesson_progress_id'],
														'subtopic_id' => $value['subtopic_id'],
														'subtopic_order' => intval($value['subtopic_order']),
														'compare_date' => $value['compare_date']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subject_topic_subtopics ($subject_id) {
		GLOBAL $connect;

		try {
			
			$query = "SELECT sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							t.topic_order,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							st.subtopic_order
						FROM subject sj,
							topic t,
							subtopic st
						WHERE sj.id = :subject_id
							AND t.subject_id = sj.id
							AND st.topic_id = t.id
						ORDER BY sj.title, t.topic_order, st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['subject_id']])) {
					$result[$value['subject_id']] = array('subject_id' => $value['subject_id'],
															'subject_title' => $value['subject_title'],
															'topics' => array());
				}

				if (!isset($result[$value['subject_id']]['topics'][$value['topic_order']])) {
					$result[$value['subject_id']]['topics'][$value['topic_order']] = array('topic_id' => $value['topic_id'],
																							'topic_title' => $value['topic_title'],
																							'subtopics' => array());
				}

				$result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'][$value['subtopic_order']] = array('subtopic_id' => $value['subtopic_id'],
																														'subtopic_title' => $value['subtopic_title']);
			}

			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_next_group_lesson ($group_result) {
		GLOBAL $connect;

		try {
			$subject_infos = array();
			$total_result = array();
			$current_date = date('Y-m-d H:i:s');
			foreach ($group_result as $group_info) {
				if (!isset($subject_infos[$group_info['subject_id']])) {
					$subject_info = get_subject_topic_subtopics($group_info['subject_id']);
					$subject_infos = array_merge($subject_infos, $subject_info);
				}

				if ($group_info['lesson_progress_id'] == null || $group_info['lesson_progress_id'] == '') {
					$subtopics = array();
					if ($group_info['lesson_type'] == 'topic') {
						$subtopics = $subject_infos[$group_info['subject_id']]['topics'][$group_info['topic_order']]['subtopics'];
					}

					if (isset($subtopics) && count($subtopics) >= 3) {
						$next_subtopic = next_subtopic($subtopics, null, $group_info['group_info_id']);

						if (count($next_subtopic) == 0) {
							array_push($total_result, array('subtopic_id' => -1,
															'group_info_id' => $group_info['group_info_id']));
						} else {
							$total_result = array_merge($total_result, $next_subtopic)
						}
					}
				} else {
					if ($group_info['lesson_type'] == 'topic') {
						$subtopics = $subject_infos[$group_info['subject_id']]['topics'][$group_info['topic_order']]['subtopics'];
						$next_subtopic = next_subtopic($subtopics, $group_info['subtopic_order'], $group_info['group_info_id']);
						if (count($next_subtopic) == 0) {
							array_push($total_result, array('subtopic_id' => -1,
															'group_info_id' => $group_info['group_info_id']))
						} else {
							$total_result = array_merge($total_result, $next_subtopic);
						}
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function next_subtopic ($subtopics, $subtopic_order, $group_info_id) {

		try {
			$result = array();

			if ($subtopic_order == null) {
				$count = count($subtopics);

				if ($count >= 1) {
					array_push($result, array('subtopic_id' => $subtopics[1]['id'],
												'group_info_id' => $group_id));
				}
				if ($count >= 2) {
					array_push($result, array('subtopic_id' => $subtopics[2]['id'],
												'group_info_id' => $group_id));
				}
				if ($count == 3) {
					array_push($result, array('subtopic_id' => $subtopics[3]['id'],
												'group_info_id' => $group_id));
				}
			} else {
				$count = count($subtopics) - $subtopic_order;

				if ($count == 2) {
					array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 1]['id'],
												'group_info_id' => $group_id));

					array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 2]['id'],
												'group_info_id' => $group_id));
				} else if ($count >= 1) {
					array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 1]['id'],
												'group_info_id' => $group_id));					
				}
			}


		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_lesson_progress ($group_progress) {
		GLOBAL $connect;

		try {

			$result = array();

			foreach ($group_progress as $value) {
				$subtopic_id = $value['subtopic_id'];
				$group_info_id = $value['group_info_id'];

				if ($subtopic_id != -1) {

					if (!lesson_progress_exists($group_info_id, $subtopic_id)) {
						$lesson_progress_id = insert_lesson_progress($group_info_id, $subtopic_id);

						array_push($result, array('lesson_progress_id' => $lesson_progress_id,
													'group_students_id' => get_group_students_id($group_info_id)));
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function lesson_progress_exists ($group_info_id, $subtopic_id) {
		GLOBAL $connect;

		try {
			
			$query = "SELECT lp.id
						FROM lesson_progress lp
						WHERE lp.subtopic_id = :subtopic_id
							AND lp.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			return $row_count == 0 ? false : true;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_lesson_progress ($group_info_id, $subtopic_id) {
		GLOBAL $connect;

		try {

			$created_date = date('Y-m-d').' 07:00:00';

			$query = "INSERT INTO lesson_progress (group_info_id, subtopic_id, created_date)
												VALUES (:group_info_id, :subtopic_id, :created_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
			$stmt->execute();

			$lesson_progress_id = $connect->lastInsertId();

			return $lesson_progress_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_students_id ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.id AS group_student_id
						FROM group_student gs
						WHERE gs.is_archive = 0
							AND gs.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['group_student_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_material_acces_by_lesson_progress () {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id,
							lp.subtopic_id,
							lp.group_info_id
						FROM lesson_progress lp
						WHERE DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$lesson_progress_list = $stmt->fetchAll();

			foreach ($lesson_progress_list as $lesson_progress) {
				$group_info_id = $lesson_progress['group_info_id'];
				$lesson_progress_id = $lesson_progress['id'];
				$query = "SELECT gs.id,
								gs.start_from,
								gs.student_id,
								gs.status,
								gs.id NOT IN (SELECT tva.group_student_id
												FROM tutorial_video_action tva
												WHERE tva.lesson_progress_id = :lesson_progress_id AND tva.forced_material_access_id IS NULL) AS not_tva_exists,
								gs.id NOT IN (SELECT tda.group_student_id
												FROM tutorial_document_action tda
												WHERE tda.lesson_progress_id = :lesson_progress_id AND tda.forced_material_access_id IS NULL) AS not_tda_exists,
								gs.id NOT IN (SELECT eva.group_student_id
												FROM end_video_action eva
												WHERE eva.lesson_progress_id = :lesson_progress_id AND eva.forced_material_access_id IS NULL) AS not_eva_exists,
								gs.id NOT IN (SELECT mta.group_student_id
												FROM material_test_action mta
												WHERE mta.lesson_progress_id = :lesson_progress_id AND mta.forced_material_access_id IS NULL) AS not_mta_exists
							FROM group_student gs
							WHERE gs.group_info_id = :group_info_id
								AND gs.is_archive = 0";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->execute();
				$group_student_list = $stmt->fetchAll();

				foreach ($group_student_list as $group_student) {
					if ($group_student['start_from'] == 0 || has_access($group_info_id, $group_student['start_from'])) {
						$payment = true;
						if ($group_student['status'] == 'inactive') {
							$payment = use_student_exist_payments($group_student['student_id'], $group_student['id'], $group_info_id);
						}
						if ($payment) {
							if ($group_student['status'] == 'waiting' || $group_student['status'] == 'inactive') {
								check_access_until($group_info_id, $group_student['start_from'], $group_student['id']);
							}
							if ($group_student['not_tva_exists'] && $group_student['not_tda_exists'] && $group_student['not_eva_exists'] && $group_student['not_mta_exists']) {
								set_tutorial_video_action($lesson_progress_id, $group_student['id']);
								set_tutorial_document_action($lesson_progress_id, $group_student['id']);
								set_end_video_action($lesson_progress_id, $group_student['id']);
								set_material_test_action($lesson_progress_id, $group_student['id']);
							}
						}
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function has_access ($group_info_id, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.subtopic_id
						FROM lesson_progress lp
						WHERE lp.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$subtopics = array();
			foreach ($query_result as $value) {
				array_push($subtopics, $value['subtopic_id']);
			}

			return in_array($subtopic_id, $subtopics);
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function use_student_exist_payments ($student_id, $group_student_id, $group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gsp.id
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
							AND gsp.is_used = 0
						ORDER BY gsp.payed_date ASC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 1) {
				$group_student_payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

				$query = "UPDATE group_student_payment SET is_used = 0, start_date = NOW() WHERE id = :id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $group_student_payment_info['id'], PDO::PARAM_INT);
				$stmt->execute();

				update_group_student_to_waiting($group_student_id);
				return true;
			} else {
				$query = "SELECT sb.id,
								sb.days
							FROM student_balance sb
							WHERE sb.student_id = :student_id
								AND sb.is_used = 0";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->execute();
				$query_result = $stmt->fetchAll();

				$days = 0;
				foreach ($query_result as $student_balance) {
					$days += $student_balance['days'];

					$query = "UPDATE student_balance SET is_used = 1, used_for_group = :group_info_id, used_date = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->bindParam(':id', $student_balance['id'], PDO::PARAM_INT);
					$stmt->execute();
				}

				if ($days > 0) {
					$query = "INSERT INTO group_student_payment (group_student_id, payed_date, is_used, payment_type, partial_payment_days)
														VALUES (:group_student_id, NOW(), 0, 'balance', :partial_payment_days)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
					$stmt->bindParam(':partial_payment_days', $days, PDO::PARAM_INT);
					$stmt->execute();

					update_group_student_to_waiting($group_student_id);
					return true;
				}
				return false;
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function update_group_student_to_waiting ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_student SET status = 'waiting' WHERE id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_access_until ($group_info_id, $subtopic_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id
						FROM lesson_progress lp
						WHERE lp.subtopic_id = :subtopic_id
							AND lp.group_info_id = :group_info_id
							AND lp.created_date <= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:10:00')";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$query = "UPDATE group_student SET status = 'active' WHERE id = :group_student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();

				$query = "SELECT gsp.id,
								gsp.payment_type,
								DATE_FORMAT(gsp.start_date, '%Y-%m-%d') AS start_date,
								gsp.partial_payment_days
							FROM group_student_payment gsp
							WHERE gsp.group_student_id = :group_student_id
								AND gsp.is_used = 0
								AND gsp.finished_date IS NULL
							ORDER BY gsp.payment_date, gsp.payed_date
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
				$group_student_payment = $stmt->fetch(PDO::FETCH_ASSOC);

				$access_until = "";
				if ($group_student_payment['payment_type'] == 'money') {
					if ($group_student_payment['start_date'] != '') {
						if ($group_student_payment['partial_payment_days'] != '') {
							$access_until = date('Y-m-d', strtotime($group_student_payment['start_date'].' + '.$group_student_payment['partial_payment_days'].' days'));
						} else {
							$access_until = date('Y-m-d', strtotime($group_student_payment['start_date'].' + 1 month'));
						}
					} else {
						$access_until = date('Y-m-d', strtotime(' + 1 month'));
					}
				} else if ($group_student_payment['payment_type'] == 'balance') {
					if ($group_student_payment['start_date'] != '') {
						$access_until = date('Y-m-d', strtotime($group_student_payment['start_date'].' + '.$group_student_payment['partial_payment_days'].' days'));
					} else {
						$access_until = date('Y-m-d', strtotime('+ '.$group_student_payment['partial_payment_days'].' days'));
					}
				}

				if ($access_until != '') {
					$query = "UPDATE group_student_payment SET access_until = :access_until, is_used = 1, used_date = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
					$stmt->bindParam(':id', $group_student_payment['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_video_action ($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tva.id
						FROM tutorial_video_action tva
						WHERE tva.lesson_progress_id = :lesson_progress_id
							AND tva.group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "INSERT INTO tutorial_video_action (lesson_progress_id, group_student_id)
													VALUES (:lesson_progress_id, :group_student_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
			}			
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_document_action ($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tda.id
						FROM tutorial_document_action tda
						WHERE tda.lesson_progress_id = :lesson_progress_id
							AND tda.group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "INSERT INTO tutorial_document_action (lesson_progress_id, group_student_id)
														VALUES (:lesson_progress_id, :group_student_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
			}			
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_end_video_action ($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT eva.id
						FROM end_video_action eva
						WHERE eva.lesson_progress_id = :lesson_progress_id
							AND eva.group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "INSERT INTO end_video_action (lesson_progress_id, group_student_id)
													VALUES (:lesson_progress_id, :group_student_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
			}			
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_material_test_action ($lesson_progress_id, $group_students_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id
						FROM material_test_action mta
						WHERE mta.lesson_progress_id = :lesson_progress_id
							AND mta.group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "SELECT mt.id
							FROM material_test mt,
								lesson_progress lp
							WHERE lp.id = :lesson_progress_id
								AND mt.subtopic_id = lp.subtopic_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();

				$is_finish = 0;
				if ($row_count == 0) {
					$is_finish = 1;
				}

				$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
													VALUES (:group_student_id, :lesson_progress_id, :is_finish)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':is_finish', $is_finish, PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>
