<?php

include_once("../../connection.php"); // altyn-bilim
include_once("../common/connection.php"); // academy

$stmt = $conn->prepare("SELECT s.subject_num,
							s.subject_name,
							t.topic_num,
							t.topic_name,
							t.topic_order,
							st.subtopic_num,
							st.subtopic_name,
							st.subtopic_order,
							v.video_link
						FROM subject s,
							topic t,
							subtopic st
						LEFT JOIN video v
						ON v.subtopic_num = st.subtopic_num
							AND v.vimeo_link = 'y'
						WHERE t.subject_num = s.subject_num
							AND st.topic_num = t.topic_num
							AND t.quiz = 'n'
						ORDER BY s.subject_name, t.topic_order, st.subtopic_order");
$stmt->execute();
$content_res = $stmt->fetchAll();
$content_count = $stmt->rowCount();

echo "<table>";
foreach ($content_res as $value) {
	echo "<tr>";
	echo "<td>Subject: ".$value['subject_name']."</td><td>Topic: (".$value['topic_order'].") ".$value['topic_name']."</td><td>Subtopic: (".$value['subtopic_order'].") ".$value['subtopic_name']."</td><td> video link: ".$value['video_link']."</td>";
	echo "</tr>";
}
echo "</table>";

$total_res = array();
$subject_num = '';
$topic_num = '';
$subtopic_num = '';
foreach ($content_res as $value) {

	if ($subject_num != $value['subject_num']) {
		$subject_num = $value['subject_num'];
		$tmp_arr = array('name' => $value['subject_name'], 'topics' => array());
		array_push($total_res, $tmp_arr);
	}

	$subject_index = count($total_res) - 1;
	if ($topic_num != $value['topic_num']) {
		$topic_num = $value['topic_num'];
		$tmp_arr = array('name' => $value['topic_name'], 'subtopics' => array());
		array_push($total_res[$subject_index]['topics'], $tmp_arr);
	}

	$topic_index = count($total_res[$subject_index]['topics']) - 1;
	if ($subtopic_num != $value['subtopic_num']) {
		$subtopic_num = $value['subtopic_num'];
		$tmp_arr = array('name' => $value['subtopic_name'], 'videos' => array());
		array_push($total_res[$subject_index]['topics'][$topic_index]['subtopics'], $tmp_arr);
	}

	$subtopic_index = count($total_res[$subject_index]['topics'][$topic_index]['subtopics']) - 1; 
	if ($value['video_link'] != '') {
		array_push($total_res[$subject_index]['topics'][$topic_index]['subtopics'][$subtopic_index]['videos'], $value['video_link']);
	}

}

if (false) {
	foreach ($total_res as $s_val) {
		$stmt = $connect->prepare("INSERT INTO subject (title) VALUES (:title)");
		$stmt->bindParam(":title", $s_val['name'], PDO::PARAM_STR);
		$stmt->execute();
		$subject_id = $connect->lastInsertId();

		$topic_order = 0;
		foreach ($s_val['topics'] as $t_val) {
			$topic_order++;
			$stmt = $connect->prepare("INSERT INTO topic (subject_id, title, topic_order) VALUES (:subject_id, :title, :topic_order)");
			$stmt->bindParam(":subject_id", $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(":title", $t_val['name'], PDO::PARAM_STR);
			$stmt->bindParam(":topic_order", $topic_order, PDO::PARAM_INT);
			$stmt->execute();
			$topic_id = $connect->lastInsertId();

			$subtopic_order = 1;
			$stmt = $connect->prepare("INSERT INTO subtopic (topic_id, title, subtopic_order) VALUE (:topic_id, 'Кіріспе', :subtopic_order)");
			$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(":subtopic_order", $subtopic_order, PDO::PARAM_INT);
			$stmt->execute();
			foreach ($t_val['subtopics'] as $st_val) {
				$subtopic_order++;
				$stmt = $connect->prepare("INSERT INTO subtopic (topic_id, title, subtopic_order) VALUES (:topic_id, :title, :subtopic_order)");
				$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
				$stmt->bindParam(":title", $st_val['name'], PDO::PARAM_STR);
				$stmt->bindParam(":subtopic_order", $subtopic_order, PDO::PARAM_INT);
				$stmt->execute();
				$subtopic_id = $connect->lastInsertId();

				$video_order = 0;
				foreach ($st_val['videos'] as $link) {

					$video_order++;
					$stmt = $connect->prepare("INSERT INTO tutorial_video (subtopic_id, link, video_order) VALUES (:subtopic_id, :link, :video_order)");
					$stmt->bindParam(":subtopic_id", $subtopic_id, PDO::PARAM_INT);
					$stmt->bindParam(":link", $link, PDO::PARAM_STR);
					$stmt->bindParam(":video_order", $video_order, PDO::PARAM_INT);
					$stmt->execute();
				}

			}

			$subtopic_order++;
			$stmt = $connect->prepare("INSERT INTO subtopic (topic_id, title, subtopic_order) VALUE (:topic_id, 'Қорытынды', :subtopic_order)");
			$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(":subtopic_order", $subtopic_order, PDO::PARAM_INT);
			$stmt->execute();
		}
	}
}

?>

<script type="text/javascript" src='../../js/jquery.js'></script>
<script type="text/javascript">
	function ok($link) {
		$res = [];
		$.ajax({
		    	url: $link,
				type: "GET",
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function($data){
					$res = $data;
			    },
			  	error: function($dataS) 
		    	{
		    		console.log($dataS);
		    	} 	     
		   	});
		return $res;
	}
</script>