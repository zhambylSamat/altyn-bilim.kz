<?php
 
// $servername = "srv-pleskdb21.ps.kz:3306";
// $username = "altyn_game";
// $password = "qoK9w30~";
// $dbname = "altynbil_game";
// $link = 'https://altyn-bilim.kz/';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game";
$link = 'http://localhost/altynbilim/';


try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->exec("set names utf8");	
} catch (PDOException $e) {
	echo "Error ".$e->getMessage()." !!!";
}
// session_set_cookie_params(0);
date_default_timezone_set("Asia/Almaty");
mb_internal_encoding("UTF-8");
if(!isset($_SESSION)) 
{
	session_start();
	$_SESSION['link'] = $link;
}
?>