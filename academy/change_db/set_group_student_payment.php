<?php
	include_once('../common/connection.php');

	try {
		
		$query = "SELECT * FROM group_student";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$query = "INSERT INTO group_student_payment (group_student_id, payed_date, access_until, is_used, used_date)
													VALUES (:group_student_id, :payed_date, :access_until, :is_used, :used_date)";
		foreach ($sql_result as $value) {
			$group_student_id = '';
			if ($value['status'] == 'active') {
				$group_student_id = $value['id'];
				$payed_date = $value['start_date'];
				$access_until = $value['access_until'];
				$is_used = 1;
				$used_date = $value['start_date'];
			} else if ($value['status'] == 'waiting') {
				$group_student_id = $value['id'];
				$payed_date = $value['created_date'];
				$access_until = null;
				$is_used = 0;
				$used_date = null;
			}

			if ($group_student_id != '') {
				// echo "group_student_id: ".$group_student_id."<br>";
				// echo "payed_date: ".$payed_date."<br>";
				// echo "access_until: ".$access_until."<br>";
				// echo "is_used: ".$is_used."<br>";
				// echo "used_date: ".$used_date."<br>";
				$stmt = $connect->prepare("SELECT count(id) AS count FROM group_student_payment WHERE group_student_id = :gs_id");
				$stmt->bindParam(':gs_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();

				if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
					$stmt->bindParam(':payed_date', $payed_date, PDO::PARAM_STR);
					$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
					$stmt->bindParam(':is_used', $is_used, PDO::PARAM_INT);
					$stmt->bindParam(':used_date', $used_date);
					$stmt->execute();
				}
			}
		}

	} catch (Exception $e) {
		throw $e;
	}
?>