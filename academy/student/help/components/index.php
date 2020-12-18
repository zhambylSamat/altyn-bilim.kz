<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/lesson/view.php');
?>

<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<p id='title'>Инструкция</p>
		</div>
	</div>
	<hr style='margin-top: 1%;'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php include_once($root.'/student/help/components/video_w_timecode.php'); ?>
		</div>
	</div>
</div>