<?php

$servername = "srv-pleskdb21.ps.kz:3306";
$username = "altyn_bilim";
$password = "glkR283*";
$dbname = "altynbil_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "UPDATE student_test_permission SET video_permission = 'f'";
if (mysqli_query($conn, $sql)) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);
?>