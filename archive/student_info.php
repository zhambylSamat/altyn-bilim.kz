<?php
	$student_num = $_GET['data_num'];
	$result = array();
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT name,
									surname,
									phone,
									username,
									DATE_FORMAT(dob, '%d.%m.%Y') as dob,
									school,
									class,
									home_phone,
									address,
									(SELECT name FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Name,
								    (SELECT surname FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Surname,
								    (SELECT phone FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Phone,
								    (SELECT name FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Name,
								    (SELECT surname FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Surname,
								    (SELECT phone FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Phone
								FROM student s
								WHERE s.student_num = :student_num");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div class='container-fluid'>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<p><b>Есімі:</b> <?php echo $result['name'];?></p>
		<p><b>Тегі:</b> <?php echo $result['surname'];?></p>
	</div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12'>
		<p><b>Туылған күні:</b> <?php echo $result['dob'];?></p>
		<p><b>Username:</b>  <?php echo $result['dob'];?></p>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12' style='border-top:1px dashed lightgray; margin:0 0 1% 0;'></div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12'>
		<p><b>Телефон:</b> <?php echo $result['phone'];?></p>
	</div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12'>
		<p><b>Мектебі:</b> <?php echo $result['school'];?></p>
		<p><b>Сыныбы:</b> <?php echo $result['class']!='' ? $result['class'] : "N/A";?></p>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12' style='border-top: 1px dashed lightgray; margin:0 0 1% 0;'></div>
	<div class='dol-md-12 col-sm-12 col-xs-12'><center><h4 style='color:gray;'>Ата-ана</h4></center></div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12' style='border: 1px solid #ccc; padding-top:1%; padding-bottom:1%; border-radius: 10px;'>
		<center><h4 style='color:#aaa;'>Порталға кіре алатын ата-ана</h4></center>
		<p><b>Есімі:</b> <?php echo $result['p1Name']!='' ? $result['p1Name'] : "N/A";?></p>
		<p><b>Тегі:</b> <?php echo $result['p1Surname']!='' ? $result['p1Surname'] : "N/A";?></p>
		<p><b>Телефон:</b> <?php echo $result['p1Phone']!='' ? $result['p1Phone'] : "N/A";?></p>
	</div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12'>
		<p><b>Есімі:</b> <?php echo $result['p2Name']!='' ? $result['p2Name'] : "N/A";?></p>
		<p><b>Тегі:</b> <?php echo $result['p2Surname']!='' ? $result['p2Surname'] : "N/A";?></p>
		<p><b>Телефон:</b> <?php echo $result['p2Phone']!='' ? $result['p2Phone'] : "N/A";?></p>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12' style='border-top: 1px dashed lightgray; margin:0 0 1% 0;'></div>
	<div class='form-group col-md-6 col-sm-6 col-xs-12'>
		<p><b>Үй телефоны:</b> <?php echo $result['home_phone']!='' ? $result['home_phone'] : "N/A";?></p>
	</div>
	<div class='form-group col-md-12 col-sm-12 col-xs-12'>
		<p><b>Мекен-жайы:</b> <?php echo $result['address']!='' ? $result['address'] : "N/A";?></p>
	</div>
</div>