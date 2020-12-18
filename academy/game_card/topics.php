<?php
	if (!isset($_GET['dir']) || !is_dir($_GET['dir'])) {
		header('location:cards.php');
	}
	$dir = $_GET['dir'];
	include_once('views.php');
	$topics = get_topics($dir);
	if (count($topics) == 0) {
		header('location:cards.php');	
	}
?>
<div id='choose-topic'>
	<?php
		$html = '';
		$html .= "<div class='col-md-4 col-sm-4 col-xs-4'>";
			$html .= "<center><img src='".$topics['all_topic_random']['cover_dir']."' class='topic-cover-img choose-all-random-topic-img'data-dir='".$topics['all_topic_random']['topics_dir']."'></center>";
		$html .= "</div>";
		foreach ($topics['topics'] as $topic) {
			$html .= "<div class='col-md-4 col-sm-4 col-xs-4'>";
				$html .= "<center><img src='".$topic['cover']."' class='topic-cover-img choose-topic-img' data-dir='".$topic['dir']."'></center>";
			$html .= "</div>";
		}
		echo $html;
	?>
</div>