<?php 
	include('../connection.php');
	$data_num = isset($for_topic) ? $for_topic : $_GET['data_num'];
?>
<div id='header-part'>
	<?php
		if((isset($part) && $part=='header-part') || (isset($_GET['part']) && $_GET['part']=='header-part')){
			try {
				$stmt = $conn->prepare("SELECT topic_num, topic_name FROM topic WHERE subject_num = :subject_num AND quiz = 'n' order by topic_order asc");
				$stmt->bindParam(':subject_num', $data_num, PDO::PARAM_STR);
			    $stmt->execute();
			    $result_topoic_modal = $stmt->fetchAll();
			} catch (PDOException $e) {
				echo "Error : ".$e->getMessage()." !!!";
			}
	?>
	<select class='edit-modal form-control' data-name='topic-list'>
		<option value='<?php echo $data_num;?>' data='all'>Барлық тақырыптар</option>
		<?php foreach ($result_topoic_modal as $value) { ?>
		<option value="<?php echo $value['topic_num']; ?>" data='single'><?php echo $value['topic_name'];?></option>
		<?php } ?>
	</select>
	<?php } ?>
</div>
<div id='body-part'>
	<?php if((isset($part) && $part=='body-part') || (isset($_GET['part']) && $_GET['part']=='body-part')){ ?>
	<form action='admin_controller.php' method='post'>
		<?php if((isset($subpart) && $subpart=='topic') || (isset($_GET['subpart']) && $_GET['subpart']=='topic')){?>
			<?php
				try {
					$stmt = $conn->prepare("SELECT topic_num, topic_name, quiz FROM topic WHERE subject_num = :subject_num  order by topic_order asc");
					$stmt->bindParam(':subject_num', $data_num, PDO::PARAM_STR);
				    $stmt->execute();
				    $result_topoic_modal = $stmt->fetchAll();
				} catch (PDOException $e) {
					echo "Error : ".$e->getMessage()." !!!";
				}
			?>
			<?php
				$left = '';
				$right = '';
				$moveDown = false;
				$moveUp = false;
				$count = count($result_topoic_modal);
				// foreach($result_topoic_modal as $value) {
				if($count==0) echo "<center><h1>N/A</h1></center>";
				for($i = 0; $i<count($result_topoic_modal); $i++){
					if($i==0) {
						$left = 'glyphicon glyphicon-chevron-down';
						$right = 'glyphicon glyphicon-record';
						$moveDown = true;
						$moveUp = false;
					}
					else if($i==$count-1){
						$left = 'glyphicon glyphicon-record';
						$right = 'glyphicon glyphicon-chevron-up'; 
						$moveDown = false;
						$moveUp = true;
					}
					else if($i>0 || $i<$count-2){
						$left = 'glyphicon glyphicon-chevron-down';
						$right = 'glyphicon glyphicon-chevron-up';
						$moveDown = true;
						$moveUp = true;
					}
					if($count==1){
						$left = 'glyphicon glyphicon-record';
						$right = 'glyphicon glyphicon-record';
						$moveDown = false;
						$moveUp = false;
					}
			?>
			<div class='form-group' style='padding:1% 5%; margin:0'>
				<div style='display: inline-block;'>
					<input type="hidden" name="data_num[]" value='<?php echo $result_topoic_modal[$i]['topic_num'];?>'>
					<?php if($result_topoic_modal[$i]['quiz']=='y'){ ?>
					<label>Аралық бақылау:</label>
					<textarea data-toggle="tooltip" data-placement="right" title="<?php echo $result_topoic_modal[$i]['topic_name'];?>" class='form-control edit-input' name="data_name[]"><?php echo $result_topoic_modal[$i]['topic_name'];?></textarea>
					<?php }else{ ?>
					<input type="text" data-toggle="tooltip" data-placement="right" title="<?php echo $result_topoic_modal[$i]['topic_name'];?>" class='form-control edit-input' name="data_name[]" value='<?php echo $result_topoic_modal[$i]['topic_name'];?>'>
					<?php } ?>
				</div>
				<div style='display: inline-block;'>
					<a class='btn btn-sm btn-default move move-down' direction='<?php echo ($moveDown) ? 'down' : 'none';?>'><span class="<?php echo $left;?>"></span></a>
					<a class='btn btn-sm btn-default move move-up' direction='<?php echo ($moveUp) ? 'up' : 'none';?>'><span class="<?php echo $right;?>"></span></a>
				</div>
				<div style="display: inline-block;">
					<a class='btn btn-xs btn-danger remove-data'>Удалить</a>
					<a style='display: none;' class='btn btn-sm btn-primary restore'>Восстановить</a>
					<a style='display: none;' class='btn btn-sm btn-warning cancel-edit' data='<?php echo $result_topoic_modal[$i]['topic_name'];?>'>Отмена</a>
				</div>
			</div>
			<?php } ?>
		<hr>
		<center><input type="submit" class='btn btn-sm btn-success' name="submit_data_modal_topic" value='Сохранить'></center>
		<?php } ?>





		<?php if((isset($subpart) && $subpart=='subtopic') || (isset($_GET['subpart']) && $_GET['subpart']=='subtopic')){?>
			<?php
				try {
					$stmt = $conn->prepare("SELECT subtopic_num, subtopic_name FROM subtopic WHERE topic_num = :topic_num order by subtopic_order asc");
					$stmt->bindParam(':topic_num', $data_num, PDO::PARAM_STR);
				    $stmt->execute();
				    $result_subtopoic_modal = $stmt->fetchAll();
				} catch (PDOException $e) {
					echo "Error : ".$e->getMessage()." !!!";
				}
			?>
			<?php
				$left = '';
				$right = '';
				$moveDown = false;
				$moveUp = false;
				$count = count($result_subtopoic_modal);
				if($count==0) echo "<center><h1>N/A</h1></center>";
				for($i = 0; $i<count($result_subtopoic_modal); $i++){
					if($i==0) {
						$left = 'glyphicon glyphicon-chevron-down';
						$right = 'glyphicon glyphicon-record';
						$moveDown = true;
						$moveUp = false;
					}
					else if($i==$count-1){
						$left = 'glyphicon glyphicon-record';
						$right = 'glyphicon glyphicon-chevron-up'; 
						$moveDown = false;
						$moveUp = true;
					}
					else if($i>0 || $i<$count-2){
						$left = 'glyphicon glyphicon-chevron-down';
						$right = 'glyphicon glyphicon-chevron-up';
						$moveDown = true;
						$moveUp = true;
					}
					if($count==1){
						$left = 'glyphicon glyphicon-record';
						$right = 'glyphicon glyphicon-record';
						$moveDown = false;
						$moveUp = false;
					}
			?>
			<div class='form-group' style='padding: 1% 5%; margin:0'>
				<div style='display: inline-block;'>
					<input type="hidden" name="data_num[]" value='<?php echo $result_subtopoic_modal[$i]['subtopic_num'];?>'>
					<input type="text" data-toggle="tooltip" data-placement="right" title="<?php echo $result_subtopoic_modal[$i]['subtopic_name'];?>" class='form-control edit-input' name="data_name[]" value='<?php echo $result_subtopoic_modal[$i]['subtopic_name'];?>'>
				</div>
				<div style='display: inline-block;'>
					<a class='btn btn-sm btn-default move move-down' direction='<?php echo ($moveDown) ? 'down' : 'none';?>'><span class="<?php echo $left;?>"></span></a>
					<a class='btn btn-sm btn-default move move-up' direction='<?php echo ($moveUp) ? 'up' : 'none';?>'><span class="<?php echo $right;?>"></span></a>
				</div>
				<div style="display: inline-block;">
					<a class='btn btn-xs btn-danger remove-data'>Удалить</a>
					<a style='display: none;' class='btn btn-sm btn-primary restore'>Восстановить</a>
					<a style='display: none;' class='btn btn-sm btn-warning cancel-edit' data='<?php echo $result_subtopoic_modal[$i]['subtopic_name'];?>'>Отмена</a>
				</div>
			</div>
			<?php } ?>
		<hr>
		<center><input type="submit" class='btn btn-sm btn-success' name="submit_data_modal_subtopic" value='Сохранить'></center>
		<?php } ?>
	</form>
	<?php } ?>
</div>