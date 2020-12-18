<?php
	include_once '../connection.php';

	try {

		$query = "SELECT s.surname,
						s.name,
						gi.group_name,
						sj.subject_name,
						DATE_FORMAT(pg.created_date, '%Y-%m-%d') AS abs_date,
						ps.progress_student_num,
						ps.attendance,
						ps.home_work,
						DATE_FORMAT((CASE
							WHEN pg.group_info_num IN (SELECT t2.new_group_info_num 
														FROM transfer t2 
														WHERE t2.student_num = ps.student_num)
								THEN (SELECT t2.created_date 
									FROM transfer t2 
									WHERE t2.new_group_info_num = gi.group_info_num 
										AND t2.student_num = ps.student_num
									LIMIT 1) 
							WHEN (SELECT gs2.created_date 
								FROM group_student gs2
								WHERE gs2.student_num = ps.student_num
									AND gs2.group_info_num = (SELECT t3.new_group_info_num
															FROM transfer t3
															WHERE t3.old_group_info_num = pg.group_info_num
																AND t3.student_num = ps.student_num)) IS NOT NULL
								THEN (SELECT gs2.created_date 
									FROM group_student gs2
									WHERE gs2.student_num = ps.student_num
										AND gs2.group_info_num = (SELECT t3.new_group_info_num
																FROM transfer t3
																WHERE t3.old_group_info_num = pg.group_info_num
																	AND t3.student_num = ps.student_num))
							WHEN (SELECT gs2.created_date 
								FROM group_student gs2
								WHERE gs2.student_num = ps.student_num
									AND gs2.group_info_num = (SELECT t3.new_group_info_num
															FROM transfer t3
															WHERE t3.student_num = ps.student_num
																AND t3.old_group_info_num = (SELECT t4.new_group_info_num
																							FROM transfer t4
																							WHERE t4.student_num = ps.student_num
																								AND t4.old_group_info_num = pg.group_info_num))) IS NOT NULL
								THEN (SELECT gs2.created_date 
										FROM group_student gs2
										WHERE gs2.student_num = ps.student_num
											AND gs2.group_info_num = (SELECT t3.new_group_info_num
																	FROM transfer t3
																	WHERE t3.student_num = ps.student_num
																		AND t3.old_group_info_num = (SELECT t4.new_group_info_num
																									FROM transfer t4
																									WHERE t4.student_num = ps.student_num
																										AND t4.old_group_info_num = pg.group_info_num)))
							ELSE (SELECT gs2.created_date 
								FROM group_student gs2
								WHERE gs2.student_num = ps.student_num
									AND gs2.group_info_num = pg.group_info_num
								LIMIT 1)
						END), '%Y-%m-%d') AS created_date
					FROM student s
					INNER JOIN progress_student ps
						ON ps.student_num = s.student_num
					INNER JOIN progress_group pg
						ON pg.progress_group_num = ps.progress_group_num
							AND pg.created_date >= STR_TO_DATE('2020-01-01', '%Y-%m-%d')
							AND pg.created_date < STR_TO_DATE('2020-02-01', '%Y-%m-%d')
					INNER JOIN group_info gi
						ON gi.group_info_num = pg.group_info_num
							AND gi.block != 6
					INNER JOIN subject sj
						ON sj.subject_num = gi.subject_num
					WHERE s.block != 6
					ORDER BY s.surname, s.name, pg.created_date, gi.group_name";

		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		// var_dump($sql_result);
		$result = json_encode($sql_result);
		// echo "<br><br><br><br><br><br><br><br><br>";
		echo $result;

		// $result = array();

		// $subject_num = '';
		// $topic_num = '';
		// $subtopic_num = '';
		// foreach ($sql_result as $value) {
		// 	if ($subject_num != $value['subject_num']) {
		// 		$subject_num = $value['subject_num'];
		// 	}
		// }
		
	} catch (Exception $e) {
		throw $e;
	}
?>