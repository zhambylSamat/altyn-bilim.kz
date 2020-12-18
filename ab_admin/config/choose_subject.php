<?php
	include_once('../../connection.php');
	try {
		$stmt_subject = $conn->prepare("SELECT * FROM subject order by subject_name");
	    $stmt_subject->execute();
	    $result_subject = $stmt_subject->fetchAll();
	} catch (PDOException $e) {
		throw $e;
	}
?>
<table class='table table-striped'>
	<tr>
		<th>Пәнді таңдаңыз</th>
	</tr>
	<?php
		foreach ($result_subject as $value) {
			echo "<tr><td><a role='button' class='config' data-type='subject' data-num='".$value['subject_num']."'>".$value['subject_name']."</a></td></tr>";
		}
	?>
</table>