<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_student_used_promo_code () {
		GLOBAL $connect;

		try {
			$student_id = $_SESSION['user_id'];

			$query = "SELECT supc.id AS student_used_promo_code_id,
							s.last_name,
							s.first_name,
							sj.title AS subject_title,
							spcl.id AS student_promo_code_log_id
						FROM student_used_promo_code supc
						INNER JOIN student_promo_code spc
							ON spc.id = supc.student_promo_code_id
						INNER JOIN student s
							ON s.id = spc.student_id
						LEFT JOIN student_promo_code_log spcl
							ON spcl.student_id = supc.student_id
								AND spcl.student_used_promo_code_id = supc.id
						LEFT JOIN group_info gi
							ON gi.id = spcl.group_info_id
						LEFT JOIN subject sj
							ON sj.id = gi.subject_id
						WHERE supc.student_id = :student_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = array('student_used_promo_code_id' => $query_result['student_used_promo_code_id'],
							'last_name' => $query_result['last_name'],
							'first_name' => $query_result['first_name'],
							'subject_title' => $query_result['subject_title'],
							'already_payment_done' => get_group_student_payment_count($student_id),
							'cant_insert_promo_code' => get_availability_to_insert_promo_code($student_id),
							'student_promo_code_log_id' => $query_result['student_promo_code_log_id']);
			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_availability_to_insert_promo_code ($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.id AS group_student_id,
							gs.transfer_from_group
						FROM group_student gs
						WHERE gs.student_id = :student_id
							AND gs.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$cant_access = false;
			foreach ($query_result as $value) {
				$group_student_discount = get_group_student_discount($value['group_student_id'], $value['transfer_from_group']);

				if (!$cant_access) {
					// && $group_student_discount['cant_insert_promo_code'] == 1
					$cant_access = true;
					break;
				}
			}

			return $cant_access;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_payment_count ($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(gsp.id) AS gsp_count
						FROM group_student gs,
							group_student_payment gsp
						WHERE gs.student_id = :student_id
							AND gsp.group_student_id = gs.id
							AND gsp.payment_type = 'money'";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$gsp_count = $stmt->fetch(PDO::FETCH_ASSOC)['gsp_count'];

			return $gsp_count;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_self_promo_code () {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT spc.code
						FROM student_promo_code spc
						WHERE spc.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC)['code'];
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_promo_code_list () {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT supc.id AS student_used_promo_code_id,
							s.last_name,
							s.first_name,
							(SELECT count(spcl.id) = 1
								FROM student_promo_code_log spcl
								WHERE spcl.student_id = supc.student_id
									AND spcl.student_used_promo_code_id = supc.id) AS is_friend_promo_code_use,
							(SELECT count(spcl.id) = 1
								FROM student_promo_code_log spcl
								WHERE spcl.student_id = spc.student_id
									AND spcl.student_used_promo_code_id = supc.id) AS is_promo_code_use
						FROM student_promo_code spc
						INNER JOIN student_used_promo_code supc
							ON supc.student_promo_code_id = spc.id
						INNER JOIN student s
							ON s.id = supc.student_id
						WHERE spc.student_id = :student_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['student_used_promo_code_id']] = array('last_name' => $value['last_name'],
																		'first_name' => $value['first_name'],
																		'is_friend_promo_code_use' => $value['is_friend_promo_code_use'],
																		'is_promo_code_use' => $value['is_promo_code_use']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>