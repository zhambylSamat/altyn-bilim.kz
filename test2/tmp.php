<?php
	
	$arr = array("content" => array(array('test_num' => "abc", "test_name" => 'abc_txt'), array("test_num" => "123", "test_name" => "123_txt")));
	print_r($arr);

	echo "<br><br>";

	if (in_array(array("test_num" => "abc"), $arr['content'], TRUE)){
		echo "in_array";
	} else {
		echo "not_in_array";
	}

?>