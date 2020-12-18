<?php
	include_once('../connection.php');

	$stmt = $conn->prepare("SELECT
FROM trial_test tt,
	trial_test_mark ttm,
	student s
WHERE s.block != 6 
	AND tt.student_num = s.student_num
	AND ttm.trial_test_num = t.trial_test_num
GROUP BY  ttm.trial_test_num ");
?>