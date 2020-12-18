<?php
	include_once('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT * FROM suggestion WHERE user_num = :user_num ORDER BY status ASC");
		$stmt->bindParam(':user_num', $_SESSION['adminNum'], PDO::PARAM_STR);
		$stmt->execute();

		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
?>
<div id='add-suggestion'>
	<a class='btn btn-xs btn-info' id='suggestion'>+ Ұсыныс енгізу</a>
	<form id='suggestion-form' method='post' style='display:none;'>
		<textarea class='form-control' name='suggestion_text' placeholder="Ұсыныс мәтіні"></textarea>
		<center>
			<input type="submit" name="" class='btn btn-sm btn-success' value='Сақтау'>
			<input type="reset" id='suggestion-cancel' class='btn btn-sm btn-warning' name="" value='Отмена'>
		</center>
	</form>
</div>
<hr>
<div id='submitted-suggestion'>
	<h4 class='text-info'>Менің ұсыныстарым</h4>
	<table class='table table-bordered table-striped'>
	<?php 
		$stop = 0;
		$count = 0;
		for ($i=0; $i<count($result); $i++) { 
			if($result[$i]['status']==0){
				$stop = $i+1;
				$count++;
	?>
		<tr>
			<td style='width:90%; border:1px solid #ddd !important;'>
				<h4 class='suggestion-text' style='font-family: "Times New Roman", Times, serif; '><?php echo nl2br($result[$i]['text']);?></h4>
				<form class='suggestion-form-edit' metho='post' style='display: none;'>
					<textarea class='form-control' name='suggestion_text' placeholder="Ұсыныс мәтіні"><?php echo $result[$i]['text']; ?></textarea>
					<center>
						<input type="submit" name="" class='btn btn-sm btn-success' value='Сақтау'>
						<input type="hidden" name="sid" value='<?php echo $result[$i]['suggestion_id'];?>'>
						<input type="reset" class='suggestion-edit-cancel btn btn-sm btn-warning' name="" value='Отмена'>
					</center>		
				</form>
			</td>
			<td style='width:10%; border:1px solid #ddd !important;'>
				<a style='width: 100%;' class='btn btn-sm btn-warning suggestion-edit'>Өзгерту</a>
				<a style='width: 100%;' class='btn btn-sm btn-danger suggestion-delete'>Өшіру</a>
			</td>
		</tr>
	<?php }else { $stop = $i; break; }}  ?>
	<?php 
		if($count==0){
			echo "<tr><td colspan='2'>N/A</td></tr>";
		}
	?>
	</table>
	<hr>
	<h4 class='text-success'>Қабылданған ұсыныстар</h4>
	<table class='table table-bordered table-striped'>
	<?php 
		$count = 0;
		for ($i=$stop; $i<count($result); $i++) { 
			if($result[$i]['status']==1){
				$stop = $i+1;
				$count++;
	?>
		<tr>
			<td style='border:1px solid #ddd !important;'>
				<h4 class='suggestion-text' style='font-family: "Times New Roman", Times, serif; '><?php echo nl2br($result[$i]['text']);?></h4>
			</td>
		</tr>
	<?php }else { $stop = $i; break; }}  ?>
	<?php
		if($count==0){
			echo "<tr><td>N/A</td></tr>";
		}
	?>
	</table>
	<hr>
	<button class='btn btn-sm btn-success' id='implementedSuggestion'>Орындалған ұсыныстар&nbsp;&nbsp;&nbsp;<span class='badge'><?php echo (count($result)-$stop);?></span></button>
	<div id='implementedSuggestionBox' style='display: none;'>
		<table class='table table-bordered table-striped'>
		<?php 
			$count = 0;
			for ($i=$stop; $i<count($result); $i++) { 
				if($result[$i]['status']==2){
					$count++;
		?>
			<tr>
				<td style='border:1px solid #ddd !important;'>
					<h4 class='suggestion-text' style='font-family: "Times New Roman", Times, serif; '><?php echo nl2br($result[$i]['text']);?></h4>
				</td>
			</tr>
		<?php }else { $stop = $i; break; }}  ?>
		<?php
			if($count==0){
				echo "<tr><td>N/A</td></tr>";
			}
		?>
		</table>
	</div>
</div>
<hr>
<div id='accept-suggestion'>
	
</div>