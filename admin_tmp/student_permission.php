<?php
	include('../connection.php');
	if(isset($_GET['data_num']) && isset($_GET['status']) && $_GET['status']=='student_single' && isset($_GET['extra_num'])){
		// echo $_GET['extra_num'];
		// echo "<br>";
		// echo $_GET['data_num'];
		$data_subject = array();
		$data_permission = array();
		$subject_name = '';
		$subject_num = '';
		try {
			$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name subject_name, t.topic_num topic_num, t.topic_name topic_name, st.subtopic_num subtopic_num, st.subtopic_name subtopic_name FROM subject s, topic t, subtopic st WHERE st.topic_num = t.topic_num AND t.subject_num = s.subject_num AND s.subject_num = :subject_num AND t.quiz = 'n' ORDER BY t.topic_order ASC");
			$stmt->bindParam(':subject_num', $_GET['extra_num'], PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();
			foreach ($result as $value) {
				// $data_subject[$value['subject_num']]['name'] = $value['subject_name'];
				$data_subject[$value['topic_num']]['name'] = $value['topic_name'];
				$data_subject[$value['topic_num']]['subtopic'][$value['subtopic_num']]['name'] = $value['subtopic_name'];
			}

			$stmt = $conn->prepare("SELECT stp.video_permission video_permission, stp.test_permission test_permission, stp.subtopic_num subtopic_num FROM student_permission sp, student_test_permission stp, subtopic st, topic t WHERE stp.subtopic_num = st.subtopic_num AND t.topic_num = st.topic_num AND t.subject_num = :subject_num AND sp.student_num = :student_num AND sp.student_permission_num = stp.student_permission_num");
			$stmt = $conn->prepare("SELECT stp.video_permission video_permission, stp.test_permission test_permission, stp.subtopic_num subtopic_num FROM student_permission sp, student_test_permission stp WHERE sp.student_num = :student_num AND sp.student_permission_num = stp.student_permission_num AND stp.subtopic_num in (SELECT st.subtopic_num FROM subtopic st, topic t WHERE st.topic_num = t.topic_num AND t.subject_num = :subject_num)");
			$stmt->bindParam(':subject_num', $_GET['extra_num'], PDO::PARAM_STR);
			$stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
		    $stmt->execute();
		    $result_permission_list = $stmt->fetchAll();
		   	foreach ($result_permission_list as $value) {
		   		$data_permission[$value['subtopic_num']]['video_permission'] = $value['video_permission'];
		   		$data_permission[$value['subtopic_num']]['test_permission'] = $value['test_permission'];
		   	}
		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}
	}
?>
<ol>
<?php
	foreach ($data_subject as $key => $value) {
?>
	<li>
		<p class='topic_name'><a><?php echo $value['name'];?></a></p>
		<ol type='I' style='display: none;'>
		<?php
			foreach ($value['subtopic'] as $sKey => $sValue) {
		?>
			<li>
				<p class='subtopic'><a><?php echo $sValue['name'];?></a></p>
				<div style='display: none;'>
					<?php if($_SESSION['role']==md5('admin')) {?>
					<form class='form-inline' id='set_permission' method='post'>
						<div class='form-group'>
							<label for='video_permission' style='user-select:none'>Видео: </label>
							<input type="checkbox" <?php echo (isset($data_permission[$sKey]) && $data_permission[$sKey]['video_permission']=='t') ? "checked" : ''; ?> name="video_permission" id='video_permission'>
						</div>
						<div class='form-group'>
							<label for='test_permission' style='user-select:none'>Тест: </label>
							<input type="checkbox" <?php echo (isset($data_permission[$sKey]) && $data_permission[$sKey]['test_permission']=='t') ? "checked" : ''; ?> name="test_permission" id='test_permission'>
						</div>
						<div class='form-group'>
							<input type="hidden" name="data_num" value='<?php echo $_GET['data_num'];?>'>
							<input type="hidden" name="extra_num" value='<?php echo $sKey;?>'>
							<input type="submit" name="submit_permission" class='btn btn-xs btn-success'>
						</div>
					</form>	
					<?php } ?>
					<div style='display: inline-block; vertical-align: top;'><b>Результат: </b></div> 
					<div style='display: inline-block; vertical-align: top;'>
						N/A<br>
					</div>
				</div>
			</li>
		<?php } ?>
		</ol>
	</li>
<?php } ?>
</ol>