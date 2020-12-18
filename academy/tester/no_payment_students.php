<!DOCTYPE html>
<html>
<head>
	<title>list</title>
	<style type="text/css">
		td {
			border: 1px solid gray;
		}
		.divider {
			padding: 10px;
		}
	</style>
</head>
<body>
	<?php
		include_once('../common/connection.php');

		$query = "SELECT s.id AS student_id, 
						s.last_name,
					    s.first_name,
					    s.phone,
					    sj.title,
					    gi.group_name
					FROM group_student gs,
					    student s,
					    subject sj,
					    group_info gi
					WHERE gs.status = 'inactive'
					    AND gi.id = gs.group_info_id
					    AND sj.id = gi.subject_id
					    AND s.id = gs.student_id
						AND 0 != (SELECT count(gsp.id)
					             FROM group_student_payment gsp
					             WHERE gsp.group_student_id = gs.id
					              	AND gsp.access_until > '2020-09-01'
					             	AND gsp.payment_type = 'money')
					    AND gi.subject_id NOT IN (SELECT gi2.subject_id
					                             FROM group_info gi2,
					                              	group_student gs2
					                             WHERE gi2.id = gs2.group_info_id
					                             	AND gs2.student_id = gs.student_id
					                             	AND gs2.status = 'active'
					                             	AND gs2.is_archive = 0)
					    AND gi.subject_id != 18
					ORDER BY s.last_name, s.first_name, gi.group_name";

		$stmt = $connect->prepare($query);
		$stmt->execute();
		$query_result = $stmt->fetchAll();

		$html = "";

		$student_id = 0;
		$html .= "<table>";
		foreach ($query_result as $value) {
			if ($student_id != 0 && $student_id != $value['student_id']) {
				$html .= "<tr><td class='divider' colspan='5'></td></tr>";
			}
			$student_id = $value['student_id'];

			$html .= "<tr>";
				$html .= "<td>".$value['last_name']."</td>";
				$html .= "<td>".$value['first_name']."</td>";
				$html .= "<td>".$value['phone']."</td>";
				$html .= "<td>".$value['title']."</td>";
				$html .= "<td>".$value['group_name']."</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		echo $html;
	?>
</body>
</html>
