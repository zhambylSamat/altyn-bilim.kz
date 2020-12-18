<?php
	include_once('sms_status.php');
	include_once('configuration.php');

	function save_sms($conn, $data){

		global $ACCEPTED;
		global $NEW;
		global $IS_PRODUCTION;
		global $IS_AUTO_SEND;
		global $WAITING_FOR_SEND;

		if (isset($IS_PRODUCTION) && !$IS_PRODUCTION) {
			return 'true';
		}
		// $message_id, $to_phone, $to_name, $to_type, $sms_text, $status
		try {
			if (count($data) != 0) {
				$query = "INSERT INTO sms_history (message_id, to_phone, to_name, to_type, sms_text, status, sent_time) VALUES ";
			    $qPart = array_fill(0, count($data), "(?, ?, ?, ?, ?, ?, ?)");
			    $query .= implode(",",$qPart);
			    $stmt = $conn->prepare($query);
			    $j = 1;
			    for($i = 0; $i<count($data); $i++) {
			    	$sent_time = NULL;
			    	if ($IS_AUTO_SEND && $data[$i]['code'] == 0) {
			    		$status = $data[$i]['status'] == "2" ? $ACCEPTED : $NEW;
			    		$sent_time = date("Y-m-d H:i:s");
			    	} else if ($data[$i]['code'] == 1) {
			    		$status = $WAITING_FOR_SEND;
			    	} else {
			    		$status = $WAITING_FOR_SEND;
			    	}
			    	$stmt->bindValue($j++, $data[$i]['message_id'], PDO::PARAM_INT);
			    	$stmt->bindValue($j++, $data[$i]['to_phone'], PDO::PARAM_STR);
			    	$stmt->bindValue($j++, $data[$i]['to_name'], PDO::PARAM_STR);
			    	$stmt->bindValue($j++, $data[$i]['to_type'], PDO::PARAM_STR);
			    	$stmt->bindValue($j++, $data[$i]['sms_text'], PDO::PARAM_STR);
			    	$stmt->bindValue($j++, $status, PDO::PARAM_STR);
			    	$stmt->bindValue($j++, $sent_time, PDO::PARAM_STR);
			    }
			    $stmt->execute();
			}
			return 'true';
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	function update_sms_history($id, $message_id, $status_id, $sent_time) {
		include_once('../connection.php');

		global $SMS_STATUS;
		global $ACCEPTED;
		global $NEW;

		$status = $status_id == "2" ? $ACCEPTED : $NEW;
		$res = array();
		try {

			$query = "UPDATE sms_history SET message_id = :message_id, status = :status, sent_time = :sent_time WHERE id = :id";
			$stmt = $conn->prepare($query);
			$stmt->bindParam(":message_id", $message_id, PDO::PARAM_INT);
			$stmt->bindParam(":status", $status, PDO::PARAM_STR);
			$stmt->bindParam(":sent_time", $sent_time, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			$res['status'] = $SMS_STATUS[$status];
			$res['success'] = true;
		} catch (PDOException $e) {
			$res['error'] = $e->getMessage();
		}
		return $res;
	}

	function set_sms_history_status($id, $status) {

		include_once('../connection.php');
		try {

			$stmt = $conn->prepare("UPDATE sms_history SET status = :status WHERE id = :id");
			$stmt->bindParam(":status", $status, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			return true;

			
		} catch (PDOException $e) {
			return false;
			throw $e;
		}

	}

	function save_sms_by_cron($data){
		global $ACCEPTED;
		global $NEW;
		global $IS_PRODUCTION;
		global $IS_AUTO_SEND;
		global $WAITING_FOR_SEND;

		if (isset($IS_PRODUCTION) && !$IS_PRODUCTION) {
			return 'true';
		}
		// $message_id, $to_phone, $to_name, $to_type, $sms_text, $status
		try {
			if (count($data) != 0) {
				$query = "INSERT INTO sms_history (message_id, to_phone, to_name, to_type, sms_text, status, sent_time) VALUES ";
				
				foreach ($data as $val) {
					$sent_time = NULL;
			    	if ($IS_AUTO_SEND && $val['code'] == 0) {
			    		$status = $val['status'] == "2" ? $ACCEPTED : $NEW;
			    		$sent_time = date("Y-m-d H:i:s");
			    	} else if ($val['code'] == 1) {
			    		$status = $WAITING_FOR_SEND;
			    	} else {
			    		$status = $WAITING_FOR_SEND;
			    	}
					$query .= "('".$val['message_id']."','".$val['to_phone']."','".$val['to_name']."','".$val['to_type']."','".$val['sms_text']."','".$status."','".$sent_time."')";
					include(dirname(__FILE__).'/../cron/connection.php');
					if (mysqli_query($conn, $query)) {
					    echo "Record inserted successfully<br><br>";
					} else {
					    echo "Error inserting record: " . mysqli_error($conn)."<br><br>";
					}
					// $parse = mysqli_fetch_assoc($result_sql);
				}


			    // $qPart = array_fill(0, count($data), "(?, ?, ?, ?, ?, ?, ?)");
			    // $query .= implode(",",$qPart);
			    // // $stmt = $conn->prepare($query);
			    // $j = 1;
			    // for($i = 0; $i<count($data); $i++) {
			    // 	$sent_time = NULL;
			    // 	if ($IS_AUTO_SEND && $data[$i]['code'] == 0) {
			    // 		$status = $data[$i]['status'] == "2" ? $ACCEPTED : $NEW;
			    // 		$sent_time = date("Y-m-d H:i:s");
			    // 	} else if ($data[$i]['code'] == 1) {
			    // 		$status = $WAITING_FOR_SEND;
			    // 	} else {
			    // 		$status = $WAITING_FOR_SEND;
			    // 	}
			    // 	// $stmt->bindValue($j++, $data[$i]['message_id'], PDO::PARAM_INT);
			    // 	// $stmt->bindValue($j++, $data[$i]['to_phone'], PDO::PARAM_STR);
			    // 	// $stmt->bindValue($j++, $data[$i]['to_name'], PDO::PARAM_STR);
			    // 	// $stmt->bindValue($j++, $data[$i]['to_type'], PDO::PARAM_STR);
			    // 	// $stmt->bindValue($j++, $data[$i]['sms_text'], PDO::PARAM_STR);
			    // 	// $stmt->bindValue($j++, $status, PDO::PARAM_STR);
			    // 	// $stmt->bindValue($j++, $sent_time, PDO::PARAM_STR);
			    // }
			    // $result_sql = mysqli_query($conn, $query);
			    // // $stmt->execute();
			    // while($value = mysqli_fetch_assoc($result_sql)){
			}
			return 'true';
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
?>