<?php
	include_once('../connection.php');
	$student_num = $_GET['data_num'];
	$subject_num = $_GET['sjn'];
	$subject_name = $_GET['sn'];
	try {
		$stmt = $conn->prepare("SELECT ttm.trial_test_mark_num,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%y') AS date_of_test
								FROM trial_test tt, 
									trial_test_mark ttm
								WHERE tt.subject_num = :subject_num
									AND tt.student_num = :student_num
									AND ttm.trial_test_num = tt.trial_test_num
								ORDER BY ttm.date_of_test DESC");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$trial_test_sql_result = $stmt->fetchAll();
		$tt_result = '';
		foreach ($trial_test_sql_result as $key => $value) {
			$tt_result[$value['trial_test_mark_num']]['mark'] = $value['mark'];
			$tt_result[$value['trial_test_mark_num']]['date'] = $value['date_of_test'];
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<center><p style='cursor: pointer;'><u><a class='add-trial-test-mark'>Баға енгізу</a></u></p></center>
<form method='post' id='trial_test_form' style='display: none;'>
	<div class='form-group'>
		<label for='trial-date' style='display:inline-block;'>Дата:&nbsp;</label>
		<input type="text" class='form-control datePicker' placeholder="dd.mm.yyyy" id='trial-date' name="trial_date" required="" style='display:inline-block; width: 50%;'>
	</div>
	<div class='form-group'>
		<label for='trial-mark' style='display: inline-block;'>Балл:&nbsp;</label>
		<input type="number" class='form-control' min='0' max='40' step='1' name="trial_mark" id='trial-mark' value='0' required="" style='display: inline-block; width: 50%;'>
	</div>
	<input type="hidden" name="stdn" value="<?php echo $student_num; ?>">
	<input type="hidden" name="sjn" value='<?php echo $subject_num;?>'>
	<input type="submit" class='btn btn-xs btn-success' name="submit-trial-test" value='Сақтау'>
	<input type="reset" class='btn btn-xs btn-warning reset-trial-test' value='Отмена'>
	<hr>
</form>
<?php 
	if($tt_result!=''){
		$count = 0;
		$local_count = 0;
		foreach($tt_result as $value_key => $value_mark) {
			if($value_key!=''){
				$count++;
				if($count<4){
					$local_count++;
					echo "<center><span><b>Балл: </b>".$value_mark['mark']."</span>&nbsp;&nbsp;<span><b>Дата: </b>".$value_mark['date']."</span></center>";
				}
			}
		}
		if($count>3){
			echo "<center><a href='student_trial_test_info.php?data_num=".$student_num."&sjn=".$subject_num."&sn=".$subject_name."' target='_blank' style='cursor:pointer;'>+ еще ".($count-$local_count)."</a></center>";
		}
		else if($count!=0 && $count<=3){
			echo "<center><a href='student_trial_test_info.php?data_num=".$student_num."&sjn=".$subject_num."&sn=".$subject_name."' target='_blank' style='cursor:pointer;'>Посмотреть подробнее</a></center>";
		}
	}
?>