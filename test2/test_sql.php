<?php
	include_once("../connection.php");
	try {
		$stmt = $conn->prepare("SELECT q.quiz_num, q.created_date, t.topic_num, t.topic_name, s.student_num, s.surname, s.name, qm.mark, q.max_mark 
	FROM quiz q, quiz_tail qt, quiz_mark qm, topic t, student s 
    	WHERE q.group_info_num = 'GI599ea6883fe9c4.64853275' AND q.quiz_num = qt.quiz_num AND qt.topic_num = t.topic_num AND q.quiz_num = qm.quiz_num AND qm.student_num = s.student_num");
    	$stmt->execute();
    	$result = $stmt->fetchAll();
    	print_r($result);
	} catch (PDOException $e) {
		echo "ERROR: ".$e->getMessage()." !!!";
	}
?>