<?php
	include_once("../connection.php");

	$data = array();

	$position = $_POST['position'];
	$lat = (float) $position['coords']['latitude'];
	$long = (float) $position['coords']['longitude'];

	$name_id = "ab_sary_arka";

	$bd_lat = 0.0;
	$bd_long = 0.0;	
	$data['geo_access'] = false;
	$data['content'] = "<center><h3>Видео не доступно</h3></center>";

	try {

		$stmt = $conn->prepare("SELECT latitude, longitude
								FROM ab_location
								WHERE name_id = :name_id");

		$stmt->bindParam(':name_id', $name_id, PDO::PARAM_STR);
	    $stmt->execute();
	    $res = $stmt->fetch(PDO::FETCH_ASSOC);

	    $bd_lat = (float) $res['latitude'];
	    $bd_long = (float) $res['longitude'];

	    if($lat>=$bd_lat-0.105 && $lat<=$bd_lat+0.105 && $long>=$bd_long-0.1005 & $long<=$bd_long+0.1005){
	    	$data['geo_access'] = true;
	    	$data['content'] = "lesson.php";
	    }
	    else{
	    	$data['geo_access'] = false;
	    	$data['content'] = "<center><h3>Видео не доступно</h3></center>";
	    }
	} catch (PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error : ".$e->getMessage()." !!!";
	}
	echo json_encode($data);
?>