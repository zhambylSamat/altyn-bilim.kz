<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'entrance_examination';
	}
?>
<div class='row entrance_examination' data='entrance_examination'>
	<ul class="nav nav-tabs">
	 	<li role="presentation" class='sub_navigation active' data='student'><a href="#">Оқушылар</a></li>
	 	<li role="presentation" class="sub_navigation" data='test'><a href="#">Тест</a></li>
	</ul>

	<div class='entrance_examination_box test'>
		<!-- entrance_examination_test.php -->
		<?php include_once("entrance_examination_student.php"); ?>
	</div>

	<div class='entrance_examination_box student'>
		<!-- entrance-examination_student.php -->
	</div>

</div>