<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	if (!isset($lesson_progress_id)) {
		if (isset($_GET['lp_id'])) {
			$lesson_progress_id = $_GET['lp_id'];
		} else {
			header('Loaction:index.php');
		}
	}

	include_once($root.'/staff/group/view.php');
	$actions_log_result = get_materials_by_lesson_progress_id($lesson_progress_id);
	// echo json_encode($actions_log_result, JSON_UNESCAPED_UNICODE);
?>

<table class='table table-bordered table-striped td-cursor-pointer' data-lp-id="<?php echo $lesson_progress_id; ?>">
	<?php
		$html = "";
		$html .= "<tr>";
			$html .= "<th rowspan='2'><center>Аты-жөні</center></th>";
			$html .= "<th rowspan='2'><center>Материалдарға доступ берілген уақыт</center></th>";
			if (($tv_count = count($actions_log_result['tutorial_videos'])) > 0) {
				$html .= "<th colspan='".$tv_count."'><center>Тақырыптық видео</center></th>";
			}
			if (($ev_count = count($actions_log_result['end_videos'])) > 0) {
				$html .= "<th colspan='".$ev_count."'><center>Есептердің шығару жолы (видео)</center></th>";
			}
			if (($mt_count = count($actions_log_result['material_tests'])) > 0) {
				$html .= "<th colspan='1'><center>Тақырыпқа байланысты тесттер</center></th>";
			}
			$html .= "<th rowspan='2'><center>Статус</center></th>";
		$html .= "</tr>";

		$html .= '<tr>';
		$count = 0;
		foreach ($actions_log_result['tutorial_videos'] as $value) {
			$html .= "<td title='".$value['title']."'><center>№".(++$count)."</center></td>";
		}

		$count = 0;
		foreach ($actions_log_result['end_videos'] as $value) {
			$html .= "<td title='".$value['title']."'><center>№".(++$count)."</center></td>";
		}

		$count = 0;
		if (count($actions_log_result['material_tests']) > 0) {
			$html .= "<td title='Тест'><center>№".(++$count)."</center></td>";
		}
		$html .= '</tr>';

		$student_id = 0;
		foreach ($actions_log_result['student_list'] as $std_id => $std_val) {
			$progress_log_count = count($std_val['progress_log']);
			
			if ($student_id != $std_id) {
				$html .= "<tr style='border-top: 2px solid darkgray;'>";
				$full_name = $std_val['last_name'].' '.$std_val['first_name']."<br>"."<span>+7 ".$std_val['phone']."</span>";
				$archive_html = "";
				if ($std_val['is_archive'] == '1') {
					$archive_html .= "<p class='text-danger' style='font-weight: bold;'>";
						$archive_html .= "Архив";
						$archive_html .= "<button class='btn btn-xs btn-info activate-group-student' data-gs-id='".$std_val['group_student_id']."'>Архивтен шығару</button>";
					$archive_html .= "</p>";
				}
				$transfer_html = "";
				if ($std_val['is_archive'] != '1') {
					$transfer_html = "<button class='btn btn-info btn-xs pull-right transfer-group-btn' data-toggle='modal' data-target='#transfer-group-modal' data-gs-id='".$std_val['group_student_id']."'><i class='fas fa-exchange-alt'></i></button>";
				}
				$find_student_in_list_html = "<button class='btn btn-default btn-xs btn-block find-student-in-list' data-phone='".$std_val['phone']."' style='margin-top: 5%;'><i style='color: #28A4C9; font-size: 20px;' class='fas fa-search-location'></i></button>";
				$html .= "<td rowspan='".$progress_log_count."' title='".$full_name."'>".$full_name.$archive_html.$transfer_html.$find_student_in_list_html."</td>";
			} else {
				$html .= "<tr>";
			}

			foreach ($std_val['progress_log'] as $progress_id => $value) {
				$html .= "<td title='".$value['created_date']."'><center>".$value['created_date']."</center></td>";

				if ($tv_count > 0) {
					if (count($value['tutorial_video_action_logs']) == 0) {
						for ($i=0; $i < $tv_count; $i++) { 
							$html .= "<td title='Доступ берілмеген'><center>x</center></td>";
						}
					} else {
						foreach ($value['tutorial_video_action_logs'] as $tutorial_video_order => $tv_log_val) {
							if ($tv_log_val['tval_id'] == '' || $tv_log_val['tval_id'] == null) {
								$html .= "<td title='Видео көрілмеген'><center>-</center></td>";
							} else {
								$html .= "<td title='".$tv_log_val['opened_date']."'><center>";
									$html .= "<b>+</b>";
								$html .= "</center></td>";
							}
						}
					}
				}

				if ($ev_count > 0) {
					if (count($value['end_video_action_logs']) == 0) {
						for ($i = 0; $i < $ev_count; $i++) {
							$html .= "<td title='Доступ берілмеген'><center>x</center></td>";
						}
					} else {
						foreach ($value['end_video_action_logs'] as $end_video_order => $ev_log_val) {
							if ($ev_log_val['eval_id'] == '' || $ev_log_val['eval_id'] == null) {
								$html .= "<td title='Видео көрілмеген'><center>-</center></td>";
							} else {
								$html .= "<td title='".$ev_log_val['opened_date']."'><center>";
									$html .= "<b>+</b>";
								$html .= "</center></td>";
							}
						}
					}
				}

				if ($mt_count > 0) {
					if (isset($value['material_test_result']) && count($value['material_test_result']) > 0) {
						$test_action = $value['material_test_result'];
						if ($test_action['actual_result'] == '' || $test_action['actual_result'] == null) {
							$plus_minus = '';
							if ($test_action['is_finish'] == 0) {
								$plus_minus = '-';
							} else {
								$plus_minus = '+';
							}

							$html .= "<td rowspan='".$progress_log_count."' title='Тест орындамаған'><center>".$plus_minus."</center></td>";
						} else {
							$percent_result = 0.0;
							if ($test_action['actual_result'] > 0) {
								$percent_result = round(($test_action['actual_result'] / $test_action['total_result']) * 100, 1);
							}
							$html .= "<td rowspan='".$progress_log_count."' title='".$test_action['start_date'].' - '.$test_action['finish_date']."'><center>";
								$html .= "<b>".$test_action['actual_result'].'/'.$test_action['total_result'].' ('.$percent_result.'%)'."</b>";
							$html .= "</center></td>";
						}
					}
				}

				$extra_status = "";
				if ($std_val['status'] == 'inactive') {
					$extra_status = "<p><b style='color: #EA3131'>Оплатасы төленбеген</b></p>";
				} else if ($std_val['status'] == 'waiting') {
					$extra_status = "<p><b>".$std_val['title'].".</b> осы тақырыптан бастайды</p>";
				}

				if ($student_id != $std_val['id']) {
					if ($std_val['is_access']) {
						$tmp_html = "<b class='text-success'>Доступ ашық</b>";
					} else {
						$tmp_html = '<b class="text-danger">Доступ жабық</b>';
						$tmp_html .= "<br>";
						$tmp_html .= !$std_val['is_archive'] ? "<button class='btn btn-info btn-xs reaccess-material' data-lp-id='".$lesson_progress_id."' data-group-student-id='".$std_val['group_student_id']."'>Доступты қайта ашу</button>" : '';
					}
					$html .= "<td rowspan='".$progress_log_count."'>".$tmp_html.$extra_status."</td>";
				}
				$html .= "</tr>";
				$student_id = $std_val['id'];
			}
		}

		echo $html;
	?>
</table>


<?php
	$html = "";
	if (count($actions_log_result['student_submitted_class_work_list']) > 0) {
		$html .= "<center><h4>Оқушылардың үй жұмыстары:</h4></center>";
		$html .= "<table class='table table-striped table-bordered'>";
		foreach ($actions_log_result['student_submitted_class_work_list'] as $student_id => $student_info) {
			$html .= "<tr>";
				$html .= "<td>".$student_info['last_name']." ".$student_info['first_name']."</td>";
				$html .= "<td>";
					if (count($student_info['class_work_files']) > 0) {
						$html .= "<div class='student-class-work-img-gallery' itemscope>";
						foreach ($student_info['class_work_files'] as $file_link) {
							if ($file_link != '') {
								$html .= "<figure class='student-class-work-figure' itemprop='associatedMedia' itemscope style='display: inline-block;'>";
									$html .= "<a href='".$ab_root.'/academy/'.$file_link."' itemprop='contentUrl' data-size='1500x2000'>";
										$html .= "<img src='".$ab_root.'/academy/'.$file_link."' class='student-class-work-img' itemprop='thumbnail'>";
									$html .= "</a>";
								$html .= "</figure>";
							}
						}
						$html .= "</div>";
					} else {
						$numeration = 0;
						if (count($student_info['warning_info']) == 0) {
							$numeration = 1;
						} else {
							$numeration = $student_info['warning_info']['warning_count'];
							$numeration++;
						}
						if (count($student_info['warning_info']) > 0 
							&& in_array($lesson_progress_id, $student_info['warning_info']['lesson_progress_ids'])) {
							$gsnhww_id = $student_info['warning_info']['group_student_no_home_work_warning_id'];
							$html .= "<p class='text-danger' class='warning-setted-text'>".(--$numeration)."-ші ескерту жасалды</p>";
							$html .= "<button class='btn btn-sm btn-warning undo-no-home-work-warning' data-lp-id='".$lesson_progress_id."' data-gsnhww-id='".$gsnhww_id."'>".$numeration."-ші ескертуді болдырмау</button>";
						} else {
							if (count($student_info['warning_info']) > 0) {
								$group_student_id = $student_info['group_student_id'];
								$gsnhww_id = $student_info['warning_info']['group_student_no_home_work_warning_id'];
								if ($student_info['warning_info']['warning_count'] == 1) {
									$html .= "<button class='btn btn-lg btn-danger set-no-home-work-warning' data-gs-id='".$group_student_id."' data-lp-id='".$lesson_progress_id."' data-gsnhww-id='".$gsnhww_id."'>".$numeration." -ші ескерту. Группадан шығару</button>";
								} else {
									$html .= "<button class='btn btn-md btn-danger set-no-home-work-warning' data-gs-id='".$group_student_id."' data-lp-id='".$lesson_progress_id."' data-gsnhww-id='0'>Үй жұмысы жоқ. ".$numeration."-ші ескерту</button>";	
								}
							} else {
								$group_student_id = $student_info['group_student_id'];
								$html .= "<button class='btn btn-md btn-danger set-no-home-work-warning' data-gs-id='".$group_student_id."' data-lp-id='".$lesson_progress_id."' data-gsnhww-id='0'>Үй жұмысы жоқ. ".$numeration."-ші ескерту</button>";
							}
						}
					}
				$html .= "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
	}

	echo $html;

	if ($actions_log_result['is_army_group']) {
		include_once($root.'/staff/group/group/components/group_student_trial_test_list.php');
	}
	
	$html = "";
	if (count($actions_log_result['test_progress_information']) > 0) {
		$html .= "<center><h4>Оқушылардың осы тақырыпқа байланысты жазған тесттердің жалпы жауаптары</h4></center>";
		$html .= "<table class='table table-bordered table-striped'>";
			foreach ($actions_log_result['test_progress_information'] as $numeration => $info) {
				if ($info['passed'] == 0) {
					$percent = 0;
				} else {
					$percent = intval(($info['passed']/$info['total'])*100);
				}
				$html .= "<tr>";
					$html .= "<td>№".$numeration."-сұрақ</td>";
					$html .= "<td>";
						$html .= "<div class='progress'>";
							$html .= "<div class='progress-bar' role='progressbar' aria-valunow='".$percent."' aria-valuemin='0' aria-valuemax='100' style='width: ".$percent."%;'>";
							$html .= $info['passed'].' / '.$info['total'];
							$html .= "</div>";
						$html .= "</div>";
					$html .= "</td>";
				$html .= "</tr>";
			}
		$html .= "</table>";
	}

	echo $html;

?>


<div class="modal fade" id="transfer-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>


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
	initPhotoSwipeFromDOM('.student-class-work-img-gallery');
</script>