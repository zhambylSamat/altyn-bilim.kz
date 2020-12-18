<?php 
	if(!isset($_SESSION)) {
		session_start();
	} 
	function ch($el){
		if(is_array($el)) {
			ch($el);
		} 
		else {
			echo str_replace("<br>", " ", str_replace("\r\n", " ", $el));
		}
	} 
	foreach ($_SESSION as $key => $value) { 
		echo "KEY: "; 
		echo str_replace("<br>", " ", str_replace("\r\n", " ", $key)); 
		echo "VALUES: "; 
		ch($value); 
	} 
?>



	$(document).on('click',"#abc",function(){
		console.log("asdfasdfasdfasd");
		console.log("1");
    	$('#abc2').html('<?php if(!isset($_SESSION)) session_start(); echo json_encode($_SESSION); ?>');
     	console.log('<?php if(!isset($_SESSION)) session_start(); echo json_encode($_SESSION); ?>');
	});
