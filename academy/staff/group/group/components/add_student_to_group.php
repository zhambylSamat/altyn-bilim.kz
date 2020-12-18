<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
?>
<div class='row' style='margin-bottom: 2%;'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='open-add-student-to-group'>
			+ Оқушыны группаға енгізу
		</div>
		<form id='add-student-to-group' class='form-horizontal' action='<?php echo $ab_root.'/academy/staff/group/controller.php'; ?>' method='post'>
			<div class='col-md-6 col-sm-6 col-xs-12'>
				<div class='form-group'>
					<label for='phone' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Телефоны: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-md-8 col-xs-6'>
						<div class='input-group'>
							<div class='input-group-addon'>+7</div>
							<input type="number" name="phone" max='7999999999' min='7000000000' step='1' id='phone' class='form-control' placeholder="Оқушының ұялы телефоны" required>
							<input type="hidden" name="group_info_id" value='<?php echo $group_id; ?>'>
						</div>
					</div>
				</div>
			</div>
			<div class='col-md-6 col-sm-6 col-xs-12'>
				<b id='student-info-by-phone' class=''></b>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<input type="submit" name="submit-add-student-to-group" class='btn btn-sm btn-success' value='Сақтау'>
				<button type='button' class='btn btn-xs btn-warning' id='cancel-add-student-to-group'>Отмена</button>
			</div>
		</form>
	</div>
</div>