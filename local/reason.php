<?php
	include_once('../connection.php');
	// echo $_SESSION['student_num']."<br>";
	if(isset($_SESSION['student_num']) && isset($_SESSION['access']) && $_SESSION['access']==md5('true')){
		header('location:index.php');
	}
	else if(!isset($_SESSION['student_num'])){
		header('location:signin.php');
	}
	

	$stmt = $conn->prepare("SELECT * FROM reason_info ORDER BY reason_text ASC");
	$stmt->execute();
	$reason_result = $stmt->fetchAll();
	$absents_arr = $_SESSION['reason'];
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('style.php');?>
	<link rel="stylesheet" type="text/less" href="css/style.less">
</head>
<body>
	<?php include_once('nav.php');?>
	<div class='container'>
		<center>
			<h3><b>Сабаққа келмеу себебі.</b></h3>
			<h2 style='color:red;'>** Жауап беру міндетті!!!</h2>
			<form method='post' action='controll-user.php'>
			<?php
				$count = 0;
				foreach ($absents_arr as $key => $value) {
					echo "<hr style='border:1px solid gray; border-radius:5px;'>";
					echo "<h3>".$value['subject_name']."</h3>";
					echo "<table class='table table-striped table-bordered'>";
					foreach ($value['group'] as $key => $value) {
						echo "<tr><td colspan='2'><center><b>".$value['group_name']."</b></center></td></tr>";
						foreach ($value['data'] as $key => $value) {
							echo "<tr>";
							echo "<td><center><h3><b>".$value."</b></h3></center></td>";
							echo "<td>";
							echo "<input type='hidden' name='psn[".$count."]' value='".$key."'>";
							$inner_count=0;
							foreach ($reason_result as $value) {
								$inner_count++;
			?>
				<div class="radio">
				  <label>
				    <input type="radio" name="<?php echo 'reason['.$count.']';?>" id="optionsRadios1" value="<?php echo $value['reason_info_num'];?>" <?php echo ($inner_count==1) ? "required" : ""?>>
				    <span class='h4'><?php echo substr($value['reason_text'], 2);?></span>
				  </label>
				</div>
			<?php
							}
							echo "</td>";
							echo "</tr>";
							$count++;
						}
					}
					echo "</table>";
				}
			?>
			<input type="submit" class='btn btn-sm btn-success' name="submit_reason">
			</form>
		</center>
	</div>
</div>
</body>
</html>