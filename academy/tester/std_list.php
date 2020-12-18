<?php
	include_once('../common/connection.php');

	$query = "SELECT s.id,
					s.last_name,
					s.first_name,
					s.phone
				FROM student s,
					group_student gs
				WHERE gs.group_info_id = 458
					AND gs.created_date <= STR_TO_DATE('2020-09-19 07:00:00', '%Y-%m-%d %H:%i:%s')
					AND s.id = gs.student_id
				ORDER BY gs.created_date";

	$stmt = $connect->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll();

	$html = "<table>";
	$count = 0;
	foreach ($result as $value) {
		$html .= "<tr>";
			$html .= "<td>".(++$count)."</td>";
			$html .= "<td>".$value['last_name'].' '.$value['first_name']."</td>";
			$html .= "<td><a target='_blank' href='https://api.whatsapp.com/send?phone=7".$value['phone']."&text=Сәлеметсіз%20бе!%20😊%0A%0AТөмендегі%20сілтеме%20арқылы%20телеграмдағы%20марафон%20группамызға%20қосылыңыз:%0A%0A%20https://t.me/joinchat/EImm908TQhhTArDjrTOfzg%0A%0AЕгер%20сілтеме%20ашылмаса,%20маған%20жазыңыз.'>+7 ".$value['phone']."</a></td>";
		$html .= "</tr>";
	}
	$html .= "</table>";
	echo $html;
?>