<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/lesson/views.php');

	if (!isset($subtopic_id) || !isset($code)) {
		echo 'ERROR';
	} else {
		$materials = get_materials_by_subtopic($code, $subtopic_id);
		// echo json_encode($materials, JSON_UNESCAPED_UNICODE);
	}
?>

<?php
	$html = '';
	if (count($materials) > 0) {
		$class = 'material-body';
		if (!isset($is_many) || $is_many) {
			$class = 'unbox-material-body';
		}
		$html .= "<div class='".$class."'>";
		$html .= "<div class='row'><div class='col-md-12 col-sm-12 col-xs-12'><h3 class='topic-title'>".$materials['title']."</h3></div></div>";
		
		if (count($materials['tutorial_video']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыптық видео:</h4></div>";
			foreach ($materials['tutorial_video'] as $tutorial_video_id => $tutorial_video) {
				$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
					$html .= "<center><i>Видеоның ұзақтығы: ".$tutorial_video['duration']."</i></center>";
					$html .= "<div class='tutorial-video-tmp'>";
						$html .= "<input type='hidden' name='video_id' value='".$tutorial_video_id."'>";
						$html .= "<input type='hidden' name='link' value='".$tutorial_video['link']."'>";
					$html .= "</div>";
				$html .= "</div>";
			}
			$html .= "</div><hr></div>";
		}

		if (count($materials['tutorial_document']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыпқа байланысты есептер жане жауаптары:</h4></div>";
			$html .= "<div class='col-md-10 col-sm-10 col-xs-12 parent-document'>";
			$html .= "<input type='hidden' name='document-question-count' value='".(count($materials['tutorial_document'])-1)."'>";
			$count = 0;
			$question_carousel_html = "<div class='carousel' style='display:none;'>";
			foreach ($materials['tutorial_document'] as $tutorial_document_id => $tutorial_document) {
				$count++;
				if ($count != count($materials['tutorial_document'])) {
					$class = 'question-img questions-img ';
					if ($count > 1) {
						$class .= 'question-secondary ';
					} else {
						$class .= "question-primary ";
					}
					$link = $ab_root.'/academy'.$tutorial_document['link'];
					$html .= "<div style='display:inline;'><img src='".$link."' class='".$class."' data-target='#question-answers' data-index='".($count-1)."' data-link='".$link."'/></div>";

					$question_carousel_html .= "<div class='carousel-box'><div style='text-align: center; width: 100vw; height: 100vh;'><img src='".$link."' class='carousel-img'/></div></div>";
				} else {
					$html .= "<div id='document-".$tutorial_document_id."'>";
						$html .= "<br><br><button class='btn btn-info btn-lg question-answers-btn' data-link='".$ab_root.'/academy'.$tutorial_document['link']."' data-toggle='modal' data-target='#question-answers'>Есептердің жауаптары</button>";
					$html .= "</div>";
				}
			}
			$question_carousel_html .= "</div>";
			$html .= $question_carousel_html;
			$html .= "</div></div><hr></div>";
		}

		if (count($materials['end_video']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыпқа байланысты есептердің шығару жолы:</h4></div>";
			foreach ($materials['end_video'] as $end_video_id => $end_video) {
				$html .= "<div class='end_video_content col-md-6 col-sm-6 col-xs-12'>";
					$html .= "<div class='end_video_tmp'>";
						$html .= "<input type='hidden' name='video_id' value='".$end_video_id."'>";
						$html .= "<input type='hidden' name='link' value='".$end_video['link']."'>";
						$html .= "<input type='hidden' name='video_duration' value='".$end_video['duration']."'>";
						$html .= "<input type='hidden' name='video_second_duration' value='".$end_video['second_duration']."'>";
					$html .= "</div>";
					$html .= "<div class='timecode'></div>";
				$html .= "</div>";
			}
			$html .= "</div></div>";
		}
		$html .= "</div>";
	}
	echo $html;
?>