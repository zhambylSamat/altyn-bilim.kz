<?php
	include_once('../common/connection.php');

	$query = "SELECT id FROM `end_video_timecode` where title = :title ORDER BY title";
	for ($i = 1; $i <= 59; $i++) {
		$stmt = $connect->prepare($query);
		$title = $i.'-тапс.';
		$new_title = '№'.$i;
		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
		$stmt->execute();
		$query_result = $stmt->fetchAll();

		$result = array();

		foreach ($query_result as $value) {
			array_push($result, $value['id']);
		}

		if (count($result) > 0) {
			$result_str = implode(',', $result);
			echo "UPDATE `end_video_timecode` SET title = '".$new_title."' where id IN (".$result_str.");<br>";
		}
	}
?>