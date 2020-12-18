<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');


	function get_discount_list () {
		GLOBAL $connect;

		try {

			$query = "SELECT d.id,
							d.title,
							d.type,
							d.amount, 
							d.for_month,
							d.cant_insert_promo_code
						FROM discount d
						ORDER BY d.created_date DESC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$addon = '';
				if ($value['type'] == 'percent') {
					$addon = '%';
				} else if ($value['type'] == 'money') {
					$addon = ' тг.';
				}

				$result[$value['id']] = array('title' => $value['title'],
														'type' => $value['type'],
														'amount' => $value['amount'],
														'for_month' => $value['for_month'],
														'text' => $value['amount'].$addon,
														'cant_insert_promo_code' => $value['cant_insert_promo_code']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_discount_group_student () {
		GLOBAL $connect;

		try {

			$query = "SELECT s.id AS student_id,
							s.last_name,
							s.first_name,
							s.phone,
							gi.id AS group_info_id,
							gs.id AS group_student_id,
							gi.group_name,
							dgs.used_count,
							d.title AS discount_title,
							d.for_month,
							dgs.id AS dgs_id
						FROM discount_group_student dgs,
							group_student gs,
							group_info gi,
							student s,
							discount d
						WHERE gs.id = dgs.group_student_id
							AND gi.id = gs.group_info_id
							AND s.id = gs.student_id
							AND d.id = dgs.discount_id
							AND dgs.status = 'active'";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if (!isset($result[$value['student_id']])) {
					$result[$value['student_id']] = array('last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'phone' => $value['phone'],
															'discounts' => array());
				}
				$result[$value['student_id']]['discounts'][$value['group_student_id']] = array('group_name' => $value['group_name'],
																					'is_army_group' => get_is_army_group($value['group_info_id']),
																					'is_marathon_group' => get_is_marathon_group($value['group_info_id']),
																					'discount_title' => $value['discount_title'],
																					'used_count' => $value['used_count'],
																					'for_month' => $value['for_month']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>