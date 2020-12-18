<?php
	include_once('../common/connection.php');

	try {

		$stmt = $connect->prepare("SELECT rr.student_id,
										rr.topic_id,
										st.id,
										st.title
									FROM registration_reserve rr,
										subtopic st
									WHERE st.topic_id = rr.topic_id
										AND st.subtopic_order = 2
										AND is_done = 0
									ORDER BY rr.id");
		$stmt->execute();
		$sql_res = $stmt->fetchAll();
		$datas = array();
		$html = "";
		foreach ($sql_res as $value) {
			array_push($datas, array('student_id' => $value['student_id'],
									'topic_id' => $value['topic_id'],
									'subtopic_id' => $value['id'],
									'subtopic_title' => $value['title']));
			$html .= $value['student_id'].' - '.$value['topic_id'].' - '.$value['id'].' - '.$value['title']."<br><br>";
		}
		echo json_encode($datas);
		echo "<br><br><br>";
		echo $html;


	} catch(Exception $e) {
		throw $e;
	}
?>