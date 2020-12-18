<?php
	include_once('../connection.php');
	if (isset($_GET['select_chocolate'])) {
		$year_int = $_GET['year'];
		$month_int = $_GET['month'];
	}

	try {

		$result = array();
		$object_ids = array();

		$stmt = $conn->prepare("SELECT object_id, object_num, DATE_FORMAT(date, '%d.%m.%Y') d FROM chocolate WHERE YEAR(date) = :year AND MONTH(date) = :month ORDER BY date DESC");
		$stmt->bindParam(':year', $year_int, PDO::PARAM_INT);
		$stmt->bindParam(':month', $month_int, PDO::PARAM_INT);
		$stmt->execute();
		$chocolate_result = $stmt->fetchAll();

		foreach ($chocolate_result as $key => $value) {
			$result[$value['object_num'].'!'.$value['object_id']]['student_name'] = "";
			$result[$value['object_num'].'!'.$value['object_id']]['reason'] = "";
			$result[$value['object_num'].'!'.$value['object_id']]['result'] = "";
			$result[$value['object_num'].'!'.$value['object_id']]['subject'] = "";
			$result[$value['object_num'].'!'.$value['object_id']]['student_num'] = "";
			$result[$value['object_num'].'!'.$value['object_id']]['date'] = $value['d'];
			
			if (!isset($object_ids[$value['object_id']])) {
				$object_ids[$value['object_id']] = array();
			}
			array_push($object_ids[$value['object_id']], $value['object_num']);
		}

		// print_r($chocolate_result);

		$object_nums_1 = "";
		$object_nums_4 = "";
		$object_nums_5 = "";
		$object_nums_6 = "";
		$object_nums_7 = "";

		foreach ($object_ids as $key => $value) {
			if ($key == 1) {
				foreach ($value as $val) {
					$object_nums_1 .= "'".$val."',";
				}
			} else if ($key == 4) {
				foreach ($value as $val) {
					$object_nums_4 .= "".$val.",";
				}
			} else if ($key == 5) {
				foreach ($value as $val) {
					$object_nums_5 .= "".$val.",";
				}
			} else if ($key == 6) {
				foreach ($value as $val) {
					$object_nums_6 .= "".$val.",";
				}
			} else if ($key == 7){
				foreach ($value as $val) {
					$object_nums_7 .= "".$val.",";
				}
			}
		}
		if ($object_nums_1 != "") {
			$object_nums_1 = rtrim($object_nums_1,',');
			$reason = "Аралық бақылау";
			$stmt = $conn->prepare("SELECT spn.id,
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										student_prize_notification spn,
										group_student gs,
										group_info gi,
										subject sj,
										quiz_mark qm
									WHERE spn.id in (".$object_nums_1.")
										AND spn.group_student_num = gs.group_student_num
										AND gs.group_info_num = gi.group_info_num
										AND gi.subject_num = sj.subject_num
										AND spn.quiz_mark_num = qm.quiz_mark_num
										AND gs.student_num = s.student_num");
			$stmt->execute();

			foreach ($stmt->fetchAll() as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата: </span><span>".$value['date']."</span><br>";
				$result_txt .= "<span style='color: gray;'>Теория: </span><b>".$value['mark_theory']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Есеп: </span><b>".$value['mark_practice']."</b><br>";

				$result[$value['id'].'!1']['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id'].'!1']['reason'] = $reason;
				$result[$value['id'].'!1']['result'] = $result_txt;
				$result[$value['id'].'!1']['subject'] = $value['subject_name'];
				$result[$value['id'].'!1']['student_num'] = $value['student_num'];
			}

		}
		if ($object_nums_4 != "") {
			$reason = "Пробный тест <i style='color:gray;'>10% скидка</i>";
			$object_nums_4 = rtrim($object_nums_4,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										ttm.mark,
										DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										trial_test tt,
										trial_test_mark ttm,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_4."))
										AND n.object_num = ttm.trial_test_mark_num
										AND ttm.trial_test_num = tt.trial_test_num
										AND sj.subject_num = tt.subject_num
										AND s.student_num = tt.student_num");
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".$value['mark']."</b><br>";

				$result[$value['id'].'!4']['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id'].'!4']['reason'] = $reason;
				$result[$value['id'].'!4']['result'] = $result_txt;
				$result[$value['id'].'!4']['subject'] = $value['subject_name'];
				$result[$value['id'].'!4']['student_num'] = $value['student_num'];
			}
		} 
		if ($object_nums_5 != "") {
			$reason = "Пробный тест <i style='color:gray;'>10% скидка</i>";
			$notification_id = "";
			$object_nums_5 = rtrim($object_nums_5,',');
			$query_pattern = "SELECT n1.id 
				               	FROM notification n1 
				               	WHERE n1.object_parent_num = (SELECT n2.object_parent_num 
				                                        	FROM notification n2 
				                                            WHERE n2.id = ?) 
				               		AND n1.status = 'D'
				               		AND n1.id >= ?
				               	ORDER BY n1.id ASC
				              	LIMIT 3";
			$query = "";
			$ids = explode(',', $object_nums_5);
			foreach ($ids as $key => $value) {
				$query .= "(".$query_pattern.") UNION ALL";
			}
			$query = rtrim($query,'UNION ALL');

			$stmt = $conn->prepare($query);
			$j = 1;
			foreach ($ids as $key => $value) {
				$stmt->bindValue($j++, $value, PDO::PARAM_INT);
				$stmt->bindValue($j++, $value, PDO::PARAM_INT);
			}
			$stmt->execute();
			$res = $stmt->fetchAll();

			foreach ($res as $key => $value) {
				$notification_id .= $value['id'].",";
			}
			$notification_id = rtrim($notification_id, ',');
			$stmt = $conn->prepare("SELECT n.id,
										s.student_num,
										s.name,
										s.surname,
										sj.subject_name,
										ttm.mark,
										DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') as date
									FROM student s, 
										subject sj,
										trial_test tt,
										trial_test_mark ttm,
										notification n
									WHERE n.id in (".$notification_id.")
										AND ttm.trial_test_mark_num = n.object_num
  										AND tt.trial_test_num = n.object_parent_num
  										AND sj.subject_num = tt.subject_num
  										AND s.student_num = tt.student_num
  									ORDER BY n.object_parent_num, n.id");
			$stmt->execute();
			$result_arr = $stmt->fetchAll();
			for ($i=0; $i<count($result_arr); $i=$i+3) {
				$result_txt = "<span style='color: gray;'>Дата ".$result_arr[$i]['date'].": </span><b>".$result_arr[$i]['mark']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Дата ".$result_arr[$i+1]['date'].": </span><b>".$result_arr[$i+1]['mark']."</b><br>";
				$result_txt .= "<span style='color: gray;'>Дата ".$result_arr[$i+2]['date'].": </span><b>".$result_arr[$i+2]['mark']."</b><br>";

				$result[$result_arr[$i]['id'].'!5']['student_name'] = $result_arr[$i]['surname']." ".$result_arr[$i]['name'];
				$result[$result_arr[$i]['id'].'!5']['reason'] = $reason;
				$result[$result_arr[$i]['id'].'!5']['result'] = $result_txt;
				$result[$result_arr[$i]['id'].'!5']['subject'] = $result_arr[$i]['subject_name'];
				$result[$result_arr[$i]['id'].'!5']['student_num'] = $result_arr[$i]['student_num'];
			}
		}

		if ($object_nums_6 != "") {
			$reason = "Аралық бақылау <i style='color:gray;'>10% скидка</i>";
			$object_nums_6 = rtrim($object_nums_6,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										t.topic_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										topic t,
										quiz_mark qm,
										quiz q,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_6."))
										AND qm.quiz_mark_num = n.object_num
										AND q.quiz_num = qm.quiz_num
										AND t.topic_num = q.topic_num
										AND sj.subject_num = t.subject_num
										AND s.student_num = qm.student_num");
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".($value['mark_theory']!=0 ? "Теория: ".$value['mark_theory']."%" : "")." Есеп: ".$value['mark_practice']."%</b><br>";

				$result[$value['id'].'!6']['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id'].'!6']['reason'] = $reason;
				$result[$value['id'].'!6']['result'] = $result_txt;
				$result[$value['id'].'!6']['subject'] = $value['subject_name']." <i style='color:gray;'>".$value['topic_name']."</i>";
				$result[$value['id'].'!6']['student_num'] = $value['student_num'];
			}
		}

		if ($object_nums_7 != "") {
			$reason = "Аралық бақылау <i style='color:gray;'>5% скидка</i>";
			$object_nums_7 = rtrim($object_nums_7,',');
			$stmt = $conn->prepare("SELECT n.id, 
										s.student_num,
										s.surname, 
										s.name,
										sj.subject_name,
										t.topic_name,
										qm.mark_theory,
										qm.mark_practice,
										DATE_FORMAT(qm.created_date, '%d.%m.%Y') as date
									FROM student s,
										subject sj,
										topic t,
										quiz_mark qm,
										quiz q,
										notification n
									WHERE n.id in (SELECT n1.id 
													FROM notification n1 
													WHERE n1.id in (".$object_nums_7."))
										AND qm.quiz_mark_num = n.object_num
										AND q.quiz_num = qm.quiz_num
										AND t.topic_num = q.topic_num
										AND sj.subject_num = t.subject_num
										AND s.student_num = qm.student_num");
			$stmt->execute();
			$r = $stmt->fetchAll();

			foreach ($r as $key => $value) {
				$result_txt = "<span style='color: gray;'>Дата ".$value['date'].": </span><b>".($value['mark_theory']!=0 ? "Теория: ".$value['mark_theory']."%" : "")." Есеп: ".$value['mark_practice']."%</b><br>";

				$result[$value['id'].'!7']['student_name'] = $value['surname']." ".$value['name'];
				$result[$value['id'].'!7']['reason'] = $reason;
				$result[$value['id'].'!7']['result'] = $result_txt;
				$result[$value['id'].'!7']['subject'] = $value['subject_name']." <i style='color:gray;'>".$value['topic_name']."</i>";
				$result[$value['id'].'!7']['student_num'] = $value['student_num'];
			}
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<hr>
<table class='table table-bordered table-hover'>
	<tr>
		<th>№</th>
		<th>Аты-жөні</th>
		<th>Пәні</th>
		<th>Себебі</th>
		<th>Қорытындысы</th>
		<th>Шоколад берілген күні</th>
	</tr>
	<?php
		$count = 0;
		foreach ($result as $key => $value) {
	?>
	<tr>
		<th><?php echo ++$count; ?></th>
		<td><a href="student_info_marks.php?data_num=<?php echo $value['student_num']; ?>" target="_blank"><?php echo $value['student_name']; ?></a></td>
		<td><?php echo $value['subject']; ?></td>
		<td><?php echo $value['reason']; ?></td>
		<td><?php echo $value['result']; ?></td>
		<td><?php echo $value['date']; ?></td>
	</tr>
	<?php } ?>
</table>