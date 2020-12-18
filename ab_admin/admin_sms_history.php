<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'sms_history';
	}
?>
<button class='btn btn-info btn-sm' id='refresh_sms_history'>Обновить</button>
<br>
<br>
<div id='sms_history'>
	<?php include_once('index_sms_history.php'); ?>
</div>