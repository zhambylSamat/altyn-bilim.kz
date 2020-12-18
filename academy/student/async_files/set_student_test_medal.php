<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	// $request = json_encode($_REQUEST);
	$material_test_action_id = $_POST['material_test_action_id'];
	// $material_test_action_id = 13410;

	$is_in_army = check_if_group_student_is_in_army($material_test_action_id);
	echo $is_in_army ? 'true' : 'false';

	if ($is_in_army) {
		$group_student_id = get_group_student_id_by_mta_id($material_test_action_id);
		echo json_encode($group_student_id)."<br>";

		$group_student_ids = get_group_student_ids_by_gs_id($group_student_id);
		echo json_encode($group_student_ids)."<br>";

		$material_test_action_infos = get_material_test_action_infos_by_gs_id($group_student_ids);
		echo json_encode($material_test_action_infos)."<br>";

		set_student_army_medal($material_test_action_infos, $group_student_id);
		
		if (!check_if_group_student_is_in_school($material_test_action_id)) {
			set_extra_coins_for_medal($group_student_id, $material_test_action_id);
		}
	}

	function set_extra_coins_for_medal ($group_student_id, $material_test_action_id) {
		GLOBAL $connect;
		GLOBAL $bonus_coins_for_army_medal;

		try {

			$query = "SELECT gs.student_id,
							am.level
						FROM army_test_medal atm,
							army_medal am,
							group_student gs
						WHERE gs.id = :group_student_id
							AND atm.group_student_id = gs.id
							AND am.id = atm.army_medal_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$coins = 0;
			foreach ($bonus_coins_for_army_medal as $value) {
				if ($value['level'] == $query_result['level']) {
					$coins = $value['coins'];
					break;
				}
			}

			$query = "INSERT INTO coin_log (object_type, object_id, coins)
										VALUES ('extra_coin_for_test_medal', :object_id, :coins)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':object_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
			$stmt->execute();

			$query = "UPDATE student_coins SET total_coins = total_coins + :coins WHERE student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $query_result['student_id'], PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_student_army_medal ($material_test_action_infos, $last_group_student_id) {
		GLOBAL $connect;

		try {

			$group_student_ids = array();
			$total_percents = 0;
			$total_count = 0;
			foreach ($material_test_action_infos as $info) {
				array_push($group_student_ids, $info['group_student_id']);
				$total_percents += round(($info['actual_result']/$info['total_result'])*100);
				$total_count++;
			}
			// echo json_encode($material_test_action_infos)."<br>";
			// echo $total_count."<br>";
			if ($total_count == 3) {
				$percent = round($total_percents/$total_count);

				$query = "SELECT am.level,
								atm.id AS army_test_medal_id,
								atm.percent
							FROM army_test_medal atm,
								army_medal am
							WHERE atm.group_student_id IN (".implode(',', $group_student_ids).")
								AND am.id = atm.army_medal_id";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$row_count = $stmt->rowCount();

				$medal_info = get_army_level($percent);

				// echo $row_count."<br>";
				// echo json_encode($medal_info)."<br>";
				if ($row_count == 1) {
					$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					// if ($medal_info['level'] != $query_result['level']) {
					if ($percent != $query_result['percent']) {
						$query = "UPDATE army_test_medal SET group_student_id = :group_student_id,
															army_medal_id = :army_medal_id,
															percent = :percent,
															last_medal_change_date = NOW()
														WHERE id = :id";
						$stmt = $connect->prepare($query);
						$stmt->bindParam(':group_student_id', $last_group_student_id, PDO::PARAM_INT);
						$stmt->bindParam(':army_medal_id', $medal_info['army_medal_id'], PDO::PARAM_INT);
						$stmt->bindParam(':percent', $percent, PDO::PARAM_INT);
						$stmt->bindParam(':id', $query_result['army_test_medal_id'], PDO::PARAM_INT);
						$stmt->execute();
					}
				} else {
					$query = "INSERT INTO army_test_medal (army_medal_id, percent, group_student_id, last_medal_change_date)
													VALUES (:army_medal_id, :percent, :group_student_id, NOW())";

					$stmt = $connect->prepare($query);
					$stmt->bindParam(':army_medal_id', $medal_info['army_medal_id'], PDO::PARAM_INT);
					$stmt->bindParam(':group_student_id', $last_group_student_id, PDO::PARAM_INT);
					$stmt->bindParam(':percent', $percent, PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_army_level($mark) {
		GLOBAL $connect;

		try {
			$query = "SELECT am.id AS army_medal_id,
							am.level
						FROM army_medal am
						WHERE am.min_val <= :mark
							AND am.max_val >= :mark";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':mark', $mark, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = array('army_medal_id' => $query_result['army_medal_id'],
							'level' => $query_result['level']);

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_action_infos_by_gs_id ($group_student_ids) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.group_student_id,
							mta.id AS material_test_action_id,
							mtr.actual_result AS actual_result,
							mtr.total_result AS total_result
						FROM material_test_action mta,
							material_test_result mtr
						WHERE mta.group_student_id IN (".implode(',', $group_student_ids).")
							AND mta.is_finish = 1
							AND mtr.material_test_action_id = mta.id
							AND mtr.result_json IS NOT NULL
							AND mtr.actual_result IS NOT NULL
							AND mtr.total_result IS NOT NULL
						ORDER BY mtr.start_date DESC
						LIMIT 3";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$material_test_action_infos = array();

			foreach ($query_result as $value) {
				array_push($material_test_action_infos, array('material_test_action_id' => $value['material_test_action_id'],
															'actual_result' => $value['actual_result'],
															'total_result' => $value['total_result'],
															'group_student_id' => $value['group_student_id']));
			}

			return $material_test_action_infos;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_id_by_mta_id ($material_test_action_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.group_student_id
						FROM material_test_action mta
						WHERE mta.id = :material_test_action_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_student_id = $stmt->fetch(PDO::FETCH_ASSOC)['group_student_id'];
			return $group_student_id;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_ids_by_gs_id ($group_student_id) {
		GLOBAL $connect;

		try {

			$group_student_ids = array($group_student_id);

			$query = "SELECT gs.transfer_from_group,
							gs.student_id
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);



			$group_info_id = $query_result['transfer_from_group'];
			$student_id = $query_result['student_id'];

			$transfer_from_group = get_transfer_from_group($group_info_id, $group_student_id);

			if ($group_info_id != '') {
				// $group_info_id = $transfer_from_group;
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
			}

			return $group_student_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_transfer_from_group($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.transfer_from_group
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				return $stmt->fetch(PDO::FETCH_ASSOC)['transfer_from_group'];
			}
			return '';
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_if_group_student_is_in_army ($material_test_action_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT ag.id
						FROM material_test_action mta,
							group_student gs,
							army_group ag
						WHERE mta.id = :material_test_action_id
							AND gs.id = mta.group_student_id
							AND ag.group_info_id = gs.group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->execute();

			$row_count = $stmt->rowCount();

			return $row_count == 0 ? false : true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_if_group_student_is_in_school ($material_test_action_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT sg.id
						FROM material_test_action mta,
							group_student gs,
							school_group sg
						WHERE mta.id = :material_test_action_id
							AND gs.id = mta.group_student_id
							AND sg.group_info_id = gs.group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
			$stmt->execute();

			$row_count = $stmt->rowCount();

			return $row_count == 0 ? false : true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>