<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();


    if (isset($_POST['add-new-discount'])) {
    	try {

    		$discount_title = $_POST['discount-title'];
    		$amount = $_POST['amount'];
    		// $discount_type = $_POST['discount-type'];
            $discount_type = 'percent';
    		$discount_month = $_POST['discount-month'];
            $cant_insert_promo_code = isset($_POST['cant-insert-promo-code']) ? 1 : 0;

    		$query = "INSERT INTO discount (title, type, amount, for_month, cant_insert_promo_code)
                                    VALUES (:title, :type, :amount, :for_month, :cant_insert_promo_code)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':title', $discount_title, PDO::PARAM_STR);
    		$stmt->bindParam(':type', $discount_type, PDO::PARAM_STR);
    		$stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    		$stmt->bindParam(':for_month', $discount_month, PDO::PARAM_INT);
            $stmt->bindParam(':cant_insert_promo_code', $cant_insert_promo_code, PDO::PARAM_INT);
    		$stmt->execute();
    		
    		header('Location:../index.php');
    	} catch (Exception $e) {
    		throw $e;
    	}
    } else if (isset($_POST['set-discount-for-gs'])) {
        try {

            $discount_id = $_POST['discount'];
            $student_ids = $_POST['std-id'];
            $group_students = $_POST['group_student'];

            $insert_discount_group_student_query = "INSERT INTO discount_group_student
                                                            (discount_id, group_student_id, used_count, status, last_updated_date)
                                                    VALUES (:discount_id, :group_student_id, 0, 'active', NOW())";
            $get_group_student_id_query = "SELECT gs.id
                                            FROM group_student gs
                                            WHERE gs.group_info_id = :group_info_id
                                                AND gs.student_id = :student_id";


            foreach ($student_ids as $student_id) {
                if (isset($group_students[$student_id])) {
                    foreach ($group_students[$student_id] as $group_info_id) {
                        $stmt = $connect->prepare($get_group_student_id_query);
                        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                        $stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $group_student_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

                        $stmt = $connect->prepare($insert_discount_group_student_query);
                        $stmt->bindParam(':discount_id', $discount_id, PDO::PARAM_INT);
                        $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }

            header('Location:../index.php');
            
        } catch (Exception $e) {
            throw $e;
        }
    } else if (isset($_GET['get_student_info_by_phone'])) {
        $data = array();

        try {

            $phone = $_GET['phone'];

            $query = "SELECT s.id,
                            s.last_name,
                            s.first_name
                        FROM student s
                        WHERE s.phone = :phone";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            if ($row_count == 1) {
                $query_result = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = array('student_id' => $query_result['id'],
                                'last_name' => $query_result['last_name'],
                                'first_name' => $query_result['first_name']);
                $data['info'] = $result;
            }

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['get_student_group_with_discount'])) {
        $data = array();

        try {

            $student_id = $_GET['student_id'];

            $query = "SELECT gs.id AS group_student_id,
                            gi.id AS group_info_id,
                            gs.transfer_from_group,
                            gi.group_name
                        FROM group_student gs,
                            group_info gi
                        WHERE gs.student_id = :student_id
                            AND gs.is_archive = 0
                            AND gi.id = gs.group_info_id
                            AND gi.is_archive = 0";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();
            foreach ($query_result as $value) {
                $discount_info = get_group_student_discount($value['group_student_id'], $value['transfer_from_group']);
                $result[$value['group_info_id']] = array('group_name' => $value['group_name'],
                                                        'discount_title' => isset($discount_info['discount_id']) 
                                                                            ? $discount_info['title']
                                                                            : '',
                                                        'is_army_group' => get_is_army_group($value['group_info_id']),
                                                        'is_marathon_group' => get_is_marathon_group($value['group_info_id']));
            }
            $data['info'] = $result;
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    }
?>