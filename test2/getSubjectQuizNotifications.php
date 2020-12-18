<?php
	include_once '../connection.php';

	try {
		$query = "SELECT n.id,
						n.count,
						n.status
					FROM notification n
					WHERE n.object_id = 5
						AND n.status NOT IN ('D', 'DA', 'AD')
					ORDER BY n.object_parent_num, n.count DESC";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$sql_row_num = $stmt->rowCount();

		// $result = json_encode($res);
		// echo $result;
		
		$notification_id_list = array();
		$i = 0;
		while ($i < $sql_row_num) {
			if ($res[$i]['count'] == '1') {
				if ($res[$i]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
				}
				$i = $i + 1;
				
			} else if ($res[$i]['count'] == '2') {
				if ($res[$i]['status'] == 'A' && $res[$i+1]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
					array_push($notification_id_list, $res[$i+1]['id']);
				}
				$i = $i + 2;
			} else if ($res[$i]['count'] == '3') {
				if ($res[$i]['status'] == 'A' && $res[$i+1]['status'] == 'A' && $res[$i+2]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
					array_push($notification_id_list, $res[$i+1]['id']);
					array_push($notification_id_list, $res[$i+2]['id']);
				}
				$i = $i + 3;
			}
		}
		$query = "SELECT s.student_num,
						s.surname,
						s.name,
						sj.subject_num,
						sj.subject_name,
						ttm.mark,
						ttm.date_of_test
					FROM notification n,
						trial_test tt,
						trial_test_mark ttm,
						subject sj,
						student s
					WHERE n.id IN (".implode(', ', $notification_id_list).")
						AND n.object_num = ttm.trial_test_mark_num
						AND ttm.trial_test_num = tt.trial_test_num
						AND tt.subject_num = sj.subject_num
						AND tt.student_num = s.student_num
						AND s.block != 6
					ORDER BY s.surname, s.name, sj.subject_name, n.object_parent_num, n.count";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$result = json_encode($res);
		echo $result;
	} catch (Exception $e) {
		throw $e;
	}
?>
