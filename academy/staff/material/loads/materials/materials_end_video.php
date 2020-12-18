<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();

	if (isset($_GET['subtopic_id'])) {
		$subtopic_id = $_GET['subtopic_id'];
	}

	if (isset($subtopic_id) && $subtopic_id != '') {
		$videos = get_end_video($subtopic_id);
		$perceive = get_material_config($subtopic_id, 'end_video');
	}
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='perceive'>
			<label class='checkbox-s1'>
				Не обязательный материал
				<input type="checkbox" name="perceive" id='perceive' <?php echo $perceive ? 'checked' : ''; ?>>
				<span></span>
			</label>
			<a class='btn btn-xs btn-success material-config-btn hide' data-type='end-video' data-subtopic-id='<?php echo $subtopic_id; ?>'>Сохранить</a>
		</div>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<?php include_once($root.'/staff/material/loads/common/order_action_btns.php'); ?>
		<ul class='list-position-inside list-btn sortable' data-obj='end_video' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
			<?php
				$html = "";
				foreach ($videos as $value) {
					$hour = intval($value['duration'] / 3600);
	    			$minute = intval(($value['duration'] - $hour * 3600) / 60);
	    			$second = intval($value['duration'] - $hour * 3600 - $minute * 60);
					
					$duration = '';
					$duration .= $hour > 0 ? $hour.":" : "";
	    			$duration .= $minute > 0 ? $minute.":" : "";
	    			$duration .= $second > 10 ? $second : "0".$second;

					$html .= "<li class='ls-btn show-video' data-id='".$value['id']."' data-order='".$value['video_order']."' data-link='".$value['link']."' data-title='".$value['title']."'>".$value['title']." <span class='pull-right'>".$duration."</span></li>";
				}
				echo $html;
			?>
		</ul>

		<div class='form'>
			<button class='btn btn-xs btn-info adding-vimeo-video'>Қосу +</button>
			<form class='form-inline add-vimeo-video hide' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
				<div class="form-group">
					<div class="input-group">
				    	<input type="text" class='form-control' name='vimeo-link' placeholder="Enter vimeo video link">
				    	<input type="hidden" name="subtopic_id" value='<?php echo $subtopic_id; ?>'>
				    </div>
				</div>
				<input type='submit' class='btn btn-sm btn-success' value='Сақтау'>
				<a class='btn btn-sm btn-warning cancel-vimeo-video'>Отмена</a>
			</form>
		</div>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<div class='vimeo-video-content'>
			
		</div>
	</div>
</div>