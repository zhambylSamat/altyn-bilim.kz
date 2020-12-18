<?php

	// include_once('../send_sms/recipient_type.php');
	include_once("../send_sms/index.php");
	include_once('../connection.php');

	function get_sms_history($load, $offset) {

		global $conn;

		$res = array();
		try {

			$stmt = $conn->prepare("SELECT sh.id, 
										sh.message_id, 
										sh.to_phone, 
										sh.to_name, 
										sh.to_type, 
										sh.sms_text, 
										sh.status, 
										sh.sent_time
									FROM sms_history sh
									ORDER BY sh.created_date DESC
									LIMIT ".($load*$offset)." , ".$load);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			$row_count =  $stmt->rowCount();

			$res['html'] = get_html($query_result, $load, $offset);
			$res['last'] = $row_count < $load ? true : false;

			$res['success'] = true;
		} catch (PDOException $e) {
			$res['success'] = false;
			throw $e;
		}
		return $res;
	}

	function get_html($data, $load, $offset) {

		global $RECIPIENT_TYPE_P;
		global $RECIPIENT_TYPE_S;
		global $WAITING_FOR_SEND;
		global $NEW;
		global $REJECT_BY_ADMIN;
		global $SMS_STATUS;
		global $DELIVERED;

		$html = "";
		$count = $load*$offset;
		foreach ($data as $value) {

			$to_type = "";
			if ($value['to_type'] == $RECIPIENT_TYPE_P) {
				$to_type = "Ата-анаға";
			} else if ($value['to_type'] == $RECIPIENT_TYPE_S) {
				$to_type = "Оқушыға";
			}

			$to_name = $value['to_name'];
			$to_phone = "+".$value['to_phone'];
			$sms_text = $value['sms_text'];
			$sent_time = "N/A";
			if ($value['sent_time'] != '') {
				$sent_time = $value['sent_time'];
			}
			$status = "";
			if ($value['status'] == $WAITING_FOR_SEND || $value['status'] == $NEW) {
				$status .= "<div class='status'>";
				$status .= "<input type='hidden' name='id' value='".$value['id']."'>";
				$status .= "<input type='hidden' name='recipient' value='".$value['to_phone']."'>";
				$status .= "<button style='margin: 10px 0 10px 0; width: 100%;' class='btn btn-sm btn-success send_manually' data-action='send'>Жіберу</button>";
				$status .= "<button style='margin: 10px 0 10px 0; width: 100%;' class='btn btn-sm btn-danger send_manually' data-action='reject'>Жібермеу</button>";
				$status .= "</div>";
			} else if ($value['status'] == $REJECT_BY_ADMIN) {
				$status .= "<center><b class='text-danger'>".$SMS_STATUS[$REJECT_BY_ADMIN]['description']."</b></center>";
			} else {
				$text_color = "text-warning";
				if ($value['status'] == $DELIVERED) {
					$text_color = "text-success";
				} else if ($SMS_STATUS[$value['status']]['is_finish_step']) {
					$text_color = "text-danger";
				}
				$status = "<center><b class='".$text_color."'>".$SMS_STATUS[$value['status']]['description']."</b></center>";
				if (!$SMS_STATUS[$value['status']]['is_finish_step']) {
					$status .= "<button class='btn btn-xs btn-info pull-right check_status_manually' message_id='".$value['message_id']."' id='".$value['id']."'><span class='glyphicon glyphicon-refresh'></span></button>";
					$status .= "<input type='hidden' class='loading-status' load_status='load' message-id = '".$value['message_id']."' value='".$value['id']."'>";
				}
			}
			
			$html .= "<tr>";
			$html .= "<td>".(++$count)."</td>";
			$html .= "<td>".$to_type."</td>";
			$html .= "<td>".$to_name."</td>";
			$html .= "<td>".$to_phone."</td>";
			$html .= "<td class='sms_text'>".$sms_text."</td>";
			$html .= "<td class='sent_time'>".$sent_time."</td>";
			$html .= "<td>".$status."</td>";
			$html .= "</tr>";
		}
		return $html;
	}

	function get_count_sms_in_order() {
		
		global $conn;	

		$res = array();
		try {

			$stmt = $conn->prepare("SELECT count(sh.id) AS c
									FROM sms_history sh
									WHERE sh.status = 'waiting_for_send'");
			$stmt->execute();

			$res['data'] = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
			$res['success'] = true;
		} catch (PDOException $e) {
			$res['success'] = false;
			throw $e;
		}
		return $res;
	}

	$result = array();

	if (isset($_GET['load']) && $_GET['load'] != '') {
		$load = 50;
		$offset = $_GET['load'];
		$result['load_sms_history'] = get_sms_history($load, $offset);
	}
	if (isset($_GET['balance']) && $_GET['balance']=='get') {
		$result['get_balance'] = get_balance();
	}
	if (isset($_GET['waiting_for_send']) && $_GET['waiting_for_send']=="show") {
		$result['waiting_for_send'] = get_count_sms_in_order();
	}

	echo json_encode($result);
?>