<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_marathon_list () {
		GLOBAL $connect;

		try {

			$result = array();

			$query = "SELECT mf.id,
							mf.last_name,
							mf.first_name,
							mf.phone,
							mf.city,
							mf.school,
							mf.class,
							mf.instagram,
							mf.subject_ids
						FROM marathon_form mf
						WHERE mf.is_commit = 0
						ORDER BY mf.created_date ASC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			$query_result = $stmt->fetchAll();

			$all_subject_info = $row_count > 0 ? get_all_subject_info() : array();

			foreach ($query_result as $value) {
				$subjects = array();
				if ($value['subject_ids'] != '' && $value['subject_ids'] != 'null') {
					foreach (json_decode($value['subject_ids']) as $subject_id) {
						if (array_key_exists($subject_id, $all_subject_info)) {
							$subjects[$subject_id] = $all_subject_info[$subject_id];
						}
					}
				}
				$result[$value['id']] = array('marathon_form_id' => $value['id'],
												'last_name' => $value['last_name'],
												'first_name' => $value['first_name'],
												'phone' => $value['phone'],
												'city' => $value['city'],
												'school' => $value['school'],
												'class' => $value['class'],
												'instagram' => $value['instagram'],
												'subjects' => $subjects);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_all_subject_info() {
		GLOBAL $connect;

		try {

			$query = "SELECT sj.id,
							sj.title
						FROM subject sj";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['id']] = $value['title'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_marathon_group_list () {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id, 
							gi.group_name
						FROM marathon_group mt,
							group_info gi
						WHERE gi.id = mt.group_info_id
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = $value['group_name'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_marathon_groups_by_subject_ids ($subject_ids) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.group_name,
							gi.id,
							gi.subject_id,
							sj.title
						FROM group_info gi,
							subject sj
						WHERE gi.subject_id IN (".implode(',', $subject_ids).")
							AND gi.id IN (SELECT mg.group_info_id FROM marathon_group mg)
							AND sj.id = gi.subject_id
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();

			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = array('group_name' => $value['group_name'],
												'subject_id' => $value['subject_id'],
												'subject_title' => $value['title']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>