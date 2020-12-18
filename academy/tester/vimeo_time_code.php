<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div id='a'>
		<!-- <iframe src="https://player.vimeo.com/video/358305100" width="560" height="315" frameborder="0" allowfullscreen=""></iframe> -->
	</div>
	<a id='b'>20s</a>
	<p id='c'>ccccccccccccccccc</p>

<?php
	include_once('../common/assets/js.php');
?>	
<!-- <script src="https://player.vimeo.com/api/player.js"></script> -->
<script type="text/javascript">

	$options = {
		id: 358305100,
		width: 560,
		height: 315
	}

	$(document).on('click', '#c', function(){
		// var player = new Vimeo.Player($('#a'), $options);
		render_vimeo_video($('#a'), $options);
	});


	$(document).on('click', '#b', function(){
		$players[358305100].setCurrentTime(17);
	});

	// var iframe = document.querySelector('iframe');
 //    var player = new Vimeo.Player(iframe);

	// $(document).on('click', '#b', function(){
	// 	// $time = $(this).text();
	// 	// $elem = $('#a').find('iframe');
	// 	// $src = $elem.attr('src') + '?autoplay=1#t='+$time;
	// 	// console.log($src);
	// 	// $elem.attr('src', $src);

	// });

	// player.on('seeking', function({
	// 							    duration: 61.857
	// 							    percent: 0.485
	// 							    seconds: 30
	// 							}) {
	// 		console.log($(this));
	// 		console.log('ok');
	// 	});
</script>
</body>
</html>