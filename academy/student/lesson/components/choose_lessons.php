<style type="text/css">
	@media (max-width: 421px) {
		#lesson-choose-title {
			padding-bottom: 3%;
		}

		#next-payment-text {
			padding-bottom: 1%;
		}

		.lessons-box {
			margin-bottom: 15px !important;
		}
		
	}
	.freeze-off {
		margin-top: 1%;
	}
	@media (max-width: 768px) {
		.holiday-text-style {
			font-size: 14px !important;
		}
		.freeze-off {
			margin-top: 4%;
		}
	}
	#lesson-choose-title a, #lesson-choose-title span {
		font-size: 15px;
	}
	#holiday-box {
		background-color: #45D545;
		padding: 0.5%;
		border-radius: 5px;
		display: inline-block;
		margin-bottom: 1%;
	}
	.holiday-text-style {
		color: white;
		font-size: 16px;
	}
	.no-lesson-content {
		border-radius: 5px;
		border: 1px solid #F79463;
		background-color: #F7C600;
		margin: 1% 2%;
		padding: 2% 2%;
	}
	@media (max-width: 421px) {
		.no-lesson-content {
			padding: 5% 2%;
		}
		#first-trial-period {
			width: 100% !important;
		}
		#first-trial-period #text {
			font-size: 14px !important;
			line-height: 19px !important;
			font-style: italic !important;
		}
		#first-trial-period #text #inline-text {
			margin-top: 3% !important;
		}
	}
	#no-lesson-text {
		font-size: 16px;
		font-weight: bold;
	}
	#first-trial-period {
		width: 80%;
		border-radius: 5px;
		padding: 0% 1%;
		margin-bottom: 1%;
		animation: pulse-green-animation 2s 2;
		/*border: 1px solid #3C763D;*/
	}
	#first-trial-period #text {
		text-align: center;
		font-size: 15.5px;
		line-height: 25px;
		color: #008700;
	}
	#first-trial-period #text #inline-text {
		margin-top: 0.5%;
		display: inline-block;
	}

	@keyframes pulse-green-animation {
		0% {
			box-shadow: 0 0 0 0px rgba(0, 135,0, 1);
		}
		100% {
			box-shadow: 0 0 0 15px rgba(0,135,0, 0);
		}
	}

</style>
<?php
	$group_info = get_full_groups_info_for_student();

	include($root.'/student/lesson/components/use_collected_coins.php');

	$week_day_str = array('', 'Дүйсенбі', 'Сейсенбі', 'Сәрсенбі', 'Бейсенбі', 'Жұма', 'Сенбі', 'Жексенбі');
	$week_day_short_str = array('', 'Дс', 'Сс', 'Ср', 'Бс', 'Жм', 'Сб', 'Жб');
	$hour = date('H');
	if ($hour >= 7) {
		$week_day_id = date('w') == 0 ? 7 : date('w');
	} else {
		$week_day_id = date('w', strtotime('-1 days'));
		$week_day_id = $week_day_id == 0 ? 7 : $week_day_id;
	}
	$access_html = array();
	$reject_html = array();

	$instruction_html = "<div style='text-align: right;'><button class='btn btn-sm btn-default go-to-instruction-page'>Инструкция</button></div>";
	echo $instruction_html;

	$first_trial_period_html = "";
	if (isset($_SESSION['first_register']) && $_SESSION['first_register'] == 1) {
		$first_trial_period_html .= "<center><div id='first-trial-period'>";
			$first_trial_period_html .= "<p id='text'>";
				$first_trial_period_html .= "Altyn Bilim онлайн академиясына қош келдің! Бұл сенің жеке кабинетің. Оны қолдану жайлы <a href='#'>инструкция</a> осы тексттің жоғарғы бөлігінде тұр.<br><span id='inline-text'> Таңдаған пәніңе байланысты cаған алғашқы сабақ <b>ТЕГІН</b> берілді. Осы арқылы сен біздің оқыту жүйемізбен және оқу порталымызбен таныссаң болады. Келесі сабақта оқу төлемін төлеп, оқуды жалғастыра аласың.</span>";
			$first_trial_period_html .= "</p>";
		$first_trial_period_html .= "</div></center>";	
	}
	echo $first_trial_period_html;

	$holiday = get_holiday();
	$holiday_html = '';
	if ($holiday['title'] != '' && date('H') > 7) {
		$holiday_html .= "<center><div id='holiday-box'>";
			$holiday_html .= '<center><p class="holiday-text-style">"<i><b>'.$holiday['title'].'</b></i>" мейрамына байланысты бүгін жаңа тақырып болмайды.<br>';
			$holiday_html .= "Алайда, көрмеген өткен тақырыптарың болса, олар ашық тұрады.</p></center>";
		$holiday_html .= "</div></center>";
	}
	echo $holiday_html;
	if (count($group_info) == 0) {
		$no_lesson_html = "<div class='no-lesson-content'>";
			$no_lesson_html .= "<center>";
				$no_lesson_html .= "<p id='no-lesson-text'>Оқып жатқан пәндерің жоқ. Төмендегі батырманы басып, керек пәнді және тарауды таңда</p>";
				$no_lesson_html .= "<button class='btn btn-md btn-success' id='no-lesson-btn'>Курсқа тіркелу</button>";
			$no_lesson_html .= "</center>";
		$no_lesson_html .= "</div>";
		echo $no_lesson_html;
	}
	foreach ($group_info as $group_id => $value) {
		sort($value['schedule']);

		$html = '';
		$is_access = false;
		$is_weekday = false;
		if (in_array($week_day_id, $value['schedule'])) {
			$is_access = true;
			$is_weekday = true;
		} else if ($value['is_forced_access'] == '1') {
			$is_access = true;
		}
		$has_access = $value['has_access'];
		$is_access = $is_access && $has_access && $value['status'] == 'active';

		$freeze_lesson_html = "";
		if ($value['freeze_lesson_id'] != '') {
			$is_access = false;
			$freeze_lesson_html .= "<p style='color: red;'>Сенің сұрауың бойынша ".$value['freeze_lesson_from_date'].' - '.$value['freeze_lesson_to_date']." аралығында тақырыптар ашылмайды</p>";
		}

		$lesson_week_day_id = 0;
		foreach ($value['schedule'] as $wd_id) {
			if ($wd_id > $week_day_id) {
				$lesson_week_day_id = $wd_id;
				break;
			}
		}

		if ($lesson_week_day_id == 0) {
			$lesson_week_day_id = $value['schedule'][0];
		}

		$html .= "<div data-gi-id='".(($is_access && $value['is_freeze'] == 0) ? $value['group_info_id'] : '')."' class='lessons-box'><div class='row'>";
			$html .= "<div class='col-md-7 col-sm-6 col-xs-12' id='lesson-choose-title'>";
				$text = $value['subject_title'];
				if ($value['lesson_type'] == 'topic') {
					$group_type = "";
					if ($value['is_army_group']) {
						$group_type = " | <b>Армия</b>";
					} else if ($value['is_marathon_group']) {
						$group_type = " | <b>Марафон</b>";
					}
					$text .= ' | '.$value['group_name'].$group_type; //group_name
					if (!$value['has_access']) {
						$text .= ' | <br>'.$value['last_lesson_subtitle'];   //$value['subtopic_title2'];
					}	
				}
				$html .= $is_access && $value['is_freeze'] == 0 ? '<a>'.$text.'</a>' : '<span>'.$text.'</span>'.$freeze_lesson_html;
			$html .= "</div>";
			$html .= "<div class='col-md-5 col-sm-6 col-xs-12'>";
				if ($value['is_freeze'] == 0) {
					if (!$is_weekday && $has_access) {
						if ($is_access) {
							if (strtotime(date('d.m.Y')) > strtotime($value['start_date'])) {
								$html .= "<p class='text-warning' style='font-size: 14px; font-style: italic;'><b>Бүгін кешегі тақырыбың ашылып тұр.<br>Жаңа тақырып ".$week_day_str[$lesson_week_day_id]." ашылады!</b></p>";
							}
						} else {
							$short_week_day_ids = array();
							foreach ($value['schedule'] as $wdi) {
								if (!in_array($week_day_short_str[$wdi], $short_week_day_ids)) {
									array_push($short_week_day_ids, $week_day_short_str[$wdi]);
								}
							}
							$short_week_days = implode(', ', $short_week_day_ids);
							$html .= "<p class='text-warning' style='font-size: 14px; font-style: italic;'><b>Бүгін сабақ жоқ. Келесі сабақ күні: ".$week_day_str[$lesson_week_day_id].".</b><br><b>Cабақ кестесі: ".$short_week_days."</b></p>";
						}
					} else if (!$has_access) {
						if ($value['status'] != 'inactive' && strtotime(date('Y-m-d')) <= strtotime($value['start_date'])) {
							$remine_html = '';
							if ($value['remine_days'] == 1) {
								$remine_html = "ертең (".$value['start_date'].") ".' ашылады';
							} else {
								$remine_html = "(".$value['start_date'].") ".(intval($value['remine_days']) - 1).'  күннен кейін ашылады';
							}
							$html .= "<p class='text-success' style='font-size: 14px; font-weight:bold;'><i>Таңдаған тақырыбың ".$remine_html."</i></p>";
						}
						else if ($holiday['title'] != '' && date('H') > 7 && $value['status'] != 'inactive') {
							$html .= "<i class='text-success'>Бүгін жаңа тақырып жоқ.</i>";
						}
						else if (strtotime($value['start_date']) == strtotime(date('Y-m-d')) && $value['status'] == 'inactive') {
							$html .= "<p class='text-success' style='font-size: 14px; font-weight:bold;'><i>Таңдаған тақырыбың төлем жасағаннан кейін ашылады</i></p>";
						} else if (strtotime($value['start_date']) == strtotime(date('Y-m-d')) && $value['status'] != 'inactive') {
							$html .= "<p class='text-success' style='font-size: 14px; font-weight:bold;'><i>Таңдаған тақырыбың бүгін сағат 7:00 де ашылады</i></p>";
						} else {
							$remine_html = '';
							if ($value['remine_days'] == 1) {
								$remine_html = "ертең (".$value['start_date'].") ".' ашылады';
							} else {
								$remine_html = "(".$value['start_date'].") ".(intval($value['remine_days']) - 1).'  күннен кейін ашылады';
							}
							$html .= "<p class='text-success' style='font-size: 14px; font-weight:bold;'><i>Таңдаған тақырыбың ".$remine_html."</i></p>";
						}
					}
					if ($value['status'] == 'inactive') {
						$html .= "<p><i style='color: red;'>Курстың төлемі төленбеген! Төлемі: ".$value['payment']['sum']."тг.</i></p>";
						$html .= "&nbsp;&nbsp;&nbsp;";
						$html .= "<button class='btn btn-success btn-sm do-payment'>Төлем жасау</button>";
						// $html .= "<button class='btn btn-success btn-sm do-payment' data-toggle='modal' data-target='#do-payment'>Төлем жасау</button>";
					} else if ($value['status'] == 'active') {
						$html .= "<p class='text-success' id='next-payment-text' style='font-size: 14px;'><i>Келесі оқу төлемі: ".$value['access_until_with_extra_payments']."</i></p>";
					}
				} else {
					$html .= "<p class='text-success' style='font-size: 14px;'><i>Соңғы сабақтың тесті орындалмады.<br>Сол үшін бүгінгі күннің жаңа тақырыбы ашылмай тұр</i></p>";
					$html .= "<button class='btn btn-warning btn-md freeze-off' data-group-info-id='".$group_id."'>Тақырыпты <br class='hidden-lg hidden-md hidden-sm'> қайта ашу</button>";
				}
			$html .= "</div>";
		$html .= "</div></div>";

		if ($is_access) {
			array_push($access_html, $html);
		} else {
			array_push($reject_html, $html);
		}
	}

	echo implode(' ', $access_html).' '.implode(' ', $reject_html);

	$student_balances = get_student_balances();
	$html = '';
	if (count($student_balances) > 0) {
		$html .= "<div id='student-balance-box' style='font-size: 14px;'><div class='row'><div class='col-md-4 col-md-offset-8 col-sm-6 col-sm-offset-6'>";
			$html .= "<p id='title' style='margin-bottom: 3px;'>Өткен группалардан артылып қалған күндер:</p>";
			$html .= "<ul id='group-days' style='margin-bottom: 3px;'>";
				$total_days = 0;
				foreach ($student_balances as $value) {
					$total_days += $value['days'];
					$html .= "<li>".$value['group_name'].": <i>".$value['days'].' күн'.($value['comment'] != '' ? " (".$value['comment'].")" : '')."</i></li>";
				}
				$html .= "<li>Барлығы: <i>".$total_days." күн</i></li>";
			$html .= "</ul>";
			$html .= "<i id='payment-message'>Келесі төлемде автоматты түрде ескеріледі!</i>";
		$html .= "</div></div></div>";
	}

	echo $html;
?>