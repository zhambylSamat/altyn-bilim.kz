<?php
	include_once('../common/connection.php');

	try {
		
		$query = "SELECT gi.id AS group_info_id,
						gi.group_name,
						gs.id AS group_student_id,
						gs.start_from,
						gs.start_date,
						gs.access_until,
						s.id AS student_id,
						s.last_name,
						s.first_name,
						(SELECT DATE_FORMAT(lp2.created_date, '%Y-%m-%d')
						FROM lesson_progress lp2
						WHERE lp2.group_info_id = gi.id
						ORDER BY lp2.created_date DESC
						LIMIT 1) AS lesson_progress
					FROM group_info gi,
						group_student gs,
						student s
					WHERE gi.status_id = 4
						AND gs.group_info_id = gi.id
						AND s.id = gs.student_id
					ORDER BY gi.group_name, s.last_name, s.first_name";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$group_id = 0;
		$bg_color = "";
		$special_group_info_id = 12;
		$finish_group_id = 18;
		foreach ($sql_result as $value) {
			if ($group_id != $value['group_info_id']) {
				$group_id = $value['group_info_id'];
				echo "<hr>";
				if ($bg_color == '') {
					$bg_color = "#ccc";
				} else {
					$bg_color = "";
				}
			}
			$finish_date = $value['lesson_progress'];
			$date1 = date_create($value['access_until']);
			$date2 = date_create($value['lesson_progress']);
			$student_balance = date_diff($date1, $date2)->format('%a');
			// echo "<div style='background-color: ".$bg_color.";'>";
			// echo "group_info_id: ".$value['group_info_id']."<br>";
			// echo "group_name: ".$value['group_name']."<br>";
			// echo "group_student_id: ".$value['group_student_id']."<br>";
			// echo "start_from: ".$value['start_from']."<br>";
			// echo "start_date: ".$value['start_date']."<br>";
			// echo "access_until: ".$value['access_until']."<br>";
			// echo "student_id: ".$value['student_id']."<br>";
			// echo "last_name: ".$value['last_name']."<br>";
			// echo "first_name: ".$value['first_name']."<br>";
			// echo "lesson_progress: ".$value['lesson_progress']."<br>";
			// echo "finish_date: ".$finish_date."<br>";
			// echo "student_balance: ".$student_balance."<br>";
			// echo "<br>";
			// echo "</div>";

			$is_used = 1;
			$used_date = date('Y-m-d', strtotime($value['start_date']." + 1 days"));
			if ($value['group_info_id'] == $finish_group_id) {
				$is_used = 0;
				$used_date = null;
			}
			$query = "INSERT INTO student_balance (student_id, group_id, is_used, days, created_date, used_date)
												VALUES (:student_id, :group_id, :is_used, :days, :created_date, :used_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $value['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $value['group_info_id'], PDO::PARAM_INT);
			$stmt->bindParam(':is_used', $is_used, PDO::PARAM_INT);
			$stmt->bindParam(':days', $student_balance, PDO::PARAM_INT);
			$stmt->bindParam(':created_date', $finish_date, PDO::PARAM_STR);
			$stmt->bindParam(':used_date', $used_date, PDO::PARAM_STR);
			$stmt->execute();

			if ($special_group_info_id != $value['group_info_id']) {
				$query = "UPDATE group_student SET status = 'inactive' WHERE id = :group_student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			$query = "UPDATE group_student_payment SET full_finished = 0, finished_date = :finished_date WHERE group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':finished_date', $finish_date, PDO::PARAM_STR);
			$stmt->bindParam(':group_student_id', $value['group_student_id'], PDO::PARAM_INT);
			$stmt->execute();

		}

	} catch (Exception $e) {
		throw $e;
	}
?>