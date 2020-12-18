<?php
	include_once('connection.php');
	include_once('emails.php');


	$html = "<html><body>";
	$html .= "<style>
				td {
					border: 1px solid lightgray;
					padding:5px;
				}
				tr {
					height: 20px;
				}
			</style>";

	$month_list = array(1 => "Қаңтар",
						2 => "Ақпан",
						3 => "Наурыз",
						4 => "Сәуір",
						5 => "Мамыр",
						6 => "Маусым",
						7 => "Шілде",
						8 => "Тамыз",
						9 => "Қыркүйек",
						10 => "Қазан",
						11 => "Қараша",
						12 => "Желтоқсан");

	$polled_month = $month_list[intval(date('m', strtotime("-1 month", strtotime(date('d-m-Y')))))];
	// $polled_month = $month_list[intval(date('m'))];

	$html .= "<h2>Сауалнанма өткізілген ай: ".$polled_month."</h2>";
	// $current_day = intval(date('d'));
	$start_day = 25;
	$end_day = 10;
	$start_date = "";
	$end_date = "";
	// if ($current_day >= $start_day) {
		// $start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
		// $end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
		// $is_active = true;
	// } else if ($current_day <= $end_day) {
	$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
	$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
	// }
	$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));

	$sql1 = "SELECT s.student_num,
				s.surname s_surname,
				s.name s_name,
				s.phone,
				t.teacher_num,
				t.surname t_surname,
				t.name t_name,
				sp.id sp_id,
				sps.id sps_id,
				sps.mark,
				tpi.id tpi_id,
				tpi.text
			FROM student_poll sp,
				student_polls sps,
				student s,
				teacher t,
				teacher_poll_info tpi
			WHERE DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE('$start_date', '%d-%m-%Y')
				AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE('$end_date', '%d-%m-%Y')
				AND sps.student_poll_id = sp.id
				AND tpi.id = sps.teacher_poll_info_id
				AND t.teacher_num = sp.teacher_num
				AND s.student_num = sp.student_num
			ORDER BY s.student_num, t.teacher_num, sp.id, sps.id, tpi.id";

	$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi.group_info_num
                                    	AND tr2.student_num = gs.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
	$sql2 = "SELECT DISTINCT s.student_num,
				s.name s_name,
				s.surname s_surname,
				s.phone,
				t.teacher_num,
				t.name t_name,
				t.surname t_surname
			FROM student s,
				group_student gs,
				group_info gi,
				teacher t
			WHERE s.block != 6
				AND gs.student_num = s.student_num
				AND gs.block != 6
				AND gi.group_info_num = gs.group_info_num
				AND gi.block != 6
				AND t.teacher_num = gi.teacher_num
				AND gi.subject_num != 'S5985a7ea3d0ae721486338'
				AND STR_TO_DATE('$poll_activate_days', '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')
				AND 0 = (SELECT count(sp2.id)
						FROM student_poll sp2
						WHERE DATE_FORMAT(sp2.polled_date, '%Y-%m-%d') >= STR_TO_DATE('$start_date', '%d-%m-%Y')
							AND DATE_FORMAT(sp2.polled_date, '%Y-%m-%d') <= STR_TO_DATE('$end_date', '%d-%m-%Y')
							AND sp2.teacher_num = t.teacher_num
							AND sp2.student_num = s.student_num)
			ORDER BY s.surname, s.name, t.surname, t.name";

	$sql3 = "SELECT tpi.id,
					tpi.text
			FROM teacher_poll_info tpi
			ORDER BY tpi.text";

	$poll_text = array();
	$default_teacher_avg_result = array();
	$tpi_html = "<table>";
	if ($result = mysqli_query($conn, $sql3)) {
		while ($value = mysqli_fetch_assoc($result)) {
			$poll_text[$value['id']] = $value['text'];
			$default_teacher_avg_result[$value['id']] = array("full_text" => $value['text'],
																"sum_mark" => 0,
																"count" => 0);
			$tpi_html .= "<tr><td>".$value['text']."</td></tr>";
		}
	}
	$tpi_html .= "</table>";
	
	if ($result = mysqli_query($conn, $sql2)) {
		$rowcount = mysqli_num_rows($result);
		$html .= "<h3>Сауалнаманы белгілемеген оқушылар саны: ".$rowcount."</h3><ol>";
		$student_num = "";
		while ($value = mysqli_fetch_assoc($result)) {
			if ($student_num != $value['student_num']) {
				$student_num = $value['student_num'];
				$html .= "</ul></li>";
				$html .= "<li>".$value['s_surname']." ".$value['s_name']." <a href='tel:+7".$value['phone']."'>+7".$value['phone']."</a><ul>";
			}
			$html .= "<li>".$value['t_surname']." ".$value['t_name']."</li>";
		}
		$html .= "</ol>";
	}

	$html .= "<hr>";

	if ($result = mysqli_query($conn, $sql1)) {
		$rowcount = mysqli_num_rows($result);
		$html .= "<h3>Сауалнаманың мұғалімдер бойынша орташа және толық қорытындылары</h3>";
		$avg_result = array();
		$full_result = array();
		while ($value = mysqli_fetch_assoc($result)) {
			$teacher_num = $value['teacher_num'];
			$student_num = $value['student_num'];
			if (!array_key_exists($teacher_num, $avg_result)) {
				$avg_result[$teacher_num] = array("name" => $value['t_name'],
												"surname" => $value['t_surname'],
												"poll_avg" => $default_teacher_avg_result);

				$full_result[$teacher_num] = array("name" => $value['t_name'],
													"surname" => $value['t_surname'],
													"student" => array());
			}
			$avg_result[$teacher_num]['poll_avg'][$value['tpi_id']]['sum_mark'] += $value['mark']; 
			$avg_result[$teacher_num]['poll_avg'][$value['tpi_id']]['count'] += 1;

			if (!array_key_exists($student_num, $full_result[$teacher_num]['student'])) {
				$full_result[$teacher_num]['student'][$student_num] = array("name" => $value['s_name'],
																			"surname" => $value['s_surname'],
																			"poll_res" => array());
			}
			$full_result[$teacher_num]['student'][$student_num]['poll_res'][$value['tpi_id']] = $value['mark'];
			
		}

		$html .= $tpi_html;
		$html .= "<table>";
		$html .= "<tr><td></td>";
		foreach ($poll_text as $value) {
			$html .= "<td><center><b>".substr($value, 0, 1)."</b></center></td>";
		}
		$html .= "</tr>";
		foreach ($avg_result as $value) {
			$html .= "<tr>";
			$html .= "<td>".$value['surname']." ".$value['name']."</td>";
			foreach ($poll_text as $key => $val) {
				$sum_mark = $value['poll_avg'][$key]['sum_mark'];
				$count = $value['poll_avg'][$key]['count'];
				$avg_mark_style = "";
				if ($count != 0) {
					$mark_result = round(($sum_mark / $count), 2);
					if ($mark_result <= 3) {
						$avg_mark_style = " color: red; font-weight: bold;";
					}
				} else {
					$mark_result = "N/A";
				}
				$html .= "<td style='width: 12%; ".$avg_mark_style."'><center>".$mark_result."</center></td>";
			}
			$html .= "</tr>";
		}
		$html .= "</table>";
		$html .= "<hr>";

		$html .= "<table>";
		$colspan = count($poll_text)+1;
		foreach ($full_result as $value) {
			$html .= "<tr><td style='border-top: 2px solid black;' colspan='".$colspan."'><center><i>".$value['surname']." ".$value['name']."<i></td></tr>";
			$html .= "<tr><td></td>";
			foreach ($poll_text as $text) {
				$html .= "<td><center><b>".substr($text, 0, 1)."</b></center></td>";
			}
			$html .= "</tr>";
			$student_count = 0;
			foreach ($value['student'] as $val) {
				$html .= "<tr>";
				$html .= "<td><span>".(++$student_count).")</span><div style='float: right;'><i>".$val['surname']." ".$val['name']."</i></div></td>";
				foreach ($val['poll_res'] as $value) {
					$res_style = "";
					if ($value <= 3) {
						$res_style = "color: red; font-weight: bold;";
					}
					$html .= "<td style='width: 10%; ".$res_style."'><center>".$value."</center></td>";
				}
				$html .= "</tr>";
			}
		}
		$html .= "</table></body></html>";
	}



	mysqli_close($conn);
	if (isset($_GET['view'])) {
		echo $html;
	} else if (isset($_GET['send'])) {
		// $actual_link = "altyn-bilim.kz/cron/poll_report.php?view";
		$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
			$actual_link = "{$_SERVER['HTTP_HOST']}".$uri_parts[0]."?view";
		$html = "<h2><center>".$polled_month." айының сауалнамасының толық қорытындысын төмендегі сілтеме арқылы көруге болады.</center></h2>";
		$html .= "<center><a href='".$actual_link."' target='_blank'>Сауалнамаға қорытындысына сілтеме (".$actual_link.")</a></center>";
		echo $html;
		$to = $super_admin_mail;
	    $subject = "Сауалнаманың толық ақпараттары";
	    $headers = "MIME-Version: 1.0" . "\r\n";
	    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	    //     // More headers
	    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
	    $headers .= 'Bcc: '.$developer_mail. "\r\n";
	   	
		if(mail($to,$subject, $html, $headers)){
			echo "<br>Message sent successfully";
		}
	}


?>