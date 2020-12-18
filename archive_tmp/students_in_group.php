<?php
	include('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT s.subject_name,
									s.subject_num, 
									gi.group_info_num, 
									t.name, 
									t.surname, 
									gi.group_name, 
									gi.comment,
									gs.block,
									DATE_FORMAT(gs.start_date, '%d.%m.%Y') AS start_date,
									DATE_FORMAT(gs.block_date, '%d.%m.%Y') AS block_date,
								    (SELECT count(r.group_student_num) 
								    FROM review r WHERE r.group_student_num = gs.group_student_num 
								    	AND r.review_info_num != (SELECT review_info_num 
								    								FROM review_info 
								    								WHERE description = 'comment') 
								    GROUP BY r.group_student_num) c
								FROM group_info gi, 
									group_student gs, 
									subject s, 
									teacher t
								WHERE gi.subject_num = s.subject_num 
									AND gs.student_num = :student_num 
									AND gs.start_date <= CURDATE()
									AND gs.group_info_num = gi.group_info_num 
									AND gi.teacher_num = t.teacher_num
								ORDER BY gs.start_date ASC");
		$stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result = $stmt->fetchAll(); 
	    $result_count = $stmt->rowCount();

	    $stmt = $conn->prepare("SELECT count(description) c 
	    						FROM review_info 
	    						WHERE description = 'review' 
	    						GROUP BY description");
	    $stmt->execute();
	    $total_comment_number = $stmt->fetch(PDO::FETCH_ASSOC);

	    $stmt = $conn->prepare("SELECT gi.group_info_num, 
	    							gi.group_name, 
	    							DATE_FORMAT(gs.start_date, '%d.%m.%y') as start_date
	    						FROM group_info gi,
	    							group_student gs
	    						WHERE gs.student_num = :student_num
	    							AND gs.start_date > CURDATE() 
	    							AND gi.group_info_num = gs.group_info_num
	    						ORDER BY gs.start_date ASC");
	    $stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_queue_student = $stmt->fetchAll(); 

	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<td colspan='3'>
<?php if($result_count > 0) {?>
<table style='border:2px solid gray;' class='table table-striped table-bordered'>
	<tr>
		<th>#</th>
		<th>Группа</th>
		<th>Пән</th>
		<th>Мұғалім</th>
		<th>Курс:</th>
		<th>Статус</th>
	</tr>

	<?php 
		for($i = 0; $i<count($result); $i++){ 
			$alert = 'hide';
			if($result[$i]['c']=='' || $result[$i]['c']%intval($total_comment_number['c'])!=0){
				$alert='show';
			}
	?>
	<tr>
		<td>
			<?php echo $i+1;?>
			<?php if($alert=='show' && $result[$i]['subject_num']!='S5985a7ea3d0ae721486338'){ ?>
			<span class='glyphicon glyphicon-remove' style='color: red;?>'></span>
			<?php } ?>
		</td>
		<td>
			<a href="group.php?data_num=<?php echo $result[$i]['group_info_num'];?>" target='_blank'><?php echo $result[$i]['group_name'];?></a>
		</td>
		<td><?php echo $result[$i]['subject_name'];?></td>
		<td><?php echo $result[$i]['name']." ".$result[$i]['surname'];?></td>
		<td>
			<span style='font-size:10px;'>Бастауы: </span><span class='text-success'><?php echo $result[$i]['start_date'];?></span>
			<?php if($result[$i]['block']==6){?>
			<br>
			<span style='font-size:10px;'>Аяқтауы: </span><span class='text-danger'><?php echo $result[$i]['block_date'];?></span>
			<?php }?>
		</td>
		<td><?php echo $result[$i]['block']==6 ? "<p class='text-warning'>Архив</p>" : "<p class='text-success'>Активный</p>" ;?></td>
	</tr>
	<?php } ?>

</table>
<?php }else { ?>
<center><b>N/A</b></center>
<?php } ?>





<?php 
	if(!empty($result_queue_student)){
		echo "<hr>
			<table style='border:2px solid gray;' class='table table-striped table-bordered'>
				<tr>
					<th>#</th>
					<th>Группа</th>
					<th class='text-warning'>Курсты бастайтын уақыты</th>
				</tr>";
		// foreach ($result_queue_student as $value) 
		for($i = 0; $i<count($result_queue_student); $i++){
?>
	<tr>
		<td><?php echo $i+1;?></td>
		<td>
			<a href="group.php?data_num=<?php echo $result_queue_student[$i]['group_info_num'];?>" target='_blank'>
				<?php echo $result_queue_student[$i]['group_name'];?>
			</a>	
		</td>
		<td>
			<b><?php echo $result_queue_student[$i]['start_date'];?></b>
		</td>
	</tr>
<?php } echo "</table>"; } ?>
</td>