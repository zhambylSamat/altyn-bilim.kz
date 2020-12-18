<?php 
	include('../connection.php'); 
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'chocolate';
	}

	try {
		$stmt = $conn->prepare("SELECT quantity FROM chocolate_history WHERE id = 1");
		$stmt->execute();
		$chocolate_quantity_left = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div class='chocolates'>
	<span><h4 class='text-success'>Қалған шоколадтар саны: <b><?php echo $chocolate_quantity_left['quantity']; ?></b></h4></span>
	<br>
	<form class='form-inline pull-right' method='post' action='admin_controller.php'>
		<div class='form-group'>
			<input type="number" class='form-control' name="quantity" min='1' step='1' placeholder="0" required>
			<input type="submit" class='btn btn-xs btn-success' name='add_chocolate' value='Шоколад енгізу'>
			<a class='btn btn-xs btn-info' data-toggle='modal' data-target='.box-chocolate-history'>Өзгеру тарихы</a>
		</div>
	</form>
</div>
<br>
<br>
<br>
<div class='chocolate_select'>
	<?php
		try {
			
			$stmt = $conn->prepare("SELECT MONTH(date) month, YEAR(date) year FROM chocolate GROUP BY YEAR(date) DESC, MONTH(date) DESC");
			$stmt->execute();
			$result_select = $stmt->fetchAll();
			$month_txt;
			$month_int;
			$year_int;
			$month_arr = array("","Қаңтар","Ақпан","Наурыз","Сәуір","Мамыр","Маусым","Шілде","Тамыз","Қыркүйек","Қазан","Қараша","Желтоқсан");
		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}
	?>
	<select class='form-control' required="" id='chocolate_select'>
	<?php 

		foreach ($result_select as $key => $value) {
			$month_int = $value['month'];
			$year_int = $value['year'];
			$month_txt = $month_arr[$month_int];
	?>
		<option value='<?php echo $month_int.".".$year_int; ?>'><?php echo $month_txt." ".$year_int; ?></option>
	<?php 
		}
		$month_int = $result_select[0]['month'];
		$year_int = $result_select[0]['year'];
	?>
	</select>
</div>
<div id='chocolate'>
	<?php include_once('index_chocolate.php');?>
</div>