<?php
	include_once '../connection.php';

	try {

		$query = "SELECT sj.subject_num,
						sj.subject_name,
						t.topic_num,
						t.topic_name,
						t.topic_order,
						t.quiz,
						st.subtopic_num,
						st.subtopic_name,
						st.subtopic_order,
						v.video_link,
						v.timer,
						(SELECT count(st1.subtopic_num)
						FROM subtopic st1
						WHERE st1.topic_num = t.topic_num) AS subtopic_count
					FROM subject sj
					LEFT JOIN topic t
						ON t.subject_num = sj.subject_num
							AND t.quiz = 'n'
					LEFT JOIN subtopic st
						ON st.topic_num = t.topic_num
					LEFT JOIN video v
						ON v.subtopic_num = st.subtopic_num
							AND v.vimeo_link = 'y'
					ORDER BY sj.subject_name, t.topic_order, st.subtopic_order";
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