<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	

	function get_sms_history_list ($limit, $offset, $statuses) {

		GLOBAL $connect;

		try {

			$query = "SELECT sh.id,
							sh.to_name,
							sh.to_phone,
							sh.sms_text,
							sh.status,
							sh.message_id,
							DATE_FORMAT(sh.sent_time, '%Y-%m-%d') AS sent_time
						FROM sms_history sh
						ORDER BY sh.sent_time DESC
						LIMIT ".$offset.', '.$limit;


						// LIMIT ".$limit."
						// OFFSET ".$offset
			$stmt = $connect->prepare($query);
			// $stmt->bindParam(':selection_limit', $limit, PDO::PARAM_INT);
			// $stmt->bindParam(':selection_offset', $offset, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			// return $query_result;
			$result = array();

			foreach ($query_result as $value) {
				$result[$value['id']] = array('to_name' => $value['to_name'],
												'to_phone' => $value['to_phone'],
												'sms_text' => $value['sms_text'],
												'message_id' => $value['message_id'],
												'status' => $statuses[$value['status']]['description'],
												'is_finish_step' => $statuses[$value['status']]['is_finish_step'] ? 1 : 0,
												'sent_time' => $value['sent_time']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>