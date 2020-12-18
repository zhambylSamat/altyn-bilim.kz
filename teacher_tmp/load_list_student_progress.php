<?php
	include('../connection.php');
	$student_num = $_GET['sn'];
	$subject_num = $_GET['sjn'];
	try {
		$stmt = $conn->prepare("SELECT t.topic_name,
									sp.student_progress_num,
									st.subtopic_num,
									st.subtopic_name, 
								    sp.progress
									FROM subject s
								    	INNER JOIN topic t 
								        	ON t.subject_num = s.subject_num
								        INNER JOIN subtopic st 
								        	ON st.topic_num = t.topic_num
								        LEFT JOIN student_progress sp 
								        	ON sp.subtopic_num = st.subtopic_num
								            	AND sp.student_num = :student_num
								    WHERE s.subject_num = :subject_num
								    ORDER BY t.topic_order, st.subtopic_order ASC");
		$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
		$result_progress = array();
		foreach ($result as $key => $value) {
			$topic_name = str_replace(" ", "_", $value['topic_name']);
			$topic_name = str_replace(".", "",$topic_name);
			$topic_name = str_replace(",", "",$topic_name);
			$result_progress[$topic_name][$value['subtopic_num']]['name']  = $value['subtopic_name'];
			$result_progress[$topic_name][$value['subtopic_num']]['num']  = $value['student_progress_num'];
			$result_progress[$topic_name][$value['subtopic_num']]['progress']  = floatval($value['progress']);
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<center>
	<table class='table table-bordered table-progress'>
		<?php 
			$topic_num = '';
			$count = 0;
			foreach ($result_progress as $key_topic => $value_topic) { 
				$count_subtopic = 1;
				$count++;
		?>
		<tr class='head-topic <?php echo ($count%2==0) ? "even-tr" : "odd-tr";?>' data-name='<?php echo $key_topic;?>'>
			<td colspan='6'><b><a class='st-lists' style='cursor:pointer;'><?php echo str_replace("_"," ",$key_topic); ?></a></b></td>
		</tr>
		<tr class='head-topic <?php echo ($count%2==0) ? "even-tr" : "odd-tr";?>' id='<?php echo $key_topic; ?>' data-name='<?php echo $key_topic;?>' style='display:none;'>
			<td rowspan="<?php echo count($value_topic)+2;?>">
				<b><a class='st-lists' style='cursor:pointer;'><?php echo str_replace("_"," ",$key_topic);?></a></b>
			</td>
		</tr>
		<tr class='even-tr' data-name='<?php echo $key_topic; ?>' style='display:none;'>
			<th><center>Сабақ тақырыбы</center></th>
			<th><center>Прогресс</center></th>
		</tr>
		<?php
			$count2 = 0; 
			foreach($value_topic as $key_st => $value_st){ 
				$count2++;
		?>
		<tr class='body-topic <?php echo ($count2%2==0)?"even-tr":"odd-tr";?>' data-name='<?php echo $key_topic; ?>' style='display:none;'>
			<td>
				<?php echo ($count_subtopic++).". ".$value_st['name'];?>	
			</td>
			<?php
				$style = '';
				if($value_st['progress']==0){
					$style = "red";
				}
				else if($value_st['progress']==0.5){
					$style = 'orange';
				}
				else if($value_st['progress']==1){
					$style = "green";
				}
			?>
			<td>
				<center>
					<h3 style='color:<?php echo $style; ?>; margin:0; padding:0;'><b><?php echo $value_st['progress'];?></b></h3>
				</center>
			</td>
		</tr>
		<?php
			// if($value_st['num']!='' && ($value_st['video']!=0 || $value_st['class']!=0 || $value_st['home']!=0)){
			if($value_st['num']!='' && $value_st['progress']!=0){
				$topic_num = $key_topic;
			}
		?>
		<?php } ?>
		<?php } ?>
	</table>
</center>
<script type="text/javascript">
	$count = 0;
	$(document).ready(function(){
		$count = 1;
		if("<?php echo $topic_num; ?>"!=''){
			console.log("<?php echo $topic_num;?>"+"asdfasdfasdf");
			$("tr[data-name=<?php echo $topic_num; ?>]").toggle('fast',function(){
				location.href = "#<?php echo $topic_num; ?>";
			});
		}
	});
</script>