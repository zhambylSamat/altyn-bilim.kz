<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'schedule';
	}
	try {
		$stmt = $conn->prepare("SELECT gi.office_number,
									gi.start_lesson,
									gi.finish_lesson,
									sch.week_id,
									gi.group_info_num,
									gi.group_name,
									s.student_num,
									s.name,
									s.surname,
									(SELECT r1.status 
									FROM review r1 
									WHERE r1.group_student_num = gs.group_student_num 
										AND r1.review_info_num = 1) as review_status
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
									AND s.block != 6
									AND gs.block != 6
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
			$tt = substr($value['start_lesson'],0,5)." - ".substr($value['finish_lesson'],0,5);
			$result[$value['office_number']][$tt][$value['week_id']][$value['group_info_num']][$value['group_name']][$value['student_num']]['name'] = $value['surname']." ".mb_substr($value['name'], 0, 1).".";
			$result[$value['office_number']][$tt][$value['week_id']][$value['group_info_num']][$value['group_name']][$value['student_num']]['title'] = $value['surname']." ".$value['name'];
			$result[$value['office_number']][$tt][$value['week_id']][$value['group_info_num']][$value['group_name']][$value['student_num']]['review'] = $value['review_status'];
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
			<tr style='width: 100%;'>
				<th style='width: 10%;'><center>Сабақ уақыттары</center></th>
				<th style='width: 15%;'><center>Понедельник</center></th>
				<th style='width: 15%;'><center>Вторник</center></th>
				<th style='width: 15%;'><center>Среда</center></th>
				<th style='width: 15%;'><center>Четверг</center></th>
				<th style='width: 15%;'><center>Пятница</center></th>
				<th style='width: 15%;'><center>Суббота</center></th>
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
									echo "<b>".$key_group."</b> (".count($value_group).")";
									echo "<hr style='padding:0; margin:2px 0 2px 0;'>";
									$count = 1;
									foreach ($value_group as $value_student) {
										$review = isset($value_student['review']) ? "<span class='pull-right' style='color:blue; font-size:14px;'><b>K:".$value_student['review']."</b></span>" : "<span class='pull-right' style='color:red; font-size:14px;'>N/A</span>";
										echo "<p style='text-align:left; margin:0;' class='schedule-student' data-toggle='tooltip' data-placement='left' title='".$value_student['title']."'>".$value_student['name']." ".$review."</p>";
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