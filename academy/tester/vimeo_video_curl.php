<?php
	$link = 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/video/274218997';
	$ch = curl_init($link);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$out = curl_exec($ch);
	$result_json = json_decode($out, true);

	echo "link: ".$link."<br><br>json: ";
	print_r($result_json);
	echo "<br>out --->>".$out;

	curl_close($ch);
?>
