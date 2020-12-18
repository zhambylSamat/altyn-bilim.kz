<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Animation | Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
<center>
	<div style='width: 90%; height: 10%; border:1px solid black; margin-top:10%; padding:1%;'>
		<button class='btn btn-lg'>click</button>
	</div>
</center>
<?php include_once('js.php');?>
<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<script type="text/javascript">
	$(document).on('click','.btn',function(){
		$(this).parent().css({'background-color':"#5CB85C"}).animate({backgroundColor: '#fff'},1500);
		// $(this).parent();
	});
</script>
</body>
</html>