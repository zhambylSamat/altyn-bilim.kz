<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/sms/views.php');
	include_once($root.'/send_sms/sms_statuses.php');
	$limit = 40;
	$offset = 0;
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}
	$sms_history_list = get_sms_history_list($limit, $offset, $SMS_STATUS);

	$html = "";
	$count = $offset+1;
	foreach ($sms_history_list as $sms_history_idd => $value) {
		$html .= "<tr class='sms-history-row' id='message-id-".$value['message_id']."' data-id='".$value['message_id']."' data-is-finish-step='".$value['is_finish_step']."'>";
			$html .= "<td class='count'>".($count++)."</td>";
			$html .= "<td>".$value['to_name']."</td>";
			$html .= "<td>".$value['to_phone']."</td>";
			$html .= "<td>".$value['sms_text']."</td>";
			$html .= "<td class='status-text'>".$value['status']."</td>";
			$html .= "<td>".$value['sent_time']."</td>";
		$html .= "</tr>";
	}
	echo $html;
?>

<script type="text/javascript">
	check_sms_statuses();
</script>







<!-- https://docs.google.com/spreadsheets/d/1oANVYI89T6xW4B_rELjAuW4wnDGu7aRTKrMgDzROXCw/edit?usp=sharing -->
