<?php
	if (!isset($_GET['dir']) || !is_dir($_GET['dir']) || !isset($_GET['is_random'])) {
		header('location:cards.php');
	}
	$dir = $_GET['dir'];
	$is_random = $_GET['is_random'];
	include_once('views.php');
	$part_questions_and_answers = get_questions_and_answers($dir, $is_random);
	if (count($part_questions_and_answers) == 0) {
		header('location:cards.php');	
	}
?>

<div class='select-question'>
	<div>
		<button class="previous-question" disabled>&laquo; Алдыңғы сұрақ</button>
	</div>
	<?php
		$html = "";
		foreach ($part_questions_and_answers as $index => $value) {
			$current_question_class = 'current-question';
			$other_question_class = 'questions-in-queue';
			$class = 'q-a-images ';
			if ($index != 0) {
				$class .= $other_question_class;
			} else {
				$class .= $current_question_class;
			}
			$html .= "<div class='".$class."' data-index='".$index."'>";
				$html .= "<div class='card-image question-img card-front'>";
					$html .= "<img src='".$value['q_dir']."' class='img-response'/>";
					// $html .= "<div class='img-helper'><span class='title'>Сұрақтың жауабын білу үшін үстінен бас</span></div>";
				$html .= "</div>";
				$html .= "<div class='card-image answer-img card-back'>";
					$html .= "<img src='".$value['a_dir']."' class='img-response'/>";
					// $html .= "<div class='img-helper'><span class='title'>Келесі сұрақ</span></div>";
				$html .= "</div>";
			$html .= "</div>";
		}
		echo $html;
	?>
</div>