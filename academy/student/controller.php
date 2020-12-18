<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_student_access();

    $data = array();  

    if (isset($_GET['check_and_get_token'])) {

    	try {

    		$local_storage_token = $_POST['token'];
    		$session_token = $_SESSION['token'];
    		$id = $_SESSION['user_id'];

    		$stmt = $connect->prepare("SELECT token FROM student WHERE id = :id");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
			$token = $stmt->fetch(PDO::FETCH_ASSOC)['token'];

			if ($session_token == '') {
				$_SESSION['token'] = $token;
				$data['is_authenticate'] = true;
				$data['token'] = $token;
			} else if ($session_token != '' && $session_token == $local_storage_token && $local_storage_token == $token) {
				$data['is_authenticate'] = true;
				$data['token'] = $token;
			} else {
				$data['is_authenticate'] = false;
			}
			$data['success'] = true;

    		
    	} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = "ERROR: ".$e->getMessage()."!!!";
    	}
    	echo json_encode($data);
    } else if (isset($_GET['check_token'])) {
    	$data['success'] = check_current_student_token();
    	echo json_encode($data);
    } else if (isset($_GET['submit_friends_promo_code'])) {
        try {
            $student_id = $_SESSION['user_id'];
            $promo_code = $_POST['promo-code'];

            $data['promo_code_message'] = array('use_self_promo_code' => false,
                                        'friends_already_used' => false,
                                        'promo_code_already_used' => false,
                                        'incorrect_promo_code' => false);



            $use_self_promo_code = get_use_self_promo_code($student_id, $promo_code);
            if (!$use_self_promo_code) {
                $promo_code_id = get_promo_code_id($promo_code);
                if ($promo_code_id != '') {
                    $friend_already_used = get_friend_already_used($student_id, $promo_code_id);
                    if (!$friend_already_used) {
                        $promo_code_already_used = get_promo_code_already_used($student_id);
                        if (!$promo_code_already_used) {
                            $already_payment_done = get_already_payment_done($student_id);
                            if (!$already_payment_done) {
                                $data['success'] = insert_promo_code($student_id, $promo_code_id);
                                $data['data'] = get_promo_code_info($promo_code_id);
                            } else {
                                $data['promo_code_message']['already_payment_done'] = true;
                            }
                        } else {
                            $data['success'] = false;
                            $data['promo_code_message']['promo_code_already_used'] = true;
                        }
                    } else {
                        $data['success'] = false;
                        $data['promo_code_message']['friends_already_used'] = true;
                    }
                } else {
                    $data['success'] = false;
                    $data['promo_code_message']['incorrect_promo_code'] = true;
                }
            } else {
                $data['success'] = false;
                $data['promo_code_message']['use_self_promo_code'] = true;
            }
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "ERROR: ".$e->getMessage()."!!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['has_army_group'])) {
        try {
            $data = array();
            $student_id = $_SESSION['user_id'];

            $query = "SELECT ag.id
                        FROM group_student gs,
                            army_group ag
                        WHERE gs.student_id = :student_id
                            AND gs.is_archive = 0
                            AND ag.group_info_id = gs.group_info_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            $data['has_army_group'] = $row_count == 0 ? false : true;
    
            $data['success'] = true;            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "ERROR: ".$e->getMessage()."!!!";
        }
        echo json_encode($data);

    }

    function get_already_payment_done ($student_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT count(gsp.id) AS c
                        FROM group_student_payment gsp,
                            group_student gs
                        WHERE gs.student_id = :student_id
                            AND gsp.group_student_id = gs.id
                            AND gsp.payment_type = 'money'";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $data_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

            return $data_count == 0 ? false : true;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_use_self_promo_code ($student_id, $promo_code) {
        GLOBAL $connect;

        try {

            $query = "SELECT spc.id
                        FROM student_promo_code spc
                        WHERE spc.student_id = :student_id
                            AND spc.code = :promo_code";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':promo_code', $promo_code, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC)['id'] == '' ? false : true;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_promo_code_info ($promo_code_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT s.last_name,
                            s.first_name
                        FROM student_promo_code spc,
                            student s
                        WHERE spc.id = :promo_code_id
                            AND s.id = spc.student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':promo_code_id', $promo_code_id, PDO::PARAM_INT);
            $stmt->execute();
            $query_result = $stmt->fetch(PDO::FETCH_ASSOC);

            $result = array('last_name' => $query_result['last_name'],
                            'first_name' => $query_result['first_name']);

            return $result;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function insert_promo_code ($student_id, $promo_code_id) {
        GLOBAL $connect;

        try {
            
            $query = "INSERT student_used_promo_code (student_id, student_promo_code_id) VALUES (:student_id, :student_promo_code_id)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_promo_code_id', $promo_code_id, PDO::PARAM_INT);
            $stmt->execute();

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_promo_code_id ($promo_code) {
        GLOBAL $connect;

        try {

            $query = "SELECT spc.id
                        FROM student_promo_code spc
                        WHERE spc.code = :promo_code";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':promo_code', $promo_code, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_promo_code_already_used ($student_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT supc.id
                        FROM student_used_promo_code supc
                        WHERE supc.student_id = :student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $supc_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            return $supc_id == '' ? false : true;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_friend_already_used ($student_id, $promo_code_id) {
        GLOBAL $connect;

        try {
            $query = "SELECT spc.student_id
                        FROM student_promo_code spc
                        WHERE spc.id = :promo_code_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':promo_code_id', $promo_code_id, PDO::PARAM_INT);
            $stmt->execute();
            $friend_student_id = $stmt->fetch(PDO::FETCH_ASSOC)['student_id'];

            $query = "SELECT spc.id
                        FROM student_promo_code spc
                        WHERE spc.student_id = :student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $p_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            $query = "SELECT supc.id
                        FROM student_used_promo_code supc
                        WHERE supc.student_id = :student_id
                            AND supc.student_promo_code_id = :student_promo_code_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $friend_student_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_promo_code_id', $p_id, PDO::PARAM_INT);
            $stmt->execute();
            $supc_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];


            return $supc_id == "" ? false : true;
        } catch (Exception $e) {
            throw $e;
        }
    }

?>