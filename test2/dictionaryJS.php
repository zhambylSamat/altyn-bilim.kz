<!DOCTYPE html>
<html>
<head>
	<title>Dictionary jQuery</title>
	<?php include_once('style.php');?>
</head>
<body>
	<?php include_once('js.php');?>
	<script type="text/javascript">
		$dic = {};
		$dic['abc'] = 'asdf';
		$dic['ddd'] = "asdfasdf";
		$dic['abc.asdf'] = '123';
		$dic['arr'] = ['1',2,'a'];
		$dic['aa'] = {'bb':'asdf', 'dd' : 'aasdf'};
		// delete $dic['aa'];
		console.log($dic);
		console.log($dic['abcd']==undefined)
	</script>
</body>
</html>