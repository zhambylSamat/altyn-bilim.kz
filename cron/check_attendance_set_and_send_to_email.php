<?php
	$week_day = date('w');
	include_once('connection.php');
	include_once('emails.php');

	$sql = "SELECT gi.group_name,
				t.name, 
				t.surname,
				t.teacher_num
			FROM teacher t,
				group_info gi,
				schedule sch
			WHERE sch.group_info_num NOT IN (SELECT pg2.group_info_num 
											FROM progress_group pg2 
											WHERE DATE_FORMAT(pg2.created_date, '%d.%m.%Y') = DATE_FORMAT(NOW(), '%d.%m.%Y'))
				AND sch.group_info_num = gi.group_info_num
				AND gi.teacher_num = t.teacher_num
				
				AND (gi.group_name NOT LIKE '%прокачка%' 
					AND gi.group_name NOT LIKE '%Прокачка%'
					AND gi.group_name NOT LIKE '%Геометрия%'
					AND gi.group_name NOT LIKE '%геометрия')
				AND gi.block != 6
				AND 0 != (SELECT count(gs2.group_student_num) 
						FROM group_student gs2
						WHERE gs2.group_info_num = gi.group_info_num
							AND gs2.block != 6
							AND DATE_FORMAT(gs2.start_date, '%d.%m.%Y') <= DATE_FORMAT(NOW(), '%d.%m.%Y'))
				AND sch.week_id = ".$week_day."
			ORDER BY t.surname, t.name";

	if (mysqli_query($conn, $sql)) {
	    echo "Record select successfully";
	} else {
	    echo "Error updating record: " . mysqli_error($conn);
	}
	$result = mysqli_query($conn, $sql);
	$rowcount=mysqli_num_rows($result);
	$html = "<h3>Окушылардың сабаққа қатысуы белгіленбеген! (".date('d.m.Y').")</h3>";
	$html .= "<table>";
	$html .= "<tr><th style='border:1px solid black;'>Мұғалімнің аты-жөні</th><th style='border:1px solid black;'>Группасы</th></tr>";
	$group_and_teacher_name_list = "";
	$teacher_num = "";
	while($value = mysqli_fetch_assoc($result)){
		$html .= "<tr><td style='border:1px solid gray;'>".$value['surname']." ".$value['name']."</td><td style='border:1px solid gray;'>".$value['group_name']."</td></tr>";

		if ($teacher_num != $value['teacher_num']) {
			if ($teacher_num != '') {
				$group_and_teacher_name_list .= ";]&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			$group_and_teacher_name_list .= "[".$value['surname']." ".$value['name'].": '".$value['group_name']."'";
		} else if ($teacher_num = $value['teacher_num']) {
			$group_and_teacher_name_list .= ", '".$value['group_name']."'";
		}
		$teacher_num = $value['teacher_num'];
	}
	$html .= "</table>";
	mysqli_close($conn);
	$to = $admin_mail.', '.$moderator_mail.', '.$super_admin_mail; 
    $subject = "Окушылардың сабаққа қатысуын белгілемеген Мұғалімдер";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
    $headers .= 'Bcc: '.$developer_mail. "\r\n";
    echo $html;
    if ($rowcount > 0) {
    	if(mail($to,$subject,$html,$headers)){
    		echo "<br>Message sent successfully";
    	}
    }
    // print_r($html);
?>