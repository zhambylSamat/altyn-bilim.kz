<?php include_once('../connection.php'); ?>
<div class='row'>
	<div class='col-md-4 col-sm-4 col-xs-4'>
		<h2 style='font-family: "Times New Roman";'><center>Аралық бақылау</center></h2>
	</div>
	<div class='col-md-8 col-sm-8 col-xs-8'>
		<div style="padding:3%;">
			<span class='pull-right'>
				<?php 
					$max_practice = 'max_practice';
					$max_theory = 'max_theory';
					$last_result_practice = 'last_result_practice';
					$last_result_theory = 'last_result_theory';
					$search_order_type = $max_practice;

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
					<select class='form-control quiz_serach_order' id='quiz_select_search_subject'>
						<option value=''>Барлығы</option>
						<?php
							foreach ($result_subject as $value) {
								$text = $value['subject_name'];
								echo "<option value='".$value['subject_num']."' title='".$text."'>".$text."</option>";
							}
						?>
					</select>
					<select class='form-control quiz_serach_order' id='quiz_select_search_school'>
						<option value=''>Барлығы</option>
						<?php
							foreach ($result_school as $value) {
								$text = $value['school'];
								echo "<option value='".$value['school']."' title='".$text."'>".$text."</option>";
							}
						?>
					</select>
					<select class='form-control quiz_serach_order' id='quiz_search_order_type'>
						<option value='<?php echo $max_practice;?>'>Есеп бойынша ең жоғарғы балл</option>
						<option value='<?php echo $max_theory;?>'>Теория бойынша ең жоғарғы балл</option>
						<option value='<?php echo $last_result_practice;?>'>Есеп бойынша соңғы балл</option>	
						<option value='<?php echo $last_result_theory;?>'>Теория бойынша соңғы балл</option>
					</select>
					<br>
					<div class='form-group pull-right'>
						<label for='quiz_search_archive' title='Архивтегі оқушылармен бірге'>
							A
						</label>
						<input type="checkbox" class='quiz_serach_order' id='quiz_search_archive' value='6' title='Архивтегі оқушылармен бірге'>
					</div>
				</span>
			</span>
			<h4 class='pull-right trial_test_search' style='padding-right: 1%;'>Пән:</h4>
		</div>
	</div>

	<div class='col-md-12 col-sm-12 col-xs-12 progress_result_quiz_container'>
		<?php include_once("progress_result_quiz_list.php"); ?>
	</div>
</div>