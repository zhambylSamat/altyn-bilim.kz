<?php
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT s.suggestion_id, 
									s.text, 
									s.status, 
									s.last_changed_date,
								    t.teacher_num,
									t.name t_name, 
									t.surname t_surname,
								    a.admin_num,
									a.name a_name,
									a.surname a_surname
								FROM suggestion s
								LEFT JOIN teacher t 
									ON t.teacher_num = s.user_num
								LEFT JOIN admin a
									ON a.admin_num = s.user_num
								ORDER BY s.status, 
									s.last_changed_date DESC");
		$stmt->execute();
		$result = $stmt->fetchAll();	
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<div>
	<h4><b>Жаңа ұсыныстар</b></h4>
	<form method='post' id='suggestion-wating-form'>
		<table class='table table-bordered table-striped'>
			<?php 
				$stop = 0;
				$count = 0;
				for ($i=0; $i < count($result); $i++) { 
					if($result[$i]['status']==0){
						$stop = $i+1;
						$count++;
			?>
			<tr>
				<td style='width: 1%;'>
					<center>
						<input type="checkbox" class='suggestion-checkbox' name="sid[]" value='<?php echo $result[$i]['suggestion_id'];?>'>
					</center>
				</td>
				<td>
					<b><?php echo ($result[$i]['teacher_num']!="" ? $result[$i]['t_surname']." ".$result[$i]['t_name'] : $result[$i]['a_surname']." ".$result[$i]['a_name']).": ".date('d.m.Y', strtotime($result[$i]['last_changed_date']));?></b>
					<p id='text' style='font-family: "Times New Roman", Times, serif; font-size:15px;'><?php echo nl2br($result[$i]['text']);?></p>
				</td>
			</tr>
			<?php } else { $stop = $i; break; }} ?>
		</table>
		<?php
			if($count==0){
				echo "<center><h4>N/A</h4></center>";
			}
			else {
		?>
		<center>
			<input type="submit" class='btn btn-sm btn-success' value='Қабылдау'>
			<a class='btn btn-sm btn-danger btn-suggestion-waiting-reject'>Өшіру</a>
		</center>
		<?php } ?>
	</form>
	<hr>
	<h4 class='text-success'><b>Қабылданған ұсыныстар</b></h4>
	<form method='post' id='suggestion-accepted-form'>
		<table class='table table-bordered table-striped'>
			<?php 
				$count = 0;
				for ($i=$stop; $i < count($result); $i++) { 
					if($result[$i]['status']==1){
						$stop = $i+1;
						$count++;
			?>
			<tr>
				<td style='width: 1%;'>
					<center>
						<input type="checkbox" class='suggestion-checkbox' name="sid[]" value='<?php echo $result[$i]['suggestion_id'];?>'>
					</center>
				</td>
				<td>
					<b><?php echo ($result[$i]['teacher_num']!="" ? $result[$i]['t_surname']." ".$result[$i]['t_name'] : $result[$i]['a_surname']." ".$result[$i]['a_name']);?></b>
					<p id='text' style='font-family: "Times New Roman", Times, serif; font-size:15px;'><?php echo nl2br($result[$i]['text']);?></p>
				</td>
			</tr>
			<?php } else {$stop = $i; break; }} ?>
		</table>
		<?php
			if($count==0){
				echo "<center><h4>N/A</h4></center>";
			}
			else {
		?>
		<center>
			<input type="submit" class='btn btn-sm btn-success' value='Орындалды'>
			<a class='btn btn-sm btn-danger btn-suggestion-accepted-reject'>Өшіру</a>
		</center>
		<?php } ?>
	</form>

	<hr>
	<button class='btn btn-sm btn-success' id='implementedSuggestion'>Орындалған ұсыныстар&nbsp;&nbsp;&nbsp;<span class='badge'><?php echo (count($result)-$stop);?></span></button>
	<div id='implementedSuggestionBox' style='display: none;'>
		<table class='table table-bordered table-striped'>
			<?php 
				$count = 0;
				for ($i=$stop; $i < count($result); $i++) { 
					if($result[$i]['status']==2){
						$count++;
			?>
			<tr>
				<td>
					<b>
						<?php 
							$date = new DateTime(date("Y-m-d"));
							$date2 = new DateTime(date("Y-m-d", strtotime($result[$i]['last_changed_date'])));
							$dt = $date->format('Y-m-d');
							$dt2 = $date2->format('Y-m-d');
							$interval = date_diff(date_create($dt2), date_create($dt));
							echo ($result[$i]['teacher_num']!="" ? $result[$i]['t_surname']." ".$result[$i]['t_name'] : $result[$i]['a_surname']." ".$result[$i]['a_name']).":	<span class='text-warning'>".(30-intval($interval->format("%a")))." кун калды</span>";
							?>
					</b>
					<p id='text' style='font-family: "Times New Roman", Times, serif; font-size:15px;'><?php echo nl2br($result[$i]['text']);?></p>
				</td>
			</tr>
			<?php } else {$stop = $i; break; }} ?>
		</table>
		<?php
			if($count==0){
				echo "<center><h4>N/A</h4></center>";
			}
			else {
		?>
		<?php } ?>
	</div>
</div>