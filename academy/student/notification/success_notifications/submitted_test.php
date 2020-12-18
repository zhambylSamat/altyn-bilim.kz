<?php
	include_once ($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	$submitted_test = get_submitted_tests();

	$html = "";

	foreach ($submitted_test as $material_test_result_id => $value) {
		$html .= "<div class='s-notification'>";
			$html .= "<div class='sn-mark-success sn-test-result-content sn-notification-content'>";
				$html .= "<div class='sn-notification-subtitle sn-subtitle-success row'>";
					$html .= "<span>Тест аяқталды. Қатемен жұмысты істеуды ұмытпа!</span>";
				$html .= "</div>"; // .sn-subtitle-success .row
				$html .= "<div class='sn-test-result-title row'>";
					$html .= "<div class='col-md-10 col-sm-10 col-xs-12'>";
						$html .= "<span>";
							$percent = ceil(intval(($value['actual_result']/$value['total_result'])*100));
							$html .= $value['subject_title'].' - '.$value['topic_title'].' - '.$value['subtopic_title'];
							$html .= "<br><i>Тест қорытындысы:</i> <b style='font-size: 17px;'>".$percent."% (".$value['actual_result']."/".$value['total_result'].")</b>";
						$html .= "</span>";
					$html .= "</div>"; // .col-...
					$html .= "<div class='col-md-2 col-sm-2 col-xs-12'>";
						$html .= "<a href='lesson/testing.php?subtopic_id=".$value['subtopic_id']."&mta=".$value['material_test_action_id']."' target='_blank' class='btn btn-sm btn-info pull-right btn-block'>Қатемен жұмыс</a>";
					$html .= "</div>"; // .col-...
				$html .= "</div>"; // .sn-test-result-title .row
			$html .= "</div>"; // .sn-mark-success .sn-test-result-content
		$html .= "</div>"; // .s-notification
	}

	echo $html;
?>