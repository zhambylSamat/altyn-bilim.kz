<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	check_admin_access();

	function get_all_materials() {
		GLOBAL $connect;

		try {
			$query = "SELECT sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							tv.id AS tv_id,
							td.id AS td_id,
							ev.id AS ev_id,
							(SELECT count(mt.id)
							FROM material_test mt
							WHERE mt.subtopic_id = st.id) AS material_test_count
						FROM subject sj
						INNER JOIN topic t
							ON t.subject_id = sj.id
						INNER JOIN subtopic st
							ON st.topic_id = t.id
						LEFT JOIN tutorial_video tv
							ON tv.subtopic_id = st.id
						LEFT JOIN tutorial_document td
							ON td.subtopic_id = st.id
						LEFT JOIN end_video ev
							ON ev.subtopic_id = st.id
						WHERE sj.id IN (16, 18, 20, 21, 26, 27)
						ORDER BY sj.title, t.topic_order, st.subtopic_order, tv.video_order";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $v) {
				if (!isset($result[$v['subject_id']])) {
					$result[$v['subject_id']] = array('title' => $v['subject_title'],
														'topics' => array());
				}
				if (!isset($result[$v['subject_id']]['topics'][$v['topic_id']])) {
					$result[$v['subject_id']]['topics'][$v['topic_id']] = array('title' => $v['topic_title'],
																				'subtopics' => array());
				}
				if (!isset($result[$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']])) {
					$result[$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']] =
																array('title' => $v['subtopic_title'],
																		'tv_count' => 0,
																		'td_count' => 0,
																		'ev_count' => 0,
																		'mt_count' => $v['material_test_count']);
				}
				if ($v['tv_id'] != '') {
					$result[$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['tv_count']++;
				}
				if ($v['td_id'] != '') {
					$result[$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['td_count']++;
				}
				if ($v['ev_id'] != '') {
					$result[$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['ev_count']++;
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_material_active_links() {
		GLOBAL $connect;

		try {

			$query = "SELECT ml.id AS material_link_id, 
							ml.code,
							ml.access_until,
							ml.comment,
							mlc.type,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							st.id AS subtopic_id,
							st.title AS subtopic_title
						FROM material_link ml,
							material_link_content mlc,
							subtopic st,
							topic t,
							subject sj
						WHERE ml.id = mlc.material_link_id
							AND mlc.subtopic_id = st.id
							AND st.topic_id = t.id
							AND t.subject_id = sj.id
							AND ml.access_until > NOW()
						ORDER BY ml.created_date DESC, sj.title, t.topic_order, st.subtopic_order";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $v) {
				if (!isset($result[$v['material_link_id']])) {
					$date1 = date_create($v['access_until']);
					$date2 = date_create(date('Y-m-d H:i:s'));
					$diff = date_diff($date1, $date2);
					$days = $diff->format('%a');
					$hours = intval($diff->format('%h')) + intval($days) * 24;
					$minutes = $diff->format('%i');
					$access_time = $hours.'сағ. '.$minutes.'мин.';
					$result[$v['material_link_id']] = array('code' => $v['code'],
															'access_until' => $v['access_until'],
															'access_time' => $access_time,
															'comment' => $v['comment'],
															'subjects' => array());
				}

				if (!isset($result[$v['material_link_id']]['subjects'][$v['subject_id']])) {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']] = array('title' => $v['subject_title'],
																							'topics' => array());
				}

				if (!isset($result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']])) {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]
																					= array('title' => $v['topic_title'],
																							'subtopics' => array());
				}

				if (!isset($result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']])) {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']] = array('title' => $v['subtopic_title'],
									'tv' => false,
									'td' => false,
									'ev' => false,
									'mt' => false);
				}

				if ($v['type'] == 'tutorial_video') {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['tv'] = true;
				} else if ($v['type'] == 'tutorial_document') {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['td'] = true;
				} else if ($v['type'] == 'end_video') {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['ev'] = true;
				} else if ($v['type'] == 'material_test') {
					$result[$v['material_link_id']]['subjects'][$v['subject_id']]['topics'][$v['topic_id']]['subtopics'][$v['subtopic_id']]['mt'] = true;
				}

			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_mlt_student_list () {
		GLOBAL $connect;

		try {

			$query = "SELECT mlti.id AS material_link_test_info_id, 
							ml.comment,
							mlti.fio,
							mlti.result_json,
							DATE_FORMAT(mlti.submit_time, '%d.%m.%Y %H:%i:%s') AS submit_time,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							ml.code
						FROM material_link_test_info mlti,
							material_link ml,
							subtopic st,
							topic t,
							subject sj
						WHERE ml.id = mlti.material_link_id
							AND st.id = mlti.subtopic_id
							AND t.id = st.topic_id
							AND sj.id = t.subject_id
						ORDER BY mlti.submit_time DESC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$test_result_json = json_decode($value['result_json'], true);
				$actual_result = $test_result_json['actual_result'];
				$total_result = $test_result_json['total_result'];
				$percent = ceil(intval(($actual_result/$total_result)*100));
				$result[$value['subtopic_id'].$value['material_link_test_info_id']] = array('material_link_comment' => $value['comment'],
																							'fio' => $value['fio'],
																							'submit_time' => $value['submit_time'],
																							'subject_title' => $value['subject_title'],
																							'subtopic_title' => $value['subtopic_title'],
																							'subtopic_id' => $value['subtopic_id'],
																							'topic_title' => $value['topic_title'],
																							'actual_result' => $actual_result,
																							'total_result' => $total_result,
																							'percent' => $percent,
																							'code' => $value['code']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>