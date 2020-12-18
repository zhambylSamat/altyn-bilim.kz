<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'parent';
	}
?>
<hr>
<?php 
	try {
		$stmt = $conn->prepare("SELECT s.surname, s.name FROM student s where s.student_num not in (select p.student_num from parent p) AND s.student_num != 'US5985cba14b8d3100168809' AND block != 1 AND block != 6");
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
	if(count($result)>0){
?>
	<b><h4>Ата-анасы енгізілмеген оқушылар.</h4></b>
	<ol>
	<?php foreach ($result as $value) {?>
		<li><?php echo "<b>".$value['surname']." ".$value['name']."</b>";?></li>
	<?php } ?>
	</ol>
<?php } ?>
<input type="text" name="search" data-name='parent' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
<div class='parents'>
	<?php include_once('index_parents.php'); ?>
</div>