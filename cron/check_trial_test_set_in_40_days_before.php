<?php
	include_once('connection.php');
	include_once('emails.php');

	$student_nums = "";
	$subject_nums = "";
	$sql = "SELECT s.student_num, 
				s.surname,
			    s.name,
			    sj.subject_num,
			    sj.subject_name
			FROM trial_test tt,
				student s,
			    subject sj,
                group_student gs,
               	group_info gi
			WHERE DATE_SUB(NOW(), INTERVAL 40 DAY) >= (SELECT ttm1.date_of_test
			                                         FROM trial_test_mark ttm1
			                                         WHERE ttm1.trial_test_num = tt.trial_test_num
			                                         ORDER BY ttm1.date_of_test DESC
			                                         LIMIT 1)
			    AND (gs.unblocked_date IS NULL OR DATE_SUB(NOW(), INTERVAL 40 DAY) >= gs.unblocked_date)
				AND s.block != 6
				AND s.student_num NOT IN ('US5985cba14b8d3100168809')
                AND gs.student_num = s.student_num
                AND gs.block != 6
                AND gs.group_info_num = gi.group_info_num
                AND gi.subject_num = sj.subject_num
			    AND tt.student_num = s.student_num
			    AND tt.subject_num = sj.subject_num
			ORDER BY sj.subject_name, s.surname, s.name DESC";

	if (mysqli_query($conn, $sql)) {
	    echo "Record select successfully";
	} else {
	    echo "Error updating record: " . mysqli_error($conn);
	}
	$result = mysqli_query($conn, $sql);
	$rowcount=mysqli_num_rows($result);
	$html = "<h3>Соңғы 40 күнде пробный тесті енгізілмеген оқушылар! (".date('d.m.Y').")</h3>";
	$html .= "<table>";
	$html .= "<tr><th style='border:1px solid black;'>Оқушының аты-жөні</th><th style='border:1px solid black;'>Пәні</th></tr>";
	while($value = mysqli_fetch_assoc($result)){
		$html .= "<tr><td style='border:1px solid gray;'>".$value['surname']." ".$value['name']."</td><td style='border:1px solid gray;'>".$value['subject_name']."</td></tr>";
		$student_nums .= "'".$value['student_num']."', ";
		$subject_nums .= "'".$value['subject_num']."', ";
	}
	$student_nums = rtrim($student_nums, ', ');
	$subject_nums = rtrim($subject_nums, ', ');
	$html .= "</table>";

	$sql = "SELECT DISTINCT sj.subject_name, t.surname, t.name
			FROM group_info gi,
				teacher t,
                group_student gs,
               	subject sj
			WHERE gi.subject_num IN (".$subject_nums.")
				AND gi.teacher_num = t.teacher_num
			    AND gi.subject_num not in ('S5985a7ea3d0ae721486338')
			    AND gi.block != 6
			    AND t.block != 6
                AND sj.subject_num = gi.subject_num
                AND gs.group_info_num = gi.group_info_num
                AND gs.student_num IN (".$student_nums.")
			ORDER BY sj.subject_name, t.surname, t.name";
	if (mysqli_query($conn, $sql)) {
	    echo "Record select successfully";
	} else {
	    echo "Error updating record: " . mysqli_error($conn);
	}
	$result = mysqli_query($conn, $sql);
	$html .= "<br><br><h3>Пән және мұғалімдер</h3>";
	$html .= "<table>";
	$html .= "<tr><th style='border:1px solid black;'>Пәннің аты</th><th style='border:1px solid black;'>Мұғалімнің аты-жөні</th></tr>";
	while($value = mysqli_fetch_assoc($result)){
		$html .= "<tr><td style='border:1px solid gray;'>".$value['subject_name']."</td><td style='border:1px solid gray;'>".$value['surname']." ".$value['name']."</td></tr>";
	}
	$html .= "</table>";
	mysqli_close($conn);
	print_r($html);
	$to = $admin_mail.', '.$moderator_mail.', '.$super_admin_mail;
    $subject = "Соңғы 40 күнде пробный тесті енгізілмеген оқушылар!";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
    $headers .= 'Bcc: '.$developer_mail. "\r\n";
    if ($rowcount > 0) {
    	if(mail($to,$subject,$html,$headers)){
    		echo "<br>Message sent successfully";
    	}
    }

?>