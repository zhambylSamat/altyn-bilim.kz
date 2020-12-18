<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
		include_once($root.'/common/assets/meta.php');
		include_once($root.'/common/assets/style.php');
		include_once($root.'/lesson/views.php');
	?>
	<link rel="stylesheet" type="text/less" href="<?php echo $ab_root.'/academy/lesson/style/style.less?v=1.1.2'; ?>">
	
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


<div class="modal fade" id='pre-test-start' tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class='modal-header'>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h5 class="modal-title" id="myModalLabel">Тақырыпқа байланысты тестті бастамас бұрын Аты-жөніңізді еңгізу керексіз</h5>
			</div>
			<div class='modal-body'>
				<form class='form-horizontal' id='pre-test-start-form'>
					<div class='form-group'>
						<label for='fio' class="control-label col-md-5 col-sm-5 col-xs-5">Аты-жөніңіз</label>
						<div class='col-md-7 col-sm-7 col-xs-7'>
							<!-- <input type="text" name="fio" id='fio' class='form-control' placeholder="Аты-жөніңіз" required> -->
							<div class='input-group'>
								<div class='input-group-addon'>@</div>
								<input type="text" name="fio" id='fio' class='form-control' placeholder="Инстаграм" required>
							</div>
							<input type="hidden" name="code" value='<?php echo $code; ?>'>
							<input type='hidden' name='subtopic_id' value=''>
						</div>
					</div>
					<button type='submit' class='btn btn-success btn-sm'>Тестке өту</button>
				</form>
			</div>
		</div>
	</div>
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
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/photoswipe.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/default-skin/default-skin.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $ab_root.'/academy/photo_swipe/custom_style/photo-swipe-style.css?v=0.0.4'; ?>">

	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/photoswipe.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/photoswipe-ui-default.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/photo_swipe/custom_js/photo-swipe-action.js?v=0.0.7'; ?>"></script>
	<script type="text/javascript" src="<?php echo $ab_root.'/academy/lesson/js/actions.js?v=1.6.9'; ?>"></script>
</body>
</html>