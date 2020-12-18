<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

    if (isset($_GET['remove_marathon_student'])) {
    	try {

    		$marathon_form_id = $_GET['marathon_form_id'];

    		$query = "DELETE FROM marathon_form WHERE id = :marathon_form_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':marathon_form_id', $marathon_form_id, PDO::PARAM_INT);
    		$stmt->execute();

    		$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: ['.$e->getMessage().']!!!';
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['submit_marathon_student'])) {
    	try {

    		$marathon_form_id = $_GET['marathon_form_id'];
    		$group_infos = json_decode($_POST['group_infos'], true);
    		$marathon_form_infos = get_marathon_infos_by_id($marathon_form_id);
    		
    		$student_infos = get_student_by_phone($marathon_form_infos['phone']);
    		if (count($student_infos) == 0) {
    			$student_id = insert_into_student($marathon_form_infos);
    		} else {
    			$student_id = $student_infos['id'];
    		}

    		$query = "UPDATE marathon_form SET is_commit = 1 WHERE id = :marathon_form_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':marathon_form_id', $marathon_form_id, PDO::PARAM_INT);
    		$stmt->execute();

    		foreach ($group_infos as $group_info_id) {
    			insert_to_group($student_id, $group_info_id);
    		}


    		$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: ['.$e->getMessage().']!!!';
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    function insert_to_group ($student_id, $group_info_id) {
    	GLOBAL $connect;

    	try {

    		$already_in_group = student_already_in_group($student_id, $group_info_id);

    		if (!$already_in_group) {
    			$group_short_info = get_group_short_info($group_info_id);

    			if ($group_short_info['already_started']) {
    				insert_to_middle($student_id, $group_short_info);
    			} else {
    				insert_to_start($student_id, $group_short_info);
    			}
     		}
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_to_middle($student_id, $group_info) {
    	GLOBAL $connect;

    	try {

    		$access_until = date('Y-m-d', strtotime($group_info['start_date'].' + 7 days'));
    		$date1 = date_create(date('Y-m-d'));
    		$date2 = date_create($access_until);
    		$diff = date_diff($date1, $date2);
    		$partial_payment_days = $diff->format('%a');

    		$lesson_progress_infos = get_all_lesson_progress_by_group_info_id($group_info['group_info_id']);
    		$first_subtopic_id = get_first_subtopic_id_by_topic_id($group_info['topic_id']);
    		$group_student_id = insert_group_student($group_info['group_info_id'], $student_id, $first_subtopic_id, 'active');
    		insert_student_balance($student_id, $group_info['group_info_id'], $partial_payment_days);

 			$query = "INSERT INTO group_student_payment 
 						(group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type, partial_payment_days)
 						VALUES (:group_student_id, NOW(), NOW(), :access_until, 1, NOW(), 'balance', :partial_payment_days)";
 			$stmt = $connect->prepare($query);
 			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
 			$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
 			$stmt->bindParam(':partial_payment_days', $partial_payment_days, PDO::PARAM_INT);
 			$stmt->execute();

 			foreach ($lesson_progress_infos as $info) {
 				$forced_material_access_id = set_forced_material_access($info['lesson_progress_id'], $group_student_id);
 				insert_tutorial_video_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
 				insert_tutorial_document_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
 				insert_end_video_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
 				insert_material_test_action($group_student_id, $lesson_progress_id, $info['subtopic_id']);
 			}


    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_tutorial_video_action ($group_student_id, $lesson_progress_id, $forced_material_access_id) {

    	GLOBAL $connect;

    	try {

    		$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
    												VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
    		$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}

    }

    function insert_tutorial_document_action ($group_student_id, $lesson_progress_id, $forced_material_access_id) {

    	GLOBAL $connect;

    	try {

    		$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id)
    												VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
    		$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}

    }

    function insert_end_video_action ($group_student_id, $lesson_progress_id, $forced_material_access_id) {

    	GLOBAL $connect;

    	try {

    		$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
    										VALUES (:group_student_id, :lesson_progress_id, :forced_material_access_id)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
    		$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}

    }

    function insert_material_test_action($group_student_id, $lesson_progress_id, $subtopic_id) {

    	GLOBAL $connect;

    	try {

    		$query = "SELECT mt.id
    					FROM material_test mt
    					WHERE mt.subtopic_id = :subtopic_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 0) {
    			$no_subtopic = 1;
    		} else {
    			$no_subtopic = 0;
    		}

    		$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
    											VALUES (:group_student_id, :lesson_progress_id, :is_finish)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
    		$stmt->bindParam(':is_finish', $no_subtopic, PDO::PARAM_INT);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}

    }

    function insert_to_start ($student_id, $group_info) {
    	GLOBAL $connect;

    	try {

    		$first_subtopic_id = get_first_subtopic_id_by_topic_id($group_info['topic_id']);

    		$group_student_id = insert_group_student($group_info['group_info_id'], $student_id, $first_subtopic_id, 'waiting');

    		insert_student_balance($student_id, $group_info['group_info_id'], 7);

    		$query = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, payment_type, partial_payment_days)
    											VALUES (:group_student_id, NOW(), :start_date, 'balance', 7)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':start_date', $group_info['start_date'], PDO::PARAM_STR);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function set_forced_material_access ($lesson_progress_id, $group_student_id) {
    	GLOBAL $connect;

    	try {
    		$created_date = date('Y-m-d').' 07:00:00';
    		$query = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id, created_date)
    												VALUES (:lesson_progress_id, :group_student_id, :created_date)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
    		$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
    		$stmt->execute();
    		$forced_material_access_id = $connect->lastInsertId();

    		return $forced_material_access_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_all_lesson_progress_by_group_info_id ($group_info_id) {
    	GLOBAL $connect;

    	try {
    		$result = array();
    		$query = "SELECT lp.id,
    						lp.subtopic_id
    					FROM lesson_progress lp
    					WHERE lp.group_info_id = :group_info_id
    					ORDER BY lp.created_date DESC";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		foreach ($query_result as $value) {
    			array_push($result, array('lesson_progress_id' => $value['id'],
    										'subtopic_id' => $value['subtopic_id']));
    		}
    		
    		return $result;

    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_student_balance ($student_id, $used_for_group, $days) {

    	GLOBAL $connect;

    	try {
    			
    		$query = "INSERT INTO student_balance (student_id, used_for_group, is_used, days, comment, used_date)
    										VALUES (:student_id, :used_for_group, 1, :days, 'Марафонға арналған бонус күндер', NOW())";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':used_for_group', $used_for_group, PDO::PARAM_INT);
    		$stmt->bindParam(':days', $days, PDO::PARAM_INT);
    		$stmt->execute();

    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_group_student ($group_info_id, $student_id, $start_from, $status) {
    	GLOBAL $connect;

    	try {

    		$query = "INSERT INTO group_student (group_info_id, student_id, start_from, status)
    										VALUES (:group_info_id, :student_id, :start_from, :status)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':start_from', $start_from, PDO::PARAM_INT);
    		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
    		$stmt->execute();
    		$group_student_id = $connect->lastInsertId();

    		return $group_student_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_first_subtopic_id_by_topic_id($topic_id) {
    	GLOBAL $connect;

    	try {
    		$query = "SELECT st.id
    					FROM subtopic st
    					WHERE st.topic_id = :topic_id
    						AND st.subtopic_order = 1";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$first_subtopic_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            return $first_subtopic_id;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_group_short_info ($group_info_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT gi.id AS group_info_id, 
    						gi.start_date < NOW() AS already_started,
    						gi.subject_id,
    						gi.topic_id,
    						gi.start_date
    					FROM group_info gi
    					WHERE gi.id = :group_info_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

    		return $query_result;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function student_already_in_group ($student_id, $group_info_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT gs.id
    					FROM group_student gs
    					WHERE gs.student_id = :student_id
    						AND gs.group_info_id = :group_info_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		return $row_count == 0 ? false : true;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_into_student ($marathon_form_infos) {
    	GLOBAL $connect;

    	try {
    		$password = md5('12345');
    		$query = "INSERT INTO student (first_name, last_name, school, class, phone, instagram, city, password)
    								VALUES (:first_name, :last_name, :school, :class, :phone, :instagram, :city, :password)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':last_name', $marathon_form_infos['last_name'], PDO::PARAM_STR);
    		$stmt->bindParam(':first_name', $marathon_form_infos['first_name'], PDO::PARAM_STR);
    		$stmt->bindParam(':school', $marathon_form_infos['school'], PDO::PARAM_STR);
    		$stmt->bindParam(':class', $marathon_form_infos['class'], PDO::PARAM_STR);
    		$stmt->bindParam(':phone', $marathon_form_infos['phone'], PDO::PARAM_INT);
    		$stmt->bindParam(':instagram', $marathon_form_infos['instagram'], PDO::PARAM_STR);
    		$stmt->bindParam(':city', $marathon_form_infos['city'], PDO::PARAM_STR);
    		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
    		$stmt->execute();
    		$student_id = $connect->lastInsertId();

            set_generated_promo_code($student_id);

    		return $student_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_student_by_phone ($phone) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT s.id,
    						s.last_name,
    						s.first_name,
    						s.phone,
    						s.city,
    						s.school,
    						s.class,
    						s.instagram
    					FROM student s
    					WHERE s.phone = :phone";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
    		$stmt->execute();
            $row_count = $stmt->rowCount();
            $result = array();

            if ($row_count > 0) {
                $student_infos = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = array('id' => $student_infos['id'],
                                'last_name' => $student_infos['last_name'],
                                'first_name' => $student_infos['first_name'],
                                'phone' => $student_infos['phone'],
                                'city' => $student_infos['city'],
                                'school' => $student_infos['school'],
                                'class' => $student_infos['class'],   
                                'instagram' => $student_infos['instagram'],);
            }

    		return $result;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }


    function get_marathon_infos_by_id ($marathon_form_id) {
    	GLOBAL $connect;

    	try {

			$query = "SELECT mt.id,
    						mt.last_name,
    						mt.first_name,
    						mt.phone,
    						mt.city,
    						mt.school,
    						mt.class,
    						mt.instagram
    					FROM marathon_form mt
    					WHERE mt.id = :marathon_form_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':marathon_form_id', $marathon_form_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$marathon_form_infos = $stmt->fetch(PDO::FETCH_ASSOC);

    		return $marathon_form_infos;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
?>