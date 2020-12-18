<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/controller_functions.php');
	include_once($root.'/common/global_controller.php');


	function get_students_no_payments() {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT gi.id AS group_info_id,
							gi.group_name,
							gs.id AS group_student_id,
							gi.start_date AS group_start_date,
							gi.subject_id,
							gs.start_from,
							sj.title AS subject_title,
							s.phone,
							s.email
						FROM group_info gi,
							group_student gs,
							subject sj,
							student s
						WHERE s.id = :student_id
							AND gs.student_id = s.id
							AND gi.is_archive = 0
							AND gs.is_archive = 0
							AND gs.status = 'inactive'
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id
						ORDER BY sj.title, gi.start_date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();

			$query_result = $stmt->fetchAll();

			$result =  array('payment_infos' => array(),
							'student_info' => array());

			foreach ($query_result as $value) {
				$subject_topics_order = array();
				if (!isset($subject_topics_order[$value['subject_id']])) {
					$subjects_order[$value['subject_id']] = get_subtopics_total_order($value['subject_id']);
				}
				$payment = get_next_payment_price($value['group_info_id'], $value['group_student_id'],
															$subjects_order[$value['subject_id']], $value['group_start_date'], $value['start_from']);

				$result['payment_infos'][$value['group_info_id']] = array('group_name' => $value['group_name'],
														'subject_title' => $value['subject_title'],
														'group_student_id' => $value['group_student_id'],
														'payment' => $payment);
				if (count($result['student_info']) == 0) {
					$result['student_info'] = array('phone' => $value['phone'],
													'email' => $value['email']);
				}
			}

			return $result;	
		} catch (Exception $e) {
			return array();
		}
	}

?>