<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'schedule';
	}
	try {
		$stmt = $conn->prepare("SELECT gi.office_number office_number,
									gi.start_lesson start_lesson,
									gi.finish_lesson finish_lesson,
									sch.week_id week_id,
									gi.group_info_num group_info_num,
									gi.group_name group_name,
									s.student_num student_num,
									s.name name,
									s.surname surname
								FROM group_info gi,
									group_student gs,
									student s,
									schedule sch
								WHERE gs.group_info_num = gi.group_info_num
									AND sch.group_info_num = gi.group_info_num
									AND s.student_num = gs.student_num
									AND s.block = 0
									AND gi.start_lesson NOT IN ('00:00:00:')
									AND gi.finish_lesson NOT IN ('00:00:00')
									AND s.block = 0
								ORDER BY gi.office_number,
									gi.start_lesson,
									gi.finish_lesson,
									sch.week_id,
									gi.group_name,
									s.name ASC");
		$stmt->execute();
		$result_from_query = $stmt->fetchAll();
		$result = array();
		foreach ($result_from_query as $value) {
			$tt = substr($value['start_lesson'],0,5)."-".substr($value['finish_lesson'],0,5);
			$result[$value['office_number']][$tt][$value['week_id']][$value['group_info_num']][$value['group_name']][$value['student_num']] = $value['surname']." ".$value['name'];
		}
		// foreach ($result as $key => $value) {
		// 	print_r($value);
		// 	echo "<br><br>";
		// }
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div class='row'>
	<div class='col-md-12 col-sm-12'>
		<?php $count = 0; foreach($result as $office_key => $office_value){ ?>
		<center><h4><b><?php echo "Кабинет: ".$office_key;?></b></h4></center>
		<table class='table table-bordered'>
			<tr>
				<th><center>Сабақ уақыттары</center></th>
				<th><center>Понедельник</center></th>
				<th><center>Вторник</center></th>
				<th><center>Среда</center></th>
				<th><center>Четверг</center></th>
				<th><center>Пятница</center></th>
				<th><center>Суббота</center></th>
			</tr>

			<?php foreach ($office_value as $time_key => $time_value) { ?>
			<tr>
				<td>
					<center>
						<?php echo "<b>".$time_key."</b>"; ?>
					</center>
				</td>
				<?php
					for($i = 1; $i < 7; $i++){
						if(!array_key_exists($i, $time_value)){
							echo "<td></td>";
						}else{
				?>
				<td class='info'>
					<center>
						<?php 
							$group_info_num = '';
							foreach ($time_value[$i] as $key_group_num => $value_group_num) {
								foreach ($value_group_num as $key_group => $value_group) {
									echo "<b>".$key_group."</b>";
									echo "<hr style='padding:0; margin:2px 0 2px 0;'>";
									$count = 1;
									foreach ($value_group as $value_student) {
										echo "<p style='text-align:left; margin:0;'>".($count++).") ".$value_student."</p>";
									}
									echo "<br>";
								}
							}
						?>
					</center>
				</td>
				<?php } } ?>
			</tr>
			<?php } ?>
		</table>
		<hr>
		<?php $count++;} ?>
		<?php if($count==0){ echo "<center><h3>Сабақ кестесі жүктелмеген</h3></center>";} ?>
	</div>
</div>