<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/global_controller.php');
	include_once($root.'/staff/group/view.php');
    check_admin_access();

    $data = array();  

	if (isset($_GET['create-group'])) {
		try {
			$subject			= isset($_POST['subject']) ? $_POST['subject'] : '';
			$topic				= isset($_POST['topic']) ? $_POST['topic'] : '';
			$week_id			= isset($_POST['week_id']) ? $_POST['week_id'] : array();
			$group_start_date	= isset($_POST['group-start-date']) ? date('Y-m-d', strtotime($_POST['group-start-date'])) : '';
			$group_type 		= $_POST['group-type'];

			$data['error'] = array('subject' => array('message' => ''),
									'topic' => array('message' => ''),
									'students' => array('message' => ''),
									'week_id' => array('message' => ''),
									'group_start_date' => array('message' => '')
								);
			$has_error = false;
			if ($subject == '') {
				$data['error']['subject']['message'] = 'Пәнді таңдаңыз!';
				$has_error = true;
			} 
			if ($topic == '') {
				$data['error']['topic']['message'] = 'Тарауды таңдаңыз!';
				$has_error = true;
			}
			if (count($week_id) == 0) {
				$data['error']['week_id']['message'] = 'Апта күндерін таңдаңыз!';
				$has_error = true;
			}
			if ($group_start_date == '') {
				$data['error']['group_start_date']['message'] = 'Сабақ басталатын күн енгізілуі керек!';
				$has_error = true;
			}

			if (!$has_error) {
				$query = "SELECT t.title
							FROM topic t
							WHERE t.id = :topic_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':topic_id', $topic, PDO::PARAM_INT);
				$stmt->execute();
				$group_name = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

				$stmt = $connect->prepare("INSERT INTO group_info (subject_id, topic_id, lesson_type, group_name, start_date)
															VALUES (:subject_id, :topic_id, 'topic', :group_name, :start_date)");
				$stmt->bindParam(':subject_id', $subject, PDO::PARAM_INT);
				$stmt->bindParam(':topic_id', $topic, PDO::PARAM_INT);
				$stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
				$stmt->bindParam(':start_date', $group_start_date, PDO::PARAM_STR);
				$stmt->execute();

				$group_info_id = $connect->lastInsertId();

				$query = "INSERT INTO group_schedule (group_info_id, week_day_id) VALUES";
				$qPart = array_fill(0, count($week_id), "(?, ?)");
				$query .= implode(',', $qPart);
				$stmt = $connect->prepare($query);
				$j = 1;
				foreach ($week_id as $value) {
					$stmt->bindValue($j++, $group_info_id, PDO::PARAM_INT);
					$stmt->bindValue($j++, $value, PDO::PARAM_INT);
				}
				$stmt->execute();

				if ($group_type != '') {
					if ($group_type == 'army') {
						$query = "INSERT INTO army_group (group_info_id) VALUES (:group_info_id)";
					} else if ($group_type == "marathon") {
						$query = "INSERT INTO marathon_group (group_info_id) VALUES (:group_info_id)";
					}

					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}




			$data['success'] = !$has_error;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: ['.$e->getMessage().']!!!';
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	} else if (isset($_GET['get_lesson_progress'])) {

		try {

			$topic_id = $_POST['topic_id'];
			$group_id = $_POST['group_id'];

			$stmt = $connect->prepare("SELECT lp.id,
											st.title,
											st.subtopic_order
										FROM lesson_progress lp,
											subtopic st
										WHERE lp.group_info_id = :group_id
											AND st.topic_id = :topic_id
											AND lp.subtopic_id = st.id
										ORDER BY st.subtopic_order DESC");
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();
			$result = array();
			foreach ($sql_result as $value) {
				array_push($result, array('lp_id' => $value['id'],
											'title' => $value['title']));
				// $result[$value['subtopic_order']] = array('lp_id' => $value['id'],
				// 										'title' => $value['title']);
			}

			$data['result'] = $result;	
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['reset_log'])) {
		try {

			$log_id = $_GET['log_id'];
			$obj = $_GET['obj'];

			$data['success'] = false;

			if ($obj == 'tutorial_video') {
				$data['success'] = reset_tutorial_video($log_id);
			} else if ($obj == 'end_video') {
				$data['success'] = reset_end_video($log_id);
			}

		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['reaccess_material'])) {
		try {

			$lp_id = $_GET['lesson_progress_id'];
			$group_student_id = $_GET['group_student_id'];

			$stmt = $connect->prepare("INSERT INTO forced_material_access (lesson_progress_id, group_student_id) VALUES(:lesson_progress_id, :group_student_id)");
			$stmt->bindParam(':lesson_progress_id', $lp_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$forced_material_access_id = $connect->lastInsertId();

			$data['success'] = set_tutorial_video_action($lp_id, $group_student_id, $forced_material_access_id);
			$data['success'] = set_tutorial_document_action($lp_id, $group_student_id, $forced_material_access_id);
			$data['success'] = set_end_video_action($lp_id, $group_student_id, $forced_material_access_id);
			$data['success'] = set_material_test_action_if_does_not_exists($lp_id, $group_student_id);
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['get_single_student_log'])) {
		try {

			$lp_id = $_GET['lp_id'];
			$gs_id = $_GET['gs_id'];

			$result = array();
			$result = array('progress_log' => array());

			$stmt = $connect->prepare("SELECT fma.id,
											DATE_FORMAT(fma.created_date, '%H:%i:%s %d.%m.%Y') AS created_date,
											gs.student_id
										FROM forced_material_access fma,
											group_student gs
										WHERE fma.lesson_progress_id = :lp_id
											AND gs.id = :gs_id
											AND gs.id = fma.group_student_id
										ORDER BY fma.created_date DESC
										LIMIT 1");
			$stmt->bindParam(':lp_id', $lp_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $gs_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$fma_id = $sql_result['id'];
			$result['progress_log'] = array('lp_id' => $lp_id,
											'fma_id' => $fma_id,
											'created_date' => $sql_result['created_date']);

			$tv_action_log = get_student_tutorial_video_action_logs_by_gs_id($lp_id, $gs_id, $fma_id);
			$td_action_log = get_student_tutorial_document_action_logs_by_gs_id($lp_id, $gs_id, $fma_id);
			$ev_action_log = get_student_end_video_action_logs_by_gs_id($lp_id, $gs_id, $fma_id);
			if (count($tv_action_log) > 0) {
				$result['tutorial_video_logs'] = $tv_action_log;
			}
			if (count($td_action_log) > 0) {
				$result['tutorial_document_logs'] = $td_action_log;
			}
			if (count($ev_action_log) > 0) {
				$result['end_video_logs'] = $ev_action_log;
			}
			$data['result'] = $result;			
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['get_groups'])) {
		$data = array();
		try {

			$group_id = $_GET['group_id'];

			$query = "SELECT gi.id,
							gi.group_name
						FROM group_info gi
						WHERE gi.id != :group_id
							AND 1 = (SELECT st2.is_active
									FROM status st2
									WHERE st2.id = gi.status_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
			$stmt->execute();

			$result = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($result, array('group_id' => $value['id'],
											'group_name' => $value['group_name']));
			}
			$data['result'] = $result;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['transfer_students'])) {
		$data = array();
		try {
			$new_group_id = $_POST['group'];
			$old_group_id = $_POST['group-id'];

			$query = "INSERT INTO group_student
						(group_info_id,
						student_id,
						start_from,
						has_payment,
						start_date,
						access_until,
						is_archive,
						created_date)
						SELECT :new_group_id,
							gs.student_id,
							(SELECT st2.id
							FROM subtopic st2,
								topic t2,
								group_info gi2
							WHERE gi2.id = :new_group_id
								AND t2.id = gi2.topic_id
								AND st2.topic_id = t2.id
								AND st2.subtopic_order = 1),
							(CASE
								WHEN gs.access_until > (SELECT gi2.start_date
														FROM group_info gi2
														WHERE gi2.id = :new_group_id)
									AND gs.has_payment = 1 THEN 1
								ELSE 0
							END),
							(SELECT gi2.start_date
								FROM group_info gi2
								WHERE gi2.id = :new_group_id),
							(CASE
								WHEN gs.access_until > (SELECT gi2.start_date
														FROM group_info gi2
														WHERE gi2.id = :new_group_id)
									AND gs.has_payment = 1 THEN gs.access_until
								ELSE null
							END),
							gs.is_archive,
							NOW()
						FROM group_student gs
						WHERE gs.group_info_id = :old_group_id
							AND gs.is_archive = 0
							AND gs.student_id NOT IN (SELECT gs2.student_id FROM group_student gs2 WHERE gs2.group_info_id = :new_group_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':new_group_id', $new_group_id, PDO::PARAM_INT);
			$stmt->bindParam(':old_group_id', $old_group_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "SELECT gi.id, gi.group_name FROM group_info gi WHERE gi.id IN (:new_group_id, :old_group_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':new_group_id', $new_group_id, PDO::PARAM_INT);
			$stmt->bindParam(':old_group_id', $old_group_id, PDO::PARAM_INT);
			$stmt->execute();

			$result = array();
			foreach ($stmt->fetchAll() as $value) {
				if ($value['id'] == $new_group_id) {
					$result['new_group'] = $value['group_name'];
				} else if ($value['id'] == $old_group_id) {
					$result['old_group'] = $value['group_name'];
				}
			}
			$data['result'] = $result;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['set_is_army'])) {

		try {
			$data = array();

			$is_army = $_GET['set_is_army'];
			$group_info_id = $_GET['group_info_id'];

			if ($is_army == 'true') {
				$is_marathon_group = get_is_marathon_group($group_info_id);
				if (!$is_marathon_group) {
					$query = "INSERT INTO army_group (group_info_id) VALUES (:group_info_id)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			} else if ($is_army == 'false') {
				$query = "DELETE FROM army_group WHERE group_info_id = :group_info_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			$data['checked'] = $is_army;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['set_is_marathon'])) {
		try {
			$data = array();

			$is_marathon = $_GET['set_is_marathon'];
			$group_info_id = $_GET['group_info_id'];

			if ($is_marathon == 'true') {
				$is_army_group = get_is_army_group($group_info_id);
				if (!$is_army_group) {
					$query = "INSERT INTO marathon_group (group_info_id) VALUES (:group_info_id)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			} else if ($is_marathon == 'false') {
				$query = "DELETE FROM marathon_group WHERE group_info_id = :group_info_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			$data['checked'] = $is_marathon;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['set_group_student_no_home_work_warning'])) {
		try {
			$lesson_progress_id = $_GET['lesson_progress_id'];
			$group_student_id = $_GET['group_student_id'];
			$gsnhww_id = $_GET['gsnhww_id'];
			$query = "SELECT gsnhww.warning_count
						FROM group_student_no_home_work_warning gsnhww
						WHERE gsnhww.id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			$warning_count = 0;
			if ($row_count == 0) {
				$lesson_progress_ids = json_encode(array($lesson_progress_id));
				$query = "INSERT INTO group_student_no_home_work_warning (group_student_id, lesson_progress_ids, warning_count, nullify_date)
																	VALUES (:group_student_id, :lesson_progress_ids, 1, NOW())";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_ids', $lesson_progress_ids, PDO::PARAM_STR);
				$stmt->execute();
				$gsnhww_id = $connect->lastInsertId();
				$warning_count = 1;

				$query = "INSERT INTO group_student_no_home_work_notification (group_student_no_home_work_warning_id, is_notified, last_notified_date)
																	VALUES (:group_student_no_home_work_warning_id, 0, NOW())";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_no_home_work_warning_id', $gsnhww_id, PDO::PARAM_INT);
				$stmt->execute();
			} else {
				$query = "SELECT gsnhww.warning_count
							FROM group_student_no_home_work_warning gsnhww
							WHERE gsnhww.id = :id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
				$stmt->execute();

				$warning_count = $stmt->fetch(PDO::FETCH_ASSOC)['warning_count'];
				if ($warning_count == 1) {
					force_archive_student_from_group($group_student_id);

					$query = "SELECT gsnhww.lesson_progress_ids
								FROM group_student_no_home_work_warning gsnhww
								WHERE gsnhww.id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
					$stmt->execute();
					$lesson_progress_ids = json_decode($stmt->fetch(PDO::FETCH_ASSOC)['lesson_progress_ids'], true);
					array_push($lesson_progress_ids, $lesson_progress_id);
					$lesson_progress_ids = json_encode($lesson_progress_ids);

					// $query = "DELETE FROM group_student_no_home_work_warning WHERE id = :id";
					// $stmt = $connect->prepare($query);
					// $stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
					// $stmt->execute();

					$query = "UPDATE group_student_no_home_work_warning SET group_student_id = :group_student_id, warning_count = 2, lesson_progress_ids = :lesson_progress_ids WHERE id = :id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
					$stmt->bindParam(':lesson_progress_ids', $lesson_progress_ids, PDO::PARAM_STR);
					$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
					$stmt->execute();

					$query = "UPDATE group_student_no_home_work_notification SET is_notified = 0, last_notified_date = NOW(), seen_date = NULL WHERE group_student_no_home_work_warning_id = :group_student_no_home_work_warning_id";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_student_no_home_work_warning_id', $gsnhww_id, PDO::PARAM_INT);
					$stmt->execute();

					$data['student_deleted'] = true;
				} 
				// else {
					// update
				// }
			}

			$data['group_student_id'] = $group_student_id;
			$data['warning_count'] = $warning_count;
			$data['gsnhww_id'] = $gsnhww_id;

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['undo_group_student_no_home_work_warning'])) {
		$data = array();
		try {
			$lesson_progress_id = $_GET['lesson_progress_id'];
			$gsnhww_id = $_GET['gsnhww_id'];

			$query = "DELETE FROM group_student_no_home_work_notification WHERE group_student_no_home_work_warning_id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "SELECT gsnhww.lesson_progress_ids,
							gsnhww.warning_count
						FROM group_student_no_home_work_warning gsnhww
						WHERE gsnhww.id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($query_result['warning_count'] == 1) {
				$query = "DELETE FROM group_student_no_home_work_warning WHERE id = :id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $gsnhww_id, PDO::PARAM_INT);
				$stmt->execute();
			}
			// else { decrease and remove lesson_progress from lesson_progress_ids }

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
		}
		echo json_encode($data);
	} else if (isset($_GET['get_student_info_by_phone'])) {
		$data = array();
        try {

            $phone = $_GET['phone'];
            $group_info_id = $_GET['group_info_id'];

            $query = "SELECT s.phone,
                            s.last_name,
                            s.first_name,
                            (SELECT count(gs.group_info_id)
                            	FROM group_student gs
                            	WHERE gs.student_id = s.id
                            		AND gs.group_info_id = :group_info_id) AS is_in_group
                        FROM student s
                        WHERE s.phone = :phone";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
            $stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            $data['message'] = "";
            if ($row_count > 0) {
                $student_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($student_info['is_in_group'] == 0) {
                	$data['message'] = 'Оқушы: '.$student_info['last_name'].' '.$student_info['first_name'];	
                	$data['style'] = 'success';
                } else {
                	$data['message'] = 'Оқушы ұже группаға тіркелінген. ('.$student_info['last_name'].' '.$student_info['first_name'].')';
                	$data['style'] = 'warning';
                }
                
            } else {
            	$data['message'] = 'Бұндай номермен оқушы тіркелмеген!';
            	$data['style'] = 'error';
            }

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_POST['submit-add-student-to-group'])) {
		try {

			$phone = $_POST['phone'];
			$group_info_id = $_POST['group_info_id'];
			$start_from = 0;

			$query = "SELECT s.id
						FROM student s
						WHERE s.phone = :phone";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if($row_count == 1) {
				$student_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
				add_student_to_group($student_id, $group_info_id);
			}
			header('Location:../index.php?page=group&group='.$group_info_id);
		} catch (Exception $e) {
			throw $e;
		}
	} else if (isset($_GET['get_available_groups_for_transfer'])) {
		try {

			$group_student_id = $_GET['group_student_id'];

			$query = "SELECT gi.id AS group_info_id,
							gi.subject_id,
							sj.title AS subject_title,
							gs.student_id
						FROM group_student gs,
							group_info gi,
							subject sj
						WHERE gs.id = :group_student_id
							AND gi.id = gs.group_info_id
							AND sj.id = gi.subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$current_group_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "SELECT gi.id AS group_info_id,
							gi.group_name
						FROM group_info gi
						WHERE gi.status_id = 2
							AND gi.is_archive = 0
							AND gi.subject_id = :subject_id
							AND gi.id != :group_info_id
							AND gi.id IN (SELECT ag.group_info_id
											FROM army_group ag)
							AND gi.id NOT IN (SELECT gs.group_info_id
												FROM group_student gs
												WHERE gs.student_id = :student_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $current_group_info['subject_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $current_group_info['group_info_id'], PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $current_group_info['student_id'], PDO::PARAM_INT);
			$stmt->execute();
			$available_groups_info = $stmt->fetchAll();

			$result = array('subject_title' => $current_group_info['subject_title'],
							'groups' => array());

			foreach ($available_groups_info as $value) {
				$result['groups'][$value['group_info_id']] = $value['group_name'];
			}

			$data['info'] = $result;
			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['transfer_student_to_group'])) {
		try {
			$group_student_id = $_GET['group_student_id'];
			$group_info_id = $_GET['group_info_id'];

			$query = "SELECT gs.student_id,
							gs.group_info_id
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$student_id = $query_result['student_id'];
			$old_group_info_id = $query_result['group_info_id'];

			$query = "UPDATE group_student SET status = 'inactive', is_archive = 1 WHERE id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "SELECT gsp.used_date,
							gsp.start_date,
							gsp.access_until
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$old_group_payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$access_until = date_create($old_group_payment_info['access_until']);
			// if ($old_group_payment_info['start_date'] != '') {
			// 	$start_date = date_create($old_group_payment_info['start_date']);
			// } else {
			// 	$start_date = date_create($old_group_payment_info['used_date']);
			// }
			$start_date = date_create(date('Y-m-d'));

			$diff = date_diff($start_date, $access_until);
			$partial_days = $diff->format('%a');

			$query = "UPDATE group_student_payment SET full_finished = 0, finished_date = NOW() WHERE group_student_id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "INSERT INTO student_balance (student_id, group_id, used_for_group, is_used, days, used_date)
											VALUES (:student_id, :group_id, :used_for_group, 1, :days, NOW())";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_id', $old_group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':used_for_group', $group_info_id, PDO::PARAM_STR);
			$stmt->bindParam(':days', $partial_days, PDO::PARAM_INT);
			$stmt->execute();

			$new_group_student_id = add_student_to_group($student_id, $group_info_id);


			$access_until = date('Y-m-d', strtotime(' + '.$partial_days.' days'));
			$query = "INSERT INTO group_student_payment
								(group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type, partial_payment_days)
						VALUES (:new_group_student_id, NOW(), NOW(), :access_until, 1, NOW(), 'balance', :partial_payment_days)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':new_group_student_id', $new_group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
			$stmt->bindParam(':partial_payment_days', $partial_days, PDO::PARAM_INT);
			$stmt->execute();

			$query = "UPDATE group_student SET status = 'waiting' WHERE id = :new_group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':new_group_student_id', $new_group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['group_student_id'] = $new_group_student_id;
			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['set_group_student_trial_test'])) {
		try {

			$lesson_progress_id = $_GET['lesson_progress_id'];

			$query = "SELECT lp.group_info_id,
							gi.subject_id
						FROM lesson_progress lp,
							group_info gi
						WHERE lp.id = :lesson_progress_id
							AND gi.id = lp.group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "SELECT tt.id,
							tt.title
						FROM trial_test tt
						WHERE tt.subject_id = :subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $group_info['subject_id'], PDO::PARAM_INT);
			$stmt->execute();
			$trial_test_result = $stmt->fetchAll();

			$trial_test_list = array();
			foreach ($trial_test_result as $value) {
				array_push($trial_test_list, $value['id']);
			}

			$query = "SELECT gs.student_id
						FROM group_student gs
						WHERE gs.is_archive = 0
							AND gs.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info['group_info_id'], PDO::PARAM_INT);
			$stmt->execute();
			$student_list = $stmt->fetchAll();

			$student_trial_test_ids = array();
			foreach ($student_list as $value) {
				$student_trial_test_id = set_student_trial_test($value['student_id'], $trial_test_list);
				array_push($student_trial_test_ids, $student_trial_test_id);
				$data['ok'][$value['student_id']] = $student_trial_test_id;
				if ($student_trial_test_id != 0) {
					$query = "INSERT INTO group_student_trial_test (student_trial_test_id, lesson_progress_id)
															VALUES (:student_trial_test_id, :lesson_progress_id)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':student_trial_test_id', $student_trial_test_id, PDO::PARAM_INT);
					$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			if (count($student_trial_test_ids) > 0) {
				$query = "INSERT INTO group_trial_test (group_info_id) VALUES (:group_info_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info['group_info_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['get-group-trial-test-result'])) {
		try {

			$group_info_id = $_GET['group_info_id'];

			$data['result'] = get_group_student_trial_test_result_list($group_info_id);
			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['activate_archive_group_student'])) {
		try {
			
			$group_student_id = $_GET['group_student_id'];

			$group_student_info = activate_group_student($group_student_id);

			$student_balance_days = get_free_student_balances($group_student_info);

			$data['set_lesson_access'] = false;

			if ($student_balance_days > 0) {
				insert_group_student_payment($group_student_id, $student_balance_days);
				update_group_student_status($group_student_id);
				$data['set_lesson_access'] = true;
			}

			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'Error: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	}

	function update_group_student_status ($group_student_id) {
		GLOBAL $connect;
		try {

			$query = "UPDATE group_student SET status = 'waiting' WHERE id = :group_student_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_group_student_payment ($group_student_id, $days) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO group_student_payment (group_student_id, payed_date, payment_type, partial_payment_days, start_date)
											VALUES (:group_student_id, NOW(), 'balance', :days, NOW())";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':days', $days, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_free_student_balances ($group_student_info) {
		GLOBAL $connect;

		try {

			$query = "SELECT sb.id AS student_balance_id,
							sb.days
						FROM student_balance sb
						WHERE sb.is_used = 0
							AND sb.student_id = :student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $group_student_info['student_id'], PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$days = 0;

			foreach ($query_result as $value) {
				$query = "UPDATE student_balance SET used_for_group = :group_info_id, is_used = 1, used_date = NOW() WHERE id = :student_balance_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_student_info['group_info_id'], PDO::PARAM_INT);
				$stmt->bindParam(':student_balance_id', $value['student_balance_id'], PDO::PARAM_INT);
				$stmt->execute();

				$days += $value['days'];
			}

			return $days;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function activate_group_student ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_student SET is_archive = 0 WHERE id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "SELECT gs.id,
							gs.group_info_id,
							gs.student_id
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = array('group_student_id' => $query_result['id'],
							'group_info_id' => $query_result['group_info_id'],
							'student_id' => $query_result['student_id']);

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function add_student_to_group ($student_id, $group_info_id) {
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
			$gs_row_count = $stmt->rowCount();

			if ($gs_row_count == 0) {
				$query = "SELECT lp.subtopic_id,
								(DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_today,
								st.topic_id
							FROM lesson_progress lp,
								subtopic st
							WHERE lp.group_info_id = :group_info_id
								AND st.id = lp.subtopic_id
							ORDER BY lp.created_date DESC
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$lp_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$lp_row_count = $stmt->rowCount();

				if ($lp_row_count == 1) {
					if ($lp_result['is_today']) {
						$start_from = $lp_result['subtopic_id'];
					} else {
						$query = "SELECT st.id
									FROM subtopic st
									WHERE st.topic_id = :topic_id
										AND st.subtopic_order = (SELECT st2.subtopic_order + 1
																FROM subtopic st2
																WHERE st2.id = :subtopic_id)";
						$stmt = $connect->prepare($query);
						$stmt->bindParam(':topic_id', $lp_result['topic_id'], PDO::PARAM_INT);
						$stmt->bindParam(':subtopic_id', $lp_result['subtopic_id'], PDO::PARAM_INT);
						$stmt->execute();
						$start_from = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

						if ($start_from == '') {
							$query = "SELECT st.id
										FROM subtopic st
										WHERE st.topic_id = :topic_id
											AND st.subtopic_order = (SELECT st2.subtopic_order - 1
																	FROM subtopic st2
																	WHERE st2.id = :subtopic_id)";
							$stmt = $connect->prepare($query);
							$stmt->bindParam(':topic_id', $lp_result['topic_id'], PDO::PARAM_INT);
							$stmt->bindParam(':subtopic_id', $lp_result['subtopic_id'], PDO::PARAM_INT);
							$stmt->execute();
							$start_from = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
						}
					}

				} else {
					$query = "SELECT st.id
								FROM subtopic st,
									group_info gi
								WHERE gi.id = :group_info_id
									AND st.topic_id = gi.topic_id
									AND st.subtopic_order = 1";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
					$stmt->execute();
					$start_from = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
				}

				$query = "INSERT INTO group_student (group_info_id, student_id, start_from) VALUES (:group_info_id, :student_id, :start_from)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':start_from', $start_from, PDO::PARAM_INT);
				$stmt->execute();

				$new_group_student_id = $connect->lastInsertId();

				return $new_group_student_id;
			}

			return 0;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function force_archive_student_from_group ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.group_info_id,
							gs.student_id
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_student_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "UPDATE group_student SET status = 'inactive', is_archive = 1 WHERE id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$partial_days = 0;

			$query = "SELECT DATE_FORMAT(gsp.access_until, '%Y-%m-%d') AS access_until
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
							AND gsp.is_used = 1
							AND gsp.full_finished IS NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$group_student_payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "UPDATE group_student_payment 
						SET full_finished = 0, 
							finished_date = NOW() 
						WHERE group_student_id = :group_student_id 
							AND is_used = 1 
							AND full_finished IS NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			$date1 = date_create(date('Y-m-d'));
			$date2 = date_create($group_student_payment_info['access_until']);
			$diff = date_diff($date1, $date2);
			$days = $diff->format('%a');

			$query = "INSERT INTO student_balance (student_id, group_id, days)
											VALUES (:student_id, :group_info_id, :days)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $group_student_info['student_id'], PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $group_student_info['group_info_id'], PDO::PARAM_INT);
			$stmt->bindParam(':days', $days, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_tutorial_video_action_logs_by_gs_id($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT tv.video_order,
							tva.id as tva_id
						FROM lesson_progress lp
						LEFT JOIN tutorial_video tv
							ON tv.subtopic_id = lp.subtopic_id
						LEFT JOIN tutorial_video_action tva
							ON tva.lesson_progress_id = lp.id
								AND tva.group_student_id = :gs_id
								AND tva.forced_material_access_id = :fma_id
						WHERE lp.id = :lp_id
						ORDER BY tv.video_order";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':fma_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				if ($value['tva_id'] != '' && $value['video_order'] != '') {
					$result[$value['video_order']] = array('tva_id' => $value['tva_id']);
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_tutorial_document_action_logs_by_gs_id($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT td.document_order,
							tda.id as tda_id
						FROM lesson_progress lp
						LEFT JOIN tutorial_document td
							ON td.subtopic_id = lp.subtopic_id
						LEFT JOIN tutorial_document_action tda
							ON tda.lesson_progress_id = lp.id
								AND tda.group_student_id = :gs_id
								AND tda.forced_material_access_id = :fma_id
						WHERE lp.id = :lp_id
						ORDER BY td.document_order";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':fma_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				if ($value['tda_id'] != '' && $value['document_order'] != '') {
					$result[$value['document_order']] = array('tda_id' => $value['tda_id']);
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_end_video_action_logs_by_gs_id($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT ev.video_order,
							eva.id AS eva_id
						FROM lesson_progress lp
						LEFT JOIN end_video ev
							ON ev.subtopic_id = lp.subtopic_id
						LEFT JOIN end_video_action eva
							ON eva.lesson_progress_id = lp.id
								AND eva.group_student_id = :gs_id
								AND eva.forced_material_access_id = :fma_id
						WHERE lp.id = :lp_id
						ORDER BY ev.video_order";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lp_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':fma_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();
			foreach ($sql_result as $value) {
				if ($value['eva_id'] != '' && $value['video_order'] != '') {
					$result[$value['video_order']] = array('eva_id' => $value['eva_id']);
				}
			}
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_video_action($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
											VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)");
			$stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(":lesson_progress_id", $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(":forced_material_access_id", $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
			return true;
		} catch (Exception $e) {
			// return false;
			return $e->getMessage();
			// throw $e;
		}
	}

	function set_tutorial_document_action($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id)
											VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)");
			$stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(":lesson_progress_id", $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(":forced_material_access_id", $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
			return true;
		} catch (Exception $e) {
			// return false;
			return $e->getMessage();
			// throw $e;
		}
	}

	function set_material_test_action_if_does_not_exists ($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id
						FROM material_test_action mta
						WHERE mta.group_student_id = :group_student_id
							AND mta.lesson_progress_id = :lesson_progress_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$query = "SELECT count(mt.id) AS c
							FROM material_test mt,
								lesson_progress lp
							WHERE mt.subtopic_id = lp.subtopic_id
								AND lp.id = :lesson_progress_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->execute();
				$mt_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

				$is_finish = $mt_count > 0 ? 1 : 0;

				$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
													VALUES (:group_student_id, :lesson_progress_id, :is_finish)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
				$stmt->bindParam(':is_finish', $is_finish, PDO::PARAM_INT);
				$stmt->execute();
			}
			return true;
		} catch (Exception $e) {
			// throw $e;
			return $e->getMessage();
		}
	}

	function set_end_video_action($lesson_progress_id, $group_student_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
											VALUES(:group_student_id, :lesson_progress_id, :forced_material_access_id)");
			$stmt->bindParam(":group_student_id", $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(":lesson_progress_id", $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(":forced_material_access_id", $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
			return true;
		} catch (Exception $e) {
			// return false;
			return $e->getMessage();
			// throw $e;
		}
	}

	function reset_tutorial_video($tutorial_video_action_log_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("DELETE FROM tutorial_video_action_log WHERE id = :id");
			$stmt->bindParam(":id", $tutorial_video_action_log_id, PDO::PARAM_INT);
			$stmt->execute();
			return true;
			
		} catch (Exception $e) {
			// return false;
			return $e->getMessage();
			// throw $e;
		}
	}

	function reset_end_video($end_video_action_log_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("DELETE FROM end_video_action_log WHERE id = :id");
			$stmt->bindParam(":id", $end_video_action_log_id, PDO::PARAM_INT);
			$stmt->execute();
			return true;
			
		} catch (Exception $e) {
			// return false;
			return $e->getMessage();
			// throw $e;
		}
	}
?>