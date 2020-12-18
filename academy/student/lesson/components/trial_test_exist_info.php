<div>
	<a style='cursor:pointer;' id='back-to-subtopic-list'><span class='glyphicon glyphicon-chevron-left'></span> Тақырыптар тізіміне оралу</a>
</div>

<?php
	$student_trial_test_ids = $_SESSION['student_trial_test_info'];
	$subject_titles = array();

	foreach ($student_trial_test_ids as $subject_id => $value) {
		array_push($subject_titles, $value['subject_title']);
	}
?>

<div id='mandatory_trial_test_content'>
	<center>
		<p>Саған, <b><?php echo implode(',', $subject_titles); ?></b> пәнінен пробный тест берілген. Осы тестті аяқтап болған соң сабақты өте аласың!</p>
		<button class='btn btn-info btn-md open-trial-test-nav'>Пробный тестке өту</button>
	</center>
</div>