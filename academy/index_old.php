<!DOCTYPE html>
<html>
<head>
	<?php include_once('common/assets/meta.php');?>
	<title>Кіру - Онлайн Академия. Altyn Bilim</title>
	<?php include_once('common/assets/style.php');?>
	<?php include_once('common/connection.php'); ?>
	<link rel="stylesheet" type="text/less" href="common/assets/style/lp_style.less">
</head>
<body>
	<?php
		if (isset($_SESSION['password_reset'])) {
			if ($_SESSION['password_reset'] == 1) {
				header("Location:reset-password.php");
			} else {// if ($_SESSION['password_reset'] == -1) {
				unset($_SESSION);
			}
		}
	?>
	<?php include_once('common/assets/js.php'); ?>

	<div class='container-fluid'>
		<div class='row' style='display: <?php echo isset($_GET['r_done']) ? 'block' : 'none';?>'>
			<br>
			<div class="alert alert-success alert-dismissible" style='position: absolute; width: 100%; z-index: 100;' role="alert">
			  <center>
			  	<strong>Құттықтаймыз! Тіркелу сәтті аяқталды. Оқу төлемін жасап чекті менеджерге жіберіңіз.</strong>
			  </center>
			</div>
		</div>
	</div>
	<?php
		if (isset($_SESSION['user'])) {
			if ($_SESSION['user'] == $ADMIN) {
				header('Location:staff');
			} else if ($_SESSION['user'] == $STUDENT && $_SESSION['password_reset'] == 0) {
				header('Location:student');
			}
		} else {
			$get_sign_in = true;
			include_once('sign_in.php');
		}
	?>
	<?php
		include_once('subjects.php');
		include_once('game_card.php');
		include_once('groups.php');
	?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-4 col-sm-4 col-xs-5' style='margin-top: 1%;'>
				<img src="common/assets/img/kaspi_logo.png" class='img-responsive pull-right' style='height: 116px; width: auto;'>
			</div>
			<div class='col-md-6 col-sm-6 col-xs-7'>
				<h4 style='color: #666;'>Төлемі Kaspi Bank картасы арқылы жүзеге асырылады</h4>
				<h2><b>5169 4931 2313 8979</b></h2>
				<i style='font-size: 16px;'>(Алмат Серікұлы М.)</i>
				<p style='font-size: 18px;'>Төлем жасалған соң чекті менеджерге жіберіңіз: +7 777 389 0099</p>
				<center>
					<a class='btn btn-sm btn-success' href="https://wa.me/77773890099?text=Altyn%20Bilim%20онлайн%20академиясының%20төлемін%20төледім.%20Аты-жөнім:%20%20.%20Қатысатын%20пән(дер)ім:%20" target="_blank">WHATSAPP номер</a>
				</center>
			</div>
		</div>
	</div>
	<div style='margin-bottom: 10%;'></div>
	<?php
		include_once('footer.html');
	?>
	<script type="text/javascript" src='script.js?v=1.0.9'></script>
</body>
</html>