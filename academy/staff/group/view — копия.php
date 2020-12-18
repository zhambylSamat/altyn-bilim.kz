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

			$total_result = array();

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

			foreach ($groups as $value) {
				$is_army_group = get_is_army_group($value['group_info_id']);
				$total_result[$value['group_info_id']] = array("group_name" => $value['group_name'],
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
																'is_army_group' => $is_army_group
																);
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
				array_push($total_result[$value['group_info_id']]['schedule'], $value['week_day_id']);
			}

			$stmt = $connect->prepare("SELECT gi.id AS group_info_id,
											s.id AS student_id,
											s.last_name,
											s.first_name,
											s.password_reset
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
				$total_result[$value['group_info_id']]['students'][$value['student_id']] = array('student_id' => $value['student_id'],
																								'last_name' => $value['last_name'],
																								'first_name' => $value['first_name'],
																								'password_reset' => $value['password_reset']);
			}

			$result = array();

			foreach ($groups as $value) {
				if (!isset($result[$value['subject_id']])) {
					$result[$value['subject_id']] = array('subject_title' => $value['subject_title'],
														'groups' => array());
				}

				$result[$value['subject_id']]['groups'][$value['group_info_id']] = $total_result[$value['group_info_id']];
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
			$result = array();
			$result['time1'] = date('H:i:s');
			$tutorial_videos = get_tutorial_video_by_lesson_progress_id($lesson_progress_id);
			$result['time2'] = date('H:i:s');
			// $tutorial_documents = get_tutorial_document_by_lesson_progress_id($lesson_progress_id);
			$end_videos = get_end_video_by_lesson_progress_id($lesson_progress_id);
			$result['time3'] = date('H:i:s');
			$material_tests = get_material_test_by_lesson_progress_id($lesson_progress_id);
			$result['time4'] = date('H:i:s');
			
			// $material_config = get_material_config($lesson_progress_id);

			$student_list = get_student_actions_log($lesson_progress_id);
			$result['time5'] = date('H:i:s');
			$query = "SELECT lp.group_info_id
						FROM lesson_progress lp
						WHERE lp.id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_info_id = $stmt->fetch(PDO::FETCH_ASSOC)['group_info_id'];
			$result['time6'] = date('H:i:s');
			$is_army_group = get_is_army_group($group_info_id);
			$result['time7'] = date('H:i:s');
			$student_submitted_class_work_list = $is_army_group
												? get_student_submitted_class_work_list($lesson_progress_id) 
												: array();
			$result['time8'] = date('H:i:s');
			$test_progress_information = $is_army_group
											? get_test_progress_information($lesson_progress_id)
											: array();
			$result['time9'] = date('H:i:s');

			$result['tutorial_videos'] = $tutorial_videos;
			$result['end_videos'] = $end_videos;
			$result['material_tests'] = $material_tests;
			$result['student_list'] = $student_list;
			$result['student_submitted_class_work_list'] = $student_submitted_class_work_list;
			$result['test_progress_information'] = $test_progress_information;
			// $result = array('tutorial_videos' => $tutorial_videos,
			// 				// 'tutorial_documents' => $tutorial_documents,
			// 				'end_videos' => $end_videos,
			// 				'material_tests' => $material_tests,
			// 				// 'material_config' => $material_config,
			// 				'student_list' => $student_list,
			// 				'student_submitted_class_work_list' => $student_submitted_class_work_list,
			// 				'test_progress_information' => $test_progress_information
			// 			);
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
											-- AND gs.is_archive = 0
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

	function get_material_config($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT smc.perceive
										FROM subtopic_material_config smc,
											lesson_progress lp
										WHERE lp.id = :lp_id
											AND smc.subtopic_id = lp.subtopic_id");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();
			$result = array();
			foreach ($sql_result as $value) {
				array_push($result, $value['perceive']);
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

	function get_student_tutorial_video_action_logs_depreciated($lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			if (count($forced_material_access_id) == 0) {
				$forced_material_access_id = array(0);
				$start_point = 1;
				$query = "SELECT s.id AS student_id,
								tv.id AS tv_id,
								tv.video_order,
								tva.id AS tva_id,
								DATE_FORMAT(tva.accessed_date, '%H:%i:%s %d.%m.%Y') AS accessed_date,
								tval.id AS tval_id,
								DATE_FORMAT(tval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
								tval.access_before,
								(tval.access_before > NOW()) AS now_accessed,
								tva.forced_material_access_id
							FROM lesson_progress lp
							INNER JOIN group_info gi
								ON gi.id = lp.group_info_id
							INNER JOIN group_student gs
								ON gs.group_info_id = gi.id
							INNER JOIN student s
								ON s.id = gs.student_id
							LEFT JOIN tutorial_video tv
								ON tv.subtopic_id = lp.subtopic_id
							LEFT JOIN tutorial_video_action tva
								ON tva.lesson_progress_id = lp.id
									AND tva.group_student_id = gs.id
									AND tva.forced_material_access_id IS NULL
							LEFT JOIN tutorial_video_action_log tval
								ON tval.tutorial_video_action_id = tva.id
									AND tval.tutorial_video_id = tv.id
							WHERE lp.id = :lp_id
							ORDER BY tva.forced_material_access_id,
									s.last_name,
									s.first_name,
									tv.video_order,
									tva.accessed_date,
									tval.opened_date";
			} else {
				$start_point = 2;
				$query = "SELECT s.id AS student_id,
								tv.id AS tv_id,
								tv.video_order,
								tva.id AS tva_id,
								DATE_FORMAT(tva.accessed_date, '%H:%i:%s %d.%m.%Y') AS accessed_date,
								tval.id AS tval_id,
								DATE_FORMAT(tval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
								tval.access_before,
								(tval.access_before > NOW()) AS now_accessed,
								tva.forced_material_access_id
							FROM lesson_progress lp
							INNER JOIN group_info gi
								ON gi.id = lp.group_info_id
							INNER JOIN group_student gs
								ON gs.group_info_id = gi.id
							INNER JOIN student s
								ON s.id = gs.student_id
							LEFT JOIN tutorial_video tv
								ON tv.subtopic_id = lp.subtopic_id
							LEFT JOIN tutorial_video_action tva
								ON tva.lesson_progress_id = lp.id
									AND tva.group_student_id = gs.id
									AND tva.forced_material_access_id IN (".implode(',', $forced_material_access_id).")
							LEFT JOIN tutorial_video_action_log tval
								ON tval.tutorial_video_action_id = tva.id
									AND tval.tutorial_video_id = tv.id
							WHERE lp.id = :lp_id
							ORDER BY tva.forced_material_access_id,
									s.last_name,
									s.first_name,
									tv.video_order,
									tva.accessed_date,
									tval.opened_date";
			}
			
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			$fma_id = 0;
			$position = $start_point;
			foreach ($sql_result as $value) {
				if ($fma_id == 0) {
					$fma_id = $value['forced_material_access_id'];
				}
				else if ($value['forced_material_access_id'] != $fma_id) {
					$position++;
					$fma_id = $value['forced_material_access_id'];
				}
				if ($value['tva_id'] != '' && $value['video_order'] != '') {
					if ($fma_id != 0) {
						$tmp = array_search($fma_id, $forced_material_access_id);
						$position = explode('!', $tmp)[0];
						// print_r($tmp);
						// echo $position.' '.$val['forced_material_access_id']."<br>";
					}
					$result[$value['student_id']][$position][$value['video_order']] = array('tval_id' => $value['tval_id'],
																							'opened_date' => $value['opened_date'],
																							'access_before' => $value['access_before'],
																							'accessed' => $value['now_accessed']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_document_by_lesson_progress_id($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT td.id,
											td.document_order,
											td.link,
											td.title
										FROM lesson_progress lp,
											tutorial_document td
										WHERE lp.id = :lp_id
											AND td.subtopic_id = lp.subtopic_id
										ORDER BY td.document_order");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$result[$value['document_order']] = array('id' => $value['id'],
														'title' => $value['title']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_end_video_action_logs_depreciated($lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {
			if (count($forced_material_access_id) == 0) {
				$forced_material_access_id = array(0);
				$start_point = 1;
				$query = "SELECT s.id AS student_id,
								ev.id AS ev_id,
								ev.video_order,
								eva.id AS eva_id,
								eva.accessed_date,
								eval.id AS eval_id,
								DATE_FORMAT(eval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
								eval.access_before,
								(eval.access_before > NOW()) AS now_accessed,
								eva.forced_material_access_id
							FROM lesson_progress lp
							INNER JOIN group_info gi
								ON gi.id = lp.group_info_id
							INNER JOIN group_student gs
								ON gs.group_info_id = gi.id
							INNER JOIN student s
								ON s.id = gs.student_id
							LEFT JOIN end_video ev
								ON ev.subtopic_id = lp.subtopic_id
							LEFT JOIN end_video_action eva
								ON eva.lesson_progress_id = lp.id
									AND eva.group_student_id = gs.id
									AND eva.forced_material_access_id IS NULL
							LEFT JOIN end_video_action_log eval
								ON eval.end_video_action_id = eva.id
									AND eval.end_video_id = ev.id
							WHERE lp.id = :lp_id
							ORDER BY eva.forced_material_access_id,
									s.last_name,
									s.first_name,
									ev.video_order,
									eva.accessed_date,
									eval.opened_date";
			} else {
				$start_point = 2;
				$query = "SELECT s.id AS student_id,
								ev.id AS ev_id,
								ev.video_order,
								eva.id AS eva_id,
								eva.accessed_date,
								eval.id AS eval_id,
								DATE_FORMAT(eval.opened_date, '%H:%i:%s %d.%m.%Y') AS opened_date,
								eval.access_before,
								(eval.access_before > NOW()) AS now_accessed,
								eva.forced_material_access_id
							FROM lesson_progress lp
							INNER JOIN group_info gi
								ON gi.id = lp.group_info_id
							INNER JOIN group_student gs
								ON gs.group_info_id = gi.id
							INNER JOIN student s
								ON s.id = gs.student_id
							LEFT JOIN end_video ev
								ON ev.subtopic_id = lp.subtopic_id
							LEFT JOIN end_video_action eva
								ON eva.lesson_progress_id = lp.id
									AND eva.group_student_id = gs.id
									AND eva.forced_material_access_id IN (".implode(',', $forced_material_access_id).")
							LEFT JOIN end_video_action_log eval
								ON eval.end_video_action_id = eva.id
									AND eval.end_video_id = ev.id
							WHERE lp.id = :lp_id
							ORDER BY eva.forced_material_access_id,
									s.last_name,
									s.first_name,
									ev.video_order,
									eva.accessed_date,
									eval.opened_date";
			}
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			$fma_id = 0;
			$position = $start_point;
			foreach ($sql_result as $value) {
				if ($fma_id == 0) {
					$fma_id = $value['forced_material_access_id'];
				}
				else if ($fma_id != 0 && $value['forced_material_access_id'] != $fma_id) {
					$position++;
					$fma_id = $value['forced_material_access_id'];
				}
				if ($value['eva_id'] != '' && $value['video_order'] != '') {
					if ($fma_id != 0) {
						$tmp = array_search($fma_id, $forced_material_access_id);
						$position = explode('!', $tmp)[0];
						// print_r($tmp);
						// echo $position.' '.$fma_id."<br>";
					}
					$result[$value['student_id']][$position][$value['video_order']] = array('eval_id' => $value['eval_id'],
																							'opened_date' => $value['opened_date'],
																							'access_before' => $value['access_before'],
																							'accessed' => $value['now_accessed']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_material_test_action_logs_depreciated($lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT s.id AS student_id,
							mta.id AS mta_id,
							mta.accessed_date,
							mtr.id AS mtr_id,
							DATE_FORMAT(mtr.start_date, '%H:%i:%s %d.%m.%Y') AS start_date,
							DATE_FORMAT(mtr.finish_date, '%H:%i:%s %d.%m.%Y') AS finish_date,
							mtr.total_result,
							mtr.actual_result,
							mta.is_finish,
							lp.subtopic_id AS subtopic_id,
							lp.id AS lp_id,
							1 AS now_accessed
						FROM lesson_progress lp
						INNER JOIN group_info gi
							ON gi.id = lp.group_info_id
						INNER JOIN group_student gs
							ON gs.group_info_id = gi.id
						INNER JOIN student s
							ON s.id = gs.student_id
						LEFT JOIN material_test_action mta
							ON mta.lesson_progress_id = lp.id
								AND mta.group_student_id = gs.id
						LEFT JOIN material_test_result mtr
							ON mtr.material_test_action_id = mta.id
								AND mtr.subtopic_id = lp.subtopic_id
						WHERE lp.id = :lp_id
						ORDER BY s.last_name,
								s.first_name,
								mta.accessed_date,
								mtr.start_date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			$position = 1;
			foreach ($sql_result as $value) {
				if ($value['mta_id'] != '') {
					$result[$value['student_id']][$position][$value['subtopic_id']] = array('mtr_id' => $value['mtr_id'],
																						'mta_id' => $value['mta_id'],
																						'lp_id' => $value['lp_id'],
																						'is_finish' => $value['is_finish'],
																						'accessed_date' => $value['accessed_date'],
																						'start_date' => $value['start_date'],
																						'finish_date' => $value['finish_date'],
																						'accessed' => $value['now_accessed'],
																						'total_result' => $value['total_result'],
																						'actual_result' => $value['actual_result']);
				}
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

	function get_student_actions_log($lesson_progress) {
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
				array_push($student_list[$student_id]['progress_log'], array('lp_id' => $lesson_progress_id,
																			'fma_id' => 
																			'created_date' => ));

				$tv_action_log = get_student_tutorial_video_action_log($student_id, $lesson_progress_id);
				$ev_action_log = get_student_end_video_action_log($student_id, $lesson_progress_id);
				$mt_action_log = get_student_material_test_action_log($student_id, $lesson_progress_id);

				// $tv_action_log = get_student_tutorial_video_action_logs($lesson_progress_id, array());
				// $ev_action_log = get_student_end_video_action_logs($lesson_progress_id, array());
				// $mt_action_result = get_student_material_test_action_logs($lesson_progress_id);
				// $tv_action_log = get_student_tutorial_video_action_logs($lesson_progress_id, $fma_ids);
				// // $td_action_log = get_student_tutorial_document_action_logs($lesson_progress_id, $fma_ids);
				// $ev_action_log = get_student_end_video_action_logs($lesson_progress_id, $fma_ids);

				$student_list[$student_id]['is_access'] = $is_access;
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_actions_log_depreciated($lesson_progress_id) {

		GLOBAL $connect;


		function append_to_student_list($student_list, $tv_action_log, $ev_action_log, $mt_action_result) {

			foreach ($tv_action_log as $student_id => $v) {
				foreach ($v as $position => $value) {
					$student_list[$student_id]['progress_log'][$position]['tutorial_video_logs'] = $value;
				}
			}

			// foreach ($td_action_log as $student_id => $v) {
			// 	foreach ($v as $position => $value) {
			// 		$student_list[$student_id]['progress_log'][$position]['tutorial_document_logs'] = $value;
			// 	}
			// }

			foreach ($ev_action_log as $student_id => $v) {
				foreach ($v as $position => $value) {
					$student_list[$student_id]['progress_log'][$position]['end_video_logs'] = $value;
				}
			}

			foreach ($mt_action_result as $student_id => $v) {
				foreach ($v as $position => $value) {
					$student_list[$student_id]['progress_log'][$position]['material_test_result'] = $value;
				}
			}

			return $student_list;
		}

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

			$position = 1;
			foreach ($student_list as $student_id => $value) {
				$student_list[$student_id]['progress_log'][$position] = array('lp_id' => $lesson_progress_id,
																				'fma_id' => null,
																				'created_date' => $lp_created_date);
				$student_list[$student_id]['is_access'] = $is_access;
			}

			$tv_action_log = get_student_tutorial_video_action_logs($lesson_progress_id, array());
			$ev_action_log = get_student_end_video_action_logs($lesson_progress_id, array());
			$mt_action_result = get_student_material_test_action_logs($lesson_progress_id);

			$student_list = append_to_student_list($student_list, $tv_action_log, $ev_action_log, $mt_action_result);

			$stmt = $connect->prepare("SELECT fma.id,
											DATE_FORMAT(fma.created_date, '%H:%i:%s %d.%m.%Y') AS created_date,
											gs.student_id,
											IF(TIME(DATE_FORMAT(NOW(), '%H:%i:%s')) > TIME('07:00:00'), 
												(fma.created_date < TIMESTAMP(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00')
													AND fma.created_date >= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')), 
        										(fma.created_date < TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')
        											AND fma.created_date >= TIMESTAMP(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00'))) AS is_active
										FROM forced_material_access fma,
											group_student gs
										WHERE fma.lesson_progress_id = :lp_id
											AND gs.id = fma.group_student_id
										ORDER BY fma.created_date ASC");
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$fma_ids = array();
			foreach ($sql_result as $fma_value) {
				$fma_id = $fma_value['id'];
				$student_list[$fma_value['student_id']]['progress_log'][(++$position)] = array('lp_id' => $lesson_progress_id,
																						'fma_id' => $fma_id,
																						'created_date' => $fma_value['created_date']);
				$fma_ids[$position.'!'] = $fma_id;
				$student_list[$fma_value['student_id']]['is_access'] = $fma_value['is_active'];
			}
			if (count($fma_ids) > 0) {
				$tv_action_log = get_student_tutorial_video_action_logs($lesson_progress_id, $fma_ids);
				// $td_action_log = get_student_tutorial_document_action_logs($lesson_progress_id, $fma_ids);
				$ev_action_log = get_student_end_video_action_logs($lesson_progress_id, $fma_ids);
				$student_list = append_to_student_list($student_list, $tv_action_log, $ev_action_log, array());
			}

			return $student_list;
			
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
							gs.id AS group_student_id
						FROM group_student gs
						WHERE gs.student_id = :student_id
							AND gs.transfer_from_group = :group_info_id
							AND gs.created_date <= :created_date";

			$created_date = date('Y-m-d', strtotime(' + 1 month'));
			$enough_group_infos = false;
			while (!$enough_group_infos) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					$group_info_id = $query_result['group_info_id'];
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

?>