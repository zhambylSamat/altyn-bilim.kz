<?php
	include_once('../common/connection.php');

	try {

		$new_group_id = 27; //25, 24, 23, 27
		$old_group_id = 10; //16, 15, 14, 10

		$query = "SELECT sb.id,
						sb.student_id,
						sb.group_id,
						sb.days,
						sb.used_date,
						(SELECT gs2.id FROM group_student gs2 WHERE gs2.student_id = sb.student_id AND gs2.group_info_id = :new_group_id) AS group_student_id,
						(SELECT gs2.start_date FROM group_student gs2 WHERE gs2.student_id = sb.student_id AND gs2.group_info_id = :new_group_id) AS start_date
					FROM student_balance sb
					WHERE sb.is_used = 1
						AND sb.group_id = :old_group_id";
		$stmt = $connect->prepare($query);
		$stmt->bindParam(':old_group_id', $old_group_id, PDO::PARAM_INT);
		$stmt->bindParam(':new_group_id', $new_group_id, PDO::PARAM_INT);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$query_student_balance = "UPDATE student_balance SET used_date = :used_date 
										WHERE id = :student_balance_id";
		$query_group_student_payment = "UPDATE group_student_payment 
										SET payment_type = 'balance', partial_payment_days = :partial_payment_days, access_until = :access_until 
										WHERE group_student_id = :group_student_id";

		foreach ($sql_result as $value) {
			$access_until = date('Y-m-d', strtotime($value['start_date']." + ".$value['days']." days"));
			// echo "student_id: ".$value['student_id']."<br>";
			// echo "group_id: ".$value['group_id']."<br>";
			// echo "used_date: ".$value['used_date']."<br>";
			// echo "days: ".$value['days']."<br>";
			// echo "group_student_id: ".$value['group_student_id']."<br>";
			// echo "start_date: ".$value['start_date']."<br>";
			// echo 'access_until: '.$access_until."<br>";
			// echo "<br>";

			$stmt = $connect->prepare($query_student_balance);
			$stmt->bindParam(':used_date', $value['start_date'], PDO::PARAM_STR);
			$stmt->bindParam(':student_balance_id', $value['id'], PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $connect->prepare($query_group_student_payment);
			$stmt->bindParam(':partial_payment_days', $value['days'], PDO::PARAM_INT);
			$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
			$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
			$stmt->execute();
		}

	} catch (Exception $e) {
		throw $e;
	}
?>