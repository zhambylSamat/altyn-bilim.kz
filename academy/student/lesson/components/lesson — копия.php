<?php
	GLOBAL $ab_root;
	if (!isset($_SESSION['materials'])) {
		$content = array();
	} else {
		$content = $_SESSION['materials'];
	}
?>
<div>
	<a style='cursor:pointer;' id='back-to-subtopic-list'><span class='glyphicon glyphicon-chevron-left'></span> Тақырыптар тізіміне оралу</a>
</div>
<?php
	if ($_SESSION['user_id'] == 87) {
		echo "<div id='console'></div>";
	}
?>
<?php 
	if (isset($_SESSION['test_finished']) && $_SESSION['test_finished'] == 'true') {
		$_SESSION['test_finished'] = 'false';
?>
	<div class='container'>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong>Тест аяқталды!</strong> Жауабын төменнен көруге болады
		</div>		
	</div>
<?php } ?>
<div id='lesson-content'>
	<?php
		$html = '';
		foreach ($content as $value) {
			$html .= '<h3>'.$value['subtopic_title'].'</h3>';
			$materials = $value['materials'];
			if (count($materials['tutorial_video']) > 0) {
				$html .= '<div class="show-materials">';
					$html .= '<div class="show-material"><a>Тақырыптың түсіндірілуі</a>';
					$html .= '<div class="material-actions pull-right">';
						$html .= '<span class="open-material glyphicon glyphicon-plus"></span><span class="close-material glyphicon glyphicon-minus"></span>';
					$html .= '</div>';
					$html .= "</div>";
					$html .= "<div class='material-content vimeo-video row'>";
						foreach ($materials['tutorial_video'] as $tv_val) {
							$html .= "<div class='video-datas'>";
								$html .= '<input type="hidden" name="video-link" value="'.$tv_val['link'].'">';
								$html .= '<input type="hidden" name="video-id" value="'.$tv_val['id'].'">';
								$html .= '<input type="hidden" name="action-id" value="'.$tv_val['action_id'].'">';
								$html .= '<input type="hidden" name="obj" value="tutorial_video">';
								$html .= "<input type='hidden' name='title' value='".$tv_val['title']."'>";
								$html .= "<input type='hidden' name='access_before' value='".$tv_val['log']['formatted_access_before']."'>";
								$html .= "<input type='hidden' name='duration' value='".$tv_val['duration']."'>";
							$html .= "</div>";
						}
					$html .= "</div>";
				$html .= "</div>";
			}
			if (count($materials['tutorial_document']) > 0) {
				$html .= '<div class="show-materials">';
					$html .= '<div class="show-material"><a>Есептер мен тапсырмалар</a>';
					$html .= '<div class="material-actions pull-right">';
						$html .= '<span class="open-material glyphicon glyphicon-plus"></span><span class="close-material glyphicon glyphicon-minus"></span>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= "<div class='material-content material-files'>";
						$html .= "<ol>";
						foreach ($materials['tutorial_document'] as $td_val) {
							$html .= "<li><a data-file-id='".$td_val['id']."' data-action-id='".$td_val['action_id']."' href='".$ab_root.'/academy'.$td_val['link']."' target='_blank'>".$td_val['title']."</a></li>";
						}
						$html .= "</ol>";
					$html .= "</div>";
				$html .= '</div>';
			}
			if (count($materials['end_video']) > 0) {
				$html .= '<div class="show-materials">';
					$html .= '<div class="show-material"><a>Есептердің шығарылуы</a>';
					$html .= '<div class="material-actions pull-right">';
						$html .= '<span class="open-material glyphicon glyphicon-plus"></span><span class="close-material glyphicon glyphicon-minus"></span>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= "<div class='material-content vimeo-video row'>";
						foreach ($materials['end_video'] as $ev_val) {
							$html .= "<div class='video-datas'>";
								$html .= '<input type="hidden" name="video-link" value="'.$ev_val['link'].'">';
								$html .= '<input type="hidden" name="video-id" value="'.$ev_val['id'].'">';
								$html .= '<input type="hidden" name="action-id" value="'.$ev_val['action_id'].'">';
								$html .= '<input type="hidden" name="obj" value="end_video">';
								$html .= '<input type="hidden" name="timecode" value="true">';
								$html .= "<input type='hidden' name='title' value='".$ev_val['title']."'>";
								$html .= "<input type='hidden' name='access_before' value='".$ev_val['log']['formatted_access_before']."'>";
								$html .= "<input type='hidden' name='duration' value='".$ev_val['duration']."'>";
							$html .= "</div>";
						}
					$html .= "</div>";
				$html .= '</div>';
			}
			
			if (count($materials['material_test']) > 0) {
				$html .= "<div>";
					$html .= "<b>Тақырыпқа байлансыты тест жұмыстры</b>";
					$html .= "<table class='table table-striped table-bordered'>";
					foreach ($materials['material_test'] as $value) {
						$html .= "<tr>";
							$html .= "<td style='width: 25%;'>".$value['title']."</td>";
							if ($value['result']['actual_result'] == '') {
								$html .= "<td><a class='btn btn-success btn-sm' href='".$ab_root."/academy/student/lesson/testing.php?mt=".$value['mt_id']."&mta=".$value['mta_id']."'>Тестті бастау</a></td>";
							} else {
								$result_percent = 0.0;

								if ($value['result']['actual_result'] > 0) {
									$result_percent = round(($value['result']['actual_result'] / $value['result']['total_result']) * 100, 1);
								}

								$html .= "<td><b>".$value['result']['total_result']."</b> тан сұрақтан <b>".$value['result']['actual_result']."</b> сұрақ дұрыс. <b>".$result_percent."%</b></td>";
							}
						$html .= "</tr>";
					}
					$html .= "</table>";
				$html .= "</div>";
			}
			$html .= '<hr>';
		}
		echo $html;
	?>
</div>