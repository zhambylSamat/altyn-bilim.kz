<?php
    $data = array();
	if(isset($_GET['send'])){
        try {
            $message = "";

            $message .= "<h1>Клиент оставил заявку!</h1><br>";
            $message .= "<p>Имя: <b>".$_POST['name']."<b></p>";
            $message .= "<p>Телефон: <b>".$_POST['phone']."<b></p>";
            // $message .= "<p>Курс: <b>".$_POST['course']."<b></p>";
            // $message .= "<p>Класс: <b>".$_POST['class']."<b></p>";

            $to = "sciencetechgroup@gmail.com, almat.myrzabek@gmail.com, aruzhan.issabek@altyn-bilim.kz, aisulu.otegen@altyn-bilim.kz";
            $subject = "Request from altyn-bilim.kz";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: info@altyn-bilim.kz' . "\r\n";
            $send = false;
            if(mail($to,$subject,$message,$headers)){
                $send = true;
            }

            $data['success'] = $send;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['error'] .= "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
		
	}
	// echo $send;
    // echo "<br>";
    // echo $message;
    // echo "<br>";
    // print_r(error_get_last());
    // header("location:index.php?msg=".md5('ok'));
    // if($send){
	   // header("location:index.php?msg=".md5('ok'));
    // }
    // else{
        // header("location:index.php?msg=".md5('fail'));   
    // }
?>