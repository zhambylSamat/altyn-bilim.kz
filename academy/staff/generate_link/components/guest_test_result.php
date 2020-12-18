<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/generate_link/views.php');

	$mlt_student_list = get_mlt_student_list();

	// echo json_encode($mlt_student_list, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($root.'/common/assets/meta.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/assets/js.php');
		check_admin_access();
	?>
	<title>Тест жазған оқушылардың тізімі</title>
	<style type="text/css">
		th {
			text-align: center;
		}
	</style>
</head>
<body>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<table class='table table-bordered table-striped' style='margin-top: 5%;'>
					<tr>
						<th>#</th>
						<th>Ссылканың аты</th>
						<th>Оқушының аты</th>
						<th>Тақырыбы</th>
						<th>Жауабы</th>
						<th>Тест жасаған уақыты</th>
					</tr>
					<?php
						$count = 0;
						$html = "";
						foreach ($mlt_student_list as $subtopic_id => $value) {
							$html .= "<tr>";
								$html .= "<td>".(++$count)."</td>";
								$html .= "<td>".$value['material_link_comment']."</td>";
								$html .= "<td>".$value['fio']."</td>";
								$html .= "<td>";
									$html .= "<b>Пән:</b> ".$value['subject_title']."<br>";
									$html .= "<b>Тарау:</b> ".$value['topic_title']."<br>";
									$html .= "<b>Тақырып:</b> ".$value['subtopic_title']."<br>";
								$html .= "</td>";
								$html .= "<td>";
									$html .= "<a href='".$ab_root."/academy/lesson/testing.php?fio=".$value['fio']."&code=".$value['code']."&subtopic_id=".$value['subtopic_id']."' target='_blank'>";
										$html .= "<b>Дұрыс белгілгендері:</b> ".$value['actual_result']."<br>";
										$html .= "<b>Барлық сұрақтар:</b> ".$value['total_result']."<br>";
										$html .= "<b>".$value['percent']."%</b><br>";
									$html .= "</a>";
								$html .= "</td>";
								$html .= "<td>".$value['submit_time']."</td>";
							$html .= "</tr>";
						}
						echo $html;
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>