<?php
	$admin_page_navigator = Array(['short_name' => 'student',
									'full_name' => 'Оқушылар',
									'description' => 'Барлық оқушылар тізімі',
									'pages' => Array(['short_name' => 'in_group',
														'full_name' => 'Топтағы оқушылар',
														'description' => 'Топтарда тіркеліп тұрған оқушылар'],
													['short_name' => 'active_student',
														'full_name' => 'Белсенді оқушылар',
														'description' => 'Барлық порталдағы белсенді оқушылар'],
													['short_name' => 'profile',
														'full_name' => 'Өңделмеген оқушылар',
														'description' => 'Сауалнамасы өнделмеген оқушылар'],
													['short_name' => 'archive',
														'full_name' => 'Архив',
														'description' => 'Архивке шыққан оқушылар'])],
								['short_name' => 'group',
									'full_name' => 'Топтар',
									'description' => 'Бралық топтар тізімі'],
								['short_name' => 'material',
									'full_name' => 'Материалдар',
									'description' => 'Материалдар'],
								['short_name' => 'staff',
									'full_name' => 'Қызметкерлер',
									'description' => 'Барлық қызметкерлер тізімі']);

	print_r(array_slice($admin_page_navigator, 0, 1));
?>