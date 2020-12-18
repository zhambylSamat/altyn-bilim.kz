<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/trial_test/views.php');

	if (!isset($_GET['trial_test_id'])) {
		$html = "<center><h4>Пробный тестті таңда</h4></center>";
	} else {
		$trial_test_id = $_GET['trial_test_id'];
		$trial_test_datas = get_trial_test_datas($trial_test_id);

		$html = "";
		$html .= "<input type='hidden' id='trial-test-id' value='".$trial_test_id."'>";
		$html .= "<div class='change-trial-test-img-order'>";
			$html .= "<button class='btn btn-xs btn-success save-order'>Ретті сақтау</button> ";
			$html .= "<button class='btn btn-xs btn-warning cancel-order'>Отмена</button>";
		$html .= "</div>";
		$html .= "<div class='uploaded-imgs sortable trial-test-img-gallery' itemscope>";
		foreach ($trial_test_datas['files'] as $trial_test_file_id => $file) {
			$html .= trial_test_single_img($trial_test_file_id, $ab_root.'/academy/'.$file['file_link'], $file['file_order']);
		}
		$html .= "</div>";
		$html .= "<div class='upload-img-box'>";
			$html .= "<label class='upload-img-label' for='upload-trial-test-file'>";
				$html .= "<i class='fas fa-plus'></i>";
				$html .= "<div class='percent'></div>";
			$html .= "</label>";
			$html .= "<input type='file' multiple id='upload-trial-test-file' class='upload-img-input' accept='.jpeg,.jpg,.png,.gif'>";
		$html .= "</div>";
		$html .= "<hr>";
		$html .= "<div class='trial-test-answers-box'>";
		$html .= "<input type='hidden' name='trial-test-id' value='".$trial_test_id."'>";
		if (count($trial_test_datas['answers']) > 0) {
			foreach ($trial_test_datas['answers'] as $numeration => $value) {
				$html .= trial_test_single_answer($numeration, $value);
			}
		}
		$html .= "</div>";
		$html .= "<button class='btn btn-sm btn-info add-answer' data-trial-test-id='".$trial_test_id."'>+ Жауап қосу</button>";
		$html .= "<hr>";
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

<script type="text/javascript">
	initPhotoSwipeFromDOM('.trial-test-img-gallery');
</script>