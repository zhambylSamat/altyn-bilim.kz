<?php

$student_1 = 'US5d5f8665e25e74.96628955_1566541413';
$student_2 = '';
$student_3 = '';

echo checkExistingActivePoll($student_1);

function checkExistingActivePoll($student_num) {
	$current_day = intval(date('d'));
	$start_day = 25;
	$end_day = 10;

	if ($current_day >= $start_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
	} else if ($current_day <= $end_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
	}

	// $thirty_days_before =  date('d-m-Y', strtotime("-20 days"));
	$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));


	if (isset($start_date) && isset($end_date) && isset($student_num)) {
		try {
			include('../connection.php');

			$stmt = $conn->prepare("SELECT count(sp.id) AS c,
										(SELECT count(tpi.id)
										FROM teacher_poll_info tpi) AS cc
									FROM student_poll sp
									WHERE sp.student_num = :student_num
										AND sp.polled_date >= STR_TO_DATE(:start_date, '%d-%m-%Y')
										AND sp.polled_date <= STR_TO_DATE(:end_date, '%d-%m-%Y')");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
			$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
			$stmt->execute();
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi.group_info_num
                                    	AND tr2.student_num = gs.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
			$stmt = $conn->prepare("SELECT count(DISTINCT gi.teacher_num) AS c
									FROM group_student gs,
										group_info gi
									WHERE gs.student_num = :student_num
										AND gs.block != 6
										AND gi.subject_num != 'S5985a7ea3d0ae721486338'
										AND gi.group_info_num = gs.group_info_num
										AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')");
  			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->bindParam(':poll_activate_days', $poll_activate_days, PDO::PARAM_STR);
			$stmt->execute();
			$active_poll_teachers = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

			// $created_date = strtotime($res['created_date']);
			// $checked_date = strtotime($thirty_days_before);
			if ($active_poll_teachers > 0 && $res['cc'] > 0 && $res['c'] < $active_poll_teachers) {
				return "fill_poll.php";
			} else {
				return "";
			}

		} catch (PDOException $e) {
			return "";
		}
	}
}
?>