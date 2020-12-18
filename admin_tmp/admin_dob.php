<?php 
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'dob';
	}
	try {
		$stmt = $conn->prepare("SELECT name, 
									surname, 
									DATE_FORMAT(dob, '%d.%m.%Y') as dob,
									CASE WHEN DATE_FORMAT(DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '%m-%d') >= DATE_FORMAT(dob, '%m-%d') THEN 1 ELSE 0 END  AS coming 
								FROM student
								WHERE DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%m-%d')
									AND block != 6
									AND dob != '0000-00-00'
								ORDER BY MONTH(dob), DAY(dob) ASC");
		$stmt->execute();
		$future_dob = $stmt->fetchAll();

		$stmt = $conn->prepare("SELECT name, 
									surname, 
									DATE_FORMAT(dob, '%d.%m.%Y') as dob
								FROM student
								WHERE DATE_FORMAT(dob, '%m-%d') < DATE_FORMAT(CURRENT_DATE, '%m-%d')
									AND block != 6
								    AND dob != '0000-00-00'
								ORDER BY MONTH(dob) ASC, DAY(dob) ASC");	
		$stmt->execute();
		$past_dob = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div id='coming_dob' style='background-color:#5CB85C; padding:0.1% 0.1%; color:#333; margin:0.5% 0; font-size: 11px; display: none;'>
	<i>Алдағы 10 күндегі оқушылардың <b>"туған күн"</b> тізімдері</i>
</div>
<table class='table table-bordered table-striped'>
	<tr>
		<th><center>#</center></th>
		<th>Тегі</th>
		<th>Есімі</th>
		<th>Туылған күні</th>
	</tr>
	<?php 
		$count = 0;
		$coming_count = 0;
		foreach ($future_dob as $value) {
			$color = '';
			if($value['coming']==1){
				$coming_count++;
				$color='#5CB85C';
			}
	?>
	<tr style='<?php echo "background-color:".$color.";"; ?>'>
		<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
		<td><?php echo $value['surname'];?></td>
		<td><?php echo $value['name'];?></td>
		<td><?php echo $value['dob'];?></td>
	</tr>
	<?php } ?>
	<?php 
		foreach ($past_dob as $value) {
	?>
	<tr>
		<td style='width: 5%;'><center><?php echo ++$count;?></center></td>
		<td><?php echo $value['surname'];?></td>
		<td><?php echo $value['name'];?></td>
		<td><?php echo $value['dob'];?></td>
	</tr>
	<?php } ?>
</table>
<script type="text/javascript">
	$(document).ready(function(){
		if(<?php echo $coming_count;?>>0){
			$("#coming_dob").show();
		}
	});
</script>