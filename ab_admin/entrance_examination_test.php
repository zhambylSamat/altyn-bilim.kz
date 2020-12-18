<?php
	include_once("../connection.php");

	$result_test = array();
	$result_test_pocket = array();
	try {
		$stmt = $conn->prepare("SELECT test_num, 
									name
								FROM test 
								WHERE type = 'entrance_examination_test' 
								ORDER BY SUBSTRING_INDEX(name, ' ', 1), 
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
										CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
										SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 3), ' ', -1)");
		$stmt->execute();
		$result_test = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT id, name FROM entrance_examination_pocket ORDER BY name");
		$stmt->execute();
		$result_test_pocket = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT ptc.is_test, 
									ptc.is_percent,
									ptc.eep_id, 
									ptc.percent,
									(SELECT t1.name 
									FROM test t1, 
										entrance_examination ee1 
									WHERE ee1.id = ee_id 
										AND t1.test_num = ee1.test_num) as name
								FROM pocket_test_coefficient ptc");
		$stmt->execute();
		$test_pocket_coeff_res = $stmt->fetchAll();
		$test_pocket_coeff = array();
		foreach ($test_pocket_coeff_res as $value) {
			$test_pocket_coeff[$value['eep_id']]['is_test'] = $value['is_test'] == 1 ? true : false;
			$test_pocket_coeff[$value['eep_id']]['is_percent'] = $value['is_percent'] == 1 ? true : false;
			$test_pocket_coeff[$value['eep_id']]['test_name'] = $value['name'];
			$test_pocket_coeff[$value['eep_id']]['percent'] = $value['percent'];
		}



	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<button class='btn btn-info btn-xs entrance-examination-test-refresh'>Refresh</button>
<div class='row'>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<center><h3>Тест</h3></center>
		<hr>
		<div>
			<?php
				$count = 0;
				foreach ($result_test as $value) {
					$count++;
					$name = ($value['name']=="") ? "<b><i>UNNAMED</i></b>" : $value['name'];
					echo "<div class='test-list' style='width:100%; margin:5px; border-bottom:1px solid lightgray;'><b>-</b> <a href='../create_test/index.php?data_num=".$value['test_num']."' target='_blank'>".$name."</a>&nbsp;&nbsp;&nbsp;";
					if($_SESSION['role']==md5('admin')) {
						echo "<a class='pull-right btn btn-xs btn-danger delete-test' data-num='".$value['test_num']."' >Delete</a>";
					}
					echo "</div>";
				}
				if($count==0){
					echo "N/A";
				}
			?>
		</div>
		<br>
		<div>
			<?php
				if($_SESSION['role']==md5('admin')) {
					echo "<a class='btn btn-xs btn-success' href='../create_test/index.php?data_num=new' target='_blank'>Тест құрастыру +</a>";
				}
			?>
		</div>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<center><h3>Тесттер жиынтығы</h3></center>
		<hr>
		<div>
			<?php
				$count = 0;
				echo "<table class='table'>";
				$border_top = "border-top:1px solid gray;";
				$border_bottom = "border-bottom:1px solid gray;";
				$border_right = "border-right:1px solid gray;";
				$border_left = "border-left:1px solid gray;";
				foreach ($result_test_pocket as $value) {
					$count++;
					$name = $value['name'];
					echo "<tr class='test-pocket-list' style='width:100%; margin:5px; border-bottom:1px solid lightgray;'><td style='$border_top $border_left'><a class='collect-test' data-toggle='modal' data-target='.box-collect-data' data-num='".$value['id']."'>".$name."</a></td>";
					if($_SESSION['role']==md5('admin')) {
						echo "<td style='$border_top $border_right'><a class='pull-right btn btn-xs btn-danger delete-pocket' data-num='".$value['id']."' >Delete</a>";
						echo "<a class='pull-right btn btn-xs btn-info set-coefficient' data-num='".$value['id']."' data-toggle='modal' data-target='.box-collect-data' style='margin-right: 10px;'>Коэффициент</a></td>";
					}
					echo "</tr>";

					echo "<tr><td style='$border_bottom $border_left'></td><td style='$border_bottom $border_right'>";
					$coeff = $test_pocket_coeff[$value['id']]; 
					if (isset($coeff)) {
						if ($coeff['is_test'] && $coeff['test_name']!="") {
							echo "<b>".$test_pocket_coeff[$value['id']]['test_name']."</b> - осы тақырыпты дурыс орындаса: K=7, қате орындаса н/се орындай алмаса: K=6";
						} else if ($coeff['is_percent'] && $coeff['percent']!=0){
							echo "Тесттен <b>".$test_pocket_coeff[$value['id']]['percent']."%</b> дан жоғары н/се тең жинаса: K=7, төмен жинаса: K=6";
						} else {
							echo "Енгізілмеген";	
						}
					} else {
						echo "Енгізілмеген";
					}
					echo "</td></tr>";
				}
				echo "</table>";
				if($count==0){
					echo "N/A";
				}
			?>
		</div>
		<br>
		<div>
			<?php
				if($_SESSION['role']==md5('admin')) {
					echo "<a class='btn btn-xs btn-success collect-test' data-toggle='modal' data-target='.box-collect-data' data-num='new'>Тесттер жиынтығын құрастыру +</a>";
				}
			?>
		</div>
	</div>
</div>