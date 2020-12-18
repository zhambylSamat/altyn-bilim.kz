<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	$group_student_discount_notification = get_group_student_discount_notification();
	$html = "";
	if (count($group_student_discount_notification) > 0) {
		$html .= "<div class='s-notification'>";
			$html .= "<div class='sn-mark-success'>";
				$html .= "<div class='sn-notification-subtitle sn-test-subtitle sn-subtitle-success row'>";
					$html .= "<span class='text'>".$group_student_discount_notification['text']."</span>";
					$html .= "<input type='hidden' class='group-student-discount-notification-pop-up' value='".$group_student_discount_notification['is_notified']."'>";
					$html .= "<input type='hidden' class='group-student-discount-notification-text' value='".$group_student_discount_notification['is_notified']."'>";
				$html .= "</div>";
			$html .= "</div>"; //.sn-mark-success
		$html .= "</div>"; // .s-notification
	}
	echo $html;
?>