<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/trial_test/view.php');

	$html = "<div class='row'>";
		$html .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$html .= "<div class='student-trial-test-result-content'>";
			$html .= "</div>";
		$html .= "</div>"; // .row
	$html .= "</div>"; // .row
	echo $html;
?>
<script type="text/javascript">
	$(document).ready(function() {
		set_student_trial_test_result();
	});
</script>