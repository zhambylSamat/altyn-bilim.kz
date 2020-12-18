<button class='btn btn-info btn-sm open-holiday-form'>Демалыс күндерін енгізу</button>
<form class='form-horizontal holiday-form' style='display: none;' action='<?php echo $ab_root; ?>/academy/staff/holidays/controller.php' method='post'>
	<div class='form-group'>
		<label class='control-label col-md-4 col-sm-4 col-xs-12'>
			Демалыс күндер: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
		</label>
		<br>
		<div class='col-md-8 col-sm-8 col-xs-12'>
			<div class='input-daterange holiday-datepicker'>
				<input type="text" name="from-date" autocomplete="off" class='input-sm holiday-datepicker-input' required style='border: 1px solid gray; border-radius: 3px;'>
				<span class='add-on'>-</span>
				<input type="text" name='to-date' autocomplete="off" class='input-sm holiday-datepicker-input' required style='border: 1px solid gray; border-radius: 3px;'>
			</div>
		</div>
	</div>
	<div class='form-group'>
		<label for='title' class='control-label col-md-4 col-sm-4 col-xs-12'>
			Демалыс күннің аты: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
		</label>
		<div class='col-md-8 col-sm-8 col-xs-12'>
			<input type="text" name="title" class='form-control' required placeholder="Мысалы: Конституция күні">
		</div>
	</div>
	<!-- <div class='form-group'>
		<label for='comment' class='control-label col-md-4 col-sm-4 col-xs-12'>
			Коммент: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
		</label>
		<div class='col-md-8 col-sm-8 col-xs-12'>
			<textarea name='comment' id='comment' class='form-control' rows='4' cols='50' required></textarea>
		</div>
	</div> -->
	<div class='form-group'>
		<center>
			<input type="submit" class='btn btn-sm btn-success' name="insert-new-holiday" value='Сақтау'>
			<button type='button' class='btn btn-xs btn-warning cancel-btn'>Отмена</button>
		</center>
	</div>
</form>