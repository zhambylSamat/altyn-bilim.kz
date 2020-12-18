<?php
	function get_available_subjects() {
		GLOBAL $connect;

		try {

			$query = "SELECT sj.id AS subject_id,
							sj.title AS subject_title
						FROM subject sj,
							group_info gi
						WHERE sj.id = gi.subject_id
							AND gi.status_id = 2
							AND gi.is_archive = 0
							AND 1 = (SELECT s2.is_active
									FROM status s2
									WHERE s2.id = gi.status_id)
						GROUP BY sj.title
						ORDER BY sj.title";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			$subjects = array();
			foreach ($sql_res as $value) {
				array_push($subjects, array('id' => $value['subject_id'], 'title' => $value['subject_title']));
			}
			return $subjects;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_groups_by_subject($subject_id) {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];
			$query = "SELECT gi.id,
							gi.group_name,
							gi.lesson_type
						FROM group_info gi
						WHERE gi.subject_id = :subject_id
							AND gi.id NOT IN (SELECT gi2.id FROM group_info gi2, group_student gs2 WHERE gs2.student_id = :student_id AND gi2.id = gs2.group_info_id)
							AND gi.id NOT IN (SELECT rc2.group_info_id FROM registration_course rc2 WHERE rc2.student_id = :student_id AND rc2.is_done = 0)
							AND gi.is_archive = 0
							AND 1 = (SELECT s2.is_active
									FROM status s2
									WHERE s2.id = gi.status_id)
						ORDER BY gi.group_name";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'group_name' => $value['group_name'], 'lesson_type' => $value['lesson_type']));
			}
			return $datas;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topics_by_group($group_id) {
		GlOBAL $connect;

		try {
			$query = "SELECT t.id,
							t.title
						FROM topic t,
							group_info gi
						WHERE gi.id = :group_id
							AND t.subject_id = gi.subject_id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	} 

	function get_subtopics_by_topic($topic_id, $group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT st.id,
							st.title,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned
						FROM subtopic st
						WHERE st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title'], 'learned' => $value['learned']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopics_by_group($group_id) {
		GLOBAL $connect;

		try {
			// $query = "SELECT st.id,
			// 				st.title,
			// 				(CASE
			// 					WHEN st.id = (SELECT lp2.subtopic_id
			// 									FROM lesson_progress lp2
			// 									WHERE lp2.group_info_id = :group_id
			// 										AND lp2.subtopic_id = st.id
			// 										AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
			// 					ELSE 0
			// 				END) AS learned
			// 			FROM subtopic st,
			// 				group_info gi
			// 			WHERE gi.id = :group_id
			// 				AND gi.topic_id = st.topic_id
			// 			ORDER BY st.subtopic_order";
			// $stmt = $connect->prepare($query);
			// $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			// $stmt->execute();
			// $sql_res = $stmt->fetchAll();
			// $datas = array();
			// foreach ($sql_res as $value) {
			// 	array_push($datas, array('id' => $value['id'], 'title' => $value['title'], 'learned' => $value['learned']));
			// }
			$query = "SELECT st.id,
							st.title,
							st.subtopic_order,
							gi.start_date,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned2,
							(SELECT DATE_FORMAT(lp2.created_date, '%d.%m.%Y')
							FROM lesson_progress lp2
							WHERE lp2.subtopic_id = st.id
								AND lp2.group_info_id = gi.id) AS learned_date,
							(SELECT st2.subtopic_order
							FROM lesson_progress lp2,
								subtopic st2
							WHERE lp2.subtopic_id = st.id
								AND lp2.group_info_id = gi.id
							ORDER BY lp2.created_date DESC
							LIMIT 1) AS learned_subtopic_order
						FROM subtopic st,
							group_info gi
						WHERE gi.id = :group_id
							AND gi.topic_id = st.topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			$schedules = get_schedul_of_group($group_id);
			$prev_date = '';
			$prev_subtopic_order = 1;
			$count = 0;
			foreach ($sql_res as $value) {
				$count++;
				$will_learn_date = "";
				if ($value['learned2'] == 0) {
					if ($prev_date == '') {
						$prev_date = $value['start_date'];
					}
					if ($count == count($sql_res) || $count == 1 || $count == 2) {
						$will_learn_date = date('d.m.Y', strtotime($prev_date));
					}
					else {
						$will_learn_date = learn_date($schedules, $prev_date);
					}
					$prev_date = $will_learn_date;
				} else {
					$prev_date = $value['learned_date'];
					$prev_subtopic_order = $value['learned_subtopic_order'];
				}
				array_push($datas, array('id' => $value['id'],
										'title' => $value['title'],
										'learned' => $value['learned'],
										'learned2' => $value['learned2'],
										'learned_date' => $value['learned_date'],
										'will_learn_date' => $will_learn_date));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function learn_date($schedules, $prev_date) {
		GLOBAL $connect;

		try {
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

	function get_start_date_of_lesson($group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT DATE_FORMAT(gi.start_date, '%Y-%m-%d') AS start_date
						FROM group_info gi
						WHERE gi.id = :group_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetch(PDO::FETCH_ASSOC);
			return $sql_res['start_date'];
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_schedul_of_group($group_id) {
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

	function get_reserve_subjects() {
		GLOBAL $connect;

		try {

			// $query = "SELECT sj.id, sj.title
			// 			FROM subject sj
			// 			WHERE sj.title IN ('Геометрия', 'Алгебра', 'Физика', 'Математикалық сауаттылық')
			// 			ORDER BY sj.title";
			$query = "SELECT sj.id, sj.title
						FROM subject sj
						WHERE sj.id IN (SELECT sc.subject_id
										FROM subject_configuration sc)
						ORDER BY sj.title";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topic_by_subject($subject_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT t.id, t.title, (count(st.id) - 2) AS subtopic_count
						FROM topic t,
							subtopic st
						WHERE t.subject_id = :subject_id
							AND st.topic_id = t.id
						GROUP BY t.id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":subject_id", $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title'], 'subtopic_count' => $value['subtopic_count']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}
?>