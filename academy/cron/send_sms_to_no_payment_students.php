<?php
	include_once('../common/connection.php');
	include_once('../send_sms/index.php');
	include_once('../send_sms/sms_statuses.php');

	$no_payment_students = get_no_payment_students();

	foreach ($no_payment_students as $student_id => $student) {
		$text = "";
		$parent_text = "Балаңыздың ";
		$fio = $student['last_name'].' '.$student['first_name'];
		$recipient = '7'.$student['phone'];

		$subjects_arr = array();
		foreach ($student['subjects'] as $subject_id => $subject) {
			array_push($subjects_arr, $subject['subject_title']);
		}

		$parent_text .= 'Балаңыздың ';
		if (count($subjects_arr) == 1) {
			$text .= implode(', ', $subjects_arr).' пәнінен ';
			$parent_text .= implode(', ', $subjects_arr).' пәнінен ';
		} else {
			$text .= implode(', ', $subjects_arr).' пәндерінен ';
			$parent_text .= implode(', ', $subjects_arr).' пәндерінен ';
		}

		$parent_text .= "оқу төлемі аяқталды. Оқуды жалғастыру үшін төлем жасау керек. ".$end_text;
		$parent_text = kiril2latin($parent_text);

		$text .= "оқу төлемі аяқталды. Оқуды жалғастыру үшін төлем жасау керек. ".$end_text;
		$text = kiril2latin($text);
		$recipient_info = array('recipient' => $recipient,
									'text' => $text);
		// tmp_send_sms_save($recipient, $text, $fio);
		$sms_response = send_sms($recipient_info, $fio);
		save_to_db($sms_response);

		if ($value['parent_phone'] != '' && $value['parent_text'] != 0) {
			$fio = $student['last_name'].' '.$student['first_name'].' оқушының ата-анасы';
			$recipient = '7'.$value['parent_phone'];
			// tmp_send_sms_save($recipient, $parent_text, $fio);
			$recipient_info = array('recipient' => $recipient,
										'text' => $parent_text);
			$sms_response = send_sms($recipient_info, $fio);
			save_to_db($sms_response);
		}
	}

	// echo json_encode($no_payment_students, JSON_UNESCAPED_UNICODE);


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

	function get_no_payment_students () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id,
							gi.group_name,
							s.last_name,
							s.first_name,
							sj.id AS subject_id,
							s.phone,
							s.parent_phone,
							sj.title AS subject_title,
							s.id AS student_id
						FROM group_student_payment gsp,
							group_student gs,
							student s,
							group_info gi,
							subject sj
						WHERE DATE_FORMAT(gsp.finished_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
							AND gs.id = gsp.group_student_id
							AND gi.id = gs.group_info_id
							AND s.id = gs.student_id
							AND sj.id = gi.subject_id
							AND 0 < (SELECT count(gsp2.id)
									FROM group_student_payment gsp2
									WHERE gsp2.group_student_id IN (SELECT gs2.id 
																	FROM group_student gs2
																	WHERE gs2.student_id = gs.student_id))
							AND s.phone NOT IN (7771234567)";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['student_id']])) {
					$result[$value['student_id']] = array('last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'phone' => $value['phone'],
															'parent_phone' => $value['parent_phone'],
															'subjects' => array());
				}
				if (!isset($result[$value['student_id']]['subjects'][$value['subject_id']])) {
					$result[$value['student_id']]['subjects'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																							'groups' => array());
				}
				$result[$value['student_id']]['subjects'][$value['subject_id']]['groups'][$value['group_info_id']] = array('group_name' => $value['group_name']);
			}
			return $result;
				
		} catch (Exception $e) {
			throw $e;
		}
	}
?>