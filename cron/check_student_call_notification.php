<?php
	include_once('connection.php');

	$sql = "SELECT s.student_num, (SELECT count(scn2.id)
									FROM student_call_notification scn2
									WHERE scn2.student_num = s.student_num) AS notification_count
			FROM student s
			WHERE (CASE
					WHEN (SELECT count(scn2.id)
							FROM student_call_notification scn2
							WHERE scn2.student_num = s.student_num) = 0
						THEN (
								s.created_date <= DATE_ADD(NOW(), INTERVAL -14 DAY)
								AND
								(s.unblocked_date <= DATE_ADD(NOW(), INTERVAL -14 DAY)
									OR s.unblocked_date IS NULL
									OR s.unblocked_date = '0000-00-00 00:00:00')
							)
					-- ELSE (
					-- 		(SELECT scn2.called_date
					-- 		FROM student_call_notification scn2
					-- 		WHERE scn2.student_num = s.student_num
					-- 		ORDER BY scn2.called_date DESC
					-- 		LIMIT 1) <= DATE_ADD(NOW(), INTERVAL -2 MONTH)
					-- 		AND
					-- 		(s.unblocked_date <= DATE_ADD(NOW(), INTERVAL -2 MONTH) 
					-- 			OR s.unblocked_date IS NULL
					-- 			OR s.unblocked_date = '0000-00-00 00:00:00')
					-- 	)
					END)
				AND s.created_date IS NOT NULL
				AND s.created_date != '0000-00-00 00:00:00'
				AND 0 = (SELECT count(scn1.id)
							FROM student_call_notification scn1
							WHERE scn1.student_num = s.student_num
								AND scn1.status = 0)
				AND s.block != 6
				AND (SELECT count(gs2.student_num)
					FROM group_student gs2
					WHERE gs2.block != 6
						AND gs2.student_num = s.student_num) >= 1";

	if ($execution = mysqli_query($conn, $sql)) {
	    echo "Record selected successfully<br>";
	} else {
	    echo "Error on selecting record: " . mysqli_error($conn);
	}

	while ($row = mysqli_fetch_assoc($execution)) {
		$student_num = $row['student_num'];
		$notification_status = $row['notification_count'] == 0 ? 1 : 2;
		$sql = "INSERT INTO student_call_notification (student_num, notification_status) VALUES('$student_num', $notification_status)";
		if (mysqli_query($conn, $sql)) {
			echo "Record inserted successfully<br>";
		} else {
			echo "Error on inserting record: ".mysqli_error($conn);
		}
	}

?>