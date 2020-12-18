<?php include_once('../connection.php');?>
<?php
	if(!isset($_SESSION['default_teacher_num'])){
		header('location:signin.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim - Сброс пароля</title>
	<?php include_once('style.php');?>
</head>
<body>
<div class='container'>
	<div class='row'>
		<div class='col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4'>
			<div style='margin-top:40%;'>
				<h5><b>Здравствуйте, <?php echo $_SESSION['teacher_name'].' '.$_SESSION['teacher_surname'].'.';?></b></h5>
				<h5>Вам следует поменять пароль с "123456" на любой другой, чтобы войти в систему.</h5>
				<p>*Важно: Ваш пароль должен содержать не менее 7 символов!</p>
				<center>
					<form method='post' action='teacher_controller.php'>
						<div class='form-group'>
							<label for='new_password'>Новый парлоль</label>
							<input type="password" class='form-control' name="new-password" id='new_password'>
						</div>
						<div class='form-group'>
							<label for='confirm_password'>Повторите пароль</label>
							<input type="password" class='form-control' name="confirm-password" id='confirm_password'>
						</div>
						<input type="submit" class='btn btn-success btn-sm' name="reset-password">
					</form>
				</center>
			</div>
		</div>
	</div>
</div>

<?php include_once('js.php');?>
<script type="text/javascript">
	$(document).on('submit','form',function(e){
		thisParent = $(this);
		e.preventDefault();
		$.ajax({
        	url: "teacher_controller.php?<?php echo md5('resetPassword')?>",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(dataS){	
		    	console.log(dataS);
		    	data = $.parseJSON(dataS);
		    	console.log(data);
		    	if(data.success){
		    		window.location.href = "index.php";
		    	}
		    	else{
		    		alert("Устраните ошибки ниже: \n"+data.error);
		    	}
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	        
	   	});
	});
</script>
</body>
</html>