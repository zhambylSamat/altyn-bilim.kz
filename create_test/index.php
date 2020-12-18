<?php
	include_once("../connection.php");
	if(!isset($_SESSION['adminNum'])){
		header('location:..');
	}
	if(!isset($_GET['data_num'])){
		header('location:..');
	}

	$data_num = $_GET['data_num'];
	$test_name = "";
	if($data_num!='new'){
		try {
			$stmt = $conn->prepare("SELECT name FROM test WHERE test_num = :test_num");	
			$stmt->bindParam(':test_num', $data_num, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$test_name = $result['name'];
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
    	}
	}
	$test_name_disabled = $test_name!="" ? true : false;
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Тест құрастыру | Altyn-bilim</title>
	<?php include_once("style.php"); ?>
</head>
<body>

	<?php include_once("js.php"); ?>

	<section style='height: 15px;'></section>
	<section id='main'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12'>
					<label for='test-title-field'>Тесттің тақырыбы: </label>
					<form id='test-title' class='form-inline' method='post' style='display: inline-block;'>
						<div class='form-group'>
							<input type="text" id='test-title-field' class='form-control' name="test_title" value='<?php echo $test_name; ?>' <?php echo $test_name_disabled ? "disabled" : "";?> placeholder="Тест тақырыбы">
							<input type="hidden" name="test_num" value='<?php echo $data_num; ?>'>
							<input type="submit" class='btn btn-xs btn-success' style='<?php echo $test_name_disabled ? "display:none;" : "display:inline-block"; ?>' value='Сақтау'>
							<input type="reset" class='btn btn-xs btn-warning' style='<?php echo $test_name_disabled ? "display:none;" : "display:inline-block"; ?>' value="Отмена">
							<a class='btn btn-info btn-xs' id='edit-test-name' style="<?php echo $test_name_disabled ? "display: inline-block;" : "display: none;" ?>">Өзгерту</a>
						</div>
					</form>	
					<b class="help-block" style='color:red;'>"пәннің Атауы"+"тақырыптың Реті"+"тақырыптың Атауы"</b>
					<p class="help-block">М: "Алгебра 1.6 Амалдарды орындау"</p>
				</div>
			</div>
		</div>
	</section>

	<section id='test-content'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12' id='main-content'>
					<?php
						include_once('ajax_test.php');
					?>		
				</div>
			</div>
		</div>
	</section>

	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>
</body>
</html>