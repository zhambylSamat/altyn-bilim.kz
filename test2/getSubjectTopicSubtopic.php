<?php
include_once '../connection.php';

try {
	$stmt = $conn->prepare("SELECT sj.subject_num as snum,
								sj.subject_name as sname, 
								t.topic_num as tnum,
								t.topic_name as tname,
								st.subtopic_num as stnum,
								st.subtopic_name as stname
							FROM subject sj,
								topic t,
								subtopic st
							WHERE t.subject_num = sj.subject_num
								AND st.topic_num = t.topic_num
								AND t.quiz = 'n'
							ORDER BY sj.subject_name, t.topic_order, st.subtopic_order");
	$stmt->execute();
	$result = $stmt->fetchAll();

	$sub_result = array();
	foreach ($result as $val) {
		$sub_result[$val['snum']]['subject_name'] = $val['sname'];
		$sub_result[$val['snum']]['topics'][$val['tnum']]['topic_name'] = $val['tname'];
		$sub_result[$val['snum']]['topics'][$val['tnum']]['subtopics'][$val['stnum']]['subtopic_name'] = $val['stname'];
	}

	$total_result = array();
	foreach ($sub_result as $subject) {
		$subject_arr = array();
		$subject_arr['subject_name'] = $subject['subject_name'];
		$subject_arr['topics'] = array();
		foreach ($subject['topics'] as $topic) {
			$topic_arr = array();
			$topic_arr['topic_name'] = $topic['topic_name'];
			$topic_arr['subtopics'] = array();
			foreach ($topic['subtopics'] as $subtopic) {
				array_push($topic_arr['subtopics'], $subtopic['subtopic_name']);
			}
			array_push($subject_arr['topics'], $topic_arr);
		}
		array_push($total_result, $subject_arr);
	}
	echo json_encode($total_result);
} catch (PDOException $e) {
	throw $e;
}
?>