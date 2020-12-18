<?php 
	include('../connection.php');
	if(!$_SESSION['archive_load_page']){
		$_SESSION['archive_page'] = 'student';
	}
	$like = isset($_GET['search']) ? $_GET['search'] : "";
	$result_student = array();
	try {
		$stmt = $conn->prepare("SELECT s.student_num,
									s.name,
								    s.surname,
								    s.school,
								    s.phone,
								    DATE_FORMAT(s.block_date, '%d.%m.%Y') as block_date
								FROM student s
									WHERE s.block = 6 
										AND (s.name LIKE ?
										OR s.surname LIKE ?
										OR s.school LIKE ?
										OR s.phone LIKE ?)
								ORDER BY s.surname, s.name, s.school ASC");
		$stmt->bindValue(1, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(2, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(3, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(4, "%$like%", PDO::PARAM_STR);
	    $stmt->execute();
	    $result_student = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<table class='table table-bordered table-striped'>
	<tr>
		<th>
			<center>#</center>
		</th>
		<th>
			<div class='row'>
				<div class='col-md-4 col-sm-4 col-xs-4'>
					Оқушы
				</div>
				<div class='col-md-2 col-sm-2 col-xs-2'>
					Мектеп
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					Телефон
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					Архивке шыққан уақыты
				</div>
			</div>
		</th>
		<th>Әрекет</th>
	</tr>
	<?php
		$student_count = 0; 
		foreach ($result_student as $value) { 
	?>
	<tr class='head'>
		<td>
			<center>
				<h4><i class='count	'><?php echo ++$student_count; ?></i></h4>
			</center>
		</td>
		<td>
			<div class='row'>
				<div class='col-md-4 col-sm-4 col-xs-4'>
					<h4 style='display: inline-block;'>
						<a style='cursor: pointer;' class='object_full_name' href="../admin/student_info_marks.php?data_num=<?php echo $value['student_num']; ?>" target="_blank">
							<?php echo $value['surname']?>&nbsp;<?php echo $value['name']?>
						</a> 
					</h4> 
					<a data-toggle='modal' style='cursor: pointer;' class='student-modal' data-target='.box-pop-up' data-num="<?php echo $value['student_num'];?>">[инфо]</a>
				</div>
				<div class='col-md-2 col-sm-2 col-xs-2'>
					<h4><?php echo $value['school'] != '' ? $value['school'] : "-";?></h4>
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4><?php echo $value['phone'] != "" ? $value['phone'] : "-";?></h4>
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4><?php echo $value['block_date']?></h4>
				</div>
			</div>
		</td>
		<td>
			<a class='btn btn-default btn-xs more_info' data-name='student' data_toggle='false' data_num = "<?php echo $value['student_num']; ?>" title='Толығырақ'>
				<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
			<a class='btn btn-xs btn-success from_archive' data-name='student' data-num="<?php echo $value['student_num'];?>" title='Восстановить'>
				<span class='glyphicon glyphicon-open-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
		</td>
	</tr>
	<tr class='body' style='display: none;'></tr>
	<?php }
		if($student_count==0){
	?>
	<tr>
		<td>
			<center>N/A</center>
		</td>
	</tr>
	<?php } ?>
</table>