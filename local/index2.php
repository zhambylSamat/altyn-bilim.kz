<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<script type="text/javascript">
function fnUnloadHandler() {
  xmlhttp=null; 
  if (window.XMLHttpRequest) 
     {// code for Firefox, Opera, IE7, etc. 
        xmlhttp=new XMLHttpRequest(); 
     } 
  else if (window.ActiveXObject) 
     {// code for IE6, IE5 
        xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
     } 

  if (xmlhttp!=null) 
     {  
        xmlhttp.open("GET","del_action.php",true); 
        xmlhttp.send(null); 
     } 
     else 
     { 
        alert("Your browser does not support XMLHTTP."); 
     } 

}
</script>
<body onbeforeunload="fnUnloadHandler()">
	<section id='singIn'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3'>
					<br><br><br><br><br><br>
					<form class="form-horizontal" action='controll.php' method='post'>
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