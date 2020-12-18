<?php
	include_once('connection.php');
	include_once('emails.php');

	$yesterday_date = date("d-m-Y", strtotime("-1 day", strtotime(date('d-m-Y'))));
	// $yesterday_date = date("d-m-Y", strtotime('25-11-2019'));
	// $yesterday_date = date("d-m-Y");

	$sql = "SELECT s.student_num,
				s.surname s_surname,
				s.name s_name,
				s.phone,
				t.teacher_num,
				t.surname t_surname,
				t.name t_name,
				sps.mark,
				tpi.text
			FROM student_poll sp,
				student_polls sps,
				student s,
				teacher t,
				teacher_poll_info tpi
			WHERE DATE_FORMAT(sp.polled_date, '%Y-%m-%d') = STR_TO_DATE('$yesterday_date', '%d-%m-%Y')
				AND sps.student_poll_id = sp.id
				AND tpi.id = sps.teacher_poll_info_id
				AND t.teacher_num = sp.teacher_num
				AND s.student_num = sp.student_num
			ORDER BY s.surname, s.name, t.surname, t.surname, tpi.text";

	$poll_res = array();
	$has_min_poll = false;
	if ($result = mysqli_query($conn, $sql)) {
		$poll_res_rowcount=mysqli_num_rows($result);
		$student_num = "";
		$teacher_num = "";
		while($value = mysqli_fetch_assoc($result)) {
			if ($student_num != $value["student_num"]) {
				$student_num = $value['student_num'];
				$teacher_num = "";
				$student_tmp = array('student' => array("name" => $value['s_name'],
														"surname" => $value['s_surname'],
														"phone" => $value['phone']), 
									"polls" => array(), 
									"has_min_poll" => false);

				$poll_res[$student_num] = $student_tmp;
			}
			if ($teacher_num != $value['teacher_num']) {
				$teacher_num = $value['teacher_num'];
				$teacher_tmp = array("teacher" => array("name" => $value['t_name'],
														"surname" => $value['t_surname']),
									"poll" => array(),
									"poll_min_count" => 0);
				$poll_res[$student_num]['polls'][$teacher_num] = $teacher_tmp;
			}
			$tmp = array("text" => $value['text'], "mark" => $value['mark']);
			array_push($poll_res[$student_num]['polls'][$teacher_num]['poll'], $tmp);
			if ($value['mark'] <= 3) {
				$poll_res[$student_num]['polls'][$teacher_num]['poll_min_count']++;
				$poll_res[$student_num]['has_min_poll'] = true;
				if (!$has_min_poll) {
					$has_min_poll = true;
				}
			}
		}
	}

	$html = "";
	$html .= "<style>
				td {
					border: 1px solid lightgray;
					padding:5px;
				}
				tr {
					height: 20px;
				}
				.poll-short-info {
					width: 30%;
					border-bottom: 1px solid lightgray;
				}
				.poll-danger {
					// background-color: #D98C89;
					border: 1px solid red !important;
				}
				.poll-text {
					width: 95%;
					display: inline-block;
				}
				.poll-mark {
					display: inline-block;
					width: 1%;
					padding-left: 10px;
				}
				.mark-danger {
					color: red;
					font-weight: bold;
				}
			</style>";
	foreach ($poll_res as $s_val) {
		if ($s_val['has_min_poll']) {
			$html .= "<hr><span>Оқушы: </span><b>".$s_val['student']['surname']." ".$s_val['student']['name']." "."<a href='tel:+7".$s_val['student']['phone']."'>+7".$s_val['student']['phone']."</a></b><br>";
			$html .= "<ul>";
			foreach ($s_val['polls'] as $t_val) {
				if ($t_val['poll_min_count'] > 0) {
					$html .= "<li><p>".$t_val['teacher']['surname']." ".$t_val['teacher']['name']."</p>";
					$html .= "<ol type='I'>";
					foreach ($t_val['poll'] as $p_val) {
						$poll_class = "";
						$mark_class = "";
						if ($p_val['mark'] <= 3) {
				    		$poll_class = "poll-danger";
				    		$mark_class = "mark-danger";
				    	}
						$html .= "<li><div class='poll-short-info ".$poll_class."'><div class='poll-text'>".$p_val['text']."</div><span>:</span><div class='poll-mark ".$mark_class."'>".$p_val['mark']."</div></div></li>";
					}
					$html .= "</ol>";
				}
			}
			$html .= "</ul>";
		}
	}

	mysqli_close($conn);

	if (isset($_GET['view'])) {
		echo $html;
	} else if (isset($_GET['send'])) {
		if ($has_min_poll) {
			$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
			$actual_link = "{$_SERVER['HTTP_HOST']}".$uri_parts[0]."?view";
			$polled_date = date('d.m.Y', strtotime("-1 days"));
			$html = "<h2><center>".$polled_date." күнгі сауалнамадан 3-тен төмен қойған оқушылар қорытындысын төмендегі сілтеме арқылы көруге болады.</center></h2>";
			$html .= "<center><a href='".$actual_link."' target='_blank'>Сауалнамаға қорытындысына сілтеме (".$actual_link.")</a></center>";
			echo $html;
			$to = $super_admin_mail;
		    $subject = "Сауалнамадан 3-тен төмен қойған оқушылар";
		    $headers = "MIME-Version: 1.0" . "\r\n";
		    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		        // More headers
		    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
		    $headers .= 'Bcc: '.$developer_mail. "\r\n";
	    	if(mail($to,$subject,$html,$headers)){
	    		echo "<br>Message sent successfully";
	    	}
		}
	}
?>