<?php
$data['success'] = false;
$data['error'] = '';
$data['text'] = '';
if(isset($_POST[md5(md5('addNewVideo'))]) && isset($_POST[md5('elementNum')])) {
	$ini_PostSize = preg_replace("/[^0-9,.]/", "", ini_get('post_max_size'))*(1024*1024);
	$ini_FileSize = preg_replace("/[^0-9,.]/", "", ini_get('upload_max_filesize'))*(1024*1024);
	$maxFileSize = ($ini_PostSize<$ini_FileSize ? $ini_PostSize : $ini_FileSize);
	include('../connection.php');
	// echo $_FILES['video']['type']."<br>";
	$success = true;
	$count = 1;
	if(isset($_POST['name']) && $_POST['name']!=''){
		$fileName = $_POST['name'].".mp4";
	}
	else{
		$data['error'] .= $count++.'. Укажите называние видео. ';
		$success = false;
	}
	if(isset( $_FILES['video']['tmp_name'])){
		$fileTmpLoc = $_FILES['video']['tmp_name'];
		if($_FILES['video']['type']=='video/mp4'){
			$fileType = $_FILES['video']['type'];
		}
		else{
			$success = false;
			$data['error'] .= $count++.'. Видео должно быть в формате ".mp4". ';
		}
		if($_FILES['video']['size']){
			$fileSize = $_FILES['video']['size'];
		}
		else{}
	}
	else if(isset($_FILES['video'])){
		$fileErrorMsg = $_FILES['video']['error'];
		$data['error'] .= $count++.'. Выберите видео. ';
		$success = false;
	}
	else{
		$data['error'] .= $count++.'. Выберите видео. ';
		$success = false;
	}
	if($success){
		if(!file_exists("../video/video_lesson/".$fileName)){
			if(move_uploaded_file($fileTmpLoc, '../video/video_lesson/'.$fileName)){
				$data['success'] = true;
				try {
					$stmt = $conn->prepare("INSERT INTO video (video_num, subtopic_num, video_link) VALUES(:video_num, :subtopic_num, :video_link)");
	   
				    $stmt->bindParam(':video_num', $video_num, PDO::PARAM_STR);
				    $stmt->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				    $stmt->bindParam(':video_link', $fileName, PDO::PARAM_STR);

				    $video_num = uniqid('vV', true)."_".time();
				    $subtopic_num = $_POST[md5('elementNum')];
				       
				    $stmt->execute();
				} catch(PDOException $e) {
					$data['success'] = false;
					$data['error'] .= "Error: " . $e->getMessage(); 
			    }
			    if($data['success']){
					$data['text'] = requireToVar('ajax_adminVideo.php');
				}
				echo json_encode($data);
			}
			else{
				echo json_encode($data);
			}
		}
		else{
			$data['error'] .= $count++.'. Видео с таким названием уже сушествует.';
			$data['success'] = false;
			echo json_encode($data);
		}
	}
	else{
		echo json_encode($data);
	}
}
if(isset($_POST[md5('rmvVideo')]) && isset($_POST[md5('elemName')])) {
	include('../connection.php');
	try {
		// $stmt = $conn->prepare("DELETE FROM video WHERE video_num = :video_num");
		$stmt = $conn->prepare("UPDATE video SET vimeo_link = 'n' WHERE video_num = :video_num");
		
		$stmt->bindParam(':video_num',$_POST[md5('rmvVideo')],PDO::PARAM_STR);

		$stmt->execute();
		// unlink('../video/video_lesson/'.$_POST[md5('elemName')]);
		// $obj = requireToVar('ajax_adminVideo.php');





		$data['text'] = '';
		$data['success'] = true;

	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] .= "Error: " . $e->getMessage(); 
    }
    echo json_encode($data);
}
function requireToVar($file){
    ob_start();
    require($file);
    return ob_get_clean();
}
?>