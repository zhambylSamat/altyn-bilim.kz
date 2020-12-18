<?php
include('../connection.php');
if(!isset($_GET['data_num'])){
	throw new Exception('Error!');
}

$group_info_num = $_GET['data_num'];
try {
	$stmt = $conn->prepare("SELECT week_id FROM schedule where group_info_num = :group_info_num");
	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);	
	$stmt->execute();
	$group_schedule = $stmt->fetchAll();
	$week_id = array();
	foreach ($group_schedule as $value) {
		array_push($week_id, $value['week_id']);
	}

	$stmt = $conn->prepare("SELECT start_lesson, finish_lesson, office_number FROM group_info where group_info_num = :group_info_num");
	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);	
	$stmt->execute();
	$group_info = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	echo "Error : ".$e->getMessage()." !!!";
}
?>
<form class='form-inline' id='schedule-form' method='post'>
	<div class='schedules'>
		<center>
			<a class='btn btn-sm btn-week <?php echo (in_array(1, $week_id)) ? 'active' : '';?>' week-id='1'>Пн.</a>
			<a class='btn btn-sm btn-week <?php echo (in_array(2, $week_id)) ? 'active' : '';?>' week-id='2'>Вт.</a>
			<a class='btn btn-sm btn-week <?php echo (in_array(3, $week_id)) ? 'active' : '';?>' week-id='3'>Ср.</a>
			<a class='btn btn-sm btn-week <?php echo (in_array(4, $week_id)) ? 'active' : '';?>' week-id='4'>Чт.</a>
			<a class='btn btn-sm btn-week <?php echo (in_array(5, $week_id)) ? 'active' : '';?>' week-id='5'>Пт.</a>
			<a class='btn btn-sm btn-week <?php echo (in_array(6, $week_id)) ? 'active' : '';?>' week-id='6'>Сб.</a>
		</center>
		<div class='hidden-datas'>
			<?php for($i = 0; $i<count($week_id); $i++){ ?>
			<input type="hidden" name="week_id[]" value='<?php echo $week_id[$i];?>'>
			<?php } ?>
		</div>
		<br>
		<div class='time form-group' style="width: 100%;">
			<p style='width: 50%;display: inline-block; text-align: right; vertical-align: top;'><b>Сабақ басталатын уақыт: </b></p>&nbsp;&nbsp;
			<div style='display: inline-block;'><input type="number" name='start_hour' class='form-control' max='24' min='0' step='1' name="" value='<?php echo (isset($group_info['start_lesson'])) ? substr($group_info['start_lesson'],0,2) : "0" ;?>'>сағ. <input type="number" name='start_minute' class='form-control' max='59' min='0' step='1' name="" value='<?php echo (isset($group_info['start_lesson'])) ? substr($group_info['start_lesson'],3,2) : "0" ;?>'>мин.</div>
			<br>
			<p style='width: 50%;display: inline-block; text-align: right; vertical-align: top;'><b>Сабақ бітетін уақыт: </b></p>&nbsp;&nbsp;
			<div style='display: inline-block;'><input type="number" name='finish_hour' class='form-control' max='24' min='0' step='1' name="" value='<?php echo (isset($group_info['finish_lesson'])) ? substr($group_info['finish_lesson'],0,2) : "0" ;?>'>сағ. <input type="number" name='finish_minute' class='form-control' max='59' min='0' step='1' name="" value='<?php echo (isset($group_info['finish_lesson'])) ? substr($group_info['finish_lesson'],3,2) : "0" ;?>'>мин.</div>
		</div>
		<div style="width: 100%;">
			<p class='h4' style='width: 50%; text-align: right; display: inline-block;'><b>Кабинет: </b></p>&nbsp;&nbsp;
			<input type="text" class='form-control' name="office" placeholder="Номер кабинета" value="<?php echo (isset($group_info['office_number'])) ? $group_info['office_number'] : "" ?>">
		</div>
	</div>
	<br>
	<input type="hidden" name="data_num" value='<?php echo $group_info_num;?>'>
	<center><input type="submit" name="save_group_schedule" class='btn btn-sm btn-success' value='Сақтау'></center>
</form>