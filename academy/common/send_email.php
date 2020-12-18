<?php
	include_once('emails.php');
	function send_email($subject, $from, $to, $body){
		GLOBAL $developer_mail;
	    $headers = "MIME-Version: 1.0" . "\r\n";
	    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	        // More headers
	    $headers .= 'From: '.$from."\r\n";
	    $headers .= 'Bcc: '.$developer_mail. "\r\n";
		
		if (mail($to,$subject,$body,$headers)) {
			return true;
		}
		return false;
	}
?>