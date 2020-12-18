
<?php 
	try {
		$stmt = $conn->prepare("SELECT * FROM subject order by subject_name asc");
	     
	    $stmt->execute();
	    $result_subject = $stmt->fetchAll(); 
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<?php
	$content_name = array();
	$subject_arr = array();
	$topic_arr = array();
	$quiz_arr = array();
	$subtopic_arr = array();
	foreach($result_subject as $readrow_subject){
		$content_name[$readrow_subject['subject_num']] = $readrow_subject['subject_name'];
		array_push($subject_arr, $readrow_subject['subject_num']);
	}

	try {
		$stmt_topic = $conn->prepare("SELECT * FROM topic order by topic_order asc");
   		$stmt_topic->execute();
    	$result_topic = $stmt_topic->fetchAll(); 
    	// print_r($result_topic);
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage()." !!!";
	}

    foreach($result_topic as $readrow_topic){
    	$content_name[$readrow_topic['topic_num']] = $readrow_topic['topic_name'];
    	$topic_arr[$readrow_topic['topic_num']] = $readrow_topic['subject_num']; 
    	// if($readrow_topic['quiz']=='y'){
    	// 	$quiz_arr[$readrow_topic['topic_num']] = $readrow_topic['subject_num'];
    	// }
    }

    try {
		$stmt_subtopic = $conn->prepare("SELECT * FROM subtopic order by subtopic_order asc");
   		$stmt_subtopic->execute();
    	$result_subtopic = $stmt_subtopic->fetchAll(); 
	} catch (PDOException $e) {
		echo "Error: ".$e->getMessage()." !!!";
	}

	foreach($result_subtopic as $readrow_subtopic){
		$content_name[$readrow_subtopic['subtopic_num']] = $readrow_subtopic['subtopic_name'];
		$subtopic_arr[$readrow_subtopic['subtopic_num']] = $readrow_subtopic['topic_num'];
	}
	$_SESSION["content_name"] = $content_name;
	$_SESSION["subject_arr"] = $subject_arr;
	$_SESSION["topic_arr"] = $topic_arr;
	$_SESSION["subtopic_arr"] = $subtopic_arr;
	// $_SESSION['quiz_arr'] = $quiz_arr;
?>

<ol>
	<?php foreach($subject_arr as $subject_key => $subject_value){?>
	<li>
		<div>
			<h3 class='data-list' style='display: inline-block;'><a style='cursor: pointer;'><?php echo $content_name[$subject_value];?></a></h3>
			<?php 
				$t_num = array();
				$t_num = array_keys($topic_arr, $subject_value);
				$btn_access = false;
				foreach ($t_num as $t_value) {
					if(count(array_keys($subtopic_arr,$t_value))>0) $btn_access = true;
				}
				if($btn_access){
			?>
			<a href="test.php?<?php echo md5("subjectNum")."=".$subject_value;?>" class='btn btn-info btn-sm'>&nbsp;&nbsp; <span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;</a>
			<?php } ?>
		</div>
		<ol style='display: none;' type='I' id='<?php echo $readrow['subject_num']?>'>
			<?php 
				foreach($topic_arr as $topic_key => $topic_value){
					if($topic_value == $subject_value){
			?>
			<li>
				<div>
					<h4 class='data-list'>
						<a style='cursor: pointer;'><?php echo $content_name[$topic_key];?></a>
					</h4>
				</div>
				<ul style='display: none;'>
					<?php 
						foreach($subtopic_arr as $subtopic_key => $subtopic_value){
							if($subtopic_value == $topic_key){
					?>
					<li><h5><?php echo $content_name[$subtopic_key];?></h5></li>
					<?php }} ?>
				</ul>
			</li>
			<?php }} ?>
		</ol>
	</li>
	<hr>
	<?php }?>
</ol>