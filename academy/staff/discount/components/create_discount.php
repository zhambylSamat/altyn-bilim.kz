<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
?>

<div id='open-new-discount-form'>
	+ Жаңа жеңілдік енгізу
</div>
<form id='new-discount-form' class='form-horizontal' action='<?php echo $ab_root.'/academy/staff/discount/controller.php'; ?>' method='post'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='form-group'>
			<label for='discount-title' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Жеңілдіктің аты: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
			</label>
			<div class='col-md-8 col-sm-8 col-xs-12'>
				<input type="text" name="discount-title" class='form-control' placeholder='Жеңілдіктің аты' required>
			</div>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='form-group'>
			<label for='amount' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Процент: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
			</label>
			<div class='col-md-8 col-sm-8 col-xs-6'>
				<div class='input-group'>
					<input type="number" name="amount" id='amount' step='1' min='1' max='100' class='form-control' placeholder="Процент" required>
					<div class='input-group-addon'>%</div>
				</div>
				<b id='amount-info'></b>
			</div>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<!-- <div class='form-group'>
			<label for='discount-type' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Типі: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
			</label>
			<div class='col-md-8 col-sm-8 col-xs-6'>
				<select name='discount-type' id='discount-type' class='form-control' required>
					<option value=''>Жеңілдіктің типін таңда</option>
					<option value='percent'>Процент</option>
					<option value='money'>Ақша</option>
				</select>
				<b id='discount-type-info'></b>
			</div>
		</div> -->
		<div class='form-group'>
			<label for='discount-month' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Қанша айға: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
			</label>
			<div class='col-md-8 col-sm-8 col-xs-6'>
				<select name='discount-month' id='discount-month' class='form-control' required disabled>
					<option value=''>Айдың санын белгіле</option>
					<option value='1'>1 айға</option>
					<option value='2'>2 айға</option>
					<option value='3'>2 айға</option>
					<option value='-1'>Толық курсқа</option>
				</select>
				<b id='discount-month-info'></b>
			</div>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='form-group'>
			<label for='cant-insert-promo-code' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Промокод енгізе алмайды
			</label>
			<div class='col-md-8 col-sm-8 col-xs-6'>
				<input type="checkbox" name="cant-insert-promo-code">
			</div>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<input type="submit" name="add-new-discount" class='btn btn-sm btn-success' value='Сақтау'>
		<button type='button' class='btn btn-xs btn-warning cancel-new-discount'>Отмена</button>
	</div>
</form>
<hr>