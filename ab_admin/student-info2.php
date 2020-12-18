<td colspan='3'>
	<div>
		<span class='glyphicon glyphicon-remove-sign pull-left close_body' style='cursor:pointer;'></span>
	</div>
	<br>
	<div style='border:1px dashed lightgrey;'>
		<ol>
			<?php
				include_once('../connection.php');
				$studentNum = $_GET[md5('student_num')];
				$count = 1;
				$list = $_SESSION['list-subject-topic-subtopic'];
			?>
			<?php foreach($list as $list_key => $list_value){?>
			<li>
				<h5 class='info-list'><a style='cursor:pointer;'><?php echo $list_value['name'];?></a></h5>
				<ol type='I' class='subject-info'>
					<?php foreach($list_value['topic'] as $topic_key => $topic_value){?>
					<li>
						<a style='cursor:pointer;' class='info-list'><?php echo $topic_value['name'];?></a>
						<ul class='topic-info'>
							<?php foreach($topic_value['subtopic'] as $subtopic_key => $subtopic_value){
								$video = '';
								$test = '';
							?>
							<?php
								try {
									$stmt = $conn->prepare("SELECT b.video_permission, b.test_permission FROM student_permission as a, student_test_permission as b WHERE a.student_num = :student_num AND a.student_permission_num = b.student_permission_num AND b.subtopic_num = :subtopic_num");
									$stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
									$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
				    				$subtopic_num = $subtopic_key;
				    				$student_num = $studentNum;
								    $stmt->execute();
								    $result_permission_list = $stmt->fetchAll();
								    foreach ($result_permission_list as $permission) {
								    	$video = $permission['video_permission'];
								    	$test = $permission['test_permission'];
								    }
								} catch (PDOException $e) {
									
								}
							?>
							<li>
								<a style='cursor:pointer;' class='info-list'><?php echo $subtopic_value['name'];?></a>
								<div class='media subtopic-info'>
									<form class='form-inline' action='admin_controller.php' method='post'>
										<div class='form-group'>
											<label for='video-<?php echo $count;?>'>Видео сабақ</label>
											<input type="checkbox" <?php if(isset($video) && $video == 't') echo "checked";?> name="video-subtopic" id='video-<?php echo $count;?>'>
										</div>&nbsp;
										<div class='form-group'>
											<label for="test-<?php echo $count;?>">Тест</label>
											<input type="checkbox" <?php if(isset($test) && $test == 't') echo "checked";?> name="test-subtopic" id='test-<?php echo $count; $count++;?>'>
										</div>
										<input type="hidden" name="student-test-permission-student-num" value='<?php echo $studentNum;?>'>
										<input type="hidden" name="student-test-permission-subtopic-num" value='<?php echo $subtopic_key;?>'>
										<button type='submit' name='student-test-permission' class='btn btn-info btn-xs'>Отправить</button>
									</form>
									<div class='media-left'>
										Результат:
									</div>
									<div class='media-body'>
										<div>
											<span>12.06.17 - </span>
											<span class='bg-danger' style='padding:3px;'>55%</span>
										</div>
									</div>
								</div>
							</li>
							<?php }?>
						</ul>
					</li>
					<?php }?>
				</ol>
			</li>
			<?php }?>
		</ol>
	</div>
</td>