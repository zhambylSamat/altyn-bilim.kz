<?php
	include_once('../common/connection.php');
	try {

		$new_group = 34; //33, 34
		$old_group = 24; //23, 24
	
		$query = "SELECT gs.id,
						gs.student_id,
						gs.group_info_id
					FROM group_student gs
					WHERE gs.group_info_id = :old_group";
		$stmt = $connect->prepare($query);
		$stmt->bindParam(':old_group', $old_group, PDO::PARAM_INT);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$query_update_gsp = "UPDATE group_student_payment SET full_finished = 0, finished_date = NOW() WHERE id = :id";
		$query_select_gsp = "SELECT gsp.id,
									DATE_FORMAT(gsp.access_until, '%Y-%m-%d') AS access_until
							FROM group_student_payment gsp
							WHERE gsp.group_student_id = :group_student_id
								AND gsp.is_used = 1
								AND gsp.finished_date IS NULL
							ORDER BY gsp.payed_date ASC
							LIMIT 1";

		$query_insert_sp = "INSERT INTO student_balance (student_id, group_id, is_used, days, used_date)
									VALUE(:student_id, :group_id, :is_used, :days, NOW())";

		$query_get_new_gs = "SELECT gs.id FROM group_student gs WHERE student_id = :student_id AND group_info_id = :group_info_id";

		$query_insert_gsp = "INSERT INTO group_student_payment (group_student_id,
																payed_date,
																access_until,
																is_used,
																used_date,
																payment_type,
																partial_payment_days)
										VALUES (:group_student_id,
												NOW(),
												:access_until,
												1,
												NOW(),
												'balance',
												:days)";
		foreach ($sql_result as $value) {
			$stmt = $connect->prepare($query_select_gsp);
			$stmt->bindParam(':group_student_id', $value['id'], PDO::PARAM_INT);
			$stmt->execute();
			$gsp_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$stmt = $connect->prepare($query_update_gsp);
			$stmt->bindParam(':id', $gsp_result['id']);
			$stmt->execute();

			$is_used = 1;
			$date1 = date_create($gsp_result['access_until']);
			$date2 = date_create(date('Y-m-d'));
			$days = date_diff($date1, $date2)->format('%a');
			// echo 'gs_id: '.$value['id']."<br>";
			// echo 'student_id: '.$value['student_id']."<br>";
			// echo 'group_info_id: '.$value['group_info_id']."<br>";
			// echo 'days: '.$days.'<br>';
			// echo 'access_until: '.$gsp_result['access_until']."<br>";
			// echo "<hr>";
			$stmt = $connect->prepare($query_insert_sp);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $value['group_info_id'], PDO::PARAM_INT);
			$stmt->bindParam(':is_used', $is_used, PDO::PARAM_INT);
			$stmt->bindParam(':days', $days, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $connect->prepare($query_get_new_gs);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $new_group, PDO::PARAM_INT);
			$stmt->execute();
			$gs_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$access_until = date('Y-m-d', strtotime(date()." +1 ".$days." days"));
			$stmt = $connect->prepare($query_insert_gsp);
			$stmt->bindParam(':group_student_id', $gs_result['id'], PDO::PARAM_INT);
			$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
			$stmt->bindParam(':days', $days, PDO::PARAM_INT);
			$stmt->execute();
		}


	} catch (Exception $e) {
		throw $e;
	}
?>