<?php
	include_once('../connection.php');
	$result_parent_list = array();
	try {
		if(!isset($_GET['search']) || $_GET['search']==''){
			$stmt = $conn->prepare("SELECT p.parent_num, 
										p.name parent_name, 
										p.surname parent_surname, 
										p.phone, 
										s.student_num student_num, 
										s.name student_name, 
										s.surname student_surname,
										p.checked
									FROM student s, 
										parent p 
									WHERE s.student_num = p.student_num 
										AND p.parent_order = 1
										AND s.block != 1
										AND s.block != 6 
									ORDER BY parent_surname, 
											parent_name, 
											student_name, 
											student_surname asc ");
			$stmt->execute();
			$result_parent_list = $stmt->fetchAll();
			$_SESSION['result_parent_list'] = $result_parent_list;
		}
		else{
			$q = $_GET['search'];
			foreach ($_SESSION['result_parent_list'] as $val) {
				if (strpos(mb_strtolower($val['parent_name']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['parent_surname']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['phone']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['student_name']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['student_surname']), mb_strtolower($q)) !== false
					|| strpos((mb_strtolower($val['parent_surname'])."_".mb_strtolower($val['parent_name'])), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['parent_name'])."_".mb_strtolower($val['parent_surname'])), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['student_name'])."_".mb_strtolower($val['student_surname'])), mb_strtolower($q)) !== false
					|| strpos((mb_strtolower($val['student_surname'])."_".mb_strtolower($val['student_name'])), mb_strtolower($q)) !== false) {
					array_push($result_parent_list, $val);
				}
			}
		}

		$stmt = $conn->prepare("SELECT count(p.parent_num) AS c
								FROM parent p,
									student s
								WHERE p.student_num = s.student_num
									AND s.block != 6
									AND p.checked = 0
									AND p.parent_order = 1");
		$stmt->execute();
		$not_checked = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<br>
<div style='background-color:#D9534F; padding:0.1% 0.1%; color:#555; margin:0.5% 0; font-size: 11px; <?php echo $not_checked == 0 ? 'display:none;' : ''; ?>'>
	<b style='color: white;'>*Ата-аналар тексерілмеген</b>
</div>
<table class="table table-striped table-bordered">
<?php 
	$parent_number = 1;
	$parent_num = '';
	for ($i = 0; $i<count($result_parent_list); $i++) {
		if($parent_num != $result_parent_list[$i][0]){
		$parent_num = $result_parent_list[$i][0];
?>
	<tr class='row-parents-info' style='<?php echo ($result_parent_list[$i][7]==0) ? "background-color: #D9534F;" : ''; ?>'>
		<td style='width: 5%;'><center><h4><?php echo $parent_number;?></h4></center></td>
		<td style='width: 70%'>
			<div class='parent-info'>
				<table class='' style='width:100%; background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr style='width: 100%;'>
						<td style='width: 25%;'>
							<h4><?php echo $result_parent_list[$i][2]?>&nbsp;<?php echo $result_parent_list[$i][1];?></h4> 
						</td>
						<td style='width: 25%;'><h5>Телефон: <b><?php echo $result_parent_list[$i][3];?></b></h5></td>
						<td style='width: 25%;'>
							<?php
								$j = $i;
								while(isset($result_parent_list[$j][0]) && $result_parent_list[$i][0]==$result_parent_list[$j][0]){
							?>
							<div class='single-student' style='border:1px solid lightgray; border-radius: 5px; padding:2% 4%;'>
								<span style='overflow:hidden;'><?php echo $result_parent_list[$j][5]." ".$result_parent_list[$j][6]; ?></span>
							</div>
							<?php $j++; } ?>
						</td>
					</tr>
				</table>				
			</div>
		</td>
		<td style='width: 25%'>
			<?php
				if ($result_parent_list[$i][7] == 1) {
			?>
			<center><p class='text-success'>Тексерілді</p></center>
			<?php } else { ?>
			<center>
				<form class='parent-checked'>
					<input type="hidden" name="pid" value="<?php echo $result_parent_list[$i][0]; ?>">
					<input type="submit" class='btn btn-xs btn-warning' value='Тексерілді'>
				</form>
			</center>
			<?php } ?>
		</td>
	</tr>
	<?php $parent_number++; }} ?>
</table>