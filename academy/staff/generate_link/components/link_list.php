<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/generate_link/views.php');

	$all_materials_link = get_all_material_active_links();
?>

<table class='table table-bordered' style='margin-top: 2%;'>
	<?php
		$html = "";
		foreach ($all_materials_link as $material) {
			$html .= "<tr>";
				$html .= "<td>".$material['comment']."</td>";
				$html .= "<td>";
					foreach ($material['subjects'] as $subject_id => $subject) {
						$html .= "<table class='table table-bordered'>";
							$html .= "<tr><td colspan='2' class='info'><center><b>".$subject['title']."</b></center></td></tr>";
							foreach ($subject['topics'] as $topic_id => $topic) {
								$html .= "<tr><td rowspan='".count($topic['subtopics'])."'>".$topic['title']."</td>";
								$count = 0;
								foreach ($topic['subtopics'] as $subtopic_id => $subtopic) {
									if ($count > 0) {
										$html .= "<tr>";
									}
									$html .= "<td>";
										$html .= "<span>".$subtopic['title']."</span>";
										if ($subtopic['tv']) {
											$html .= "<br><b>Тақырыптық видео</b>";
										}
										if ($subtopic['td']) {
											$html .= "<br><b>Тапсырмалар</b>";
										}
										if ($subtopic['ev']) {
											$html .= "<br><b>Шығару жолы</b>";
										}
										if ($subtopic['mt']) {
											$html .= "<br><b>Тест</b>";
										}
									$html .= "</td>";
									$html .= "</tr>";
									$count++;

								}
							}
						$html .= "</table>";
					}
				$html .= "</td>";
				$html .= "<td>".$material['access_time']."</td>";
				$html .= "<td><p class='material-link'>https://online.altyn-bilim.kz/academy/lesson/?q=".$material['code']."</p></td>";
				$html .= "<td><button class='btn btn-lg btn-info btn-block copy-material-link-btn'>Скопировать ссылку</button></td>";
			$html .= "</tr>";
		}
		echo $html;
	?>
</table>