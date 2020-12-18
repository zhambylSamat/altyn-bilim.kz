<?php
	include_once('../connection.php');
	if(isset($_GET['select_statistics_student'])) {
		$year = $_GET['year'];
	}

	$sql_result = array();
	$result = array();
	$month_arr = array(
		"",
		array("long" => "қаңтар", "short" => "қаң"),
		array("long" => "ақпан", "short" => "ақп"),
		array("long" => "наурыз", "short" => "нау"),
		array("long" => "сәуір", "short" => "сәу"),
		array("long" => "мамыр", "short" => "мам"),
		array("long" => "маусым", "short" => "мау"),
		array("long" => "шілде", "short" => "шіл"),
		array("long" => "тамыз", "short" => "там"),
		array("long" => "қыркүйек", "short" => "қыр"),
		array("long" => "қазан", "short" => "қаз"),
		array("long" => "қараша", "short" => "қар"),
		array("long" => "желтоқсан", "short" => "жел")
	);
	try {
		
		$stmt = $conn->prepare("SELECT s.student_num,
									s.surname,
									s.name,
									ssf.status,
									MONTH(ssf.period) month,
									YEAR(ssf.period) year,
									sj.subject_num,
									sj.subject_name
								FROM student s,
									subject sj,
									statistics_student_frequency ssf
								WHERE YEAR(ssf.period) = :year
									AND s.student_num = ssf.student_num
									AND sj.subject_num = ssf.subject_num
								ORDER BY sj.subject_name ASC, ssf.period ASC, s.surname ASC, s.name ASC");
		$stmt->bindParam(':year', $year, PDO::PARAM_STR);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();
	} catch (Exception $e) {
		throw $e;
	}

	foreach ($sql_result as $key => $value) {
		$result[$value['subject_num']]['subject_name'] = $value['subject_name'];

		$status = "";
		if ($value['status'] == 'in') {
			$status = "in";
		} else if ($value['status'] == 'out') {
			$status = 'out';
		}

		if ($status != "") {
			$tmp = array(
				"student_num" => $value['student_num'],
				"student_name" => $value['name'],
				"student_surname" => $value['surname']
			);
			if (!isset($result[$value['subject_num']]['items']) || !isset($result[$value['subject_num']]['items'][$value['month']][$status])) {
				$result[$value['subject_num']]['items'][$value['month']][$status] = array();
			}
			array_push($result[$value['subject_num']]['items'][$value['month']][$status], $tmp);
		}
	}
?>
<hr>
<table class='table table-striped table-bordered'>
	<tr>
		<?php
			for ($i = 1; $i<count($month_arr); $i++) {
				echo "<th><center>".($month_arr[$i]['short'].".".$year)."</center></th>";
			}
		?>
	</tr>
	<?php
		foreach ($result as $key => $value) {
			echo "<tr style='background-color: #D9EDF7;'><th colspan='12'><center>".$value['subject_name']."</center></th></tr>";
			echo "<tr>";
			for ($i = 1; $i<=12; $i++){
				if (isset($value['items'][$i])) {
					$out = "0";
					$in = "0";
					$out_students = '';
					$in_students = '';
					if (isset($value['items'][$i]['out'])) {
						$out = count($value['items'][$i]['out']);
						foreach ($value['items'][$i]['out'] as $key => $val) {
							$out_students .= "<span>";
							$out_students .= "<input type='hidden' name='surname' value='".$val['student_surname']."'>";
							$out_students .= "<input type='hidden' name='name' value='".$val['student_name']."'>";
							$out_students .= "<input type='hidden' name='student_num' value='".$val['student_num']."'>";
							$out_students .= "</span>";
						}
					}
					if (isset($value['items'][$i]['in'])) {
						$in = count($value['items'][$i]['in']);
						foreach ($value['items'][$i]['in'] as $key => $val) {
							$in_students .= "<span>";
							$in_students .= "<input type='hidden' name='surname' value='".$val['student_surname']."'>";
							$in_students .= "<input type='hidden' name='name' value='".$val['student_name']."'>";
							$in_students .= "<input type='hidden' name='student_num' value='".$val['student_num']."'>";
							$in_students .= "</span>";
						}
					}

					echo "<td class='statistics_student_more'><center>";
					echo "<b style='color: red; '>".$out."</b>";
					echo " | ";
					echo "<b style='color: green;'>".$in."</b>";
					echo "<div class='statistics-student-out'>";
					echo $out_students;
					echo "</div>";
					echo "<div class='statistics-student-in'>";
					echo $in_students;
					echo "</div>";
					echo "</center></td>";
				} else {
					echo "<td><center>-</center></td>";
				}
			}
			echo "</tr>";
		}
	?>
</table>
<style type="text/css">
	.statistics_student_more:hover {
		cursor: pointer;
		position: relative;
		z-index: 100;
		transition: 0.2s;
		box-shadow: 5px 5px 10px 0px gray;
	}
</style>