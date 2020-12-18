<?php

	include_once('connection.php');
	include_once('emails.php');

	$sql = "SELECT count(id) AS c FROM sms_history WHERE status = 'waiting_for_send'";
	
	if (mysqli_query($conn, $sql)) {
	    echo "Record select successfully<br><br>";
	} else {
	    echo "Error updating record: " . mysqli_error($conn)."<br><br>";
	}
	$result_sql = mysqli_query($conn, $sql);
	$value = mysqli_fetch_assoc($result_sql);
	// $rowcount=mysqli_num_rows($result_sql);

	if ($value['c'] > 0) {

		$html = "<b><h3>".$value['c']." SMS жіберілуі күтілуде</h3></b>";
		// $to = "zhambyl.9670@gmail.com, aisulu.otegen@altyn-bilim.kz, almat.myrzabek@gmail.com"; 
		$to = $admin_mail;
	    $subject = "SMS жіберілуі күтілуде: ".$value['c'];
	    $headers = "MIME-Version: 1.0" . "\r\n";
	    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	        // More headers
	    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
	    $headers .= 'Bcc: '.$developer_mail. "\r\n";
    	if (mail($to,$subject,$html,$headers)) {
    		echo "<br>Message sent successfully";
    	}
	}
?>