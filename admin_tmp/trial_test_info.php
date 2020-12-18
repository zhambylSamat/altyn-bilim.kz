<?php
	include_once('../connection.php');
	$subject_num = '';
	try {
		$stmt = $conn->prepare("SELECT s.student_num,
									s.name,
								    s.surname,
								    ttm.trial_test_mark_num,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%y') AS date_of_test,
								    gi.subject_num,
								    tt.trial_test_num
								FROM group_info gi
									INNER JOIN group_student gs
								    	ON gs.group_info_num = gi.group_info_num
								    		AND gs.start_date <= CURDATE()
									INNER JOIN student s
								    	ON s.student_num = gs.student_num
								    LEFT JOIN trial_test tt
								    	ON tt.subject_num = gi.subject_num
								        	AND tt.student_num = s.student_num
								    LEFT JOIN trial_test_mark ttm
								    	ON ttm.trial_test_num = tt.trial_test_num
								WHERE gi.group_info_num = :group_info_num
								ORDER BY s.surname ASC, ttm.date_of_test DESC");
		$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
		$stmt->execute();
		$trial_test_sql_result = $stmt->fetchAll();
		// print_r($trial_test_sql_result);
		$tt_result = '';
		foreach ($trial_test_sql_result as $key => $value) {
			$subject_num = $value['subject_num'];
			$tt_result[$value['student_num']]['name'] = $value['surname']." ".$value['name'];
			$tt_result[$value['student_num']]['trial_test_num'] = $value['trial_test_num'];
			$tt_result[$value['student_num']]['trial_test'][$value['trial_test_mark_num']]['mark'] = $value['mark'];
			$tt_result[$value['student_num']]['trial_test'][$value['trial_test_mark_num']]['date'] = $value['date_of_test'];
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<table class='table table-bordered table-striped'>
	<tr>
		<th colspan="2"><center>Пробный тест</center></th>
	</tr>
	<?php foreach ($tt_result as $key => $value) { ?>
	<tr>
		<td class='std-name'><center><?php echo $value['name']; ?></center></td>
		<td>
			<?php 
				$count = 0;
				$local_count = 0;
				// echo isset($value['trial_test'][0]);
				foreach($value['trial_test'] as $value_key => $value_mark) {
					if($value_key!=''){
						$count++;
						if($count<4){
							$local_count++;
							echo "<center><span></span><span><b>Дата: </b>".$value_mark['date']."&nbsp;&nbsp;<b>Балл: </b>".$value_mark['mark']."</span></center>";
						}
					}
				}
				if($count>3){
					echo "<center><a class='show-modal-trial-test-mark' data-num='".$value['trial_test_num']."' data-toggle='modal' data-target='.box-student-trial-test-mark' style='cursor:pointer;'>+ еще ".($count-$local_count)."</a></center>";
				}
				// else if($count!=0 && $count<=3){
				// 	echo "<center><a class='show-modal-trial-test-mark' data-num='".$value['trial_test_num']."' data-toggle='modal' data-target='.box-student-trial-test-mark' style='cursor:pointer;'>Посмотреть подробнее</a></center>";
				// }
			?>
		</td>
	</tr>
	<?php } ?>
</table>
<p id='testing'></p>