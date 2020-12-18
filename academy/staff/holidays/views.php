<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');


	function get_holidays_list () {
		GLOBAL $connect;

		try {

			$query = "SELECT h.id,
							DATE_FORMAT(h.from_date, '%d.%m.%Y') AS holiday_from_date,
							DATE_FORMAT(h.to_date, '%d.%m.%Y') AS holiday_to_date,
							h.title,
							h.comment
						FROM holidays h
						WHERE h.to_date >= DATE_FORMAT(NOW(), '%Y-%m-%d')
						ORDER BY h.to_date DESC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['id']] = array('holiday_from_date' => $value['holiday_from_date'],
												'holiday_to_date' => $value['holiday_to_date'],
												'title' => $value['title'],
												'comment' => $value['comment']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>