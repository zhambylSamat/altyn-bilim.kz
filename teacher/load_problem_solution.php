<?php
	include_once('../connection.php');
	$subject_num = (isset($_GET[md5('sn')])) ? $_GET[md5('sn')] : "";
	$dir = "../documents/problem_solving/";
	$single_subject = false;
	$result_subject = array();
	$count_subject = 0;
	$dir = "../documents/problem_solving/";
	try {
		if($subject_num==''){
			$stmt = $conn->prepare("SELECT s.subject_num, s.subject_name
				FROM subject s, 
					group_info gi 
				WHERE gi.teacher_num = :teacher_num 
					AND gi.subject_num = s.subject_num
				GROUP BY s.subject_num");
			$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
			$stmt->execute();
			$count_subject = $stmt->rowCount();
			$result_subject = $stmt->fetchAll();
		}
		$single_subject = ($count_subject==1 || $subject_num!='') ? true : false;
		$subject_num = ($count_subject==1) ? $result_subject[0]['subject_num'] : $subject_num;

		if($single_subject){
			$single_subject = true;
			$stmt = $conn->prepare("SELECT s.subject_num, 
										s.subject_name,
										t.topic_num, 
										t.topic_name, 
										st.subtopic_num, 
										st.subtopic_name,
										ps.problem_solution_id,
                                        ps.document_link
                                    FROM group_info gi
                                    	INNER JOIN subject s 
                                        	ON gi.subject_num = s.subject_num
                                        INNER JOIN topic t
                                        	ON s.subject_num = t.subject_num
                                        INNER JOIN subtopic st 
                                        	ON t.topic_num = st.topic_num
                                        LEFT JOIN problem_solution ps
                                        	ON st.subtopic_num = ps.subtopic_num
                                    WHERE gi.teacher_num = :teacher_num
                                    	AND gi.subject_num = :subject_num
								    ORDER BY t.topic_order, st.subtopic_order, ps.document_link ASC");
			$stmt->bindParam(':teacher_num', $_SESSION['teacher_num'], PDO::PARAM_STR);
			$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);
			$stmt->execute();
	    	$res = $stmt->fetchAll();
		}
	    $result = array();
	    if(isset($res)){
		 	foreach ($res as $key => $value) {
		 		$result[$value['subject_num']]['subject_name'] = $value['subject_name'];
		 		if($single_subject){
		 			$result[$value['subject_num']]['topic'][$value['topic_num']]['topic_name'] = $value['topic_name'];
		 			$result[$value['subject_num']]['topic'][$value['topic_num']]['subtopic'][$value['subtopic_num']]['subtopic_name'] = $value['subtopic_name'];
		 			$result[$value['subject_num']]['topic'][$value['topic_num']]['subtopic'][$value['subtopic_num']]['document'][$value['problem_solution_id']] = $value['document_link'];
		 		}
		 	}
		}
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
?>
<?php 
	if(isset($_GET['back'])){
		echo "<a id='back-to-subject-list'><- Артқа</a>";
	} 
?>
<?php
	if(!$single_subject){
		if(count($result_subject)==0){
			echo "N/A";
		}
		else{
			echo "<ol>";
			foreach ($result_subject as $key => $value) {
?>
		<li>
			<td>
				<a class='subject-solution-list' data-num='<?php echo $value['subject_num'];?>'><?php echo $value['subject_name'];?></a>
			</td>
		</li>
<?php
	}}
		echo "<ol>";
	} else if($single_subject){
?>
	<?php foreach($result as $subject_num => $value) {?>
	<h3><b><?php echo $value['subject_name'];?></b></h3>
	<ol>
		<?php foreach ($value['topic'] as $topic_num => $value) { ?>
		<li><a class='topic-name'><?php echo $value['topic_name']; ?></a>
			<ol type='I' style='display: none;'>
				<?php foreach ($value['subtopic'] as $subtopic_num => $value) { ?>
				<li><?php echo $value['subtopic_name']; ?>
					<ul type='circle'>
						<?php 
							$count = 0;
							foreach ($value['document'] as $key => $value) {
								if($value!=''){
									$count++;
						?>
						<li>
							<a href="<?php echo $dir.$value;?>" target='_blank'><?php echo explode("___", $value)[0].".pdf";?></a>
						</li>
						<?php }} if($count==0){	?>
						<li>
							<p class='text-danger'>Файл жүктелмеген</p>
						</li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
			</ol>
		</li>
		<?php } ?>
	</ol>
	<?php } ?>
<?php
	}
?>