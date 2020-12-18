<?php
	$dob_arr = array();

	include_once('connection.php');
	include_once('emails.php');

	$sql1 = "SELECT name,
				surname,
				DATE_FORMAT(dob, '%d.%m.%Y') AS dob,
				teacher_num
			FROM teacher
			WHERE dob != '0000-00-00'
				AND dob != ''
				AND DATE_FORMAT(dob, '%m') = DATE_FORMAT(NOW(), '%m')
				AND (DATE_FORMAT(dob, '%d') = DATE_FORMAT(NOW(), '%d') OR DATE_FORMAT(DATE_SUB(dob, INTERVAL 1 DAY), '%d') = DATE_FORMAT(NOW(), '%d'))
				AND block != 6
			ORDER BY surname, name";

	$sql2 = "SELECT name,
				surname,
				DATE_FORMAT(dob, '%d.%m.%Y') AS dob,
				admin_num
			FROM admin
			WHERE dob != '0000-00-00'
				AND dob != ''
				AND DATE_FORMAT(dob, '%m') = DATE_FORMAT(NOW(), '%m')
				AND (DATE_FORMAT(dob, '%d') = DATE_FORMAT(NOW(), '%d') OR DATE_FORMAT(DATE_SUB(dob, INTERVAL 1 DAY), '%d') = DATE_FORMAT(NOW(), '%d'))
				AND block != 6
			ORDER BY surname, name";

	if (mysqli_query($conn, $sql1)) {
	    echo "Record select successfully";
	} else {
	    echo "Error updating record: sq1 " . mysqli_error($conn);
	}
	$result = mysqli_query($conn, $sql1);
	$rowcount=mysqli_num_rows($result);

	while($value=mysqli_fetch_assoc($result)) {
		$dob_arr[$value['teacher_num']]['surname'] = $value['surname'];
		$dob_arr[$value['teacher_num']]['name'] = $value['name'];
		$dob_arr[$value['teacher_num']]['dob'] = $value['dob'];
	}

	if (mysqli_query($conn, $sql2)) {
	    echo "Record select successfully";
	} else {
	    echo "Error updating record: sql2 " . mysqli_error($conn);
	}
	$result = mysqli_query($conn, $sql2);
	$rowcount +=mysqli_num_rows($result);

	while($value=mysqli_fetch_assoc($result)) {
		$dob_arr[$value['admin_num']]['surname'] = $value['surname'];
		$dob_arr[$value['admin_num']]['name'] = $value['name'];
		$dob_arr[$value['admin_num']]['dob'] = $value['dob'];
	}

	mysqli_close($conn);

	$html = "<h3>Туылған күндер тізімі (Бүгін немесе Ертең)!</h3>";
	$html .= "<table>";
	$html .= "<tr><th style='border:1px solid black;'>Әріптестеріміздің аты-жөні</th><th style='border:1px solid black;'>Туылған күні</th></tr>";
	foreach ($dob_arr as $value) {
		$html .= "<tr><td style='border:1px solid gray;'>".$value['surname']." ".$value['name']."</td><td style='border:1px solid gray;'>".$value['dob']."</td></tr>";
	}
	$html .= "</table>";
	
	$to = $admin_mail.', '.$moderator_mail.', '.$super_admin_mail;
    $subject = "Туылған күндер тізімі (Бүгін немесе Ертең)!";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
    $headers .= 'Bcc: '.$developer_mail. "\r\n";
    if ($rowcount > 0) {
    	if(mail($to,$subject,$html,$headers)){
    		echo "<br>Message sent successfully";
    	}
    }

    echo "<br><br>";
    echo $html;

?>