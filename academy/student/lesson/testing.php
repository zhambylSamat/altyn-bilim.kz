<style type="text/css">
	#test-solve {
		margin-top: 5%;
	}
	#test-solve-title {
		margin-bottom: 2%;
		font-size: 20px;
		font-weight: bold;
		font-style: italic;
	}
	.test-solve-box {
		display: inline-block;
		padding: 1%;
	}
	.test-solve-content {
		cursor: pointer;
		border: 1px solid lightgray;
		border-radius: 10px;
		display: table-cell;
		width: 70px;
		height: 70px;
		vertical-align: middle;
		text-align: center;
		background-color: #C3F876;
		color: black;
		font-size: 20px;
		font-weight: bold;
	}
	.test-solve-content:hover {
		background-color: #9DF846;
	}
</style>

<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/lesson/view.php');

	if (!isset($_GET['subtopic_id']) || !isset($_GET['mta'])) {
		header("Location:../index.php");
	}

	$subtopic_id = $_GET['subtopic_id'];
	$material_test_action_id = $_GET['mta'];

	$test_content = get_material_test_answers($subtopic_id, $material_test_action_id);

	$html = "<div class='container' style='margin-top: 3%; margin-bottom: 10%;'>";
		$html .= "<div class='row'>";
			$html .= "<div class='col-md-8 col-sm-7 col-xs-12'>";
				$count = 0;
				$question_html = "<center><div class='question-gallery' itemscope>";

				foreach ($test_content['test'] as $link) {
					$count++;
					$link = $ab_root.'/academy'.$link;
					$question_class = 'question-primary';
					$img_class = "primary-img";
					if ($count > 1) {
						$question_class = "question-secondary";
						$img_class = "secondary-img";
					}
					$question_html .= "<figure class='".$question_class."' itemprop='associatedMedia' itemscope>";
						$question_html .= "<a href='".$link."' itemprop='contentUrl' data-size='1500x2000'>";
							$question_html .= "<img src='".$link."' class='".$img_class."' itemprop='thumbnail'>";
						$question_html .= "</a>";
					$question_html .= "</figure>";
				}
				$question_html .= "</div></center>";

				$html .= $question_html;
			$html .= "</div>";

			$html .= "<div class='col-md-4 col-sm-5 col-xs-12'>";
				if ($test_content['has_finished']) {
					$html .= "<table class='table table-striped table-bordered'>";
					foreach ($test_content['test_result_json']['results'] as $numeration => $res) {
						$html .= "<tr>";
							$html .= "<td><center>".$numeration.")</center></td>";
							foreach ($res as $mta_id => $val) {
								$class = "";
								$extra_html = "";
								if ($val['expected']) {
									if ($val['actual']) {
										$class = 'true-ans';
									} else {
										$extra_html = "&nbsp;&nbsp;<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
									}
								} else {
									if ($val['actual']) {
										$class = "false-ans";
									}
								}
								$html .= "<td class='".$class."'><center>".$val['prefix'].$extra_html."</center></td>";
							}
						$html .= "</tr>";
					}
					$html .= "</table>";
					$html .= "<div id='test-result-message'>";
						$actual_result = $test_content['test_result_json']['actual_result'];
						$total_result = $test_content['test_result_json']['total_result'];
						$percent = ceil(intval(($actual_result/$total_result)*100));

						$html .= "<p>Дұрыс жауап: <b>".$actual_result."</b></p>";
						$html .= "<p>Қате жауап: <b>".($total_result - $actual_result)."</b></p>";
						$html .= "<p>Қорытынды: <b>".$percent."%</b></p>";
					$html .= "</div>";
				} else {
					$html .= "<form class='submit-test-form' action='controller.php' method='post' style='margin-top: 5%;'>"; //method='post' action='".$ab_root."/academy/student/lesson/controller.php'
						$html .= "<input type='hidden' name='has_finished' value='".$test_content['has_finished']."'>";
						$html .= "<input type='hidden' name='subtopic_id' value='".$subtopic_id."'>";
						$html .= "<input type='hidden' name='material_test_action_id' value='".$material_test_action_id."'>";
						$html .= "<table class='table table-striped table-bordered'>";
							foreach ($test_content['answers'] as $numeration => $answers) {
								$html .= "<tr>";
									$html .= "<td><center>".$numeration.")</center></td>";
									foreach ($answers as $id => $prefix) {
										$html .= "<td style='padding: 0px; margin: 0px;'>";
											$html .= '<center>';
												$html .= "<label style='padding: 5px 0px 0px 5px; width:100%; height: 100%;' for='id-".$numeration."-".$id."'>".$prefix;
												$html .= "<input style='margin-left: 5px;' type='radio' class='answer-prefix-radio' id='id-".$numeration."-".$id."' name='answer[".$numeration."]' value='".$id."' required>";
												$html .= "</label>";
											$html .= "</center>";
										$html .= "</td>";
									}
								$html .= "</tr>";
							}
						$html .= "</table>";
						if (!$test_content['has_finished']) {
							$html .= "<input type='submit' class='btn btn-sm btn-success' name='submit-test' value='Тестті аяқтау'>";
						}
					$html .= "</form>";
				}
			$html .= "</div>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12' id='test-solve'></div>";
		$html .= "</div>";
	$html .= "</div>";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($root.'/common/assets/meta.php');
	?>
	<title>Тест</title>
	<?php 
		include_once($root.'/common/assets/style.php');
		include_once('../student_style.php');
	?>
	<style type="text/css">
		.true-ans {
			background-color: #61BA67;
			color: white;
		}
		.false-ans {
			background-color: #CB3D3D;
			color: white;
		}
	</style>
</head>
<body>

	<?php
		echo $html;
	?>
	<!-- Root element of PhotoSwipe. Must have class pswp. -->
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

	    <!-- Background of PhotoSwipe. 
	         It's a separate element as animating opacity is faster than rgba(). -->
	    <div class="pswp__bg"></div>

	    <!-- Slides wrapper with overflow:hidden. -->
	    <div class="pswp__scroll-wrap">

	        <!-- Container that holds slides. 
	            PhotoSwipe keeps only 3 of them in the DOM to save memory.
	            Don't modify these 3 pswp__item elements, data is added later on. -->
	        <div class="pswp__container">
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	        </div>

	        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
	        <div class="pswp__ui pswp__ui--hidden">

	            <div class="pswp__top-bar">

	                <!--  Controls are self-explanatory. Order can be changed. -->

	                <div class="pswp__counter"></div>

	                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

	                <button class="pswp__button pswp__button--share" title="Share"></button>

	                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

	                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

	                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
	                <!-- element will get class pswp__preloader--active when preloader is running -->
	                <div class="pswp__preloader">
	                    <div class="pswp__preloader__icn">
	                      <div class="pswp__preloader__cut">
	                        <div class="pswp__preloader__donut"></div>
	                      </div>
	                    </div>
	                </div>
	            </div>

	            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
	                <div class="pswp__share-tooltip"></div> 
	            </div>

	            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
	            </button>

	            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
	            </button>

	            <div class="pswp__caption">
	                <div class="pswp__caption__center"></div>
	            </div>

	        </div>

	    </div>

	</div>

	<?php
		include_once($root.'/common/assets/js.php');
		include_once('../student_js.php');
	?>

	<script type="text/javascript">
		$subtopic_id = get_url_params('subtopic_id');
		$mta_id = get_url_params('mta');
		if ($subtopic_id !== undefined && $mta_id !== undefined) {
			set_test_solve($subtopic_id, $mta_id);
		}
	</script>
</body>
</html>