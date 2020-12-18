<?php
	$money_types = get_money_type();
?>

<button class='btn btn-info btn-sm btn-block money-transfer-btn' data-toggle="modal" data-target="#money-transfer-modal">Перевод</button>

<div class="modal fade" id='money-transfer-modal' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Перевод денег</h4>
			</div>
			<div class="modal-body">
				<form id='money-transfer-form' class='form-horizontal'>
					<div class='form-group'>
						<label for='from' class='control-label col-md-4 col-sm-4 col-xs-4'>
							Откуда:
						</label>
						<div class='col-md-8 col-sm-8 col-xs-8'>
							<select id='from' required name='from-money-type' class='form-control'>
								<option value=''>Выберите откуда:</option>
								<?php
									$html = "";
									foreach ($money_types['datas'] as $money_type_id => $money_type) {
										$html .= "<option value='".$money_type_id."'>".$money_type['title_full']."</option>";
									}
									echo $html;
								?>
							</select>
						</div>
					</div>
					<div class='form-group'>
						<label for='to' class='control-label col-md-4 col-sm-4 col-xs-4'>
							Куда:
						</label>
						<div class='col-md-8 col-sm-8 col-xs-8'>
							<select id='to' required name='to-money-type' class='form-control'>
								<option value=''>Выберите куда:</option>
								<?php
									$html = "";
									foreach ($money_types['datas'] as $money_type_id => $money_type) {
										$html .= "<option value='".$money_type_id."'>".$money_type['title_full']."</option>";
									}
									echo $html;
								?>
							</select>
						</div>
					</div>
					<div class='form-group'>
						<label for='date' class='control-label col-md-4 col-sm-4 col-xs-4'>Дата:</label>
						<div class='col-md-8 col-sm-8 col-xs-8'>
							<input type="text" name="money-transfer-date" id='date' autocomplete="off" id='date' required placeholder="dd.mm.yyyy" class='form-control money-transfer-datepicker' value='<?php echo date('d.m.Y'); ?>'>
						</div>
					</div>
					<div class='form-group'>
						<label for='amount' class='control-label col-md-4 col-sm-4 col-xs-4'>Сумма:</label>
						<div class='col-md-8 col-sm-8 col-xs-8'>
							<input type="number" name="amount" class='form-control' min='0.01' step='0.01' required placeholder="Сумма">
						</div>
					</div>
					<div class='form-group'>
						<label for='fee' class='control-label col-md-4 col-sm-4 col-xs-4'>Комиссия</label>
						<div class='col-md-8 col-sm-8 col-xs-8'>
							<input type="number" name="fee" id='fee' class='form-control' min='0' step='0.01' value='0' required placeholder="Комиссия">
						</div>
					</div>
					<div class='form-group'>
						<div class='col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-12'>
							<button type='submit' class='btn btn-sm btn-success'>Перевод</button>
							<button type='button' class='btn bbtn-sm btn-warning cancel-money-transfer'>Очистить</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>