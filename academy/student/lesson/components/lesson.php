<?php
	GLOBAL $ab_root;
	include_once($root.'/student/lesson/view.php');
	if (!isset($_SESSION['materials'])) {
		$content = array();
	} else {
		$content = $_SESSION['materials'];
		// echo json_encode($content, JSON_UNESCAPED_UNICODE);
	}
	$week_day_str = array('', 'Дүйсенбі', 'Сейсенбі', 'Сәрсенбі', 'Бейсенбі', 'Жұма', 'Сенбі', 'Жексенбі');
?>
<style type="text/css">
	@media (max-width: 421px) {
		#danger-info-box {
			padding: 4% 2% 1% 2% !important;
		}	
		#danger-info-description {
			margin-top: 1%;
			display: inline-block;
		}
	}
	#danger-info-box {
		padding: 1% 2%;
	}
	.danger-info-text {
		color: #C7012E;
		font-size: 13px;
		margin: 0;
	}

	#danger-info-description {
		color: black;
		font-size: 16px;
	}
	.danger-info-description-mark {
		text-decoration: underline;
		font-style:italic;
	}
</style>
<div id='vimeo-video'>
	<div id='vimeo-content'></div>
</div>
<div>
	<a style='cursor:pointer;' id='back-to-subtopic-list'><span class='glyphicon glyphicon-chevron-left'></span> Тақырыптар тізіміне оралу</a>
	<button class='btn btn-sm btn-default pull-right go-to-instruction-page'>Инструкция</button>
</div>
<div id='danger-info-box'>
	<p class='danger-info-text'>
		Жаңа тақырып 07:00-ге дейін ашық тұрады 
		<button id='danger-info-mark' data-toggle='modal' data-target='#danger-info-modal' class='glyphicon glyphicon-search btn btn-primary btn-xs'></button>
		<!-- <button id='danger-info-mark' class='glyphicon glyphicon-search btn btn-primary btn-xs' data-trigger="focus" tabindex="0"  data-container="body" data-toggle="popover" data-placement="bottom" data-content='Егер жаңа тақырыпты бүгін көріп үлгермейтін болып жатсаң, онда видеосабақтың "play" батырмасын баспа. Сол кезде бұл тақырып келесі күні тағы ашық тұрады. Алайда, 3 сабақ бойы көрмесең, тақырып өшіп қалады.'></button> -->
	</p>
</div>

<div class="modal fade" id="danger-info-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">
      		<div class="modal-body">
      			<span id='danger-info-description'>Егер жаңа тақырыпты бүгін көріп үлгермейтін болып жатсаң, онда видеосабақтың <span class='danger-info-description-mark'>"play"</span> батырмасын баспа. Сол кезде бұл тақырып келесі күні тағы ашық тұрады. Алайда, <span class='danger-info-description-mark'>3 сабақ бойы</span> көрмесең, тақырып өшіп қалады.</span> 
      		</div>
    	</div>
	</div>
</div>

<?php
	if ($_SESSION['user_id'] == 87) {
		echo "<div id='console'></div>";
	}
?>
<?php 
	if (isset($_SESSION['test_finished']) && $_SESSION['test_finished'] == 'true') {
		$_SESSION['test_finished'] = 'false';
?>
	<div class='container'>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong>Тест аяқталды!</strong> Жауабын төменнен көруге болады
		</div>		
	</div>
<?php } ?>


<?php
	$html = '';
	$class = 'material-body';
	if (count($content) > 1) {
		$class = 'unbox-material-body';
	}
	$is_last_subtopic = false;
	// echo json_encode($content, JSON_UNESCAPED_UNICODE);
	foreach ($content as $lp_id =>  $value) {
		$materials = $value['materials'];
		$week_id = date('w', strtotime($value['access_date']));
		if (!$is_last_subtopic && $value['is_last_subtopic']) {
			$is_last_subtopic = true;
		}
		$date_text = '';
		if ($week_id == date('w')) {
			$date_text = ' (Жаңа тақырып)';
		} else {
			$week_id = $week_id == 0 ? 7 : $week_id;
			$date_text = ' ('.$week_day_str[$week_id].' күнгі тақырып)';
		}
		$html_content = "";
		$html_content .= "<div class='".$class."'>";
		$html_content .= "<div class='row'><div class='col-md-12 col-sm-12 col-xs-12'><span class='topic-title'>".$value['subtopic_title'].$date_text."</span></div></div>";

		if (count($materials['tutorial_video']) > 0) {
			$html_content .= "<div class='material-box'>";
			$html_content .= "<div class='row'>";
			$html_content .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$html_content .= "<p class='subtitle'>Тақырыптық видео:</p>";
			$html_content .= "</div>";
			foreach ($materials['tutorial_video'] as $tutorial_video) {
				$html_content .= "<div class='tutorial_video_content video-content col-md-7 col-sm-7 col-xs-12'>";
					$html_content .= "<center><i class='video-duration'></i></center>";
					$html_content .= "<div class='tutorial-video-tmp'>";
						$html_content .= "<input type='hidden' name='access_before' value='".$tutorial_video['log']['formatted_access_before']."'/>";
						$html_content .= "<input type='hidden' name='video_id' value='".$tutorial_video['id']."'/>";
						$html_content .= "<input type='hidden' name='link' value='".$tutorial_video['link']."'/>";
						$html_content .= "<input type='hidden' name='duration' value='".$tutorial_video['duration']."'/>";
						$html_content .= '<input type="hidden" name="action-id" value="'.$tutorial_video['action_id'].'"/>';
						$html_content .= '<input type="hidden" name="video-pop-up" value="'.$tutorial_video['pop_up'].'">';
					$html_content .= "</div>";
				$html_content .= "</div>"; // .tutorial_video_content
			}
			$html_content .= "</div>"; // .row
			$html_content .= "<hr>";
			$html_content .= "</div>"; // .material-box
		}

		if (count($materials['tutorial_document']) > 0) {
			$html_content .= "<div class='material-box'>";
			$html_content .= "<div class='row'>";

			$html_content .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$html_content .= "<span class='subtitle'>Тақырыпқа байланысты есептер жане жауаптары:</span>";
			$html_content .= "</div>";
			$html_content .= "<div class='col-md-10 col-sm-10 col-xs-12'>";

			$count = 0;
			$question_html = "<div class='question-gallery' itemscope>";
			$answer_html = "";

			foreach ($materials['tutorial_document'] as $tutorial_document) {
				$count++;
				$link = $ab_root.'/academy'.$tutorial_document['link'];
				if ($count != count($materials['tutorial_document'])) {
					$question_class = 'question-primary';
					$img_class = "primary-img";
					if ($count > 1) {
						$question_class = 'question-secondary';
						$img_class = 'secondary-img';
					}
					$question_html .= "<figure class='".$question_class."' itemprop='associatedMedia' itemscope>";
						$question_html .= "<a href='".$link."' itemprop='contentUrl' data-size='1500x2000'>";
							$question_html .= "<img src='".$link."' class='".$img_class."' itemprop='thumbnail'>";
						$question_html .= "</a>";
					$question_html .= "</figure>";
				} else {
					$answer_html .= "<div class='answer-gallery'>";
						$answer_html .= "<input type='hidden' name='src' value='".$link."'/>";
						$answer_html .= "<input type='hidden' name='size' value='1500x2000'/>";
						$answer_html .= "<button class='btn btn-info btn-lg question-answer-btn' style='margin-top: 4%;'>Есептердің жауаптары</button>";
					$answer_html .= "</div>";
				}
			}

			$question_html .= "</div>"; // .question-gallery

			$html_content .= $question_html;
			$html_content .= $answer_html;
			$html_content .= "</div>"; // .col-md-10 ...
			$html_content .= "</div>"; // .row
			$html_content .= "<hr>";
			$html_content .= "</div>"; // .material-box
		}

		if (count($materials['end_video']) > 0) {
			$html_content .= "<div class='material-box'>";
			$html_content .= "<div class='row'>";

			$html_content .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$html_content .= "<span class='subtitle'>Тақырыпқа байланысты есептердің шығару жолы:</span>";
			$html_content .= "</div>";

			foreach ($materials['end_video'] as $end_video) {
				$html_content .= "<div class='end_video_content video-content col-md-7 col-sm-7 col-xs-12'>";
					$html_content .= "<center><i class='video-duration'></i></center>";
					$html_content .= "<div class='end_video_tmp'>";
						$html_content .= "<input type='hidden' name='video_id' value='".$end_video['id']."'/>";
						$html_content .= "<input type='hidden' name='link' value='".$end_video['link']."'/>";
						$html_content .= "<input type='hidden' name='duration' value='".$end_video['duration']."'/>";
						$html_content .= "<input type='hidden' name='access_before' value='".$end_video['log']['formatted_access_before']."'/>";
						$html_content .= '<input type="hidden" name="action-id" value="'.$end_video['action_id'].'"/>';
					$html_content .= "</div>";
					$html_content .= "<div class='timecode'></div>";
				$html_content .= "</div>"; // .end_video_content
			}

			$html_content .= "</div>"; // .row
			$html_content .= "<hr></div>"; // .material-box
		}

		if ($materials['is_exist_material_test']) {
			$html_content .= "<div class='material-box'>";
			$html_content .= "<div class='row'>";
			$html_content .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
				if ($materials['has_finished']) {
					$html_content .= "<div class='finish-test'>";
						$html_content .= "<input type='hidden' name='subtopic_id' value='".$value['subtopic_id']."'>";
						$html_content .= "<input type='hidden' name='material_test_action_id' value='".$materials['material_test_action_id']."'>";
						$html_content .= "<input type='hidden' name='actual_result' value='".$materials['test_result']['actual_result']."'>";
						$html_content .= "<input type='hidden' name='total_result' value='".$materials['test_result']['total_result']."'>";
					$html_content .= "</div>";
				} else {
					$html_content .= "<span class='subtitle' style='font-size: 16px;'>Тақырыпқа байланысты тест жұмыстары: </span>";
					$html_content .= "<a class='btn btn-success btn-lg' href='".$ab_root."/academy/student/lesson/testing.php?subtopic_id=".$value['subtopic_id']."&mta=".$materials['material_test_action_id']."'>Тестті бастау</a>";
				}
			$html_content .= "</div>";
			$html_content .= "</div>";
			$html_content .= "</div>";
		}
		if ($value['is_army_group']) {
			$html_content .= "<div class='material-box'>";
		
			$html_content .= "<button class='btn btn-primary btn-md submit-class-work-btn' data-toggle='modal' data-target='#class-work-submit-form' data-lp-id='".$lp_id."'>Конспектті ж/не с. жұмысының<br> шығарылуын жіберу</button>";
			foreach ($materials['class_work_files'] as $gscwsf_id => $file_link) {
				$html_content .= "<input type='hidden' class='gscwsf-infos' data-gscwsf-id='".$gscwsf_id."' data-gscwsf-file-link='".$ab_root.'/academy/'.$file_link."'>";
			}
			$html_content .= "</div>";
		}

		$html_content .= "</div>"; // .material-body ...

		if (count($materials['tutorial_video']) != 0 || count($materials['end_video']) != 0 || count($materials['tutorial_document']) != 0) {
			$html .= $html_content;
		}
	}
	if ($is_last_subtopic) {
		$href = "window.open('../game_card','_blank')";
		$html .= "<center>";
			$html .= "<div class='row game-box'>";
				$html .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
					$html .= "<p class='game-text'>Осы өткен тарауың бойынша теориялық дайындығыңды төмендегі ЭЛЕКТРОНДЫ КАРТОЧКАЛАР арқылы тексеріп шық:</p>";
					$html .= "<button class='beautiful-btn btn-play btn-sep icon-cart' onclick=$href>Ойынды бастау</button>";
				$html .= "</div>";
			$html .= "</div>";
		$html .= "</center>";
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


<div class="modal fade" id="class-work-submit-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  		<div class="modal-header">
	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    		<h4 class="modal-title" id="myModalLabel">Конспектті және сынып жұмысының шығарылуын жіберу</h4>
	  		</div>
	  		<div class="modal-body">
	  			<div class='uploaded-imgs'>
	  			</div>
	  			<input type="hidden" name="lesson_progress_id" id='modal-lp-id' value=''>
	  			<div class='upload-img-box'>
	  				<label class='upload-img-label' for='upload-home-work-1'>
	  					<i class="fas fa-plus"></i>
	  					<div class='percent'></div>
	  				</label>
	  				<input type="file" name="home-work-img" multiple id='upload-home-work-1' class='upload-img-input' accept='.jpeg,.jpg,.png,.gif'>
	  			</div>
	  		</div>
	  		<div class='modal-footer'>
	  			<button class='btn btn-sm btn-success' data-dismiss="modal">Жіберу</button>
	  		</div>
		</div>
	</div>
</div>


<div class="modal fade" id="class-work-img-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  		</div>
	  		<div class="modal-body">
	  		</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$element = $('.material-body');
		render_selected_content($element);
		initPhotoSwipeFromDOM('.question-gallery');
	});
	$('.lesson-body').ready(function() {
		set_test_result();
	});
</script>