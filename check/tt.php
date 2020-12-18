<?php
	include_once('../connection.php');
	$stmt = $conn->prepare("SELECT s.student_num AS s_num,
								s.surname,
								s.name,
								sj.subject_num AS sj_num,
								sj.subject_name,
								ttm.mark,
								ttm.date_of_test,
								p.phone
							FROM student s
							INNER JOIN trial_test tt
								ON s.student_num = tt.student_num
							INNER JOIN subject sj
								ON sj.subject_num = tt.subject_num
									AND sj.subject_num IN (SELECT gi2.subject_num
															FROM group_info gi2,
																group_student gs2
															WHERE gs2.student_num = s.student_num
																AND gs2.block != 6
																AND gi2.group_info_num = gs2.group_info_num
																AND gi2.block != 6) 
							INNER JOIN trial_test_mark ttm
								ON ttm.trial_test_num = tt.trial_test_num
							LEFT JOIN parent p
								ON p.student_num = s.student_num
									AND p.parent_order = 1
							INNER JOIN teacher t
								ON t.teacher_num IN (SELECT gi2.teacher_num
													FROM group_info gi2,
														group_student gs2
													WHERE gs2.student_num = s.student_num
														AND gs2.block != 6
														AND gi2.group_info_num = gs2.group_info_num
														AND gi2.block != 6)
							WHERE s.block != 6
							ORDER BY s.surname, s.name, sj.subject_name, ttm.date_of_test DESC");
	$stmt->execute();
	$sql_result = $stmt->fetchAll();

	$result = array();
	$tmp_test_arr = array();
	$tmp_subject_arr = array();
	
	function makeSubject($value) {
		return array('subject_num' => $value['sj_num'],
					'subject_title' => $value['subject_name'],
					'tests' => array());
	}
	function makeStudent($value) {
		return array('student_num' => $value['s_num'],
					'last_name' => $value['surname'],
					'first_name' => $value['name'],
					'parent_phone' => $value['phone'],
					'subjects' => array());
	}
	function makeTeacher($value) {
		return array('teacher_num' => $value['teacher_num'],
					'surname' => $value['t_surname'],
					'name' => $value['t_name']);
	}

	function checkMark($tests) {
		if (count($tests) == 2) {
			return $tests[0]['mark'] < $tests[1]['mark'];
		} else if (count($tests) == 3) {
			return ($tests[0]['mark'] - $tests[2]['mark']) <= 3;
		}
		return false;
	}

	$student = array();
	$subject = array();
	$subject_arr = array();
	$test_arr = array();
	$teacher = array();
	$teacher_arr = array();
	foreach ($sql_result as $value) {
		if (count($student) == 0) {
			$student = makeStudent($value);
		} else if ($student['student_num'] != $value['s_num']) {
			if (checkMark($test_arr)) {
				$subject['tests'] = $test_arr;
				array_push($subject_arr, $subject);
			}
			$test_arr = array();
			$subject = array();

			if (count($subject_arr) > 0) {
				$student['subjects'] = $subject_arr;
				array_push($result, $student);
			}
			$subject_arr = array();
			$student = makeStudent($value);
		}

		if (count($subject) == 0) {
			$subject = makeSubject($value);
		} else if ($subject['subject_num'] != $value['sj_num']) {
			if (checkMark($test_arr)) {
				$subject['tests'] = $test_arr;
				array_push($subject_arr, $subject);
			}
			$test_arr = array();
			$subject = makeSubject($value);
		}

		if (count($test_arr) < 3) {
			array_push($test_arr, array('mark' => $value['mark'],
										'date' => $value['date_of_test']));
		}
	}
	if (checkMark($test_arr)) {
		$subject['tests'] = $test_arr;
		array_push($subject_arr, $subject);
	}
	if (count($subject_arr) > 0) {
		$student['subjects'] = $subject_arr;
		// $student['teacher'] = $teacher_arr;
		array_push($result, $student);
	}

	$teacher_query = "SELECT t.teacher_num,
							t.surname,
							t.name
						FROM teacher t,
							group_info gi,
							group_student gs
						WHERE gs.student_num = :student_num
							AND gi.group_info_num = gs.group_info_num
							AND gi.subject_num = :subject_num
							AND t.teacher_num = gi.teacher_num
							AND gs.block != 6
							AND gi.block != 6";

	$teacher_list = array();
	foreach ($result as $student) {
		foreach ($student['subjects'] as $subject) {
			$stmt = $conn->prepare($teacher_query);
			$stmt->bindParam(':student_num', $student['student_num'], PDO::PARAM_STR);
			$stmt->bindParam(':subject_num', $subject['subject_num'], PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			foreach ($query_result as $value) {
				if (!isset($teacher_list[$student['student_num']][$subject['subject_num']])) {
					$tmp = $value['surname'].' '.$value['name'];
					$teacher_list[$student['student_num']][$subject['subject_num']] = array($tmp);
				}
				else {
					$tmp = $value['surname'].' '.$value['name'];
					array_push($teacher_list[$student['student_num']][$subject['subject_num']], $tmp);
				}
			}
		}
	}
?>
<table>
	<tr>
		<th>FIO</th>
		<th>Subject</th>
		<th>Teacher</th>
		<th colspan='3'>result</th>
	</tr>
	<?php
		$html = "";
		$count = 0;
		foreach ($result as $student_val) {
			$html .= "<tr>";
			$html .= "<td rowspan='".(count($student_val['subjects'])+1)."'>";
			$has_phone = $student_val['parent_phone'] != null;
				$html .= (++$count).") ";
				$html .= $has_phone ? "<a href='../parent/parent_controller.php?signIn&phone=".$student_val['parent_phone']."' target='__blank'>" : '';
				$html .= $student_val['last_name'].' '.$student_val['first_name'];
				$html .= $has_phone ? "</a>" : '';
			$html .= "</td>";
			$html .= "<td style='display:none;'></td>";
			$html .= "<td style='display:none;'></td>";
			$html .= "<td style='display:none;'></td>";
			$html .= "</tr>";
			foreach ($student_val['subjects'] as $subject_val) {
				$html .= "<tr>";
				$html .= "<td>".$subject_val['subject_title']."</td>";
				$html .= "<td>".implode(', ', $teacher_list[$student_val['student_num']][$subject_val['subject_num']])."</td>";
				for ($i=2; $i>=0; $i--) { 
					if (isset($subject_val['tests'][$i])) {
						$html .= "<td style='text-align:center;'>".$subject_val['tests'][$i]['date'].' <br> '.$subject_val['tests'][$i]['mark']."</td>";
					} else {
						$html .= "<td></td>";
					}
				}
				$html .= "</tr>";
			}
			$html .= "<tr><td colspan='5'></td></tr>";
		}
		echo $html;
	?>
</table>

<style type="text/css">
	th, td {
		padding: 5px 10px;
		border-left: 1px solid grey;
		border-bottom: 1px solid grey;
	}
	th:last-child, td:last-child {
		border-right: 1px solid grey;
	}
 </style>
