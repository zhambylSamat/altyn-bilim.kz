<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

    if (isset($_POST['insert-bonus-days'])) {
    	try {

    		$days = $_POST['days'];
    		$comment = $_POST['comment'];
    		$student_id = $_POST['student-id'];

    		$query = "INSERT INTO student_balance (student_id, days, comment)
    										VALUES (:student_id, :days, :comment)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':days', $days, PDO::PARAM_INT);
    		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    		$stmt->execute();

    		header('Location:index.php?student_id='.$student_id);
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    } else if (isset($_GET['add_extra_coins'])) {
        try {

            $student_id = $_GET['student_id'];

            $query = "INSERT INTO coin_log (object_type, object_id, coins) VALUES ('force_insert_coin', :student_id, 50)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();

            $query = "SELECT sc.id
                        FROM student_coins sc
                        WHERE sc.student_id = :student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            if ($row_count == 0) {
                $query = "INSERT INTO student_coins (student_id, total_coins) VALUES (:student_id, 50)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $query = "UPDATE student_coins SET total_coins = total_coins + 50 WHERE student_id = :student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    }
?>