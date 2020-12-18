<?php
	include_once('../common/connection.php');

	$single_student_groups = get_single_student_groups();
	echo json_encode($single_student_groups)."<br><br>";
	foreach ($single_student_groups as $key => $value) {
		$no_progress_groups = get_no_progress_groups($value);
		if ($no_progress_groups) {
			$freeze_group = freeze_group($value['group_info_id']);
			echo $value['group_info_id'].': ';
			echo json_encode($no_progress_groups)."<br><br>";
		}
	}

	function get_single_student_groups() {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id,
							gi.subject_id,
							gi.topic_id,
							(SELECT gs.id
							FROM group_student gs
							WHERE gs.group_info_id = gi.id) AS group_student_id,
							(SELECT st.id
							FROM subtopic st
							WHERE st.topic_id = gi.topic_id
							ORDER BY st.subtopic_order ASC
							LIMIT 1) AS first_subtopic_id,
							(SELECT st.id
							FROM subtopic st
							WHERE st.topic_id = gi.topic_id
							ORDER BY st.subtopic_order DESC
							LIMIT 1) AS last_subtopic_id
						FROM group_info gi
						WHERE (SELECT ag.id 
								FROM army_group ag
								WHERE ag.group_info_id = gi.id) IS NULL
							AND (SELECT count(gs.id)
								FROM group_student gs
								WHERE gs.group_info_id = gi.id) = 1
							AND gi.status_id = 2
							AND gi.is_archive = 0
							AND gi.is_freeze = 0
							AND (SELECT fl.id
								FROM freeze_lesson fl
								WHERE fl.group_info_id = gi.id
									AND DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN fl.from_date AND fl.to_date) IS NULL";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, array('group_info_id' => $value['group_info_id'],
											'group_student_id' => $value['group_student_id'],
											'topic_id' => $value['topic_id'],
											'subject_id' => $value['subject_id'],
											'first_subtopic_id' => $value['first_subtopic_id'],
											'last_subtopic_id' => $value['last_subtopic_id']));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_no_progress_groups ($group_infos) {
		GLOBAL $connect;

		try {
			
			$query = "SELECT lp.id,
							lp.subtopic_id,
							(SELECT count(tv.id)
							FROM tutorial_video tv
							WHERE tv.subtopic_id = lp.subtopic_id
								AND tv.pop_up = 0) AS tutorial_video_count,
							(SELECT count(mt.id)
							FROM material_test mt
							WHERE mt.subtopic_id = lp.subtopic_id) AS material_test_count
						FROM lesson_progress lp,
							subtopic st
						WHERE lp.group_info_id = :group_info_id
							AND st.id = lp.subtopic_id
						ORDER BY st.subtopic_order DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_infos['group_info_id'], PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			$no_progress = false;
			// if ($row_count == 3) {
			// 	$lesson_3 = $stmt->fetch(PDO::FETCH_ASSOC);
			// 	$lesson_2 = $stmt->fetch(PDO::FETCH_ASSOC);
			// 	$lesson_1 = $stmt->fetch(PDO::FETCH_ASSOC);

			// 	if ($lesson_3['subtopic_id'] == $group_infos['last_subtopic_id']) {
			// 		$lesson_progress_ids = array($lesson_2, $lesson_1);
			// 		$no_progress = get_group_student_no_progress($group_infos['group_student_id'], $lesson_progress_ids);
			// 	} else {
			// 		$lesson_progress_ids = array($lesson_3, $lesson_2);
			// 		$no_progress = get_group_student_no_progress($group_infos['group_student_id'], $lesson_progress_ids);
			// 	}

			// } else if ($row_count == 2) {
			// 	$lesson_2 = $stmt->fetch(PDO::FETCH_ASSOC);
			// 	$lesson_1 = $stmt->fetch(PDO::FETCH_ASSOC);

			// 	if ($lesson_1['subtopic_id'] != $group_infos['first_subtopic_id']) {
			// 		$lesson_progress_ids = array($lesson_1, $lesson_2);
			// 		$no_progress = get_group_student_no_progress($group_infos['group_student_id'], $lesson_progress_ids);
			// 	}
			// }

			if ($row_count == 2) {
				$lesson_2 = $stmt->fetch(PDO::FETCH_ASSOC);
				$lesson_1 = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($lesson_2['subtopic_id'] != $group_infos['last_subtopic_id']) {
					$no_progress = get_group_student_no_progress_single($group_infos['group_student_id'], $lesson_1);
				} else {
					$no_progress = get_group_student_no_progress_single($group_infos['group_student_id'], $lesson_2);
				}
			}

			return $no_progress;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_no_progress ($group_student_id, $lesson_progress_ids) {
		GLOBAL $connect;

		try {

			$progress = array(false, false);
			foreach ($lesson_progress_ids as $index => $value) {
				$no_material_tests_result = false;
				if ($value['material_test_count'] > 0) {
					$no_material_tests_result = get_no_material_test_result($group_student_id, $value['id']);
				}
				$no_tutorial_video_action = false;
				if ($value['tutorial_video_count'] > 0 && $no_material_tests_result) {
					$no_tutorial_video_action = get_no_tutorial_video_action($group_student_id, $value['id'], $value['tutorial_video_count']);
				} else if ($value['tutorial_video_count'] > 0 && $value['material_test_count'] == 0) {
					$no_tutorial_video_action = get_no_tutorial_video_action($group_student_id, $value['id'], $value['tutorial_video_count']);
				}

				$progress[$index] = $no_tutorial_video_action && $no_material_tests_result;
			}

			return $progress[0] && $progress[1];

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_no_progress_single ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$no_material_tests_result = false;
			if ($lesson_progress_id['material_test_count'] > 0) {
				$no_material_tests_result = get_no_material_test_result($group_student_id, $lesson_progress_id['id']);	
			}

			return $no_material_tests_result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_no_material_test_result ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id AS material_test_action_id,
							mtr.id AS material_test_result_id,
							mtr.result_json
						FROM material_test_action mta
						LEFT JOIN material_test_result mtr
							ON mtr.material_test_action_id = mta.id
						WHERE mta.group_student_id = :group_student_id
							AND mta.lesson_progress_id = :lesson_progress_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['material_test_action_id'] != '') {
				if ($result['result_json'] == '') {
					return true;
				}
				return false;
			}

			return false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_no_tutorial_video_action ($group_student_id, $lesson_progress_id, $tutorial_video_count) {
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
				return false;
			}

			$query_result = $stmt->fetchAll();

			$tva_ids = array();
			foreach ($query_result as $value) {
				array_push($tva_ids, $value['id']);
			}

			$query = "SELECT tval.id
						FROM tutorial_video_action_log tval
						WHERE tval.tutorial_video_action_id IN (".implode(',', $tva_ids).")
						GROUP BY tval.tutorial_video_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == $tutorial_video_count) {
				return false;
			}

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function freeze_group ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_info SET is_freeze = 1 WHERE id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>