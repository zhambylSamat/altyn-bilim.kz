<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'progress_result';
	}
?>
<div class='row progress_result'>
	<ul class="nav nav-tabs">
	 	<li role="presentation" class="sub_navigation active" data='quiz'><a href="#">Аралық бақылау</a></li>
	 	<li role="presentation" class='sub_navigation' data='trial_test'><a href="#">Пробный тест</a></li>
	</ul>

	<div class='progress_result_box quiz'>
		<?php include_once("progress_result_quiz.php"); ?>
	</div>

	<div class='progress_result_box trial_test'></div>

</div>