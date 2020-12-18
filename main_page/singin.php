<!DOCTYPE html>
<html>
<head>
	<?php include_once('meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
	<section id='singIn'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
					<br><br><br><br><br><br>
					<form class="form-horizontal" action='controll-user.php' method='post'>
					  	<div class="form-group">
					    	<label for="username" class="col-sm-2 control-label">Username</label>
					    	<div class="col-sm-10">
					      		<input type="text" name='username' class="form-control" id="username" placeholder="Username">
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="password" class="col-sm-2 control-label">Пароль</label>
					    	<div class="col-sm-10">
					      		<input type="password" class="form-control" name='password' id="password" placeholder="Пароль">
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