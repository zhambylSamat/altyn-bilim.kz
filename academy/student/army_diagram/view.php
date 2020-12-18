<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_group_army_infos() {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT gs.group_info_id,
							sj.title AS subject_title,
							s.last_name,
							s.first_name,
							s.avatar_link
						FROM group_student gs,
							group_info gi,
							subject sj,
							army_comander ac,
							staff s,
							army_group ag
						WHERE gs.student_id = :student_id
							AND gs.is_archive = 0
							AND ag.group_info_id = gs.group_info_id
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id
							AND ac.subject_id = sj.id
							AND s.id = ac.staff_id
							AND gi.status_id = 2
						ORDER BY s.last_name, s.first_name";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_group_info = $stmt->fetchAll();

			$group_info = array();
			foreach ($query_group_info as $value) {
				$group_info[$value['group_info_id']] = array('subject_title' => $value['subject_title'],
															'last_name' => $value['last_name'],
															'first_name' => $value['first_name'],
															'avatar_link' => $value['avatar_link'],
															'group_students' => array());
			}

			$all_level_medals = get_all_level_medals();
			foreach ($group_info as $group_info_id => $info) {
				$query = "SELECT gs.id AS group_student_id,
								s.id AS student_id,
								s.last_name,
								s.first_name,
								s.avatar_link
						FROM group_student gs,
							student s
						WHERE gs.group_info_id = :group_info_id
							AND s.id = gs.student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$query_result = $stmt->fetchAll();

				foreach ($query_result as $value) {
					$group_student_ids = get_group_info_ids($group_info_id, $value['student_id'], $value['group_student_id']);
					$army_medal_info = get_army_medal_info($group_student_ids, $all_level_medals);
					$group_info[$group_info_id]['group_students'][$value['group_student_id']] = array('student_id' => $value['student_id'],
																									'last_name' => $value['last_name'],
																									'first_name' => $value['first_name'],
																									'avatar_link' => $value['avatar_link'],
																									'army_medal_info' => $army_medal_info);
				}
			}

			foreach ($group_info as $key => $value) {
				uasort($group_info[$key]['group_students'], 
					function($a, $b) {
						if ($a['army_medal_info']['percent'] == $b['army_medal_info']['percent']) {
							return 0;
						}
						return $a['army_medal_info']['percent'] < $b['army_medal_info']['percent'] ? 1 : -1;
						// return strcmp($a['army_medal_info']['percent'], $b['army_medal_info']['percent']);
					}
				);
			}

			return $group_info;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_army_medal_info ($group_student_ids, $all_level_medals) {
		GLOBAL $connect;

		try {

			$query = "SELECT atm.id,
							atm.army_medal_id,
							atm.percent,
							am.level
						FROM army_test_medal atm,
							army_medal am
						WHERE atm.group_student_id IN (".implode(',', $group_student_ids).")
							AND am.id = atm.army_medal_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			$result = array();

			if ($row_count == 1) {
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$result = array('icon_link' => $all_level_medals[$query_result['level']]['icon_link'],
								'level' => $query_result['level'],
								'title' => $all_level_medals[$query_result['level']]['title'],
								'description' => $all_level_medals[$query_result['level']]['description'],
								'percent' => $query_result['percent']);
			} else {
				$result = array('icon_link' => $all_level_medals[1]['icon_link'],
								'level' => 1,
								'title' => $all_level_medals[1]['title'],
								'description' => $all_level_medals[1]['description'],
								'percent' => 0);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_level_medals () {
		GLOBAL $connect;

		try {

			$query = "SELECT am.id,
							am.icon_link,
							am.level,
							am.title,
							am.description
						FROM army_medal am";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['level']] = array('army_medal_id' => $value['id'],
												'icon_link' => $value['icon_link'],
												'level' => $value['level'],
												'title' => $value['title'],
												'description' => $value['description']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_info_ids ($group_info_id, $student_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$group_student_ids = array($group_student_id);

			$query = "SELECT gs.transfer_from_group,
							gs.id AS group_student_id
						FROM group_student gs
						WHERE gs.student_id = :student_id
							AND gs.group_info_id = :group_info_id";
			$enough_group_infos = false;
			$group_info_id_count = 0;
			while (!$enough_group_infos) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					$group_info_id = $query_result['transfer_from_group'];
					$group_student_id = $query_result['group_student_id'];
					array_push($group_student_ids, $group_student_id);
					$group_info_id_count++;
				} else {
					$enough_group_infos = true;
				}

				if ($group_info_id_count == 2) {
					$enough_group_infos = true;
				}
			}

			return $group_student_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>