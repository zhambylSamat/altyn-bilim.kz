<?php
$servername = "srv-pleskdb21.ps.kz:3306";
$username = "altyn_bilim";
$password = "glkR283*";
$name = "altynbil_db";

// $servername = "srv-pleskdb21.ps.kz:3306";
// $username = "altyn_daily";
// $password = "A$c6et95";
// $name = "altynbil_daily";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $name = "altyn_bilim";

$conn = mysqli_connect($servername, $username, $password, $name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($conn,'SET CHARACTER SET utf8'); 
mysqli_select_db($conn,$name);

$sql = "DELETE FROM suggestion WHERE status = 2 AND date_format(last_changed_date,'%Y-%m-%d') < date_sub(curdate(), interval 29 day)";
if (mysqli_query($conn, $sql)) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);;
?>