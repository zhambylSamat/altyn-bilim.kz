<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/material/view.php');
	include_once($root.'/common/check_authentication.php');
    check_admin_access();

    if (isset($_GET['subtopic_id'])) {
		$subtopic_id = $_GET['subtopic_id'];
	}

	$material_tests = get_material_test_by_subtopic_id($subtopic_id);
?>

<?php
	$form_html = "<div class='col-md-12 col-sm-12 col-xs-12'>";
	$form_html .= "<form id='add-test-form' class='form-inline' data-dir='".$_SERVER['REQUEST_URI']."'>";
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
		<ul class='list-position-inside list-btn sortable' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>' data-obj='material_test'>
			<?php
			$html = '';
				foreach ($material_tests['test'] as $value) {
					$html .= "<li class='ls-btn material-obj' data-order='".$value['test_order']."' data-id='".$value['id']."'>";
						$html .= "<a href='/academy".$value['link']."' target='_blank'>".$value['title']."</a>";
						$html .= "<button class='btn btn-xs btn-danger pull-right delete-material-test' value='".$value['id']."'>Удалить</button>";
					$html .= "</li>";
				}
				echo $html;
			?>
		</ul>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<ul class='list-position-inside list-btn' data-dir='<?php echo $_SERVER['REQUEST_URI']; ?>'>
			<div class='ans-box'>
			<?php
				$html  = '';
				foreach ($material_tests['answers'] as $numeration => $value) {
					$html .= "<form class='prefixes'>";
						$html .= "<input type='hidden' name='subtopic_id' value='".$subtopic_id."'>";
						$html .= "<input type='hidden' name='numeration' value='".$numeration."'>";
						$html .= "<span>".$numeration.") &nbsp;&nbsp;</span>";
						foreach ($value as $i => $answer) {
							$html .= "<label class='inline-ans-prefix ".($answer['torf'] ? 'ans-prefix-checked' : '')."' for='ans-".$answer['id']."'>";
							$html .= "<input type='hidden' class='ans-id' name='ans_id[".$i."]' value='".$answer['id']."'>";
							$html .= "<input type='text' name='prefix[".$i."]' required class='forn-control ans-prefix-input' value='".$answer['prefix']."'>";
							$html .= "<input type='radio' class='ans-prefix-radio' name='ans_radio[".$i."]' id='ans-".$answer['id']."' ".($answer['torf'] ? 'checked' : '').">";
							$html .= "<button type='button' class='btn btn-xs btn-danger btn-block remove-prefix'><span class='glyphicon glyphicon-remove'></span></button>";
							$html .= "</label>";
						}
						$html .= "<button type='button' style='maragin-left: 10px;' class='btn btn-info btn-sm extra-prefix'><span class='glyphicon glyphicon-plus'></span></button>";
						$html .= "<button type='submit' style='margin-left: 10px;' class='btn btn-success btn-sm save-add-prefixes hidden'>Сақтау</button>";
						$html .= "<button type='button' style='margin-left: 10px;' class='btn btn-warning btn-sm cancel-add-prefixes hidden'>Отмена</button>";
						$html .= "<p class='message text-danger'></p>";
					$html .= "</form>";
				}
				$html .= "<button type='button' class='btn btn-info btn-sm add-prefixes' data-subtopic-id='".$subtopic_id."'>Жауаптар енгізгу +</button>";
				echo $html;
			?>
			</div>
		</ul>
	</div>

</div>
