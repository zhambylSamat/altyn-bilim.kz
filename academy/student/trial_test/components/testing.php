<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/trial_test/view.php');

	if (!isset($_GET['student_trial_test_id'])) {
		header('Location:../../index.php');
	}

	$student_trial_test_id = $_GET['student_trial_test_id'];

	$test_info = get_test_info_by_stt_id($student_trial_test_id);

	// print_r($get_test_info);
	// echo json_encode($test_info, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html>
<head>
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
		.true-ans {
			background-color: #61BA67;
			color: white;
		}
		.false-ans {
			background-color: #CB3D3D;
			color: white;
		}
	</style>
	<?php
		include_once($root.'/common/assets/meta.php');
		include_once($root.'/common/assets/style.php');
	?>
	<title>Пробный тест</title>
</head>
<body>

	<?php
		$has_test_result = count($test_info['test_result']) == 0 ? false : true;

		$html = "";
		$html .= "<div class='container' style='margin-top: 3%; margin-bottom: 10%;'>";
			$html .= "<div class='row'>";
				$html .= "<div class='col-md-8 col-sm-7 col-xs-12'>";
					$count = 0;
					$question_html = "<center><div class='question-gallery itemscope'>";

					foreach ($test_info['test']['test_files'] as $link) {
						$count++;
						$question_class = 'question-primary';
						$img_class = 'primary-img';
						if ($count > 1) {
							$question_class = 'question-secondary';
							$img_class = 'secondary-img';
						}
						$question_html .= "<figure class='".$question_class."' itemprop='associatedMedia' itemscope>";
							$question_html .= "<a href='".$link."' itemprop='contentUrl' data-size='1500x2000'>";
								$question_html .= "<img src='".$link."' class='".$img_class."' itemprop='thumbnail'>";
							$question_html .= "</a>";
						$question_html .= "</figure>";
					}
					$question_html .= "</div></center>";

					$html .= $question_html;
				$html .= "</div>"; // .col-...

				$html .= "<div class='col-md-4 col-sm-5 col-xs-12'>";
					if ($has_test_result) {
						$html .= "<table class='table table-striped table-bordered'>";
						// foreach ($test_info['test_result']['result'] as $numeration => $res) {
						// 	$html .= "<tr>";
						// 		$html .= "<td><center>".$numeration.")</center></td>";
						// 		foreach ($res as $val) {
						// 			$class = "";
						// 			$extra_html = "";
						// 			if ($val['expected']) {
						// 				if ($val['actual']) {
						// 					$class = 'true-ans';
						// 				} else {
						// 					$extra_html = "&nbsp;&nbsp;<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
						// 				}
						// 			} else {
						// 				if ($val['actual']) {
						// 					$class = "false-ans";
						// 				}
						// 			}
						// 			$html .= "<td class='".$class."'><center>".$val['prefix'].$extra_html."</center></td>";
						// 		}
						// 	$html .= "</tr>";
						// }
						foreach ($test_info['test']['answers'] as $numeration => $answer) {
							$html .= "<tr>";
								$html .= "<td>".$numeration.")</td>";
								foreach ($answer as $id => $value) {
									$class = "";
									$extra_html = "";
									if (in_array($id, $test_info['test_result']['result'][$numeration]['actual_result'])) {
										if ($value['torf'] == 1) {
											$class = 'true-ans';
											$extra_html = "&nbsp;&nbsp;<span class='glyphicon glyphicon-ok' style='color: green; display:inline;'></span>";
										} else {
											$class = "false-ans";
										}
									} else if ($value['torf'] == 1) {
										$extra_html = "&nbsp;&nbsp;<span class='glyphicon glyphicon-ok' style='color: green; display:inline;'></span>";
									}	
									$html .= "<td class='".$class."'><center><span>".$value['prefix']."</span>".$extra_html."</center></td>";
								}
							$html .= "</tr>";
						}
						$html .= "</table>";
						$html .= "<div id='test-result-message'>";
							$actual_result = $test_info['test_result']['actual_result'];
							$total_result = $test_info['test_result']['total_result'];

							$html .= "<p>Жинаған баллың: <b>".$actual_result."</b></p>";
							$html .= "<p>Барлығы: <b>".$total_result."</b></p>";
						$html .= "</div>";
					} else {
						$html .= "<form class='submit-trial-test-form' style='margin-top: 5%'>";
							$html .= "<input type='hidden' name='student-trial-test-id' value='".$student_trial_test_id."'>";
							$html .= "<table class='table table-striped table-bordered'>";
								foreach ($test_info['test']['answers'] as $numeration => $answer) {
									$html .= "<tr>";
										$html .= "<td><center>".$numeration.")</center></td>";
										foreach ($answer as $id => $value) {
											$html .= "<td style='padding: 0px; margin: 0px;'>";
												$html .= "<center>";
													$html .= "<label style='padding: 5px 0px 0px 5px; width: 100%; height: 100%;' for='id-".$numeration."-".$id."'>".$value['prefix'];
													if (count($answer) == 8) {
														$html .= "<input type='checkbox' style='margin-left: 5px;' class='answer-prefix-checkbox' id='id-".$numeration."-".$id."' name='answer[".$numeration."][]' value='".$id."'>";
													} else {
														$html .= "<input type='radio' style='margin-left: 5px;' class='answer-prefix-radio' id='id-".$numeration."-".$id."' name='answer[".$numeration."][]' value='".$id."' required>";
													}
													$html .= "</label>";
												$html .= "</center>";
											$html .= "</td>";
										}
									$html .= "</tr>";
								}
							$html .= "</table>";
							$html .= "<input type='submit' class='btn btn-sm btn-success' value='Тестті аяқтау'>";
						$html .= "</form>";
					}
				$html .= "</div>"; // .col-...
			$html .= "</div>"; // .row
		$html .= "</div>"; // .container

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
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/photoswipe.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/default-skin/default-skin.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/custom_style/photo-swipe-style.css?v=0.0.4'; ?>">
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/custom_js/single-photo-swipe-action.js?v=0.0.2'; ?>"></script>

	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/photoswipe.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/photoswipe-ui-default.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/custom_js/photo-swipe-action.js?v=0.0.7'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/student/js/trial_test.js?v=1.6.8'; ?>"></script>
</body>
</html>