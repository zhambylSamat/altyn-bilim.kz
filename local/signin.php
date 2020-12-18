<?php
	include_once('../connection.php');
	if(isset($_SESSION['student_num']) && isset($_SESSION['access']) && $_SESSION['access']==md5('true')){
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
					<center><h3 style="font-family: 'Times New Roman'; font-weight: 900; color:#555;">Оқушы</h3></center>
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

	<div class="modal fade box-alert" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	    	<div class="modal-header">
	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
	    		<br>
	    		<center><h3 class="modal-title"></h3></center>
	    	</div>
	    	<div class="modal-body">
	    		<center>
	    			<h4 class='text-danger'></h4>
	    			<h3 class='text-warning'></h3>
	    		</center>
	    	</div> 
	    </div>
	  </div>
	</div>

	<?php include_once('js.php');?>
	<?php 
		if(isset($_GET['noPayment'])){
			echo 	'<script type="text/javascript">
						$(document).ready(function(){
							$(".box-alert").modal("show");
							$(".box-alert .modal-title").text("Оқудың төлемі төленбеген");
							$(".box-alert .modal-body").html("<center><h4 class=\\"text-danger\\">Порталға кіру мүмкін емес.</h4><h3 class=\\"text-warning\\">Менеджерге жолығыңыз!</h3></center>");
						});
					</script>';
		}
		if(isset($_GET['noContract'])){
			echo 	'<script type="text/javascript">
						$(document).ready(function(){
							$(".box-alert").modal("show");
							$(".box-alert .modal-title").text("Договор өткізілмеген");
							$(".box-alert .modal-body").html("<center><h4 class=\\"text-danger\\">Порталға кіру мүмкін емес.</h4><h3 class=\\"text-warning\\">Менеджерге жолығыңыз!</h3></center>");
						});
					</script>';
		}
	?>
</body>
</html>