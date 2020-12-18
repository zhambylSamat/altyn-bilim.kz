<?php
//
// A very simple PHP example that sends a HTTP POST to a remote site
//
// for($i = 0; $i <= 1; $i++){
	$ch = curl_init();

	// curl_setopt($ch, CURLOPT_URL,"http://almfb.biznesmolodost.kz/720249/leads/receive");
	curl_setopt($ch, CURLOPT_URL,"https://altyn-bilim.kz/landing/send.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS,
	            // "postvar1=value1&postvar2=value2&postvar3=value3");

	// in real life you should use something like:
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 
	//          http_build_query(array(
	//          	'frma5b90216e08248fa8627f293267b9199[fieldcef858e7496d48c689d69c8fedc72434]' => 'name',
	//          	'frma5b90216e08248fa8627f293267b9199[field3335821845764aac8f01d32a07e82f50]' => '+76054352244',
	//          	'frma5b90216e08248fa8627f293267b9199[field6843378ecf5047b0a21631b153c33e1c]' => 'em@i.l'
	//          )));

	curl_setopt($ch, CURLOPT_POSTFIELDS, 
	         http_build_query(array(
	         	'name' => 'test',
	         	'phone' => '+76054352244',
	         	'course' => 'course#1',
	         	'class' => '15'
	         )));

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec ($ch);
	echo $server_output;

	curl_close ($ch);
// }

?>

