<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/sms/views.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/staff/sms/components/mobizon_balance.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<table class='table table-striped table-bordered sms-history-table'>
				<tr>
					<th>#</th>
					<th>Аты-жөні</th>
					<th>Телефоны</th>
					<th>Текст</th>
					<th>Статус</th>
					<th>Жіберілген уақыты</th>
				</tr>
				<?php include_once($root.'/staff/sms/components/sms_history.php'); ?>
			</table>
			<center><button class='btn btn-sm btn-info load-more-btn'>Загрузить еще +</button></center>
		</div>
	</div>
</div>

<script type="text/javascript">
	get_mobizon_balance();
</script>