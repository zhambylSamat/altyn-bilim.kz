<?php
	include_once('../common/connection.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	// echo $root;

	$interval_days = 8;

	$class_works = get_class_work_infos($interval_days);

	if (count($class_works) > 0) {
		remove_class_work_datas_and_files($class_works);
		remove_class_work_submites($class_works);
	}

	function get_class_work_infos ($interval_days) {
		GLOBAL $connect;

		try {

			$query = "SELECT gscws.id
						FROM group_student_class_work_submit gscws
						WHERE DATE_ADD(gscws.submit_date, INTERVAL :interval_days DAY) <= NOW()
						ORDER BY gscws.submit_date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':interval_days', $interval_days, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_class_work_datas_and_files ($group_student_class_work_submit_ids) {
		GLOBAL $connect;
		GLOBAL $root;

		try {
			
			$query = "SELECT gscwsf.id,
							gscwsf.file_link
						FROM group_student_class_work_submit_files gscwsf
						WHERE gscwsf.group_student_class_work_submit_id IN (".implode(',', $group_student_class_work_submit_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			print_r($query_result);

			foreach ($query_result as $value) {
				if (file_exists($root.$value['file_link'])) {
					unlink($root.$value['file_link']);
				}
			}

			$query = "DELETE FROM group_student_class_work_submit_files WHERE group_student_class_work_submit_id IN (".implode(',', $group_student_class_work_submit_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();


		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_class_work_submites ($group_student_class_work_submit_ids) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM group_student_class_work_submit WHERE id IN (".implode(',', $group_student_class_work_submit_ids).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>