<?php
	include_once('../common/connection.php');

	$end_groups = get_end_groups();
	transfer_or_archive_group($end_groups);

	function get_end_groups () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id,
							gi.status_id,
							(gi.status_change_date = NOW()) AS is_status_today_changed,
							gi.topic_id,
							(SELECT st.id
							FROM subtopic st
							WHERE st.topic_id = gi.topic_id
							ORDER BY st.subtopic_order DESC) AS last_subtopic_id,
							gi.subject_id
						FROM group_info gi
						WHERE gi.is_archive = 0
							AND gi.status_id IN (2, 5)
							AND gi.lesson_type = 'topic'
							AND (WEEKDAY(NOW()) + 1) IN (SELECT gsch.week_day_id
														FROM group_schedule gsch
														WHERE gsch.group_info_id = gi.id)";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				if (group_is_in_last_subtopic($value['group_info_id'], $value['last_subtopic_id'])) {
					$result[$value['group_info_id']] = array('status_id' => $value['status_id'],
															'is_status_today_changed' => $value['is_status_today_changed'],
															'topic_id' => $value['topic_id'],
															'subject_id' => $value['subject_id'],
															'schedules' => get_group_schedules($value['group_info_id']));
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function group_is_in_last_subtopic ($group_info_id, $last_subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id
						FROM lesson_progress lp
						WHERE lp.group_info_id = :group_info_id
							AND lp.subtopic_id = :last_subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':last_subtopic_id', $last_subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			return $row_count == 0 ? false : true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function transfer_or_archive_group ($groups) {
		GLOBAL $connect;

		try {

			foreach ($groups as $group_info_id => $group_info) {
				if ($group_info['status_id'] == 5 && ($group_info['is_status_today_changed'] == '' || !$group_info['is_status_today_changed'])) {
					$query = "UPDATE group_info SET status_id = 4, is_archive = 1, status_change_date = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();

					$query = "UPDATE group_student SET status = 'inactive', is_archive = 1 WHERE group_info_id = :group_info_id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();
				} else if ($group_info['status_id'] == 2) {
					$query = "UPDATE group_info SET status_id = 5, status_change_date = NOW() WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();

					$group_student_result = get_active_group_students($group_info_id);

					foreach ($group_student_result as $group_student) {
						$query = "SELECT gsp.id AS group_student_payment_id,gsp.access_until,
										gsp.is_used,
										gsp.payment_type,
										gsp.payed_date,
										MONTH(gsp.payed_date) payed_month,
										YEAR(gsp.payed_date) payed_year,
										gsp.partial_payment_days
									FROM group_student_payment gsp
									WHERE gsp.group_student_id = :group_student_id
										AND gsp.group_studnet_id = gs.id
										AND gsp.full_finised IS NULL";
						$stmt = $connect->prepare($query);
						$stmt->bindParam(':group_student_id', $group_student['id'], PDO::PARAM_INT);
						$stmt->execute();
						$group_student_payment_result = $stmt->fetchAll();

						$partial_days = 0;
						foreach ($group_student_payment_result as $group_student_payment) {
							if ($group_student_payment['is_used'] == 1) {
								$date1 = date_create($group_student_payment['access_until']);
								$date2 = date_create(date('Y-m-d'));
								$partial_days += date_diff($date1, $date2)->format('%a');
							} else if ($group_student_payment['payment_type'] == 'money') {
								$partial_days += cal_days_in_month(CAL_GREGORIAN, $group_student_payment['payed_month'], $group_student_payment['payed_year']);
							} else if ($group_student_payment['payment_type'] == 'balance' && $group_student_payment['partial_payment_days'] != "") {
								$partial_days += $group_student_payment['partial_payment_days'];
							}
						}

						if ($partial_days > 0) {
							insert_student_balance ($group_student['student_id'], $group_info_id, $partial_days);
							transfer_if_topic_exists($group_info_id, $group_info);
						}
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_student_balance ($student_id, $group_info_id, $partial_days) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO student_balance (:student_id, :group_id, :days) VALUES (:student_id, :group_id, :days)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':days', $partial_days, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function transfer_if_topic_exists ($old_group_info_id, $old_group_info) {
		GLOBAL $connect;

		try {

			$query = "SELECT t.id AS topic_id,
							(SELECT st.id
							FROM subtopci st
							WHERE st.topic_id = t.id
							ORDER BY st.subtopic_order ASC
							LIMIT 1) AS first_subtopic_id,
							t.title AS topic_title
						FROM topic t
						WHERE t.subject_id = :subject_id
							AND t.topic_order = (SELECT t1.topic_order + 1
												FROM topic t1
												WHeRE t1.id = :topic_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $old_group_info['topic_id'], PDO::PARAM_INT);
			$stmt->bindParam(':subject_id', $old_group_info['subject_id'], PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$new_lesson_info = $stmt->fetch(PDO::FETCH_ASSOC);

				$new_group_info_id = create_new_active_group($old_group_info['subject_id'], $new_lesson_info['topic_id'], $new_lesson_info['topic_title'], $old_group_info['schedules']);
				set_group_configuration($old_group_info_id, $new_group_info_id);

				$students = get_active_group_students($old_group_info_id);

				foreach ($students as $student) {
					$query = "SELECT sb.id,
									sb.days
								FROM student_balance sb
								WHERE sb.student_id = :student_id
									AND sb.is_used = 0";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':student_id', $student['id'], PDO::PARAM_INT);
					$stmt->execute();
					$row_count = $stmt->rowCount();

					$student_balances = array();
					$group_student_status = 'inactive';
					$partial_payment_days = 0;
					if ($row_count > 0) {
						$group_student_status = 'waiting';
						$student_balances = $stmt->fetchAll();

						foreach ($student_balances as $student_balance) {
							$partial_payment_days += $student_balance['days'];

							update_student_balance_to_used($student_balance['id'], $new_group_info_id);
						}
					}

					$new_group_student_id = insert_group_student($new_group_info_id, $old_group_info_id, $student['id'], $new_lesson_info['first_subtopic_id'], $group_student_status);

					if ($partial_payment_days > 0) {
						insert_group_student_payment($group_student_id, $partial_payment_days);
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function update_student_balance_to_used ($student_balance_id, $used_for_group) {
		GLOBAL $connect;

		try {

			$query = "UPDATE student_balance SET is_used = 1, used_date = NOW(), used_for_group = :used_for_group WHERE id = :student_balance_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':used_for_group', $used_for_group, PDO::PARAM_INT);
			$stmt->bindParam(':student_balance_id', $student_balance_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function create_new_active_group ($subject_id, $topic_id, $group_name, $schedules) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO group_info (subject_id, topic_id, lesson_type, group_name, start_date, created_date, status_id)
									VALUES (:subject_id, :topic_id, 'topic', :group_name, NOW(), NOW(), 2)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $old_group_info['subject_id'], PDO::PARAM_INT);
			$stmt->bindParam(':topic_id', $new_lesson_info['topic_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_name', $new_lesson_info['topic_title'], PDO::PARAM_STR);
			$stmt->execute();

			$new_group_info_id = $connect->lastInsertId();

			foreach ($schedules as $week_day_id) {
				$query = "INSERT INTO group_schedule (group_info_id, week_day_id) VALUES (:group_info_id, :week_day_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $new_group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':week_day_id', $week_day_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			return $new_group_info_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_active_group_students ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.id,
							gs.student_id
						FROM group_student gs
						WHERE gs.group_info_id = :group_info_id
							AND gs.is_archive = 0
							AND gs.status != 'inactive'";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->fetchAll();
			$group_student_result = $stmt->fetchAll();

			return $group_student_result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student ($new_group_info_id, $old_group_info_id, $student_id, $first_subtopic_id, $group_student_status) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO group_student (group_info_id, transfer_from_group, student_id, start_from, status)
										VALUES (:group_info_id, :transfer_from_group, :student_id, :start_from, :status)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $new_group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':transfer_from_group', $old_group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':start_from', $first_subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':status', $group_student_status, PDO::PARAM_STR);
			$stmt->execute();

			$new_group_student_id = $connect->lastInsertId();

			return $new_group_student_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student_payment ($group_student_id, $partial_payment_days) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, is_used, payment_type, partial_payment_days)
												VALUES (:group_student_id, NOW(), NOW(), 0, 'balance', :partial_payment_days)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_group_configuration ($old_group_info_id, $new_group_info_id) {
		GLOBAL $connect;

		try {

			if (get_is_army_group($old_group_info_id)) {
				$query = "INSERT INTO army_group (group_info_id) VALUES (:new_group_info_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':new_group_info_id', $new_group_info_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			if (get_is_school_group($old_group_info_id)) {
				$query = "INSERT INTO school_group (group_info_id) VALUES (:new_group_info_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':new_group_info_id', $new_group_info_id, PDO::PARAM_INT);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>