<?php
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
?>
<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div id='open-add-new-student-form'>
			+ Жаңа оқушы енгізу
		</div>
		<form id='add-new-student' class='form-horizontal' action='<?php echo $ab_root.'/academy/staff/student/controller.php'; ?>' method='post'>
			<div class='col-md-6 col-sm-6 col-xs-12'>
				<div class='form-group'>
					<label for='phone' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Телефоны: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<div class='input-group'>
							<div class='input-group-addon'>+7</div>
							<input type="number" name="phone" max='7999999999' min='7000000000' step='1' id='phone' class='form-control' placeholder="Оқушының ұялы телефоны" required>
						</div>
						<b id='phone-err' class='text-danger'></b>
					</div>
				</div>
				<div class='form-group'>
					<label for='last-name' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Тегі: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="last-name" id='last-name' class='form-control' placeholder="Оқушының тегі" required>
						<b id='last-name-err' class='text-danger'></b>
					</div>
				</div>
				<div class='form-group'>
					<label for='first-name' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Аты: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="first-name" id='first-name' class='form-control' placeholder="Оқушының аты" required>
						<b id='first-name-err' class='text-danger'></b>
					</div>
				</div>
				<div class='form-group'>
					<label for='promo-code' class='control-label col-md-4 col-sm-4 col-xs-6'>Промо-код:</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="promo-code" id='promo-code' class='form-control' placeholder="Промо-код">
						<b id='promo-code-info' class='text-warning'></b>
					</div>
				</div>
			</div>
			<div class='col-md-6 col-sm-6 col-xs-12'>
				<div class='form-group'>
					<label for='school' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Мектебі: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="school" id='school' class='form-control' placeholder="Оқушының мектебі" required>
						<b id='school' class='text-danger'></b>
					</div>
				</div>
				<div class='form-group'>
					<label for='class' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Сыныбы:  <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="class" id='class' class='form-control' placeholder="Оқушының сыныбы" required>
					</div>
				</div>
				<div class='form-group'>
					<label for='city' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Қала: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<input type="text" name="city" id='city' class='form-control' placeholder="Қала" required>
						<b id='city' class='text-danger'></b>
					</div>
				</div>
				<div class='form-group'>
					<label for='parent-phone' class='control-label col-md-4 col-sm-4 col-xs-6'>
						Ата-анасының телефоны: 
					</label>
					<div class='col-md-8 col-sm-8 col-xs-6'>
						<div class='input-group'>
							<div class='input-group-addon'>+7</div>
							<input type="number" name="parent_phone" max='7999999999' min='7000000000' step='1' id='parent-phone' class='form-control' placeholder="Оқушының ата-анасының ұялы телефоны">
						</div>
						<b id='phone-err' class='text-danger'></b>
					</div>
				</div>
				<div clas='form-group'>
					<input type="submit" name='add-new-student-submit' class='btn btn-sm btn-success' value='Сақтау'>
					<button type='button' class='btn btn-sm btn-warning cancel-add-new-student-form'>Отмена</button>
				</div>
			</div>
		</form>
	</div>
</div>