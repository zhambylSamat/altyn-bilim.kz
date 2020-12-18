<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
		include_once($root.'/common/assets/meta.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/lesson/views.php');
	?>
	<link rel="stylesheet" type="text/less" href="<?php echo $ab_root.'/academy/lesson/style/style.less?v=1.1.2'; ?>">
	<title>Тақырыпқа байланысты тест жұмысы</title>

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
</head>
<body>
	<?php
		if (!isset($_GET['fio']) || !isset($_GET['code']) || !isset($_GET['subtopic_id'])) {
			header('Location:index.php');
		}
		$fio = $_GET['fio'];
		$code = $_GET['code'];
		$subtopic_id = $_GET['subtopic_id'];
		$material_test_info = get_material_test_info($fio, $code, $subtopic_id);

		// echo json_encode($material_test_info, JSON_UNESCAPED_UNICODE);

		// if (!$material_test_info['is_test_access']) {
			// header('Location:index.php');
		// }

		$has_test_result = count($material_test_info['test_result']) == 0 ? false : true;

		$html = "";
		$html .= "<div class='container' style='margin-top: 3%; margin-bottom: 10%;'>";
			$html .= "<div class='row'>";
				$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><center><h4>".$fio."</h4></center></div>";
				$html .= "<div class='col-md-8 col-sm-7 col-xs-12'>";
					$count = 0;
					$question_html = "<center><div class='question-gallery itemscope'>";

					foreach ($material_test_info['material_test']['test'] as $link) {
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
						foreach ($material_test_info['test_result']['results'] as $numeration => $res) {
							$html .= "<tr>";
								$html .= "<td><center>".$numeration.")</center></td>";
								foreach ($res as $val) {
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
							$actual_result = $material_test_info['test_result']['actual_result'];
							$total_result = $material_test_info['test_result']['total_result'];
							$percent = ceil(intval(($actual_result/$total_result)*100));

							$html .= "<p>Дұрыс жауап: <b>".$actual_result."</b></p>";
							$html .= "<p>Қате жауап: <b>".($total_result - $actual_result)."</b></p>";
							$html .= "<p>Қорытынды: <b>".$percent."%</b></p>";
						$html .= "</div>";
					} else {
						$html .= "<form class='submit-test-form' style='margin-top: 5%'>";
							$html .= "<input type='hidden' name='fio' value='".$fio."'>";
							$html .= "<input type='hidden' name='subtopic_id' value='".$subtopic_id."'>";
							$html .= "<input type='hidden' name='material_link_id' value='".$material_test_info['material_link_id']."'>";
							$html .= "<table class='table table-striped table-bordered'>";
								foreach ($material_test_info['material_test']['answers'] as $numeration => $answer) {
									$html .= "<tr>";
										$html .= "<td><center>".$numeration.")</center></td>";
										foreach ($answer as $id => $prefix) {
											$html .= "<td style='padding: 0px; margin: 0px;'>";
												$html .= "<center>";
													$html .= "<label style='padding: 5px 0px 0px 5px; width: 100%; height: 100%;' for='id-".$numeration."-".$id."'>".$prefix;
													$html .= "<input type='radio' style='margin-left: 5px;' class='answer-prefix-radio' id='id-".$numeration."-".$id."' name='answer[".$numeration."]' value='".$id."' required>";
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
				if (count($material_test_info['material_test_solve']) > 0) {
					$html .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
						$html .= "<center><p id='test-solve-title'>Тесттегі есептердің шығару жолдары</p></center>";

						foreach ($material_test_info['material_test_solve'] as $value) {
							$html .= "<div class='test-solve-box' data-url='".$value['link']."'>";
								$html .= "<div class='test-solve-content'>";
									$html .= "<span>".$value['title']."</span>";
								$html .= "</div>"; // .test-solve-content
							$html .= "</div>"; // .test-solve-box
						}
					$html .= "</div>"; // .col-...
				}
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
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/lesson/js/actions.js?v=1.6.9'; ?>"></script>
</body>
</html>