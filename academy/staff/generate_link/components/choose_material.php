<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/generate_link/views.php');

	$all_materials = get_all_materials();

	$html = '';

	foreach ($all_materials as $subject_id => $subject) {
		$html .= "<table class='table table-bordered materials-table'>";
			$html .= "<tr class='info'><td class='subject-title' colspan='6'>";
			$html .= "<center><b><a>".$subject['title']."</a></b></center>";
			$html .= "</td></tr>";
			foreach ($subject['topics'] as $topic_id => $topic) {
				$html .= "<tr class='topics'><td class='topic-title' data-id='".$topic_id."' colspan='6'>";
				$html .= "<a>".$topic['title']."</a>";
				$html .= "</td></tr>";
				$html .= "<tr class='subtopics subtopic-".$topic_id."'><td class='subtopic-title' data-id='".$topic_id."' rowspan='".count($topic['subtopics'])."'><a>".$topic['title']."</a></td>";
				$count = 0;
				foreach ($topic['subtopics'] as $subtopic_id => $subtopic) {
					if ($count > 0) {
						$html .= "<tr class='subtopics subtopic-".$topic_id."'>";	
					}
						$html .= "<td class='material-info'>";
							$html .= $subtopic['title'];
							$html .= "<input type='hidden' name='subject-id' value='".$subject_id."'>";
							$html .= "<input type='hidden' name='subject-title' value='".$subject['title']."'>";
							$html .= "<input type='hidden' name='topic-id' value='".$topic_id."'>";
							$html .= "<input type='hidden' name='topic-title' value='".$topic['title']."'>";
							$html .= "<input type='hidden' name='subtopic-id' value='".$subtopic_id."'>";
							$html .= "<input type='hidden' name='subtopic-title' value='".$subtopic['title']."'>";
						$html .= "</td>";
						$html .= "<td style='position:relative;'>";
							$html .= "<center>";
							if ($subtopic['tv_count'] != 0) {
								$html .= "<input type='checkbox' class='material-elem' value='tutorial_video' style='width:20px; height:20px;'>";
							} else {
								$html .= "-";
							}
							$html .= "</center>";
						$html .= "</td>";
						$html .= "<td style='position:relative;'>";
							$html .= "<center>";
							if ($subtopic['td_count'] != 0) {
								$html .= "<input type='checkbox' class='material-elem' value='tutorial_document' style='width:20px; height: 20px;'>";
							} else {
								$html .= "-";
							}
							$html .= "</center>";
						$html .= "</td>";
						$html .= "<td style='position:relative;'>";
							$html .= "<center>";
							if ($subtopic['ev_count'] != 0) {
								$html .= "<input type='checkbox' class='material-elem' value='end_video' style='width:20px; height:20px;'>";
							} else {
								$html .= "-";
							}
							$html .= "</center>";
						$html .= "</td>";
						$html .= "<td><center>";
							if ($subtopic['mt_count'] != 0) {
								$html .= "<input type='checkbox' class='material-elem' value='material_test' style='width: 20px; height: 20px;'>";
							} else {
								$html .= "-";
							}
						$html .= "</center></td>";
					$html .= "</tr>";
					$count++;
				}
			}
		$html .= "</table>";
	}
	echo $html;
?>