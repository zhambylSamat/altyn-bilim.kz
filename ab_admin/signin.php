<?php
	include_once('../connection.php');
	if(isset($_SESSION['adminNum'])){
		header('location:index.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
	<section id='singIn'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
					<br><br><br><br><br><br>
					<center><h3 style="font-family: 'Times New Roman'; font-weight: 900; color:#555;">Администратор</h3></center>
					<form class="form-horizontal" action='admin_controller.php' method='post' autocomplete='off'>
					  	<div class="form-group">
					    	<label for="username" class="col-sm-2 control-label">Username</label>
					    	<div class="col-sm-10">
					      		<input type="text" name='username' class="form-control" id="username" placeholder="Username" autocomplete='off' value='username'>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="password" class="col-sm-2 control-label">Пароль</label>
					    	<div class="col-sm-10">
					      		<input type="password" class="form-control" name='password' id="password" placeholder="Пароль" autocomplete='off' value='password'>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<div class="col-sm-offset-2 col-sm-10">
					      		<button type="submit" name='signIn' class="btn btn-default">Войти</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</section>

	<?php include_once('js.php');?>
</body>
</html>