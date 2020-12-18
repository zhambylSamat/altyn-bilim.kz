<?php
$ini_PostSize = preg_replace("/[^0-9,.]/", "", ini_get('post_max_size'))*(1024*1024);
$ini_FileSize = preg_replace("/[^0-9,.]/", "", ini_get('upload_max_filesize'))*(1024*1024);
$maxFileSize = ($ini_PostSize<$ini_FileSize ? $ini_PostSize : $ini_FileSize);
echo $ini_PostSize."<br>";
echo ($ini_FileSize)." MByte<br>";
echo (10*1024*1024)."<br>";
echo $maxFileSize."   byte<br>";
// $fileName = $_FILES["file1"]["name"]; // The file name
// $fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
// $fileType = $_FILES["file1"]["type"]; // The type of file it is
// $fileSize = $_FILES["file1"]["size"]; // File size in bytes
// $fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
// echo $fileTmpLoc." ---------";
// if (!$fileTmpLoc) { // if file not chosen
//     echo "ERROR: Please browse for a file before clicking the upload button.";
//     exit();
// }
// if(move_uploaded_file($fileTmpLoc, "test_uploads/$fileName")){
//     echo "$fileName upload is complete";
// } else {
//     echo "move_uploaded_file function failed";
// }

?>
