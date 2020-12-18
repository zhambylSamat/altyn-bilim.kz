<!DOCTYPE html>
<html>
<head>
	<?php include_once('common/assets/meta.php');?>
	<title>Онлайн Академия. Altyn Bilim</title>
	<?php include_once('common/assets/style.php');?>
	<?php include_once('common/connection.php'); ?>
	<link rel="stylesheet" type="text/less" href="lending_style/style.less?v=0.4.1">
	<link rel="stylesheet" type="text/css" href="swiper_slider/css/swiper-boundle.min.css">
	<script type="text/javascript" src='swiper_slider/js/swiper-boundle.min.js'></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
</head>
<body>

	<?php
		$date1=date_create("2020-08-01");
		$date2=date_create(date('Y-m-d'));
		$diff=date_diff($date1,$date2);
		$add_days = $diff->format("%a");
		if (isset($_SESSION['password_reset'])) {
			if ($_SESSION['password_reset'] == 1) {
				header("Location:reset-password.php");
			} 
			else if ($_SESSION['password_reset'] == -1) {
				unset($_SESSION);
			}
		}
	?>
	<?php include_once('common/assets/js.php'); ?>
	<div class='container-fluid'>
		<div class='row' style='display: <?php echo isset($_GET['r_done']) ? 'block' : 'none';?>'>
			<br>
			<div class="alert alert-success alert-dismissible" style='position: absolute; width: 100%; z-index: 100;' role="alert">
			  <center>
			  	<strong>Құттықтаймыз! Тіркелу сәтті аяқталды. Оқу төлемін өзіңнің жеке кабинетінен жаса.</strong>
			  </center>
			</div>
		</div>
	</div>
	<?php
		if (isset($_SESSION['user'])) {
			if ($_SESSION['user'] == $ADMIN || $_SESSION['user'] == $MODERATOR) {
				header('Location:staff');
			} else if ($_SESSION['user'] == $STUDENT && $_SESSION['password_reset'] == 0) {
				header('Location:student/index.php');
			}
		}
	?>


<div id='vimeo-video'>
	<div id='vimeo-content'></div>
</div>

<div id='section-1'>
	<div class='container-fluid'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div id='black-sheet-cover'>
				<div id='live-students'>
					<img id='live-students-img' src="lending_img/live_dot.gif">
					<span id='live-students-text'><span id='live-students-count'><?php echo 1237+$add_days; ?></span> оқушы курстан өтті</span>
					<div id='log-in-content-mobile' class='hidden-lg hidden-md hidden-sm pull-right'>
						<div id='log-in' title='Порталға кіру' data-toggle='modal' data-target='#login-modal'>
							<center><p>Жеке кабинетке</p><p>КІРУ</p></center>
							<!-- <img src="lending_img/log_in.png"> -->
						</div>
					</div>
				</div>
				<div id='welcome-content'>
					<!-- <span id='head-subtitle'>ҚАЗАҚСТАНДАҒЫ ЕҢ ҮЗДІК</span> -->
					<!-- <br> -->
					<p id='head-subtitle-2'>Өзіңе керек <span class='head-subtitle-2-yellow'>кез келген тақырыптан</span> бастап, өзіңе ыңғайлы <span class='head-subtitle-2-yellow'>кез келген уақытта</span> <span class='head-subtitle-2-blue'>ең арзан бағамен</span> ҰБТ-ға дайындал</p>
					<p id='head-title'>ALTYN BILIM ОНЛАЙН АКАДЕМИЯСЫ</p>
					<!-- <span id='head-offer'></span> -->
					<center>
						<table class='table'>
							<tr>
								<td class='subjects subject-box-btn' data-id='16' data-type='topic' data-title='Математика' data-toggle='modal' data-target='#topics-modal' style='cursor:pointer;'>
									<center>Математика</center>
								</td>
								<td class='subjects subject-box-btn' data-id='21' data-type='topic' data-title='Физика' data-toggle='modal' data-target='#topics-modal' style='cursor:pointer;'>
									<center>Физика</center>
								</td>
								<td class='subjects subject-box-btn' data-id='124' data-type='subtopic' data-title='Матсауаттылық' data-toggle='modal' data-target='#topics-modal' style='cursor:pointer;'>
									<center>Матсауаттылық</center>
								</td>
							</tr>
						</table>
					</center>
				</div>
			</div>
		</div>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div id='log-in-content' class='hidden-xs'>
				<div id='log-in' title='Порталға кіру' class='pull-right' data-toggle='modal' data-target='#login-modal'>
					<span style='display: inline-block;'><center>Жеке кабинетке <br><span>КІРУ</span></center></span>
					<img src="lending_img/log_in.png">
				</div>
			</div>
			<center>
				<table id='top-advantages'>
					<tr>
						<td class='dot-list'><img src="lending_img/dot_yellow.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-yellow'>Арнайы дайындалған платформа</span></td>

						<td class='dot-list'><img src="lending_img/dot_blue.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-blue'>Авторлық методика | сапалы оқу материалдары</span></td>
					</tr>
					<tr>
						<td class='dot-list'><img src="lending_img/dot_blue.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-blue'>Индивидуалды оқу жоспары </span></td>

						<td class='dot-list'><img src="lending_img/dot_yellow.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-yellow'>Білікті мұғалімдер </span></td>
					</tr>
					<tr>
						<td class='dot-list'><img src="lending_img/dot_yellow.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-yellow'>SMS хабарламалар</span></td>

						<td class='dot-list'><img src="lending_img/dot_blue.png"></td>
						<td class='head-list-content'><span class='head-list-text head-list-text-blue'>100% кепілдік</span></td>
					</tr>
					<tr>
						<td class='head-list-btn' colspan='2'>
							<center>
								<button onclick="window.location.href='registration.php'" class='btn-block btn-yellow pulse-yellow'>КУРСҚА ТІРКЕЛУ</button>
								<button title='Порталға кіру' data-toggle='modal' data-target='#login-modal' style='margin-top: 5%;' class='btn-block btn-login'>Жеке кабинетке кіру</button>
							</center>
						</td>
						<td class='head-list-btn' colspan='2'>
							<center>
								<button class='btn-block btn-red-border' id='welcome-video-btn' data-url='https://vimeo.com/488388323'>
									<table>
										<tr>
											<td id='play-btn-icon'><span class='glyphicon glyphicon-play-circle'></span></td>
											<td id='play-btn-text'>Курс туралы видео ақпарат 6 минутта</td>
										</tr>
									</table>
								</button>
							</center>
						</td>
					</tr>
				</table>
			</center>
		</div>
	</div>
</div>

<div id='section-2'>
	<div class='container-fluid'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<p id='section-2-title'>Біз қалай оқытамыз? </p>
					<p id='section-2-subtitle'>Бар болғаны 3 қадам: </p>
				</center>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<div id='lesson-step-btn-group'>
						<button id='lesson-step-btn-left' class='lesson-step-btn-focus' data-step='1'>Қадам 1</button><button id='lesson-step-btn-middle' data-step='2'>Қадам 2</button><button id='lesson-step-btn-right' data-step='3'>Қадам 3</button>
					</div>
				</center>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div id='step-1' class='lesson-step'>
					<div class='row'>
						<div class='col-md-5 col-sm-4 hidden-xs'>
							<center><img src="lending_img/mac_boo_lesson.png" class='step-img'></center>
						</div>
						<div class='col-md-7 col-sm-8 col-xs-12'>
							<p class='step-title'>Тақырыпты көріп, конспектіле</p>
							<p class='step-subtitle'>
								&nbsp;&nbsp;Сабақтың басында жаңа тақырып сапалы, әрі жеңіл тілмен түсіндіріледі. Түсіндіру барысында тақырып 100% толық қамтылады және мысал есептер талданады. Осы кезде оқушы мұғалімнің айтуымен конспектілеп отырғаны дұрыс.
							</p>
						</div>
					</div>
				</div>

				<div id='step-2' class='lesson-step'>
					<div class='row'>
						<div class='col-md-5 col-sm-4 hidden-xs'>
							<center><img src="lending_img/phone_solve.png" class='step-img'></center>
						</div>
						<div class='col-md-7 col-sm-8 col-xs-12'>
							<p class='step-title'>Есептерін шығарып, шығару жолдарын видеодан қара</p>
							<p class='step-subtitle'>
								&nbsp;&nbsp;Сабақтың түсіндірілуін көріп, конспектілеп біткен соң оқушыға сол тақырыпқа байланысты есептер беріледі.
								Шығару барысында оқушы өзін тексеріп отыру үшін есептердің жауаптары болады. Егер қандай да бір есеп шықпай жатса, оқушы оның шығару жолын видеодан қарай алады.
							</p>
						</div>
					</div>
				</div>

				<div id='step-3' class='lesson-step'>
					<div class='row'>
						<div class='col-md-5 col-sm-4 hidden-xs'>
							<center><img src="lending_img/mac_book_solve.png" class='step-img'></center>
						</div>
						<div class='col-md-7 col-sm-8 col-xs-12'>
							<p class='step-title'>Тест есептерімен жұмыс </p>
							<p class='step-subtitle'>
								&nbsp;&nbsp;Сабақтың аяғында өтілген тақырыпқа байланысты ҰБТ базасынан тест есептері беріледі. Осы арқылы оқушы сабақты қалай меңгергенін көріп, тақырыпты толық қамтиды. Тест соңында шықпай қалған есептері болса, олардың жауаптары мен шешу жолдары көрсетіледі.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='section-3'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-8 col-md-offset-2 col-sm-12 col-xs-12'>
				<center><p id='section-3-title'>Ең білікті және ҰБТ-да тәжірибесі мол мұғалімдерден білім аласың</p></center>
			</div>
			<div class='col-md-4 col-sm-4 col-xs-6'>
				<div class='teachers'>
					<div class='teacher-portret'>
						<center><img src="lending_img/almat.jpg"></center>
					</div>
					<div>
						<center><span class='teacher-info'>АЛМАТ Мырзабек <br><span class='teacher-subinfo'>Жоба жетекшісі, Мат.сауаттылық</span></span></center>
					</div>
					<center><button class='teacher-welcome' data-url='https://vimeo.com/449537050'>ТАНЫСУ</button></center>
				</div>
			</div>
			<div class='col-md-4 col-sm-4 col-xs-6'>
				<div class='teachers'>
					<div class='teacher-portret'>
						<center><img src="lending_img/olzhas.jpg"></center>
					</div>
					<div>
						<center><span class='teacher-info'>ОЛЖАС Нұрдәулет <br><span class='teacher-subinfo'>Математика, Мат.сауаттылық</span></span></center>
					</div>
					<center><button class='teacher-welcome' data-url='https://vimeo.com/449537183'>ТАНЫСУ</button></center>
				</div>
			</div>
			<div class='col-md-4 col-md-offset-0 col-sm-4 col-sm-offset-0 col-xs-6 col-xs-offset-3'>
				<div class='teachers'>
					<div class='teacher-portret'>
						<center><img src="lending_img/belgi.jpg"></center>
					</div>
					<div>
						<center><span class='teacher-info'>БЕЛГІ Рахат <br><span class='teacher-subinfo'>Физика</span></span></center>
					</div>
					<center><button class='teacher-welcome' data-url='https://vimeo.com/449536815'>ТАНЫСУ</button></center>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='section-4'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<p id='section-4-title'>
					<span class='section-4-title-blue'>ALTYN BILIM Онлайн Академиясында</span> оқу басқа мектептермен салыстырғанда <span class='section-4-title-yellow'>6 есе,</span> ал репетитормен айналысқаннан <span class='section-4-title-yellow'>10 есе арзан</span> және:
				</p>
			</div>
			<div class='col-md-4 col-sm-4 hidden-xs'>
				<div id='section-4-target'>
					<center><p id='section-4-price-subtitle' style='margin: 0;'>Әр пәнге</p></center>
					<center><p id='section-4-price-title'>2 990тг</p></center>
					<!-- <center><p id='section-4-price-subtitle'>*алғашқы аптасы ТЕГІН </p></center> -->
					<center><button onclick="window.location.href='registration.php'" id='section-4-register-btn' class='pulse-yellow'>ТІРКЕЛУ </button></center>
				</div>
			</div>
			<div class='col-md-8 col-sm-8 col-xs-12'>
				<div id='section-4-list-box'>
					<table>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Өзіңе керек  <span class='section-4-text-yellow'>кез келген</span> тақырыптан бастасаң болады</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Топқа тәуелсіз, <span class='section-4-text-blue'>индивидуалды оқу жоспарың</span> болады </td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Сабақты өзіңе ыңғайлы <span class='section-4-text-yellow'>кез келген уақытта</span> оқысаң болады</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Әр тақырып <span class='section-4-text-blue'>100%</span> қамтылады</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'><span class='section-4-text-yellow'>Сапалы</span> оқу материалдармен оқисың</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Тақырыпты <span class='section-4-text-blue'>фундаменталды</span> меңгересің, жаттанды емес</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'><span class='section-4-text-yellow'>SMS хабарландырулар</span> арқылы сенімен байланыста боламыз</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Мотивациялық видеолармен сені <span class='section-4-text-blue'>жігерлендіреміз</span></td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'>Оқу барысында <span class='section-4-text-yellow'>бонустар жинап</span>, айдың аяғында оларды <span class='section-4-text-yellow'>жеңілдіктерге</span> айырбастайсың</td>
						</tr>
						<tr class='section-4-list'>
							<td class='section-4-thick'><img src="lending_img/tick.png"></td>
							<td class='section-4-text'><span class='section-4-text-blue'>ҰБТ базасының</span> тест сұрақтарымен жұмыс жасайсың</td>
						</tr>
					</table>
				</div>
			</div>
			<div class='hidden-lg hidden-md hidden-sm col-xs-12'>
				<div id='section-4-target-mobile'>
					<center><p id='section-4-price-subtitle' style='margin: 0;'>Әр пәнге</p></center>
					<center><p id='section-4-price-title-mobile'>2 990тг</p></center>
					<!-- <center><p id='section-4-price-subtitle-mobile'>*алғашқы сабақ ТЕГІН </p></center> -->
					<center><button onclick="window.location.href='registration.php'" id='section-4-register-btn-mobile' class='pulse-orange'>ТІРКЕЛУ </button></center>
					<!-- <center><button onclick="window.location.href='registration.php'" id='section-4-register-btn-mobile' class='pulse-orange'>ТІРКЕЛУ </button></center> -->
				</div>
			</div>
		</div>
	</div>
</div>

<div id='section-counter'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12 section-counter-header'>
				Біздің статистика
				<hr>
			</div>
			<div class='col-md-3 col-sm-3 col-xs-6 section-counter-box'>
				<p class='count' id='count-2'>0 %</p>
				<p class='section-counter-description'>түлектеріміз мемлекеттік грант иелері атанды.</p>
				<br>
			</div>
			<div class='col-md-3 col-sm-3 col-xs-6 section-counter-box'>
				<p class='count' id='count-1'>0</p>
				<p class='section-counter-description'>оқушы біздің орталықта оқудан өтті.</p>
				<br>
			</div>
			<div class='col-md-3 col-sm-3 col-xs-6 section-counter-box'>
				<p class='count' id='count-3'>0 жыл</p>
				<p class='section-counter-description'><b>Altyn Bilim</b> оқу орталығы білім нарығында.</p>
				<br>
			</div>
			<div class='col-md-3 col-sm-3 col-xs-6 section-counter-box'>
				<p class='count' id='count-4'>0</p>
				<p class='section-counter-description'><b>"Алтын белгі"</b> үміткерлері ҰБТ-да қорғап шықты.</p>
				<br>
			</div>
		</div>
	</div>
</div>

<?php
$student_progress = array(
	// array('img_link'=>'abilzhan_zere-min.jpg',          'fio'=>'Әбілжан Зере',         'subject'=>'Физика',         'min'=>'25', 'max'=>'35'),
	// array('img_link'=>'agadil_damir-min.jpg',           'fio'=>'Агадил Дамир',         'subject'=>'Физика',         'min'=>'15', 'max'=>'32'),
	array('img_link'=>'arinova_aru-min.jpg',            'fio'=>'Аринова Ару',          'subject'=>'Мат.сауаттылық', 'min'=>'15', 'max'=>'18'),
	array('img_link'=>'aldabergen_aruzhan-min.jpg',     'fio'=>'Алдаберген Аружан',    'subject'=>'Физика',         'min'=>'14', 'max'=>'35'),
	// array('img_link'=>'baichapanova_roza-min.jpg',      'fio'=>'Байчапанова Роза',     'subject'=>'Физика',         'min'=>'19', 'max'=>'33'),
	array('img_link'=>'daurenbek_akerke-min.jpg',       'fio'=>'Дауренбек Ақерке',     'subject'=>'Физика',         'min'=>'13', 'max'=>'30'),
	// array('img_link'=>'baltabek_anar-min.jpg',          'fio'=>'Балтабек Анар',        'subject'=>'Математика',     'min'=>'22', 'max'=>'33'),
	array('img_link'=>'bolisova_amina-min.jpg',         'fio'=>'Болысова Амина',       'subject'=>'Математика',     'min'=>'20', 'max'=>'39'),
	array('img_link'=>'dauletbekuli_eldos-min.jpg',     'fio'=>'Даулетбекұлы Елдос',   'subject'=>'Математика',     'min'=>'5',  'max'=>'36'),
	array('img_link'=>'daurenbek_akerke-min.jpg',       'fio'=>'Дауренбек Ақерке',     'subject'=>'Математика',     'min'=>'13', 'max'=>'36'),
	// array('img_link'=>'dosaieva_aruzhan-min.jpg',       'fio'=>'Досаева Аружан',       'subject'=>'Математика',     'min'=>'23', 'max'=>'37'),
	array('img_link'=>'bolisova_amina-min.jpg',         'fio'=>'Болысова Амина',       'subject'=>'Физика',         'min'=>'16', 'max'=>'32'),
	array('img_link'=>'dosaieva_aruzhan-min.jpg',       'fio'=>'Досаева Аружан',       'subject'=>'Физика',         'min'=>'22', 'max'=>'32'),
	array('img_link'=>'ergali_dana-min.jpg',            'fio'=>'Ерғали Дана',          'subject'=>'Математика',     'min'=>'11', 'max'=>'40'),
	// array('img_link'=>'arinova_aru-min.jpg',            'fio'=>'Аринова Ару',          'subject'=>'Физика',         'min'=>'5',  'max'=>'33'),
	array('img_link'=>'baichapanova_roza-min.jpg',      'fio'=>'Байчапанова Роза',     'subject'=>'Математика',     'min'=>'28', 'max'=>'34'),
	array('img_link'=>'erkinbek_gaziza-min.jpg',        'fio'=>'Еркінбек Ғазиза',      'subject'=>'Химия',          'min'=>'18', 'max'=>'35'),
	// array('img_link'=>'arinova_aru-min.jpg',            'fio'=>'Аринова Ару',          'subject'=>'Математика',     'min'=>'27', 'max'=>'35'),
	array('img_link'=>'ermokova_dana-min.jpg',          'fio'=>'Ермекова Дана',        'subject'=>'Физика',         'min'=>'17', 'max'=>'32'),
	// array('img_link'=>'kosaidarova_alua-min.jpg',       'fio'=>'Қосайдарова Алуа',     'subject'=>'Математика',     'min'=>'27', 'max'=>'39'),
	array('img_link'=>'kulzhabai_anelia-min.jpg',       'fio'=>'Құлжабай Анеля',       'subject'=>'Химия',          'min'=>'5',  'max'=>'37'),
	array('img_link'=>'ergali_dana-min.jpg',            'fio'=>'Ерғали Дана',          'subject'=>'Физика',         'min'=>'14', 'max'=>'39'),
	array('img_link'=>'mukan_erkebulan-min.jpg',        'fio'=>'Мұқан Еркебұлан',      'subject'=>'Физика',         'min'=>'5',  'max'=>'34'),
	// array('img_link'=>'parimbai_erzhan-min.jpg',        'fio'=>'Пәрімбай Ержан',       'subject'=>'Математика',     'min'=>'24', 'max'=>'35'),
	array('img_link'=>'raiymbekov_daniar-min.jpg',      'fio'=>'Райымбеков Данияр',    'subject'=>'Математика',     'min'=>'15', 'max'=>'37'),
	// array('img_link'=>'kydyrova_ainur-min.jpg',         'fio'=>'Кадырова Айнур',       'subject'=>'Физика',         'min'=>'18', 'max'=>'32'),
	array('img_link'=>'sagidrakhmanov_akezhan-min.jpg', 'fio'=>'Сагидрахманов Әкежан', 'subject'=>'Математика',     'min'=>'13', 'max'=>'36'),
	array('img_link'=>'dauletbekuli_eldos-min.jpg',     'fio'=>'Даулетбекұлы Елдос',   'subject'=>'Физика',         'min'=>'5',  'max'=>'38'),
	array('img_link'=>'serikkizi_aidana-min.jpg',       'fio'=>'Серікқызы Айдана',     'subject'=>'Математика',     'min'=>'18', 'max'=>'40'),
	array('img_link'=>'raiymbekov_daniar-min.jpg',      'fio'=>'Райымбеков Данияр',    'subject'=>'Физика',         'min'=>'18', 'max'=>'39'),
	// array('img_link'=>'turlybekova_alua-min.jpg',       'fio'=>'Турлыбекова Алуа',     'subject'=>'Физика',         'min'=>'12', 'max'=>'31'),
	array('img_link'=>'serikkizi_aidana-min.jpg',       'fio'=>'Серікқызы Айдана',     'subject'=>'Мат.сауаттылық', 'min'=>'11', 'max'=>'20'),
	// array('img_link'=>'zhakanbaieva_aiana-min.jpg',     'fio'=>'Жақанбаева Айана',     'subject'=>'Мат.сауаттылық', 'min'=>'9',  'max'=>'18')
);
?>

<div id='section-6'>
	<center>
		<p id='section-6-title'>
			Біздің оқушыларымыздың жетістіктері
		</p>
	</center>
	<?php
		$html = "<div class='swiper-container swiper-progress'><div class='swiper-wrapper'>";
		foreach ($student_progress as $value) {
			$html .= "<div class='swiper-slide'>";
				$html .= "<div class='section-6-content'>";
					$html .= "<table><tr>";
						$html .= "<td class='section-6-portret'>";
							$html .= "<div class='section-6-portret-container'>";
								$html .= "<img src='lending_img/progress_certificates_min/".$value['img_link']."' class='img-response swiper-lazy' />";
							$html .= "</div>";
						$html .= "</td>";

						$html .= "<td class='section-6-progress-info'>";
							$html .= "<p class='section-6-progress-info-fio'>".$value['fio']."<p>";
							$html .= "<p class='section-6-progress-info-subject'>".$value['subject']."</p>";
							$html .= "<div class='section-6-progress-info-marks'>";
								$html .= "<p class='section-6-progress-info-mark-min'>Алғашқы тесттен: <span class='min-val'>".$value['min']."</span></p>";
								$html .= "<p class='section-6-progress-info-mark-max'>ҰБТ-да: <span class='max-val'>".$value['max']."</span></p>";
							$html .= "</div>";
						$html .= "</td>";
					$html .= "</tr></table>";
				$html .= "</div>";
			$html .= "</div>";
		}
		$html .= "</div></div>";

		echo $html;
	?>
</div>

<?php
	$review_images = array('19-min.jpeg', 
							// '1-min.jpg',
							// '2-min.jpg',
							// '3-min.jpg',
							// '4-min.jpg',
							'5-min.jpg',
							// '6-min.jpg',
							// '7-min.png',
							'9-min.jpg',
							// '10-min.jpg',
							'11-min.png',
							'12-min.jpg',
							'13-min.jpg',
							'14-min.png',
							'15-min.jpg',
							'16-min.jpg',
							'17-min.jpeg',
							'18-min.jpeg', 
							'20-min.jpeg',
							'21-min.jpeg');
?>

<div id='section-7'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div id='section-7-title'>
					Оқушыларымыздың пікірлері
				</div>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<div id='section-7-phone'>
						<div class='swiper-container swiper-reviews'>
							<div class='swiper-wrapper'>
								<?php
									$html = "";
									foreach ($review_images as $link) {
										$html .= "<div class='swiper-slide'>";
											$html .= "<div class='section-7-img'>";
												$html .= "<img class='img-response' src='lending_img/reviews_min/".$link."'>";
											$html .= "</div>";
										$html .= "</div>";
									}
									echo $html;
								?>
							</div>
						</div>
						<div class="swiper-pagination"></div>
					    <!-- Add Arrows -->
					    <div class="swiper-button-next"></div>
					    <div class="swiper-button-prev"></div>
					</div>
				</center>
			</div>
		</div>
	</div>
</div>


<div id='section-9'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-6 col-sm-6 col-xs-12'>
				<center>
					<p id='section-9-title'>
						Оқу барысында монеталар жинап, айдың аяғында жеңілдіктерге % айырбастасаң болады
						<!-- <br>
						<span id='section-9-subtitle'>90%-ға дейінгі жеңілдік қарастырылған</span> -->
					</p>
				</center>
			</div>
		</div>
	</div>
</div>

<div id='section-5'>
	<div class='container'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<center><p id='section-5-title'>Жиі қойылатын сұрақтар</p></center>
		</div>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div class='faq-topics faq-top faq-open'>
				<p class='faq-title'>Оқытуымыз жайлы <span class='glyphicon glyphicon-menu-down'></span></p>
				<div class='faq-content'>
					<div class='parent-faq' data-num='1'>
						<div class='parent-faq-question'>
							<span>Оффлайн оқу жақсы еді. Онлайн арқылы оқи аламын ба?</span>
						</div>
						<div class='parent-faq-answer' style='display: none;'>
							<p class='indent'></p>Мұғалімнің баламен бірге отырып, сабақ қарағанына, әрине, не жетсін? Алайда, қазіргі пандемияның жағдайында баланың жолда жүруі, сыныпта басқа балалармен отыруы, көпшілік жерлерде болуы денсаулықты орынсыз тәуекелге соқтырады. Оған қоса, қазіргі заманның беріп отырған мүмкіндігі оқушыға интернет арқылы шексіз, әрі сапалы білімге жол ашып отыр. Бұл керемет мүмкіндікті қолдану керек.
							<br>
							<p class='indent'></p>Қазіргі біздің мақсатымыз – біздің оффлайн оқытуда берген білім сапасын онлайнға алып келу. Бұған қоса, интернеттің мүмкіндіктерін пайдалана отырып, бұл сапаны әлдеқайда арттыру. Біздің қазір беріп отырған оқыту әдістемеміз оқушының сабақты максималды меңгеруіне бағытталған, ал әлеуметтік желідегі жұмыстарымыз баланың оқуға деген қызушылығы мен мотивациясының артуына септігін тигізеді.
						</div>
					</div>
					<div class='parent-faq' data-num='2'>
						<div class='parent-faq-question'>
							<span>Онлайнда сабақты жақсы түсіне аламын ба?</span>
						</div>
						<div class='parent-faq-answer' style='display: none;'>
							<p class='indent'></p>Иә, түсіне аласыз. Себебі біздің ең басты мақсатымыздың бірі – оқушы өтілген тақырыпты максималды меңгеруі. Бұл үшін біз әр тақырыптың материалын 100% түсінікті, әрі оқушыға толық жеткізетіндей етіп жасадық.
							<br>
							<p class='indent'></p>Әр сабақта жаңа тақырыптың түсіндірілуі мен мысал есептермен жұмыс болады. Содан кейін оқушыға өзіндік жұмысқа есептер және өзін тексеріп отыруы үшін олардың жауаптары беріледі. Егер кейбір есептері шықпай жатса, төменгі бөлікте әр есептің шығарылу жолы бар видеосы берілген. Таймкод арқылы кез келген шықпай жатқан есебінің толықтай шығару жолын мұғалімнің түсіндіруімен көре алады.
							<br>
							<p class='indent'></p>Бұған қоса, біз оқушылармен үнемі тікелей эфирге шығып тұрамыз. Сол жерде оқушы түсінбеген сұрақтарын мұғалімге қойса болады.
							<br>
							<p class='indent'></p>Тақырыпты меңгеріп болған соң, өтілген тақырыпқа байланысты тест есептері беріледі. Бұл арқылы оқушы өзінің тақырыпты қаншалықты меңгергенін көре алады және тест есептерімен жұмыс жасауды үйренеді.
						</div>
					</div>
					<!-- <div class='parent-faq' data-num='3'>
						<div class='parent-faq-question'>
							<span>Сабақ бойынша сұрақтарым болып жатса, оны қалай сұраймын?</span>
						</div>
						<div class='parent-faq-answer' style='display: none;'>
							<p class='indent'></p>Біз оқушыларымызға арнап Telegram қосымшасында жабық группа құрдық. Сол жерде сіздермен тікелей байланыста боламыз.
						</div>
					</div> -->
					<div class='parent-faq' data-num='4'>
						<div class='parent-faq-question'>
							<span>Телефон арқылы оқысам болады ма? Әлде ноутбукпен кіру керек па?</span>
						</div>
						<div class='parent-faq-answer' style='display: none;'>
							<p class='indent'></p>Сіз телефон арқылы да, ноутбукпен де оқи аласыз. Біз оқу порталымызды максималды бейімді етіп жасадық.
						</div>
					</div>
					<div class='parent-faq' data-num='5'>
						<div class='parent-faq-question'>
							<span>Бірнеше пәнге қатыссам бола ма?</span>
						</div>
						<div class='parent-faq-answer' style='display: none;'>
							<p class='indent'></p>Иә, болады. Сіз тек бір пәнге немесе бірден бірнеше пәнге қатыссаңыз болады.
						</div>
					</div>
					<div class='parent-faq' data-num='6'>
						<div class='parent-faq-question'>
							<span>ҰБТ-ның жаңартылған базасымен оқытасыздар ма?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Жыл сайын ҰБТ-дан кейін біз жаңадан келген сұрақтарды талдап, оқыту әдістемемізге сәйкес өзгерістер енгізіп отырамыз. Оқу жылы барысында жаңа сұрақтар келіп жатса, оларды тікелей эфирлерде және тестпен жұмыстарда талдап отырамыз.
						</div>
					</div>
					<div class='parent-faq' data-num='7'>
						<div class='parent-faq-question'>
							<span>Сабақ жоспарын қайдан көре аламын?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Осы сайттың жоғарғы бөлігінде «Математика», «Физика», «Матсауаттылық» сөздерін бассаңыз, оқу жоспары көрінеді.
						</div>
					</div>
					<div class='parent-faq' data-num='8'>
						<div class='parent-faq-question'>
							<span>Курсты өткен соң ҰБТ-да жақсы ұпай жинай аламын ба?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Иә, жинай аласыз. Егер сіз біздің берген сабақтарды тыңдап, конспектілеп, берген есептерімізді шығарып, айтқан кеңестерімізді орындап жүрсеңіз, ҰБТ-да міндетті түрде жоғары балл жинайсыз.
						</div>
					</div>
				</div>
			</div>



			<div class='faq-topics faq-middle'>
				<p class='faq-title'>Төлем <span class='glyphicon glyphicon-menu-down'></span></p>
				<div class='faq-content' style='display: none'>
					<div class='parent-faq' data-num='9'>
						<div class='parent-faq-question'>
							<span>2 990 тг бір пәнге ма?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Иә, 2 990 тг – бір пәннің бір айға төлемі. Оқу барысында төлемді ай сайын жасап тұрсаңыз болады немесе бірден бірнеше айға төлесеңіз болады.
						</div>
					</div>
					<div class='parent-faq' data-num='9'>
						<div class='parent-faq-question'>
							<span>Қалай төлем жасаймын?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Курсымызға тіркелген соң жеке кабинетіңде "Төлем жасау" батырмасы болады. Соны басқаннан кейін мәліметтерді толтырып, банк карточкасы арқылы төлемді жасайсың. Төлем жасау процесі 100% қауіпсіз
						</div>
					</div>
					<div class='parent-faq' data-num='10'>
						<div class='parent-faq-question'>
							<span>Қандай жеңілдіктер бар?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Біздің курсқа досыңды шақырсаң, саған бір айға 20% жеңілдік беріледі. Екі досыңды шақырсаң, 40% жеңілдік беріледі, т.с.с. Демек, 5 досың сен арқылы тіркелсе, толық 1 ай тегін оқисың.
							<br>
							<p class='indent'></p>Достарыңды шақыру үшін жеке кабинетіңдегі промокодты досыңа жіберу керек. Жеке кабинет курсқа тіркелгеніңнен кейін ашылады
						</div>
					</div>
				</div>
			</div>

			<div class='faq-topics faq-bottom'>
				<p class='faq-title'>Оқу порталы бойынша <span class='glyphicon glyphicon-menu-down'></span></p>
				<div class='faq-content' style='display: none'>
					<div class='parent-faq' data-num='11'>
						<div class='parent-faq-question'>
							<span>Жеке кабинетке қалай кіремін?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Осы сайттың ең жоғарғы бөлігінде «Жеке кабинетке кіру» батырмасы арқылы жеке кабинетіңізге кіре аласыз.
						</div>
					</div>
					<div class='parent-faq' data-num='12'>
						<div class='parent-faq-question'>
							<span>Тіркелу кезінде не үшін телефон номерімізді енгіземіз?</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<p class='indent'></p>Курсымызға тіркелген соң осы номеріңе саған көмек ретінде оқу бойынша SMS хабарландырулар жіберіліп тұрады.
						</div>
					</div>
					<div class='parent-faq' data-num='13'>
						<div class='parent-faq-question'>
							<span>Бұдан басқа да сұрақтарыңыз болса, осы ватсап номерімізге жазыңыз.</span>
						</div>
						<div class='parent-faq-answer' style='display: none'>
							<a target="_blank" href="https://wa.me/77773890099?text=Саламатсыз%20ба.%20Altyn%20Bilim%20Онлайн%20академиясы%20бойынша%20сұрайын%20деп%20едім.">+7 777 389 00 99</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-md-6 col-sm-6 hidden-xs'>
			<div class='parent-faq-appropriate-img' data-num='1'>
				<center><img src="lending_img/onlineedu_square_info.png"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='2' style='display: none;'>
				<center><img src="lending_img/olzhas_laptop.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='3' style='display: none;'>
				<center><img src="lending_img/faq.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='4' style='display: none;'>
				<center><img src="lending_img/belgi_laptop.jpg"></center>
			</div>

			<div class='parent-faq-appropriate-img' data-num='5' style='display: none;'>
				<center><img src="lending_img/subjects.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='6' style='display: none;'>
				<center><img src="lending_img/ubt2020.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='7' style='display: none;'>
				<center><img src="lending_img/topics.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='8' style='display: none;'>
				<center><img src="lending_img/140.png"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='9' style='display: none;'>
				<center><img src="lending_img/price_v_2.png"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='10' style='display: none;'>
				<center><img src="lending_img/invite_friends_v_1.png"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='11' style='display: none;'>
				<center><img src="lending_img/login.jpg"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='12' style='display: none;'>
				<center><img src="lending_img/whatsapp-sms.png"></center>
			</div>
			<div class='parent-faq-appropriate-img' data-num='13' style='display: none;'>
				<center><img src="lending_img/whatsapp1.png"></center>
			</div>
		</div>
	</div>
</div>

<!-- <div id='section-welcome-video'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<div id='welcome-vimeo-video' data-url='https://vimeo.com/443894525'> 
 -->						<!-- /443894525 -->
						<!-- /448470242 -->
<!-- 					</div>
			</div>
		</div>
	</div>
</div> -->

<div id='section-12'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<img src="lending_img/before.png" id='section-12-before'>
				</center>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12' id='section-12-content'>
				<p id='section-12-title'>Сонымен, Болашақ Түлек, <br>егер сен</p>
				<p class='section-12-list'>- Сапалы білімге қол жеткізгің келсе</p>
				<p class='section-12-list'>- Тез арада жоғары көрсеткішті қаласаң</p>
				<p class='section-12-list'>- Бағасы тиімді оқу курсын іздесең</p>
				<p id='section-12-subtitle'>онда</p>
				<center><button onclick="window.location.href='registration.php'" id='section-12-btn'>Біздің курсқа тіркел</button></center>
				<!-- <center><p id='section-12-free'>* курстың алғашқы сабағы тегін</p></center> -->
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<img src="lending_img/after.png" id='section-12-after'>
				</center>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="topics-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel"><span class='title'></span></h4>
      		</div>
      		<div class="modal-body">
        		
      		</div>
    	</div>
	</div>
</div>

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Порталға кіру</h4>
				<center><p id='login-error' style='color: red; display: none;'></p></center>
			</div>
			<div class="modal-body">
				<?php
					$get_sign_in = true;
					include_once('log_in.php');
				?>
			</div>
		</div>
	</div>
</div>

<?php
	include_once('reset_sms_password.php');
	include_once('reset_password_code.php');
?>

<!-- <div id='section-whatsapp'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-8 col-sm-8 col-xs-10'>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
				quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
				cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
				proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			</div>
			<div class='col-md-4 col-sm-4 col-xs-2' id='whatsapp-content'>
				<div id="whatsapp" class='pull-right'>
					<a target="_blank" href="https://wa.me/77773890099?text=Сәлеметсіз бе. Мен ҰБТ онлайн академясы жайлы хабарласып тұрмын.">
						<img src="lending_img/whatsapp.png">
					</a>
				</div>
			</div>
		</div>
	</div>
</div> -->

<?php
	include_once('footer.html');
?>
<script type="text/javascript" src='script.js?v=1.1.1'></script>
<script type="text/javascript" src='lending_js/script.js?v=0.2.1'></script>
<script type="text/javascript" src="../js/jquery.animateNumber.min.js"></script>

<script type="text/javascript">

	$is_tablet = $('body').width() <= 768 ? true : false;
	$is_mobile = $('body').width() <= 425 ? true: false;

	$slidesPerView = 3;
	if ($is_mobile) {
		$slidesPerView = 1;
	} else if ($is_tablet) {
		$slidesPerView = 2;
	}
	var swiper_review = new Swiper('.swiper-reviews', {
      slidesPerView: 1,
      spaceBetween: 1,
      loop: true,
      speed: 1000,
      // autoplay: {
      //   delay: 2000,
      //   disableOnInteraction: false,
      // },
      pagination: {
        clickable: true,
      },
      pagination: {
        el: '.swiper-pagination',
        type: 'fraction',
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });

	var swiper_progress = new Swiper('.swiper-progress', {
      slidesPerView: $slidesPerView,
      spaceBetween: 1,
      loop: true,
      speed: 1000,
      autoplay: {
        delay: 2000,
        disableOnInteraction: false,
      },
      pagination: {
        clickable: true,
      },
    });
</script>
</body>
</html>