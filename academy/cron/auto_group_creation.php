<?php
	include_once('../common/connection.php');

	$registration_reserves = get_grouped_registration_reserve();
	create_new_group($registration_reserves);

	function get_grouped_registration_reserve() {
		GLOBAL $connect;

		try {

			$reserves_before_n_days = 10;

			$query = "SELECT rr.topic_id,
							DATE_FORMAT(rr.created_date, '%Y-%m-%d') AS created_date,
							sj.id AS subject_id,
							t.title AS topic_title,
							(SELECT gi.group_name
							FROM group_info gi
							WHERE gi.subject_id = sj.id
							ORDER BY CONVERT(SUBSTRING_INDEX(gi.group_name, ' ', 1), UNSIGNED INTEGER) DESC
							LIMIT 1) AS last_group_name
						FROM registration_reserve rr,
							topic t,
							subject sj
						WHERE rr.is_done = 0
							AND t.id = rr.topic_id
							AND sj.id = t.subject_id
						GROUP BY rr.topic_id
						ORDER BY rr.created_date ASC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				$date1 = date_create($value['created_date']);
				$date2 = date_create(date('Y-m-d'));
				$date_diff = date_diff($date1, $date2)->format('%a');
				if ($date_diff >= $reserves_before_n_days) {
					array_push($result, array('subject_id' => $value['subject_id'],
												'topic_id' => $value['topic_id'],
												'topic_title' => $value['topic_title'],
												'last_group_name' => $value['last_group_name']));
				}
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function create_new_group($reserve_infos) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO group_info (subject_id, topic_id, lesson_type, group_name, start_date)
								VALUES (:subject_id, :topic_id, 'topic', :group_name, NOW())";
			$count_group_info = "SELECT count(gi.id) AS c
									FROM group_info gi
									WHERE gi.subject_id = :subject_id
										AND DATE_FORMAT(gi.start_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') 
										AND gi.topic_id = :topic_id";
			foreach ($reserve_infos as $value) {
				$stmt = $connect->prepare($count_group_info);
				$stmt->bindParam(':subject_id', $value['subject_id'], PDO::PARAM_INT);
				$stmt->bindParam(':topic_id', $value['topic_id'], PDO::PARAM_INT);
				$stmt->execute();
				$group_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

				if ($group_count == 0) {
					$group_numeration = intval(explode(' ', $value['last_group_name'])[0]) + 1;
					$clear_topic_title = explode('-', $value['topic_title'])[1];
					$group_name = $group_numeration.' ағым - '.$clear_topic_title;
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':subject_id', $value['subject_id'], PDO::PARAM_INT);
					$stmt->bindParam(':topic_id', $value['topic_id'], PDO::PARAM_INT);
					$stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
					$stmt->execute();
				}
			}

		} catch (Exception $e) {
			throw $e;
		}
	}
?>