<?php
	include_once('../connection.php');

	$stmt = $conn->prepare("SELECT tpi.id,
								tpi.text
							FROM teacher_poll_info tpi
							ORDER BY tpi.text ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
?>
<form id='poll-info-form'>
	<center>
		<table style='width: 50%;' class='table table-bordered table-striped'>
		<?php
			foreach ($result as $value) {
		?>
			<tr>
				<td><input type='text' class='form-control' name='' data-name='edit-poll-info-text[]' value='<?php echo $value['text']; ?>'/></td>
				<td>
					<button type='button' class='btn btn-danger btn-xs poll-info-delete'>Delete</button>
					<button type='button' class='btn btn-primary btn-xs poll-info-cancel' style='display: none;'>Restore</button>
					<button type='button' class='btn btn-warning btn-xs poll-info-restore' style='display: none;'>Cancel</button>
					<input type="hidden" name="" data-name='delete-poll-info[]' value='<?php echo $value['id']; ?>'>
					<input type="hidden" name="" data-name='edit-poll-info[]' value='<?php echo $value['id']; ?>'>
				</td>
			</tr>
		<?php } ?>
		</table>
		<button id='add-poll-info' type='button' class='btn btn-info btn-xs'>+ Қосу</button>
		<hr>
		<button type='submit' class='btn btn-success'>Сақтау</button>
	</center>
</form>