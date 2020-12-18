<?php 
include_once '../connection.php';
if(isset($_POST['signIn'])){
	try {
		$stmt = $conn->prepare("SELECT * FROM student WHERE username = :username AND password = :password");
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$username = $_POST['username'];
		$password = md5($_POST['password']); 
	    $stmt->execute();
	    $result = $stmt->fetchAll();
	    $count = 0;
	    foreach($result as $readrow){
	    	if(isset($readrow['student_num'])){
	    		$client  = @$_SERVER['HTTP_CLIENT_IP'];
			    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			    $remote  = $_SERVER['REMOTE_ADDR'];
			    $is = '';
			    if(filter_var($client, FILTER_VALIDATE_IP))
			    {
			        $ip = $client;
			    }
			    elseif(filter_var($forward, FILTER_VALIDATE_IP))
			    {
			        $ip = $forward;
			    }
			    else
			    {
			        $ip = $remote;
			    }
			    $stmt = $conn->prepare("INSERT INTO user_connection_tmp (student_num, ip) VALUES(:student_num, :ip)");
   
			    $stmt->bindParam(':student_num', $readrow['student_num'], PDO::PARAM_STR);
			    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
			       
			    $stmt->execute();
			    $_SESSION['student_num'] = $readrow['student_num'];

			    $_SESSION['ip_address'] = $ip;
	    		header('location:check_mac.php');
	    	}
	    	else{
	    		// header('location:index.php');
	    	}
	    } 
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
}
?>