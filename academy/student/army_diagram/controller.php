<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/global_controller.php');
    check_student_access();

    $data = array();

    if (isset($_GET['upload_avatar'])) {
    	try {
    		$file = $_FILES['avatar_img'];
    		$student_id = $_SESSION['user_id'];

    		if ($file['error'] == UPLOAD_ERR_OK) {
    			$allowed_extentins = array('jpeg', 'jpg', 'png', 'gif', 'JPEG', 'JPG', 'PNG', 'GIF');
    			$filename = $file['name'];
    			$ext = pathinfo($filename, PATHINFO_EXTENSION);

    			if (in_array($ext, $allowed_extentins)) {
    				$query = "SELECT s.avatar_link
    							FROM student s
    							WHERE s.id = :student_id";
    				$stmt = $connect->prepare($query);
    				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    				$stmt->execute();
    				$old_avatar_img = $stmt->fetch(PDO::FETCH_ASSOC)['avatar_link'];

                    if ($old_avatar_img != '') {
                        if (file_exists($root.$old_avatar_img)) {
                            unlink($root.$old_avatar_img);
                        }
                    }

					$target_file = '/student/img/avatar/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;
    				if (move_uploaded_file($file['tmp_name'], $root.$target_file)) {
    					$query = "UPDATE student SET avatar_link = :avatar_link WHERE id = :student_id";
    					$stmt = $connect->prepare($query);
    					$stmt->bindParam(':avatar_link', $target_file, PDO::PARAM_STR);
    					$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    					$stmt->execute();

    					$data['avatar_link'] = $target_file;
    					$data['success'] = true;
    				} else {
    					$data['message'] = 'Сурет жүктелмеді. Қайталап көріңіз';
    					$data['success'] = false;
    				}
    				$data['success'] = true;
    			} else {
    				$data['success'] = false;
    				$data['message'] = 'Жүктелген суреттің типі "jpeg", "jpg", "png", "gif" болу керек.';
    			}
	    	} else if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
	    		$data['message'] = 'Жүктелген суреттің салмағы 5 мб (мега бит) тан көп болмауы керек';
    			$data['success'] = false;
    		} else if ($file['error'] == UPLOAD_ERR_NO_FILE) {
    			$data['message'] = 'Сурет жүктелмеді. Қайталап көріңіз';
    			$data['success'] = false;
    		}
    	} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
    }
?>