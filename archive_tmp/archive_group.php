<?php
	include('../connection.php');
	if(!$_SESSION['archive_load_page']){
		$_SESSION['archive_page'] = 'group';
	}
	$like = isset($_GET['search']) ? $_GET['search'] : "";
	$result_group = array();
	try {
		$stmt = $conn->prepare("SELECT gi.group_info_num, 
									gi.group_name, 
									gi.block,
									t.name, 
									t.surname, 
									sj.subject_name,
									count(s.student_num) as c
								FROM group_student gs
								INNER JOIN group_info gi 
									ON gs.group_info_num = gi.group_info_num
								   		AND gs.block = 6
								INNER JOIN student s
									ON s.student_num = gs.student_num
								INNER JOIN subject sj
									ON sj.subject_num = gi.subject_num
										AND sj.subject_name LIKE ?
								LEFT JOIN teacher t
									ON t.teacher_num = gi.teacher_num
										AND (t.name LIKE ?
										OR t.surname LIKE ?)
								GROUP BY gi.group_info_num
								ORDER BY gi.group_name, 
									t.surname, 
									t.name, 
									sj.subject_name ASC");

		$stmt->bindValue(1, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(2, "%$like%", PDO::PARAM_STR);
		$stmt->bindValue(3, "%$like%", PDO::PARAM_STR);
	    $stmt->execute();
	    $result_group = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<table class='table table-bordered table-striped'>
	<?php
		$group_count = 0; 
		foreach ($result_group as $value) { 
	?>
	<tr class='head'>
		<td>
			<center>
				<h4><i class='count	'><?php echo ++$group_count; ?></i></h4>
			</center>
		</td>
		<td>
			<div class='row'>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4 style='display: inline-block;' class='object_full_name'>
						<a href="group.php?data_num=<?php echo $value['group_info_num'];?>""><?php echo $value['group_name']?></a>
					</h4> 
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4 style='color:#444'><?php echo $value['surname']?>&nbsp;<?php echo $value['name']?></h4>
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4><span><?php echo $value['subject_name'];?></span></h4>
				</div>
				<div class='col-md-3 col-sm-3 col-xs-3'>
					<h4><span>Бітірген оқушылар саны: <?php echo $value['c']; ?></span></h4>
				</div>
			</div>
		</td>
		<td>
			<?php 
				if($value['block']!=6){
					echo "<p class='text-success'>Активный</p>";
				}
				else if($value['block']==6){
			?>
			<b class='text-warning'>Архив</b>
			<a class='btn btn-xs btn-success from_archive' data-name='group' data-num="<?php echo $value['group_info_num'];?>" title='Восстановить'>
				<span class='glyphicon glyphicon-open-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
			<?php } ?>
		</td>
	</tr>
	<tr class='body'></tr>
	<?php }
		if($group_count==0){
	?>
	<tr>
		<td>
			<center>N/A</center>
		</td>
	</tr>
	<?php } ?>
</table>