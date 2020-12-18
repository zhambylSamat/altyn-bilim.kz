<?php
include_once('../connection.php');
echo "string";
$stmt = $conn->prepare("DELETE FROM user_connection_tmp WHERE student_num = :student_num");

$stmt->bindParam(':student_num',$_SESSION['student_num'],PDO::PARAM_STR);

$stmt->execute();
// echo "<h1>".$_SESSION['student_num']."</h1>";
header('location:index.php');
?>