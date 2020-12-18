<?php
	include('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT ri.review_info_num review_info_num, ri.review_text review_text, ri.description description, r.review_num review_num, r.status status 
									FROM review_info ri
										LEFT JOIN review r 
									    	ON r.review_info_num = ri.review_info_num
									        	AND r.group_student_num = :group_student_num
									ORDER BY ri.description DESC, ri.review_text ASC");
		$stmt->bindParam(':group_student_num', $_GET['gin'], PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<form onsubmit='return confirm("Подтвердите действие!");' class='form-inline' method='post' action='teacher_controller.php'>
<center>
<?php foreach ($result as $value) { 
	if($value['description']=='review'){
?>
<div class='form-group'>
	<table>
		<tr>
			<td style='width: 50%;'><label for='review-status' class='pull-right'><?php echo substr($value['review_text'],2); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
			<td style='width: 50%;'>
				<span class='pull-left'><input type="number" id='review-status' class="form-control" min='1' max='10' step='1' required="" name="<?php echo ($value['status']!=null) ? "old_status[]" : "new_status[]";?>" value="<?php echo ($value['status']!=null) ? intval($value['status']) : ''; ?>">
				<input type="hidden" name="<?php echo ($value['status']!=null) ? "old_rin[]" : "new_rin[]";?>" value='<?php echo $value['review_info_num'];?>'><span class='h4'><b>&nbsp;/&nbsp;10</b></span></span>
			</td>
		</tr>
	</table>
</div>
<br>
<?php 
	}else if($value['description']=='comment'){
?>
	<hr>
	<table class='table table-borderd'>
		<tr>
			<td style='width: 50%;'><center><b>Мысалы:</b><div style='border:1px solid lightgray; padding:5px;'><?php echo ($value['review_text']=='') ? "N/A" : nl2br($value['review_text']);?></div></center></td>
			<td style='width: 50%;'>
				<textarea cols='50' rows='10' class='form-control' name='<?php echo ($value['status']!=null) ? "old_review_comment" : "new_review_comment";?>' placeholder="<?php echo $value['review_text'];?>"><?php echo $value['status'];?></textarea>
				<input type="hidden" name="rc" value='<?php echo $value['review_info_num'];?>'>
			</td>
		</tr>
	</table>
<?php
	}} 
?>
<input type="hidden" name="gin" value='<?php echo $_GET['gin'];?>'>
<input type="hidden" name="data_num" value='<?php echo $_GET['data_num'];?>'>
<input type="submit" class='btn btn-success btn-sm' name="submit_review_for_student" value='Сақтау'>
<input type="reset" class='btn btn-sm btn-warning' name="" value='Отмена'> 
</center>
</form>