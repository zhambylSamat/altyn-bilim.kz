<?php
include_once('../connection.php');
if(isset($_GET['data'])){
	$mmm = explode(",",$_GET['data']);
	print_r($mmm);
	echo "<br><br>";
	try {
		$stmt = $conn->prepare("SELECT * FROM device_mac_address");
		     
	    $stmt->execute();
	    $result_mac = $stmt->fetchAll(); 
	    print_r($result_mac);
	    $count = 0;
	    foreach ($result_mac as $value) {
	    	foreach ($mmm as $mValue) {
	    		if($mValue!='' && $mValue == $value['mac_address']){
	    			$count++;
	    			$_SESSION[md5(md5("mac"))] = md5(md5('ok'));
	    			break;
	    		}
	    	}
	    }
	    if($count!=2){
	    	$_SESSION[md5(md5("mac"))] = md5(md5('false'));
	    }
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
?>