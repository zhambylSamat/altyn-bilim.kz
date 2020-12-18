<?php
	include_once('../common/connection.php');

	add_student_to_group_from_reserve();

	function add_student_to_group_from_reserve() {
		GLOBAL $connect;

		try {

			$dayofweek = date('w') == '0' ? '7' : date('w');

			$query = "SELECT gi.id,
						gi.lesson_type,
						gi.topic_id
					FROM group_info gi
						WHERE gi.start_date >= DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			foreach ($sql_result as $value) {
				collect_registration_reserved_students($value['id'], $value['lesson_type'], $value['topic_id']);
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function collect_registration_reserved_students($group_info_id, $lesson_type, $topic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT st.id
										FROM subtopic st
										WHERE st.topic_id = :topic_id
										ORDER BY st.subtopic_order ASC
										LIMIT 1");
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$start_from_subtopic = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			$stmt = $connect->prepare("SELECT rr.id,
											rr.student_id,
											rr.topic_id
										FROM registration_reserve rr
										WHERE rr.is_done = 0
											AND rr.topic_id = :topic_id");
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$result_get_rr = $stmt->fetchAll();
			foreach ($result_get_rr as $value) {
				$status = 'inactive';

				$total_days = 0;
				$srp_id = '';

				$stmt = $connect->prepare('SELECT count(gs.id) AS c
											FROM group_student gs,
												group_info gi,
												topic t
											WHERE gs.student_id = :student_id
												AND gi.id = gs.group_info_id
												AND t.id = :topic_id
												AND gi.subject_id = t.subject_id');
				$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
				$stmt->bindParam(':topic_id', $value['topic_id'], PDO::PARAM_INT);
				$stmt->execute();
				$number_of_learned_with_special_subject = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

				if ($number_of_learned_with_special_subject == 0) {
					$free_trial_days = 7;
					$free_trial_comment = 'Тегін '.$free_trial_days.' күндік сабақ';
					$stmt = $connect->prepare("INSERT INTO student_balance (student_id, used_for_group, is_used, days, comment, used_date)
														VALUES (:student_id, :used_for_group, 1, :days, :comment, NOW())");
					$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
					$stmt->bindParam(':used_for_group', $group_info_id, PDO::PARAM_INT);
					$stmt->bindParam(':days', $free_trial_days, PDO::PARAM_INT);
					$stmt->bindParam(':comment', $free_trial_comment, PDO::PARAM_STR);
					$stmt->execute();

					$total_days = $free_trial_days;
				}
				else {
					$stmt = $connect->prepare("SELECT sb.id,
													sb.days
												FROM student_balance sb
												WHERE sb.is_used = 0
													AND sb.student_id = :student_id");
					$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
					$stmt->execute();
					$result_get_student_balance = $stmt->fetchAll();
					foreach ($result_get_student_balance as $val) {
						$total_days += intval($val['days']);
						$stmt = $connect->prepare("UPDATE student_balance
													SET is_used = 1, used_for_group = :used_for_group, used_date = NOW()
													WHERE id = :student_balance_id");
						$stmt->bindParam(':used_for_group', $group_info_id, PDO::PARAM_INT);
						$stmt->bindParam(':student_balance_id', $val['id']);
						$stmt->execute();
					}

					$stmt = $connect->prepare("SELECT srp.id 
												FROM student_reserve_payment srp 
												WHERE srp.used_date IS NULL 
													AND srp.registration_reserve_id = :registration_reserve_id");
					$stmt->bindParam(':registration_reserve_id', $value['id'], PDO::PARAM_INT);
					$stmt->execute();
					$srp_row_count = $stmt->rowCount();
					if ($srp_row_count == 1) {
						$srp_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
					}

				}

				if ($total_days > 0 || $srp_id != '') {
					$status = 'waiting';
				}

				$group_student_id = 0;
				$stmt = $connect->prepare("INSERT INTO group_student (group_info_id, student_id, start_from, status)
												VALUES (:group_info_id, :student_id, :start_from, :status)");
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
				$stmt->bindParam(':start_from', $start_from_subtopic, PDO::PARAM_STR);
				$stmt->bindParam(':status', $status, PDO::PARAM_STR);
				$stmt->execute();

				$group_student_id = $connect->lastInsertId();

				$query_insert_gsp = "INSERT INTO group_student_payment (group_student_id, payed_date, is_used, payment_type, partial_payment_days)
										VALUES (:group_student_id, NOW(), 0, :payment_type, :partial_payment_days)";

				if ($group_student_id != 0 && $srp_id != '') {

					$stmt = $connect->prepare($query_insert_gsp);
					$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
					$stmt->bindValue(':payment_type', 'money', PDO::PARAM_STR);
					$stmt->bindValue(':partial_payment_days', null, PDO::PARAM_INT);
					$stmt->execute();

					$stmt = $connect->prepare("UPDATE student_reserve_payment SET used_date = NOW() WHERE id = :student_reserve_payment_id");
					$stmt->bindParam(':student_reserve_payment_id', $srp_id, PDO::PARAM_INT);
					$stmt->execute();

				} else if ($group_student_id != 0 && $total_days > 0) {

					$stmt = $connect->prepare($query_insert_gsp);
					$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
					$stmt->bindValue(':payment_type', 'balance', PDO::PARAM_STR);
					$stmt->bindValue(':partial_payment_days', $total_days, PDO::PARAM_INT);
					$stmt->execute();
				}

				$stmt = $connect->prepare("UPDATE registration_reserve SET is_done = 1 WHERE id = :registration_reserve_id");
				$stmt->bindParam(':registration_reserve_id', $value['id'], PDO::PARAM_INT);
				$stmt->execute();

			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
		
?>