<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/lesson/views.php');

	if (!isset($subtopic_id) || !isset($code)) {
		echo 'ERROR';
	} else {
		$materials = get_materials_by_subtopic($code, $subtopic_id);
		// echo json_encode($materials, JSON_UNESCAPED_UNICODE);
	}
?>

<?php
	$html = '';
	if (count($materials) > 0) {
		$class = 'material-body';
		if (!isset($is_many) || $is_many) {
			$class = 'unbox-material-body';
		}
		$html .= "<div class='".$class."'>";
		$html .= "<div class='row'><div class='col-md-12 col-sm-12 col-xs-12'><h3 class='topic-title'>".$materials['title']."</h3></div></div>";
		
		if (count($materials['tutorial_video']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыптық видео:</h4></div>";
			foreach ($materials['tutorial_video'] as $tutorial_video_id => $tutorial_video) {
				$html .= "<div class='col-md-6 col-sm-6 col-xs-12' style='padding-top: 5%;'>";
					$html .= "<center><i>Видеоның ұзақтығы: ".$tutorial_video['duration']."</i></center>";
					$html .= "<div class='tutorial-video-tmp'>";
						$html .= "<input type='hidden' name='video_id' value='".$tutorial_video_id."'>";
						$html .= "<input type='hidden' name='link' value='".$tutorial_video['link']."'>";
					$html .= "</div>";
				$html .= "</div>";
			}
			$html .= "</div><hr></div>";
		}

		if (count($materials['tutorial_document']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыпқа байланысты есептер жане жауаптары:</h4></div>";
			$html .= "<div class='col-md-10 col-sm-10 col-xs-12'>";
			$question_img = "<div class='question-gallery' itemscope>";
			$count = 0;
			foreach ($materials['tutorial_document'] as $tutorial_document_id => $tutorial_document) {
				$count++;
				$link = $ab_root.'/academy'.$tutorial_document['link'];
				if ($count != count($materials['tutorial_document'])) {
					$class = '';
					$img_class = "";
					if ($count == 1) {
						$class .= 'question-primary';
						$img_class .= 'primary-img';
					} else {
						$class .= 'question-secondary';
						$img_class .= "secondary-img";
					}
					$question_img .= "<figure class='".$class."' itemprop='associatedMedia' itemscope>";
						$question_img .= "<a href='".$link."' itemprop='contentUrl' data-size=''>";
							$question_img .= "<img src='".$link."' class='".$img_class."' itemprop='thumbnail' alt='".$count."-бет' />";
						$question_img .= "</a>";
					$question_img .= "</figure>";
				} else {
					$answer_img = "<div class='answer-gallery'>";
						$answer_img .= "<input type='hidden' name='src' value='".$link."'/>";
						$answer_img .= "<input type='hidden' name='size' value='1500x2000'/>";
						$answer_img .= "<button class='btn btn-info btn-lg question-answer-btn' style='margin-top: 4%'>Есептердің жауаптары</button>";
					$answer_img .= "</div>";
				}
			}
			$question_img .= "</div>"; // .question-gallery
			$html .= $question_img;
			$html .= $answer_img;
			$html .= "</div>";
			$html .= "</div>"; // .row
			$html .= "<hr></div>"; // .material-box
		}

		if (count($materials['end_video']) > 0) {
			$html .= "<div class='material-box'><div class='row'>";
			$html .= "<div class='col-md-12 col-sm-12 col-xs-12'><h4>Тақырыпқа байланысты есептердің шығару жолы:</h4></div>";
			foreach ($materials['end_video'] as $end_video_id => $end_video) {
				$html .= "<div class='end_video_content col-md-6 col-sm-6 col-xs-12'>";
					$html .= "<div class='end_video_tmp'>";
						$html .= "<input type='hidden' name='video_id' value='".$end_video_id."'>";
						$html .= "<input type='hidden' name='link' value='".$end_video['link']."'>";
						$html .= "<input type='hidden' name='video_duration' value='".$end_video['duration']."'>";
						$html .= "<input type='hidden' name='video_second_duration' value='".$end_video['second_duration']."'>";
					$html .= "</div>";
					$html .= "<div class='timecode'></div>";
				$html .= "</div>";
			}
			$html .= "</div><hr></div>";
		}

		if ($materials['material_test'] != 0) {
			$html .= "<div class='material-box'><div class='row'><div class='col-md-12 col-sm-12 col-xs-12'>";
				$html .= "<button class='btn btn-primary btn-block btn-md open-pre-test-start' data-toggle='modal' data-subtopic-id='".$subtopic_id."' data-target='#pre-test-start'>Тақырыпқа байланысты <br class='hidden-lg hidden-md hidden-sm'> тест жұмысы</button>";
			$html .= "</div></div></div>";
		}
		$html .= "</div>";
	}
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