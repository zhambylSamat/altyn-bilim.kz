<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Charts.js</title>
	<style type="text/css">
		.chart {
  border: 3px solid royalblue;
}
	</style>
</head>
<body>
	<div class="chart" style='width: 50%; height: 50%;'>
		<canvas id="myChart" width="400" height="200"></canvas>
	</div>

	<script src="../js/chart.js"></script>
	<script type="text/javascript">
var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
	type: 'line', 
	data: {
		labels: ["May 2017", "May 2017"],
	    datasets: [{
				label: "Rainfall",
				backgroundColor: 'lightblue',
				borderColor: 'royalblue',
				data: [15, 30],
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
			text: 'Precipitation in Toronto'
		},
		scales: {
			yAxes: [{
				scaleLabel: {
					display: true,
					labelString: 'Precipitation in mm'
				}
			}],
			xAxes: [{
				scaleLabel: {
					display: true,
					labelString: 'Month of the Year'
				}
			}]
		}
	}
});

	</script>
</body>
</html>