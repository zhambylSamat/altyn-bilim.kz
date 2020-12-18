	<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	$payment_info = json_decode($_POST['payment_info'], true);
	$payment_result = json_encode($_REQUEST);
	// $payment_info = json_decode($_POST['payment_info'], true);

	// $payment_info = json_decode('{"groups":{"1050":{"amount":6990,"partial_payment_days":null}},"order_id":"1598416185182"}', true);
	// $payment_result = '{"pg_order_id":"1601031817182","pg_payment_id":"334653643","pg_amount":"990.00","pg_currency":"KZT","pg_net_amount":"961.29","pg_ps_amount":"990.00","pg_ps_full_amount":"990.00","pg_ps_currency":"KZT","pg_payment_system":"EPAYWEBKZT","pg_description":"990,00 \u0442\u0435\u04a3\u0433\u0435","pg_result":"1","pg_payment_date":"2020-09-25 17:04:10","pg_can_reject":"0","pg_user_phone":"77074105268","pg_need_phone_notification":"0","pg_user_contact_email":"zhambyl.9670@gmail.com","pg_need_email_notification":"1","pg_captured":"1","payment_info":"{\"groups\":{\"2095\":{\"amount\":6990,\"amount_with_discount\":990,\"discount_group_student_id\":\"2\",\"partial_payment_days\":null}},\"order_id\":\"1601031817182\"}","pg_card_pan":"5169-49XX-XXXX-8979","pg_card_exp":"12\/20","pg_card_owner":"almat myrzabek","pg_auth_code":"432496","pg_card_brand":"MC","pg_salt":"WHLH18KMoPl6X0d7","pg_sig":"68d8c8f852ac0c9dcfe376a101fb0400"}';

	// $payment_result = '{"pg_order_id":"1599823199290","pg_payment_id":"317468113","pg_amount":"6990.00","pg_currency":"KZT","pg_net_amount":"6787.29","pg_ps_amount":"6990.00","pg_ps_full_amount":"6990.00","pg_ps_currency":"KZT","pg_payment_system":"EPAYWEBKZT","pg_description":"6 990,00 \u0442\u0435\u04a3\u0433\u0435","pg_result":"1","pg_payment_date":"2020-09-11 17:21:45","pg_can_reject":"0","pg_user_phone":"77768472079","pg_need_phone_notification":"0","pg_user_contact_email":"dias200003@icloud.com","pg_need_email_notification":"1","pg_captured":"1","payment_info":"{\"groups\":{\"1312\":{\"amount\":6990,\"amount_with_discount\":6990,\"discount_group_student_id\":6,\"partial_payment_days\":null}},\"order_id\":\"1599823199290\"}","pg_card_pan":"4400-43XX-XXXX-2446","pg_card_exp":"01\/23","pg_card_owner":"Dias Junisbek","pg_auth_code":"942597","pg_card_brand":"VI","pg_salt":"YxoOP8lbHEcLTuJN","pg_sig":"2c771849b6d8fa605968ca9eab99417f"}';
	// $payment_info = json_decode('{"groups":{"2062":{"amount":6990,"amount_with_discount":6990,"discount_group_student_id":"","partial_payment_days":null},"2097":{"amount":6990,"amount_with_discount":1398,"discount_group_student_id":"1","partial_payment_days":null}},"order_id":"1601031924182"}', true);

	echo $payment_result;
	echo "<br><br>";
	print_r($payment_info);
	echo "<br><br>";
	echo json_encode($payment_info);
	echo "<br><br>";

	$date_time = '';

	if (date('H') >= 0 && date('H') < 7) {
		$date_time = date('Y-m-d').' 07:00:00';
		$date_time = date('Y-m-d H:i:s', strtotime($date_time.' + 1 days'));
	} else {
		$date_time = date('Y-m-d').' 07:00:00';
	}

	$order_id = $payment_info['oid'];
	if ($_POST['pg_result'] == '1') {
		foreach ($payment_info['g'] as $group_student_id => $value) {
			$amount = $value['1'];
			$discount_group_student_id = $value['3'];
			$partial_payment_days = $value['4'];
			$supc_ids = $value['supc_ids'];

			$last_learned_date = get_last_learn_date($group_student_id);
			echo $last_learned_date."<br><br>";

			if ($last_learned_date != '') {
				$new_group_student_payment_id = insert_group_student_payment($group_student_id, $last_learned_date, $partial_payment_days);

				if ($new_group_student_payment_id != '') {
					update_group_student_status_to_active($group_student_id);
					$lesson_progresses = get_lesson_progresses_by_last_learned_date($group_student_id, $last_learned_date);
					if (count($lesson_progresses['for_today']) > 0) {
						set_tutorial_video_actions_by_lp_id($group_student_id, $lesson_progresses['for_today'], $date_time);
						set_tutorial_document_actions_by_lp_id($group_student_id, $lesson_progresses['for_today'], $date_time);
						set_end_video_actions_by_lp_id($group_student_id, $lesson_progresses['for_today'], $date_time);
						set_material_test_actions_by_lp_id($group_student_id, $lesson_progresses['for_today'], $date_time);
					}
					if (count($lesson_progresses['for_prev_lessons']) > 0) {
						$forced_material_access_ids = set_forced_material_access($group_student_id, $lesson_progresses['for_prev_lessons'], $date_time);

						if (count($forced_material_access_ids) > 0) {
							set_tutorial_video_actions_by_fma_id($group_student_id, $forced_material_access_ids, $date_time);
							set_tutorial_document_actions_by_fma_id($group_student_id, $forced_material_access_ids, $date_time);
							set_end_video_actions_by_fma_id($group_student_id, $forced_material_access_ids, $date_time);
							set_material_test_actions_by_fma_id($group_student_id, $forced_material_access_ids, $date_time);
						}
					}

					insert_student_payment_log($order_id, $group_student_id, $new_group_student_payment_id, $amount, $payment_result);

					if ($discount_group_student_id != '') {
						set_discount_group_student_use($discount_group_student_id, $group_student_id);
					}				
				}
			} else {
				$new_group_start_date = get_new_group_start_date($group_student_id);

				if ($new_group_start_date != '') {
					$new_group_student_payment_id = insert_group_student_waiting_payment($group_student_id, $new_group_start_date, $partial_payment_days);
					if ($new_group_student_payment_id != '') {
						update_group_student_status_to_waitnig($group_student_id);
					}
					
					insert_student_payment_log($order_id, $group_student_id, $new_group_student_payment_id, $amount, $payment_result);
				}
			}
			if ($discount_group_student_id != '') {
				set_discount_group_student_use($discount_group_student_id, $group_student_id);
			}
			if (count($value['supc_ids']) > 0) {
				set_promo_code_use($value['supc_ids'], $group_student_id);
			}
		}
		$query = "INSERT INTO test_text (text) VALUES (:text)";
		$stmt = $connect->prepare($query);
		$stmt->bindValue(':text', $payment_result, PDO::PARAM_STR);
		$stmt->execute();
	} else {
		$query = "INSERT INTO test_text (text) VALUES (:text)";
		$stmt = $connect->prepare($query);
		$stmt->bindValue(':text', $payment_result, PDO::PARAM_STR);
		$stmt->execute();
	}

	function set_promo_code_use($supc_ids, $group_student_id) {
		GLOBAL $connect;

		try {

			$query_student_id = "SELECT gs.student_id,
										gs.group_info_id
								FROM group_student gs
								WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query_student_id);
			$stmt->bindParam(':group_student_id', $group_student_id,  PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$student_id = $query_result['student_id'];
			$group_info_id = $query_result['group_info_id'];


			$query = "SELECT supc.id
						FROM student_used_promo_code supc
						WHERE supc.student_id = :student_id
							AND supc.is_used = 0";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$self_supc_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			$supc_id = '';
			if (in_array($self_supc_id, $supc_ids)) {
				$supc_id = $self_supc_id;
			}

			if ($supc_id != '') {
				$query = "UPDATE student_used_promo_code SET is_used = 1 WHERE id = :id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $supc_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			$query = "INSERT INTO student_promo_code_log (student_id, student_used_promo_code_id, group_info_id)
													VALUES (:student_id, :student_used_promo_code_id, :group_info_id)";
			foreach ($supc_ids as $student_used_promo_code_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':student_used_promo_code_id', $student_used_promo_code_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_discount_group_student_use ($discount_group_student_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT dgs.used_count,
							d.for_month
						FROM discount_group_student dgs,
							discount d
						WHERE dgs.id = :discount_group_student_id
							AND d.id = dgs.discount_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':discount_group_student_id', $discount_group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$used_count = intval($query_result['used_count']);
			$for_month = intval($query_result['for_month']);
			$status = 'active';

			if ($for_month != -1) {
				$used_count++;
				if ($used_count == $for_month) {
					$status = 'inactive';
				}
			}

			$query = "UPDATE discount_group_student SET used_count = :used_count,
														status = :status,
														group_student_id = :group_student_id
													WHERE id = :discount_group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':used_count', $used_count, PDO::PARAM_INT);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':discount_group_student_id', $discount_group_student_id, PDO::PARAM_INT);
			$stmt->execute();

		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_test($text) {
		GLOBAL $connect;

		try {
			$query = "INSERT INTO payment_test (test_test) VALUES(:test_text)";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':test_text', $text, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_student_payment_log ($order_id, $group_student_id, $group_student_payment_id, $amount, $payment_result) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO student_payment_log (order_id, group_student_id, group_student_payment_id, amount, payment_result)
											VALUES (:order_id, :group_student_id, :group_student_payment_id, :amount, :payment_result)";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_payment_id', $group_student_payment_id, PDO::PARAM_INT);
			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
			$stmt->bindParam(':payment_result', $payment_result, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_material_test_actions_by_lp_id ($group_student_id, $lesson_progresses_id, $date_time) {
		GLOBAL $connect;

		try {
			$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, accessed_date)
												VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";
			foreach ($lesson_progresses_id as $lesson_progress_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_material_test_actions_by_fma_id ($group_student_id, $forced_material_access_ids, $date_time) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";

			foreach ($forced_material_access_ids as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_end_video_actions_by_lp_id ($group_student_id, $lesson_progresses_id, $date_time) {
		GLOBAL $connect;

		try {
			$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";

			foreach ($lesson_progresses_id as $lesson_progress_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_end_video_actions_by_fma_id ($group_student_id, $forced_material_access_ids, $date_time) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id, :accessed_date)";

			foreach ($forced_material_access_ids as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':forced_material_access_id', $value['forced_material_access_id'], PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_document_actions_by_lp_id ($group_student_id, $lesson_progresses_id, $date_time) {
		GLOBAL $connect;

		try {
			
			$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, accessed_date)
													VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";

			foreach ($lesson_progresses_id as $lesson_progress_id) {

				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();

			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_document_actions_by_fma_id ($group_student_id, $forced_material_access_ids, $date_time) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id, :accessed_date)";

			foreach ($forced_material_access_ids as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':forced_material_access_id', $value['forced_material_access_id'], PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_video_actions_by_lp_id ($group_student_id, $lesson_progresses_id, $date_time) {
		GLOBAL $connect;

		try {
			
			$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, accessed_date)
												VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";

			foreach ($lesson_progresses_id as $lesson_progress_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_video_actions_by_fma_id ($group_student_id, $forced_material_access_ids, $date_time) {
		GLOBAL $connect;

		try {
			
			$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id, :accessed_date)";

			foreach ($forced_material_access_ids as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $value['lesson_progress_id'], PDO::PARAM_INT);
				$stmt->bindParam(':forced_material_access_id', $value['forced_material_access_id'], PDO::PARAM_INT);
				$stmt->bindParam(':accessed_date', $date_time, PDO::PARAM_STR);
				$stmt->execute();
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_forced_material_access($group_student_id, $lesson_progresses, $date_time) {
		GLOBAL $connect;

		try {
			$result = array();
			$query = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id, created_date)
								VALUES (:lesson_progress_id, :group_student_id, :date_time)";
			foreach ($lesson_progresses as $id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $id, PDO::PARAM_INT);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':date_time', $date_time, PDO::PARAM_STR);
				$stmt->execute();

				$forced_material_access_id = $connect->lastInsertId();
				array_push($result, array('forced_material_access_id' => $forced_material_access_id,
											'lesson_progress_id' => $id));
			}

			return $result;
		} catch (Exception $e) {
			// return array();
			throw $e;
		}
	}

	function get_lesson_progresses_by_last_learned_date($group_student_id, $last_learned_date) {
		GLOBAL $connect; 

		try {

			$query = "SELECT lp.id,
							lp.created_date,
							DATE_FORMAT(lp.created_date, 'Y-m-d') = DATE_FORMAT(NOW(), 'Y-m-d') is_today
						FROM lesson_progress lp,
							group_student gs
						WHERE gs.id = :group_student_id
							AND lp.group_info_id = gs.group_info_id
							AND lp.created_date >= :last_learned_date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':last_learned_date', $last_learned_date, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('for_today' => array(),
							'for_prev_lessons' => array());

			foreach ($query_result as $value) {
				if ($value['is_today'] == 1) {
					array_push($result['for_today'], $value['id']);
				} else {
					array_push($result['for_prev_lessons'], $value['id']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			// return array();
			throw $e;
		}
	}

	function update_group_student_status_to_waitnig ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_student SET status='waiting' WHERE id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function update_group_student_status_to_active($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_student SET status='active' WHERE id = :group_student_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student_waiting_payment ($group_student_id, $start_date, $partial_payment_days) {

		GLOBAL $connect;

		try {

			$new_group_student_payment_id = "";

			if ($partial_payment_days != '') {
				$query = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, partial_payment_days)
														VALUES (:group_student_id, NOW(), :start_date, :partial_payment_days)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
				$stmt->execute();

				$new_group_student_payment_id = $connect->lastInsertId();
			} else {
				$query = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date)
														VALUES (:group_student_id, NOW(), :start_date)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
				$stmt->execute();

				$new_group_student_payment_id = $connect->lastInsertId();
			}

			return $new_group_student_payment_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student_payment($group_student_id, $last_learned_date, $partial_payment_days) {
		GLOBAL $connect;

		try {

			$new_group_student_payment_id = '';

			if ($partial_payment_days != '') {
				$access_until = date('Y-m-d', strtotime($last_learned_date.' + '.$partial_payment_days.' days'));
				$query = "INSERT INTO group_student_payment
									(group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type, partial_payment_days)
							VALUES (:group_student_id, NOW(), :last_learned_date, :access_until, 1, NOW(), 'money', :partial_payment_days)";

				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':last_learned_date', $last_learned_date, PDO::PARAM_STR);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
				$stmt->execute();

				$new_group_student_payment_id = $connect->lastInsertId();
			} else {
				$access_until = date('Y-m-d', strtotime($last_learned_date.' + 1 month'));
				$query = "INSERT INTO group_student_payment
									(group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type)
							VALUES (:group_student_id, NOW(), :last_learned_date, :access_until, 1, NOW(), 'money')";

				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':last_learned_date', $last_learned_date, PDO::PARAM_STR);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->execute();

				$new_group_student_payment_id = $connect->lastInsertId();
			}

			return $new_group_student_payment_id;
			
		} catch (Exception $e) {
			// return '';
			throw $e;
		}
	}

	function get_last_learn_date($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gsp.access_until
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
						ORDER BY gsp.access_until DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$last_learned_date = $stmt->fetch(PDO::FETCH_ASSOC)['access_until'];

			if ($last_learned_date == '' || $last_learned_date == null) {
				$query = "SELECT lp.created_date
							FROM lesson_progress lp,
								group_student gs
							WHERE gs.id = :group_student_id
								AND lp.group_info_id = gs.group_info_id
							ORDER BY lp.created_date ASC
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();

				$last_learned_date = $stmt->fetch(PDO::FETCH_ASSOC)['created_date'];
			}


			return $last_learned_date;
			
		} catch (Exception $e) {
			// return '';
			throw $e;
		}
	} 

	function get_new_group_start_date ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.start_date
						FROM group_student gs,
							group_info gi
						WHERE gs.id = :group_student_id
							AND gi.id = gs.group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$new_group_start_date = $stmt->fetch(PDO::FETCH_ASSOC)['start_date'];
			return $new_group_start_date;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>

<!-- 
delete from tutorial_video_action    where forced_material_access_id in (7582, 7583, 7584, 7585, 7586, 7587);
delete from tutorial_document_action where forced_material_access_id in (7582, 7583, 7584, 7585, 7586, 7587);
delete from end_video_action         where forced_material_access_id in (7582, 7583, 7584, 7585, 7586, 7587);
delete from material_test_action     where lesson_progress_id in (1809, 1810, 1822, 1805, 1806, 1819);
 -->

<!-- 
{"pg_order_id":"1598939314153","pg_payment_id":"304845319","pg_amount":"20970.00","pg_currency":"KZT","pg_net_amount":"20361.87","pg_ps_amount":"20970.00","pg_ps_full_amount":"20970.00","pg_ps_currency":"KZT","pg_payment_system":"EPAYWEBKZT","pg_description":"20 970,00 \u0442\u0435\u04a3\u0433\u0435","pg_result":"1","pg_payment_date":"2020-09-01 11:59:58","pg_can_reject":"0","pg_user_phone":"77714463782","pg_need_phone_notification":"0","pg_user_contact_email":"teleuhanovalinur@gmail.com","pg_need_email_notification":"1","pg_captured":"1","payment_info":"{\"groups\":{\"1198\":{\"amount\":6990,\"partial_payment_days\":null},\"1200\":{\"amount\":6990,\"partial_payment_days\":null},\"1199\":{\"amount\":6990,\"partial_payment_days\":null}},\"order_id\":\"1598939314153\"}","pg_card_pan":"4400-43XX-XXXX-0939","pg_card_exp":"06\/23","pg_card_owner":"Alinur TELEUKHANOV","pg_auth_code":"233992","pg_card_brand":"VI","pg_salt":"nwaCtaM6fRYUI93b","pg_sig":"13c15f63c84c50bc22a44cba3c1df9ba"}
 --> 