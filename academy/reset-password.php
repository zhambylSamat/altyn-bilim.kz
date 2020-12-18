<!DOCTYPE html>
<html>
<head>
	<?php include_once('common/assets/meta.php');?>
	<title>Құпия сөзді өзгерту - Онлайн Академия. Алтын Білім</title>
	<?php include_once('common/assets/style.php');?>
	<?php include_once('common/connection.php'); ?>
	<style type="text/css">
		.password-info {
			font-size: 15px;
			padding-top: 1%;
			padding-bottom: 1%;
		}
		@media (max-width: 421px) {
			.password-info {
				font-size: 15px;
				padding-top: 3%;
				padding-bottom: 3%;
			}
		}

	</style>
</head>
<body>
	<?php
		include_once('common/connection.php');
		$_SESSION['password_reset'] = -1;
		$first_register = false;
		if (isset($_SESSION['first_register'])) {
			$first_register = $_SESSION['first_register'];	
		}
		if (isset($_SESSION['reset_password_by_sms'])) {
			$_SESSION['first_register'] = 0;
		}
		$last_password_display = $first_register == 1 ? 'display: none;' : '';
		$last_password = $first_register == 1 ? '12345' : '';
	?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-12'>
				<div style='margin-top: 20%;'>
					<center>
						<?php
							if ($first_register != 1) { ?>
							<h4><b>Құпия сөзді ауыстыру</b></h4>
						<?php } else { ?>
							<h3>Курсқа тіркелгеніңе рақмет</h3>
						<?php } ?>
						<h3 style='color: #1E60AE;'><?php echo $_SESSION['last_name'].' '.$_SESSION['first_name']; ?></h3>
						<p>
							<?php
								if ($first_register == 1) {
									echo "<p class='password-info'>Өзіңнің жаңа құпия сөзіңді құрастыр. Ұзындығы 7 символдан кем болмауы керек!</p>";
								} else {
									echo "<p class='password-info'>Жаңа құпия сөздің ұзындығы 7 символдан кем болмауы керек!</p>";
								}
							?>
						</p>
					</center>
					<form class="form-horizontal" method='post' action='controller.php'>
						<div class="form-group" style='<?php echo $last_password_display; ?>'>
							<label for="old-password" class="col-sm-3 control-label">Соңғы құпия сөз</label>
						    <div class="col-sm-9">
						    	<input type="password" class="form-control" name='old-password' id="old-password" value='<?php echo $last_password; ?>' required placeholder="Құпия сөз">
						    	<?php if (isset($_GET['pwd'])) { ?>
						    		<p class='text-danger'>Құпия сөз қате</p>
						  		<?php } ?>
						    </div>
						</div>
						<div class="form-group">
							<label for="new-password" class="col-sm-3 control-label">Жаңа құпия сөз:</label>
						    <div class="col-sm-9">
						    	<input type="password" class="form-control" name='new-password' id="new-password" required placeholder="Жаңа құпия сөз">
						    	<?php if (isset($_GET['lngth'])) { ?>
						    		<p class='text-danger'>Жаңа құпия сөздің ұзындығы 7 символдан кем болмауы керек!</p>
						    	<?php } ?>
						    </div>
						</div>
						<div class="form-group">
							<label for="confirm-new-password" class="col-sm-3 control-label">Жаңа құпия сөзіңді қайта енгіз:</label>
						    <div class="col-sm-9">
						    	<input type="password" class="form-control" name='confirm-new-password' id="confirm-new-password" required placeholder="Жаңа құпия сөз">
						    	<?php if (isset($_GET['cnfrm'])) { ?>
						    		<p class='text-danger'>Құпия сөздер сәйкес келмейді!</p>
						    	<?php } ?>
						    </div>
						</div>
						<?php
							if ($first_register) {
								echo "<input type='hidden' name='first_register' value='true'>";
							}
						?>
						<center><input type="submit" class='btn btn-sm btn-success' name="reset-password" value='Сақтау'></center>
					</form>
				</div>
			</div>
		</div>
	</div>

</body>
</html>