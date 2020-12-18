<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script> -->
<script type="text/javascript">
	$a = [1, 2, 3, 4, 5, 6];

	for (var i = 0; i<$a.length; i++) {
		if ($a[i] == 3) {
			$a.splice(i, 1);
			i--;
		} else if ($a[i] == 4) {
			$a.splice(i, 1);
			i--;
		}
	}
	console.log($a);

	// $.each($a, function($index, $element) {
	// 	if ($element == 3) {
	// 		$a.splice($index, 1);
	// 		console.log($index, 'one');
	// 	} else if ($element == 4) {
	// 		$a.splice($index, 1);
	// 		console.log($index, 'two');
	// 	}
	// 	console.log($element);
	// });
	// console.log($a);

</script>