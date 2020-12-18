<?php
include('../connection.php');
if($teacher_num==''){
	throw new Exception("ERROR...");
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
								WHERE gi.teacher_num = :teacher_num
									AND gs.group_info_num = gi.group_info_num
									AND sch.group_info_num = gi.group_info_num
									AND s.student_num = gs.student_num
									AND gi.start_lesson NOT IN ('00:00:00:')
									AND gi.finish_lesson NOT IN ('00:00:00')
									AND s.block != 1
									AND s.block != 6
								ORDER BY gi.office_number,
									gi.start_lesson,
									gi.finish_lesson,
									sch.week_id,
									gi.group_name,
									s.name ASC");
	$stmt->bindParam(':teacher_num', $teacher_num, PDO::PARAM_STR);
	$stmt->execute();
	$result_from_query = $stmt->fetchAll();
	$result = array();
	foreach ($result_from_query as $value) {
		$tt = substr($value['start_lesson'],0,5)."-".substr($value['finish_lesson'],0,5);
		$result[$tt][$value['week_id']][$value['group_info_num']][$value['group_name']]['students'][$value['student_num']] = $value['surname']." ".$value['name'];
		$result[$tt][$value['week_id']][$value['group_info_num']][$value['group_name']]['office_number'] = $value['office_number'];
	}
} catch (PDOException $e) {
	echo "Error ".$e->getMessage()." !!!";
}
?>
<style type="text/css">
	td, th{
		border:1px solid black !important;
	}
</style>
<div class='row'>
	<div class='col-md-12 col-sm-12'>
		<center><h3><b>Сабақ кестесі</b></h3></center>
		<?php $count = 0;?>
		
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

			<?php foreach ($result as $time_key => $time_value) { ?>
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
									echo "<b>".$key_group."</b><br><b>Кабинет: ".$value_group['office_number']."</b><br>";
									echo "<hr style='padding:0; margin:2px 0 2px 0;'>";
									$count = 1;
									foreach ($value_group['students'] as $value_student) {
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
			<?php $count++;} ?>
		</table>
		<hr>
		
		<?php if($count==0){ echo "<center><h3>Сабақ кестесі жүктелмеген</h3></center>";} ?>
	</div>
</div>