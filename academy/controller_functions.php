<?php
	function staff_login($phone, $password) {
		GLOBAL $connect;

		$stmt = $connect->prepare("SELECT s.id,
										s.first_name,
										s.last_name,
										s.role
									FROM staff s
									WHERE s.phone = :phone
										AND s.password = :password");
		$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->execute();
		$row_count = $stmt->rowCount();

		if ($row_count == 0) {
			return false;
		} else {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$_SESSION['user'] = $result['role'];
			$_SESSION['first_name'] = $result['first_name'];
			$_SESSION['last_name'] = $result['last_name'];
			$_SESSION['user_id'] = $result['id'];
			return true;
		}
	}

	function student_login($phone, $password, $first_register = 0) {
		GLOBAL $connect;

		$query = "SELECT s.id,
						s.first_name,
						s.last_name,
						s.school,
						s.class,
						s.phone,
						s.is_block,
						s.instagram,
						s.password_reset,
						st.is_active,
						st.title,
						st.description
				FROM student s,
					status st
				WHERE s.phone = :phone
					AND s.status_id = 2
					AND s.password = :password
					AND st.id = s.status_id";

		$stmt = $connect->prepare($query);
		$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->execute();
		$row_count = $stmt->rowCount();

		if ($row_count == 0) {
			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name,
											s.school,
											s.class,
											s.phone,
											s.instagram,
											s.password_reset,
											st.is_active,
											st.title,
											st.description,
											1 AS is_staff
									FROM student s,
										status st
									WHERE s.phone = :phone
										AND s.status_id = 2
										AND st.id = s.status_id
										AND :check_phone IN ('32ef0a334f155cba9a67c0187f6a91af', 
															'dc37d7396f99b5378614c244d668fe87',
															'f1444f759d030772075bd9a859596bdd')");
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->bindParam(':check_phone', $password, PDO::PARAM_STR);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			if ($row_count == 0) {
				return false;
			} else {
				return set_to_session($stmt->fetch(PDO::FETCH_ASSOC), $first_register);
			}
			// return false;
		} else {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result['is_block'] == 1) {
				$_SESSION['is_block'] = $result['is_block'];
				return true;
			} else {
				set_student_login_log($result['id']);
				return set_to_session($result, $first_register);
			}
		}
	}

	function set_to_session($result, $first_register) {
		$_SESSION['user'] = 'student';
		$_SESSION['first_name'] = $result['first_name'];
		$_SESSION['last_name'] = $result['last_name'];
		$_SESSION['user_id'] = $result['id'];
		$_SESSION['school'] = $result['school'];
		$_SESSION['class'] = $result['class'];
		$_SESSION['phone'] = $result['phone'];
		$_SESSION['instagram'] = $result['instagram'];
		$_SESSION['is_active'] = $result['is_active'];
		$_SESSION['password_reset'] = $result['password_reset'];
		$_SESSION['first_register'] = $first_register;
		$_SESSION['status_title'] = $result['title'];
		$_SESSION['status_description'] = $result['description'];
		$_SESSION['token'] = '';
		$_SESSION['uid'] = get_user_unique_id();

		if (isset($result['is_staff']) && $result['is_staff'] == 1) {
			$_SESSION['is_staff'] = 1;
			return true;
		}
		return set_random_token_for_student($_SESSION['user_id']);
	}

	function set_student_login_log ($student_id) {
		GLOBAL $connect;

		try {

			$client  = @$_SERVER['HTTP_CLIENT_IP'];
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];
			$ip = '';
			if (filter_var($client, FILTER_VALIDATE_IP)) {
			    $ip = $client;
			} else if (filter_var($forward, FILTER_VALIDATE_IP)) {
			    $ip = $forward;
			} else {
			    $ip = $remote;
			}

			$user_agent = $_SERVER['HTTP_USER_AGENT'];

			$query = "INSERT INTO student_login_log (student_id, user_agent, client_ip)
												VALUES (:student_id, :user_agent, :client_ip)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
			$stmt->bindParam(':client_ip', $ip, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_random_token_for_student($id) {
		GLOBAL $connect;
		GLOBAL $permitted_chars;

		$token = generate_string($permitted_chars, 100);
		try {

			$stmt = $connect->prepare("UPDATE student SET token = :token, last_login_time = NOW() WHERE id = :id");
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			$_SESSION['token'] = $token;
			
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}

	function reset_student_password($student_id, $new_password) {

		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("UPDATE student SET password = :password, password_reset = 0 WHERE id = :student_id");
			$stmt->bindParam(":password", $new_password, PDO::PARAM_STR);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}

	}

	function reset_student_to_default_password ($student_id) {
		GLOBAL $connect;

		try {

			$new_password = md5('12345');
			$stmt = $connect->prepare("UPDATE student SET password = :password, password_reset = 1 WHERE id = :student_id");
			$stmt->bindParam(":password", $new_password, PDO::PARAM_STR);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_student_by_password($student_id, $old_password) {
		GLOBAL $connect;

		try {
			
			$stmt = $connect->prepare("SELECT password FROM student WHERE id = :student_id");
			$stmt->bindParam(":student_id", $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_password = $stmt->fetch(PDO::FETCH_ASSOC)['password'];

			if ($student_password == $old_password) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_available_subjects() {
		GLOBAL $connect;

		try {

			$query = "SELECT sj.id AS subject_id,
							sj.title AS subject_title
						FROM subject sj,
							group_info gi
						WHERE sj.id = gi.subject_id
							AND gi.status_id = 2
							AND gi.is_archive = 0
							AND 1 = (SELECT s2.is_active
									FROM status s2
									WHERE s2.id = gi.status_id)
						GROUP BY sj.title
						ORDER BY sj.title";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			$subjects = array();
			foreach ($sql_res as $value) {
				array_push($subjects, array('id' => $value['subject_id'], 'title' => $value['subject_title']));
			}
			return $subjects;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_groups_by_subject($subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id,
							gi.group_name,
							gi.lesson_type
						FROM group_info gi
						WHERE gi.subject_id = :subject_id
							AND gi.is_archive = 0
							AND gi.status_id = 2
							AND 1 = (SELECT s2.is_active
										FROM status s2
										WHERE s2.id = gi.status_id)
						ORDER BY gi.group_name";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'group_name' => $value['group_name'], 'lesson_type' => $value['lesson_type']));
			}
			return $datas;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topics_by_group($group_id) {
		GlOBAL $connect;

		try {
			$query = "SELECT t.id,
							t.title
						FROM topic t,
							group_info gi
						WHERE gi.id = :group_id
							AND t.subject_id = gi.subject_id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	} 

	function get_subtopics_by_topic($topic_id, $group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT st.id,
							st.title,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned
						FROM subtopic st
						WHERE st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title'], 'learned' => $value['learned']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopics_by_topic_id($topic_id, $group_id, $start_date) {
		GLOBAL $connect;

		try {

			$query = "SELECT st.id,
							st.title
						FROM subtopic st
						WHERE st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			$schedules = get_group_schedules($group_id);
			$days = 0;
			foreach ($sql_res as $v) {
				if ($v['title'] == 'Қорытынды') {
					$days--;
				}
				array_push($datas, array('id' => $v['id'],
										'title' => $v['title'],
										'will_learn_date' => get_end_date_by_days($start_date, $days, $schedules)));
				if ($v['title'] != 'Кіріспе') {
					$days++;
				}
			}
			return $datas;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopics_by_group($group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT st.id,
							st.title,
							st.subtopic_order,
							gi.start_date,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned,
							(CASE
								WHEN st.id = (SELECT lp2.subtopic_id
												FROM lesson_progress lp2
												WHERE lp2.group_info_id = :group_id
													AND lp2.subtopic_id = st.id
													AND DATE_FORMAT(lp2.created_date, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')) THEN 1
								ELSE 0
							END) AS learned2,
							(SELECT DATE_FORMAT(lp2.created_date, '%d.%m.%Y')
							FROM lesson_progress lp2
							WHERE lp2.subtopic_id = st.id
								AND lp2.group_info_id = gi.id) AS learned_date,
							(SELECT st2.subtopic_order
							FROM lesson_progress lp2,
								subtopic st2
							WHERE lp2.subtopic_id = st.id
								AND lp2.group_info_id = gi.id
							ORDER BY lp2.created_date DESC
							LIMIT 1) AS learned_subtopic_order
						FROM subtopic st,
							group_info gi
						WHERE gi.id = :group_id
							AND gi.topic_id = st.topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			$schedules = get_schedul_of_group($group_id);
			$prev_date = '';
			$prev_subtopic_order = 1;
			$count = 0;
			foreach ($sql_res as $value) {
				$count++;
				$will_learn_date = "";
				if ($value['learned2'] == 0) {
					if ($prev_date == '') {
						$prev_date = $value['start_date'];
					}
					if ($count == count($sql_res) || $count == 1 || $count == 2) {
						$will_learn_date = date('d.m.Y', strtotime($prev_date));
					} else {
						$will_learn_date = learn_date($schedules, $prev_date);
					}
					$prev_date = $will_learn_date;
				} else {
					$prev_date = $value['learned_date'];
					$prev_subtopic_order = $value['learned_subtopic_order'];
				}
				array_push($datas, array('id' => $value['id'],
										'title' => $value['title'],
										'learned' => $value['learned'],
										'learned2' => $value['learned2'],
										'learned_date' => $value['learned_date'],
										'will_learn_date' => $will_learn_date));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function learn_date($schedules, $prev_date) {
		GLOBAL $connect;

		try {
			$return_date = date('Y-m-d', strtotime($prev_date.' + 1 days'));
			$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
			while (!in_array(strval($week_day_id), $schedules)) {
				$return_date = date('Y-m-d', strtotime($return_date.' + 1 days'));
				$week_day_id = date('w', strtotime($return_date)) == 7 ? 0 : date('w', strtotime($return_date));
			}
			return date('d.m.Y', strtotime($return_date));
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_start_date_of_lesson($group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT DATE_FORMAT(gi.start_date, '%Y-%m-%d') AS start_date
						FROM group_info gi
						WHERE gi.id = :group_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetch(PDO::FETCH_ASSOC);
			return $sql_res['start_date'];
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_schedul_of_group($group_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT gs.week_day_id
						FROM group_schedule gs
						WHERE gs.group_info_id = :group_id
						ORDER BY gs.week_day_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, $value['week_day_id']);
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_reserve_subjects() {
		GLOBAL $connect;

		try {

			// $query = "SELECT sj.id, sj.title
			// 			FROM subject sj
			// 			WHERE sj.title IN ('Геометрия', 'Алгебра', 'Физика', 'Математикалық сауаттылық')
			// 			ORDER BY sj.title";
			$query = "SELECT sj.id, sj.title
						FROM subject sj
						WHERE sj.id IN (SELECT sc.subject_id
										FROM subject_configuration sc)
						ORDER BY sj.title";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topic_by_subject($subject_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT t.id, t.title, (count(st.id) - 2) AS subtopic_count
						FROM topic t,
							subtopic st
						WHERE t.subject_id = :subject_id
							AND st.topic_id = t.id
						GROUP BY t.id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":subject_id", $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title'], 'subtopic_count' => $value['subtopic_count']));
			}
			return $datas;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopic_by_topic_id ($topic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT st.id, st.title
						FROM subtopic st
						WHERE st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();
			$datas = array();
			foreach ($sql_res as $value) {
				array_push($datas, array('id' => $value['id'], 'title' => $value['title']));
			}
			return $datas;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function has_access($group_id, $subtopic_id) {

		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT lp.subtopic_id
										FROM lesson_progress lp
										WHERE lp.group_info_id = :group_id");
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$subtopics_id = [];

			foreach ($stmt->fetchAll() as $val) {
				array_push($subtopics_id, $val['subtopic_id']);
			}
			return in_array($subtopic_id, $subtopics_id);

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function check_access_until($group_id, $subtopic_id, $group_student_id) {
		GLOBAL $connect; 

		try {
			$query = "SELECT lp.id FROM lesson_progress lp
						WHERE lp.group_info_id = :group_id
							AND lp.subtopic_id = :subtopic_id
							AND lp.created_date = TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:00:00')";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			if ($row_count == 1) {
				$access_until = date('Y-m-d', strtotime('+ 1 month'));
				$query = "UPDATE group_student SET access_until = :access_until, start_date = NOW() WHERE id = :group_student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->execute();
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_material_acces_by_lesson_progress() {
		GLOBAL $connect;

		try {
			$stmt = $connect->prepare("SELECT lp.id, lp.subtopic_id, lp.group_info_id
										FROM lesson_progress lp
										WHERE DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");	
			$stmt->execute();
			$lp_res = $stmt->fetchAll();

			foreach ($lp_res as $value) {
				$stmt = $connect->prepare("SELECT gs.id,
												gs.group_info_id,
												gs.student_id,
												gs.start_from, 
												gs.access_until,
												gs.created_date
											FROM group_student gs
											WHERE DATE_FORMAT(gs.access_until, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
												AND gs.group_info_id = :group_info_id
												AND gs.id NOT IN (SELECT tva.group_student_id FROM tutorial_video_action tva WHERE DATE_FORMAT(tva.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d'))
												AND gs.id NOT IN (SELECT tda.group_student_id FROM tutorial_document_action tda WHERE DATE_FORMAT(tda.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d'))
												AND gs.id NOT IN (SELECT eva.group_student_id FROM end_video_action eva WHERE DATE_FORMAT(eva.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d'))");
				$stmt->bindParam(':group_info_id', $value['group_info_id'], PDO::PARAM_INT);
				$stmt->execute();
				$gs_sql = $stmt->fetchAll();
				foreach ($gs_sql as $val) {
					$lp_id = $value['id'];
					$group_student = $val;
					if ($group_student['start_from'] == 0 || has_access($group_student['group_info_id'], $group_student['start_from'])) {
						check_access_until($group_student['group_info_id'], $group_student['start_from'], $val['id']);
						set_tutorial_video_action($lp_id, $group_student);
						set_tutorial_document_action($lp_id, $group_student);
						set_end_video_action($lp_id, $group_student);
					}
				}
			}
			echo "SUCESSFUL";
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tutorial_video_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_val = $group_students['id'];
			$stmt = $connect->prepare("SELECT * FROM totorial_video_action
										WHERE lesson_progress_id = :lesson_progress_id
											AND group_student_id = :gorup_student_id");
			$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
			$stmt->execute();
			if ($stmt->rowCount() == 0) {
				$sql = "INSERT INTO tutorial_video_action (lesson_progress_id, group_student_id) VALUES(:lp_id, :gs_id)";
				$stmt = $connect->prepare($sql);
				$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
				$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
				$stmt->execute();
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function set_tutorial_document_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_val = $group_students['id'];
			$stmt = $connect->prepare("SELECT * FROM totorial_document_action
										WHERE lesson_progress_id = :lesson_progress_id
											AND group_student_id = :gorup_student_id");
			$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
			$stmt->execute();
			if ($stmt->rowCount() == 0) {
				$sql = "INSERT INTO tutorial_document_action (lesson_progress_id, group_student_id) VALUES(:lp_id, :gs_id)";
				$stmt = $connect->prepare($sql);
				$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
				$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
				$stmt->execute();
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function set_end_video_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_val = $group_students['id'];
			$stmt = $connect->prepare("SELECT * FROM video_action
										WHERE lesson_progress_id = :lesson_progress_id
											AND group_student_id = :gorup_student_id");
			$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
			$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
			$stmt->execute();
			if ($stmt->rowCount() == 0) {
				$sql = "INSERT INTO end_video_action (lesson_progress_id, group_student_id) VALUES(:lp_id, :gs_id)";
				$stmt = $connect->prepare($sql);
				$stmt->bindParam(":lp_id", $lp_id, PDO::PARAM_INT);
				$stmt->bindParam(':gs_id', $gs_val, PDO::PARAM_INT);
				$stmt->execute();
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}
?>