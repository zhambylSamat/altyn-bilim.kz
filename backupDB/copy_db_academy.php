<?php
$servername = "srv-pleskdb21.ps.kz:3306";
$username = "altyn_online";
$password = "tMm680#h";
$name = "altynbil_online_academy";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $name = "altyn_bilim";

$link = mysqli_connect($servername,$username,$password,$name);
mysqli_query($link,'SET CHARACTER SET utf8'); 
mysqli_select_db($link,$name);

$tables = "*";
$return = "";
if($tables == '*')
{
	$tables = array();
	$result = mysqli_query($link,'SHOW TABLES');
	while($row = mysqli_fetch_row($result))
	{
		$tables[] = $row[0];
	}
}
else
{
	$tables = is_array($tables) ? $tables : explode(',',$tables);
}

foreach($tables as $table)
{
	$result = mysqli_query($link,'SELECT * FROM '.$table);
	$num_fields = mysqli_num_fields($result);
	
	$return.= 'DROP TABLE IF EXISTS '.$table.';';
	$row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
	$return.= "\n\n".$row2[1].";\n\n";
	
	for ($i = 0; $i < $num_fields; $i++) 
	{
		while($row = mysqli_fetch_row($result))
		{
			$return.= 'INSERT INTO '.$table.' VALUES(';
			for($j=0; $j < $num_fields; $j++) 
			{
				$row[$j] = addslashes($row[$j]);
				// $row[$j] = ereg_replace("\n","\\n",$row[$j]);
				$row[$j] = preg_replace('/\\\n/','\\\\\n',$row[$j]);
				if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
				if ($j < ($num_fields-1)) { $return.= ','; }
			}
			$return.= ");\n";
		}
	}
	$return.="\n\n\n";
}

$nme = 'db-backup-academy-'.date('Ymd').'-'.time().'-'.(md5(implode(',',$tables))).'.sql';
$handle = fopen($nme,'w+');
fwrite($handle,$return);
$date = date('d.m.Y');
$date = strtotime($date);
$date = strtotime("-1 month", $date);
$date = date('m', $date);
foreach (glob("db-backup-2017".$date."*.sql") as $filename) {
    unlink($filename);
}
fclose($handle);


?>