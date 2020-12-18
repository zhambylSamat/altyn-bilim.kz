<?php
	include_once '../connection.php';

	try {

		$query = "SELECT gi.group_info_num,
						gi.group_name,
						t.surname,
						t.name,
						sj.subject_name,
						gi.start_lesson,
						gi.finish_lesson,
						gi.office_number
					FROM group_info gi,
						subject sj,
						teacher t
					WHERE gi.subject_num = sj.subject_num
						AND gi.teacher_num = t.teacher_num
						AND gi.block != 6
						AND gi.group_name NOT LIKE '%рокачка%'
					ORDER BY gi.office_number, gi.start_lesson, gi.finish_lesson, gi.group_name";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
		// $result = json_encode($sql_result);
		// echo $result;

		$result = array();
		foreach ($sql_result as $value) {
			$tmp = array(
							"group_name" => $value['group_name'],
							"surname" => $value['surname'],
							"name" => $value['name'],
							"subject_name" => $value['subject_name'],
							"start_lesson" => $value['start_lesson'],
							"finish_lesson" => $value['finish_lesson'],
							"office_number" => $value['office_number'],
							"week_ids" => array()
						);
			$result[$value['group_info_num']] = $tmp;
		}

		$query = "SELECT sch.group_info_num,
						sch.week_id
					FROM schedule sch,
						group_info gi
					WHERE sch.group_info_num = gi.group_info_num
						AND gi.block != 6
						AND gi.group_name NOT LIKE '%рокачка%'
					ORDER BY sch.week_id";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		foreach ($sql_result as $value) {
			array_push($result[$value['group_info_num']]['week_ids'], $value['week_id']);
		}

		$week_txt = ['', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

		$html = "";
		$html .= "<table>";
		$html .= "<tr>";
			$html .= "<th>#</th> <th>Группаның аты</th> <th>Мұғалімі</th> <th>Пәні</th> <th>Сабақ уақыты</th> <th>Сабақ күндері</th> <th>Кабинет</th>";
		$html .= "</tr>";
		$count = 0;
		foreach ($result as $value) {
			$week_str = '';
			$count++;
			foreach ($value['week_ids'] as $val) {
				$week_str .= $week_txt[$val].", ";
			}
			$html .= "<tr>";
			$html .= "<td>".$count."</td>";
			$html .= "<td onclick='copyFunction(".$count.")' id='copy-".$count."'>".$value['group_name']."</td>";
			$html .= "<td>".$value['surname']." ".$value['name']."</td>";
			$html .= "<td>".$value['subject_name']."</td>";
			$html .= "<td>".$value['start_lesson']." - ".$value['finish_lesson']."</td>";
			$html .= "<td>".$week_str."</td>";
			$html .= "<td>".$value['office_number']."</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
	} catch (Exception $e) {
		throw $e;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		td, th {
			border: 1px solid lightgray;
			padding: 5px;
		}
		input {
			/*width: 0px;*/
			/*height: 0px;*/
			font-size:0;
			/*border: none;*/
		}
	</style>
</head>
<body>
<?php echo $html; ?>
</body>
<script type="text/javascript">
	function copyFunction($id) {
		var el = document.getElementById("copy-"+$id);
		var body = document.body, range, sel;
		if (document.createRange && window.getSelection) {
			range = document.createRange();
			sel = window.getSelection();
			sel.removeAllRanges();
			try {
				range.selectNodeContents(el);
				sel.addRange(range);
			} catch (e) {
				range.selectNode(el);
				sel.addRange(range);
			}
			document.execCommand("copy");
		} else if (body.createTextRange) {
			range = body.createTextRange();
			range.moveToElementText(el);
			range.select();
			range.execCommand("Copy");
		}
	}
</script>
</html>