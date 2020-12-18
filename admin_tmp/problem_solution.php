<?php
	include_once('../connection.php');
	$subtopic_num = $_GET['data_num'];
	$dir = "../documents/problem_solving/";
	try {
		$stmt = $conn->prepare("SELECT * FROM problem_solution WHERE subtopic_num = :subtopic_num ORDER BY document_link ASC");
		$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage()." !!!";
	}
?>
<hr>
<center><b>Есептің жауаптары</b></center>
<table class='table table-bordered'>
	<?php 
		$count = 0;
		foreach ($result as $key => $value) {
	?>
	<tr>
		<td>
			Файл<?php echo ++$count;?> :&nbsp;<a href="<?php echo $dir.$value['document_link'];?>" target='_blank'><?php echo explode("___", $value['document_link'])[0].".pdf";?></a>
			<form class='form-inline form-problem-solving-remove-file' method='post' enctype="multipart/form-data">
				<input type="hidden" name="file_name" value='<?php echo $value['document_link'];?>'>
				<input type="hidden" name="file_dir" value='<?php echo $dir;?>'>
				<input type="hidden" name="id" value='<?php echo $value['problem_solution_id'];?>'>
				<input type="hidden" name="sbtn" value='<?php echo $subtopic_num;?>'>
				<input type="submit"  class='btn btn-xs btn-danger' value='Удалить'>
			</form>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td>
			<form class='form-inline form-problem-solving-new-file' method='post' enctype="multipart/form-data">
				<div class='form-group'>
					<p class="help-block">Файлдың аты латын алфавитінде болу керек.</p>
					<label>Файлды енгізу:&nbsp;</label>
					<input type="file" style='display:inline-block;' name="pdf_file" required="">
				</div>
				<input type="hidden" name="file_dir" value='<?php echo $dir;?>'>
				<input type="hidden" name="sbtn" value='<?php echo $subtopic_num;?>'>
				<input type="submit"  class='btn btn-xs btn-success' value='Сақтау'>
			</form>
		</td>
	</tr>
</table>