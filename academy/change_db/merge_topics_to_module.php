<?php
	include_once('../common/connection.php');

	try {

		$modules = [
						[
							[
								'title' => "Модуль 1 - Фундамент. Өрнекті ықшамдау.",
								'ids' => [16, 17],
								'orders' => array()
							],
							[
								'title' => 'Модуль 2 - Теңдеулер. Теңсіздіктер. Бөлшектің бөлімін иррационалдықтан арылту.',
								'ids' => [18, 19, 20],
								'orders' => array()
							],
							[
								'title' => 'Модуль 3 - Иррационал теңдеулер мен теңсіздіктер. Прогрессия.',
								'ids' => [21, 22],
								'orders' => array()
							],
							[
								'title' => 'Модуль 4 - Тригонометрия',
								'ids' => [23, 24, 25, 26],
								'orders' => array()
							],
							[
								'title' => 'Модуль 5 - Модуль. Бөлшек дәреже. Көрсеткіштік теңдеулер мен теңсіздіктер',
								'ids' => [27, 28, 29],
								'orders' => array()
							],
							[
								'title' => 'Модуль 6 - Логарифм',
								'ids' => [30, 31, 32, 33],
								'orders' => array()
							],
							[
								'title' => 'Модуль 7 - Туынды. Алғашқы функция. Интеграл',
								'ids' => [34, 35],
								'orders' => array()
							],
							[
								'title' => 'Модуль 8 - Функция. Мәселе есептер',
								'ids' => [36, 37],
								'orders' => array()
							],
							[
								'title' => 'Модуль 9 - Тест есептерімен жұмыс',
								'ids' => [38],
								'orders' => array()
							]
						],
						[
							[
								'title' => 'Модуль 1 - Планиметрия',
								'ids' => [44, 45, 46],
								'orders' => array()
							],
							[
								'title' => 'Модуль 2 - Вектор. Түзудің және шеңбердің теңдеуі',
								'ids' => [47, 48],
								'orders' => array()
							],
							[
								'title' => 'Модуль 3 - Стереометрия',
								'ids' => [49, 50, 51, 52],
								'orders' => array()
							]
						],
						[
							[
								'title' => 'Модуль 1 - Механика. Гидростатика',
								'ids' => [82, 83, 84, 85, 86],
								'orders' => array()
							],
							[
								'title' => 'Модуль 2 - Молекулалық физика',
								'ids' => [87],
								'orders' => array()
							],
							[
								'title' => 'Модуль 3 - Электр өрісі. Тұрақты ток',
								'ids' => [88, 89],
								'orders' => array()
							],
							[
								'title' => 'Модуль 4 - Магнит өрісі. Электромагниттік тербелістер мен толқындар',
								'ids' => [90, 91],
								'orders' => array()
							],
							[
								'title' => 'Модуль 5 - Оптика. Фотоэффект. Ядролық физика. Салыстырмалылық теориясы',
								'ids' => [92, 93, 94, 95],
								'orders' => array()
							],
							[
								'title' => 'Модуль 6 - Тест есептерімен жұмыс',
								'ids' => [96],
								'orders' => array()
							]
						]
					];
		$query = "SELECT t.id, count(st.id) AS subtopic_nums
					FROM subtopic st,
						topic t
					WHERE t.id IN (";

		$query_change_title = "UPDATE topic SET title = :title WHERE id = :id";
		for ($i=0; $i < count($modules); $i++) { 
			for ($j=0; $j < count($modules[$i]); $j++) {
				$topic_ids = implode(",", $modules[$i][$j]['ids']);
				$subquery = $query.$topic_ids.")
						AND st.topic_id = t.id
					GROUP BY t.id
					ORDER BY t.id";
				$stmt = $connect->prepare($subquery);
				$stmt->execute();
				$sql_res = $stmt->fetchAll();
				$count = 0;
				$order = 0;
				$parent_topic_id = 0;
				foreach ($sql_res as $val) {
					if ($count == 0) {
						$stmt = $connect->prepare($query_change_title);
						$stmt->bindParam(':title', $modules[$i][$j]['title'], PDO::PARAM_STR);
						$stmt->bindParam(':id', $val['id'], PDO::PARAM_INT);
						$stmt->execute();
						$order = $val['subtopic_nums'];
						$parent_topic_id = $val['id'];
					} else {
						$set_order = "SET @position:=:position;
								UPDATE subtopic
								SET subtopic_order=@position:=@position+1 
								WHERE topic_id=:id
								ORDER BY subtopic_order";
						$stmt = $connect->prepare($set_order);
						$stmt->bindParam(':position', $order, PDO::PARAM_INT);
						$stmt->bindParam(':id', $val['id'], PDO::PARAM_INT);
						$stmt->execute();
						$order += $val['subtopic_nums'];

						$stmt = $connect->prepare("UPDATE subtopic SET topic_id = :new_topic_id WHERE topic_id = :old_topic_id");
						$stmt->bindParam(":new_topic_id", $parent_topic_id, PDO::PARAM_INT);
						$stmt->bindParam(':old_topic_id', $val['id'], PDO::PARAM_INT);
						$stmt->execute();
					}
					array_push($modules[$i][$j]['orders'], array('topic_id' => $val['id'],
																'subtopic_nums' => $val['subtopic_nums']));
					$count++;
				}
			}
		}
		echo "<br><br><br><br>";
		print_r($modules);
		
	} catch (Exception $e) {
		throw $e;
	}
?>