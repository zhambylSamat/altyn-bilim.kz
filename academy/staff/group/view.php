<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/global_controller.php');

	function get_subjects() {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT id, title FROM subject ORDER BY title");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_active_groups_by_groups_id($groups = array()) {
		GLOBAL $connect;

		try {

			$total_result = array('simple_group' => array(),
									'army_group' => array(),
									'marathon_group' => array());

			$query_part = '';
			if (count($groups) > 0) {
				$query_part = "AND gi.id IN ('".implode("', '", $groups)."') ";
			}

				$stmt = $connect->prepare("SELECT gi.id AS group_info_id,
												gi.group_name,
												gi.lesson_type,
												DATE_FORMAT(gi.start_date, '%d.%m.%Y') AS start_date,
												DATE_FORMAT(gi.created_date, '%d.%m.%Y') AS created_date,
												sj.id AS subject_id,
												sj.title AS subject_title,
												t.id AS topic_id,
												t.title AS topic_title,
												(SELECT count(gs2.id)
												FROM group_student gs2
												WHERE gs2.group_info_id = gi.id
													AND gs2.is_archive = 0) AS student_count
											FROM group_info gi
											INNER JOIN subject sj
												ON sj.id = gi.subject_id
											LEFT JOIN topic t
												ON t.id = gi.topic_id
											WHERE sj.id = gi.subject_id
												AND 1 = (SELECT s2.is_active
														FROM status s2
														WHERE s2.id = gi.status_id)
												$query_part
											ORDER BY sj.title, gi.group_name ASC");
			$stmt->execute();
			$groups = $stmt->fetchAll();

			$all_army_groups = get_all_active_army_groups();
			$all_marathon_groups = get_all_active_marathon_groups();
			$all_school_groups = get_all_active_school_groups();

			foreach ($groups as $value) {
				$is_army_group = in_array($value['group_info_id'], $all_army_groups);
				$is_marathon_group = in_array($value['group_info_id'], $all_marathon_groups);
				$is_school_group = in_array($value['group_info_id'], $all_school_groups);
				$tmp = array("group_name" => $value['group_name'],
								"group_info_id" => $value['group_info_id'],
								"lesson_type" => $value['lesson_type'],
								"start_date" => $value['start_date'],
								"create_date" => $value['created_date'],
								"subject" => array('id' => $value['subject_id'],
													'title' => $value['subject_title']),
								"topic" => array('id' => $value['topic_id'],
													'title' => $value['topic_title']),
								"student_count" => $value['student_count'],
								"schedule" => array(),
								"students" => array(),
								'is_army_group' => $is_army_group,
								'is_marathon_group' => $is_marathon_group,
								'is_school_group' => $is_school_group
								);
				if ($is_army_group) {
					$total_result['army_group'][$value['group_info_id']] = $tmp;
				} else if ($is_marathon_group) {
					$total_result['marathon_group'][$value['group_info_id']] = $tmp;
				} else {
					$total_result['simple_group'][$value['group_info_id']] = $tmp;
				}
			}

			$stmt = $connect->prepare("SELECT gs.group_info_id,
											gs.week_day_id
										FROM group_schedule gs,
											group_info gi,
											status s
										WHERE gs.group_info_id = gi.id
											AND s.id = gi.status_id
											AND s.is_active = 1
											$query_part
										GROUP BY gs.group_info_id, gs.week_day_id
										ORDER BY gi.created_date DESC, gs.week_day_id ASC");
			$stmt->execute();
			$group_schedule = $stmt->fetchAll();

			foreach ($group_schedule as $value) {
				$is_army_group = in_array($value['group_info_id'], $all_army_groups);
				$is_marathon_group = in_array($value['group_info_id'], $all_marathon_groups);

				if ($is_army_group) {
					array_push($total_result['army_group'][$value['group_info_id']]['schedule'], $value['week_day_id']);	
				} else if ($is_marathon_group) {
					array_push($total_result['marathon_group'][$value['group_info_id']]['schedule'], $value['week_day_id']);	
				} else {
					if (isset($total_result['simple_group'][$value['group_info_id']])) {
						array_push($total_result['simple_group'][$value['group_info_id']]['schedule'], $value['week_day_id']);
					}
				}
				
			}

			$stmt = $connect->prepare("SELECT gi.id AS group_info_id,
											s.id AS student_id,
											s.last_name,
											s.first_name,
											s.password_reset,
											s.instagram
										FROM group_info gi,
											group_student gs,
											student s
										WHERE gs.group_info_id = gi.id
											$query_part
											AND s.id = gs.student_id
											AND gs.is_archive = 0
											AND 1 = (SELECT st2.is_active
													FROM status st2
													WHERE st2.id = gi.status_id)
											AND 1 = (SELECT st2.is_active
													FROM status st2
													WHERE st2.id = s.status_id)
										GROUP BY gi.id, s.id");
			$stmt->execute();
			$group_students = $stmt->fetchAll();
			foreach ($group_students as $value) {
				$is_army_group = in_array($value['group_info_id'], $all_army_groups);
				$is_marathon_group = in_array($value['group_info_id'], $all_marathon_groups);

				if ($is_army_group) {
					$total_result['army_group'][$value['group_info_id']]['students'][$value['student_id']]
																					= array('student_id' => $value['student_id'],
																							'last_name' => $value['last_name'],
																							'first_name' => $value['first_name'],
																							'instagram' => $value['instagram'],
																							'password_reset' => $value['password_reset']);
				} else if ($is_marathon_group) {
					$total_result['marathon_group'][$value['group_info_id']]['students'][$value['student_id']]
																					= array('student_id' => $value['student_id'],
																							'last_name' => $value['last_name'],
																							'first_name' => $value['first_name'],
																							'instagram' => $value['instagram'],
																							'password_reset' => $value['password_reset']);
				} else {
					$total_result['simple_group'][$value['group_info_id']]['students'][$value['student_id']]
																					= array('student_id' => $value['student_id'],
																							'last_name' => $value['last_name'],
																							'first_name' => $value['first_name'],
																							'instagram' => $value['instagram'],
																							'password_reset' => $value['password_reset']);
				}
			}

			$result = array('army_group' => array(),
							'marathon_group' => array(),
							'simple_group' => array());

			foreach ($groups as $value) {
				$is_army_group = in_array($value['group_info_id'], $all_army_groups);
				$is_marathon_group = in_array($value['group_info_id'], $all_marathon_groups);

				if ($is_army_group) {
					if (!isset($result['army_group'][$value['subject_id']])) {
						$result['army_group'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																			'groups' => array());	
					}
					$result['army_group'][$value['subject_id']]['groups'][$value['group_info_id']]
																			= $total_result['army_group'][$value['group_info_id']];
				} else if ($is_marathon_group) {
					if (!isset($result['marathon_group'][$value['subject_id']])) {
						$result['marathon_group'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																				'groups' => array());	
					}
					$result['marathon_group'][$value['subject_id']]['groups'][$value['group_info_id']]
																			= $total_result['marathon_group'][$value['group_info_id']];
				} else {
					if (!isset($result['simple_group'][$value['subject_id']])) {
						$result['simple_group'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																				'groups' => array());	
					}
					$result['simple_group'][$value['subject_id']]['groups'][$value['group_info_id']]
																			= $total_result['simple_group'][$value['group_info_id']];
				}
			}


			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_short_group_info_by_group_id($group_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT gi.id,
											gi.subject_id,
											gi.topic_id,
											gi.lesson_type,
											gi.group_name,
											DATE_FORMAT(gi.start_date, '%d.%m.%Y') AS start_date,
											gsch.week_day_id
										FROM group_info gi,
											group_schedule gsch
										WHERE gi.id = :id
											AND gsch.group_info_id = gi.id
										ORDER BY gsch.week_day_id ASC");
			$stmt->bindParam(':id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			$group_id = '';
			foreach ($sql_result as $value) {
				if ($group_id != $value['id']) {
					$group_id = $value['id'];

					$result['group_id'] = $value['id'];
					$result['subject_id'] = $value['subject_id'];
					$result['topic_id'] = $value['topic_id'];
					$result['lesson_type'] = $value['lesson_type'];
					$result['group_name'] = $value['group_name'];
					$result['start_date'] = $value['start_date'];
					$result['schedule'] = array();
				}
				array_push($result['schedule'], $value['week_day_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_lesson_progress_by_group_id($group_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT lp.id AS lp_id,
											DATE_FORMAT(lp.created_date, '%d.%m.%Y') AS created_date,
											sj.id AS subject_id,
											sj.title AS subject_title,
											t.id AS topic_id,
											t.title AS topic_title,
											t.topic_order,
											st.id AS subtopic_id,
											st.title AS subtopic_title,
											st.subtopic_order
										FROM lesson_progress lp,
											subject sj,
											topic t,
											subtopic st
										WHERE lp.group_info_id = :group_id
											AND st.id = lp.subtopic_id
											AND t.id = st.topic_id
											AND sj.id = t.subject_id
										ORDER BY sj.title, t.topic_order DESC, st.subtopic_order ASC");
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();

			foreach ($sql_result as $value) {
				$result[$value['subject_id']]['title'] = $value['subject_title'];
				$result[$value['subject_id']]['id'] = $value['subject_id'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['title'] = $value['topic_title'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['id'] = $value['topic_id'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'][$value['subtopic_order']]['title'] = $value['subtopic_title'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'][$value['subtopic_order']]['id'] = $value['subtopic_id'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'][$value['subtopic_order']]['lp_id'] = $value['lp_id'];
				$result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'][$value['subtopic_order']]['created_date'] = $value['created_date'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_info_with_lesson_progress($group_id) {

		$group_short_info = get_short_group_info_by_group_id($group_id);
		$group_lesson_progress = get_group_lesson_progress_by_group_id($group_id);

		$result = $group_short_info;
		$result['lesson_progress'] = $group_lesson_progress;

		return $result;
	}

	function get_materials_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect;

		try {
			$result = array('time');
			$tutorial_videos = get_tutorial_video_by_lesson_progress_id($lesson_progress_id);
			$end_videos = get_end_video_by_lesson_progress_id($lesson_progress_id);
			$material_tests = get_material_test_by_lesson_progress_id($lesson_progress_id);

			$student_list = get_student_actions_log($lesson_progress_id);
			$query = "SELECT lp.group_info_id
						FROM lesson_progress lp
						WHERE lp.id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_info_id = $stmt->fetch(PDO::FETCH_ASSOC)['group_info_id'];
			$is_army_group = get_is_army_group($group_info_id);
			$student_submitted_class_work_list = $is_army_group
												? get_student_submitted_class_work_list($lesson_progress_id) 
												: array();
			$test_progress_information = $is_army_group
											? get_test_progress_information($lesson_progress_id)
											: array();

			$result['tutorial_videos'] = $tutorial_videos;
			$result['end_videos'] = $end_videos;
			$result['material_tests'] = $material_tests;
			$result['student_list'] = $student_list;
			$result['is_army_group'] = $is_army_group;
			$result['student_submitted_class_work_list'] = $student_submitted_class_work_list;
			$result['test_progress_information'] = $test_progress_information;
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_list_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect; 

		try {

			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name,
											s.phone,
											gs.status,
											gs.is_archive,
											gs.start_from,
											st.title,
											gs.id AS gs_id
										FROM lesson_progress lp,
											group_info gi,
											group_student gs,
											student s,
											subtopic st
										WHERE lp.id = :lp_id
											AND gi.id = lp.group_info_id
											AND gs.group_info_id = gi.id
											AND s.id = gs.student_id
											AND st.id = gs.start_from
										ORDER BY s.last_name, s.first_name");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$result[$value['id']] = array('id' => $value['id'],
										'group_student_id' => $value['gs_id'],
										'first_name' => $value['first_name'],
										'last_name' => $value['last_name'],
										'status' => $value['status'],
										'title' => $value['title'],
										'phone' => $value['phone'],
										'is_archive' => $value['is_archive'],
										'progress_log' => array());
			}
			return $result;

		} catch (Exception $e) {
			throw $e;
		}

			
	}

	function get_tutorial_video_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT tv.id,
											tv.video_order,
											tv.link,
											tv.title,
											tv.duration
										FROM lesson_progress lp,
											tutorial_video tv
										WHERE lp.id = :lp_id
											AND tv.subtopic_id = lp.subtopic_id
										ORDER BY tv.video_order");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$result[$value['video_order']] = array('id' => $value['id'],
														'title' => $value['title'],
														'duration' => $value['duration']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_end_video_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT ev.id,
											ev.video_order,
											ev.link,
											ev.title,
											ev.duration
										FROM lesson_progress lp,
											end_video ev
										WHERE lp.id = :lp_id
											AND ev.subtopic_id = lp.subtopic_id
										ORDER BY ev.video_order");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$result[$value['video_order']] = array('id' => $value['id'],
														'title' => $value['title'],
														'duration' => $value['duration']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mt.id,
							mt.title
						FROM lesson_progress lp,
							material_test mt
						WHERE lp.id = :lp_id
							AND mt.subtopic_id = lp.subtopic_id
						ORDER BY mt.title";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$result[$value['id']] = array('id' => $value['id'],
												'title' => $value['title']);
			}
			
			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_actions_log($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT DATE_FORMAT(lp.created_date, '%H:%i:%s %d.%m.%Y') AS created_date,
											IF(TIME(DATE_FORMAT(NOW(), '%H:%i:%s')) > TIME('07:00:00'), 
												(lp.created_date < TIMESTAMP(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00')
													AND lp.created_date >= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')), 
        										(lp.created_date < TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')
        											AND lp.created_date >= TIMESTAMP(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00'))) AS is_active
										FROM lesson_progress lp
										WHERE lp.id = :lp_id");
			$stmt->bindParam(":lp_id", $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$lp_created_date = $sql_result['created_date'];
			$is_access = $sql_result['is_active'];

			$student_list = get_student_list_by_lesson_progress_id($lesson_progress_id);

			foreach ($student_list as $student_id => $value) {
				$student_list[$student_id]['is_access'] = $is_access;

				$forced_material_access_datas = get_forced_material_access_datas($lesson_progress_id, $student_id);

				$mt_result = get_student_material_test_result($student_id, $lesson_progress_id);
				$student_list[$student_id]['progress_log'][$lesson_progress_id] = array('lp_id' => $lesson_progress_id,
																						'fma_id' => '',
																						'created_date' => $lp_created_date,
																						'tutorial_video_action_logs' => array(),
																						'end_video_action_logs' => array(),
																						'material_test_result' => $mt_result);
				foreach ($forced_material_access_datas as $value) {
					$student_list[$student_id]['is_access'] = $value['is_active'];
					$student_list[$student_id]['progress_log'][$value['forced_material_access_id']] = array('lp_id' => $lesson_progress_id,
																										'fma_id' => $value['forced_material_access_id'],
																										'created_date' => $value['created_date'],
																										'tutorial_video_action_logs' => array(),
																										'end_video_action_logs' => array());
				}

				$tv_action_log = get_student_tutorial_video_action_log($student_id, $lesson_progress_id);
				foreach ($tv_action_log as $tutorial_video_action_id => $value) {
					if ($value['fma_id'] == '') {
						$student_list[$student_id]['progress_log'][$lesson_progress_id]['tutorial_video_action_logs'] = $value['video'];
					} else {
						$student_list[$student_id]['progress_log'][$value['fma_id']]['tutorial_video_action_logs'] = $value['video'];
					}
				}
				$ev_action_log = get_student_end_video_action_log($student_id, $lesson_progress_id);
				foreach ($ev_action_log as $end_video_action_id => $value) {
					if ($value['fma_id'] == '') {
						$student_list[$student_id]['progress_log'][$lesson_progress_id]['end_video_action_logs'] = $value['video'];
					} else {
						$student_list[$student_id]['progress_log'][$value['fma_id']]['end_video_action_logs'] = $value['video'];	
					}
				}
			}

			return $student_list;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_forced_material_access_datas ($lesson_progress_id, $student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT fma.id AS forced_material_access_id,
							DATE_FORMAT(fma.created_date, '%H:%i:%s %d.%m.%Y') AS created_date,
							IF(TIME(DATE_FORMAT(NOW(), '%H:%i:%s')) > TIME('07:00:00'), 
								(fma.created_date < TIMESTAMP(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00')
									AND fma.created_date >= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')), 
								(fma.created_date < TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')
									AND fma.created_date >= TIMESTAMP(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00'))) AS is_active
						FROM forced_material_access fma,
							group_student gs
						WHERE gs.student_id = :student_id
							AND fma.lesson_progress_id = :lesson_progress_id
							AND fma.group_student_id = gs.id
						ORDER BY fma.created_date ASC";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_material_test_result ($student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id AS material_test_action_id,
							mtr.id AS material_test_result_id,
							DATE_FORMAT(mtr.start_date, '%H:%i:%s %d.%m.%Y') AS start_date,
							DATE_FORMAT(mtr.finish_date, '%H:%i:%s %d.%m.%Y') AS finish_date,
							mtr.total_result,
							mtr.actual_result,
							mta.is_finish
						FROM material_test_action mta
						INNER JOIN lesson_progress lp
							ON lp.id = :lesson_progress_id
						INNER JOIN group_student gs
							ON gs.student_id = :student_id
						LEFT JOIN material_test_result mtr
							ON mtr.material_test_action_id = mta.id
						WHERE mta.lesson_progress_id = lp.id
							AND mta.group_student_id = gs.id
						ORDER BY mta.accessed_date ASC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = array();
			if ($row_count > 0) {
				$result = array('mta_id' => $query_result['material_test_action_id'],
								'mtr_id' => $query_result['material_test_result_id'],
								'start_date' => $query_result['start_date'],
								'finish_date' => $query_result['finish_date'],
								'total_result' => $query_result['total_result'],
								'actual_result' => $query_result['actual_result'],
								'is_finish' => $query_result['is_finish']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	} 

	function get_student_end_video_action_log ($student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT ev.video_order AS end_video_order,
							eva.id AS end_video_action_id,
							eval.id AS end_video_action_log_id,
							DATE_FORMAT(eval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
							eval.access_before,
							(eval.access_before > NOW()) AS now_accessed,
							eva.forced_material_access_id
						FROM end_video_action eva
						INNER JOIN lesson_progress lp
							ON lp.id = :lesson_progress_id
						INNER JOIN group_student gs
							ON gs.student_id = :student_id
						LEFT JOIN end_video ev
							ON ev.subtopic_id = lp.subtopic_id
						LEFT JOIN end_video_action_log eval
							ON eval.end_video_action_id = eva.id
						LEFT JOIN forced_material_access fma
							ON fma.id = eva.forced_material_access_id
						WHERE eva.lesson_progress_id = lp.id
							AND eva.group_student_id = gs.id
						ORDER BY eva.accessed_date DESC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if (!isset($result[$value['end_video_action_id']])) {
					$result[$value['end_video_action_id']] = array('video' => array(),
																	'fma_id' => '',
																	'fma_created_date' => '');
				}
				$result[$value['end_video_action_id']]['video'][$value['end_video_order']]  = array('eval_id' => $value['end_video_action_log_id'],
																									'opened_date' => $value['opened_date'],
																									'access_before' => $value['access_before'],
																									'accessed' => $value['now_accessed']);

				$result[$value['end_video_action_id']]['fma_id'] = $value['forced_material_access_id'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	} 

	function get_student_tutorial_video_action_log ($student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tv.video_order AS tutorial_video_order,
							tva.id AS tutorial_video_action_id,
							tval.id AS tutorial_video_action_log_id,
							DATE_FORMAT(tval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
							tval.access_before,
							(tval.access_before > NOW()) AS now_accessed,
							tva.forced_material_access_id
						FROM tutorial_video_action tva
						INNER JOIN lesson_progress lp
							ON lp.id = :lesson_progress_id
						INNER JOIN group_student gs
							ON gs.student_id = :student_id
						LEFT JOIN tutorial_video tv
							ON tv.subtopic_id = lp.subtopic_id
						LEFT JOIN tutorial_video_action_log tval
							ON tval.tutorial_video_action_id = tva.id
						LEFT JOIN forced_material_access fma
							ON fma.id = tva.forced_material_access_id
						WHERE tva.lesson_progress_id = lp.id
							AND tva.group_student_id = gs.id
						ORDER BY tva.accessed_date DESC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if (!isset($result[$value['tutorial_video_action_id']])) {
					$result[$value['tutorial_video_action_id']] = array('video' => array(),
																		'fma_id' => '',
																		'fma_created_date' => '');
				}
				$result[$value['tutorial_video_action_id']]['video'][$value['tutorial_video_order']] 
																							= array('tval_id' => $value['tutorial_video_action_log_id'],
																									'opened_date' => $value['opened_date'],
																									'access_before' => $value['access_before'],
																									'accessed' => $value['now_accessed']);
				$result[$value['tutorial_video_action_id']]['fma_id'] = $value['forced_material_access_id'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_submitted_class_work_list ($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT s.id AS student_id,
							s.last_name,
							s.first_name,
							gscwsf.file_link,
							gs.id AS group_student_id,
							gs.group_info_id
						FROM tutorial_document_action tda
						INNER JOIN group_student gs
							ON gs.id = tda.group_student_id
								AND gs.is_archive = 0
						INNER JOIN student s
							ON s.id = gs.student_id
						LEFT JOIN group_student_class_work_submit gscws
							ON gscws.tutorial_document_action_id = tda.id
						LEFT JOIN group_student_class_work_submit_files gscwsf
							ON gscwsf.group_student_class_work_submit_id = gscws.id
						WHERE tda.lesson_progress_id = :lesson_progress_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['student_id']])) {
					$result[$value['student_id']] = array('last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'group_student_id' => $value['group_student_id'],
															'group_info_id' => $value['group_info_id'],
															'class_work_files' => array(),
															'warning_info' => array());
				}
				if ($value['file_link'] != '') {
					array_push($result[$value['student_id']]['class_work_files'], $value['file_link']);
				}
				
			}

			foreach ($result as $student_id => $info) {
				if (count($info['class_work_files']) == 0) {
					$result[$student_id]['warning_info'] = get_student_no_home_work_warning($student_id, $info['group_student_id'], $value['group_info_id']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_no_home_work_warning ($student_id, $group_student_id, $group_info_id) {
		GLOBAL $connect;

		try {
			$group_student_ids = array($group_student_id);
			
			$query = "SELECT gs.group_info_id,
							gs.id AS group_student_id,
							gs.transfer_from_group
						FROM group_student gs
						WHERE gs.student_id = :student_id
							AND gs.group_info_id = :group_info_id";

			$created_date = date('Y-m-d', strtotime(' + 1 month'));
			$enough_group_infos = false;
			$group_info_id_count = 0;
			while (!$enough_group_infos) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				// $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					// $group_info_id = $query_result['group_info_id'];
					$group_info_id = $query_result['transfer_from_group'];
					$group_student_id = $query_result['group_student_id'];
					array_push($group_student_ids, $group_student_id);
					$group_info_id_count++;
				} else {
					$enough_group_infos = true;
				}
			}

			$query = "SELECT gsnhw.id,
							gsnhw.group_student_id,
							gsnhw.warning_count,
							gsnhw.nullify_date,
							gsnhw.lesson_progress_ids,
							DATE_FORMAT(gsnhw.nullify_date, '%Y-%m-%d') < DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-%d') AS is_depreciate
						FROM group_student_no_home_work_warning gsnhw
						WHERE gsnhw.group_student_id IN (".implode(',', $group_student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$row_count = $stmt->rowCount();

			$result = array();
			if ($row_count > 0) {
				if ($query_result['is_depreciate']) {
					$query = "DELETE FROM group_student_no_home_work_warning WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $query_result['id'], PDO::PARAM_INT);
					$stmt->execute();
				} else {
					$result = array('group_student_no_home_work_warning_id' => $query_result['id'],
									'group_student_id' => $group_student_id,
									'warning_count' => $query_result['warning_count'],
									'lesson_progress_ids' => json_decode($query_result['lesson_progress_ids'], true));
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_test_progress_information ($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mtr.result_json
						FROM material_test_result mtr
						WHERE mtr.material_test_action_id IN (SELECT mta.id 
																FROM material_test_action mta
																WHERE mta.lesson_progress_id = :lesson_progress_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if ($value['result_json'] != "") {
					$json = json_decode($value['result_json'], true)['json'];
					foreach ($json as $numeration => $info) {
						if (!isset($result[$numeration])) {
							$result[$numeration] = array('total' => 0,
															'passed' => 0);
						}

						$result[$numeration]['total']++;
						if ($info['expected'] == $info['actual']) {
							$result[$numeration]['passed']++;
						}
					}
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	if (isset($_GET['topics-by-subject-id'])) {

		try {

			$subject_id = $_GET['subject_id'];

			$stmt = $connect->prepare("SELECT id,
											title
										FROM topic t
										WHERE subject_id = :subject_id
										ORDER BY topic_order ASC");
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$data['data'] = $stmt->fetchAll();

			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['content-info'])) {
		try {

			if (isset($_GET['topic_id'])) {
				$topic_id = $_GET['topic_id'];
				$stmt = $connect->prepare("SELECT t.id AS topic_id, 
												t.title AS topic_title,
												st.id AS subtopic_id,
												st.title AS subtopic_title,
												(SELECT count(tv2.id)
												FROM tutorial_video tv2
												WHERE tv2.subtopic_id = st.id) AS tutorial_video_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'tutorial_video') AS tutorial_video_config,
												(SELECT count(td2.id)
												FROM tutorial_document td2
												WHERE td2.subtopic_id = st.id) AS tutorial_document_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'tutorial_document') AS tutorial_document_config,
												(SELECT count(ev2.id)
												FROM end_video ev2
												WHERE ev2.subtopic_id = st.id) AS end_video_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'end_video') AS end_video_config
										FROM topic t,
											subtopic st
										WHERE t.id = :topic_id
											AND st.topic_id = t.id
										ORDER BY st.subtopic_order ASC");
				$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			} else if (isset($_GET['subject_id'])) {
				$subject_id = $_GET['subject_id'];
				$stmt = $connect->prepare("SELECT t.id AS topic_id, 
												t.title AS topic_title,
												st.id AS subtopic_id,
												st.title AS subtopic_title,
												(SELECT count(tv2.id)
												FROM tutorial_video tv2
												WHERE tv2.subtopic_id = st.id) AS tutorial_video_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'tutorial_video') AS tutorial_video_config,
												(SELECT count(td2.id)
												FROM tutorial_document td2
												WHERE td2.subtopic_id = st.id) AS tutorial_document_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'tutorial_document') AS tutorial_document_config,
												(SELECT count(ev2.id)
												FROM end_video ev2
												WHERE ev2.subtopic_id = st.id) AS end_video_count,
												(SELECT count(smc2.id)
												FROM subtopic_material_config smc2
												WHERE smc2.subtopic_id = st.id
													AND smc2.perceive = 'end_video') AS end_video_config
										FROM topic t,
											subtopic st
										WHERE t.subject_id = :subject_id
											AND st.topic_id = t.id
										ORDER BY t.topic_order ASC,
												st.subtopic_order ASC");
				$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			}
			$stmt->execute();
			$result = $stmt->fetchAll();
			$total_result = array('topic_count' => 0,
								'total_subtopic_count' => 0,
								'topics' => array());

			$topic_id = '';
			$subtopic_id = '';
			foreach ($result as $value) {
				if ($topic_id != $value['topic_id']) {
					$topic_id = $value['topic_id'];
					$total_result['topic_count']++;
					$total_result['topics'][$topic_id] = array('title' => $value['topic_title'],
																'subtopic_count' => -2, // -1 for 'кіріспе' and -1 for 'қорытынды' -> total -2
																'subtopics' => array());
				}

				$total_result['topics'][$topic_id]['subtopic_count']++;
				if ($total_result['topics'][$topic_id]['subtopic_count'] > 0) {
					$total_result['total_subtopic_count']++;
				}
				$total_result['topics'][$topic_id]['subtopics'][$value['subtopic_id']] = array('title' => $value['subtopic_title'],
																						'tutorial_video_count' => $value['tutorial_video_count'],
																						'tutorial_video_config' => $value['tutorial_video_config'],
																						'tutorial_document_count' => $value['tutorial_document_count'],
																						'tutorial_document_config' => $value['tutorial_document_config'],
																						'end_video_count' => $value['end_video_count'],
																						'end_video_config' => $value['end_video_config']);
			}
			$data['content_result'] = $total_result;
			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_POST['search-student'])) {
		try {

			$search_text = $_POST['search-text'];
			$except_students = $_POST['except-students'];

			$search_text_arr = explode(' ', $search_text);
			$query_part = "";
			if (isset($search_text_arr[0])) {
				$query_part .= "s.first_name LIKE '%$search_text_arr[0]%'";
				$query_part .= "OR s.last_name LIKE '%$search_text_arr[0]%'";
			}
			if (isset($search_text_arr[1])) {
				$query_part .= "OR s.first_name LIKE '%$search_text_arr[1]%'";
				$query_part .= "OR s.last_name LIKE '%$search_text_arr[1]%'";	
			}

			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name
										FROM student s
										WHERE s.status_id = 2
										AND s.id NOT IN ($except_students)
										AND ($query_part)
										ORDER BY s.last_name, s.first_name
										LIMIT 20");
			$stmt->execute();

			$data['students'] = $stmt->fetchAll();
			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	}

	function get_student_army_medals ($group_info_id) {
		GLOBAL $connect;

		try {
			$result = array();

			$query = "SELECT gs.id AS group_student_id,
							s.id AS student_id,
							s.last_name,
							s.first_name,
							s.avatar_link
					FROM group_student gs,
						student s
					WHERE gs.group_info_id = :group_info_id
						AND s.id = gs.student_id
					ORDER BY s.last_name, s.first_name";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$all_level_medals = get_all_level_medals();

			$transfer_from_group = get_transfer_from_group($group_info_id);

			foreach ($query_result as $value) {
				if ($transfer_from_group != '') {
					$group_student_ids = get_group_info_ids($transfer_from_group, $value['student_id'], $value['group_student_id']);	
				} else {
					$group_student_ids = array($value['group_student_id']);
				}
				$army_medal_info = get_army_medal_info($group_student_ids, $all_level_medals);
				$result[$value['group_student_id']] = array('student_id' => $value['student_id'],
														'last_name' => $value['last_name'],
														'first_name' => $value['first_name'],
														'avatar_link' => $value['avatar_link'],
														'army_medal_info' => $army_medal_info);
			}

			uasort($result, 
				function($a, $b) {
					if ($a['army_medal_info']['percent'] == $b['army_medal_info']['percent']) {
						return 0;
					}
					return $a['army_medal_info']['percent'] < $b['army_medal_info']['percent'] ? 1 : -1;
				}
			);
				
			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_transfer_from_group($group_info_id) {
		GLOBAL $connect;
		try {

			$query = "SELECT gs.transfer_from_group
						FROM group_student gs
						WHERE gs.group_info_id = :group_info_id
							AND gs.transfer_from_group IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				return $stmt->fetch(PDO::FETCH_ASSOC)['transfer_from_group'];
			}
			return '';
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_info_ids ($group_info_id, $student_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$group_student_ids = array($group_student_id);

			$query = "SELECT gs.transfer_from_group,
							gs.id AS group_student_id
						FROM group_student gs
						WHERE gs.student_id = :student_id
							AND gs.group_info_id = :group_info_id";
			$enough_group_infos = false;
			$group_info_id_count = 0;
			while (!$enough_group_infos) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					$group_info_id = $query_result['transfer_from_group'];
					$group_student_id = $query_result['group_student_id'];
					array_push($group_student_ids, $group_student_id);
					$group_info_id_count++;
				} else {
					$enough_group_infos = true;
				}

				if ($group_info_id_count == 2) {
					$enough_group_infos = true;
				}
			}

			return $group_student_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_army_medal_info ($group_student_ids, $all_level_medals) {
		GLOBAL $connect;

		try {

			$query = "SELECT atm.id,
							atm.army_medal_id,
							atm.percent,
							am.level
						FROM army_test_medal atm,
							army_medal am
						WHERE atm.group_student_id IN (".implode(',', $group_student_ids).")
							AND am.id = atm.army_medal_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			$result = array();

			if ($row_count == 1) {
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$result = array('icon_link' => $all_level_medals[$query_result['level']]['icon_link'],
								'level' => $query_result['level'],
								'title' => $all_level_medals[$query_result['level']]['title'],
								'description' => $all_level_medals[$query_result['level']]['description'],
								'percent' => $query_result['percent']);
			} else {
				$result = array('icon_link' => $all_level_medals[1]['icon_link'],
								'level' => 1,
								'title' => $all_level_medals[1]['title'],
								'description' => $all_level_medals[1]['description'],
								'percent' => 0);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_level_medals () {
		GLOBAL $connect;

		try {

			$query = "SELECT am.id,
							am.icon_link,
							am.level,
							am.title,
							am.description
						FROM army_medal am";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['level']] = array('army_medal_id' => $value['id'],
												'icon_link' => $value['icon_link'],
												'level' => $value['level'],
												'title' => $value['title'],
												'description' => $value['description']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_is_time_to_trial_test ($group_info_id) {
		GLOBAL $connect;

		try {

			if (!get_is_army_group($group_info_id)) {
				return false;
			}

			$last_trial_test_time = get_last_trial_test_time ($group_info_id);

			$date1 = date_create($last_trial_test_time);
			$date2 = date_create(date('Y-m-d'));
			$days = date_diff($date1, $date2)->format('%a');

			if ($days >= 12) {
				return true;
			}
			return false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_last_trial_test_time ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gtt.appointment_date
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

	function get_group_student_trial_test_list ($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT stt.id AS student_trial_test_id,
							s.last_name,
							s.first_name,
							s.phone,
							tt.title AS trial_test_title,
							stt.result,
							DATE_FORMAT(stt.appointment_date, '%d.%m.%Y') AS appointment_date,
							DATE_FORMAT(stt.submit_date, '%d.%m.%Y') AS submit_date
						FROM group_student_trial_test gstt,
							student_trial_test stt,
							trial_test tt,
							student s,
							lesson_progress lp
						WHERE lp.id = :lesson_progress_id
							AND gstt.lesson_progress_id = lp.id
							AND stt.id = gstt.student_trial_test_id
							AND s.id = stt.student_id
							AND tt.id = stt.trial_test_id
						ORDER BY s.last_name, s.first_name";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				return array();
			}
			
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['student_trial_test_id']] = array('last_name' => $value['last_name'],
																'first_name' => $value['first_name'],
																'phone' => $value['phone'],
																'trial_test_title' => $value['trial_test_title'],
																'result' => json_decode($value['result'], true),
																'appointment_date' => $value['appointment_date'],
																'submit_date' => $value['submit_date']);
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_trial_test_result_list ($group_info_id) {
		GLOBAL $connect;

		try {

			$group_id_list = array($group_info_id);

			$group_info_id_point = $group_info_id;
			while (($group_info_id_point = has_transfer_from_group($group_info_id_point)) != 0) {
				array_push($group_id_list, $group_info_id_point);
			}

			$group_student_trial_test = get_group_student_trial_test($group_id_list);

			return $group_student_trial_test;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function has_transfer_from_group ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.transfer_from_group
						FROM group_student gs
						WHERE gs.group_info_id = :group_info_id
							AND gs.transfer_from_group IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();

			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				return 0;
			}

			return $stmt->fetch(PDO::FETCH_ASSOC)['transfer_from_group'];
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_trial_test ($group_info_ids) {
		GLOBAL $connect;

		try {

			$query = "SELECT gstt.id AS group_student_trial_test_id, 
							gstt.lesson_progress_id,
							s.id AS student_id,
							s.last_name,
							s.first_name,
							s.phone,
							stt.result,
							stt.id AS student_trial_test_id,
							DATE_FORMAT(stt.submit_date, '%d.%m.%Y') AS submit_date,
							DATE_FORMAT(stt.appointment_date, '%d.%m.%Y') AS appointment_date,
							tt.title AS trial_test_title
						FROM group_student_trial_test gstt,
							student_trial_test stt,
							student s,
							trial_test tt,
							lesson_progress lp
						WHERE gstt.lesson_progress_id IN (SELECT lp2.id
															FROM lesson_progress lp2
															WHERE lp2.group_info_id IN (".implode(',', $group_info_ids)."))
							AND stt.id = gstt.student_trial_test_id
							AND s.id = stt.student_id
							AND tt.id = stt.trial_test_id
							AND lp.id = gstt.lesson_progress_id
						ORDER BY lp.created_date DESC, s.last_name, s.first_name";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if (!isset($result[$value['lesson_progress_id'].'!'])) {
					$result[$value['lesson_progress_id'].'!'] = array();
				}

				array_push($result[$value['lesson_progress_id'].'!'], array('group_student_trial_test_id' => $value['group_student_trial_test_id'],
																		'lesson_progress_id' => $value['lesson_progress_id'],
																		'student_id' => $value['student_id'],
																		'last_name' => $value['last_name'],
																		'first_name' => $value['first_name'],
																		'phone' => $value['phone'],
																		'result' => json_decode($value['result'], true),
																		'submit_date' => $value['submit_date'],
																		'appointment_date' => $value['appointment_date'],
																		'trial_test_title' => $value['trial_test_title'],
																		'student_trial_test_id' => $value['student_trial_test_id']));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_trial_test_short_info ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT stt.id,
							stt.result
						FROM group_student_trial_test gstt,
							student_trial_test stt
						WHERE gstt.lesson_progress_id = (SELECT lp.id
														FROM group_student_trial_test gstt,
															lesson_progress lp
														WHERE lp.group_info_id = :group_info_id
															AND gstt.lesson_progress_id = lp.id
														ORDER BY lp.created_date DESC
														LIMIT 1)
							AND stt.id = gstt.student_trial_test_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('total_student_trial_test_count' => 0,
							'submitted_student_trial_test_count' => 0);
			foreach ($query_result as $value) {
				$result['total_student_trial_test_count']++;
				if ($value['result'] != '') {
					$result['submitted_student_trial_test_count']++;
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

?>