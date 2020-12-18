<?php
	include_once('../connection.php');
	if(isset($_SESSION['teacher_num'])){
		header('location:index.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn-bilim.kz</title>
	<?php include_once('style.php');?>
</head>
<body>
	<section id='singIn'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
					<br><br><br><br><br><br>
					<form class="form-horizontal" action='parent_controller.php' method='post'>
					  	<div class="form-group">
					    	<label for="phone" class="col-sm-2 control-label">Телефон</label>
					    	<div class="col-sm-10">
					    		<div class='input-group'>
									<div class='input-group-addon'>+7</div>
					      			<input type="number" max='7999999999' min='7000000000' step='1' name='phone' class="form-control" id="phone" placeholder="Введите номер телефона" value='7*********'>
					      		</div>
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