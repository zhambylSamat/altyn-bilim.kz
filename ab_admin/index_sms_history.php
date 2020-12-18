<?php
	include_once("../connection.php");
	include_once("../send_sms/index.php");

	$sms_history_query_result = array();
	try {

		$ch = curl_init("http://localhost/altynbilim/ab_admin/load_sms_history.php?load=0&balance=get&waiting_for_send=show");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result_json = json_decode(curl_exec($ch), true);
		curl_close($ch);

		$sms_history_query_result = isset($result_json['load_sms_history']) ? $result_json['load_sms_history'] : array();
		$get_balance = isset($result_json['get_balance']) ? $result_json['get_balance'] : "";
		$waiting_for_send_sms = isset($result_json['waiting_for_send']['data']) ? $result_json['waiting_for_send']['data'] : 0;
		$call_js_function = "";
		if ($sms_history_query_result['html'] != '') {
			$call_js_function = "<script type='text/javascript'>check_sms_statuses.call();</script>";
		}

	} catch (PDOException $e) {
		throw $e;
	}
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>

		<h3>Баланс: <span id='sms_balance'><?php echo $get_balance == "" ? "N/A" : $get_balance; ?></span> <button class='btn btn-xs btn-info' id='refresh-sms-balance'><span class='glyphicon glyphicon-refresh'></span></button></h3>
		<span><p style='background-color:#F0AD4E;' id='sms_in_order'><?php echo $waiting_for_send_sms == "" || $waiting_for_send_sms == 0 ? "" : "<b id='count'>".$waiting_for_send_sms."</b> SMS-тің жіберілуі күтілуде";?></p></span>

		<table class='table table-bordered table-striped sms_history_table'>
			<tr style='position: sticky; top: 0px; z-index: 10; width: 100%;'>
				<th style='width: 2%;'>#</th>
				<th style='width: 10%;'>Кімге</th>
				<th style='width: 20%;'>Оқушының Аты-жөні</th>
				<th style='width: 10%;'>Телефон</th>
				<th style='width: 30%;'>Текст</th>
				<th style='width: 10%;'>Жіберілген уақыты</th>
				<th style='width: 20%;'>Статус</th>
			</tr>
			<?php
				echo isset($sms_history_query_result['html']) ? $sms_history_query_result['html'] : "<tr><td colspan='7'>N/A</td></tr>";
			?>
		</table>
		<center>
			<button class='btn btn-md btn-info' id='load_sms_history' data-count='1'>Загрузить больше</button>
			<br><br>
		</center>
	</div>
</div>
<?php echo $call_js_function; ?>