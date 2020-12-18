<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/intensive/view.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<!-- <button class='btn btn-sm btn-default pull-right go-to-instruction-page'>Инструкция</button> -->
			<p id='title'>Интенсивті курс</p>
			<div class='row'>
				<div class='col-md-6 col-sm-8 col-xs-12'>
					<p id='subtitle'>&nbsp;&nbsp;Оқуыңды <mark>2 есе</mark> жылдамдатқың келсе, интенсивті курсқа ауыссаң болады. Осы арқылы сен сабақты аптасына 3 реттің орнына 6 рет оқисың. Ол үшін төмендегі "Интенсивке ауысу" батырмасын бас</p>
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div id='intensive-group-list'>
				<?php include_once($root.'/student/intensive/components/student_group_list.php'); ?>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div id='intensive-course-instruction-video'>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	render_intensive_course_video_instruction();
</script>