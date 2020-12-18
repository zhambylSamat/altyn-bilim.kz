<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/send_sms/configuration.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/send_sms/sms_statuses.php');

	$conf = get_mobizon_config();

	function send_sms($data, $recipient_fio) {
		global $METHODS;
		global $IS_PRODUCTION;

		$method_name = 'send_sms';
		$params = array(
			'recipient' => $data['recipient'],
			'text' => $data['text']
		);
		$url_data = get_url_data($method_name, $params);
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$message_id = "";
		$status = "";
		$sms_code = 0;
		$res = array();

		$res = send_request($url, $method, $params);
		// $res = json_decode('{"code":0,"data":{"campaignId":"66003310","messageId":"111856192","status":2},"message":""}', true);

		$message_id = $res['data']['messageId'];
		$status = $res['data']['status'];
		$sms_code = $res['code'];

		$sms_response = array(
			"response_code" => $sms_code,
			"message_id" => $message_id,
			"status" => $status,
			"to_phone" => $data['recipient'],
			"to_name" => $recipient_fio,
			"sms_text" => $data['text'],
			'sms_response' => json_encode($res, JSON_UNESCAPED_UNICODE),
			'sent_time' => date('Y-m-d H:i:s')
		);

		return $sms_response;
	}

	function get_balance() {

		global $METHODS;

		$method_conf = 'get_balance';
		$url_data = get_url_data($method_conf, array());
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$res = send_request($url, $method, $params);

		return $res['data']['balance']." ".$res['data']['currency'];
	}

	function sms_status($sms_ids) {

		global $METHODS;
		global $IDS;

		$method_conf = 'sms_status';
		$params = array(
			'ids' => $sms_ids
		);
		$url_data = get_url_data($method_conf, $params);
		$url = $url_data['url'];
		$method = $url_data['method'];
		$params = $url_data['params'];
		$res = send_request($url, $method, $params);
		return $res;
	}

	function send_request($url, $method, $data) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result_json = json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $result_json;
	}

	function get_url_data($method_name, $params) {
		
		global $MULTIPLE;
		global $SINGLE;
		global $conf;

		$url = $conf['url'].(sprintf($conf['urls'][$method_name]['url'], 'json', $conf['key']));
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

	function save_sms_response_to_db ($sms_response) {
		GLOBAL $connect;
		GLOBAL $ACCEPTED;
		GLOBAL $NEW;
		try {
			$status = $sms_response['status'] == 2 ? $ACCEPTED : $NEW;
			$query = "INSERT INTO sms_history (message_id, sms_response_code, to_name, to_phone, sms_text, status, sms_response, sent_time)
										VALUES (:message_id, :sms_response_code, :to_name, :to_phone, :sms_text, :status, :sms_response, :sent_time);";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':message_id', $sms_response['message_id'], PDO::PARAM_INT);
			$stmt->bindParam(':sms_response_code', $sms_response['response_code'], PDO::PARAM_INT);
			$stmt->bindParam(':to_name', $sms_response['to_name'], PDO::PARAM_STR);
			$stmt->bindParam(':to_phone', $sms_response['to_phone'], PDO::PARAM_STR);
			$stmt->bindParam(':sms_text', $sms_response['sms_text'], PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->bindParam(':sms_response', $sms_response['sms_response'], PDO::PARAM_STR);
			$stmt->bindParam(':sent_time', $sms_response['sent_time'], PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function save_to_db ($sms_response) {
		GLOBAL $connect;
		GLOBAL $ACCEPTED;
		GLOBAL $NEW;
		try {
			$status = $sms_response['status'] == "2" ? $ACCEPTED : $NEW;
			$query = "INSERT INTO sms_history (message_id, sms_response_code, to_name, to_phone, sms_text, status, sms_response, sent_time)
										VALUES (:message_id, :sms_response_code, :to_name, :to_phone, :sms_text, :status, :sms_response, :sent_time);";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':message_id', $sms_response['message_id'], PDO::PARAM_INT);
			$stmt->bindParam(':sms_response_code', $sms_response['response_code'], PDO::PARAM_INT);
			$stmt->bindParam(':to_name', $sms_response['to_name'], PDO::PARAM_STR);
			$stmt->bindParam(':to_phone', $sms_response['to_phone'], PDO::PARAM_STR);
			$stmt->bindParam(':sms_text', $sms_response['sms_text'], PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_INT);
			$stmt->bindParam(':sms_response', $sms_response['sms_response'], PDO::PARAM_STR);
			$stmt->bindParam(':sent_time', $sms_response['sent_time'], PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>