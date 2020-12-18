<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'group';
	}
?>
<button class='btn btn-success btn-sm new-group' at='new-group' id='new-group-btn'>Жаңа группа</button>
<div id='new-group'>
	<form class='form-inline' id='create-group' method='post'>
		<div class="form-group">
			<!-- <label for="id-g-name">Аты</label> -->
			<?php
				try {
					$stmt = $conn->prepare("SELECT * FROM subject order by subject_name asc");
				    $stmt->execute();
				    $result_subject_list = $stmt->fetchAll();
				} catch (PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
	    	<select class="form-control" name='subject' required="">
	    		<option value=''>Пән</option>
	    		<?php
	    			foreach ($result_subject_list as $value) {
	    		?>
	    		<option value='<?php echo $value['subject_num'];?>'><?php echo $value['subject_name'];?></option>
	    		<?php }	?>
	    	</select>
	  	</div>
	  	<div class="form-group">
	    	<!-- <label for="id-teacher-surname">Тегі</label> -->
	    	<?php
				try {
					$stmt = $conn->prepare("SELECT teacher_num, name, surname FROM teacher WHERE block != 6 ORDER BY surname ASC");
				    $stmt->execute();
				    $result_teacher_list = $stmt->fetchAll();
				} catch (PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
			?>
	    	<select class="form-control" name='teacher' required="">
	    		<option value=''>Мұғалім</option>
	    		<?php
	    			foreach ($result_teacher_list as $value) {
	    		?>
	    		<option value='<?php echo $value['teacher_num'];?>'><?php echo $value['surname']." ".$value['name'];?></option>
	    		<?php } ?>
	    	</select>
	  	</div>
	  	<div class='form-group'>
	  		<input type="text" class='form-control' name="group_name" placeholder='Группаның аты'>
	  	</div>
	  	<div class='form-group'>
	  		<textarea name='comment' class='form-control' placeholder="Түсініктеме" cols='48' rows='2'></textarea>
	  		<p class='help-block'>Мысалы (сабак болатын кундер): Пн, Ср, Пт. 15:00 - 17:30 </p>
	  	</div>
	  	<input type="submit" class='btn btn-info btn-sm' value='Жіберу'>
	  	<!-- <a class='btn close-add-new-group' title='Отмена'><span class='glyphicon glyphicon-remove text-danger' style="font-size: 18px;"></span></a> -->
	</form>
</div>
<input type="text" name="search" data-name='group' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
<hr>
<div class='groups'>
	<?php include_once('index_groups.php');?>
</div>