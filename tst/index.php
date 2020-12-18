<?php
	include_once('../connection.php');
	if(!isset($_SESSION['tst_number'])){
		header('location:signin.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>TEST</title>
	<?php include_once('style.php');?>
</head>
<body>
	<?php include_once('nav.php');?>

	<?php
		$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name subject_name
									FROM subject s, topic t 
									WHERE t.subject_num = s.subject_num 
										AND t.quiz = 'n' 
											GROUP BY s.subject_num, s.subject_name 
											ORDER BY s.subject_name ASC");
		$stmt->execute();
		$subject_list = $stmt->fetchAll();
	?>
	<div class='container'>
		<div class='row'>
			<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12'>
				<ol>
					<?php foreach ($subject_list as $value) {?>
					<li>
						<a href="video.php?data_num=<?php echo $value['subject_num'];?>" class='btn btn-md btn-info' style='width: 50%; margin:1%'><?php echo $value['subject_name'];?></a>
					</li>
					<?php } ?>
				</ol>		
			</div>
		</div>
	</div>

	<?php include_once('js.php');?>
</body>
</html>