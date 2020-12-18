<?php
	include('../connection.php');
	if(!$_SESSION['archive_load_page']){
		$_SESSION['archive_page'] = 'teacher';
	}
	$like = isset($_GET['search']) ? $_GET['search'] : "";
	$result_teacher = array();
	try {
		$stmt = $conn->prepare("SELECT teacher_num,
									name,
								    surname
								FROM teacher
									WHERE block = 6 
										AND (name LIKE ?
										OR surname LIKE ?)
								ORDER BY surname, name ASC");
		$stmt->bindValue(1, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(2, "%$like%", PDO::PARAM_STR);
	    $stmt->execute();
	    $result_teacher = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<table class='table table-bordered table-striped'>
	<?php
		$teacher_count = 0; 
		foreach ($result_teacher as $value) { 
	?>
	<tr class='head'>
		<td>
			<center>
				<h4><i class='count	'><?php echo ++$teacher_count; ?></i></h4>
			</center>
		</td>
		<td>
			<div class='row'>
				<div class='col-md-4 col-sm-4 col-xs-4'>
					<h4 style='display: inline-block;'>
						<p class='object_full_name text-success'>
							<?php echo $value['surname']?>&nbsp;<?php echo $value['name']?>
						</p> 
					</h4>
				</div>
			</div>
		</td>
		<td>
			<a class='btn btn-xs btn-success from_archive' data-name='teacher' data-num="<?php echo $value['teacher_num'];?>" title='Восстановить'>
				<span class='glyphicon glyphicon-open-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
		</td>
	</tr>
	<tr class='body'></tr>
	<?php }
		if($teacher_count==0){
	?>
	<tr>
		<td>
			<center>N/A</center>
		</td>
	</tr>
	<?php } ?>
</table>