<?php

	$ADMIN = 'admin';
	$MODERATOR = 'moderator';
	$TEACHER = 'teacher';
	$STUDENT = 'student';

	$GET = 'get';
	$CREATE = 'create';
	$EDIT = 'edit';
	$ARCHIVATE = 'archivate';
	$DEARCHIVATE = 'deachivate';
	$DELETE = 'delete';
	$FULL_ACCESS = [$GET, $CREATE, $EDIT, $ARCHIVATE, $DEARCHIVATE, $DELETE];

	$month = ['',
				'Қаңтар',
				'Ақпан',
				'Наурыз',
				'Сәуір',
				'Мамыр',
				'Маусым',
				'Шілде',
				'Тамыз',
				'Қыркүйек',
				'Қазан',
				'Қараша',
				'Желтоқсан'];

	$month_ru = ['',
				'Январь',
				'Февраль',
				'Март',
				'Апрель',
				'Май',
				'Июнь',
				'Июль',
				'Август',
				'Сентябрь',
				'Октябрь',
				'Ноябрь',
				'Декабрь'];

	$day_full_name = ['',
				'Понедельник',
				'Вторник',
				'Среда',
				'Четверг',
				'Пятница',
				'Суббота',
				'Воскресенье'];

	$day_name = ['',
				'Пн.',
				'Вт.',
				'Ср.',
				'Чт.',
				'Пт.',
				'Сб.',
				'Вс.'];
	
	// $admin_page_navigator = ['group', 'student', 'material', 'staff'];
	$admin_page_navigator = Array(['short_name' => 'active_groups',
									'full_name' => 'Группалар',
									'description' => 'Қазіргі уақыттағы жумыс істеп тұрған топтар тізімі',
									'icon' => "<span class='fas fa-users'></span>",
									'show' => true],
									['short_name' => 'active_student',
										'full_name' => 'Барлық оқушылар',
										'description' => 'Барлық порталдағы оқушылар',
										'show' => false,
										'class' => 'active-student-nav',
										'icon' => "<span class='fas fa-user-check'></span>",
										'roles' => [$ADMIN => $FULL_ACCESS,
													$MODERATOR => $FULL_ACCESS]],
									['short_name' => 'archive',
										'full_name' => 'Группасы жоқ оқушылар',
										'description' => 'Группасы немесе резервке тіркелген тарауы жоқ оқушылар',
										'show' => false,
										'icon' => "<span class='fas fa-user-times'></span>",
										'roles' => [$ADMIN => $FULL_ACCESS,
													$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'material',
									'full_name' => 'Материалдар',
									'description' => 'Материалдар',
									'icon' => "<span class='fas fa-folder-open'></span>",
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => [$GET]]],
								// ['short_name' => 'staff',
								// 	'full_name' => 'Қызметкерлер',
								// 	'description' => 'Барлық қызметкерлер тізімі',
								// 	'show' => false,
								// 	'roles' => [$ADMIN => $FULL_ACCESS,
								// 				$MODERATOR => [$GET]]],
								['short_name' => 'generate_link',
									'full_name' => 'Сабаққа жеке доступ',
									'description' => 'Материалдарға жеке ссылка құрастыру',
									'icon' => "<span class='fas fa-share-square'></span>",
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'discount',
									'full_name' => 'Оқушыларға жеңілдіктер',
									'description' => 'Оқушыларға жеңілдіктер',
									'icon' => '<i class="fas fa-percent"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'marathon_form',
									'full_name' => 'Марафон',
									'description' => 'Марафон Потенциальный',
									'icon' => '<i class="fas fa-flag-checkered"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'trial_test',
									'full_name' => 'Пробный тест',
									'description' => 'Пробный тест',
									'icon' => '<i class="fas fa-tasks"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'student_payment_list',
									'full_name' => 'Оқушылардың оплатасы',
									'description' => 'Оқушылардың оплатасы',
									'icon' => '<i class="fas fa-coins"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'holidays',
									'full_name' => 'Демалыс күндер',
									'description' => 'Оқушылардың демалыс күндері. Сабақ болмайды',
									'icon' => '<i class="fas fa-calendar-day"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'sms',
									'full_name' => 'SMS',
									'description' => 'SMS',
									'icon' => '<i class="fas fa-sms"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => $FULL_ACCESS]],
								['short_name' => 'accounting',
									'full_name' => 'Бухгалтерия',
									'description' => 'Бухгалтерия',
									'icon' => '<i class="fas fa-file-invoice-dollar"></i>',
									'show' => false,
									'roles' => [$ADMIN => $FULL_ACCESS,
												$MODERATOR => array($GET),
												$TEACHER => array($GET)]]);

	// $admin_page_navigator = Array(['short_name' => 'student',
	// 								'full_name' => 'Оқушылар',
	// 								'description' => 'Барлық оқушылар тізімі',
	// 								'icon' => 'fas fa-user',
	// 								'show' => true,
	// 								// 'roles' => [$ADMIN => $FULL_ACCESS,
	// 								// 			$MODERATOR => $FULL_ACCESS],
	// 								'pages' => Array(['short_name' => 'active_student',
	// 													'full_name' => 'Барлық оқушылар',
	// 													'description' => 'Барлық порталдағы оқушылар',
	// 													'show' => true,
	// 													'icon' => 'fas fa-user-check',
	// 													'roles' => [$ADMIN => $FULL_ACCESS,
	// 																$MODERATOR => $FULL_ACCESS]],
	// 												['short_name' => 'archive',
	// 													'full_name' => 'Архивтегі оқушылар',
	// 													'description' => 'Группасы немесе резервке тіркелген тарауы жоқ оқушылар',
	// 													'show' => false,
	// 													'icon' => 'fas fa-user-times',
	// 													'roles' => [$ADMIN => $FULL_ACCESS,
	// 																$MODERATOR => $FULL_ACCESS]],
	// 												// ['short_name' => 'profile',
	// 												// 	'full_name' => 'Өңделмеген оқушылар',
	// 												// 	'description' => 'Сауалнамасы өнделмеген оқушылар',
	// 												// 	'show' => false,
	// 												// 	'roles' => [$ADMIN => $FULL_ACCESS,
	// 												// 				$MODERATOR => $FULL_ACCESS]]
	// 												)],
	// 							['short_name' => 'group',
	// 								'full_name' => 'Топтар',
	// 								'description' => 'Бралық топтар тізімі',
	// 								'show' => false,
	// 								'icon' => 'fas fa-users',
	// 								// 'roles' => [$ADMIN => $FULL_ACCESS,
	// 								// 			$MODERATOR => $FULL_ACCESS],
	// 								'pages' => Array(['short_name' => 'active_groups',
	// 													'full_name' => 'Белсенді топтар',
	// 													'description' => 'Қазіргі уақыттағы жумыс істеп тұрған топтар тізімі',
	// 													'icon' => 'fas fa-users-cog',
	// 													'show' => true],
	// 												// ['short_name' => 'archive_groups',
	// 												// 	'full_name' => 'Архивтегы топтар',
	// 												// 	'description' => 'Архивке шыққан топтар тізімі',
	// 												// 	'show' => false]
	// 												)],
	// 							['short_name' => 'material',
	// 								'full_name' => 'Материалдар',
	// 								'description' => 'Материалдар',
	// 								'icon' => 'fas fa-folder-open',
	// 								'show' => false,
	// 								'roles' => [$ADMIN => $FULL_ACCESS,
	// 											$MODERATOR => [$GET]]],
	// 							// ['short_name' => 'staff',
	// 							// 	'full_name' => 'Қызметкерлер',
	// 							// 	'description' => 'Барлық қызметкерлер тізімі',
	// 							// 	'show' => false,
	// 							// 	'roles' => [$ADMIN => $FULL_ACCESS,
	// 							// 				$MODERATOR => [$GET]]],
	// 							['short_name' => 'generate_link',
	// 								'full_name' => 'Материалдарға жеке доступ',
	// 								'description' => 'Материалдарға жеке ссылка құрастыру',
	// 								'icon' => 'fas fa-share-square',
	// 								'show' => false,
	// 								'roles' => [$ADMIN => $FULL_ACCESS,
	// 											$MODERATOR => $FULL_ACCESS]]);

	$student_page_navigator = Array(['short_name' => 'lesson',
										'full_name' => 'Сабақ',
										'description' => 'Сабақ процессі',
										'id' => 'lesson-process-nav',
										'class' => 'lesson-process-navigation',
										'icon' => "<span class='fab fa-leanpub'></span>",
										'show' => true],
									['short_name' => 'trial_test',
										'full_name' => 'Пробный тест',
										'description' => 'Пробный тест',
										'id' => 'trial-test-nav',
										'class' => 'trial-test-nav',
										'icon' => '<i class="fas fa-chart-line"></i>',
										'show' => false],
									['short_name' => 'lesson_schedules',
										'full_name' => 'Сабақ кестесі',
										'description' => 'Сабақ кестесі',
										'icon' => "<span class='fas fa-calendar-alt'></span>",
										'show' => false],
									['short_name' => 'notification',
										'full_name' => 'Ескертулер',
										'description' => 'Ескертулер',
										'class' => 'student-notification',
										'icon' => "<span class='fas fa-bell'></span>",
										'show' => false],
									['short_name' => 'payment',
										'full_name' => 'Төлем жасау',
										'id' => 'payment-nav',
										'description' => 'Оқудың төлемін төлеу',
										'icon' => "<span class='fas fa-tenge'></span>",
										'show' => false],
									['short_name' => 'register',
										'full_name' => 'Курсқа тіркелу',
										'description' => 'Тіркелу',
										'class' => 'registration-navigation',
										'icon' => "<span class='fas fa-plus'></span>",
										'show' => false],
									['short_name' => 'full_course',
										'full_name' => 'Толық курсқа тіркелу',
										'description' => 'Толық курсқа тіркелу',
										'class' => 'full-course-navigation',
										'icon' => "<span class='fas fa-shopping-bag'></span>",
										'show' => false],
									['short_name' => 'discount',
										'full_name' => 'Промокодты қолдану <span class="fas fa-gift"></span>',
										'description' => 'Жеңілдіктер',
										'class' => 'discount-navigation',
										'icon' => "<span class='fas fa-gift'></span>",
										'show' => false],
									['short_name' => 'intensive',
										'full_name' => 'Интенсивті курс',
										'description' => 'Интенсивті курс',
										'class' => 'intensive-navigation',
										'icon' => '<i class="fas fa-graduation-cap"></i>',
										'show' => false],
									['short_name' => 'army_diagram',
										'full_name' => 'Армия статистикасы',
										'description' => 'Армия статистикасы',
										'class' => 'army-diagram-navigation hide-army-diagram-navigation',
										'icon' => "<span class='fas fa-project-diagram'></span>",
										'show' => false],
									['short_name' => 'flip_cards',
										'full_name' => 'Теориялық карточкалар',
										'description' => 'Теориялық карточкалар',
										'icon' => "<span class='fas fa-gamepad'></span>",
										'show' => false],
									['short_name' => 'help',
										'full_name' => 'Инструкция',
										'description' => 'Администраторға сұрақ қою',
										'class' => 'instruction-navigation',
										'icon' => '<i class="fas fa-question-circle"></i>',
										'show' => false]
									);
									// ,['short_name' => 'cabinet',
									// 	'full_name' => 'Жеке кабинет',
									// 	'description' => 'Оқушының толық ақпараттары',
									// 	'show' => false]);
	$teacher_page_navigator = [''];



	$page_navigator = [];


	$one_lesson_price = 100;
	$full_lesson_price = 2990;

	$full_army_lesson_price = 9990;
	$one_army_lesson_price = 330;

	$payment_info = array('merchant_id' => 532408,
							'secret_key_for_accepting_payment' => '8AlD8bsHFgR8gepR',
							'secret_key_for_payments_to_clients' => 'eOMIxk1YWh7Kllu5',
							'academy' => array('result_url' => 'https://online.altyn-bilim.kz/academy/student/payment/payment_result.php',
								'success_url' => 'https://online.altyn-bilim.kz/academy/student/?payment_status=success',
								'failure_url' => 'https://online.altyn-bilim.kz/academy/student/?payment_status=fail')
						);

	$coins_for_tutorial_video = 20;
	$amount_of_coins_for_bonus = 2000;
	$days_for_collected_coins = 10;
	$discount_for_promo_codes = 20;
	$bonus_days_from_coins_comment = "Жиналған монеталарға байланысты бонус күндер";

	$bonus_coins_for_army_medal = array(array('level' => 1,
												'coins' => 0),
										array('level' => 2,
												'coins' => 10),
										array('level' => 3,
												'coins' => 20),
										array('level' => 4,
												'coins' => 30));

	$block_count = 4;
?>