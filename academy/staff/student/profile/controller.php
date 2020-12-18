<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

    if (isset($_POST['remove']) && $_POST['remove'] == 'not-activated-student') {
    	try {
    		$id = $_POST['id'];
    		$status_id = 3;

            $stmt = $connect->prepare("DELETE FROM registration_course WHERE student_id = :student_id");
            $stmt->bindParam(':student_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $connect->prepare("DELETE FROM registration_reserve WHERE student_id = :student_id");
            $stmt->bindParam(':student_id', $id, PDO::PARAM_INT);
            $stmt->execute();

    		$stmt = $connect->prepare("DELETE FROM student WHERE id = :id AND status_id = :status_id");
    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    		$stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
    		$stmt->execute();

    		$data['success'] = true;
    		
    	} catch (Exception $e) {
    		$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
    	}
    	echo json_encode($data);
    } else if (isset($_POST['action']) && $_POST['action'] == 'accept_student') {
        try {

            $id = $_POST['id'];
            $not_accept_status_id = 3;
            $accept_status_id = 2;

            $stmt = $connect->prepare("UPDATE student
                                    SET status_id = :accept_status_id
                                    WHERE id = :id
                                        AND status_id = :not_accept_status_id");
            $stmt->bindParam(':accept_status_id', $accept_status_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':not_accept_status_id', $not_accept_status_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error: ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    }
?>