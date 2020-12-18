<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/send_sms/index.php');
	include_once($root.'/send_sms/sms_statuses.php');

    check_admin_access();

    if (isset($_GET['check_sms_status'])) {
    	try {
    		
    		$message_ids = json_decode($_GET['message_ids'], true);

    		$sms_status_response = sms_status($message_ids);

    		$data['message_ids'] = $message_ids;

    		$result = array();
    		$query = "UPDATE sms_history SET status = :status WHERE message_id = :message_id";
    		foreach ($sms_status_response['data'] as $value) {
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':status', $value['status'], PDO::PARAM_STR);
    			$stmt->bindParam(':message_id', $value['id'], PDO::PARAM_INT);
    			$stmt->execute();

    			$result[$value['id']] = array('status_text' => $SMS_STATUS[$value['status']]['description'],
    											'is_finish_step' => $SMS_STATUS[$value['status']]['is_finish_step'] ? 1 : 0);
    		}
    		$data['statuses'] = $result;
    		$data['success'] = true;
    		
    	} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'Error: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
    } else if (isset($_GET['get_mobizon_balance'])) {
        try {


            $data['balance'] = get_balance();
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    }
?>