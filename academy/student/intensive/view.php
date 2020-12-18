<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_group_student_list () {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT gi.id AS group_info_id,
							gs.id AS group_student_id,
							gi.group_name,
							sj.title AS subject_title
						FROM group_student gs,
							group_info gi,
							subject sj
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND gi.status_id = 2
							AND gi.is_archive = 0
							AND gs.is_archive = 0
							AND sj.id = gi.subject_id
							AND (SELECT ag.id 
								FROM army_group ag
								WHERE ag.group_info_id = gi.id) IS NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('data' => array());
			foreach ($query_result as $value) {
				$group_schedules = get_group_schedules($value['group_info_id']);
				$result['data'][$value['group_info_id']] = array('group_student_id' => $value['group_student_id'],
																'group_name' => $value['group_name'],
																'subject_title' => $value['subject_title'],
																'schedules' => $group_schedules);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>