<?php
	include_once('../common/connection.php');
	include_once('../send_sms/index.php');
	include_once('../send_sms/sms_statuses.php');

	// $army_group_students = get_army_group_students();
	$group_students = get_not_army_group_students();
	$student_lesson_infos = get_student_lesson_infos($group_students);

	// echo json_encode($group_students, JSON_UNESCAPED_UNICODE);
	// echo "<br></br>";
	// echo json_encode($student_lesson_infos, JSON_UNESCAPED_UNICODE);
	// echo "<br><br>";

	foreach ($student_lesson_infos as $student_id => $value) {
		$fio = $value['last_name'].' '.$value['first_name'];
		$recipient = '7'.$value['phone'];
		
		$text = "";
		$parent_text = "Балаңыз ";
		if (!$value['has_test_done'])  {
			if (!$value['has_tutorial_video_watched']) {
				$text = "Өткен тақырыптың видеосабағын көрмепсің және тест жұмысын орындамапсың. Орындау міндетті. ";
				$parent_text = " кешегі тақырыптың видеосабағын көрмепті және тест жұмысын орындамады. Қадағалауыңызды сұраймыз. ";
			} else {
				$text = "Өткен тақырыптың тест жұмысын орындамапсың. Тестті орындау міндетті. ";
				$parent_text = " кешегі тақырыптың тест жұмысын орындамады. Қадағалауыңызды сұраймыз. ";
			}
		}
		if ($text != '') {
			$text .= $end_text;
			$text = kiril2latin($text);

			$parent_text .= $end_text;
			$parent_text = kiril2latin($parent_text);

			$recipient_info = array('recipient' => $recipient,
									'text' => $text);
			$sms_response = send_sms($recipient_info, $fio);
			save_to_db($sms_response);

			if ($value['parent_phone'] != '' && $value['parent_phone'] != 0) {
				$fio = $value['last_name'].' '.$value['first_name'].' оқушының ата-анасы';
				$recipient = '7'.$value['parent_phone'];
				// tmp_send_sms_save($recipient, $parent_text, $fio);
				$recipient_info = array('recipient' => $recipient,
										'text' => $parent_text);
				$sms_response = send_sms($recipient_info, $fio);
				save_to_db($sms_response);
			}
		}
	}

	function tmp_send_sms_save ($recipient, $text, $fio) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO send_sms_tmp (recipient, text, fio)
											VALUES (:recipient, :text, :fio)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':recipient', $recipient, PDO::PARAM_STR);
			$stmt->bindParam(':text', $text, PDO::PARAM_STR);
			$stmt->bindParam(':fio', $fio, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	// function save_to_db ($sms_response) {
	// 	GLOBAL $connect;
	// 	GLOBAL $ACCEPTED;
	// 	GLOBAL $NEW;
	// 	try {
	// 		$status = $sms_response['status'] == "2" ? $ACCEPTED : $NEW;
	// 		$query = "INSERT INTO sms_history (message_id, sms_response_code, to_name, to_phone, sms_text, status, sms_response, sent_time)
	// 									VALUES (:message_id, :sms_response_code, :to_name, :to_phone, :sms_text, :status, :sms_response, :sent_time);";
	// 		$stmt = $connect->prepare($query);
	// 		$stmt->bindParam(':message_id', $sms_response['message_id'], PDO::PARAM_INT);
	// 		$stmt->bindParam(':sms_response_code', $sms_response['response_code'], PDO::PARAM_INT);
	// 		$stmt->bindParam(':to_name', $sms_response['to_name'], PDO::PARAM_STR);
	// 		$stmt->bindParam(':to_phone', $sms_response['to_phone'], PDO::PARAM_STR);
	// 		$stmt->bindParam(':sms_text', $sms_response['sms_text'], PDO::PARAM_STR);
	// 		$stmt->bindParam(':status', $status, PDO::PARAM_INT);
	// 		$stmt->bindParam(':sms_response', $sms_response['sms_response'], PDO::PARAM_STR);
	// 		$stmt->bindParam(':sent_time', $sms_response['sent_time'], PDO::PARAM_STR);
	// 		$stmt->execute();
			
	// 	} catch (Exception $e) {
	// 		throw $e;
	// 	}
	// }

	function get_student_lesson_infos ($army_group_students) {
		GLOBAL $connect;

		try {
			
			$tmp_result = array();

			foreach ($army_group_students as $group_student_id => $value) {
				if (!isset($tmp_result[$value['student_id']])) {
					$tmp_result[$value['student_id']] = array('last_name' => $value['last_name'],
																'first_name' => $value['first_name'],
																'phone' => $value['phone'],
																'parent_phone' => $value['parent_phone'],
																'has_test_done' => true,
																'has_class_work_submitted' => true,
																'has_tutorial_video_watched' => true);
				}
				if ($tmp_result[$value['student_id']]['has_test_done']) {
					$tmp_result[$value['student_id']]['has_test_done'] = get_material_test_result($group_student_id);
				}
				if ($tmp_result[$value['student_id']]['has_tutorial_video_watched']) {
					$tmp_result[$value['student_id']]['has_tutorial_video_watched'] = get_tutorial_video_watched($group_student_id);
				}
				// if ($tmp_result[$value['student_id']]['has_class_work_submitted']) {
				// 	$tmp_result[$value['student_id']]['has_class_work_submitted'] = get_class_work_submittion($group_student_id);
				// }

			}

			$result = array();
			foreach ($tmp_result as $student_id => $value) {
				if (!$value['has_test_done'] || !$value['has_tutorial_video_watched']) {
					$result[$student_id] = $value;
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_result ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id AS lesson_progress_id,
							mta.id AS material_test_action_id
						FROM group_student gs,
							lesson_progress lp,
							material_test_action mta,
							subtopic st
						WHERE gs.id = :group_student_id
							AND lp.group_info_id = gs.group_info_id
							AND DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
							AND st.id = lp.subtopic_id
							AND 0 < (SELECT count(mt.id)
									FROM material_test mt
									WHERE mt.subtopic_id = st.id)
							AND mta.group_student_id = gs.id
							AND mta.lesson_progress_id = lp.id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$lesson_progress_info = $stmt->fetch(PDO::FETCH_ASSOC);

				$query = "SELECT mtr.actual_result
							FROM material_test_result mtr
							WHERE mtr.material_test_action_id = :material_test_action_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':material_test_action_id', $lesson_progress_info['material_test_action_id'], PDO::PARAM_INT);
				$stmt->execute();

				$row_count = $stmt->rowCount();

				if ($row_count == 0) {
					return false;
				}

				$mtr_info = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($mtr_info['actual_result'] == '') {
					return false;
				}
				return true;
			}

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}

	}

	function get_tutorial_video_watched ($group_student_id) {
		GLOBAL $connect;

		try {
			
			$query = "SELECT lp.id,
							lp.subtopic_id
						FROM lesson_progress lp,
							group_student gs,
							group_info gi
						WHERE DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
							AND lp.group_info_id = gs.group_info_id
							AND gs.id = :group_student_id
							AND 1 != (SELECT st.subtopic_order
										FROM subtopic st
										WHERE st.id = lp.subtopic_id)
							AND gi.id = gs.group_info_id
							AND (SELECT st.subtopic_order 
								FROM subtopic st
								WHERE st.topic_id = gi.topic_id
								ORDER BY st.subtopic_order DESC
								LIMIT 1) != (SELECT st.subtopic_order
												FROM subtopic st
												WHERE st.id = lp.subtopic_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$lesson_progress_query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "SELECT tva.id
						FROM tutorial_video_action tva
						WHERE tva.lesson_progress_id = :lesson_progress_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_query_result['id'], PDO::PARAM_INT);
			$stmt->execute();
			$tva_query_results = $stmt->fetchAll();

			$tva_ids = array();
			foreach ($tva_query_results as $value) {
				array_push($tva_ids, $value['id']);
			}

			$query = "SELECT count(tv.id) AS tv_count
						FROM tutorial_video tv
						WHERE tv.subtopic_id = :subtopic_id
							AND tv.pop_up = 0";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $lesson_progress_query_result['subtopic_id'], PDO::PARAM_INT);
			$stmt->execute();
			$tv_count = $stmt->fetch(PDO::FETCH_ASSOC)['tv_count'];

			if (count($tva_ids) > 0) {
				$query = "SELECT DISTINCT tval.id
							FROM tutorial_video_action_log tval,
								tutorial_video tv
							WHERE tval.tutorial_video_action_id IN (".implode(',', $tva_ids).")
								AND tv.id = tval.tutorial_video_id
								AND tv.pop_up = 0";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$tval_query_result = $stmt->fetchAll();
				$tval_count = $stmt->rowCount();

				return $tval_count == $tv_count ? true : false;
			}

			return true;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_class_work_submittion ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id AS lesson_progress_id,
							tda.id AS tutorial_document_action_id
						FROM group_student gs, 
							lesson_progress lp,
							tutorial_document_action tda,
							subtopic st
						WHERE gs.id = :group_student_id
							AND lp.group_info_id = gs.group_info_id
							AND DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
							AND tda.lesson_progress_id = lp.id
							AND st.id = lp.subtopic_id
							AND st.subtopic_order != 1
							AND st.subtopic_order != (SELECT st2.subtopic_order
														FROM subtopic st2
														ORDER BY st2.subtopic_order DESC
														LIMIT 1)
							AND tda.group_student_id = gs.id
						ORDER BY tda.accessed_date ASC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$lesson_progress_info = $stmt->fetch(PDO::FETCH_ASSOC);

				$query = "SELECT (SELECT count(gscwsf.id)
								FROM group_student_class_work_submit_files gscwsf
								WHERE gscwsf.group_student_class_work_submit_id = gscws.id) AS gscwsf_count
							FROM group_student_class_work_submit gscws
							WHERE gscws.tutorial_document_action_id = :tutorial_document_action_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':tutorial_document_action_id', $lesson_progress_info['tutorial_document_action_id'], PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if ($row_count == 0) {
					return false;
				}

				$gscws_info = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($gscws_info['gscwsf_count'] == 0) {
					return false;
				}

				return true;
			}

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_video_action_log ($group_student_id) { // depreciated
		GLOBAL $connect;

		try {
			$query = "SELECT tva.id AS tutorial_video_action_id,
							(SELECT count(tv.id)
							FROM tutorial_video tv
							WHERE tv.pop_up = 0
								AND tv.subtopic_id = lp.subtopic_id) tutorial_video_count
						FROM tutorial_video_action tva,
							lesson_progress lp
						WHERE tva.group_student_id = :group_student_id
							AND tva.forced_material_access_id IS NULL
							AND lp.id = tva.lesson_progress_id
							AND DATE_FORMAT(DATE_ADD(tva.accessed_date, INTERVAL 1 DAY), '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						ORDER BY tva.accessed_date DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$query_action_result = $stmt->fetch(PDO::FETCH_ASSOC);

				$query = "SELECT count(tval.id) AS tutorial_video_action_log_count
							FROM tutorial_video_action_log tval,
								tutorial_video tv
							WHERE tval.tutorial_video_action_id = :tutorial_video_action_id
								AND tv.id = tval.tutorial_video_id
								AND tv.pop_up = 0";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':tutorial_video_action_id', $query_action_result['tutorial_video_action_id'], PDO::PARAM_INT);
				$stmt->execute();
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($query_result['tutorial_video_action_log_count'] != $query_action_result['tutorial_video_count']) {
					return false;
				}
			}
			return true;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_not_army_group_students () {
		GLOBAL $connect;

		try {

			$query = "SELECT s.id AS student_id,
							s.phone,
							s.last_name,
							s.first_name,
							s.parent_phone,
							gs.id AS group_student_id,
							gi.id AS group_info_id
						FROM group_info gi,
							group_student gs,
							student s
						WHERE gi.is_archive = 0
							AND gi.id NOT IN (SELECT ag.group_info_id
												FROM army_group ag)
							AND gi.id NOT IN (SELECT sg.group_info_id
												FROM school_group sg)
							AND gs.group_info_id = gi.id
							AND gs.status = 'active'
							AND s.id = gs.student_id
							AND (gs.transfer_from_group IS NOT NULL 
								OR 0 > (SELECT count(gsp.id)
										FROM group_student_payment gsp
										WHERE gsp.group_student_id = gs.id
											AND gsp.payment_type = 'money'))
							AND s.phone NOT IN (7771234567)";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['group_student_id']] = array('student_id' => $value['student_id'],
															'phone' => $value['phone'],
															'parent_phone' => $value['parent_phone'],
															'last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'group_info_id' => $value['group_info_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_army_group_students () { //depreciated
		GLOBAL $connect;

		try {

			$query = "SELECT s.id AS student_id,
							s.phone,
							s.last_name,
							s.first_name,
							gs.id AS group_student_id,
							gi.id AS group_info_id
						FROM group_info gi,
							group_student gs,
							student s
						WHERE gi.is_archive = 0
							AND gi.id IN (SELECT ag.group_info_id
											FROM army_group ag)
							AND gi.id NOT IN (SELECT sg.group_info_id
												FROM school_group sg)
							AND gs.group_info_id = gi.id
							AND gs.status = 'active'
							AND s.id = gs.student_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['group_student_id']] = array('student_id' => $value['student_id'],
															'phone' => $value['phone'],
															'last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'group_info_id' => $value['group_info_id']);
			}
			
			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}
?>