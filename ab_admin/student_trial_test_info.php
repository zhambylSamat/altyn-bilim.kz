<?php
	include_once('../connection.php');
	// $student_num = $_GET['data_num'];
	// $subject_num = $_GET['sjn'];
	$trial_test_num = $_GET['data_num'];
	$month_txt = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
	try {
		$stmt = $conn->prepare("SELECT name, surname FROM student WHERE student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$student_name = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT ttm.trial_test_mark_num,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%m%d%Y') AS date_of_test
								FROM trial_test tt, 
									trial_test_mark ttm
								WHERE tt.trial_test_num = :trial_test_num
									AND ttm.trial_test_num = tt.trial_test_num
								ORDER BY ttm.date_of_test ASC");
		$stmt->bindParam(':trial_test_num', $trial_test_num, PDO::PARAM_STR);
		$stmt->execute();
		$trial_test_sql_result = $stmt->fetchAll();
		$tt_result = array();
		foreach ($trial_test_sql_result as $key => $value) {
			$tt_result[$value['trial_test_mark_num']]['mark'] = $value['mark'];
			$tt_result[$value['trial_test_mark_num']]['date'] = $value['date_of_test'];
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<center>	
	<table class='table table-bordered table-striped'>
			<tr>
				<th><center>#</center></th>
				<th><center>Балл</center></th>
				<th><center>Дата</center></th>
			</tr>
			<?php 
				$count=0; 
				$chart_script = '';
				if($tt_result!=''){
					$year = '';
					$month = '';
					$day = '';
					// $chart_script = '<script>$(document).ready(function(){var chart = new CanvasJS.Chart("chartContainer", {animationEnabled: true, title:{
					// 				text: "Пробный тест: '.$group_info['subject_name'].'"}, axisY :{includeZero: false, prefix: ""}, toolTip: { shared: true}, legend: {fontSize: 13}, data: [{},{type: "spline", showInLegend: true, name: "Пробный тест", yValueFormatString: "#", dataPoints: [';


					$chart_labels = array();
					$chart_datas = array();
					$chart_label = "Сынақ тест";
					foreach($tt_result as $key => $value){
						// $chart_script .= '{ x: new Date('.intval(substr($value['date'],4,4)).', '.(intval(substr($value['date'],0,2))-1).','.intval(substr($value['date'],2,2)).'), y: '.intval($value['mark']).' },';
						$year = substr($value['date'],4,4);
						$month = substr($value['date'],0,2);
						$day = substr($value['date'],2,2);
						if ($value['mark']) {
							array_push($chart_labels, $day.".".$month.".".$year);
							array_push($chart_datas, $value['mark']);
						}
						// echo intval(substr($value['date'],4,4)).', '.intval(substr($value['date'],0,2)).','.intval(substr($value['date'],2,2))."<br>";
			?>
			<tr>
				<th><center><?php echo ++$count; ?></center></th>
				<th>
					<center><?php echo $value['mark'];?></center>
				</th>
				<th>
					<center><?php echo $month_txt[intval(substr($value['date'],0,2))]." ".substr($value['date'],2,2)." ".substr($value['date'],4,4);?></center>
				</th>
			</tr>
			<?php 
					} 
					// echo $year." ".$month." ".$day;
					// $chart_script .= '{ x: new Date('.$year.', '.$month.', '.$day.'), y: '.intval('0').' },';
					// $chart_script = rtrim($chart_script, ',');
					// $chart_script .= ']}]});chart.render();});</script>';
				}

			?>
		</table>
	<!-- <?php echo $chart_script; ?> -->
	<div id="chart" style="height: 370px; width: 100%;">
			<canvas id="chartContainer" width="400" height="200"></canvas>
		</div>
</center>
<script type="text/javascript">
var ctx = document.getElementById('chartContainer').getContext('2d');
var chart = new Chart(ctx, {
	type: 'line', 
	data: {
		labels: <?php echo json_encode($chart_labels); ?>,
	    datasets: [{
				label: "Оқушының тест қорытындылары",
				backgroundColor: 'rgba(0,0,0,0)',
				borderColor: 'royalblue',
				data: <?php echo json_encode($chart_datas); ?>,
			},{
				label: "",
				backgroundColor: 'rgba(0,0,0,0)',
				borderColor: 'rgba(0,0,0,0)',
				data: [0, 40],
			}]
		},
	options: {
	    layout: {
	      padding: 10,
	    },
		legend: {
			position: 'bottom',
		},
		title: {
			display: true,
			text: <?php echo json_encode($chart_label); ?>
		},
		scales: {
			yAxes: [{
				scaleLabel: {
					display: true,
					labelString: 'Балл'
				}
			}],
			xAxes: [{
				scaleLabel: {
					display: true,
					labelString: ''
				}
			}]
		}
	}
});

	</script>
<!-- <?php echo $chart_script; ?> -->
<script type="text/javascript">
	$(document).ready(function(){
		console.log('start');
		// $('canvas').prototype._updateSize;
		// console.log(xxx);
		console.log('end');
	});
</script>