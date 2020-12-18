<?php
	include_once('../common/connection.php');
	include_once('../send_sms/index.php');
	include_once('../send_sms/sms_statuses.php');

	$group_students = get_group_students_with_recently_started();
	print_r($group_students);
	echo "<br><br>";

	foreach ($group_students as $group_student_id => $value) {
		$text = "";
		$parent_text = "Балаңыздың ";
		if (count($value['subjects']) == 1) {
			$text .= $value['subjects'][0].' пәнінен алғашқы сабақ ашық тұр. ';
			$parent_text .= $value['subjects'][0].' пәнінен алғашқы сабағы ашық тұр. ';

		} else if (count($value['subjects']) > 1) {
			$text .= implode(', ', $value['subjects']);
			$text .= " пәндерінен алғашқы сабақ ашық тұр. ";
			$parent_text .= implode(', ', $value['subjects'])." пәндерінен алғашқы сабағы ашық тұр. ";

		}
		$text .= 'Жеке кабинетіңе кіріп көре берсең болады. ';
		$text .= $end_text;
		$text = kiril2latin($text);

		$parent_text .= 'Сайтымыздағы жеке кабинетіне кіріп көре берсе болады. ';
		$parent_text .= $end_text;
		$parent_text = kiril2latin($parent_text);

		$fio = $value['last_name'].' '.$value['first_name'];
		$recipient = '7'.$value['phone'];

		$recipient_info = array('recipient' => $recipient,
								'text' => $text);
		$sms_response = send_sms($recipient_info, $fio);
		save_to_db($sms_response);

		if ($value['parent_phone'] != '' && $value['parent_phone'] != 0) {

			$fio = $value['last_name'].' '.$value['first_name'].' оқушының ата-анасы';
			$recipient = '7'.$value['phone'];
			$recipient_info = array('recipient' => $recipient,
									'text' => $parent_text);
			// tmp_send_sms_save($recipient, $parent_text, $fio);
			$sms_response = send_sms($recipient_info, $fio);
			save_to_db($sms_response);
		}

		// tmp_send_sms_save($recipient, $text, $fio);
		// print_r($recipient);
		// echo "<br>";
		// print_r($text);
		// echo "<br>";
		// print_r($fio);
		// echo "<br><br>";
	}

	// $recipient_info = array('recipient' => '77476445460',
	// 						'text' => kiril2latin("SMS сәтті жіберілді. ".$end_text));
	// print_r($recipient_info);
	// echo "<br><br>";

	// $sms_response = send_sms($recipient_info, "Мырзабек Алмат");

	// print_r($sms_response);
	// echo "<br><br>";
	// echo json_encode($sms_response, JSON_UNESCAPED_UNICODE);
	// echo "<br><br>";

	// save_to_db($sms_response);

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

	function save_to_db ($sms_response) {
		GLOBAL $connect;
		GLOBAL $ACCEPTED;
		GLOBAL $NEW;
		try {
			$status = $sms_response['status'] == "2" ? $ACCEPTED : $NEW;
			$query = "INSERT INTO sms_history (message_id, sms_response_code, to_name, to_phone, sms_text, status, sms_response, sent_time)
										VALUES (:message_id, :sms_response_code, :to_name, :to_phone, :sms_text, :status, :sms_response, :sent_time);";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':message_id', $sms_response['message_id'], PDO::PARAM_INT);
			$stmt->bindParam(':sms_response_code', $sms_response['response_code'], PDO::PARAM_INT);
			$stmt->bindParam(':to_name', $sms_response['to_name'], PDO::PARAM_STR);
			$stmt->bindParam(':to_phone', $sms_response['to_phone'], PDO::PARAM_STR);
			$stmt->bindParam(':sms_text', $sms_response['sms_text'], PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_INT);
			$stmt->bindParam(':sms_response', $sms_response['sms_response'], PDO::PARAM_STR);
			$stmt->bindParam(':sent_time', $sms_response['sent_time'], PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_group_students_with_recently_started () {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.id AS group_student_id,
							gs.group_info_id,
							s.id AS student_id,
							s.last_name, 
							s.first_name,
							s.phone,
							s.parent_phone,
							sj.title AS subject_title
						FROM group_student gs,
							group_info gi,
							student s,
							subject sj
						WHERE gs.transfer_from_group IS NULL
							AND gs.is_archive = 0
							AND gi.id = gs.group_info_id
							AND gi.start_date <= DATE_FORMAT(NOW(), '%Y-%m-%d')
							AND 0 < (SELECT count(gsp.id)
										FROM group_student_payment gsp
										WHERE gsp.group_student_id = gs.id
											AND gsp.payment_type = 'money')
							AND gi.id NOT IN (SELECT ag.group_info_id
												FROM army_group ag)
							AND s.id = gs.student_id
							AND sj.id = gi.subject_id
							AND s.phone NOT IN (7771234567)";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			$result = array();

			foreach ($query_result as $value) {
				$has_lesson_access = get_first_lesson_progress_access($value['group_info_id']);
				echo ($has_lesson_access ? 'true' : 'false').$value['group_info_id']."<br>";
				if ($has_lesson_access) {
					if (!isset($result[$value['student_id']])) {
						$result[$value['student_id']] = array('last_name' => $value['last_name'],
																'first_name' => $value['first_name'],
																'phone' => $value['phone'],
																'parent_phone' => $value['parent_phone'],
																'subjects' => array());
					}
					array_push($result[$value['student_id']]['subjects'], $value['subject_title']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_first_lesson_progress_access ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(lp.id) AS lp_count
						FROM lesson_progress lp
						WHERE lp.group_info_id = :group_info_id
							AND DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
							AND 2 = (SELECT count(lp2.id)
									FROM lesson_progress lp2
									WHERE lp2.group_info_id = lp.group_info_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$lp_count = $stmt->fetch(PDO::FETCH_ASSOC)['lp_count'];

			return $lp_count == 2 ? true : false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>