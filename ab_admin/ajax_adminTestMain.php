<?php
	include_once("../connection.php");
	$dir = "../documents/problem_solving/";
	if(isset($_GET[md5('elementNum')])){
		$elementNum = $_GET[md5('elementNum')];
		$res = array();
		try {
			$stmt = $conn->prepare("SELECT t.topic_num, 
										t.topic_name, 
										st.subtopic_num, 
										st.subtopic_name, 
										v.video_num,
										v.video_link,
										v.vimeo_link,
										ps.problem_solution_id, 
										ps.document_link, 
										tst.test_num, 
										tst.last_date
									FROM topic t 
										INNER JOIN subtopic st
									    	ON st.topic_num = t.topic_num
									    LEFT JOIN video v
									    	ON v.subtopic_num = st.subtopic_num
									    		AND v.vimeo_link != 'n'
									   	LEFT JOIN problem_solution ps
									    	ON ps.subtopic_num = st.subtopic_num
									    LEFT JOIN test tst
									    	ON tst.subtopic_num = st.subtopic_num
									WHERE t.subject_num = :subject_num
										AND quiz = 'n'
									ORDER BY t.topic_order, st.subtopic_order ASC");

			$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);

			$subject_num = $elementNum;
	     	
		    $stmt->execute();
		    $result = $stmt->fetchAll();
		    foreach ($result as $k => $v) {
		    	$res[$v['topic_num']]['topic_name'] = $v['topic_name'];
		    	$res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['subtopic_name'] = $v['subtopic_name'];
		    	if($v['vimeo_link']=='y'){
		    		$res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['vimeo'][$v['video_num']] = $v['video_link'];
		    	}
		    	else {
		    		$res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['vimeo'][$v['video_num']] = "";
		    		// $res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['video'][$v['video_num']] = $v['video_link'];
		    	}
		    	$res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['file'][$v['problem_solution_id']] = $v['document_link'];
		    	// $res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['test'][$v['test_num']] = $v['last_date'];
		    	$res[$v['topic_num']]['subtopic'][$v['subtopic_num']]['test'] = $v['last_date'];
		    }	    
		} catch(PDOException $e) {
	        echo "Error: " . $e->getMessage();
	    }
	}
?>
<table class='table table-striped table-bordered'>
	<?php 
		$topic_count = 0;
		foreach ($res as $topic_num => $v) { 
	?>
	<tr class='info'>
		<td colspan='5'><center><?php echo $v['topic_name'];?></center></td>
	</tr>
	<tr class='active'>
		<th><center>Тақырып</center></th>
		<!-- <th><center>Видео</center></th> -->
		<th><center>Vimeo</center></th>
		<th><center>Есеп</center></th>
		<th><center>Тест</center></th>
	</tr>
	<?php 
		$existing = array(0, 0, 0);
		$subtopic_count=0; 
		foreach ($v['subtopic'] as $subtopic_num => $v) { ?>
	<tr id="<?php echo ++$topic_count;?>_tr">
		<td><i><?php echo ++$subtopic_count.". ".$v['subtopic_name']; ?></i></td>
		<!-- <td>
			<?php
				$video_count = 0;
				foreach ($v['video'] as $video_num => $val) {
					if($video_num!='') {
						$video_count++; 
						echo "<p style='margin:0;'><b>".$val."</b></p>";
						$existing[0] = 1;
					}
				}
				if($video_count==0) {
					echo "<p style='margin:0;' class='text-danger'>Видео енгізілмеген!</p>";
					$existing[0] = 0;
				} 
			?>
		</td> -->
		<td>
			<?php
				$video_count = 0;
				foreach ($v['vimeo'] as $video_num => $val) {
					if($video_num!='') {
						$video_count++; 
						echo "<p style='margin:0;'>".$val."</p>";
						$existing[0] = 1;
					}
				}
				if($video_count==0) {
					echo "<p style='margin:0;' class='text-danger'>Видео енгізілмеген!</p>";
					$existing[0] = 0;
				} 
			?>
		</td>
		<td>
			<?php
				$file_count = 0;
				foreach ($v['file'] as $doc_id => $val) {
					if($doc_id==!''){
						$file_count++;
						echo "<p style='margin:0;'><a href=".$dir.$val." target='_blank'>".explode("___", $val)[0].".pdf</a></p>";
						$existing[1] = 1;
					}
				}
				if($file_count==0) {
					echo "<p style='margin:0;' class='text-danger'>Файл енгізілмеген!</p>"; 
					$existing[1] = 0;
				}
			?>
		</td>
		<td>
			<?php
				if($v['test']!=''){
					echo "<p style='margin:0;'>".$v['test']."</p>";
					$existing[2] = 1;
				}
				else {
					echo "<p style='margin:0;' class='text-danger'>Тест енгізілмеген!</p>";
					$existing[2] = 0;
				}
			?>
		</td>
	</tr>
	<?php
		$class='danger';
		if(array_sum($existing)<3 && array_sum($existing)!=0){
			$class='warning';
		} 	
		else if(array_sum($existing)==3){
			$class='success';
		}
		echo "<script type='text/javascript'>$('#".$topic_count."_tr').addClass('".$class."');</script>" ; 
		}} 
	?>
</table>