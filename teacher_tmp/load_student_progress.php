<?php
	include('../connection.php');
	$student_num = $_GET['sn'];
	$subject_num = $_GET['sjn'];
	$student_name = $_GET['name'];
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
			$topic_name = preg_replace('/\s+/u', "_", $value['topic_name']);
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
<ol class="breadcrumb">
	<li><a class='back' style='cursor:pointer;'>Студенттер тізмі</a></li>
	<li class="active"><?php echo str_replace("_", " ", $student_name);?></li>
	<?php
		if($subject_num == 'S59848243b8350348070654'){
	?>
	<li class='pull-right'>
		<a class='btn btn-xs btn-primary static-subject-btn' data-sjn='S5985a7ea3d0ae721486338' data-sn='<?php echo $student_num; ?>' data-name='<?php echo $student_name; ?>'>Геометрия</a>
	</li>
	<?php } else if($subject_num == 'S5985a7ea3d0ae721486338'){ ?>
	<li class='pull-right'>
		<a class='btn btn-xs btn-primary static-subject-btn' data-sjn='S59848243b8350348070654' data-sn='<?php echo $student_num; ?>' data-name='<?php echo $student_name; ?>'>Алгебра</a>
	</li>
	<?php } ?>
</ol>
<center>
	<table class='table table-bordered table-progress'>
		<?php 
			$topic_num = '';
			$count = 0;
			$id = "topic_";
			foreach ($result_progress as $key_topic => $value_topic) { 
				$count_subtopic = 1;
				$count++;
				$id .= $count;
		?>
		<tr class='head-topic <?php echo ($count%2==0) ? "even-tr" : "odd-tr";?>' data-name='<?php echo $id;?>'>
			<td colspan='6'><b><a class='st-lists' style='cursor:pointer;'><?php echo str_replace("_"," ",$key_topic); ?></a></b></td>
		</tr>
		<tr class='head-topic <?php echo ($count%2==0) ? "even-tr" : "odd-tr";?>' id='<?php echo $id; ?>' data-name='<?php echo $id;?>' style='display:none;'>
			<td rowspan="<?php echo count($value_topic)+2;?>">
				<b><a class='st-lists' style='cursor:pointer;'><?php echo str_replace("_"," ",$key_topic);?></a></b>
			</td>
		</tr>
		<tr class='even-tr' data-name='<?php echo $id; ?>' style='display:none;'>
			<th><center>Сабақ тақырыбы</center></th>
			<th><center>Прогресс</center></th>
			<!-- <th></th> -->
		</tr>
		<?php
			$count2 = 0; 
			foreach($value_topic as $key_st => $value_st){ 
				$count2++;
		?>
		<tr class='body-topic <?php echo ($count2%2==0)?"even-tr":"odd-tr";?>' data-name='<?php echo $id; ?>' style='display:none;'>
			<td>
				<?php echo ($count_subtopic++).". ".$value_st['name'];?>	
			</td>
			<?php
				$class = '';
				if($value_st['progress']==0){
					$class = "btn-danger";
				}
				else if($value_st['progress']==0.5){
					$class = 'btn-warning';
				}
				else if($value_st['progress']==1){
					$class = "btn-success";
				}
			?>
			<!-- <td>
				<center>
					<a title='Прогресс' class='progress-btn btn btn-xs <?php echo $class;?>' style='padding:0 15px 0 15px;' data-name='progress' data-type='<?php echo $value_st['progress'] ;?>'><?php echo $value_st['progress'];?></a>
				</center>
			</td> -->
			<td>
				<center>
					<form class='student_progress_list' method='post'>
						<input type="hidden" name="progress_progress" value='<?php echo $value_st['progress'];?>'>
						<input type="hidden" name="tmp_count_subtopic" value='<?php echo $count_subtopic;?>'>
						<input type="hidden" name="tmp_subtopic_name" value='<?php echo $value_st['name'];?>'>
						<input type="hidden" name="id" value="<?php echo ($value_st['num']!='') ? $value_st['num'] : 'new';?>">
						<input type="hidden" name="stn" value="<?php echo $key_st; ?>">
						<input type="hidden" name="stdnum" value="<?php echo $student_num;?>">
						<input type="submit" class='progress-btn load-btn btn btn-xs <?php echo $class;?>' style='padding:0 15px 0 15px;' data-name='progress' data-type='<?php echo $value_st['progress']?>' value='<?php echo $value_st['progress']; ?>'>
						<input type="image" src='../img/loader.gif' style='display:none; width: 10%;'>
						<!-- <input type="submit" class='btn btn-xs btn-success' value='Сақтау'> -->
					</form>
				</center>
			</td>
		</tr>
		<?php
			if($value_st['num']!='' && $value_st['progress']!=0){
				// $topic_num = $key_topic;
				$topic_num = $id;
			}
		?>
		<?php } ?>
		<?php } ?>
	</table>
</center>
<center><button class='btn btn-sm btn-success save-btn-disable ok' data-num='<?php echo $student_num;?>'>Сақтау</button></center>
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