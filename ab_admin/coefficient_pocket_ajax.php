<?php
	include_once('../connection.php');

	$eep_id = $_GET['eep_id'];

	try {
		$stmt = $conn->prepare("SELECT ee.id,
									t.name
								FROM entrance_examination ee,
									test t
								WHERE ee.eep_id = :eep_id
									AND t.test_num = ee.test_num
								ORDER BY SUBSTRING_INDEX(t.name, ' ', 1), 
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
										SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 3), ' ', -1)");
		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
		$stmt->execute();
		$test_list = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT ptc.id,
									ptc.is_test,
									ptc.is_percent,
									ptc.ee_id,
									ptc.percent
								FROM pocket_test_coefficient ptc
								WHERE ptc.eep_id = :eep_id");
		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
		$stmt->execute();
		$cf = $stmt->fetch(PDO::FETCH_ASSOC);
		$cf_row_count = $stmt->rowCount();

		$is_percent = false;
		$is_test = true;
		$percent = 0;
		$ee_id = "";

		if ($cf_row_count != 0) {
			$is_percent = $cf['is_percent'];
			$is_test = $cf['is_test'];
			$percent = $cf['percent'];
			$ee_id = $cf['ee_id'];
		}
	} catch (PDOException $e) {
		throw $e;
	}

?>

<div class='row'>
	<form id='set-test-pocket-coefficient'>	
		<div class='col-md-4 col-sm-4 col-xs-12'>
			<div class='pull-right'>
				<div class='radio'>
					<label>
						<input type="radio" name="coefficient_bool" class='test_pocket_coeff_radio' value='percent' <?php echo $is_percent ? "checked" : ""; ?>>
						Процент
					</label>
				</div>
				<div class='radio'>
					<label>
						<input type="radio" name="coefficient_bool" class='test_pocket_coeff_radio' value='topic' <?php echo $is_test ? "checked" : ""; ?>>
						Тақырып
					</label>
				</div>
			</div>
		</div>
		<div class='col-md-8 col-sm-8 col-xs-12'>
			<div class='pull-left'>
				<div id='percent' class='test_pocket_coeff_content' style='<?php echo !$is_percent ? "display:none;" : ""; ?>'>
					<label for='percent-text'>
						Процент:
					</label>
					<input type="number" name="percent" class='form-control' min='0' max='100' step='1' value='<?php echo $is_percent ? $percent : 0; ?>'>
				</div>
				<div id='test' class='test_pocket_coeff_content' style='<?php echo !$is_test ? "display:none;" : ""; ?>'>
					<label for='test-text'>
						Тақырып:
					</label>
					<select name='test' class='form-control'>
						<option value=''>Тақырыпты таңда</option>
						<?php
							foreach ($test_list as $value) {
								$selected = $value['id'] == $ee_id ? 'selected' : '';
								echo "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
							}
						?>
					</select>
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<br>
			<center>
				<input type="hidden" name="eep_id" value='<?php echo $eep_id; ?>'>
				<input type="submit" class='btn btn-xs btn-success' value='Сақтау'>
			</center>
		</div>
	</form>
</div>