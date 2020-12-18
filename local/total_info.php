<?php 
	include('../connection.php');
	$permission_count = 0;
	try {
		$stmt_permission = $conn->prepare("SELECT stp.video_permission videoPermission, 
												stp.test_permission testPermission, 
												stp.subtopic_num subtopicNum 
											FROM student_permission sp, 
												student_test_permission stp, 
												subtopic s 
											WHERE sp.student_num = :student_num 
												AND stp.student_permission_num = sp.student_permission_num 
												AND s.subtopic_num=stp.subtopic_num 
											ORDER BY s.created_date ASC");


		$stmt_permission->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
     	
	    $stmt_permission->execute();
	    $permission_count = $stmt_permission->rowCount();
	    $result_permission = $stmt_permission->fetchAll(); 
	    $subject_arr = array();
	    if($permission_count!=0){

		    $content_name = array();
			$topic_arr = array();
			$subtopic_arr = array();
			$test_arr = array();
			$result_arr = array();
	    	foreach($result_permission as $readrow_permission){
	    		if($readrow_permission['videoPermission']=='t'){
	    			$test_arr[$readrow_permission['subtopicNum']] = $readrow_permission['testPermission'];
		    		$stmt_section = $conn->prepare("SELECT s.subject_name subjectName, 
		    											s.subject_num subjectNum 
		    										FROM subject s, 
		    											topic t, 
		    											subtopic st 
		    										WHERE st.subtopic_num = :subtopic_num 
		    											AND st.topic_num = t.topic_num 
		    											AND t.subject_num = s.subject_num");

					$stmt_section->bindParam(':subtopic_num', $readrow_permission['subtopicNum'], PDO::PARAM_STR);
			     	
				    $stmt_section->execute();
				    $result_subject = $stmt_section->fetch(PDO::FETCH_ASSOC);
				    if(!in_array($result_subject['subjectNum'],$subject_arr)){
				    	array_push($subject_arr, $result_subject['subjectNum']);
				    	$content_name[$result_subject['subjectNum']] = $result_subject['subjectName'];
				    }
				    $stmt_topic = $conn->prepare("SELECT t.topic_num topicNum, 
				    								t.topic_name topicName 
				    							FROM topic t, 
				    								subtopic st 
				    							WHERE st.subtopic_num = :subtopic_num 
				    								AND st.topic_num = t.topic_num 
				    							ORDER BY t.created_date, st.created_date ASC");
					$stmt_topic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_topic->execute();
					$result_topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_topic['topicNum'],$topic_arr)){
						$topic_arr[$result_topic['topicNum']] = $result_subject['subjectNum'];
						$content_name[$result_topic['topicNum']] = $result_topic['topicName'];
					}

					$stmt_subtopic = $conn->prepare("SELECT subtopic_num, subtopic_name FROM subtopic WHERE subtopic_num = :subtopic_num order by created_date asc");
					$stmt_subtopic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_subtopic->execute();
					$result_subtopic = $stmt_subtopic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_subtopic['subtopic_num'],$subtopic_arr)){
						$subtopic_arr[$result_subtopic['subtopic_num']] = $result_topic['topicNum'];
						$content_name[$result_subtopic['subtopic_num']] = $result_subtopic['subtopic_name'];
					}

					$stmt_result = $conn->prepare("SELECT tr.test_result_num testResultNum, 
														tr.submit_date submitDate, 
														tr.result result 
													FROM test_result tr, 
														test t 
													WHERE tr.student_num = :student_num 
														AND tr.test_num = t.test_num 
														AND t.subtopic_num = :st_num 
													ORDER BY tr.submit_date ASC");
					$stmt_result->bindParam(':student_num',$_SESSION['student_num'], PDO::PARAM_STR);
					$stmt_result->bindParam(':st_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_result->execute();
					$result_test_result = $stmt_result->fetchAll();
					foreach ($result_test_result as $readrow) {
						$result_arr[$readrow_permission['subtopicNum']][$readrow['testResultNum']]['date'] = $readrow['submitDate'];
						$result_arr[$readrow_permission['subtopicNum']][$readrow['testResultNum']]['result'] = $readrow['result'];
					}
				}
			}

			$stmt_student_progress = $conn->prepare("SELECT DISTINCT s.name, 
														s.surname, 
													    sj.subject_name,
													    sj.subject_num,
													    ((SELECT st3.subtopic_order + (CASE
													                                  WHEN t3.topic_order = 1 THEN 0
													                                  ELSE (SELECT SUM((SELECT st4.subtopic_order 
													                                              FROM subtopic st4 
													                                              WHERE st4.topic_num = t4.topic_num
													                                              ORDER BY st4.subtopic_order DESC
													                                              LIMIT 1))
													                                  FROM topic t4
													                                  WHERE t4.subject_num = t3.subject_num
													                                  		AND t4.topic_order < t3.topic_order)
													                                  END)
													    FROM subtopic st3,
													     	student_progress sp3,
													     	topic t3
													    WHERE sp3.student_num = s.student_num
													     	AND sp3.progress != 0
													     	AND st3.subtopic_num = sp3.subtopic_num
													     	AND st3.topic_num = t3.topic_num 
													     	AND t3.subject_num = sj.subject_num
													    ORDER BY t3.topic_order DESC, st3.subtopic_order DESC
													    LIMIT 1) * 100 / (SELECT count(st2.subtopic_num) 
													    FROM subtopic st2,
													     	topic t2
													    WHERE t2.subject_num = sj.subject_num
													     	AND st2.topic_num = t2.topic_num)) as percentage_result
													FROM student s,
														subject sj,
													    group_info gi,
													    group_student gs
													WHERE s.student_num = :student_num
														AND gs.student_num = s.student_num
													    AND gi.group_info_num = gs.group_info_num
													    AND sj.subject_num = gi.subject_num
													ORDER BY percentage_result DESC");
			// $stmt_student_progress = $conn->prepare("SELECT s.subject_name,
			// 											s.subject_num, 
			// 											(count(sp.student_progress_num) * 100 / (SELECT count(st2.subtopic_num)
			// 																					FROM topic t2, 
			// 																						subtopic st2, 
			// 																					    subject s2
			// 																					WHERE t2.subject_num = s2.subject_num
			// 																						AND st2.topic_num = t2.topic_num
			// 																						AND s2.subject_num = s.subject_num)) as percentage_result
			// 										FROM student_progress sp,
			// 											subject s,
			// 										    topic t,
			// 										    subtopic st
			// 										WHERE sp.student_num = :student_num
			// 											AND sp.progress != 0
			// 										    AND t.subject_num = s.subject_num
			// 										    AND st.topic_num = t.topic_num
			// 										    AND sp.subtopic_num = st.subtopic_num
			// 										GROUP BY s.subject_num
			// 										ORDER BY s.subject_name ASC");

			$stmt_student_progress->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
	    	$stmt_student_progress->execute();
	    	$student_progress = $stmt_student_progress->fetchAll();
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>

<center><h4>Өтілген тақырыптар пайызы</h4></center>
<?php
	if (count($student_progress) > 0) {
	foreach ($student_progress as $value) {
		$progress_percent = round($value['percentage_result'], 1);
?>
	<div class='subject_progress' style='border:1px dotted #aaa; padding: 0 1%;'>
		<p style='font-weight: 600;'><a class='list-progress' data-num='<?php echo $value['subject_num'];?>' data-toggle='modal' data-target='.box-list-student-progress' style='cursor: pointer;'><?php echo $value['subject_name'];?> оқу жоспары</a></p>
		<div class="progress">
		  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="<?php echo $progress_percent; ?>" aria-valuemax="100" style="<?php echo "width: ".$progress_percent."%"; ?>">
		    <b><?php echo $progress_percent; ?>%</b>
		  </div>
		  <div class="progress-bar progress-bar-danger" style="background-image: -webkit-linear-gradient(top,#ddd 0,#aaa 100%) !important; background-image: -o-linear-gradient(top,#ddd 0,#aaa 100%) !important; background-image: linear-gradient(to bottom,#ddd 0,#aaa 100%) !important; <?php echo "width: ".(100.0 - $progress_percent)."%"; ?>">
		  	<?php if($progress_percent==0 || $progress_percent=='') {?>
		    <b style='color:#333; '>0%</b>
		    <?php } ?>
		  </div>
		</div>
	</div>
<?php }} ?>
	
		<?php
		for($i = 0; $i<count($subject_arr); $i++){
		?>
		<div id='<?php echo $subject_arr[$i];?>'>
		<table class='table table-bordered'>

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
			<td style='width: 100%;'>
				<center><b><a style='cursor:pointer;' class='arena_section' data_name='subtopic' data_num = "<?php echo $subtopic_key;?>"><?php echo $content_name[$subtopic_key];?></a></b></center>
				<?php if($test_arr[$subtopic_key]=='t'){ ?>
				<span>Результат:</span>
				<div class='row'>
				<?php
					if(isset($result_arr[$subtopic_key])){
						$count = 0;
						$result_array = array();
						foreach ($result_arr[$subtopic_key] as $result_key => $result_value) {
							if(!isset($result_array[$subtopic_key])) $result_array[$subtopic_key] = array();
							array_push($result_array[$subtopic_key],floatval($result_value['result']));
							$count++;
							$style = 'black';
							if(floatval($result_value['result'])<80) $style = '#ED1C26';
							else $style = '#23D000'; 
							echo "<div class='col-md-5 col-md-offset-1 col-sm-5 col-sm-offset-1'>".$count.") <span style='color:".$style."'>".$result_value['date']."&nbsp;|&nbsp;".$result_value['result']."%</span></div>";
						}
					}
					else{
						echo '&nbsp;&nbsp;&nbsp;&nbsp;<span><i>"Пусто"</i></span>';
					}
				?>	
				<?php } else {?>
				Тест не доступен. Обратитесь к учителю.
				<?php } ?>
				</div>
			</td>
		</tr>
		<?php }} ?>
		<?php }} ?>
		</table>
		<?php
			if(isset($_GET[md5('data_num')])){
				try {
					$list = array();
					$stmt_subject = $conn->prepare("SELECT t.topic_name tName, t.topic_num tNum, st.subtopic_name stName, st.subtopic_num stNum FROM subject s, topic t, subtopic st WHERE s.subject_num = :subject_num AND t.subject_num = s.subject_num AND st.topic_num = t.topic_num order by t.created_date, st.created_date asc");
					$stmt_subject->bindParam(':subject_num',$_GET[md5('data_num')], PDO::PARAM_STR);
					$stmt_subject->execute();
					$subject = $stmt_subject->fetchAll();
					foreach ($subject as $value) {
						$list[$value['tNum']]['name'] = $value['tName'];
						$list[$value['tNum']]['subtopic'][$value['stNum']] = $value['stName'];
					}
				} catch (PDOException $e) {
					echo "Error ".$e->getMessage()." !!!";
				}
				echo "<pre><ol>";
				foreach ($list as $list_key => $list_value) {
					if(array_key_exists($list_key,$topic_arr)){
						echo "<li><b>".$list_value['name']."</b>";
					}
					else {
						echo "<li>".$list_value['name'];
					}
					echo "<ol type='I'>";
					foreach ($list_value['subtopic'] as $key => $value) {
						$max_val = 0;
						$color = 'lightgray';
						$symbol = '';
						if(isset($result_array) && array_key_exists($key,$result_array)) $max_val = max($result_array[$key]);
						if(array_key_exists($key, $subtopic_arr)) {
							$color = 'orange';
							$symbol = '&#8226';
						}
						if($max_val>=80) {
							$color = "green";
							$symbol = '';
						}
						echo "<li style='color:".$color."'><b><i>".$value." ".$symbol."</i></b></li>";
					}
					echo "</ol></li>";
				}
				echo "</ol></pre>";
			}
		?>
		</div>
		<?php } 
			if(count($subject_arr)==0){
				echo "<center><b><i><u><h3>Видеосабақ жіберілмеген, мұғалімнен видеосабақты жіберуін сұраңыз!</h3></u></i></b></center>";
			}
		?>
</div>
<?php
if(isset($_GET['asdf'])){
	echo "string";
}
?>




<!-- SELECT DISTINCT s.name, 
	s.surname, 
    sj.subject_name,
    (SELECT count(st2.subtopic_num) 
    FROM subtopic st2,
     	topic t2
    WHERE t2.subject_num = sj.subject_num
     	AND st2.topic_num = t2.topic_num) as total_subtopic_quantity,
        
    (SELECT st3.subtopic_order + (CASE
                                  WHEN t3.topic_order = 1 THEN 0
                                  ELSE (SELECT SUM((SELECT st4.subtopic_order 
                                              FROM subtopic st4 
                                              WHERE st4.topic_num = t4.topic_num
                                              ORDER BY st4.subtopic_order DESC
                                              LIMIT 1))
                                  FROM topic t4
                                  WHERE t4.subject_num = t3.subject_num
                                  		AND t4.topic_order < t3.topic_order)
                                  END)
    FROM subtopic st3,
     	student_progress sp3,
     	topic t3
    WHERE sp3.student_num = s.student_num
     	AND sp3.progress != 0
     	AND st3.subtopic_num = sp3.subtopic_num
     	AND st3.topic_num = t3.topic_num 
     	AND t3.subject_num = sj.subject_num
    ORDER BY t3.topic_order DESC, st3.subtopic_order DESC
    LIMIT 1) as current_progress_point
    
FROM student s,
	subject sj,
    group_info gi,
    group_student gs
WHERE s.student_num = 'US59ac0a3f3ccca6.39254141'
	AND gs.student_num = s.student_num
    AND gi.group_info_num = gs.group_info_num
    AND sj.subject_num = gi.subject_num -->