<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/check_authentication.php');
	check_admin_access();

	if (!isset($_GET['student_id']) || !isset($student_id)) {
    	header($ab_root.'/academy');
    }
    if (isset($_GET['student_id'])) {
    	$student_id = $_GET['student_id'];
    }

    include_once($root.'/staff/student/student_cabinet/view.php');

    // $student_trial_test_results = get_student_trial_teset_results($student_id);

    // echo json_encode($student_trial_test_results, JSON_UNESCAPED_UNICODE);
?>

<input type="hidden" id='ab_root' value='<?php echo $ab_root; ?>'>
<br>
<div class='row'>
    <div class='col-md-12 col-md-12 col-sm-12'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='student-trial-test-result-content'>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    set_student_trial_test_result();
</script>