<?php
	if(!isset($_SESSION)) { 
	    session_start();
	}
	if(isset($_POST['submit'])){
		$send = 'false';
		$program = isset($_POST['program']) ? $_POST['program'] : '';
		switch ($program) {
		    case "algebra":
		        $program = "Алгебра";
		        break;
		    case "geometry":
		        $program = "Геометрия";
		        break;
		    case "math_intensive":
		        $program =  "Математика (Алгебра + Геометрия)";
		        break;
		    case "phys":
		        $program =  "Физика";
		        break;
		    case "math_phys":
		        $program =  "Математика + Физика";
		        break;
	        case "math_intensive_phys":
		        $program =  "Метематика интенсив + Физика";
		        break;
	        case "math_simple":
		        $program =  "Математикалық сауаттылық";
		        break;
	        case "individual":
		        $program =  "Индивидуалдық оқу";
		        break;
		}
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
	        <h4>".$_POST['email']."</h4>
	        </td>
	       	<td>
	        <h4>".$_POST['phone']."</h4>
	        </td>
	        <td>
	        <h4>".$program."</h4>
	        </td>
	        </tr>";
        $message.="</table></center></body></html>";

		$to = "zhambyl.9670@gmail.com, almat.myrzabek@gmail.com, sciencetechgroup@gmail.com";
        $subject = "Request from altyn-bilim.kz";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <altyn-bilim.kz>' . "\r\n";

        if(mail($to,$subject,$message,$headers)){
        	$send = 'true';
        }
        if(!isset($_SESSION['n'])){
        	$_SESSION['n'] = 'false';
        }
        $_SESSION['n'] = 'true';
		header("location:".$_POST['from'].'?send='.$send);
	}
?>