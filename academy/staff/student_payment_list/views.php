<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_student_payment_list($date) {
		GLOBAL $connect;

		try {
			$date = $date.'-'.date('d');
			$query = "SELECT gsp.id AS group_student_payment_id,
							gi.id AS group_info_id,
							s.last_name,
							s.first_name,
							s.phone,
							gi.group_name,
							sj.title,
							DATE_FORMAT(gsp.payed_date, '%d.%m.%Y %H:%i:%s') AS payed_date,
							DATE_FORMAT(gsp.payed_date, '%d') AS payed_day,
							gsp.partial_payment_days,
							spl.amount AS payed_amount
						FROM group_student_payment gsp
						INNER JOIN group_student gs
							ON gs.id = gsp.group_student_id
						INNER JOIN group_info gi
							ON gi.id = gs.group_info_id
						INNER JOIN student s
							ON s.id = gs.student_id
								AND s.id NOT IN (182)
						INNER JOIN subject sj
							ON sj.id = gi.subject_id
						LEFT JOIN student_payment_log spl
							ON spl.group_student_payment_id = gsp.id
						WHERE gsp.payment_type = 'money'
							AND DATE_FORMAT(gsp.payed_date, '%Y-%m') = DATE_FORMAT(:date, '%Y-%m')
						ORDER BY gsp.payed_date DESC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('data' => array(),
							'year' => date('Y', strtotime($date)),
							'month' => date('m', strtotime($date)));

			foreach ($query_result as $value) {
				if (!isset($result['data'][$value['payed_day']])) {
					$result['data'][$value['payed_day']] = array();
				}

				$result['data'][$value['payed_day']][$value['group_student_payment_id']]
																		= array('last_name' => $value['last_name'],
																				'first_name' => $value['first_name'],
																				'phone' => $value['phone'],
																				'group_name' => $value['group_name'],
																				'subject_title' => $value['title'],
																				'payed_date' => $value['payed_date'],
																				'is_army_group' => get_is_army_group($value['group_info_id']),
																				'partial_payment_days' => $value['partial_payment_days'],
																				'payed_amount' => $value['payed_amount']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>