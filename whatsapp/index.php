<!DOCTYPE html>
<html>
<head>
	<title>Whatsapp</title>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>
<body>

	<div>
		<label>Номері</label>
		<input type="text" id='number'>
	</div>
	<br>
	<div>
		<label>Текст</label>
		<textarea id='text' cols='80' rows='30'></textarea>
	</div>
	<br>
	<button type='button' id='create-link'>Ссылка құрастыру</button>
	<br>
	<br>
	<a href="" target='_blank' id='wh_link'></a>

	<script type="text/javascript">
		$(document).on('click', '#create-link', function() {
			$number = $('#number').val();
			$text = $('#text').val();
			$text = $text.replace(/(?:\r\n|\r|\n)/g,"%0A");
			$text = $text.replace(/\s/g,"%20");

			// $link = "https://wa.me/7"+$number+"?text="+$text;
			$link = "https://api.whatsapp.com/send?phone=7"+$number+'&text='+$text;

			$('#wh_link').text($link);
			$('#wh_link').attr('href', $link);
			// %20 space
			// %0A new line
		});
	</script>
</body>
</html>