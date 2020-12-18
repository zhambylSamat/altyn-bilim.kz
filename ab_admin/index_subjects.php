<?php
	try {

		$stmt = $conn->prepare("SELECT s.subject_num subject_num, 
									s.subject_name subject_name, 
									t.topic_num topic_num, 
									t.topic_name topic_name, 
									st.subtopic_num subtopic_num, 
									st.subtopic_name subtopic_name,
									t.quiz quiz
								FROM subject s
									LEFT JOIN topic t 
										ON s.subject_num = t.subject_num
									LEFT JOIN subtopic st 
										ON t.topic_num = st.topic_num
								ORDER BY s.subject_name, 
									t.topic_order, 
									st.subtopic_order ASC");

		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage()." !!!";
	}
?>

<ol>
	<?php
		$subject_num = '';
		$topic_num = '';
		$subject_count = 0;
		$topic_count = 0;
		$count = 0;
		foreach ($result as $value) {
	?>
		<?php
			if($subject_num!=$value['subject_num']){
				if($subject_count!=0){
					echo "</ol>";
				}
		?>
		<li>
			<div>
				<?php if(!isset($result[$count][2]) || $result[$count][2]==null){ ?>
				<h3 style='display: inline-block;'><?php echo $value['subject_name'];?></h3>
				<?php }else{ ?>
				<h3 class='data-list' style='display: inline-block;'><a style='cursor: pointer;'><?php echo $value['subject_name'];?></a></h3>
				<a href="test.php?<?php echo "data_num=".$value['subject_num'];?>" class='btn btn-info btn-sm'>&nbsp;&nbsp; <span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;</a>
				<?php } ?>
			</div>
			<ol style='display: none;' type='I'>
		<?php } $subject_num = $value['subject_num']; $subject_count++; ?>
		<?php
			if($topic_num!=$value['topic_num']){
				if($topic_count!=0){
					echo "</ul>";
				}
		?>
			<li>
				<div>
					<?php if($value['quiz']=='y'){ ?>
					<h4 class='text-warning'><?php echo "<b>Аралық бақылау:</b><br> ".nl2br($value['topic_name']); ?></h4>
					<?php }else if(!isset($result[$count][5]) || $result[$count][5]==null){ ?>
					<h4><?php echo $value['topic_name']; ?></h4>
					<?php }else{ ?>
					<h4 class='data-list'><a style='cursor: pointer;'><?php echo $value['topic_name']; ?></a></h4>
					<?php } ?>
				</div>
				<ul style='display: none;'>
		<?php } $topic_num = $value['topic_num']; $topic_count++; ?>
		<li><h5><?php echo $value['subtopic_name']; ?></h5></li>
	<?php $count++; } ?>
</ol>