<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();

	$subject_id = $_GET['subject_id'];
	$topics = get_topics($subject_id);
	$topic_info = count($topics) > 0 ? get_topics_info($topics) : array();

	$result = Array();

	foreach ($topics as $value) {
		$tmp_arr = Array('title' => $value['title'],
						'topic_order' => $value['topic_order'],
						'info' => ['subtopic_count' => 0,
									'tutorial_video_count' => 0,
									'tutorial_document_count' => 0,
									'end_video_count' => 0,
									'material_test_count' => 0]);
		$result[$value['id']] = $tmp_arr;
	}

	foreach ($topic_info as $value) {
		$result[$value['topic_id']]['info']['subtopic_count'] = $value['subtopic_count'];
		$result[$value['topic_id']]['info']['tutorial_video_count'] = $value['tutorial_video_count'];
		$result[$value['topic_id']]['info']['tutorial_document_count'] = $value['tutorial_document_count'];
		$result[$value['topic_id']]['info']['end_video_count'] = $value['end_video_count'];
		$result[$value['topic_id']]['info']['material_test_count'] = $value['material_test_count'];
	}

?>

<?php include_once('common/order_action_btns.php'); ?>
<ol class='list-position-inside list-btn sortable' data-obj='topic' data-next-obj='subtopic' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
<?php
	$order = 0;
	foreach ($result as $id => $value) {
		$order = $value['topic_order'];
		$total_material_count = 0;
		$total_material_count += $value['info']['subtopic_count']
								+ $value['info']['tutorial_video_count']
								+ $value['info']['tutorial_document_count']
								+ $value['info']['end_video_count']
								+ $value['info']['material_test_count'];
		$html = "<li class='ls-btn material-obj' data-order='".$order."' data-id='".$id."' data-title='".$value['title']."'>";
		$html .= "<a class='set_breadcrumb'>".$value['title']."</a>";
		if ($total_material_count == 0) {
			$html .= "<button class='btn btn-xs btn-danger pull-right delete-topic' data-id='".$id."'>Удалить</button>";
		}
		$html .= "<span class='pull-right'>";
		$html .= "Тақырыптыр: <b>".$value['info']['subtopic_count']."</b>. ";
		$html .= "Тақырыптық видеолар: <b>".$value['info']['tutorial_video_count']."</b>. ";
		$html .= "<br>";
		$html .= "Материалдар: <b>".$value['info']['tutorial_document_count']."</b>. ";
		$html .= "Қорытынды видеолар: <b>".$value['info']['end_video_count']."</b>.";
		$html .= "Тесттер: <b>".$value['info']['material_test_count']."</b>.";
		$html .= "</span>";
		$html .= "</li>";
		echo $html;
	}
?>
	<li class='ls-btn' data-order='<?php echo ++$order; ?>'>
		<a class='show-add-material-form'>+ тарау қосу</a>
		<form class='form-inline add-material-content hide' data-obj='topic'>
			<div class='form-group'>
				<div class='input-group'>
					<input type='text' class='form-control' name='title' placeholder='Тараудың атын енгізіңіз'>
					<input type='hidden' name='id' value='<?php echo $subject_id; ?>'>
				</div>
			</div>
			<input type='submit' class='btn btn-sm btn-success' value='Сақтау'>
			<a class='btn btn-sm btn-warning cancel-add-material-form'>Отмена</a>
		</form>
	</li>
</ol>