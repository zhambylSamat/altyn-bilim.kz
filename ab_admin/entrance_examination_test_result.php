<?php
	include_once("../connection.php");
	if(!isset($_SESSION['adminNum'])){
		header('location:signin.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Тест | Altyn-bilim</title>
	<?php include_once("style.php"); ?>
</head>
<body>
	<?php include_once('nav.php');?>
	<?php include_once("js.php"); ?>

	<section style='height: 15px;'></section>
	<section id='main'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12'>
					
				</div>
			</div>
		</div>
	</section>

	<section id='test-content'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12' id='main-content'>
					<?php
						$ees_id = $_GET['ees_id'];
						include_once('../test/test_result.php');
					?>		
				</div>
			</div>
		</div>
	</section>

	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>
</body>
</html>