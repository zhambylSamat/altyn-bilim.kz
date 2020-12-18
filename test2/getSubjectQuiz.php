<?php
	include_once '../connection.php';

	try {
		$query = "SELECT s.surname,
						s.name,
						t.topic_name,
						sj.subject_name,
						qm.mark_theory,
						qm.mark_practice,
						qm.created_date
					FROM quiz q,
						quiz_mark qm,
						topic t,
						subject sj,
						student s
					WHERE q.topic_num = t.topic_num
						AND sj.subject_num = t.subject_num
						AND qm.quiz_num = q.quiz_num
						AND qm.student_num = s.student_num
						AND s.block != 6
						AND s.student_num != 'US5985cba14b8d3100168809'
					ORDER BY s.surname, s.name, t.topic_name";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$result = json_encode($res);
		echo $result;
	} catch (Exception $e) {
		throw $e;
	}
?>
