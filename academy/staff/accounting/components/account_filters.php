<?php
	if (isset($_GET['month'])) {
		$month_num = $_GET['month'];
	} else {
		$month_num = date('m');
	}

	if(isset($_GET['year'])) {
		$year_num = $_GET['year'];
	} else {
		$year_num = date('Y');
	}
?>
<hr>
<center><h4>Выбрать период</h4></center>
<input type='text' class='form-control monthPicker' autocomplete='off' name='accounting-period' required placeholder='mm.yyyy' value='<?php echo $month_num.'.'.$year_num; ?>'>
<!-- <button class='btn btn-success btn-sm btn-block choose-period' style='margin-top: 1%;'>Выбрать</button> -->
<?php
	echo "<input type='hidden' name='ab-root' value='".$ab_root."'>";
?>