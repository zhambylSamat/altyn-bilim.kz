<?php
	$plans = get_student_plans();
	// echo json_encode($plans, JSON_UNESCAPED_UNICODE);
?>
<div class='col-md-12 col-sm-12 col-xs-12'>
	<hr>
</div>
<div class='col-md-12 col-sm-12 col-xs-12'>
	<h3>Оқу жоспарың:</h3>
</div>

<div class='container-fluid'>
	<div class='row'>
		<?php
			$html = '';
			foreach ($plans as $subject_id => $subject) {
				$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
					$html .= "<h4>".$subject['subject_title']."</h4>";
					foreach ($subject['topics_and_groups'] as $topic) {
						$panel_class = 'panel ';
						$panel_title = '';
						$display_body_style = 'display: none;';
						$title = $topic['group_name'] == '' ? $topic['topic_title'] : $topic['group_name'];
						if ($topic['is_group']) {
							if ($topic['group_status'] == 2) {
								$display_body_style = '';
								$panel_title .= "<div style='width: 80%; display: inline-block;' class='panel-heading'>".$title.'</div>';
								$panel_title .= "<div style='width: 20%; display: inline-block;'>";
									$panel_title .= "<button class='btn btn-xs btn-info pull-right show-subtopics-btn'>";
										$panel_title .= "<span class='glyphicon glyphicon-align-justify'></span>";
									$panel_title .= "</button>";
								$panel_title .= "</div>";
							} else {
								$panel_title .= "<div style='width: 70%; display: inline-block;' class='panel-heading'>".$title."</div>";
								$panel_title .= "<div style='width: 30%; display: inline-block;'>";
									$panel_title .= "<p>Модуль аяқталды ";
										$panel_title .= "<button class='btn btn-xs btn-success pull-right show-subtopics-btn'>";
											$panel_title .= "<span class='glyphicon glyphicon-align-justify'></span>";
										$panel_title .= "</button>";
									$panel_title .= "</p>";
								$panel_title .= "</div>";
							}
							$panel_class .= $topic['group_status'] == 2 ? 'panel-info' : 'panel-success';
						} else {
							$panel_class .= "panel-default";
							if ($topic['start_date'] == '') {
								$panel_title .= "<div style='width: 80%; display: inline-block;' class='panel-heading'>".$title."</div>";
								$panel_title .= "<div style='width: 20%; display: inline-block;'>";
									$panel_title .= "<button style='margin-left: 5px;' class='btn btn-xs btn-default pull-right show-subtopics-btn'>";
										$panel_title .= "<span class='glyphicon glyphicon-align-justify'></span>";
									$panel_title .= "</button>";
									$panel_title .= "<button class='btn btn-xs btn-success pull-right register-btn'>Тіркелу</button>";
								$panel_title .= "</div>";
							} else {
								$panel_title .= "<div style='width: 60%; display: inline-block;' class='panel-heading'>".$title."</div>";
								$panel_title .= "<div style='width: 40%; display: inline-block;'>";
									$panel_title .= "<div style='width: 80%; display: inline-block;'><span>Модульдің басталу күні:</span><center>".$topic['start_date']."</center></div>";
									$panel_title .= "<div style='width: 20%; display: inline-block;'>";
										$panel_title .= "<button style='display: inline-block;' class='btn btn-xs btn-default pull-right show-subtopics-btn'>";
											$panel_title .= "<span class='glyphicon glyphicon-align-justify'></span>";
										$panel_title .= "</button>";
									$panel_title .= "</div>";
								$panel_title .= "</div>";
							}
						}
						$html .= "<div class='".$panel_class."'>";
							$html .= "<div class='panel-heading'>";
								$html .= $panel_title;
							$html .= "</div>";
							$html .= "<div class='panel-body' style='".$display_body_style."'>";
								$html .= "<table class='table table-striped table-bordered'>";
									foreach ($topic['subtopics'] as $subtopic) {
										$style = '';
										$learning_html = "";
										if ($subtopic['type'] == 'lesson_progress') {
											if ($subtopic['is_today'] == 1) {
												$learning_html = "<span class='glyphicon glyphicon-record' style='color:green;'></span>&nbsp;&nbsp;";
											} else {
												$style = 'background-color: #eee; color: #888;';
											}
											$date = $subtopic['learned_date'];
										} else if ($subtopic['type'] == 'subtopic') {
											$date = $subtopic['learn_date'];
										}
										$html .= "<tr style='".$style."'>";
											$html .= "<td>".$learning_html.$subtopic['subtopic_title']."</td>";
											if (isset($subtopic['total_result']) && isset($subtopic['actual_result']) && $subtopic['total_result'] != '' && $subtopic['actual_result'] != '') {
												$actual_result = $subtopic['actual_result'];
												$total_result = $subtopic['total_result'];
												if ($total_result == 0 || $total_result == '') {
													$percent = 0;
												} else {
													$percent = ceil(intval(($actual_result/$total_result)*100));
												}
												$html .= "<td>".$percent."%</td>";
											} else {
												$html .= "<td></td>";
											}
											$html .= $date != '' ? "<td>".$date."</td>" : '';
										$html .= "</tr>";
									}
								$html .= "</table>";
							$html .= "</div>";
						$html .= "</div>";
					}
				$html .= "</div>";
			}
			echo $html;
		?>
	</div>
</div>