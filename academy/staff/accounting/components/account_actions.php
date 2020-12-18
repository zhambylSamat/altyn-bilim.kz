<?php
	$money_types = get_money_type();

	$html = "<hr>";
	$html .= "<div class='row'>";
		$html .= "<div class='col-md-12 col-sm-12 col-xs-12'>";
			$html .= "<form id='add-new-amount' class='form-horizontal'>";
				$html .= "<div class='form-group'>";
					$html .= "<div class='btn-group col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-12'>";
						$html .= "<label class='btn btn-sm btn-success'>";
							$html .= "<input type='radio' name='account-type' value='coming'> Приход";
						$html .= "</label>";
						$html .= "<label class='btn btn-sm btn-success'>";
							$html .= "<input type='radio' name='account-type' value='expenditure' checked> Расход";
						$html .= "</label>";
					$html .= "</div>";
				$html .= "</div>";
				$html .= "<div class='form-group'>";
					$html .= "<label for='date' class='control-label col-md-4 col-sm-4 col-xs-3'>";
						$html .= "Дата: ";
					$html .= "</label>";
					$html .= "<div class='col-md-8 col-sm-8 col-xs-9'>";
						$html .= "<input type='text' class='form-control datePicker' autocomplete='off' name='date' required placeholder='dd.mm.yyyy' value='".date('d.m.Y')."'>";
					$html .= "</div>";
				$html .= "</div>";
				$html .= "<div class='form-group'>";
					$html .= "<label for='account-amount' class='control-label col-md-4 col-sm-4 col-xs-3'>";
						$html .= "Сумма: ";
					$html .= "</label>";
					$html .= "<div class='col-md-8 col-sm-8 col-xs-9'>";
						$html .= "<input type='number' id='account-amount' name='account-amount' class='form-control' min='0.01' step='0.01' requrired placeholder='Сумма'>";
					$html .= "</div>";
				$html .= "</div>";
				$html .= "<div class='form-group'>";
					$html .= "<label for='money-type' class='control-label col-md-4 col-sm-4 col-xs-3'>";
						$html .= "Тип";
					$html .= "</label>";
					$html .= "<div class='col-md-8 col-sm-8 col-xs-9'>";
						$html .= "<select name='money-type' id='money-type' class='form-control' required>";
							$html .= "<option value=''>Ақшаның типін таңда</option>";
							foreach ($money_types['datas'] as $money_type_id => $money_type) {
								$html .= "<option value='".$money_type_id."' ".($money_type_id == 3 ? 'selected' : '').">".$money_type['title_full']."</option>";
							}
						$html .= "</select>";
					$html .= "</div>";
				$html .= "</div>";
				$html .= "<div class='form-group'>";
					$html .= "<div class='col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-12'>";
						$html .= "<button type='button' class='btn btn-sm btn-default btn-block open-category-modal' data-toggle='modal' data-target='#category-choose'>Категория</button>";
						$html .= "<div id='category-list'>";
						$html .= "</div>";
					$html .= "</div>";
				$html .= "</div>";
				$html .= "<div class='form-group'>";
					$html .= "<div class='col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-12'>";
						$html .= "<button type='submit' class='btn btn-sm btn-success'>Сақтау</button> ";
						$html .= "<button type='button' class='btn btn-sm btn-warning cancel-add-new-amount'>Очистить</button>";
					$html .= "</div>";
				$html .= "</div>";
			$html .= "</form>";
		$html .= "</div>";
	$html .= "</div>";

	$html .= "<div class='modal fade' id='category-choose' tabindex='-1' role='dialog'>";
		$html .= "<div class='modal-dialog' role='document'>";
			$html .= "<div class='modal-content'>";
				$html .= "<div class='modal-header'>";
					$html .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
					$html .= "<h4 class='modal-title'></h4>";
				$html .= "</div>";
				$html .= "<div class='modal-body'>";
				$html .= "</div>";
			$html .= "</div>";
		$html .= "</div>";
	$html .= "</div>";

	echo $html;
?>