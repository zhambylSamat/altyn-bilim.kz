<?php
	include_once("../connection.php");
	if(isset($_GET[md5('elementNum')])){
		$elementNum = $_GET[md5('elementNum')];
		try {
			$stmt = $conn->prepare("SELECT * FROM topic WHERE subject_num = :subject_num AND quiz = 'n'");

			$stmt->bindParam(':subject_num', $subject_num, PDO::PARAM_STR);

			$subject_num = $elementNum;
	     	
		    $stmt->execute();
		    $topic = $stmt->fetchAll();
		    // echo "Coming soon...";
		    
		} catch(PDOException $e) {
	        echo "Error: " . $e->getMessage();
	    }
	}
?>
	<table class='table table-striped table-bordered'>
		<tr class='active'>
			<th><center>#</center></th>
			<th><center>Video</center></th>
		</tr>
		<?php foreach($topic as $topic_readrow){?>
		<tr class='info'>
			<td colspan='2'><center><?php echo $topic_readrow['topic_name'];?></center></td>
		</tr>
		<?php
			try {
				$stmt = $conn->prepare("SELECT *  FROM subtopic WHERE topic_num = :topic_num");

				$stmt->bindParam(':topic_num', $topic_num, PDO::PARAM_STR);

				$topic_num = $topic_readrow['topic_num'];
		     	
			    $stmt->execute();
			    $subtopic = $stmt->fetchAll();
			    // echo "Coming soon...";
			    
			} catch(PDOException $e) {
		        echo "Error: " . $e->getMessage();
		    }
		?>
		<?php $count_subtopic = 1; 
			foreach($subtopic as $subtopic_readrow){
			try {
				$stmt_video = $conn->prepare("SELECT * FROM video WHERE subtopic_num = :subtopic_num");
				$stmt_test = $conn->prepare("SELECT * FROM test WHERE subtopic_num = :subtopic_num");

				$stmt_video->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				$stmt_test->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);

				$subtopic_num = $subtopic_readrow['subtopic_num'];
		     	
			    $stmt_video->execute();
			    $stmt_test->execute();
			    $video = $stmt_video->fetch(PDO::FETCH_ASSOC);
			    $test = $stmt_test->fetch(PDO::FETCH_ASSOC);
			    $data_video = $data_test = "<h5 class='text-danger'>&#171;Видео сабақ жүктелмеген&#187;</h5>";
			    if(isset($video['video_num'])){
			    	$data_video = "<h5 class='text_success'>".$video['video_link']."</h5>";
			    }

				$table_class='danger';
				if(isset($video['video_num'])){
					$table_class='success';
				}
			    // echo "Coming soon...";
			    
			} catch(PDOException $e) {
		        echo "Error: " . $e->getMessage();
		    }
		?>
		<tr class='<?php echo $table_class;?>'>
			<td><center><?php echo $count_subtopic++.". ".$subtopic_readrow['subtopic_name'];?></center></td>
			<td><center><?php echo $data_video;?></center></td>
		</tr>
		<?php }?>
	<?php }?>
</table>