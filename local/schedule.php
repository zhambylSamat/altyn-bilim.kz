<?php
include_once('../connection.php');
try {
	$student_num = $_GET['data_num'];
	$stmt=$conn->prepare("SELECT gi.start_lesson, 
							    gi.finish_lesson, 
							    sch.week_id, 
							    gi.office_number, 
							    sj.subject_name,
							    gi.group_name,
							    gi.group_info_num
							FROM student s, 
							    group_info gi, 
							    group_student gs, 
							    subject sj, 
							    schedule sch
							WHERE s.student_num = :student_num
								AND s.student_num = gs.student_num
								AND gs.group_info_num = gi.group_info_num
							    AND gi.subject_num = sj.subject_num
							    AND gi.group_info_num = sch.group_info_num
							ORDER BY gi.start_lesson, 
								gi.finish_lesson,
							    sch.week_id,
							    gi.group_info_num,
							    gi.office_number");
	$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$result_arr = '';
	foreach ($result as $key => $value) {
		$tt = substr($value['start_lesson'],0,5)."-".substr($value['finish_lesson'],0,5);
		$result_arr[$tt][$value['week_id']][$value['group_info_num']]['subject_office'] = $value['subject_name']." : ".$value['office_number'];
		$result_arr[$tt][$value['week_id']][$value['group_info_num']]['group'] = $value['group_name'];
	}
} catch (PDOException $e) {
	echo "Error ".$e->getMessage()." !!!";
}
?>
<center>
	<table class='table table-bordered schedule'>
		<tr class='info'>
			<th><center>Сабақ уақыттары</center></th>
			<th><center>Понедельник</center></th>
			<th><center>Вторник</center></th>
			<th><center>Среда</center></th>
			<th><center>Четверг</center></th>
			<th><center>Пятница</center></th>
			<th><center>Суббота</center></th>
		</tr>
		<?php foreach ($result_arr as $time_key => $week_value) { ?>
		<tr>
			<th class='info'><center><?php echo $time_key; ?></center></th>
			<?php 
				for($i = 1; $i<7; $i++){
					if(isset($week_value[$i])){
			?>
			<td class='info'>
				<?php foreach ($week_value[$i] as $group_info_key => $group_info_value) {?>
				<center><?php echo $group_info_value['subject_office']; ?></center>
				<hr style='margin:0px 0 5px 0; border-color:#aaa;'>
				<?php } ?>
			</td>
			<?php }else { echo "<td></td>"; }} ?>
		</tr>
		<?php } ?>
	</table>
</center>