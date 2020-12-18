<?php
	include_once('configuration.php');
	include_once('send_request.php');
	include_once('sms_patterns.php');
	include_once('save_sms_in_db.php');
	include_once('recipient_type.php');
	

	$conf = get_mobizon_config();

	function get_url_data($method_name, $params) {
		
		global $JSON;
		global $MULTIPLE;
		global $SINGLE;
		global $conf;

		$url = $conf['url'].(sprintf($conf['urls'][$method_name]['url'], $JSON, $conf['key']));
		$method = $conf['urls'][$method_name]['method'];

		$res = array(
			"url" => $url,
			"method" => $method,
			"params" => ""
		);

		if ($method == "POST") {
			$params_arr = array();
			foreach ($conf['urls'][$method_name]['params'] as $key => $value) {
				if ($value == $MULTIPLE) {
					$params_arr[$key] = array();
					foreach ($params[$key] as $val) {
						array_push($params_arr[$key], $val);
					}
				} else if ($value == $SINGLE) {
					$params_arr[$key] = $params[$key];
				}
			}

			$res["params"] = json_encode($params_arr, true);
		}

		return $res;
	}

	function get_balance() {

		global $METHODS;

		$method_name = $METHODS['1'];
		$url_data = get_url_data($method_name, array());
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$res = send_request($url, $method, $params);

		return $res['data']['balance']." ".$res['data']['currency'];
	}

	function sms_status($sms_ids) {

		global $METHODS;
		global $IDS;

		$method_name = $METHODS['2'];
		$params = array(
			$IDS => $sms_ids
		);
		$url_data = get_url_data($method_name, $params);
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$res = send_request($url, $method, $params);
		return $res;
	}

	function send_sms($data, $recipient_type, $recipient_fio) {
		global $RECIPIENT;
		global $TEXT;
		global $METHODS;
		global $IS_PRODUCTION;
		global $IS_AUTO_SEND;

		$method_name = $METHODS['3'];
		$params = array(
			$RECIPIENT => $data[$RECIPIENT],
			$TEXT => $data[$TEXT]
		);
		$url_data = get_url_data($method_name, $params);
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$message_id = "";
		$status = "";
		$sms_code = 0;
		$res = array();
		if (isset($IS_PRODUCTION) && $IS_PRODUCTION && isset($IS_AUTO_SEND) && $IS_AUTO_SEND) {
			$res = send_request($url, $method, $params);
			if ($res['code'] == 0) {
				$message_id = $res['data']['messageId'];
				$status = $res['data']['status'];
			} else {
				$sms_code = $res['code'];
			}
		}

		$sms_response = array(
			"code" => $sms_code,
			"message_id" => $message_id,
			"status" => $status,
			"to_phone" => $data[$RECIPIENT],
			"to_name" => $recipient_fio,
			"to_type" => $recipient_type,
			"sms_text" => $data[$TEXT]
		);

		// 'manual_sms_response' : [{
		// 	'status' : 1 or 2,
		// 	'message_id' : or null,
		// 	'to_phone' : recipient_phone,
		// 	'to_name' : recipient_name,
		// 	'to_type' : to_parent or to_student,
		// 	'sms_text' : sms_text
		// }]

		$res['manual_sms_response'] = $sms_response;

		return $res;
	}

	function send_sms_manually($data) {
		global $METHODS;
		global $RECIPIENT;
		global $TEXT;

		$method_date = $METHODS['3'];
		$params = array(
			$RECIPIENT => $data[$RECIPIENT],
			$TEXT => $data[$TEXT]
		);
		$url_data = get_url_data($method_date, $params);
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$message_id = "";
		$status = "";
		$res = array();
		$res['success'] = false;
		$res['message'] = "Неопознанная ошибка!";
		$res_send_sms = send_request($url, $method, $params);
		// print_r($res_send_sms);
		if ($res_send_sms['code'] == 0) {
			$message_id = $res_send_sms['data']['messageId'];
			$status = $res_send_sms['data']['status'];
			$sent_time = date("Y-m-d H:i:s");

			$res_update_sms_history = update_sms_history($data['id'], $message_id, $status, $sent_time);
			if ($res_update_sms_history['success']) {
				$res['status'] = $res_update_sms_history['status'];
				$res['sent_time'] = $sent_time;
				$res['message_id'] = $message_id;
				$res['success'] = true;
			} else {
				$res['success'] = false;
			}
		} else if ($res_send_sms['code'] == 1) {
			$res['message'] = $res_send_sms['message'];
		}

		return $res;
	}


	if (isset($_GET['send_manually']) && $_GET['send_manually']=='send') {
		$id = $_POST['id'];
		$recipient = $_POST['recipient'];
		$text = $_POST['text'];

		$data = array(
			"id" => $id,
			$RECIPIENT => $recipient,
			$TEXT => $text
		);
		$res = array();
		$res['success'] = false; 
		$res['message'] = "Неопознанная ошибка!";
		if ($id != '' && $recipient != '' && $text != '') {
			$res_send_sms_manually = send_sms_manually($data);
			if ($res_send_sms_manually['success']) {
				$res['status'] = $res_send_sms_manually['status'];
				$res['sent_time'] = $res_send_sms_manually['sent_time'];
				$res['message_id'] = $res_send_sms_manually['message_id'];
				$res['success'] = true;
			} else {
				$res['message'] = $res_send_sms_manually['message'];
			}
		} else {
			$res['success'] = false;
		}
		echo json_encode($res);
	} else if (isset($_GET['send_manually']) && $_GET['send_manually']=='reject') {
		$id = $_POST['id'];
		$status = $REJECT_BY_ADMIN;

		$data = array(
			"id" => $id,
			"status" => $status
		);
		$res = array();
		$res['success'] = false;
		if ($id != '') {
			if (set_sms_history_status($id, $status)) {
				$res['status'] = $SMS_STATUS[$REJECT_BY_ADMIN];	
				$res['sent_time'] = "N/A";
				$res['success'] = true;
			} else {
				$res['status'] = false;
			}
		} else {
			$res['success'] = false;
		}
		echo json_encode($res);
	} else if (isset($_GET['check_status_manually']) && $_GET['check_status_manually'] != '') {
		$message_id = array($IDS => $_POST['message_id']);
		$id = $_POST['id'];

		$res = array();
		$res['status'] = false;
		if ($message_id != '') {
			$res_sms_status = sms_status($message_id);
			if ($res_sms_status['code'] == 0) {
				$res['fail'] = $res_sms_status['data'][0]['status'] == $DELIVERED ? false : true;
				$res['status'] = $SMS_STATUS[$res_sms_status['data'][0]['status']];
				$res['message_id'] = $message_id['ids'];
				$res['success'] = true;

				set_sms_history_status($id, $res_sms_status['data'][0]['status']);
			}
		} 

		echo json_encode($res);
	}
?>