<?php
include('../connection.php');
if(!isset($_SESSION['parent_phone'])){
	header('location:signin.php');
}
try {
	$stmt = $conn->prepare("SELECT s.student_num, s.name, s.surname
							FROM parent p,
								student s
							WHERE p.student_num = s.student_num
								AND p.phone = :phone
								AND s.block != 6");
	$stmt->bindParam(':phone', $_SESSION['parent_phone'], PDO::PARAM_STR);
	$stmt->execute();
	$result_child = array();
	if($stmt->rowCount()==1){
		$result_child = $stmt->fetch(PDO::FETCH_ASSOC);
		header('Location:student_info.php?data_num='.$result_child['student_num']);
	}
	else{
		$result_child = $stmt->fetchAll();
	}
} catch (PDOException $e) {
	echo "Error : ".$e->getMessage()." !!!";
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn-bilim.kz</title>
	<?php include_once('style.php');?>
</head>
<body>

<?php include_once('nav.php');?>
<section>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<?php 
						foreach ($result_child as $value) {
					?>
					<h1><a href="student_info.php?data_num=<?php echo $value['student_num'];?>"><?php echo $value['surname']." ".$value['name']; ?></a></h1>
					<?php } ?>
				</center>
			</div>
		</div>
	</div>
</section>

<?php 
// include_once('js.php');
?>
<script type="text/javascript">
</script>
</body>
</html>