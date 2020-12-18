<?php
	echo "Start<br><br><br><br><br>";
	include('../connection.php');

	$stmt = $conn->prepare("SELECT id, video_link FROM video where vimeo_link = 'y'");
	$stmt->execute();
	$result = $stmt->fetchAll();
	print_r($result);	
	echo "<br><br><br>";

	foreach ($result as $key => $value) {
		$curl = curl_init("https://vimeo.com/api/oembed.json?url=".$value['video_link']);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	    $out = curl_exec($curl);
	    curl_close($curl);
	    $res = json_decode($out, true)['duration'];

	    $hour = 0;
	    $minute = 0;
	    $second = 0;
	    $text_res = "";

	    $hour = intval($res / 3600);
	    $minute = intval(($res - $hour * 3600) / 60);
	    $second = intval($res - $hour * 3600 - $minute * 60);

	    $text_res .= $hour > 0 ? $hour.":" : "";
	    $text_res .= $minute > 0 ? $minute.":" : "";
	    $text_res .= $second > 10 ? $second : "0".$second;

	    // $stmt = $conn->prepare("UPDATE video SET timer = :timer WHERE id = :id");
	    // $stmt->bindParam(':id', $value['id'], PDO::PARAM_INT);
	    // $stmt->bindParam(':timer', $text_res, PDO::PARAM_STR);
	    // $stmt->execute();
	}
	echo "<br><br><br><br><br>END";
?>