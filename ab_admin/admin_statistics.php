<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'statistics';
	}
	$year = "";

	$period_result = array();
	try {
		$stmt = $conn->prepare("SELECT YEAR(ssf.period) year
								FROM statistics_student_frequency ssf
								GROUP BY year
								ORDER BY year DESC");
		$stmt->execute();
		$period_result = $stmt->fetchAll();
		$year = $period_result[0]['year'];
	} catch (Exception $e) {
		throw $e;
	}
?>

<div>
	<select class='form-control' required="" id="statistics-student-period-select">
		<?php
			if (count($period_result) == 0) {
				echo "<option>N/A</option>";
			} else {
				foreach ($period_result as $value) {
		?>
		<option value='<?php echo $value['year']; ?>'><?php echo $value['year']; ?></option>
		<?php }} ?>
	</select>
</div>

<div id='statistics'>
	<?php
		if (count($period_result) != 0) {
			include_once("index_statistics_student.php"); 
		} else {
			echo "<center>N/A</center>";
		}
	?>
</div>