<?php
	include_once('../common/connection.php');

	$students_lesson_progress = get_students_lesson_progress();

	if (count($students_lesson_progress) > 0) {
		// echo json_encode($students_lesson_progress);
		insert_notification($students_lesson_progress);
	}

	function get_students_lesson_progress() {
		GLOBAL $connect;

		try {

			$query = "SELECT fma.lesson_progress_id,
							fma.group_student_id
						FROM forced_material_access fma
						WHERE DATE_FORMAT(fma.created_date, '%Y-%m-%d') = DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
							AND (SELECT gs.id FROM group_student gs WHERE gs.id = fma.group_student_id) IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$first_part_result = $stmt->fetchAll();

			$query = "SELECT fma.lesson_progress_id,
							fma.group_student_id
						FROM forced_material_access fma
						WHERE DATE_FORMAT(fma.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
							AND (SELECT gs.id FROM group_student gs WHERE gs.id = fma.group_student_id) IS NOT NULL";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$second_part_result = $stmt->fetchAll();

			$lp_and_gs = array();

			foreach ($first_part_result as $v1) {
				$add = true;
				foreach ($second_part_result as $v2) {
					if ($v1['lesson_progress_id'] == $v2['lesson_progress_id'] && $v1['group_student_id'] == $v2['group_student_id']) {
						$add = false;
						break;
					}
				}
				if ($add) {
					array_push($lp_and_gs, array('lesson_progress_id' => $v1['lesson_progress_id'],
												'group_student_id' => $v1['group_student_id']));
				}
			}

			$query = "SELECT count(tval.id) > 0 AS is_watch
						FROM tutorial_video_action_log tval
						WHERE tval.tutorial_video_action_id IN (SELECT tva.id
																FROM tutorial_video_action tva
																WHERE tva.lesson_progress_id = :lesson_progress_id
																	AND tva.group_student_id = :group_student_id)";
			foreach ($lp_and_gs as $index => $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
				$stmt->execute();
				$is_watch = $stmt->fetch(PDO::FETCH_ASSOC)['is_watch'];

				if ($is_watch) {
					unset($lp_and_gs[$index]);
				}
			}

			$query = "SELECT count(eval.id) > 0 AS is_watch
						FROM end_video_action_log eval
						WHERE eval.end_video_action_id IN (SELECT eva.id
															FROM end_video_action eva
															WHERE eva.lesson_progress_id = :lesson_progress_id
																AND eva.group_student_id = :group_student_id)";
			foreach ($lp_and_gs as $index => $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
				$stmt->execute();

				$is_watch = $stmt->fetch(PDO::FETCH_ASSOC)['is_watch'];

				if ($is_watch) {
					unset($lp_and_gs[$index]);
				}
			}

			return $lp_and_gs;
			
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function insert_notification($students_lesson_progress) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO no_progress_student_notification (group_student_id, lesson_progress_id) VALUES(:group_student_id, :lesson_progress_id)";

			foreach ($students_lesson_progress as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>