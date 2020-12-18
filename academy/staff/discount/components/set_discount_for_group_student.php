<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	$discount_list = get_discount_list();
	// echo json_encode($discount_list, JSON_UNESCAPED_UNICODE);
?>


<div id='open-set-discount-for-gs-form'>
	+ Оқушыға жеңілдік енгізу
</div>

<form onsubmit='return validate_set_discount()' id='set-discount-for-gs-form' class='form-horizontal' action='<?php echo $ab_root.'/academy/staff/discount/controller.php'; ?>' method='post'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='form-group'>
			<label for='discount' class='control-label col-md-4 col-sm-4 col-xs-6'>
				Жеңілдікті таңда:  <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
			</label>
			<div class='col-md-8 col-sm-8 col-xs-6'>
				<select name='discount' id='discount' class='form-control' required>
					<option value=''>Скидканы таңда</option>
					<?php
						$html = "";
						foreach ($discount_list as $id => $value) {
							$html .= "<option value='".$id."'>".$value['title']."</option>";
						}
						echo $html;
					?>
				</select>
			</div>
		</div>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<table style='margin-bottom: 5%' class='table table-bordered table-striped'>
			<tr>
				<td class='student-info' style='width: 46%; padding: 2%;'>
					<div class='form-group'>
						<label for='phone' class='control-label col-md-4 col-sm-4 col-xs-6'>
							Оқушының телефоны:
						</label>
						<div class='col-md-8 col-sm-8 col-xs-6'>
							<div class='input-group'>
								<div class='input-group-addon'>+7</div>
								<input type="number" required max='7999999999' min='7000000000' step='1' name='phone[]' class="form-control" id="phone" placeholder="Телефон нөмірін енгіз" value=''>
								<input type='hidden' name='std-id[]' value=''>
							</div>
							<b class='phone-info'></b>
						</div>
					</div>
				</td>
				<td class='student-group-info' style='width: 46%; padding: 2%;'></td>
			</tr>
			<tr>
				<td colspan='2'>
					<button type='button' class='btn btn-sm btn-info btn-block add-student-group'>+ Оқуша қосу</button>
				</td>
			</tr>
		</table>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<input type="submit" class='btn btn-sm btn-success' name="set-discount-for-gs" value='Сақтау'>
		<button type='button' class='btn btn-xs btn-warning cancel-form'>Отмена</button>
	</div>
</form>