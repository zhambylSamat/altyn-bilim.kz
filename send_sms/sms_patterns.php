<?php
	
	$phone = "+77773890099";
	$end_text = "Алтын Білім оқу орталығы ".$phone;
	$start_text = "Құрметті Ата-ана,";
	$congrats = "Құттықтаймыз! ";

	$pattern_1 = "пәні бойынша";

	$MONTHS = array("", "қаңтар", "ақпан", "наурыз", "сәуір", "мамыр", "маусым", "шілде", "тамыз", "қыркуйек", "қазан", "қараша", "желтоқсан");


	$TRIAL_TEST_MAX_MARK = $start_text." %s %s ".$pattern_1." сынақ тесттен жоғары балл жинап, келесі айға %s жеңілдік алды! ".$congrats.$end_text;
	$TRIAL_TEST_INCREASE_MARK_3_TIMES = $start_text." %s %s ".$pattern_1." сынақ тесттен 3 рет қатарынан көтеріліп, келесі айға %s жеңілдік алды! ".$congrats.$end_text;
	$QUIZ_MAX_MARK = $start_text." %s %s ".$pattern_1." бақылауды 100%%-ға жазып, келесі айға %s жеңілдік алды! ".$congrats.$end_text;
	$QUIZ_MAX_MARK_2 = $start_text." %s %s ".$pattern_1." келесі бақылауды да 100%%-ға жазып, келесі айға қосымша %s жеңілдік алды! ".$congrats.$end_text;

	$ABSENT_2_TIMES = $start_text." %s %s пәніне қатарынан соңғы 2 сабаққа %s, %s күндері келмеді. ".$end_text;
	$QUIZ_RETAKE = $start_text." %s %s ".$pattern_1." негізгі бақылаудан %s және қайта тапсыру бақылауынан %s алып, бақылаудан өте алмағанын ескертеміз. ".$end_text;
	$NO_HOME_WORK_2_TIMES = $start_text." %s %s ".$pattern_1." осы айда 2 рет үй жұмысын орындамай келді. Қадағалауыңызды сұраймыз. ".$end_text;
	$NO_HOME_WORK = $start_text." %s %s ".$pattern_1." %s күні үй жұмысын орындамай келді. Қадағалауыңызды сұраймыз. ".$end_text;

	$END_COURSE = $start_text." %s Алтын Білім-де %s ".$pattern_1." оқуын тоқтатты. ҰБТ-да сәттілік тілейміз. ".$end_text;
	$START_COURSE = $start_text." %s Алтын Білім-де %s ".$pattern_1." оқуын бастады. Оқуда сәттілік тілейміз. ".$end_text;

	$ENT_RESULT = "ҰБТ-қорытындысы. %s. Барлығы: %s. %s, %s, %s, %s, %s. ".$end_text;

	// $res = kiril2latin(sprintf($END_COURSE, "Рахымназар", "Қазақстан тарихы"));
	// "11 желтоқсан", "13 желтоқсан", "15 желтоқсан"
	 // "55%", "69%"
	// echo $res;
	// echo "<br><br>";
	// echo strlen($res);

	function kiril2latin($str) {
		$converter = array(
			"а" => "a", "ә" => "a", "б" => "b", "в" => "v", "г" => "g", "ғ" => "g", 
			"д" => "d", "е" => "e", "ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", 
			"й" => "i", "к" => "k", "қ" => "k", "л" => "l", "м" => "m", "н" => "n", 
			"ң" => "n", "о" => "o", "ө" => "o", "п" => "p", "р" => "r", "с" => "s", 
			"т" => "t", "у" => "u", "ұ" => "u", "ү" => "u", "ф" => "f", "х" => "kh", 
			"һ" => "h", "ц" => "tc", "ч" => "ch", "ш" => "sh", "щ" => "sh", "ь" => "", 
			"ы" => "y", "і" => "i", "ъ" => "", "э" => "e", "ю" => "yu", "я" => "ya",

			"А" => "A", "Ә" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Ғ" => "G", 
			"Д" => "D", "Е" => "E", "Ё" => "E", "Ж" => "Zh", "З" => "Z", "И" => "I", 
			"Й" => "I", "К" => "K", "Қ" => "K", "Л" => "L", "М" => "M", "Н" => "N", 
			"Ң" => "N", "О" => "O", "Ө" => "O", "П" => "P", "Р" => "R", "С" => "S", 
			"Т" => "T", "У" => "U", "Ұ" => "U", "Ү" => "U", "Ф" => "F", "Х" => "Kh", 
			"Һ" => "H", "Ц" => "Tc", "Ч" => "Ch", "Ш" => "Sh", "Щ" => "Sh", "Ь" => "", 
			"Ы" => "Y", "І" => "I", "Ъ" => "", "Э" => "E", "Ю" => "Iu", "Я" => "Ya"  
		);

		return strtr($str, $converter);
	}
?>