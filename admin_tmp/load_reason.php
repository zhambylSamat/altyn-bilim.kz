<?php
	include_once('../connection.php');
?>
<div class='row'>
	<div class='col-md-12 col-sm-12'>
		<center>
			<form class='form-inline' method='post' action='admin_controller.php'>
				<div class='form-class'>
					<?php
						$count = 0;
						try {
							$stmt = $conn->prepare("SELECT * FROM reason_info ORDER BY reason_text ASC");
							$stmt->execute();
							$result_reason = $stmt->fetchAll();
						} catch (PDOException $e) {
							echo "Error: " . $e->getMessage();
						}
					?>
					<?php foreach($result_reason as $value){ ?>
					<div class='form-group' style='display: block;'>
						<label><b><?php ++$count; ?></b></label>
						<input type="text" name="reason" class='form-control input_reason_text' data-toggle="tooltip" data-placement="left" title="<?php echo $value['reason_text']; ?>" value='<?php echo $value['reason_text']; ?>' required="" placeholder="М: Ауырып калдым.">
						&nbsp;&nbsp;
						<a class="btn btn-sm btn-danger" data-action-reason="remove" name="">Удалить</a>
						<a style="display:none;" class="btn btn-sm btn-primary" data-action-reason="restore" name="">Восстановить</a>
						&nbsp;&nbsp;
						<a style="display:none;" class="btn btn-sm btn-warning" data-action-reason="reset" name="">Отмена</a>
						<input type="hidden" name="rin[]" value='<?php echo $value['reason_info_num'];?>'>
					</div>
					<?php } ?>
				</div>
				<br>
				<a style='width: 10%; border:1px solid lightgray' class='btn btn-sm add-row-reason'><b>+</b></a>
				<hr>
    			<input type="submit" class='btn btn-sm btn-success' name="reason_for_student" value='Сақтау'>
			</form>
		</center>
	</div>
</div>