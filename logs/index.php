<!DOCTYPE html>
<html>
<head>
	<title>Logs Altyn Bilim</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>
<body>

	<?php
		include_once('../academy/common/connection.php');

		$query = "SELECT s.last_name,
						s.first_name,
						s.phone,
						sll.user_agent,
						sll.client_ip,
						DATE_FORMAT(sll.created_date, '%d.%m.%Y %H:%i:%s') AS login_time
					FROM student_login_log sll,
						student s
					WHERE s.id = sll.student_id
					ORDER BY sll.created_date DESC";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$query_result = $stmt->fetchAll();
	?>

	<div class='container'>
		<div class='row'>
			<table class='table table-striped table-bordered' style='margin-top: 10px;'>
			<?php foreach ($query_result as $value) { ?>
				<tr>
					<td><?php echo $value['last_name'].' '.$value['first_name']; ?></td>
					<td><?php echo $value['phone']; ?></td>
					<td><?php echo $value['user_agent']; ?></td>
					<td><?php echo $value['client_ip']; ?></td>
					<td><?php echo $value['login_time']; ?></td>
				</tr>
			<?php } ?>
			</table>
		</div>
	</div>

</body>
</html>