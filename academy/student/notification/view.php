<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');


	function get_not_submitted_tests() {
		GLOBAL $connect; 

		try {
			$student_id = $_SESSION['user_id'];
			
			$query = "SELECT gs.id AS group_student_id,
							gi.id AS group_info_id,
							sj.id AS subject_id,
							sj.title AS subject_title,
							t.id AS topic_id,
							t.title AS topic_title,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							mta.id AS material_test_action_id,
							mta.accessed_date AS material_accessed_date,
							lp.id AS lesson_progress_id
						FROM group_student gs,
							group_info gi,
							material_test_action mta,
							lesson_progress lp,
							subject sj,
							topic t,
							subtopic st
						WHERE gs.student_id = :student_id
							AND gs.status != 'inactive'
							AND gi.id = gs.group_info_id
							AND gi.is_archive = 0
							AND mta.group_student_id = gs.id
							AND mta.is_finish = 0
							AND lp.id = mta.lesson_progress_id
							AND sj.id = gi.subject_id
							AND t.id = gi.topic_id
							AND st.id = lp.subtopic_id
							AND DATE_FORMAT(NOW(), '%Y-%m-%d') > (SELECT fma.created_date
																	FROM forced_material_access fma
																	WHERE fma.lesson_progress_id = lp.id
																	ORDER BY fma.created_date DESC
																	LIMIT 1)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$topic_ids = array();

			foreach ($sql_result as $value) {
				array_push($topic_ids, $value['topic_id']);
			}

			$test_exists_result = array();
			if (count($topic_ids)) {
				$query = "SELECT t.id AS topic_id,
								st.id AS subtopic_id,
								(SELECT mt.upload_date
								FROM material_test mt
								WHERE mt.subtopic_id = st.id
								ORDER BY mt.upload_date DESC
								LIMIT 1) AS upload_date
							FROM topic t,
								subtopic st
							WHERE t.id IN (".implode(',', $topic_ids).")
								AND st.topic_id = t.id";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$test_exists_result = $stmt->fetchAll();
			}

			$is_test_exists = array();
			$test_upload_dates = array();

			foreach ($test_exists_result as $value) {
				if ($value['upload_date'] != null && $value['upload_date'] != '') {
					if (!isset($is_test_exists[$value['topic_id']])) {
						$is_test_exists[$value['topic_id']] = array();
					}
					array_push($is_test_exists[$value['topic_id']], $value['subtopic_id']);
					$test_upload_dates[$value['subtopic_id']] = $value['upload_date'];
				}
			}

			$result = array();

			foreach ($sql_result as $value) {
				if (in_array($value['subtopic_id'], $is_test_exists[$value['topic_id']])
				 && strtotime($test_upload_dates[$value['subtopic_id']]) <= strtotime($value['material_accessed_date'])) {


					if (!isset($result[$value['subject_id']])){
						$result[$value['subject_id']] = array('subject_title' => $value['subject_title'],
															'group_info_id' => $value['group_info_id'],
															'group_student_id' => $value['group_student_id'],
															'topic' => array());
					}
					if (!isset($result[$value['subject_id']]['topic'][$value['topic_id']])) {
						$result[$value['subject_id']]['topic'][$value['topic_id']] = array('topic_title' => $value['topic_title'],
																							'subtopic' => array());	
					}

					$result[$value['subject_id']]['topic'][$value['topic_id']]['subtopic'][$value['subtopic_id']]
																				= array('subtopic_title' => $value['subtopic_title'],
																						'material_test_action_id' => $value['material_test_action_id'],
																						'lesson_progress_id' => $value['lesson_progress_id']);
				}
			}

			return $result;

		} catch (Exception $e) {
			throw $e;	
		}
	}

	function get_submitted_tests() {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT mtr.id AS material_test_result_id, 
							sj.title AS subject_title,
							t.title AS topic_title,
							st.title AS subtopic_title,
							mtr.actual_result,
							mtr.total_result,
							mtr.subtopic_id AS subtopic_id,
							mtr.material_test_action_id,
							mtr.finish_date
						FROM group_student gs,
							material_test_action mta,
							material_test_result mtr,
							subtopic st,
							topic t,
							subject sj
						WHERE gs.student_id = :student_id
							AND gs.is_archive = 0
							AND mta.group_student_id = gs.id
							AND mta.is_finish = 1
							AND mtr.material_test_action_id = mta.id
							AND mtr.result_json IS NOT NULL
							AND DATE_FORMAT(DATE_ADD(mtr.finish_date, INTERVAL 7 DAY), '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
							AND st.id = mtr.subtopic_id
							AND t.id = st.topic_id
							AND sj.id = t.subject_id
						ORDER BY mtr.finish_date DESC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			
			$result = array();

			foreach ($query_result as $value) {
				$result[$value['material_test_result_id']] = array('subject_title' => $value['subject_title'],
																	'topic_title' => $value['topic_title'],
																	'subtopic_title' => $value['subtopic_title'],
																	'actual_result' => $value['actual_result'],
																	'total_result' => $value['total_result'],
																	'subtopic_id' => $value['subtopic_id'],
																	'finish_date' => $value['finish_date'],
																	'material_test_action_id' => $value['material_test_action_id']);
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_no_home_notification () {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT gsnhwn.id AS gsnhwn_id, 
							gsnhww.warning_count,
							gsnhwn.is_notified,
							gsnhwn.seen_date,
							sj.title AS subject_title
						FROM group_student_no_home_work_notification gsnhwn,
							group_student_no_home_work_warning gsnhww,
							group_student gs,
							group_info gi,
							subject sj
						WHERE gs.student_id = :student_id
							AND gsnhww.group_student_id = gs.id
							AND gsnhwn.group_student_no_home_work_warning_id = gsnhww.id
							AND (gsnhwn.seen_date >= DATE_SUB(NOW(), INTERVAL 1 DAY) OR gsnhwn.seen_date IS NULL)
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if ($value['seen_date'] == '') {
					$query = "UPDATE group_student_no_home_work_notification SET is_notified = 1, seen_date = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $value['gsnhwn_id'], PDO::PARAM_INT);
					$stmt->execute();
				}

				$text = "";
				if ($value['warning_count'] == 2) {
					$text = $value['subject_title']." пәнінен үй жұмысын ".$value['warning_count']." рет орындамаған үшін 'Армия' группасынан шығарылдың. Сұрақтарың болса бізге хабарлас: 8 777 389 00 99";
				} else {
					$text = $value['subject_title']." пәнінен үй жұмысын орындымаған үшін ".$value['warning_count']."-ші ескерту. Бір айда екі ескерту болса группадан шығаруға тура келеді";
				}
				$result[$value['gsnhwn_id']] = array('is_notified' => $value['is_notified'],
													'text' => $text);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;	
		}
	}

	function get_group_student_discount_notification () {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT dgs.id,
							d.title,
							d.for_month,
							d.amount,
							gi.group_name,
							sj.title AS subject_title,
							dgs.is_notified
						FROM discount_group_student dgs,
							group_student gs,
							discount d,
							group_info gi,
							subject sj
						WHERE gs.student_id = :student_id
							AND dgs.group_student_id = gs.id
							AND dgs.status = 'active'
							AND d.id = dgs.discount_id
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id
							AND (dgs.notified_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) OR dgs.notified_time IS NULL)";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			if ($stmt->rowCount() > 0) {
				$result = array('text' => 'Құттықтаймыз, Саған Altyn Bilim нің атынан жеңілдіктер берілді:<br>',
								'is_notified' => -1,
								'notification_text' => '<span style="font-size: 17px;">Құттықтаймыз, Саған Altyn Bilim-нің атынан жеңілдік(тер) берілді:<br><br>');
				foreach ($query_result as $value) {
					$query = "UPDATE discount_group_student SET is_notified = 1, notified_time = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $value['id'], PDO::PARAM_INT);
					$stmt->execute();

					$text = $value['subject_title'].' пәніне '.($value['for_month'] != -1 ? $value['for_month'].' айға ' : 'толық курсқа ').$value['amount'].'%<br>';
					$result['text'] .= $text;
					if ($value['is_notified'] == 0) {
						$result['notification_text'] .= $text;
						$result['is_notified'] = 0;
					}
				}
				$result['notification_text'] .= "</span>";
				return $result;
			}
			return array();

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_submitted_tests_count() {
		$submitted_tests = get_submitted_tests();
		return count($submitted_tests);
	}

	function get_not_submitted_tests_count() {
		$count = 0;

		$submitted_tests = get_not_submitted_tests();

		foreach ($submitted_tests as $subject) {
			foreach ($subject['topic'] as $topic) {
				foreach ($topic['subtopic'] as $subtopic) {
					$count++;
				}
			}
		}

		return $count;

	}

	function error_notification_count() {
		$count = 0;
		$count += get_not_submitted_tests_count();

		return $count;
	}

	function success_notification_count() {
		$count = 0;
		$count += get_submitted_tests_count();

		return $count;
	}

	if (isset($_GET['notifications_count'])) {
		$data['group_student_no_home_work_notification'] = get_group_student_no_home_notification();
		$data['error_notifications_count'] = error_notification_count();
		$data['error_notifications_count'] += count($data['group_student_no_home_work_notification']);

		$data['warning_notifications_count'] = 0;

		$data['group_student_discount_notification'] = get_group_student_discount_notification();
		$data['success_notifications_count'] = success_notification_count();
		$data['success_notifications_count'] += count($data['group_student_discount_notification']) > 0 ? 1 : 0;
		$data['success'] = true;

		echo json_encode($data);
	}
?>