<?php 
	include('connection.php');
	$permission_count = 0;
	try {
		$stmt_permission = $conn->prepare("SELECT stp.video_permission videoPermission, stp.test_permission testPermission, stp.subtopic_num subtopicNum FROM student_permission sp, student_test_permission stp WHERE sp.student_num = :student_num AND stp.student_permission_num = sp.student_permission_num");

		$stmt_permission->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
     	
	    $stmt_permission->execute();
	    $permission_count = $stmt_permission->rowCount();
	    $result_permission = $stmt_permission->fetchAll(); 
	    if($permission_count!=0){

		    $content_name = array();
			$subject_arr = array();
			$topic_arr = array();
			$subtopic_arr = array();
			$test_arr = array();
	    	foreach($result_permission as $readrow_permission){
	    		if($readrow_permission['videoPermission']=='t'){
	    			$test_arr[$readrow_permission['subtopicNum']] = $readrow_permission['testPermission'];
		    		$stmt_section = $conn->prepare("SELECT s.subject_name subjectName, s.subject_num subjectNum FROM subject s, topic t, subtopic st WHERE st.subtopic_num = :subtopic_num AND st.topic_num = t.topic_num AND t.subject_num = s.subject_num");

					$stmt_section->bindParam(':subtopic_num', $readrow_permission['subtopicNum'], PDO::PARAM_STR);
			     	
				    $stmt_section->execute();
				    $result_subject = $stmt_section->fetch(PDO::FETCH_ASSOC);
				    if(!in_array($result_subject['subjectNum'],$subject_arr)){
				    	array_push($subject_arr, $result_subject['subjectNum']);
				    	$content_name[$result_subject['subjectNum']] = $result_subject['subjectName'];
				    }
				    $stmt_topic = $conn->prepare("SELECT t.topic_num topicNum, t.topic_name topicName FROM topic t, subtopic st WHERE st.subtopic_num = :subtopic_num AND st.topic_num = t.topic_num");
					$stmt_topic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_topic->execute();
					$result_topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_topic['topicNum'],$topic_arr)){
						$topic_arr[$result_topic['topicNum']] = $result_subject['subjectNum'];
						$content_name[$result_topic['topicNum']] = $result_topic['topicName'];
					}

					$stmt_subtopic = $conn->prepare("SELECT * FROM subtopic WHERE subtopic_num = :subtopic_num");
					$stmt_subtopic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_subtopic->execute();
					$result_subtopic = $stmt_subtopic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_subtopic['subtopic_num'],$subtopic_arr)){
						$subtopic_arr[$result_subtopic['subtopic_num']] = $result_topic['topicNum'];
						$content_name[$result_subtopic['subtopic_num']] = $result_subtopic['subtopic_name'];
					}
				}
			}
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessge()." !!!";
	}
?>
<div>
	
		<?php
		for($i = 0; $i<count($subject_arr); $i++){
		?>
		<table class='table table-bordered' id='<?php echo $subject_arr[$i];?>'>
		<tr class='active'>
			<td colspan='2'><center><?php echo $content_name[$subject_arr[$i]];?></center></td>	
		</tr>
		<?php
			foreach($topic_arr as $topic_key => $topic_value){
				if($topic_value == $subject_arr[$i]){
		?>
		<tr class='info'>
			<td colspan='2'><center><?php echo $content_name[$topic_key];?></center></td>
		</tr>
		<?php
			foreach($subtopic_arr as $subtopic_key => $subtopic_value){
				if($subtopic_value == $topic_key){
					$class = 'danger';
					if($test_arr[$subtopic_key]=='t') $class='warning'
		?>
		<tr class='<?php echo $class;?>'>
			<td><center><?php echo $content_name[$subtopic_key];?></center></td>
			<td>
				<?php if($test_arr[$subtopic_key]=='t'){?>
				Результат:
				<?php } else {?>
				Тест не доступен. Обратитесь к учителю.
				<?php } ?>
			</td>
		</tr>
		<?php }} ?>
		<?php }} ?>
		</table>
		<?php } ?>
	
</div>
<?php
if(isset($_GET['asdf'])){
	echo "string";
}
?>