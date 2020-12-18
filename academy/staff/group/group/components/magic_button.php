<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
?>

<style type="text/css">
	#magic-btn-box {
		margin: 0 0 2% 0;
	}

	#magic-btn-box a {
		margin-left: 1%;
	}

	#magic-btn-info {
		color: gray;
	}
</style>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='magic-btn-box'>
			<span id='magic-btn-info'>Егер программа автоматты түрде оқушыға доступ бермеген жағдайда ғана басқан дұрыс</span>
			<a href='<?php echo $ab_root.'/academy/cron/set_lesson_access.php'; ?>' target='_blank' class='btn btn-xs btn-default'>№1 Бүгінгі сабаққа оқушыларға доступ беру</a>
			<a href="<?php echo $ab_root.'/academy/cron/enable_previews_lesson_after_cron.php'; ?>" target='_blank' class='btn btn-xs btn-default'>№2 Өткен ж/е көрмеген соңғы 2 сабаққа доступ беру </a>
		</div>
	</div>
</div>