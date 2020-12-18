<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    include_once($root.'/common/global_controller.php');
    include_once($root.'/common/constants.php');
    check_student_access();

    $data = array(); 

    if (isset($_GET['select_lesson'])) {
    	try {

    		$hour = date('H');
            if ($hour >= 7) {
                $week_day_id = date('w') == 0 ? 7 : date('w');
            } else {
                $week_day_id = date('w', strtotime('-1 days'));
                $week_day_id = $week_day_id == 0 ? 7 : $week_day_id;
            }
    		$student_id = $_SESSION['user_id'];
    		$group_id = $_GET['group_id'];
    		$stmt = $connect->prepare("SELECT gs.id AS group_student_id,
                                            gi.subject_id,
                                            gi.topic_id,
                                            gi.id AS group_info_id
    									FROM group_info gi,
    										group_student gs,
    										status
    									WHERE gi.id = :group_info_id
    										AND gs.group_info_id = gi.id
    										AND gs.student_id = :student_id
    										AND status.id = gi.status_id
    										AND status.is_active = 1");

    		$stmt->bindParam(':group_info_id', $group_id, PDO::PARAM_INT);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$group_result = $stmt->fetch(PDO::FETCH_ASSOC);
            $is_army_group = get_is_army_group($group_result['group_info_id']);

    		if ($stmt->rowCount() == 1) {
                $result = array();

                $query = "SELECT stt.id AS student_trial_test_id,
                                sj.id AS subject_id,
                                sj.title AS subject_title
                            FROM student_trial_test stt,
                                group_student_trial_test gstt,
                                lesson_progress lp,
                                trial_test tt,
                                subject sj
                            WHERE stt.student_id = :student_id
                                AND stt.result IS NULL
                                AND gstt.student_trial_test_id = stt.id
                                AND lp.id = gstt.lesson_progress_id
                                AND lp.group_info_id = :group_info_id
                                AND tt.id = stt.trial_test_id
                                AND sj.id = tt.subject_id
                                AND stt.appointment_date > '2020-10-25'
                                AND DATE_ADD(DATE_FORMAT(stt.appointment_date, '%Y-%m-%d'), INTERVAL 3 DAY) <= DATE_FORMAT(NOW(), '%Y-%m-%d')";
                                
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_info_id', $group_result['group_info_id'], PDO::PARAM_INT);
                $stmt->execute();

                $student_trial_test_info = array();
                if ($stmt->rowCount() > 0) {
                    $student_trial_test_result = $stmt->fetchAll();
                    foreach ($student_trial_test_result as $value) {
                        if (!isset($student_trial_test_info[$value['subject_id']])) {
                            $student_trial_test_info[$value['subject_id']] = array();
                        }
                        $student_trial_test_info[$value['subject_id']] = array('student_trial_test_id' => $value['student_trial_test_id'],
                                                                                'subject_title' => $value['subject_title']);
                    }
                } else {
        			$stmt = $connect->prepare("SELECT lp.id AS lp_id,
        											st.id AS subtopic_id,
        											st.title,
                                                    lp.created_date
        									FROM lesson_progress lp,
        										subtopic st
        									WHERE lp.group_info_id = :group_id
        										AND ((NOW() <= DATE_ADD(lp.created_date, INTERVAL 24 HOUR)
    						 					        AND NOW() >= lp.created_date)
                                                    OR (1 = (SELECT IF(TIME(DATE_FORMAT(NOW(), '%H:%i:%s')) > TIME('07:00:00'), 
                                                                    (fma2.created_date < TIMESTAMP(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00')
                                                                        AND fma2.created_date >= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')), 
                                                                    (fma2.created_date < TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')
                                                                        AND fma2.created_date >= TIMESTAMP(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d'), '07:00:00'))) AS is_active
                                                            FROM forced_material_access fma2,
                                                                group_student gs2
                                                            WHERE fma2.lesson_progress_id = lp.id
                                                                AND fma2.group_student_id = gs2.id
                                                                AND gs2.student_id = :student_id
                                                            ORDER BY fma2.created_date DESC
                                                            LIMIT 1)))
    						 					AND st.id = lp.subtopic_id
    						 				ORDER BY lp.id ASC");
        			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
                    $stmt->bindParam(':student_id', $_SESSION['user_id'], PDO::PARAM_INT);
        			$stmt->execute();
        			$lesson_progress_result = $stmt->fetchAll();
        			
                    $material_orders = get_subtopics_total_order($group_result['subject_id']);

        			foreach ($lesson_progress_result as $value) {
        				$tutorial_video = get_tutorial_video($group_result['group_student_id'], $value['lp_id']);
        				$tutorial_document = get_tutorial_document($group_result['group_student_id'], $value['lp_id']);
        				$end_video = get_end_video($group_result['group_student_id'], $value['lp_id']);
                        $material_test = get_material_test($value['subtopic_id'], $value['lp_id'], $group_result['group_student_id']);
                        $class_work_files = get_class_work_files($group_result['group_student_id'], $value['lp_id']);

                        $is_last_subtopic = false;
                        if (count($material_orders['content'][$group_result['topic_id']]['subtopic']) == $material_orders['content_subtopic'][$value['subtopic_id']]['subtopic_order']
                            || count($material_orders['content'][$group_result['topic_id']]['subtopic']) == $material_orders['content_subtopic'][$value['subtopic_id']]['subtopic_order'] + 1) {
                            $is_last_subtopic = true;
                        }
        				if (count($tutorial_video) > 0
                            || count($tutorial_document) > 0
                            || count($end_video) > 0
                            || $material_test['test_pages'] > 0
                            || $is_last_subtopic) {
                            $is_exist_material_test = $material_test['test_pages'] > 0 ? true : false;
        					$result[$value['lp_id']] = array('subtopic_id' => $value['subtopic_id'],
    														'subtopic_title' => $value['title'],
                                                            'access_date' => $value['created_date'],
                                                            'is_last_subtopic' => $is_last_subtopic,
                                                            'is_army_group' => $is_army_group,
    														'materials' => array('tutorial_video' => $tutorial_video,
    			    															'tutorial_document' => $tutorial_document,
    			    															'end_video' => $end_video,
                                                                                'is_exist_material_test' => $is_exist_material_test,
                                                                                'material_test_action_id' => $material_test['mta_id'],
                                                                                'has_finished' => $material_test['has_finished'],
                                                                                'class_work_files' => $class_work_files,
                                                                                'test_result' => get_material_test_result($value['subtopic_id'], $material_test['mta_id'])));
        				}
        			}
                }

    			$_SESSION['group_id'] = $group_id;
     			$data['success'] = true;
    			$_SESSION['materials'] = $result;
                $_SESSION['student_trial_test_info'] = $student_trial_test_info;
    		} else {
    			$_SESSION['group_id'] = 0;
    			$data['success'] = false;
    		}
    		
    	} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
    } else if (isset($_GET['select_subtopic'])) {
    	unset($_SESSION['group_id']);
    	$data['success'] = true;
    	echo json_encode($data);
    } else if (isset($_GET['set_material_start_time'])) {
        try {
            $obj_id = $_POST['obj_id'];
            $action_id = $_POST['action_id'];
            $obj = $_POST['obj'];

            $log = get_material_action_log($obj_id, $action_id, $obj);
            $data['obj_id'] = $obj_id;
            $data['action_id'] = $action_id;
            $data['obj'] = $obj;
            if ($log['opened_time'] == '' || $log['opened_time'] == null) {
                $sql = '';
                $data['opened_time'] = $log['opened_time'];
                if ($obj == 'tutorial_video') {
                    $sql = "INSERT INTO tutorial_video_action_log
                                (tutorial_video_action_id, tutorial_video_id, opened_date, access_before)
                            SELECT :action_id, 
                                tv.id,
                                NOW(), 
                                DATE_ADD(NOW(), INTERVAL 24 HOUR)
                            FROM tutorial_video tv
                            WHERE tv.id = :obj_id";
                            // IF(tv.duration <= 3000, DATE_ADD(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 3 HOUR))
                } else if ($obj == 'end_video') {
                    $sql = "INSERT INTO end_video_action_log
                                (end_video_action_id, end_video_id, opened_date, access_before)
                            SELECT :action_id, 
                                ev.id,
                                NOW(), 
                                DATE_ADD(NOW(), INTERVAL 24 HOUR)
                            FROM end_video ev
                            WHERE ev.id = :obj_id";
                } else if ($obj == 'tutorial_document') {
                    $sql = "INSERT INTO tutorial_document_action_log
                                (tutorial_document_action_id, tutorial_document_id, opened_date)
                            SELECT :action_id, 
                                td.id,
                                NOW()
                            FROM tutorial_document td
                            WHERE td.id = :obj_id";
                }
                $stmt = $connect->prepare($sql);
                $stmt->bindParam(':obj_id', $obj_id, PDO::PARAM_INT);
                $stmt->bindParam(':action_id', $action_id, PDO::PARAM_INT);
                $stmt->execute();

                // if ($obj == 'tutorial_video') {
                //     check_and_set_coins_for_tutorial_video($action_id);
                // }
            }
            $log = get_material_action_log($obj_id, $action_id, $obj);
            $data['access_before'] = $log['formatted_access_before'];
            $data['success'] = true;

            if (isset($_SESSION['materials'])) {
                foreach ($_SESSION['materials'] as $lp_key => $lp) {
                    foreach ($lp['materials'][$obj] as $video_key => $video) {
                        if ($video['id'] == $obj_id && $video['action_id'] == $action_id) {
                            $_SESSION['materials'][$lp_key]['materials'][$obj][$video_key]['log'] = get_material_action_log($obj_id, $action_id, $obj);
                        } 
                    }
                }
            } 
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['check_video_access'])) {
        try {
            $video_id = $_GET['video_id'];
            $action_id = $_GET['action_id'];
            $obj = $_GET['obj'];

            $log = get_material_action_log($video_id, $action_id, $obj);

            if ($log['is_access']) {
                $data['success'] = true;
            } else {
                $data['success'] = false;
                if (isset($_SESSION['materials'])) {
                    foreach ($_SESSION['materials'] as $lp_key => $lp) {
                        foreach ($lp['materials'][$obj] as $video_key => $video) {
                            if ($video['id'] == $video_id && $video['action_id'] == $action_id) {
                                $_SESSION['materials'][$lp_key]['materials'][$obj][$video_key]['link'] = '';
                                $_SESSION['materials'][$lp_key]['materials'][$obj][$video_key]['log']['is_access'] = false;
                            } 
                        }
                    }
                } 
            }
            $data['log'] = $log;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_POST['submit-test'])) {
        $data = array();
        try {
            $answers = $_POST['answer'];
            $subtopic_id = $_POST['subtopic_id'];
            $mta_id = $_POST['material_test_action_id'];
            $test_result = check_students_test($answers, $subtopic_id);
            $test_result_json = json_encode($test_result);

            $query = "UPDATE material_test_result
                        SET actual_result = :actual_result,
                            total_result = :total_result,
                            result_json = :result_json,
                            finish_date = NOW()
                        WHERE subtopic_id = :subtopic_id
                            AND material_test_action_id = :mta_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':actual_result', $test_result['actual_result'], PDO::PARAM_INT);
            $stmt->bindParam(':total_result', $test_result['total_result'], PDO::PARAM_INT);
            $stmt->bindParam(':result_json', $test_result_json, PDO::PARAM_STR);
            $stmt->bindParam(":subtopic_id", $subtopic_id, PDO::PARAM_INT);
            $stmt->bindParam(":mta_id", $mta_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!check_if_group_student_is_in_school($mta_id)) {
                check_and_set_coins_for_material_test($mta_id, $test_result);
            }

            $query = "SELECT mta.lesson_progress_id,
                            mta.group_student_id
                        FROM material_test_action mta
                        WHERE mta.id = :mta_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':mta_id', $mta_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($_SESSION['materials'])) {
                $material_test = get_material_test($subtopic_id, $sql_result['lesson_progress_id'], $sql_result['group_student_id']);
                $is_exist_material_test = $material_test['test_pages'] > 0 ? true : false;
                $_SESSION['materials'][$sql_result['lesson_progress_id']]['materials']['is_exist_material_test'] = $is_exist_material_test;
                $_SESSION['materials'][$sql_result['lesson_progress_id']]['materials']['material_test_action_id'] = $material_test['mta_id'];
                $_SESSION['materials'][$sql_result['lesson_progress_id']]['materials']['has_finished'] = $material_test['has_finished'];
                $_SESSION['materials'][$sql_result['lesson_progress_id']]['materials']['test_result'] = get_material_test_result($subtopic_id, $material_test['mta_id']);
            }
            $_SESSION['test_finished'] = true;

            set_is_finish($subtopic_id, $mta_id);
            echo date('Y-m-d H:i:s');
            set_material_test_medal($mta_id);

            header('Location:'.$ab_root.'/academy/student/lesson/testing.php?subtopic_id='.$subtopic_id.'&mta='.$mta_id);
        } catch (Exception $e) {
            throw $e;
        }
    } else if (isset($_GET['get_material_test_solve'])) {
        try {

            $subtopic_id = $_GET['subtopic_id'];
            $material_test_action_id = $_GET['material_test_action_id'];

            $query = "SELECT mta.is_finish
                        FROM material_test_action mta,
                            material_test_result mtr
                        WHERE mta.id = :id
                            AND mtr.material_test_action_id = mta.id
                            AND mtr.actual_result IS NOT NULL
                            AND mtr.total_result IS NOT NULL";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->execute();
            $is_test_submitted = $stmt->fetch(PDO::FETCH_ASSOC)['is_finish'];

            if ($is_test_submitted == 1) {
                $query = "SELECT mts.title,
                                mts.link
                            FROM material_test_solve mts
                            WHERE mts.subtopic_id = :subtopic_id
                            ORDER BY mts.file_order";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
                $stmt->execute();
                $query_result = $stmt->fetchAll();

                $result = array();
                foreach ($query_result as $value) {
                    array_push($result, array('title' => $value['title'],
                                                'link' => $ab_root.'/academy'.$value['link']));
                }
                $data['result'] = $result;
            } else {
                $data['result'] = array();
            }

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['remove_registration_reserve'])) {
        try {
            $registration_reserve_id = $_GET['registration_reserve_id'];

            $query = "DELETE FROM registration_reserve WHERE id = :registration_reserve_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':registration_reserve_id', $registration_reserve_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['get_material_titles_by_subtopic'])) {
        $subtopic_ids = implode(",", json_decode($_POST['subtopic_ids']));

        try {

            $query = "SELECT sj.id AS subject_id,
                            sj.title AS subject_title,
                            t.id AS topic_id,
                            t.title AS topic_title,
                            st.id AS subtopic_id,
                            st.title AS subtopic_title
                        FROM subject sj,
                            topic t,
                            subtopic st
                        WHERE st.id IN (".$subtopic_ids.")
                            AND t.id = st.topic_id
                            AND sj.id = t.subject_id";
            $stmt = $connect->prepare($query);
            // $stmt->bindParam(':subtopic_ids', $subtopic_ids, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                $result[$value['subtopic_id']] = array('subtopic_title' => $value['subtopic_title'],
                                                        'topic_id' => $value['topic_id'],
                                                        'topic_title' => $value['topic_title'],
                                                        'subject_id' => $value['subject_id'],
                                                        'subject_title' => $value['subject_title']);
            }

            $data['result'] = $result;
            $data['success'] = true;
            $data['subtopic_ids'] = $subtopic_ids;
            $data['query_result'] = $query_result;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['upload_class_work_img'])) {
        try {
            $file = $_FILES['class_work_img'];
            $student_id = $_SESSION['user_id'];
            $lesson_progress_id = $_POST['lesson_progress_id'];

            if ($file['error'] == UPLOAD_ERR_OK) {
                $allowed_extentions = array('jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG');
                $filename = $file['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed_extentions)) {
                    $query = "SELECT tda.id
                                FROM tutorial_document_action tda,
                                    group_student gs
                                WHERE tda.lesson_progress_id = :lesson_progress_id
                                    AND tda.group_student_id = gs.id
                                    AND gs.student_id = :student_id
                                ORDER BY tda.accessed_date ASC
                                LIMIT 1";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                    $stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $tutorial_document_action_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

                    $query = "SELECT gscws.id
                                FROM group_student_class_work_submit gscws
                                WHERE gscws.tutorial_document_action_id = :tutorial_document_action_id";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':tutorial_document_action_id', $tutorial_document_action_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $gscws_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                    if ($gscws_id == "") {
                        $query = "INSERT group_student_class_work_submit (tutorial_document_action_id) VALUES (:tutorial_document_action_id)";
                        $stmt = $connect->prepare($query);
                        $stmt->bindParam(':tutorial_document_action_id', $tutorial_document_action_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $gscws_id = $connect->lastInsertId();
                    }

                    $target_file = '/student/img/class_work_img/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;

                    // if (move_uploaded_file($file['tmp_name'], $root.$target_file)) {
                    if (compressImage($file['tmp_name'], $root.$target_file, 25)) {
                        $query = "INSERT INTO group_student_class_work_submit_files (group_student_class_work_submit_id, file_link)
                                                                            VALUES (:group_student_class_work_submit_id, :file_link)";
                        $stmt = $connect->prepare($query);
                        $stmt->bindParam(':group_student_class_work_submit_id', $gscws_id, PDO::PARAM_INT);
                        $stmt->bindParam(':file_link', $target_file, PDO::PARAM_STR);
                        $stmt->execute();
                        $gscwsf_id = $connect->lastInsertId();

                        if (isset($_SESSION['materials'])) {
                            $_SESSION['materials'][$lesson_progress_id]['materials']['class_work_files'][$gscwsf_id] = $target_file;
                        }

                        $data['file_link'] = $ab_root.'/academy/'.$target_file;
                        $data['group_student_class_work_submit_file_id'] = $gscwsf_id;
                        $data['lesson_progress_id'] = $lesson_progress_id;
                        $data['success'] = true;
                    } else {
                        $data['message'] = 'Сурет жүктелмеді. Қайталап көріңіз';
                        $data['success'] = false;
                    }
                    $data['success'] = true;
                } else {
                    $data['success'] = false;
                    $data['message'] = 'Жүктелген суреттің типі "jpeg", "jpg", "png" болу керек.';
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
    } else if (isset($_GET['remove-submitted-class-work'])) {
        try {
            
            $gscwsf_id = $_GET['gscwsf_id'];
            $lp_id = $_GET['lp_id'];
            $query = "SELECT gscwsf.file_link
                        FROM group_student_class_work_submit_files gscwsf
                        WHERE gscwsf.id = :gscwsf_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':gscwsf_id', $gscwsf_id, PDO::PARAM_INT);
            $stmt->execute();
            $file_link = $stmt->fetch(PDO::FETCH_ASSOC)['file_link'];

            if ($file_link != '') {
                $query = "DELETE FROM group_student_class_work_submit_files WHERE id = :id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':id', $gscwsf_id, PDO::PARAM_INT);
                $stmt->execute();
                if (file_exists($root.$file_link)) {
                    unlink($root.$file_link);
                }
                if (isset($_SESSION['materials'])) {
                    unset($_SESSION['materials'][$lp_id]['materials']['class_work_files'][$gscwsf_id]);
                }
            }
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['set_bonus_days_from_coins'])) {
        try {
            $group_student_id = $_GET['group_student_id'];

            if (check_if_enought_balance($group_student_id)) {
                $query = "SELECT gs.status,
                                gs.group_info_id,
                                gs.student_id
                            FROM group_student gs
                            WHERE gs.id = :group_student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->execute();
                $query_result = $stmt->fetch(PDO::FETCH_ASSOC);
                $group_student_status = $query_result['status'];
                $group_info_id = $query_result['group_info_id'];
                $student_id = $query_result['student_id'];

                if ($group_student_status == 'inactive') {
                    set_payment_and_access($group_student_id);
                } else {
                    append_bonus_days($group_student_id);
                }
                set_student_bonus($student_id, $group_info_id);
                update_student_coins($student_id);
                $data['success'] = true;
            } else {
                $data['success'] = false;
            }

        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_POST['set-freeze-lessons'])) {
        try {

            $from_date_splitted = explode('.', $_POST['from-date']);
            $to_date_splitted = explode('.', $_POST['to-date']);
            $from_date = $from_date_splitted[2].'-'.$from_date_splitted[1].'-'.$from_date_splitted[0];
            $to_date = $to_date_splitted[2].'-'.$to_date_splitted[1].'-'.$to_date_splitted[0];
            $student_id = $_SESSION['user_id'];

            $query = "SELECT gs.group_info_id
                        FROM group_student gs,
                            group_info gi
                        WHERE gs.student_id = :student_id
                            AND 1 = (SELECT count(gs1.id)
                                    FROM group_student gs1 
                                    WHERE gs1.group_info_id = gs.group_info_id)
                            AND gs.group_info_id NOT IN (SELECT ag.group_info_id
                                                        FROM army_group ag)
                            AND gs.is_archive = 0
                            AND gi.id = gs.group_info_id 
                            AND gi.status_id = 2
                            AND gi.is_archive = 0";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $group_infos = $stmt->fetchAll();

            $query = "INSERT INTO freeze_lesson (student_id, group_info_id, from_date, to_date)
                                        VALUES (:student_id, :group_info_id, :from_date, :to_date)";
            foreach ($group_infos as $value) {
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
                $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
                $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
                $stmt->execute();
            }

            header('location:../index.php');
            
        } catch (Exception $e) {
            throw $e;
        }
    } else if (isset($_GET['cancel_freeze_lesson'])) {
        try {

            $student_id = $_SESSION['user_id'];

            $query = "DELETE FROM freeze_lesson WHERE student_id = :student_id AND to_date >= DATE_FORMAT(NOW(), '%Y-%m-%d')";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    } else if (isset($_GET['freeze_off'])) {
        try {
            $group_info_id = $_GET['group_info_id'];

            $query = "UPDATE group_info SET is_freeze = 0 WHERE id = :group_info_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
    }

    function set_payment_and_access ($group_student_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT gi.id AS group_info_id,
                            gi.start_date,
                            gi.start_date <= NOW() AS started
                        FROM group_student gs,
                            group_info gi
                        WHERE gs.id = :group_student_id
                            AND gi.id = gs.group_info_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $group_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($group_info['started']) {
                give_access_and_set_active($group_info['group_info_id'], $group_student_id, $group_info['start_date']);
            } else {
                insert_payment_waiting_status($group_student_id, $group_info['start_date']);
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function give_access_and_set_active ($group_info_id, $group_student_id, $group_start_date) {
        GLOBAL $connect;

        try {

            $query = "SELECT gsp.access_until
                        FROM group_student_payment gsp
                        WHERE gsp.group_student_id = :group_student_id
                        ORDER BY gsp.access_until DESC
                        LIMIT 1";

            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $last_payment_day = $stmt->fetch(PDO::FETCH_ASSOC)['access_until'];

            $date_point = $group_start_date;
            if (strtotime($last_payment_day) > $group_start_date) {
                $date_point = $last_payment_day;
            }

            $query = "SELECT lp.id AS lesson_progress_id
                        FROM lesson_progress lp
                        WHERE lp.group_info_id = :group_info_id
                            AND DATE_FORMAT(lp.created_date, '%Y-%m-%d') >= :date_point";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
            $stmt->bindParam(':date_point', $date_point, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $lp_ids = array();

            foreach ($query_result as $value) {
                array_push($lp_ids, $value['lesson_progress_id']);
            }

            set_access($group_student_id, $lp_ids);
            insert_payment_active_status($group_student_id, $date_point);
        } catch (Exception $e) {
            throw $e;
        }
    }

    function set_access ($group_student_id, $lesson_progress_ids) {
        GLOBAL $connect;

        try {

            foreach ($lesson_progress_ids as $lp_id) {
                $query = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id)
                                                        VALUES (:lesson_progress_id, :group_student_id)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->execute();
                $forced_material_access_id = $connect->lastInsertId();

                $query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                        VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
                $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                $stmt->execute();

                $query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                            VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
                $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                $stmt->execute();

                $query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
                                                VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
                $stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
                $stmt->execute();

                // insert into material_test_action (group_student_id, lesson_progress_id, is_finish)
                // select 0, 0, (select count(mt.id) = 0 FROM material_test mt, lesson_progress lp where mt.subtopic_id = lp.subtopic_id and lp.id = 0)
                $query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
                                                SELECT :group_student_id, :lesson_progress_id, (SELECT count(mt.id) = 0
                                                                                                FROM material_test mt,
                                                                                                    lesson_progress lp
                                                                                                WHERE lp.id = :lesson_progress_id
                                                                                                    AND mt.subtopic_id = lp.subtopic_id)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
                $stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function insert_payment_active_status ($group_student_id, $start_date) {
        GLOBAL $connect;
        GLOBAL $days_for_collected_coins;

        try {

            $access_until = date('Y-m-d', strtotime($start_date.' + '.$days_for_collected_coins.' days'));

            $query = "INSERT INTO group_student_payment
                                (group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type, partial_payment_days)
                        VALUES (:group_student_id, NOW(), :start_date, :access_until, 1, NOW(), 'balance', :partial_payment_days)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
            $stmt->bindParam(':partial_payment_days', $days_for_collected_coins, PDO::PARAM_INT);
            $stmt->execute();

            $query = "UPDATE group_student SET status = 'active' WHERE id = :group_student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function insert_payment_waiting_status ($group_student_id, $group_start_date) {
        GLOBAL $connect;
        GLOBAL $days_for_collected_coins;

        try {

            $query = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, payment_type, partial_payment_days)
                                                    VALUES (:group_student_id, NOW(), :start_date, 'balance', :partial_payment_days)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $group_start_date, PDO::PARAM_STR);
            $stmt->bindParam(':partial_payment_days', $days_for_collected_coins, PDO::PARAM_INT);
            $stmt->execute();

            $query = "UPDATE group_student SET status = 'waiting' WHERE id = :group_student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (Exception $e) {
            throw $e;
        }
    }

    function append_bonus_days ($group_student_id) {
        GLOBAL $connect;
        GLOBAL $days_for_collected_coins;

        try {

            $query = "INSERT INTO group_student_payment (group_student_id, payed_date, payment_type, partial_payment_days)
                                                    VALUES (:group_student_id, NOW(), 'balance', :partial_payment_days)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->bindParam(':partial_payment_days', $days_for_collected_coins, PDO::PARAM_INT);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function set_student_bonus ($student_id, $group_info_id) {
        GLOBAL $connect;
        GLOBAL $days_for_collected_coins;
        GLOBAL $bonus_days_from_coins_comment;

        try {

            $query = "INSERT INTO student_balance (student_id, used_for_group, is_used, days, comment, used_date)
                                            VALUES (:student_id, :used_for_group, 1, :days, :comment, NOW())";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':used_for_group', $group_info_id, PDO::PARAM_INT);
            $stmt->bindParam(':days', $days_for_collected_coins, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $bonus_days_from_coins_comment, PDO::PARAM_STR);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function update_student_coins ($student_id) {
        GLOBAL $connect;
        GLOBAL $amount_of_coins_for_bonus;

        try {

            $query = "UPDATE student_coins SET total_coins = total_coins - :coins WHERE student_id = :student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':coins', $amount_of_coins_for_bonus, PDO::PARAM_INT);
            $stmt->execute();

            $query = "INSERT INTO student_coin_log (student_id, used_coins) VALUES (:student_id, :used_coins)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':used_coins', $amount_of_coins_for_bonus, PDO::PARAM_INT);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function check_if_enought_balance ($group_student_id) {
        GLOBAL $connect;
        GLOBAL $amount_of_coins_for_bonus;

        try {

            $query = "SELECT sc.total_coins
                        FROM student_coins sc,
                            group_student gs
                        WHERE gs.id = :group_student_id
                            AND sc.student_id = gs.student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();

            $total_coins = $stmt->fetch(PDO::FETCH_ASSOC)['total_coins'];

            return intval($total_coins) >= $amount_of_coins_for_bonus ? true : false;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function set_is_finish($subtopic_id, $material_test_action_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT (SELECT count(mtr.id)
                                FROM material_test_result mtr
                                WHERE mtr.material_test_action_id = :mta_id) AS material_test_result_count,
                            (SELECT count(mt.id)
                                FROM material_test mt
                                WHERE mt.subtopic_id = :subtopic_id) AS material_test_count";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':mta_id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
            $stmt->execute();

            $sql_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($sql_result['material_test_result_count'] == 1 && $sql_result['material_test_count'] >= 1) {
                $query = "UPDATE material_test_action SET is_finish = 1 WHERE id = :mta_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':mta_id', $material_test_action_id, PDO::PARAM_INT);
                $stmt->execute();
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    
    function check_students_test($answers, $subtopic_id) {
        GLOBAL $connect;
        try {

            $query = "SELECT ans.id,
                            ans.numeration,
                            ans.prefix,
                            ans.torf
                        FROM answers ans
                        WHERE ans.subtopic_id = :subtopic_id
                        ORDER BY ans.numeration, ans.prefix";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql_result = $stmt->fetchAll();

            $result = array('actual_result' => 0,
                            'total_result' => count($answers),
                            'results' => array(),
                            'json' => array());

            $answers_result = array();
            foreach ($sql_result as $value) {
                if (!isset($answers_result[$value['numeration']])) {
                    $answers_result[$value['numeration']] = array();
                }

                $actual = false;
                if ($answers[$value['numeration']] == $value['id']) {
                    $actual = true;
                    if ($value['torf'] == 1) {
                        $result['actual_result']++;
                    }
                }

                if ($value['torf'] == 1) {
                    $result['json'][$value['numeration']] = array('expected' => $value['id'],
                                                                    'actual' => $answers[$value['numeration']]);
                }

                $answers_result[$value['numeration']][$value['id']] = array('prefix' => $value['prefix'],
                                                                            'expected' => $value['torf'] == '1' ? true : false,
                                                                            'actual' => $actual);
            }
            $result['results'] = $answers_result;

            return $result;
            
        } catch (Exception $e) {
            throw $e;
        }
    }


    function get_tutorial_video($group_student_id, $lp_id) {
    	GLOBAL $connect; 

    	try {
    		$stmt = $connect->prepare("SELECT lp.id AS lp_id, 
    										tv.id AS tv_id, 
                                            tva.id AS tva_id,
    										tv.link,
    										tv.title,
    										tv.duration,
    										tv.video_order,
                                            tv.pop_up
    									FROM tutorial_video tv,
    										tutorial_video_action tva,
    										lesson_progress lp
    									WHERE lp.id = :lp_id
    										AND tva.lesson_progress_id = lp.id
											AND tva.group_student_id = :group_student_id
											AND NOW() <= DATE_ADD(tva.accessed_date, INTERVAL 24 HOUR)
											AND NOW() >= tva.accessed_date
											AND tv.subtopic_id = lp.subtopic_id
										ORDER BY lp.id, tv.video_order ASC");
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lp_id', $lp_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$sql_res = $stmt->fetchAll();

    		$result = array();
    		foreach ($sql_res as $value) {
                $log = get_material_action_log($value['tv_id'], $value['tva_id'], 'tutorial_video');
    			$result[$value['video_order']] = array('id' => $value['tv_id'],
                                                        'action_id' => $value['tva_id'],
														'link' => $log['is_access'] ? $value['link'] : '',
														'title' => $value['title'],
														'duration' => $value['duration'],
                                                        'pop_up' => $value['pop_up'],
                                                        'log' => $log);
    		}

    		return $result;

    	} catch (Exception $e) {
    		return array();
    		// throw $e;
    	}
    }

    function get_tutorial_document($group_student_id, $lp_id) {
    	GLOBAL $connect; 

    	try {
    		$stmt = $connect->prepare("SELECT lp.id AS lp_id,
    										td.id AS td_id,
                                            tda.id AS tda_id,
    										td.link,
    										td.title,
    										td.document_order
    									FROM tutorial_document td,
    										tutorial_document_action tda,
    										lesson_progress lp
    									WHERE lp.id = :lp_id
    										AND tda.lesson_progress_id = lp.id
											AND tda.group_student_id = :group_student_id
											AND NOW() <= DATE_ADD(tda.accessed_date, INTERVAL 24 HOUR)
											AND NOW() >= tda.accessed_date
											AND td.subtopic_id = lp.subtopic_id
										ORDER BY td.document_order ASC");
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lp_id', $lp_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$sql_res = $stmt->fetchAll();
    		
    		$result = array();
    		foreach ($sql_res as $value) {
                $log = get_material_action_log($value['td_id'], $value['tda_id'], 'tutorial_document');
    			$result[$value['document_order']] = array('id' => $value['td_id'],
                                                            'action_id' => $value['tda_id'],
	    													'link' => $value['link'],
	    													'title' => $value['title'],
                                                            'log' => $log);
    		}

    		return $result;

    	} catch (Exception $e) {
    		return array();
    		throw $e;
    	}
    }

    function get_end_video($group_student_id, $lp_id) {
    	GLOBAL $connect; 

    	try {
    		$stmt = $connect->prepare("SELECT lp.id AS lp_id,
    										ev.id AS ev_id, 
                                            eva.id AS eva_id,
    										ev.link,
    										ev.title,
    										ev.duration,
    										ev.video_order
    									FROM end_video ev,
    										end_video_action eva,
    										lesson_progress lp
    									WHERE lp.id = :lp_id
    										AND eva.lesson_progress_id = lp.id
											AND eva.group_student_id = :group_student_id
											AND NOW() <= DATE_ADD(eva.accessed_date, INTERVAL 24 HOUR)
											AND NOW() >= eva.accessed_date
											AND ev.subtopic_id = lp.subtopic_id
										ORDER BY ev.video_order ASC");
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lp_id', $lp_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$sql_res = $stmt->fetchAll();
    		
    		$result = array();
    		foreach ($sql_res as $key => $value) {
                $log = get_material_action_log($value['ev_id'], $value['eva_id'], 'end_video');
    			$result[$value['video_order']] = array('id' => $value['ev_id'],
                                                        'action_id' => $value['eva_id'], 
    													'link' => $log['is_access'] ? $value['link'] : '',
    													'title' => $value['title'],
    													'duration' => $value['duration'],
                                                        'log' => $log);
    		}

    		return $result;

    	} catch (Exception $e) {
    		return array();
    		throw $e;
    	}
    }

    function get_material_test_result($subtopic_id, $material_test_action_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT mtr.actual_result,
                            mtr.total_result,
                            mtr.start_date,
                            mtr.finish_date
                        FROM material_test_result mtr
                        WHERE mtr.subtopic_id = :subtopic_id
                            AND mtr.material_test_action_id = :material_test_action_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
            $stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql_result = $stmt->fetch(PDO::FETCH_ASSOC);

            $result = array('material_test_action_id' => $material_test_action_id,
                            'actual_result' => $sql_result['actual_result'],
                            'total_result' => $sql_result['total_result'],
                            'start_date' => $sql_result['start_date'],
                            'finish_date' => $sql_result['finish_date']);
            return $result;

        } catch (Exception $e) {
            throw $e;
        }
        
    }

    function get_class_work_files ($group_student_id, $lesson_progress_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT gscwsf.id AS gscwsf_id,
                            gscwsf.file_link AS file_link
                        FROM group_student_class_work_submit gscws,
                            group_student_class_work_submit_files gscwsf,
                            tutorial_document_action tda
                        WHERE tda.lesson_progress_id = :lesson_progress_id
                            AND tda.group_student_id = :group_student_id
                            AND gscws.tutorial_document_action_id = tda.id
                            AND gscwsf.group_student_class_work_submit_id = gscws.id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();
            foreach ($query_result as $value) {
                $result[$value['gscwsf_id']] = $value['file_link'];
            }
            return $result;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
 
    function get_material_test($subtopic_id, $lesson_progress_id, $group_student_id) {
        GLOBAL $connect;

        try {
            $query = "SELECT mta.id AS mta_id,
                            (SELECT count(mt.id)
                            FROM material_test mt
                            WHERE mt.subtopic_id = :subtopic_id) AS test_pages,
                            (SELECT count(mtr.id)
                            FROM material_test_result mtr
                            WHERE mtr.subtopic_id = :subtopic_id
                                AND mtr.material_test_action_id = mta.id
                                AND mtr.actual_result IS NOT NULL
                                AND mtr.total_result IS NOT NULL) AS has_finished
                        FROM material_test_action mta
                        WHERE mta.lesson_progress_id = :lesson_progress_id
                            AND mta.group_student_id = :group_student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
            $stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (Exception $e) {
            return array();
            throw $e;
        }
    }

    function check_and_set_coins_for_tutorial_video ($tutorial_video_action_id) { //depreciated
        GLOBAL $connect;
        GLOBAL $coins_for_tutorial_video;

        try {
            $query = "SELECT tv.id AS tutorial_video_id
                        FROM tutorial_video tv,
                            lesson_progress lp
                        WHERE tv.pop_up = 0
                            AND tv.subtopic_id = lp.subtopic_id
                            AND lp.id = (SELECT tva2.lesson_progress_id
                                        FROM tutorial_video_action tva2
                                        WHERE tva2.id = :tutorial_video_action_id)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':tutorial_video_action_id', $tutorial_video_action_id, PDO::PARAM_INT);
            $stmt->execute();
            $tv_query_result = $stmt->fetchAll();

            $tv_ids = array();

            foreach ($tv_query_result as $value) {
                array_push($tv_ids, $value['tutorial_video_id']);
            }

            $query = "SELECT (SELECT count(DISTINCT tval.tutorial_video_id)
                                FROM tutorial_video_action_log tval
                                WHERE tval.tutorial_video_action_id IN 
                                                        (SELECT tva.id
                                                        FROM tutorial_video_action tva
                                                        WHERE tva.lesson_progress_id = 
                                                                    (SELECT tva2.lesson_progress_id
                                                                        FROM tutorial_video_action tva2
                                                                        WHERE tva2.id = :tutorial_video_action_id))
                                    AND tval.tutorial_video_id IN (".implode(',', $tv_ids).")) AS tval_count";

            $stmt = $connect->prepare($query);
            $stmt->bindParam(':tutorial_video_action_id', $tutorial_video_action_id, PDO::PARAM_INT);
            $stmt->execute();
            $tval_count = intval($stmt->fetch(PDO::FETCH_ASSOC)['tval_count']);

            if (count($tv_ids) == $tval_count) {
                $query = "INSERT INTO coin_log (object_type, object_id, coins) VALUES ('tutorial_video_action', :tva_id, :coins)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':tva_id', $tutorial_video_action_id, PDO::PARAM_INT);
                $stmt->bindParam(':coins', $coins_for_tutorial_video, PDO::PARAM_INT);
                $stmt->execute();

                $query = "SELECT gs.student_id
                            FROM group_student gs,
                                tutorial_video_action tva
                            WHERE tva.id = :tutorial_video_action_id
                                AND gs.id = tva.group_student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':tutorial_video_action_id', $tutorial_video_action_id, PDO::PARAM_INT);
                $stmt->execute();
                $student_id = $stmt->fetch(PDO::FETCH_ASSOC)['student_id'];

                $query = "SELECT sc.total_coins
                            FROM student_coins sc
                            WHERE sc.student_id = :student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->execute();
                $row_count = $stmt->rowCount();

                if ($row_count == 0) {
                    $query = "INSERT INTO student_coins (student_id, total_coins) VALUES (:student_id, :total_coins)";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                    $stmt->bindParam(':total_coins', $coins_for_tutorial_video, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $query = "UPDATE student_coins SET total_coins = total_coins + :coins WHERE student_id = :student_id";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                    $stmt->bindParam(':coins', $coins_for_tutorial_video, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    function check_and_set_coins_for_material_test($material_test_action_id, $test_result) {
        GLOBAL $connect;
        // actual_result
        // total_result
        try {

            $coins = round(($test_result['actual_result'] / $test_result['total_result']) * 100);

            $query = "SELECT gs.student_id
                        FROM material_test_action mta,
                            group_student gs
                        WHERE mta.id = :material_test_action_id
                            AND gs.id = mta.group_student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->execute();
            $student_id = $stmt->fetch(PDO::FETCH_ASSOC)['student_id'];

            $query = "INSERT INTO coin_log (object_type, object_id, coins) VALUES ('material_test_action', :material_test_action_id, :coins)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
            $stmt->execute();


            $query = "SELECT sc.total_coins
                        FROM student_coins sc
                        WHERE sc.student_id = :student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            if ($row_count == 0) {
                $query = "INSERT INTO student_coins (student_id, total_coins) VALUES (:student_id, :total_coins)";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->bindParam(':total_coins', $coins, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $query = "UPDATE student_coins SET total_coins = total_coins + :coins WHERE student_id = :student_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->bindParam(':coins', $coins, PDO::PARAM_INT);
                $stmt->execute();
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function check_if_group_student_is_in_school ($material_test_action_id) {
        GLOBAL $connect;

        try {

            $query = "SELECT sg.id
                        FROM material_test_action mta,
                            group_student gs,
                            school_group sg
                        WHERE mta.id = :material_test_action_id
                            AND gs.id = mta.group_student_id
                            AND sg.group_info_id = gs.group_info_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':material_test_action_id', $material_test_action_id, PDO::PARAM_INT);
            $stmt->execute();

            $row_count = $stmt->rowCount();

            return $row_count == 0 ? false : true;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function set_material_test_medal($material_test_action_id) {
        GLOBAL $ab_root;
        $url = $ab_root.'/academy/student/async_files/set_student_test_medal.php';
        $params = array('material_test_action_id' => $material_test_action_id);
        post_without_wait_curl($url, $params);
    }
?>