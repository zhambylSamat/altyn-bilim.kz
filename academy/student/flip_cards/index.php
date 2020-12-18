<?php
	$LEVEL = 1;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');

    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);
?>

<div class='row game-box' style='margin-left: 1%;'>
	<div class='col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12'>
		<center>
			<p class='game-text'>Алгебра, Геометрия және Физика пәндерінен теориялық сұрақтарға ФЛИП-КАРТОЧКАЛАР</p>
			<button class='beautiful-btn btn-play btn-sep icon-cart' onclick="window.open('../game_card', '_blank')">Ойынды бастау</button>
		</center>
	</div>
</div>
