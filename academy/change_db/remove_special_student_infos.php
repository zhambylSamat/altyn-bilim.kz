<?php
	include_once('../common/connection.php');

	// $students = array(302, 313);
	$students = array(886);

	function get_marathon_students() {
		GLOBAL $connect;

		try {
			$except_students = array(747, 805, 358, 553, 478, 307);

			$query = "SELECT gs.student_id
						FROM group_student gs
						WHERE gs.group_info_id = 458
							AND 1 = (SELECT count(gs2.id)
									FROM group_student gs2
									WHERE gs2.student_id = gs.student_id)
							AND gs.student_id NOT IN (".implode(',', $except_students).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['student_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	echo "1. ".json_encode($students)." ".count($students)."<br><br>";

	delete_students($students);
	delete_student_coins($students);
	delete_student_promo_codes($students);
	delete_student_balances($students);

	$group_student_infos = get_group_student_infos_then_delete($students);
	echo "2. ".json_encode($group_student_infos)."<br><br>";

	if (count($group_student_infos) > 0) {
		delete_group_student_payment($group_student_infos);
		delete_group_if_zero_student($group_student_infos);

		
		remove_forced_material_access($group_student_infos);
		$tutorial_video_action_ids = get_tutorial_video_action_then_delete($group_student_infos);
		if (count($tutorial_video_action_ids) > 0) {
			delete_tutorial_video_action_logs($tutorial_video_action_ids);
		}

		$tutorial_document_action_ids = get_tutorial_document_action_then_delete($group_student_infos);
		if (count($tutorial_document_action_ids) > 0) {
			delete_tutorial_document_action_logs($tutorial_document_action_ids);
		}

		$end_video_action_ids = get_end_video_action_then_delete($group_student_infos);
		if (count($end_video_action_ids) > 0) {
			delete_end_video_action_logs($end_video_action_ids);
		}

		$material_test_action_ids = get_material_test_action_then_delete($group_student_infos);
		if (count($material_test_action_ids) > 0) {
			delete_material_test_result($material_test_action_ids);
		}
	}

	function remove_forced_material_access($group_student_infos) {
		GLOBAL $connect;

		try {
			
			$group_student_ids = array();

			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			if (count($group_student_ids) > 0) {
				$query = "DELETE FROM forced_material_access WHERE group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_material_test_result ($material_test_action_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM material_test_result WHERE material_test_action_id IN (".implode(',', $material_test_action_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_tutorial_document_action_logs ($tutorial_document_action_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_document_action_log WHERE tutorial_document_action_id IN (".implode(',', $tutorial_document_action_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_end_video_action_logs ($end_video_action_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM end_video_action_log WHERE end_video_action_id IN (".implode(',', $end_video_action_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_tutorial_video_action_logs ($tutorial_video_action_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_video_action_log WHERE tutorial_video_action_id IN (".implode(',', $tutorial_video_action_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_action_then_delete ($group_student_infos) {
		GLOBAL $connect;

		try {

			$material_test_action_ids = array();
			$group_student_ids = array();

			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			if (count($group_student_ids) > 0) {
				$query = "SELECT mta.id AS material_test_action_id
							FROM material_test_action mta
							WHERE mta.group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$query_result = $stmt->fetchAll();
				foreach ($query_result as $value) {
					array_push($material_test_action_ids, $value['material_test_action_id']);
				}

				$query = "DELETE FROM material_test_action WHERE group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
			}

			return $material_test_action_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_end_video_action_then_delete ($group_student_infos) {
		GLOBAL $connect;

		try {

			$end_video_action_ids = array();
			$group_student_ids = array();

			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			if (count($group_student_ids) > 0) {
				$query = "SELECT eva.id AS end_video_action_id
							FROM end_video_action eva
							WHERE eva.group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$query_result = $stmt->fetchAll();
				foreach ($query_result as $value) {
					array_push($end_video_action_ids, $value['end_video_action_id']);
				}

				$query = "DELETE FROM end_video_action WHERE group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
			}

			return $end_video_action_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_document_action_then_delete ($group_student_infos) {
		GLOBAL $connect;

		try {

			$tutorial_document_action_ids = array();
			$group_student_ids = array();

			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			if (count($group_student_ids) > 0) {
				$query = "SELECT tda.id AS tutorial_document_action_id
							FROM tutorial_document_action tda
							WHERE tda.group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$query_result = $stmt->fetchAll();
				foreach ($query_result as $value) {
					array_push($tutorial_document_action_ids, $value['tutorial_document_action_id']);
				}

				$query = "DELETE FROM tutorial_document_action WHERE group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
			}

			return $tutorial_document_action_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_video_action_then_delete ($group_student_infos) {
		GLOBAL $connect;

		try {

			$tutorial_video_action_ids = array();
			$group_student_ids = array();

			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			if (count($group_student_ids) > 0) {
				$query = "SELECT tva.id AS tutorial_video_action_id
							FROM tutorial_video_action tva
							WHERE tva.group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
				$query_result = $stmt->fetchAll();
				foreach ($query_result as $value) {
					array_push($tutorial_video_action_ids, $value['tutorial_video_action_id']);
				}

				$query = "DELETE FROM tutorial_video_action WHERE group_student_id IN (".implode(',', $group_student_ids).")";
				$stmt = $connect->prepare($query);
				$stmt->execute();
			}

			return $tutorial_video_action_ids;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_group_if_zero_student ($group_student_infos) {
		GLOBAL $connect;

		try {

			$query_group_student_count = "SELECT count(gs.id) AS group_student_count
									FROM group_student gs
									WHERE gs.group_info_id = :group_info_id";

			foreach ($group_student_infos as $value) {
				$stmt = $connect->prepare($query_group_student_count);
				$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
				$stmt->execute();
				$group_student_count = $stmt->fetch(PDO::FETCH_ASSOC)['group_student_count'];

				if ($group_student_count == 0) {
					$query_remove_group = "DELETE FROM group_info WHERE id = :group_info_id";
					$stmt = $connect->prepare($query_remove_group);
					$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
					$stmt->execute();

					$query_remove_schedules = "DELETE FROM group_schedule WHERE group_info_id = :group_info_id";
					$stmt = $connect->prepare($query_remove_schedules);
					$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
					$stmt->execute();

					$query_remove_marathon_group = "DELETE FROM marathon_group WHERE group_info_id = :group_info_id";
					$stmt = $connect->prepare($query_remove_marathon_group);
					$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
					$stmt->execute();

					$query_remove_army_group = "DELETE FROM army_group WHERE group_info_id = :group_info_id";
					$stmt = $connect->prepare($query_remove_army_group);
					$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_group_student_payment ($group_student_infos) {
		GLOBAL $connect;

		try {

			$group_student_ids = array();
			foreach ($group_student_infos as $value) {
				array_push($group_student_ids, $value['group_student_id']);
			}

			$query = "DELETE FROM group_student_payment WHERE group_student_id IN (".implode(',', $group_student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_student_infos_then_delete ($student_ids) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.id AS group_student_id,
							gs.group_info_id AS group_info_id
						FROM group_student gs
						WHERE gs.student_id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			$group_student_infos = array();

			foreach ($query_result as $value) {
				array_push($group_student_infos, array('group_student_id' => $value['group_student_id'],
														'group_info_id' => $value['group_info_id']));
			}

			$query = "DELETE FROM group_student WHERE student_id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();

			return $group_student_infos;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_student_balances ($student_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM student_balance WHERE student_id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_student_promo_codes ($student_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM student_promo_code WHERE student_id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_student_coins ($student_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM student_coins WHERE student_id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function delete_students ($student_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM student WHERE id IN (".implode(',', $student_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>