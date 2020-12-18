<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	$group_student_no_home_work_notification = get_group_student_no_home_notification();
	$html = "";
	foreach ($group_student_no_home_work_notification as $gsnhwn_id => $value) {
		$html .= "<div class='s-notification'>";
			$html .= "<div class='sn-mark-error'>";
				$html .= "<div class='sn-notification-subtitle sn-test-subtitle sn-subtitle-error row'>";
					$html .= "<span class='text'>".$value['text']."</span>";
					$html .= "<input type='hidden' class='group-student-no-home-work-notification-pop-up' value='".$value['is_notified']."'>";
				$html .= "</div>";
			$html .= "</div>"; //.sn-mark-error
		$html .= "</div>"; // .s-notification
	}
	echo $html;
?>