<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();

	if (isset($_GET['subtopic_id'])) {
		$subtopic_id = $_GET['subtopic_id'];
	}

	$documents = array();
	if (isset($subtopic_id) && $subtopic_id != '') {
		$documents = get_tutorial_document($subtopic_id);
		$perceive = get_material_config($subtopic_id, 'tutorial_document');
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
			<a class='btn btn-xs btn-success material-config-btn hide' data-type='tutorial-document' data-subtopic-id='<?php echo $subtopic_id; ?>'>Сохранить</a>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<?php include_once($root.'/staff/material/loads/common/order_action_btns.php'); ?>
		<ul class='list-position-inside list-btn sortable' data-obj='tutorial_document' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
			<?php
				$html = "";
				foreach ($documents as $value) {

					$html .= "<li class='ls-btn' data-id='".$value['id']."' data-order='".$value['document_order']."' data-title='".$value['title']."'>";
					$html .= "<a href='/academy".$value['link']."' target='_blank'>".$value['title']."</a>";
					$html .= "<button class='btn btn-xs btn-danger pull-right delete-tutorial-document' value='".$value['id']."' data-obj='tutorial_document'>Удалить</button>";
					$html .= "</li>";
				}
				echo $html;
			?>
		</ul>

		<div class='form'>
			<button class='btn btn-xs btn-info adding-document'>Қосу +</button>
			<form class='form-inline add-document hide' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
				<div class="form-group">
					<div class="input-group">
				    	<input type="text" class='form-control' name='title' placeholder="Документтің аты" required>
				    </div>
					<div class="input-group">
				    	<input type="file" class='form-control' name='document' required>
				    	<input type="hidden" name="subtopic_id" value='<?php echo $subtopic_id; ?>'>
				    </div>
				    <p class="text-danger">Файлдың көлемі 2МБ тан (немесе '2 048'КБ немесе '2 097 152'Б тан) аспау керек</p>
				</div>
				<input type='submit' class='btn btn-sm btn-success' value='Сақтау'>
				<a class='btn btn-sm btn-warning cancel-adding-document'>Отмена</a>
			</form>
		</div>
	</div>
</div>