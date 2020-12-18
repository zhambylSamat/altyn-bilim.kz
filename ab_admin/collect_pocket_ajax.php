<?php
	include_once("../connection.php");

	$pocket_name = '';
	$pocket_num = $_GET['data_num'];

	$unselected_tests = array();
	$selected_tests = array();

	try {

		$stmt = $conn->prepare("SELECT eep.name
								FROM entrance_examination_pocket eep
								WHERE eep.id = :eep_id");
		$stmt->bindParam(':eep_id', $pocket_num, PDO::PARAM_STR);
		$stmt->execute();
		$pocket_name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

		$stmt = $conn->prepare("SELECT ee.eep_id as eep_id, 
									ee.id,
									ee.test_order,
									t.test_num, 
									t.name
								FROM test t,
									entrance_examination ee
								WHERE ee.eep_id = :eep_id
									AND ee.test_num = t.test_num
								ORDER BY SUBSTRING_INDEX(t.name, ' ', 1), 
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
										SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 3), ' ', -1)");
		$stmt->bindParam(':eep_id', $pocket_num, PDO::PARAM_STR);
		$stmt->execute();
		$selected_tests = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT t.test_num, 
									t.name
								FROM test t
								WHERE t.type = 'entrance_examination_test' 
									AND t.test_num NOT IN (SELECT ee.test_num 
															FROM entrance_examination ee 
															WHERE ee.eep_id = :eep_id)
								ORDER BY SUBSTRING_INDEX(t.name, ' ', 1), 
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
										SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 3), ' ', -1)");
		$stmt->bindParam(':eep_id', $pocket_num, PDO::PARAM_STR);
		$stmt->execute();
		$unselected_tests = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>

<form id='test-list'>
	<div class='form-group'>
		<label for='pocket-name-id'>Пакеттің аты</label>
		<input type="text" name="pocket_name" required="" class='form-control' id='pocket-name-id' value="<?php echo $pocket_name!='' ? $pocket_name : ''; ?>">
	</div>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php  if (count($unselected_tests) > 0) { ?>
			<select id='test_list' style='height: 300px;' class='form-control' name='test_num_list[]' multiple="" required="">
			<?php foreach ($unselected_tests as $key => $value) { ?>
				<option title='<?php echo $value['name']; ?>' value="<?php echo $value['test_num']; ?>"><?php echo (++$key).") ".$value['name']; ?></option>
			<?php } ?>
			</select>
			<?php } ?>
			<input type="hidden" name="pocket_num" value='<?php echo $pocket_num; ?>'>
			<br>
			<center>
				<input type="submit" class='btn btn-sm btn-success' value='Сақтау'>
			</center>
			<hr>
		</div>
	</div>
</form>
<form id='test-config'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php
				if(empty($selected_tests)){
					echo "N/A";
				}
				else {
					echo "<table class='table'>";
					$start_order = 0;
					foreach ($selected_tests as $key => $value) {
			?>
					<tr class='test-pocket-test'>
						<td><span><?php echo ++$key.")"; ?></span></td>
						<?php 
							if(isset($_SESSION['role']) && $_SESSION['role']==md5('admin')){ 
								$start = false;
								$end = false;
								if ($value['test_order']!=0) {
									if ($value['test_order']%2!=0 && $start_order != $value['test_order']) {
										$start = true;
									} else if ($value['test_order']%2==0 && $start_order != $value['test_order']) {
										$end = true;
									}
									$start_order = $value['test_order'];
								}
						?>
						<td>
							<input type="checkbox" name="config[]" value='<?php echo $value['test_num']; ?>' <?php echo $start || $end ? "checked" : ""; ?>>
							<?php
								echo $start ? "&nbsp;&nbsp;<b class='text-success'>Start</b>" : "";	
								echo $end ? "&nbsp;&nbsp;<b class='text-primary'>End</b>" : "";
							?>
						</td>
						<?php } ?>
						<td><span><?php echo $value['name'];?></span></td>
						<td><a class='pull-right btn btn-xs btn-danger delete-test-from-pocket' data-num='<?php echo $value['id']; ?>' data-pocket-num="<?php echo $pocket_num; ?>">Тізімнен өшіру</a></td>
					</tr>
			<?php 
					}
					echo "</table>"; 
				} 
			?>
		</div>
	</div>
	<?php if(!empty($selected_tests)){ ?>
	<center>
		<input type="submit" class='btn btn-xs btn-info' value='Сақтау'>
		<input type="hidden" name="eep_id" value='<?php echo $value['eep_id']; ?>'>
	</center>
	<?php } ?>
</form>