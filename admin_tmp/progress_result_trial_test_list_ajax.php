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
	$chart_script = '<script>$(document).ready(function(){var chart = new CanvasJS.Chart("chartContainer'.$s_val['chart_count'].'", {animationEnabled: true, title:{
					text: "Пробный тест: '.$data_name.'"}, axisY :{includeZero: false, prefix: ""}, toolTip: { shared: true}, legend: {fontSize: 13}, data: [{},{type: "spline", showInLegend: true, name: "Пробный тест", yValueFormatString: "#", dataPoints: [';
	$year = 0;
	$month = 0;
	$day = 0;
	foreach ($s_val['ttm'] as $ttm_key => $ttm_val) { 
		$chart_script .= '{ x: new Date('.intval(substr($ttm_val['date_of_test'],6,4)).', '.(intval(substr($ttm_val['date_of_test'],3,2))-1).','.intval(substr($ttm_val['date_of_test'],0,2)).'), y: '.intval($ttm_val['mark']).' },';
		$year = intval(substr($ttm_val['date_of_test'],6,4));
		$month = (intval(substr($ttm_val['date_of_test'],3,2))-1);
		$day = intval(substr($ttm_val['date_of_test'],0,2));
?>
	<tr style="width: 100%;">
		<?php if($mark_count==0) {?>
		<td rowspan="<?php echo count($s_val['ttm']);?>" style="width: 60%;">
			<div id="chartContainer<?php echo $s_val['chart_count']; ?>" style="height: 370px; width: 100%;"></div>
		</td>
		<?php } ?>
		<td style="width: 1%;"><?php echo ++$mark_count; ?></td>
		<td style="width: 25%; text-align: right;"><?php echo $ttm_val['mark']; ?></td>
		<td style="width: 15%; text-align: left;"><?php echo $ttm_val['date_of_test']; ?></td>
	</tr>
<?php 
	}
	$chart_script .= '{ x: new Date('.$year.', '.$month.', '.$day.'), y: '.intval('0').' },';
	$chart_script = rtrim($chart_script, ',');
	$chart_script .= ']}]});chart.render(); });</script>';
	echo $chart_script; 
?>	
</table>