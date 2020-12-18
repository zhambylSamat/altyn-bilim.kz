<?php
	include_once('../common/connection.php');

	try {

		$new_group_id = 25; //25, 24, 23, 27
		$old_group_id = 16; //16, 15, 14, 10

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

		// $query = "UPDATE group_student_payment SET payed_date = :payed_date, used_date = :used_date, full_finished = NULL "

		foreach ($sql_result as $value) {
			echo "student_id: ".$value['student_id']."<br>";
			echo "group_id: ".$value['group_id']."<br>";
			echo "used_date: ".$value['used_date']."<br>";
			echo "days: ".$value['days']."<br>";
			echo "group_student_id: ".$value['group_student_id']."<br>";
			echo "<br>";
			
		}

	} catch (Exception $e) {
		throw $e;
	}
?>