<?php include_once('../connection.php'); ?>
<div class='row'>
	<div class='col-md-4 col-sm-4 col-xs-4'>
		<h2 style='font-family: "Times New Roman";'><center>Пробный тест</center></h2>
	</div>
	<div class='col-md-8 col-sm-8 col-xs-8'>
		<div style="padding:3%;">
			<span class='pull-right'>
				<?php 
					$max = 'max';
					$last_result = 'last_result';
					$search_order_type = $max;

					$stmt = $conn->prepare("SELECT sj.subject_num, sj.subject_name
											FROM subject sj
											ORDER BY sj.subject_name ASC");
					$stmt->execute();
					$result_subject = $stmt->fetchAll();

					$stmt = $conn->prepare("SELECT DISTINCT s.school 
											FROM student s
											WHERE s.block != 6
												AND s.school != ''
											ORDER BY s.school ASC");
					$stmt->execute();
					$result_school = $stmt->fetchAll();
				?>
				<span class='pull-right' style='width: 100%;'>
					<select class='form-control trial_test_serach_order' id='trial_test_select_search_subject'>
						<option value=''>Барлығы</option>
						<?php
							foreach ($result_subject as $value) {
								$text = $value['subject_name'];
								echo "<option value='".$value['subject_num']."' title='".$text."'>".$text."</option>";
							}
						?>
					</select>
					<select class='form-control trial_test_serach_order' id='trial_test_select_search_school'>
						<option value=''>Барлығы</option>
						<?php
							foreach ($result_school as $value) {
								$text = $value['school'];
								echo "<option value='".$value['school']."' title='".$text."'>".$text."</option>";
							}
						?>
					</select>
					<select class='form-control trial_test_serach_order' id='trial_test_search_order_type'>
						<option value='<?php echo $max;?>'>Оқушының ең жоғарғы баллы бойынша</option>
						<option value='<?php echo $last_result;?>'>Оқушының соңғы алған баллы бойынша</option>
					</select>
					<br>
					<div class='form-group pull-right'>
						<label for='trial_test_search_archive' title='Архивтегі оқушылармен бірге'>
							A
						</label>
						<input type="checkbox" class=' trial_test_serach_order' id='trial_test_search_archive' value='6' title='Архивтегі оқушылармен бірге'>
					</div>
				</span>
			</span>
			<h4 class='pull-right trial_test_search' style='padding-right: 1%;'>Пән:</h4>
		</div>
	</div>

	<div class='col-md-12 col-sm-12 col-xs-12 progress_result_trial_test_container'>
		<?php include_once("progress_result_trial_test_list.php"); ?>
	</div>
</div>