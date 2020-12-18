<?php
// $servername = "srv-db-plesk09.ps.kz:3306";
// $username = "woodl_altynbilim";
// $password = "ps_db_123";
// $dbname = "woodland_altynbilim";

//prod
// $servername = "srv-pleskdb21.ps.kz:3306";
// $username = "altyn_bilim";
// $password = "glkR283*";
// $dbname = "altynbil_db";

// preprod
// $servername = "srv-pleskdb21.ps.kz:3306";
// $username = "altyn_preprod";
// $password = "i_wj5C44";
// $dbname = "altynbil_preprod";
 
$servername = "localhost";
$username = "root";
$password = "";
// $password = "zhambylsamat";
$dbname = "altyn_bilim";

// $servername = "srv-pleskdb19.ps.kz:3306";
// $username = "byoth_admin";
// $password = "1Ox#zu58";
// $dbname = "byotheak_cosmetic";


// email
// password
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// $mysqli = new mysqli("localhost", "root", "", $dbname);
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
}
if(!isset($_SESSION['n'])){
	$_SESSION['n'] = 'false';
}
if(!isset($_SESSION['page'])){
	$_SESSION['page'] = '';
}
?>