<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();
	$subjects = get_subjects();
	$subject_info = get_subects_info($subjects);

	$result = Array();

	foreach ($subjects as $value) {
		$tmp_arr = Array('title' => $value['title'],
						'is_active' => $value['is_active'],
						'info' => ['topic_count' => 0,
									'subtopic_count' => 0,
									'tutorial_video_count' => 0,
									'tutorial_document_count' => 0,
									'end_video_count' => 0]);
		$result[$value['id']] = $tmp_arr;
	}

	foreach ($subject_info as $value) {
		$result[$value['subject_id']]['info']["topic_count"] = $value['topic_count'];
		$result[$value['subject_id']]['info']['subtopic_count'] = $value['subtopic_count'];
		$result[$value['subject_id']]['info']['tutorial_video_count'] = $value['tutorial_video_count'];
		$result[$value['subject_id']]['info']['tutorial_document_count'] = $value['tutorial_document_count'];
		$result[$value['subject_id']]['info']['end_video_count'] = $value['end_video_count'];
		$result[$value['subject_id']]['info']['material_test_count'] = $value['material_test_count'];
	}
?>

<ol class='list-position-inside list-btn' data-obj='subject' data-next-obj='topic'>
<?php
	foreach ($result as $id => $value) {
		$total_material_count = 0;
		$total_material_count += $value['info']['topic_count']
								+ $value['info']['subtopic_count']
								+ $value['info']['tutorial_video_count']
								+ $value['info']['tutorial_document_count']
								+ $value['info']['end_video_count']
								+ $value['info']['material_test_count'];
		$html = "<li class='ls-btn material-obj' data-id='".$id."' data-title='".$value['title']."'>";
		$html .= "<a class='set_breadcrumb'>".$value['title']."</a>";
		if ($total_material_count == 0) {
			$html .= "<button class='btn btn-xs btn-danger pull-right delete-subject' data-id='".$id."'>Удалить</button>";
		}
		$html .= "<span class='pull-right'>";
		$html .= "Тараулар: <b>".$value['info']['topic_count']."</b>. ";
		$html .= "Тақырыптыр: <b>".$value['info']['subtopic_count']."</b>. ";
		$html .= "Тақырыптық видеолар: <b>".$value['info']['tutorial_video_count']."</b>. ";
		$html .= "<br>";
		$html .= "Материалдар: <b>".$value['info']['tutorial_document_count']."</b>. ";
		$html .= "Қорытынды видеолар: <b>".$value['info']['end_video_count']."</b>.";
		$html .= "Тесттер: <b>".$value['info']['material_test_count']."</b>. ";
		$html .= "</span>";
		$html .= "</li>";
		echo $html;
	}
?>
	<li class='ls-btn'>
		<a class='show-add-material-form'>+ Пән қосу</a>
		<form class='form-inline add-material-content hide' data-obj='subject'>
			<div class='form-group'>
				<div class='input-group'>
					<input type='text' class='form-control' name='title' placeholder='Пәннің атын енгізіңіз'>
				</div>
			</div>
			<input type='submit' class='btn btn-sm btn-success' value='Сақтау'>
			<a class='btn btn-sm btn-warning cancel-add-material-form'>Отмена</a>
		</form>
	</li>
</ol>