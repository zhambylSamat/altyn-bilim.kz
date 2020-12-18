<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
		include_once($root.'/common/assets/meta.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/lesson/views.php');
	?>
	<link rel="stylesheet" type="text/less" href="<?php echo $ab_root.'/academy/lesson/style/style.less?v=1.1.1'; ?>">
	<title>Altyn Bilim</title>
</head>
<body>

<div class='container'>
	<?php
		$access_denied_message = "<h2><center>Материал недоступен</center></h2>";
		if (!isset($_GET['q'])) {
			echo $access_denied_message;
		} else {
			$code = $_GET['q'];
			$materials_count = get_short_info_by_material_code($code);
			if (count($materials_count) == 0) {
				echo $access_denied_message;
			} else {
				$is_many = false;
				if (count($materials_count) > 1) {
					$is_many = true;
				}
				foreach ($materials_count as $c) {
					$subtopic_id = $c['subtopic_id'];
					include($root.'/lesson/components/single_lesson.php');
				}
			}
		}
	?>
</div>

<div class="modal fade" id='question-answers' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		</div>
      <div class='modal-body'>
      </div>
    </div>
  </div>
</div>

	<?php
		include_once($root.'/common/assets/js.php');
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/slide_effect/css/flickity.min.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/slide_effect/css/fullscreen.css'; ?>">
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/slide_effect/js/flickity.pkgd.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/slide_effect/js/fullscreen.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/lesson/js/actions.js?v=1.6.7'; ?>"></script>
</body>
</html>