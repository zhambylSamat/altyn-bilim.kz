<?php
	include_once('connection.php');
	include_once('../common/send_email.php');
	include_once('../common/emails.php');

	$group_student_payments = get_group_student_payments();
	check_and_fix_payments($group_student_payments);

	function get_group_student_payments() {
		GLOBAL $connect;

		try {
			$query = "SELECT gsp.id,
							gsp.group_student_id,
							gsp.payed_date,
							DATE_FORMAT(gsp.access_until, '%Y-%m-%d') AS access_until,
							gsp.is_used
						FROM group_student_payment gsp
						WHERE gsp.finished_date IS NULL
							AND gsp.is_used = 1
						ORDER BY gsp.payed_date ASC";

			if ($row = mysqli_query($connect, $query)) {
				$result = array();
				while ($val = mysqli_fetch_assoc($row)) {
					if (!isset($result[$val['group_student_id']])) {
						$result[$val['group_student_id']] = array();
					}
					array_push($result[$val['group_student_id']], array('group_student_payment_id' => $val['id'],
																		'group_student_id' => $val['group_student_id'],
																		'payed_date' => $val['payed_date'],
																		'access_until' => $val['access_until'],
																		'is_used' => $val['is_used']));
				}
				// print_r($result);
				return $result;
			} else {
				print_r(mysqli_error($connect));
				return array();
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_and_fix_payments($group_student_payments) {
		GLOBAL $connect;

		try {

			$query_gsp = "UPDATE group_student_payment SET full_finished = %s, finished_date = '%s' WHERE id = %s";
			$query_gs = "UPDATE group_student SET status = '%s' WHERE id = %s";
			foreach ($group_student_payments as $gs_id => $val) {
				for ($i = 0; $i < count($val); $i++) {
					if ($val[$i]['is_used'] == 1 && strtotime($val[$i]['access_until']) == strtotime(date('Y-m-d'))) {
						$query_gsp_tmp = sprintf($query_gsp, 1, date('Y-m-d H:i:s'), $val[$i]['group_student_payment_id']);
						$row = mysqli_query($connect, $query_gsp_tmp);
						// if (!$row) {
						// 	print_r(mysqli_error($connect));
						// }

						$query_gs_tmp = sprintf($query_gs, 'inactive', $val[$i]['group_student_id']);
						$row = mysqli_query($connect, $query_gs_tmp);
						if (!$row) {
							print_r(mysqli_error($connect));
						}
					} else if ($val[$i]['is_used'] == 0) {
						$query_gs_tmp = sprintf($query_gs, 'waiting', $gs_id);
						$row = mysqli_query($connect, $query_gs_tmp);
						if (!$row) {
							print_r(mysqli_error($connect));
						}
						break;
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>