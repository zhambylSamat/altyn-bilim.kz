<!DOCTYPE html>
<html>
<head>
	<title>Формулалар</title>
	<link rel="stylesheet" type="text/css" href="style.css?v=1.0.1">
</head>
<body>
	<main>
		<ol class="gradient-list">
		    <li class='file' data-url='Алгебра формулары.jpg'>Алгебра формулары <b>JPG</b></li>
		    <li class='file' data-url='Геометрия формулалары.jpg'>Геометрия формулалары <b>JPG</b></li>
		    <li class='file' data-url='Физика формулалары 1-ші бет.jpg'>Физика формулалары 1-ші бет <b>JPG</b></li>
		    <li class='file' data-url='Физика формулалары 2-ші бет.jpg'>Физика формулалары 2-ші бет <b>JPG</b></li>
		</ol>
	</main>
	<main>
		<ol class="gradient-list">
		    <li class='file' data-url='Алгебра формулары.pdf'>Алгебра формулары <b>PDF</b></li>
		    <li class='file' data-url='Геометрия формулалары.pdf'>Геометрия формулалары <b>PDF</b></li>
		    <li class='file' data-url='Физика формулалары 1-ші бет.pdf'>Физика формулалары 1-ші бет <b>PDF</b></li>
		    <li class='file' data-url='Физика формулалары 2-ші бет.pdf'>Физика формулалары 2-ші бет <b>PDF</b></li>
		</ol>
	</main>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		$(document).on('click', '.file', function() {
			$url = $(this).data('url');
			window.location.href = $url;
		});
	</script>
</body>
</html>