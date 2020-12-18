<?php
$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = $_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
 $dir .= $parts[$i] . "/";
}
echo $dir."<br><br>";
echo $url."<br><br>";
print_r($parts);
echo "<br><br>";
echo $_SERVER['SERVER_NAME']."<br><br>";
?>