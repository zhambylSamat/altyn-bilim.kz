<?php
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT ees.id, 
									ees.student_name, 
									ees.student_surname, 
									ees.entrance_code, 
									eep.name,
									DATE_FORMAT(ees.submit_date, '%d.%m.%Y') AS submit_date,
									ees.finish,
									ees.result_json,
									ees.coeff_config,
									ees.coeff
								FROM entrance_examination_student ees,
									entrance_examination_pocket eep 
								WHERE ees.eep_id = eep.id
								ORDER BY create_date DESC");
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<table class='table tabler-striped table-bordered'>
	<tr>
		<th>#</th>
		<th>Аты-жөні</th>
		<th>Код<br><i>altyn-bilim.kz/test</i></a></th>
		<th>Тесттер жинағы</th>
		<th>Коэффицент</th>
		<th>Қателескен тақырыптары</th>
		<th>Дата</th>
	</tr>
	<?php
		$count = 0; 
		foreach ($result as $value) {
			$false_tests = array();
			$finish = $value['finish']==1 ? true : false;
			if ($value['result_json'] != "" && $finish) {
				foreach (json_decode($value['result_json'], true)['content'] as $tValue) {
					$torf = false;
					foreach ($tValue['content'] as $qValue) {
						switch ($qValue['result']) {
							case '0':
								$torf = false;
								break;
							case '1':
								$torf = true;
								break;
							case '-1':
								$torf = false;
								break;
						}
					}
					if (!$torf) {
						array_push($false_tests, $tValue['test_name']);
					}
				}
			}
	?>
	<tr>
		<td><?php echo ++$count; ?></td>
		<td><?php echo $value['student_surname']." ".$value['student_name']; ?></td>
		<td><?php echo $value['entrance_code'].$value['id']; ?></td>
		<td><?php echo $value['name']; ?></td>
		<td>
			<?php 
				$html = "";
				if (!$finish) {
					$html .= "<a class='btn btn-xs btn-danger' id='delete-ees' data-num='".$value['id']."'>Удалить</a>" ;
				} else {
					if ($value['coeff_config']!="" && $value['coeff']!=0) {
						$coeff_config = json_decode($value['coeff_config'], true);
						if (isset($coeff_config['is_test'])){
							$test_num = $coeff_config['test_id'];
							$stmt = $conn->prepare("SELECT name FROM test WHERE test_num = :test_num");
							$stmt->bindParam(":test_num", $test_num, PDO::PARAM_STR);
							$stmt->execute();
							$test_name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

							if ($value['coeff']==6) {
								$html .= "<p>Оқушы берілген тақырыпқа дейын (<b><i>".$test_name."</i></b>) жете алмады, сондықтан <b><br>Коэффицент=".$value['coeff']."</b></p>";
							} else if ($value['coeff']==7) {
								$html .= "<p>Оқушы берілген тақырыпқа дейын (<b><i>".$test_name."</i></b>) жетті, сондықтан <b><br>Коэффицент=".$value['coeff']."</b></p>";
							}
 						} else if (isset($coeff_config['is_percent'])) {
							$percent = $coeff_config['percent'];
							if ($value['coeff']==6) {
								$html .= "<p>Оқушы берілген тестті (<b><i>".$percent."</i></b>)%-дан асыра алмады, сондықтан <b><br>Коэффицент=".$value['coeff']."</b></p>";
							} else if ($value['coeff']==7) {
								$html .= "<p>Оқушы берілген тестті (<b><i>".$percent."</i></b>)%-дан асырды, сондықтан <b><br>Коэффицент=".$value['coeff']."</b></p>";
							}
						}

					} else {
						$html .= "<p>N/A</p>";
					}
					$html .= "<a target='_blank' href='../test/index.php?ees_id=".$value['id']."'>Толығырақ</a>"; 
				}
				echo $html;
			?>
		</td>
		<td>
			<?php if (count($false_tests) != 0) { ?>
				<ol>
				<?php
					foreach ($false_tests as $fValue) {
						echo "<li><span style='color:red;'>".$fValue."</span></li>";		
					}
				?>
				</ol>
			<?php 
				} else if ($finish) { 
					echo "<b class='text-success'>Қателеспеді</b>"; 
				} else {
					echo "<span>N/A</span>"; 
				}	 
			?>
		</td>
		<td><?php echo $finish ? $value['submit_date'] : "N/A"; ?></td>
	</tr>
	<?php } ?>
</table>