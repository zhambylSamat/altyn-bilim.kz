<?php
	include('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT ri.review_info_num review_info_num, ri.review_text review_text, ri.description description, r.review_num review_num, r.status status 
									FROM review_info ri
										LEFT JOIN review r 
									    	ON r.review_info_num = ri.review_info_num
									        	AND r.group_student_num = (SELECT group_student_num 
									        									FROM group_student 
									        									WHERE group_info_num = :group_info_num 
									        										AND student_num = :student_num)
									ORDER BY ri.description DESC, ri.review_text ASC");
		$stmt->bindParam(':group_info_num', $_GET['gin'], PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $_GET['sn'], PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<table class='table table-striped table-bordered'>
	<?php 
		foreach ($result as $value) { 
			if($value['description']=='review'){
	?>
	<tr>
		<td style='width: 50%;'><p class='pull-right'><?php echo substr($value['review_text'],2);?></p></td>
		<td style='width: 50%;'><b><?php echo ($value['status']==null) ? "-" : $value['status'];?> / 10</b></td>
	</tr>
	<?php
	}else if($value['description']=='comment'){ 
	?>
	<tr>
		<td><p class='pull-right'><b>Түсініктеме: </b></p></td>
		<td><?php echo ($value['status']==null) ? "<b>-</b>" : nl2br($value['status']);?></td>
	</tr>
	<?php
	}}
	?>
</table>