<?php
	include_once('../common/connection.php');

	remove_overdue_material_links();
	remove_empty_groups();

	function remove_overdue_material_links() {
		GLOBAL $connect;

		try {

			$query = "DELETE ml, mlc
						FROM material_link ml
							JOIN material_link_content mlc
								ON mlc.material_link_id = ml.id
						WHERE DATE_ADD(access_until, INTERVAL 48 HOUR) <= NOW()";
			$stmt = $connect->prepare($query);
			$stmt->execute();

		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_empty_groups() {
		GLOBAL $connect;

		try {
			$query = "SELECT gi.id
						FROM group_info gi
						WHERE 0 = (SELECT count(gs.id)
									FROM group_student gs
									WHERE gs.group_info_id = gi.id
										AND gs.is_archive = 0)
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$group_ids = $stmt->fetchAll();
			print_r($group_ids);
			foreach ($group_ids as $value) {
				$stmt = $connect->prepare("UPDATE group_info SET is_archive = 1, status_change_date = NOW(), status_id = 4 WHERE id = :group_info_id");
				$stmt->bindParam(':group_info_id', $value['id'], PDO::PARAM_INT);
				$stmt->execute();
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

?>