<?php
	$ini_PostSize = preg_replace("/[^0-9,.]/", "", ini_get('post_max_size'))*(1024*1024);
	$ini_FileSize = preg_replace("/[^0-9,.]/", "", ini_get('upload_max_filesize'))*(1024*1024);
	$maxFileSize = ($ini_PostSize<$ini_FileSize ? $ini_PostSize : $ini_FileSize);
	echo $maxFileSize;
?>