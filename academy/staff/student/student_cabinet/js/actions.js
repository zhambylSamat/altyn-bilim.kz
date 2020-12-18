$(document).on('click', '.cabinet-navigation', function() {
	if (!$(this).hasClass('active')) {
		$element = $('.student-cabinet-content');
		$data_dir = $(this).data('dir');
		$element.load($data_dir+'?student_id='+get_url_params('student_id'));

		$('.cabinet-navigation').each(function() {
			$(this).removeClass('active');
		});

		$(this).addClass('active');
	}
});


function set_student_trial_test_result () {
	$student_id = get_url_params('student_id');
	$ab_root = $('#ab_root').val();
	$.when(get_student_trial_test_result($student_id, $ab_root)).done(function($data) {
		console.log($data);
		$json = $.parseJSON($data);
		$colors = ['#1F77B4', '#FF7F0E', '#2CA02C', '#D62728', '#9467BD', '#8C564B', '#E377C2', '#7F7F7F'];
		if ($json.success) {
			$datasets = [];
			$labels = $json.test_results.submit_dates;
			$color_index = 0;
			$html = "<div class='row'>";
			$.each($json.test_results.result, function($subject_id, $value) {
				$html += "<div class='col-md-3 col-sm-4 col-xs-6'>";
					$html += "<table class='table table-striped table-bordered'>";
					$html += "<tr><th colspan='3'><center>"+$value.subject_title+"</center></th></tr>";
					$count = 0;
					$chart_datas = [];
					$.each($value.stt, function($student_trial_test_id, $test_result) {
						++$count;
						if ($count <= 10) {
							$html += "<tr>";
								$html += "<td><center>"+$test_result.submit_date+"</center></td>";
								$html += "<td><center>"+$test_result.actual_result+'/'+$test_result.total_result+"</center></td>";
								$html += "<td><center><a target='_blank' href='"+$ab_root+"/academy/student/trial_test/components/testing.php?student_trial_test_id="+$student_trial_test_id+"'>Толығырақ</a></center></td>";
							$html += "</tr>";
						}
						$chart_datas.push({'x': $test_result.submit_date,
											'y': $test_result.actual_result});
					});
					$html += "</table>";
				$html += "</div>"; // .col-...

				$datasets.push({label: $value.subject_title,
								// backgroundColor: 'rgba(0,0,0,0)',
								backgroundColor: $colors[$color_index],
								borderColor: $colors[$color_index],
								fill: false,
								data: $chart_datas});
				$color_index++;
			});
				$html += "<div class='col-md-12 col-sm-12 col-xs-12'>";
					$html += "<canvas id='trial-test-chart-container' style='width: 100%; height: 300px;'></canvas>";
				$html += "</div>"; // .col-...
			$html += "</div>"; // .row
			$('.student-trial-test-result-content').html($html);

			var ctx = document.getElementById('trial-test-chart-container').getContext('2d');
			var chart = new Chart(ctx, {
				type: 'line', 
				data: {
					labels: $labels,
				    datasets: $datasets
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
						text: "Пробный тесттің қорытындысы "
					},
					scales: {
						yAxes: [{
							scaleLabel: {
								display: true,
								labelString: 'Балл'
							},
							ticks: {
								min: 0,
								max: 40,
								stepSize: 5
							}
						}],
						xAxes: [{
							scaleLabel: {
								display: true,
								labelString: 'Тестті орындаған уақыт'
							},
							type: 'time',
							time: {
								parser: 'DD.MM.YYYY',
								displayFormats: {
			                        day: 'DD.MM.YY'
			                    },
			                    unit: 'day'
							}
						}]
					}
				}
			});
		}
	});
}

function get_student_trial_test_result ($student_id, $ab_root) {
	return $.ajax({
		type: "GET",
		url: $ab_root+'/academy/student/trial_test/controller.php?get_student_trial_test_result&student_id='+$student_id
	});
}

$(document).on('click', '.btn-add-coins', function() {
	$student_id = $(this).data('id');

	if (confirm('Оқушыға қосымша 50 бонус күндер беруге келісесінба?')) {
		$.ajax({
			type: 'GET',
			url: 'controller.php?add_extra_coins&student_id='+$student_id,
			beforeSend: function() {
				set_load('body');
			},
			success: function ($data) {
				remove_load();
				$json = $.parseJSON($data);

				if ($json.success) {
					$('.extra-coins-added-notification').show();
				}
			}
		});
	}
});