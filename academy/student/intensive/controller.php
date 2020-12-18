<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	$data = array();

	if (isset($_GET['set_group_to_intensive'])) {
		try {

			$group_student_id = $_GET['group_student_id'];

			$query = "SELECT ag.id 
						FROM army_group ag,
							group_student gs
						WHERE ag.group_info_id = gs.group_info_id
							AND gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "SELECT gs.group_info_id FROM group_student gs WHERE gs.id = :group_student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
				$group_info_id = $stmt->fetch(PDO::FETCH_ASSOC)['group_info_id'];

				$query = "DELETE FROM group_schedule WHERE group_info_id = :group_info_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();

				$query = "INSERT INTO group_schedule (group_info_id, week_day_id) VALUES (:group_info_id, 1),
																						(:group_info_id, 2),
																						(:group_info_id, 3),
																						(:group_info_id, 4),
																						(:group_info_id, 5),
																						(:group_info_id, 6)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();

				$query = "SELECT gsp.id,
								gsp.access_until,
								gsp.payment_type,
								gsp.partial_payment_days,
								gsp.is_used
							FROM group_student_payment gsp
							WHERE gsp.group_student_id = :group_student_id
								AND gsp.full_finished IS NULL";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();

				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					$payment_type = $query_result['payment_type'];

					$date1 = date_create($query_result['access_until']);
					$date2 = date_create(date('Y-m-d'));
					$days = date_diff($date1, $date2)->format('%a');
					$days = intval($days / 2);
					$days = $days == 0 ? 1 : $days;
					$access_until = date('Y-m-d', strtotime(' + '.$days.' days'));

					$query = "UPDATE group_student_payment SET access_until = :access_until WHERE id = :group_student_payment_id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_payment_id', $query_result['id'], PDO::PARAM_INT);
					$stmt->execute();
				} else if ($row_count > 1) {
					$query_result = $stmt->fetchAll();

					$payment_type = 'balance';
					$days = 0;
					$primary_gsp_id = 0;
					$secondary_gsp_ids = array();
					foreach ($query_result as $value) {
						if ($value['payment_type'] == 'money') {
							$payment_type = 'money';
						}

						if ($value['is_used'] == 1) {
							$primary_gsp_id = $value['id'];
							$date1 = date_create($value['access_until']);
							$date2 = date_create(date('Y-m-d'));
							$days += date_diff($date1, $date2)->format('%a');
						} else {
							array_push($secondary_gsp_ids, $value['id']);
							if ($value['partial_payment_days'] == '') {
								$date1 = date_create($value['access_until']);
								$date2 = date_create(date('Y-m-d', strtotime($value['access_until'].' + 1 month')));
								$days += date_diff($date1, $date2)->format('%a');
							} else {
								$date1 = date_create($value['access_until']);
								$date2 = date_create(date('Y-m-d', strtotime($value['access_until'].' + '.$value['partial_payment_days'].' days')));
								$days += date_diff($date1, $date2)->format('%a');
							}
						}
					}
					$days = intval($days / 2);
					$days = $days == 0 ? 1 : $days;
					$access_until = date('Y-m-d', strtotime(' + '.$days.' days'));
					$query = "UPDATE group_student_payment SET access_until = :access_until,
													payment_type = :payment_type,
													partial_payment_days = :partial_payment_days
								WHERE id = :group_student_payment_id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
					$stmt->bindParam(':group_student_payment_id', $primary_gsp_id, PDO::PARAM_INT);
					$stmt->bindParam(':partial_payment_days', $days, PDO::PARAM_INT);
					$stmt->bindParam(':payment_type', $payment_type, PDO::PARAM_STR);
					$stmt->execute();

					if (count($secondary_gsp_ids) > 0) {
						$query = "DELETE FROM group_student_payment WHERE id IN (".implode(',', $secondary_gsp_ids).")";
						$stmt = $connect->prepare($query);
						$stmt->execute();
					}
				}
				
			}

			$data['success'] = true;

		} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
	} else if (isset($_GET['unset_group_to_intensive'])) {
		try {
			$group_student_id = $_GET['group_student_id'];

			$query = "SELECT gs.group_info_id FROM group_student gs WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_info_id = $stmt->fetch(PDO::FETCH_ASSOC)['group_info_id'];

			$query = "DELETE FROM group_schedule WHERE group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "SELECT ssc.week_day_id
						FROM subject_schedule_configuration ssc,
							group_info gi
						WHERE gi.id = :group_info_id
							AND ssc.subject_id = gi.subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$query = "INSERT INTO group_schedule (group_info_id, week_day_id) VALUES (:group_info_id, :week_day_id)";
			foreach ($query_result as $value) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':week_day_id', $value['week_day_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			$query = "SELECT gsp.id,
							gsp.access_until,
							gsp.payment_type,
							gsp.partial_payment_days,
							gsp.is_used
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
							AND gsp.full_finished IS NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 1) {
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$payment_type = $query_result['payment_type'];

				$date1 = date_create($query_result['access_until']);
				$date2 = date_create(date('Y-m-d'));
				$days = date_diff($date1, $date2)->format('%a');
				$days = intval($days * 2);
				$days = $days == 0 ? 1 : $days;
				$access_until = date('Y-m-d', strtotime(' + '.$days.' days'));

				$query = "UPDATE group_student_payment SET access_until = :access_until WHERE id = :group_student_payment_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_payment_id', $query_result['id'], PDO::PARAM_INT);
				$stmt->execute();
			} else if ($row_count > 1) {
				$query_result = $stmt->fetchAll();

				$payment_type = 'balance';
				$days = 0;
				$primary_gsp_id = 0;
				$secondary_gsp_ids = array();
				foreach ($query_result as $value) {
					if ($value['payment_type'] == 'money') {
						$payment_type = 'money';
					}

					if ($value['is_used'] == 1) {
						$primary_gsp_id = $value['id'];
						$date1 = date_create($value['access_until']);
						$date2 = date_create(date('Y-m-d'));
						$days += date_diff($date1, $date2)->format('%a');
					} else {
						array_push($secondary_gsp_ids, $value['id']);
						if ($value['partial_payment_days'] == '') {
							$date1 = date_create($value['access_until']);
							$date2 = date_create(date('Y-m-d', strtotime($value['access_until'].' + 1 month')));
							$days += date_diff($date1, $date2)->format('%a');
						} else {
							$date1 = date_create($value['access_until']);
							$date2 = date_create(date('Y-m-d', strtotime($value['access_until'].' + '.$value['partial_payment_days'].' days')));
							$days += date_diff($date1, $date2)->format('%a');
						}
					}
				}
				$days = intval($days * 2);
				$days = $days == 0 ? 1 : $days;
				$access_until = date('Y-m-d', strtotime(' + '.$days.' days'));
				$query = "UPDATE group_student_payment SET access_until = :access_until,
												payment_type = :payment_type,
												partial_payment_days = :partial_payment_days
							WHERE id = :group_student_payment_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_payment_id', $primary_gsp_id, PDO::PARAM_INT);
				$stmt->bindParam(':partial_payment_days', $days, PDO::PARAM_INT);
				$stmt->bindParam(':payment_type', $payment_type, PDO::PARAM_INT);
				$stmt->execute();

				if (count($secondary_gsp_ids) > 0) {
					$query = "DELETE FROM group_student_payment WHERE id IN (".implode(',', $secondary_gsp_ids).")";
					$stmt = $connect->prepare($query);
					$stmt->execute();
				}
			}

			$data['success'] = true;

		} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
	}
?>