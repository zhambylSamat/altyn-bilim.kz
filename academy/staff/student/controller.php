<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    include_once($root.'/common/global_controller.php');
    check_admin_access();

    if (isset($_GET['reset-student-password'])) {
    	try {
    		$student_id = $_POST['student_id'];

    		$password = md5('12345');

    		$stmt = $connect->prepare("UPDATE student SET password_reset = 1, password = :password, block_count = 0, is_block = 0 WHERE id = :student_id");
    		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$data['student_id'] = $student_id;
    		$data['success'] = true;
    		
    	} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'Error: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
    } else if (isset($_GET['add_to_group'])) {
        $data = array();
        try {
            $course_id = $_GET['data_id'];
            $stmt = $connect->prepare("SELECT rc.student_id,
                                            rc.group_info_id,
                                            rc.subtopic_id
                                        FROM registration_course rc
                                        WHERE rc.id = :course_id
                                            AND rc.is_done = 0");
            $stmt->bindParam(":course_id", $course_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql_res = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $connect->prepare("UPDATE registration_course SET is_done = 1 WHERE id = :course_id");
            $stmt->bindParam(":course_id", $course_id, PDO::PARAM_INT);
            $stmt->execute();

            $access_until = date('Y-m-d', strtotime('+ 1 month'));
            
            $stmt = $connect->prepare("INSERT INTO group_student 
                                        (group_info_id, student_id, status, start_from, start_date, has_payment, access_until)
                                        VALUES (:group_info_id, :student_id, 'waiting', :start_from, NOW(), 1, :access_until)");
            $stmt->bindParam(':group_info_id', $sql_res['group_info_id'], PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $sql_res['student_id'], PDO::PARAM_INT);
            $stmt->bindParam(':start_from', $sql_res['subtopic_id'], PDO::PARAM_INT);
            $stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $connect->prepare("SELECT gs.id
                                        FROM group_student gs
                                        WHERE gs.group_info_id = :group_info_id
                                            AND gs.student_id = :student_id");
            $stmt->bindParam(':group_info_id', $sql_res['group_info_id'], PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $sql_res['student_id'], PDO::PARAM_INT);
            $stmt->execute();
            $group_student_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            $query = "INSERT INTO group_student_payment (group_student_id, payed_date, payment_type)
                                VALUES (:group_student_id, NOW(), 'money')";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
            $data['group_student_id'] = $group_student_id;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['done_payment_group'])) {
        $data = array();

        try {
            $group_student_id = $_GET['data_id'];
            $student_id = $_GET['student_id'];
            $group_info_id = $_GET['group_id'];
            if (isset($_SESSION['student_list'])) {
                $payment = $_SESSION['student_list'][$student_id]['groups'][$group_info_id]['payment'];
                $start_date = $payment['start_date'];
                $partial_payment_days = $payment['days'];
                $query = "INSERT INTO group_student_payment (group_student_id, payed_date, payment_type, partial_payment_days, start_date)
                                    VALUES (:group_student_id, NOW(), 'money', :partial_payment_days, :start_date)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
                $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
                $stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
                $stmt->execute();

                $query = "UPDATE group_student SET status = 'waiting' WHERE id = :group_student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
                $stmt->execute();

                // $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
                // $stmt->execute();
            }
            $data['success']=true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['set_partial_payment'])) {

        $data = array();

        try {

            $student_id = $_POST['student_id'];
            $group_student_id = $_POST['group_student_id'];
            $group_info_id = $_POST['group_id'];
            $partial_payment_days = $_POST['partial_days'];
            if (isset($_SESSION['student_list'])) {
                $payment = $_SESSION['student_list'][$student_id]['groups'][$group_info_id]['payment'];
                $start_date = $payment['start_date'];
                $date1 = date_create($start_date);
                $date2 = date_create(date('Y-m-d'));
                $min_days = date_diff($date1, $date2)->format('%a');
                if ($min_days <= $partial_payment_days) {
                    $query = "INSERT INTO group_student_payment (group_student_id, payed_date, payment_type, partial_payment_days, start_date)
                                    VALUES (:group_student_id, NOW(), 'money', :partial_payment_days, :start_date)";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
                    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
                    $stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
                    $stmt->execute();

                    $query = "UPDATE group_student SET status = 'waiting' WHERE id = :group_student_id";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':group_student_id', $group_student_id);
                    $stmt->execute();
                }
            }

            $data['success']=true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);

    } else if (isset($_GET['student_reserve_payment'])) {
        $data = array();
        try {
            $registration_reserve_id = $_GET['registration_reserve_id'];

            $query = "INSERT INTO student_reserve_payment (registration_reserve_id) VALUES(:registration_reserve_id)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':registration_reserve_id', $registration_reserve_id, PDO::PARAM_INT);
            $stmt->execute();
            $tmp_payed_date = date('d.m.Y H:i:s');

            $data['payed_date'] = $tmp_payed_date;
            $data['success']=true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['remove_from_course'])) {
        $data = array(); 
        try {

            $course_id = $_GET['data_id'];
            $stmt = $connect->prepare("DELETE FROM registration_course where id = :id");
            $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['success']=true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['remove_from_group'])) {
        $data = array();
        try {
            $group_student_id = $_GET['data_id'];

            // $query = "SELECT gsp.id,
            //                 gsp.access_until
            //             FROM group_student_payment gsp
            //             WHERE gsp.group_student_id = :group_student_id
            //                 AND gsp.finished_date IS NULL";
            // $stmt = $connect->prepare($query);
            // $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            // $stmt->execute();
            // $sql_result = $stmt->fetchAll();

            // foreach ($sql_result as $value) {

            // }

            // $stmt = $connect->prepare("DELETE FROM group_student WHERE id = :group_student_id");
            // $stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
            // $stmt->execute();

            $data['success']=true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['set_student_lesson_access_after_payment'])) {
        $data = array();

        try {

            $group_student_id = $_GET['group_student_id'];

            $query = "SELECT gs.id AS group_student_id,
                            gi.id AS group_info_id,
                            gi.start_date AS group_start_date,
                            (SELECT DATE_FORMAT(gsp.finished_date, '%Y-%m-%d')
                            FROM group_student_payment gsp
                            WHERE gsp.group_student_id = gs.id
                            ORDER BY gsp.finished_date DESC
                            LIMIT 1) AS last_payment_date
                        FROM group_student gs,
                            group_info gi
                        WHERE gs.id = :group_student_id
                            AND gi.id = gs.group_info_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $group_student_info = $stmt->fetch(PDO::FETCH_ASSOC);

            $query_get_lp = "SELECT lp.id,
                                    lp.subtopic_id
                                FROM lesson_progress lp,
                                    subtopic st
                                WHERE lp.group_info_id = :group_info_id
                                    AND DATE_FORMAT(lp.created_date, '%Y-%m-%d') = :date_point
                                    AND st.id = lp.subtopic_id
                                    AND st.subtopic_order >= (SELECT st2.subtopic_order
                                                                FROM subtopic st2,
                                                                    group_student gs2
                                                                WHERE gs2.id = :group_student_id
                                                                    AND st2.id = gs2.start_from)";

            $query_insert_fma = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id)
                                                            VALUES(:lesson_progress_id, :group_student_id)";

            $query_insert_tva = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                            VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)";
            $query_insert_tda = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                            VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)";
            $query_insert_eva = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                            VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)";
            $query_insert_mta = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
                                                            SELECT :group_student_id, :lesson_progress_id, count(mt.id) = 0
                                                            FROM material_test mt
                                                            WHERE mt.subtopic_id = :subtopic_id";

            $date_point = $group_student_info['last_payment_date'] != ''
                                                                    ? strtotime($group_student_info['last_payment_date']) 
                                                                    : strtotime($group_student_info['group_start_date']);
            $date_point = strtotime(date('Y-m-d', $date_point).' - 1 days');
            while (strtotime(date("Y-m-d")) >= $date_point) {
                $date_point_format = date('Y-m-d', $date_point);
                $stmt = $connect->prepare($query_get_lp);
                $stmt->bindParam(':group_info_id', $group_student_info['group_info_id'], PDO::PARAM_INT);
                $stmt->bindValue(':date_point', $date_point_format, PDO::PARAM_STR);
                $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                $stmt->execute();
                $lesson_progress = $stmt->fetchAll();
                foreach ($lesson_progress as $value) {
                    $stmt = $connect->prepare($query_insert_fma);
                    $stmt->bindParam(':lesson_progress_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $forced_material_access_id = $connect->lastInsertId();

                    $stmt = $connect->prepare($query_insert_tva);
                    $stmt->bindParam(':lesson_progress_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $stmt = $connect->prepare($query_insert_tda);
                    $stmt->bindParam(':lesson_progress_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $stmt = $connect->prepare($query_insert_eva);
                    $stmt->bindParam(':lesson_progress_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $stmt = $connect->prepare($query_insert_mta);
                    $stmt->bindParam(':lesson_progress_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':subtopic_id', $value['subtopic_id'], PDO::PARAM_INT);
                    $stmt->execute();
                }

                $date_point = strtotime(date('Y-m-d', $date_point).' + 1 days');
            }

            $query_update_group_student_status = "UPDATE group_student SET status = 'active' WHERE status = 'waiting' AND id = :group_student_id";
            $stmt = $connect->prepare($query_update_group_student_status);
            $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
            $stmt->execute();

            $query_get_group_student_payment = "SELECT gsp.id,
                                                    DATE_FORMAT(gsp.start_date, '%Y-%m-%d') AS start_date,
                                                    gsp.partial_payment_days
                                                FROM group_student_payment gsp
                                                WHERE gsp.group_student_id = :group_student_id
                                                    AND gsp.finished_date IS NULL
                                                ORDER BY gsp.payed_date ASC
                                                LIMIT 1";
            $stmt = $connect->prepare($query_get_group_student_payment);
            $stmt->bindParam(':group_student_id', $group_student_info['group_student_id'], PDO::PARAM_INT);
            $stmt->execute();
            $group_student_payment = $stmt->fetch(PDO::FETCH_ASSOC);

            $access_until = '';
            if ($group_student_payment['partial_payment_days'] == '') {
                $access_until = date('Y-m-d', strtotime($group_student_payment['start_date'].' + 1 month'));
            } else {
                $start_date = $group_student_payment['start_date'];
                $access_until = date('Y-m-d', strtotime($start_date.' + '.$group_student_payment['partial_payment_days'].' days'));
            }

            $query_update_group_student_payment = "UPDATE group_student_payment SET access_until = :access_until, is_used = 1, used_date = NOW()
                                                    WHERE id = :group_student_payment_id";
            $stmt = $connect->prepare($query_update_group_student_payment);
            $stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
            $stmt->bindParam(':group_student_payment_id', $group_student_payment['id'], PDO::PARAM_INT);
            $stmt->execute();

            $data['success']=true;
            // $data['partial_payment_days'] = $group_student_payment['partial_payment_days'];
            // $data['partial_payment_dayss'] = ($group_student_payment['partial_payment_days'] == '');
            // $data['partial_payment_id'] = $group_student_payment['id'];
            // $data['access_until'] = $access_until;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['next_full_payment']) && isset($_GET['group_student_id'])) {
        $data = array();
        try {
            $group_student_id = $_GET['group_student_id'];

            $query = "INSERT INTO group_student_payment (group_student_id, payed_date) VALUES (:group_student_id, NOW())";

            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['next_partial_payment'])) {
        $data = array();
        try {

            $group_student_id = $_POST['group_student_id'];
            $partial_days = $_POST['next-partial-days'];

            $query = "INSERT INTO group_student_payment (group_student_id, payed_date, partial_payment_days)
                                                VALUES(:group_student_id, NOW(), :partial_payment_days)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->bindParam(':partial_payment_days', $partial_days, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['check_phone_exists'])) {
        $data = array();
        try {

            $phone = $_GET['phone'];

            $query = "SELECT s.phone,
                            s.last_name,
                            s.first_name
                        FROM student s
                        WHERE s.phone = :phone";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            $data['message'] = "";
            if ($row_count > 0) {
                $student_info = $stmt->fetch(PDO::FETCH_ASSOC);    
                $data['message'] = 'Бұндай номермен оқушы тіркелген. '.$student_info['last_name'].' '.$student_info['first_name'];
            }

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['check_promo_code'])) {
        $data = array();
        try {

            $promo_code = $_GET['promo_code'];

            $query = "SELECT s.last_name,
                            s.first_name
                        FROM student s,
                            student_promo_code spc
                        WHERE spc.code = :code
                            AND s.id = spc.student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':code', $promo_code, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            $data['message'] = "";
            if ($row_count > 0) {
                $student_info = $stmt->fetch(PDO::FETCH_ASSOC);
                $data['message'] = $student_info['last_name'].' '.$student_info['first_name'].' досының промокоды';
            } else {
                $data['message'] = 'Бұндай промокод жоқ';
            }

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_POST['add-new-student-submit'])) {
        try {

            $phone = $_POST['phone'];
            $parent_phone = $_POST['parent_phone'];
            $last_name = $_POST['last-name'];
            $first_name = $_POST['first-name'];
            $promo_code = $_POST['promo-code'];
            $school = $_POST['school'];
            $class = $_POST['class'];
            $city = $_POST['city'];
            $password = md5('12345');

            $query = "INSERT INTO student (first_name, last_name, school, class, phone, parent_phone, city, password)
                                    VALUES (:first_name, :last_name, :school, :class, :phone, :parent_phone, :city, :password)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':school', $school, PDO::PARAM_STR);
            $stmt->bindParam(':class', $class, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
            $stmt->bindParam(':parent_phone', $parent_phone, PDO::PARAM_STR);
            $stmt->bindParam(':city', $city, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
            $student_id = $connect->lastInsertId();

            set_generated_promo_code($student_id);
            $student_promo_code_id = check_promo_code($promo_code);
            if ($student_promo_code_id != 0) {
                add_promo_code_for_new_student($student_id, $student_promo_code_id);
            }

            header('Location:../index.php');
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function add_promo_code_for_new_student($student_id, $student_promo_code_id) {
        GLOBAL $connect;

        try {

            $query = "INSERT INTO student_used_promo_code (student_id, student_promo_code_id)
                                                    VALUES (:student_id, :student_promo_code_id)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_promo_code_id', $student_promo_code_id, PDO::PARAM_INT);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function check_promo_code ($promo_code) {
        GLOBAL $connect;

        try {

            $query = "SELECT spc.id
                        FROM student_promo_code spc
                        WHERE spc.code = :code";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':code', $promo_code, PDO::PARAM_STR);
            $stmt->execute();

            $row_count = $stmt->rowCount();

            if ($row_count == 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            }
            return 0;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
?>