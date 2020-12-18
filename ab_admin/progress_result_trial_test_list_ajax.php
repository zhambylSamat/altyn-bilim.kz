<?php
	include_once('../connection.php');
	$data = $_GET['data'];
	$data_name = $_GET['data_name'];
	$s_val = json_decode($data, true);
?>
<table class='table table-bordered' style="margin:0;">
	<tr style='width: 100%;'>
		<th style='width: 60%;'><center>График</center></th>
		<th style='width: 1%'><center>#</center></th>
		<th style='width: 14%; text-align: right;'>Бағасы</th>
		<th style='width: 25%; text-align: left;'>Күні</th>
	</tr>
<?php
	$mark_count = 0;
	$year = 0;
	$month = 0;
	$day = 0;
	$chart_labels = array();
	$chart_datas = array();
	$chart_label = "Сынақ тест: ".$data_name;
	foreach ($s_val['ttm'] as $ttm_key => $ttm_val) { 
		$year = substr($ttm_val['date_of_test'],6,4);
		$day = substr($ttm_val['date_of_test'],0,2);
		$month = substr($ttm_val['date_of_test'],3,2);
		if ($ttm_val['mark'] >= 0) {
			array_push($chart_labels, $day.".".$month.".".$year);
			array_push($chart_datas, $ttm_val['mark']);
		}
?>
	<tr style="width: 100%;">
		<?php if($mark_count==0) {?>
		<td rowspan="<?php echo count($s_val['ttm']);?>" style="width: 60%;">
			<div id="chart" style="height: 370px; width: 100%;">
				<canvas id="chartContainer<?php echo $s_val['chart_count']; ?>" width="400" height="200"></canvas>
			</div>
		</td>
		<?php } ?>
		<td style="width: 1%;"><?php echo ++$mark_count; ?></td>
		<td style="width: 25%; text-align: right;"><?php echo $ttm_val['mark']; ?></td>
		<td style="width: 15%; text-align: left;"><?php echo $ttm_val['date_of_test']; ?></td>
	</tr>
<?php 
	}
?>	
</table>
<script type="text/javascript">
var ctx = document.getElementById('chartContainer<?php echo $s_val['chart_count']; ?>').getContext('2d');
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