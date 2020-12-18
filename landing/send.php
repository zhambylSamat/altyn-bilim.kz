<?php
	if(isset($_POST['submit'])){
		$message = "
	        <html>
	        <head>
	        <title>Altyn-bilim.kz</title>
	        </head>
	        <body>
	        <center><h3>Клиент отправил запрос</h3></center>";
	        $message.="<center><table>
	        <tr>
	        <th>Имя</th>
	        <th>Email</th>
	        <th>Номер телефона</th>
	        <th>Программа</th>
	        </tr>";

			$message.="<tr>
	        <td>
	        <h4>".$_POST['name']."</h4>
	        </td>
	       	<td>
	        <h4>".$_POST['phone']."</h4>
	        </td>
	        <td>
	        <h4>".$_POST['course']."</h4>
	        </td>
	        <td>
	        <h4>".$_POST['class']."</h4>
	        </td>
	        </tr>";
        $message.="</table></center></body></html>";

		$to = "zhambyl.9670@gmail.com";
        $subject = "Request from altyn-bilim.kz";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <altyn-bilim.kz>' . "\r\n";
        $send = 'false';
        if(mail($to,$subject,$message,$headers)){
        	$send = 'true';
        	$errorMessage = error_get_last()['message'];
        	echo "okey";
        }
        echo ini_get('display_errors');
        echo "fail";
		
	}
	echo $send;
    echo "<br>";
    echo $message;
    echo "<br>";
    print_r(error_get_last());
    echo "<br>";
    echo ";ksdf";
    echo mail($to,$subject,$message,$headers);
        // try {
        // 	
        // } catch (Exception $e) {
        // 	echo $e->getMessage();
        // }
	// header("location:index.php?status=".$send);
?>