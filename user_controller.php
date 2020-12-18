<?php
	include_once('connection.php');
	if(isset($_POST['signin'])){
		try {
			$username = mb_strtolower($_POST['username']);
			$password = $_POST['password'];
			$stmt = $conn->prepare("SELECT name_surname, phone FROM admin");
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->execute();
		   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
		   	$name_surname = $result['name_surname'];
		   	$phone = $result['phone'];
			$stmt = $conn->prepare("SELECT * FROM company WHERE username = :username");
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->execute();
		   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
		   	$count = $stmt->rowCount();
		   	if($count==1 && $result['default_password'] == 'default' && $result['password'] == md5($password)){
		   		$_SESSION['tmp_company_num'] = $result['company_num'];
		   		$_SESSION['tmp_company_name'] = $result['company_name'];
		   		$_SESSION['tmp_username'] = $result['username'];
		   		$_SESSION['tmp_owner_name'] = $result['owner_name'];
		   		$_SESSION['tmp_company_description'] = $result['company_description'];
		   		$_SESSION['tmp_name_surname'] = $name_surname;
		   		$_SESSION['tmp_phone'] = $phone;
		   		header('location:reset.php?data_num='.$result['company_num']);
		   	}
		   	else if($count==1 && $result['max_fail']<=4 && $result['password']==md5($password) && $result['default_password']=='default'){
		   		$stmt = $conn->prepare("UPDATE company SET default_password = 'notdefault' AND max_fail = 0 WHERE company_num = :company_num");
			    $stmt->bindParam(':company_num', $company_num, PDO::PARAM_INT);
			    $company_num = $result['company_num'];
			    $stmt->execute();
		   		$_SESSION['user_num'] = $result['company_num'];
		   		$_SESSION['username'] = $result['username'];
		   		$_SESSION['owner_name'] = $result['owner_name'];
		   		$_SESSION['company_name'] = $result['company_name'];
		   		$_SESSION['company_description'] = $result['company_description'];
		   		$_SESSION['name_surname'] = $name_surname;
		   		$_SESSION['phone'] = $phone;
		   		header("location:index.php");
		   	}
		   	else if($count==1 && $result['max_fail']<=4 && $result['password']==md5($password)){
		   		$stmt = $conn->prepare("UPDATE company SET max_fail = 0 WHERE company_num = :company_num");
			    $stmt->bindParam(':company_num', $result['company_num'], PDO::PARAM_INT);
			    $stmt->execute();
		   		$_SESSION['user_num'] = $result['company_num'];
		   		$_SESSION['username'] = $result['username'];
		   		$_SESSION['owner_name'] = $result['owner_name'];
		   		$_SESSION['company_name'] = $result['company_name'];
		   		$_SESSION['company_description'] = $result['company_description'];
		   		$_SESSION['name_surname'] = $name_surname;
		   		$_SESSION['phone'] = $phone;
		   		header("location:index.php");
		   	}
		   	else if($count == 1 && $result['max_fail']<=4 && $result['password'] != md5($password)){
		   		$stmt = $conn->prepare("UPDATE company SET max_fail = :max_fail WHERE company_num = :company_num");
			    $stmt->bindParam(':max_fail', $max_fail, PDO::PARAM_STR);
			    $stmt->bindParam(':company_num', $company_num, PDO::PARAM_INT);
			    $company_num = $result['company_num'];
			    $max_fail = (int)$result['max_fail']+1;
			    $stmt->execute();
			    $left = ($max_fail==5) ? "block" : (5-$max_fail);
			    $_SESSION['n'] = 'true';
			    header('location:signin.php?left='.$left."&name=".$result['username']);
		   	}
		   	else if($count==1 && $result['max_fail']>=5){
		   		$_SESSION['n'] = 'true';
		   		header('location:signin.php?blocked=true&name='.$result['username']);
		   	}
		   	else{
		   		$_SESSION['n'] = 'true';
		   		header('location:signin.php?noUser=true');
		   	}
		} catch (PDOException $e) {
			echo "Error : ".$e->getMessage()." !!!";
		}
	}
	if(isset($_POST['reset_user'])){
		try {
			if($_POST['password'] == $_POST['password_confirm'] && strlen($_POST['password'])>=6){
				$stmt = $conn->prepare("UPDATE company SET password = :password, default_password = 'notdefault' WHERE company_num = :company_num");
			    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
			    $stmt->bindParam(':company_num', $_SESSION['tmp_company_num'], PDO::PARAM_INT);
			    $password = md5($_POST['password']);
			    $stmt->execute();
			    $_SESSION['user_num']            = $_SESSION['tmp_company_num'];
		   		$_SESSION['company_name']        = $_SESSION['tmp_company_name'];
		   		$_SESSION['username']            = $_SESSION['tmp_username'];
		   		$_SESSION['owner_name']          = $_SESSION['tmp_owner_name'];
		   		$_SESSION['company_description'] = $_SESSION['tmp_company_description'];
		   		$_SESSION['name_surname']        = $_SESSION['tmp_name_surname'];
		   		$_SESSION['phone']               = $_SESSION['tmp_phone'];

		   		unset($_SESSION['tmp_company_num']);
		   		unset($_SESSION['tmp_company_name']);
		   		unset($_SESSION['tmp_username']);
		   		unset($_SESSION['tmp_owner_name']);
		   		unset($_SESSION['tmp_company_description']);
		   		unset($_SESSION['tmp_name_surname']);
		   		unset($_SESSION['tmp_phone']);
			    header('location:index.php');	
			}
			else if($_POST['password'] != $_POST['password_confirm']){
				$_SESSION['n'] = "true";
				header('location:reset.php?same=false');
			}
			else if(strlen($_POST['password'])<6){
				$_SESSION['n'] = "true";
				header("location:reset.php?length=false");
			}
		} catch (PDOException $e) {
			echo "Error : ".$e->getMessage()." !!!";
		}
	}
	if(isset($_POST['product_cart'])){
		try {
			$data_num = $_POST['product_num'];
			$data_volume = $_POST['product_volume'];
			$data_addon = $_POST['addon'];
			$data_subtotal = $_POST['subtotal'];
			$data_grandTotal = $_POST['grandTotal'];
			$comment = $_POST['comment'];

			$stmt = $conn->prepare("INSERT INTO cart (cart_num, company_num, deadline, comment) VALUES(:cart_num, :company_num, :deadline, :comment)");
   
		    $stmt->bindParam(':cart_num', $cart_num, PDO::PARAM_STR);
		    $stmt->bindParam(':company_num', $company_num, PDO::PARAM_STR);
		    $stmt->bindParam(':deadline', $deadline, PDO::PARAM_STR);
		    $stmt->bindParam(':comment',$comment, PDO::PARAM_STR);

		    $cart_num = uniqid('CART',true);
		    $company_num = $_SESSION['user_num'];
		    $deadline = $_POST['deadline'];
		       
		    $stmt->execute();

			$query = "INSERT INTO product_cart (product_cart_num, cart_num, product_num, volume) VALUES";
		    $qPart = array_fill(0, count($data_num), "(?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($data_num); $i++){
		    	$product_cart_num = uniqid('PCART', true);
		    	$stmtA->bindValue($j++, $product_cart_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $cart_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $data_num[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $data_volume[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		    $data['deadline'] = $deadline;
		    $data['volume'] = $data_volume;
		    $data['company_name'] = $_SESSION['company_name'];
		    $data['products_name'] = $_SESSION['products_name'];
		    $data['addon'] = $data_addon;
		    $data['subtotal'] = $data_subtotal;
		    $data['grandTotal'] = $data_grandTotal;
		    $data['comment'] = $comment;
		    sendMessageToEmail('create_order',$data);
		    unset($_SESSION['cart']);
		    unset($_SESSION['products_name']);
		    $_SESSION['n'] = 'true';
		    header('location:index.php?cart=success');
		} catch (PDOException $e) {
			echo "Error : ".$e->getMessage()." !!!";
		}
	}
	if(isset($_POST['edit_order'])){
		try {
			$stmt = $conn->prepare("UPDATE product_cart SET volume = :volume WHERE product_num = :product_num AND cart_num = :cart_num");
		    $stmt->bindParam(':cart_num', $_POST['cart_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':product_num', $_POST['data_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':volume', $_POST['new_product_volume'], PDO::PARAM_STR);
		    $stmt->execute();
		    // echo $_POST['cart_num']."<br>";
		    // echo $_POST['data_num']."<br>";
		    // echo $_POST['product_volume']."<br>";
		    // echo $_POST['new_product_volume']."<br>";
		    $_SESSION['n'] = 'true';
		    $data['company_name'] = $_SESSION['company_name']; 
		    $data['product_name'] = $_POST['product_name'];
		    $data['product_volume'] = $_POST['product_volume'];
		    $data['new_volume'] = $_POST['new_product_volume'];
		    sendMessageToEmail('edit_order_product',$data);
		    header('location:order_detail.php?edit=1&edit=true&data_num='.$_POST['cart_num']);	
		} catch (PDOException $e) {
			echo "Error : ".$e->getMessage()." !!!";
		}
	}
	if(isset($_POST['delete_product_order'])){
		try {
			$stmt = $conn->prepare("UPDATE product_cart SET deleted = 'y' WHERE cart_num = :cart_num AND product_num = :product_num");

			// $stmt = $conn->prepare("DELETE FROM product_cart WHERE cart_num = :cart_num AND product_num = :product_num");
			$stmt->bindParam(':cart_num',$_POST['cart_num'],PDO::PARAM_STR);
			$stmt->bindParam(':product_num',$_POST['data_num'],PDO::PARAM_STR);
			$stmt->execute();

			$stmt = $conn->prepare("SELECT * FROM product WHERE product_num = :product_num ");
			$stmt->bindParam(':product_num',$_POST['data_num'],PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			echo $_POST['data_num']."<br>";
			echo $stmt->rowCount()."<br>";
			$_SESSION['n'] = 'true';
			$data['product_name'] = $result['product_name'];
			$data['company_name'] = $_SESSION['company_name'];
			sendMessageToEmail('delete_order_product',$data);
		    header('location:order_detail.php?edit=1&edit=true&data_num='.$_POST['cart_num']);	
		} catch (PDOException $e) {
			echo "Error ".$e->getMessge()." !!!";
		}
	}
	if(isset($_POST['edit_deadline'])){
		try {
			$stmt = $conn->prepare("SELECT * FROM cart WHERE cart_num = :cart_num");
			$stmt->bindParam(':cart_num', $_POST['cart_num'],PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$stmt = $conn->prepare("UPDATE cart SET deadline = :deadline WHERE cart_num = :cart_num");
		    $stmt->bindParam(':cart_num', $_POST['cart_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':deadline', $_POST['deadline'], PDO::PARAM_STR);
		    $stmt->execute();

		    $_SESSION['n'] = 'true';
		    $data['from'] = $result['deadline'];
		    $data['to'] = $_POST['deadline'];
		    $data['company_name'] = $_SESSION['company_name'];
		    sendMessageToEmail('edit_order_deadline',$data);
		    header('location:index.php?edit=1&edit-cart=true');	
		} catch (PDOException $e) {
			echo "Error ".$e->getMessge()." !!!";
		}
	}
	if(isset($_POST['edit_comment'])){
		try {
			$stmt = $conn->prepare("UPDATE cart SET comment = :comment WHERE cart_num = :cart_num");
		    $stmt->bindParam(':cart_num', $_POST['cart_num'], PDO::PARAM_STR);
		    $stmt->bindParam(':comment', $_POST['comment'], PDO::PARAM_STR);
		    $stmt->execute();

		    $_SESSION['n'] = 'true';
		    $data['comment'] = $_POST['comment'];
		    $data['company_name'] = $_SESSION['company_name'];
		    sendMessageToEmail('edit_comment',$data);
		    header('location:order_detail.php?edit=1&edit=true&edit_comment=true&data_num='.$_POST['cart_num']);	
		} catch (PDOException $e) {
			echo "Error ".$e->getMessge()." !!!";
		}
	}
	if(isset($_POST['delete_order'])){
		try {
			$stmt = $conn->prepare("SELECT p.product_name name, pc.volume volume, p.price price, p.addon addon FROM product_cart pc, product p WHERE pc.cart_num = :cart_num AND p.product_num = pc.product_num");
			$stmt->bindParam(':cart_num', $_POST['data_num'],PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();

			$stmt = $conn->prepare("DELETE FROM product_cart WHERE cart_num = :cart_num");
			$stmt->bindParam(':cart_num',$_POST['data_num'],PDO::PARAM_STR);
			$stmt->execute();

			$stmt = $conn->prepare("DELETE FROM cart WHERE cart_num = :cart_num");
			$stmt->bindParam(':cart_num',$_POST['data_num'],PDO::PARAM_STR);

			$stmt->execute();

			$_SESSION['n'] = 'true';
			$data['company_name'] = $_SESSION['company_name'];
			$data['result'] = $result;
			// print_r($data);
			sendMessageToEmail('delete_order',$data);
		    header('location:index.php?edit-cart=1');	
		} catch (PDOException $e) {
			echo "Error ".$e->getMessge()." !!!";
		}
	}







	function sendMessageToEmail($status,$data){
		include('connection.php');
		// create_order edit_order_product delte_order_product edit_order_deadline delete_order
		$message = "che za";
		if($status=="create_order"){
			$data = array_sort($data,'products_name',SORT_ASC);
			$message = "
	        <html>
	        <head>
	        <title>Новый заказ!</title>
	        </head>
	        <body>
	        <center><h3>\"".$data['company_name']."\" создал новый заказ!</h3></center>
	        <center><h3>Срок заказа: <u>".date('d.m.Y',strtotime($data['deadline']))."</u>!</h3></center>";
	        $message.="<center><table>
	        <tr>
	        <th>Наименование товара</th>
	        <th>Объем</th>
	        <th>Цена</th>
	        <th>Сумма</th>
	        </tr>";    
			for ($i=0; $i < count($data['products_name']); $i++) { 
				$message.="<tr>
		        <td>
		        <h4>".$data['products_name'][$i]."</h4>
		        </td>
		        <td>
		        <h4>".$data['volume'][$i]." ".$data['addon'][$i]."</h4>
		        <td>
		        <h4>".number_format((floatval($data['subtotal'][$i])/floatval($data['volume'][$i])),2,'.','')." тг.</h4>
		        </td>
		        </td>
		        <td>
		        <h4>".$data['subtotal'][$i]." тг.</h4>
		        </td>
		        </tr>";
	    	}
	    	$comment = ($data['comment']!='') ? $data['comment'] : "N/A";
	    	$message .= "<tr><td colspan='3'>Итог: ".$data['grandTotal']." тг.</td></tr>";
	        $message.="</table></center>
	        <b>Примечание \ Комментарии:</b><br>
	        <p>".nl2br($comment)."</p>
	        </body>
	        </html>";
		}
		else if($status=="edit_order_product"){
			$message = "
	        <html>
	        <head>
	        <title>Заказ изменен!</title>
	        </head>
	        <body>
	        <center><h3>\"".$data['company_name']."\" изменил объем заказа!</h3></center>
	        <center><h3>Измененный товар: \"".$data['product_name']."\"</h3></center>
	        <center><h3>Объем с \"".$data['product_volume']."\" на ".$data['new_volume'].".</h3></center>";
	        $message.="</body></html>";
		}
		else if($status=="edit_comment"){
			$message = "
	        <html>
	        <head>
	        <title>Заказ изменен!</title>
	        </head>
	        <body>
	        <h3>\"".$data['company_name']."\": изменил \"Примечание \ Комментарии\"!</h3>
	        <h4>".nl2br($data['comment'])."</h4>";
	        $message.="</body></html>";
		}
		else if($status=="delete_order_product"){
			$message = "
	        <html>
	        <head>
	        <title>Заказчик удалил продукт из заказа!</title>
	        </head>
	        <body>
	        <center><h3>\"".$data['company_name']."\" удалил продукт из заказа!</h3></center>
	        <center><h3>Удаленный продукт: \"".$data['product_name']."\".</h3></center>";
	        $message.="</body></html>";
		}
		else if($status=="edit_order_deadline"){
			$message = "
	        <html>
	        <head>
	        <title>Заказчик изменил срок заказа!</title>
	        </head>
	        <body>
	        <center><h3>\"".$data['company_name']."\" изменил срок заказа!</h3></center>
	        <center><h3>Срок заказа изменен с <u>".date('d.m.Y',strtotime($data['from']))."</u> на <u>".date('d.m.Y',strtotime($data['to']))."</u> !</h3></center>";
	        $message.="</body></html>";
		}
		else if($status=="delete_order"){
			$data = array_sort($data,'name',SORT_ASC);
			$message = "
	        <html>
	        <head>
	        <title>Заказчик полностью удалил заказ!</title>
	        </head>
	        <body>
	        <center><h3>\"".$data['company_name']."\" полностью удалил заказ!</h3></center>";
	        $message.="<center><table>
	        <tr>
	        <th>Наименование товара</th>
	        <th>Объем</th>
	        <th>Цена</th>
	        <th>Сумма</th>
	        </tr>";
	        $grandTotal = 0;
			foreach ($data['result'] as $value) {
				$grandTotal += floatval($value['price'])*floatval($value['volume']);
				$message.="<tr>
		        <td>
		        <h4>".$value['name']."</h4>
		        </td>
		        <td>
		        <h4>".$value['volume']." ".$value['addon']."</h4>
		        </td>
		       	<td>
		        <h4>".$value['price']." тг.</h4>
		        </td>
		        <td>
		        <h4>".number_format((floatval($value['price'])*floatval($value['volume'])),2,'.','')." тг.</h4>
		        </td>
		        </tr>";
	    	}
	    	$grandTotal = number_format($grandTotal, 2, '.', '');
	    	$message .="<tr><td colspan='4'>Итог: ".$grandTotal." тг.</td></tr>";
	        $message.="</table></center></body></html>";
		}
		try {
			$stmt = $conn->prepare("SELECT * FROM admin");
			$stmt->execute();
		   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error : ".$e->getMessage()." !!!";
		}
		$to = $result['email'];
		// $to = "zhambyl.9670@gmail.com, nurbol.uka@gmail.com";
        $subject = "Request from snabzhenec.kz";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // echo $message;
        $headers .= 'From: <snabzhenec.kz>' . "\r\n";
        mail($to,$subject,$message,$headers);
        // if(mail($to,$subject,$message,$headers)){
        // 	echo "Message has been send";
        // }
        // else{
        // 	echo "Some error happened<br>";
        // 	print_r(error_get_last());
        // }
	}


	function array_sort($array, $on, $order=SORT_ASC){
	    $new_array = array();
	    $sortable_array = array();

	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}
?>