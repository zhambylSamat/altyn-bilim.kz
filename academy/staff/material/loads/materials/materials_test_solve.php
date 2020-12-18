<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();

    if (isset($_GET['subtopic_id'])) {
		$subtopic_id = $_GET['subtopic_id'];
	}

	$material_test_solve = get_material_test_solve_by_subtopic_id($subtopic_id);

	// echo json_encode($material_test_solve, JSON_UNESCAPED_UNICODE);
?>


<?php
	$form_html = "<div class='col-md-12 col-sm-12 col-xs-12'>";
	$form_html .= "<form id='add-test-solve-form' class='form-inline' data-dir='".$_SERVER['REQUEST_URI']."'>";
		$form_html .= "<div class='form-group'>";
			$form_html .= "<input type='text' name='title' class='form-control' placeholder='Файлдың аты'>";
		$form_html .= "</div>";
		$form_html .= "<div class='form-group'>";
			$form_html .= "<input type='file' name='document'>";
			$form_html .= "<input type='hidden' name='subtopic' value='".$subtopic_id."'>";
		$form_html .= "</div>";
		$form_html .= "<div class='form-group'>";
			$form_html .= "<input type='submit' class='btn btn-success btn-sm' value='Сақтау'>";
		$form_html .= "</div>";
	$form_html .= "</form>";
	$form_html .= "</div>";
	$form_html .= "<hr>";
?>

<div class='row'>
	<?php echo $form_html;?>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<?php include_once($root.'/staff/material/loads/common/order_action_btns.php'); ?>
		<ul class='list-position-inside list-btn sortable' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>' data-obj='material_test_solve'>
			<?php
			$html = '';
				foreach ($material_test_solve as $value) {
					$html .= "<li class='ls-btn material-obj' data-order='".$value['file_order']."' data-id='".$value['id']."'>";
						$html .= "<a href='/academy".$value['link']."' target='_blank'>".$value['title']."</a>";
						$html .= "<button class='btn btn-xs btn-danger pull-right delete-material-test-solve' value='".$value['id']."'>Удалить</button>";
					$html .= "</li>";
				}
				echo $html;
			?>
		</ul>
	</div>
</div>
