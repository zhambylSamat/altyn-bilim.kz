<?php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : 'admin'; 
session_unset();
header('location:sign_in.php');
?>