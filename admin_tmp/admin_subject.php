<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'subject';
	}
?>
<?php if($_SESSION['role']==md5('admin')) {?>
<center>
	<form class='form-inline create_subject' method="post" action="admin_controller.php">
		<div class="form-group">
			<label for="id-new-subject-name">Жаңа паннің атауы </label>
	    	<input type="text" name='new-subject-name' class="form-control" id="id-new-subject-name" placeholder="Пәннің атауы" required="">
	  	</div>
	  	<button type='submit' name='create-new-subject' class='btn btn-info'>Жаңа пән енгізу</button>
	</form>
</center>
<hr>
<center>
	<form class='form-inline create_subject' method='post' action="admin_controller.php" novalidate>
		<div class='form-group'>
			<div class='topic-input'>
				<label for='new-topic-name'>Тақырып</label>
				<input type="text" class='form-control' name='new-topic-name' id='new-topic-name' placeholder='Тақырып атауы' required="">
			</div>
			<div class='quiz-input' style='display: none;'>
				<label for='new-quiz-name'>Аралық бақылау</label>
				<textarea type="text" class='form-control' id='new-quiz-name' placeholder='Тақырып атауы' required="" cols='40' rows='3'></textarea>
			</div>
		</div>
		<div class='form-group'>
			<label>Тәуелді пән</label>
			<select name='subject-num' class='form-control' required="">
				<option value=''>Таңдаңыз</option>
				<?php
					try {
						$stmt_subject = $conn->prepare("SELECT * FROM subject order by created_date desc");

					    $stmt_subject->execute();
					    $result_subject = $stmt_subject->fetchAll();
					} catch (ExcPDOExceptioneption $e) {
						echo "Error: " . $e->getMessage();
					}
				?>
				<?php foreach($result_subject as $readrow){?>
				<option value='<?php echo $readrow['subject_num'];?>'><?php echo $readrow['subject_name'];?></option>
				<?php }?>
			</select>
		</div>
		<button type='submit' name='create-new-topic' class='btn btn-info'>Енгізу</button>
	</form>
</center>
<hr>
<center>
	<form class='form-inline create_subject' action='admin_controller.php' method="post">
		<div class='form-group'>
			<label for='id-new-subtopic-name'>Тақырыпша</label>
			<input type="text" name="new-subtopic-name" class='form-control' id='new-topic-name' placeholder='Тақырыпша атауы' required="">
		</div>
		<div class='form-group'>
			<label>Тәуелді тақырып</label>
			<select name='topic-num' class='form-control' required="">
				<option value=''>Таңдаңыз</option>
				<?php
					try {
						$stmt_topic = $conn->prepare("SELECT * FROM topic order by created_date asc");

					    $stmt_topic->execute();
					    $result_topic = $stmt_topic->fetchAll();
					} catch (PDOException $e) {
						echo "Error: " . $e->getMessage();
					}
				?>
				<?php foreach($result_topic as $readrow){?>
				<option value='<?php echo $readrow['topic_num']?>'><?php echo $readrow['topic_name']?></option>
				<?php }?>
			</select>
		</div>
		<button type='submit' name='create-new-subtopic' class='btn btn-info'>Енгізу</button>
	</form>
</center>
<a class='btn pull-right' data-toggle='modal' data-target='.box-data'><u>Өзгерту</u></a>
<!-- <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target=".box-data">Өзгерту</button> -->
<hr>
<?php } ?>
<div class='subjects'>
	<?php include('index_subjects.php');?>
</div>