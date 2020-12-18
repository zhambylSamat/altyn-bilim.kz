<?php
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'ent_result';
	}
	$stmt = $conn->prepare("SELECT parse FROM config_ent WHERE id = 1");
	$stmt->execute();
	$parse = $stmt->fetch(PDO::FETCH_ASSOC)['parse'];
?>
<button class='btn btn-info btn-sm' id='refresh_ent_result'>Обновить</button>
<button class='btn btn-sm btn-info' style="<?php echo $parse == "0" ? "" : "display:none;"; ?>" id='start-parsing'>Начать сканировать результат</button>
<button class='btn btn-sm btn-danger' style="<?php echo $parse == "1" ? "" : "display:none;"; ?>" id='stop-parsing'>Остановить сканирование результат</button>
<br>
<br>
<div id='ent_result'>
	<?php include_once('index_ent_result.php'); ?>
</div>