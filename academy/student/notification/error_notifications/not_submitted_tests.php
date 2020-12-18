<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	$not_submitted_tests = get_not_submitted_tests();
	// echo json_encode($not_submitted_tests, JSON_UNESCAPED_UNICODE);


	$html = "";
	foreach ($not_submitted_tests as $subject_id => $subject) {
		foreach ($subject['topic'] as $topic_id => $topic) {
			foreach ($topic['subtopic'] as $subtopic_id => $subtopic) {
				$html .= "<div class='s-notification'>";
					$html .= "<div class='sn-mark-error sn-test-content'>";
						$html .= "<div class='sn-notification-subtitle sn-test-subtitle sn-subtitle-error row'>";
							$html .= "<span>Берілген тақырыпқа арналған тест жұмысы орындалмаған!</span>";
						$html .= "</div>";
						$html .= "<div class='sn-test-title row'>";
							$html .= "<div class='col-md-10 col-sm-10 col-xs-12'>";
								$html .= "<span>";
									$html .= $subject['subject_title'].' - '.$topic['topic_title'].' - '.$subtopic['subtopic_title'];
								$html .= "</span>";
							$html .= "</div>";
							$html .= "<div class='col-md-2 col-sm-2 col-xs-12'>";
								$html .= "<a href='".$ab_root."/academy/student/lesson/testing.php?subtopic_id=".$subtopic_id."&mta=".$subtopic['material_test_action_id']."' class='btn btn-sm btn-info pull-right btn-block'>Тестті бастау</a>";
							$html .= "</div>";
						$html .= "</div>";
					$html .= "</div>";
				$html .= "</div>";
			}
		}
	}
	echo $html;
?>