<?php
	$marathon_list = get_marathon_list();
	// $marathon_group_list = get_marathon_group_list();
// 562
	// print_r($marathon_list);
	// print_r($marathon_group_list);

	$subjects = array(21, 16);

	$marathon_groups = get_marathon_groups_by_subject_ids($subjects);
?>
<div id='marathon-list-content'>
	<table class='table table-striped table-bordered'>
		<?php 
			$html = "";
			$count = 0;
			foreach ($marathon_list as $marathon_form_id => $value) {
				$html .= "<tr>";
					$html .= "<td><center>".(++$count)."</center></td>";
					$html .= "<td>";
						$html .= "<span>".$value['last_name'].' '.$value['first_name']."</span><br>";
						$html .= "<a target='_blank' href='https://api.whatsapp.com/send?phone=7".$value['phone']."&text=Саламатсыз%20ба!😊%0AБұл%20Altyn%20Bilim%20оқу%20орталығынан.%0A%0A🚀%20Келесі%20аптада,%20сейсенбі%20күні,%20бізде%20ФИЗИКА%20пәнінен%20АРМИЯ%20курсында%20екі%20жаңа%20ағым%20оқуды%20бастайды:%0A%0A🔸%201-ші%20ағым%20физиканы%20басынан%20(с%20нуля)%20бастайды;%0A%0A🔸%202-ші%20ағым%20физиканы%20\"Сақталу%20заңдарынан\"%20бастайды.%0A%0AТолық%20ақпаратты%20осы%20сілтемеден%20көре%20аласыз:%0A%0Ahttps://www.instagram.com/p/CGH0yt4Hb5K/?igshid=1tcjsr2turisn%0A%0AСізді%20курсымызға%20шақырамыз!🙋🏻‍♂️%0AКурсқа%20жазылу%20үшін%20маған%20жазыңыз.%0A%0AҰБТ-да%20жоғары%20нәтижеге%20бірге%20жетейік!%20🏆'>+7 ".$value['phone']."</a><br>";
						$html .= "<span>".$value['city']."</span><br>";
						$html .= "<span>".$value['school'].' / '.$value['class']." сынып</span><br>";
					$html .= "</td>";
					$html .= "<td>";
						$html .= "<p class='insta'>Инстаграм: <a href='https://www.instagram.com/".$value['instagram']."' target='_blank'>@".$value['instagram']."</a></p>";
						foreach ($value['subjects'] as $subject_id => $title) {
							$html .= "<span>".$title."</span><br>";
						}
					$html .= "</td>";
					// $html .= "<td id='mf-id-".$marathon_form_id."'>";

					// 	foreach ($marathon_groups as $group_info_id => $g_info) {
					// 		$checked = "";
					// 		if (array_key_exists($g_info['subject_id'], $value['subjects'])) {
					// 			$checked = 'checked';
					// 		}
					// 		$html .= "<div class='checkbox'>";
					// 			$html .= "<label>";
					// 				$html .= "<input type='checkbox' name='group_infos[]' value='".$group_info_id."' ".$checked.">";
					// 				$html .= "<b class='group-name'>".$g_info['subject_title']." марафон</b>";
					// 			$html .= "</label>";
					// 		$html .= "</div>";
					// 	}
					// $html .= "</td>";
					// $html .= "<td>";
					// 	$html .= "<button class='btn btn-sm btn-success btn-block submit-marathon-student' data-id='".$marathon_form_id."'>Қабылдау</button>";
					// 	// $html .= "<button class='btn btn-xs btn-danger btn-block remove-marathon-student' data-id='".$marathon_form_id."'><i class='fas fa-trash-alt'></i></button>";
					// $html .= "</td>";
				$html .= "</tr>";
			}
			echo $html;
		?>
	</table>
</div>