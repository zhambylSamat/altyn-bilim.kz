<?php
	include_once('common/connection.php');
	include_once('common/old_ab_connection.php');
	include_once('controller_functions.php');
	include_once('common/global_controller.php');
	include_once('send_sms/index.php');
	include_once('send_sms/sms_statuses.php');

	if (isset($_POST['signIn'])) {
		$phone = $_POST['phone'];
		$password = md5($_POST['password']);

		$force_login = isset($_POST['force-login']) ? 1 : 0;

		if (student_login($phone, $password)) {
			$user_direction = 'student';
			$_SESSION['user_direction'] = '/'.$user_direction.'/';
			if ($_SESSION['is_block'] == 1) {
				header('Location:index.php?login_error=student_account_blocked');
			} else if ($_SESSION['is_active'] == 1) {
				if ($_SESSION['password_reset'] == 1) {
					header('Location:reset-password.php');
				} else {
					header('Location:student');
				}
			}
		} else if (staff_login($phone, $password)) {
			if ($_SESSION['user'] == $ADMIN || 
				$_SESSION['user'] == $MODERATOR ||
				$_SESSION['teacher'] == $TEACHER) {
				$user_direction = 'staff';
				$_SESSION['user_direction'] = '/'.$user_direction.'/';
				if ($force_login == 1) {
					$_SESSION['page_navigator'] = $admin_page_navigator;
					foreach ($_SESSION['page_navigator'] as $index => $value) {
						if ($value['short_name'] == 'accounting') {
							$_SESSION['page_navigator'][$index]['show'] = true;
						} else {
							$_SESSION['page_navigator'][$index]['show'] = false;
						}
					}
				}
				header('Location:staff');
			}
		} else {
			unset($_SESSION['user_direction']);
			header('Location:index.php?login_error=password_wrong');
		}
	} else if (isset($_POST['signUp'])) {
		$first_name	= trim($_POST['first_name']);	// 0
		$last_name	= trim($_POST['last_name']);	// 1
		$school		= trim($_POST['school']);		// 2
		$class		= trim($_POST['class']);		// 3
		$phone		= trim($_POST['phone']);		// 4
		$parent_phone = trim($_POST['parent-phone']);
		$city 		= trim($_POST['city']);
		$subjects	= isset($_POST['subject']) ? $_POST['subject'] : array();			// 5
		$courses = isset($_POST['courses']) && $_POST['courses'] != '1' && $_POST['courses'] != '' ? explode('|', $_POST['courses']) : array();
		$reserves = isset($_POST['reserves']) && $_POST['reserves'] != '' ? explode('|', $_POST['reserves']) : array();
		$_SESSION['registration']['has_error'] = false;

		$_SESSION['registration']['first_name']['value'] = $first_name;
		if (strlen($first_name) < 3) {
			$_SESSION['registration']['first_name']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['first_name']['err_display'] = "none";
		}

		$_SESSION['registration']['last_name']['value'] = $last_name;
		if (strlen($last_name) < 3) {
			$_SESSION['registration']['last_name']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['last_name']['err_display'] = "none";
		}

		$_SESSION['registration']['school']['value'] = $school;
		if (strlen($school) == 0) {
			$_SESSION['registration']['school']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['school']['err_display'] = "none";
		}
		
		$_SESSION['registration']['class']['value'] = $class;
		if (strlen($class) == 0) {
			$_SESSION['registration']['class']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['class']['err_display'] = "none";
		}

		$_SESSION['registration']['city']['value'] = $city;
		if (strlen($class) == 0) {
			$_SESSION['registration']['city']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['city']['err_display'] = "none";
		}

		$_SESSION['registration']['phone']['value'] = $phone;
		if (strlen($phone) != 10) {
			$_SESSION['registration']['phone']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['phone']['err_display'] = "none";
		}

		$_SESSION['registration']['parent_phone']['value'] = $parent_phone;

		$_SESSION['registration']['courses']['value'] = '';
		if (count($courses) == 0 && count($reserves) == 0) {
			$_SESSION['registration']['courses']['err_display'] = "block";
			$_SESSION['registration']['has_error'] = true;
		} else {
			$_SESSION['registration']['courses']['err_display'] = "none";
		}

		try {
			$stmt = $connect->prepare("SELECT count(id) AS c FROM student WHERE phone = :phone");
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

			if ($row_count != 0) {
				$_SESSION['registration']['phone']['err_display'] = 'block';
				$_SESSION['registration']['phone']['extra_err_text'] = 'Базада осы нөмірмен оқушы тіркелген. Менеджерге хабарласыңыз.';
				$_SESSION['registration']['phone']['exists'] = 'true';
				$_SESSION['registration']['has_error'] = true;
			} else {
				$_SESSION['registration']['phone']['err_display'] = 'none';
				$_SESSION['registration']['phone']['extra_err_text'] = '';
			}
		} catch (Exception $e) {
			throw $e;
		}

		if (!$_SESSION['registration']['has_error']) {
			try {

				$password = md5('12345');

				$stmt = $connect->prepare('INSERT INTO student (first_name, last_name, school, class, phone, parent_phone, city, password)
												VALUES (:first_name, :last_name, :school, :class, :phone, :parent_phone, :city, :password)');
				$stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
				$stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
				$stmt->bindParam(':school', $school, PDO::PARAM_STR);
				$stmt->bindParam(':class', $class, PDO::PARAM_STR);
				$stmt->bindParam(':city', $city, PDO::PARAM_STR);
				$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
				$stmt->bindParam(':parent_phone', $parent_phone, PDO::PARAM_STR);
				$stmt->bindParam(':password', $password, PDO::PARAM_STR);
				$stmt->execute();

				$student_id = $connect->lastInsertId();

				if (count($reserves) > 0) {
					$new_group_infos = array();
					foreach ($reserves as $value) {
						$subject_id = explode('-', $value)[0];
						$topic_id = explode('-', $value)[1];

						array_push($new_group_infos, array('subject_id' => $subject_id,
															'topic_id' => $topic_id));
					}
					set_generated_promo_code($student_id);
					create_new_group_for_student($student_id, $new_group_infos);
				}
				
				session_unset($_SESSION['registration']);
				student_login($phone, $password, 1);
				header('Location:index.php?r_done');

			} catch (Exception $e) {
				throw $e;
			}
		} else {
			header("Location:registration.php");
		}
	} else if (isset($_POST['reset-password'])) {
		$old_password = $_POST['old-password'];
		$new_password = $_POST['new-password'];
		$confirm_new_password = $_POST['confirm-new-password'];

		$student_id = $_SESSION['user_id'];
		$param = '?err';
		$has_error = false;
		if (!check_student_by_password($student_id, md5($old_password))) {
			$param .= '&pwd';
			$has_error = true;
		}
		if (strlen($new_password) <= 6) {
			$param .= '&lngth';
			$has_error = true;
		}
		if (md5($new_password) != md5($confirm_new_password)) {
			$param .= "&cnfrm";
			$has_error = true;
		}
		if ($has_error) {
			header('Location:reset-password.php'.$param);
		} else {
			$new_password = md5($new_password);
			reset_student_password($student_id, $new_password);
			$stmt = $connect->prepare("SELECT s.phone FROM student s WHERE s.id = :student_id");
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_info = $stmt->fetch(PDO::FETCH_ASSOC);
			$_SESSION['password_reset'] = 0;

			$first_register = 0;

			if (isset($_SESSION['first_register'])) {
				$first_register = $_SESSION['first_register'];
			}

			if (student_login($student_info['phone'], $new_password, $first_register)) {
				$user_direction = 'student';
				$_SESSION['user_direction'] = '/'.$user_direction.'/';
				header('Location:student');
			} else {
				header('Location:index.php');
			}
		}
	} else if (isset($_GET['get_timecode'])) {
		$data = array();

		try {

			$id = $_POST['id'];

			$stmt = $connect->prepare('SELECT evt.id,
											evt.timecode,
											evt.title
										FROM end_video_timecode evt
										WHERE evt.end_video_id = :id');
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$result = $stmt->fetchAll();

			$data['result'] = array();
			foreach ($result as $value) {
				$detailed_timecode_information = timecode_detailed_information($value['timecode']);
				array_push($data['result'], array('id' => $value['id'],
												'time' => $value['timecode'],
												'title' => $value['title'],
												'detailed_information' => $detailed_timecode_information,
												'total_seconds' => $detailed_timecode_information['total_seconds']));
			}
			$total_seconds = array_column($data['result'], 'total_seconds');
			array_multisort($total_seconds, SORT_ASC, $data['result']);

			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_groups_by_subject'])) {
		$data = array();
		try {
			$subject_id = $_GET['get_groups_by_subject'];
			$groups = get_groups_by_subject($subject_id);
			$data['success'] = true;
			$data['groups'] = $groups;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_topics_by_group'])) {
		$data = array();
		try {
			$group_id = $_GET['get_topics_by_group'];
			$topics = get_topics_by_group($group_id);
			$data['success'] = true;
			$data['topics'] = $topics;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_subtopics_by_topic'])) {
		$data = array();
		try {

			$topic_id = $_GET['get_subtopics_by_topic'];
			$group_id = $_GET['group'];
			$subtopics = get_subtopics_by_topic($topic_id, $group_id);
			$data['success'] = true;
			$data['subtopics'] = $subtopics;
			$data['start_date'] = get_start_date_of_lesson($group_id);
			$data['schedule'] = get_schedul_of_group($group_id);
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_subtopics_by_topic_id'])) {
		$data = array();
		try {
			$topic_id = $_GET['topic_id'];
			$group_id = $_GET['group_id'];
			$start_date = $_GET['start_date'];
			$subtopics = get_subtopics_by_topic_id($topic_id, $group_id, $start_date);
			$data['subtopics'] = $subtopics;
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_subtopics_by_group'])) {
		$data = array();
		try {
			$group_id = $_GET['get_subtopics_by_group'];
			$subtopics = get_subtopics_by_group($group_id);
			$data['success'] = true;
			$data['subtopics'] = $subtopics;
			$data['start_date'] = get_start_date_of_lesson($group_id);
			$data['schedule'] = get_schedul_of_group($group_id);
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_topic_by_subject'])) {
		$data = array();
		try {

			$subject_id = $_GET['get_topic_by_subject'];
			$data['success'] = true;
			$data['topics'] = get_topic_by_subject($subject_id);

		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['get_subtopic_by_topic'])) {

		$data = array();

		try {
			$topic_id = $_GET['get_subtopic_by_topic'];

			$data['subtopics'] = get_subtopic_by_topic_id($topic_id);
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);

	} else if (isset($_GET['set_videos_to_all_students'])) {
		header('Location:cron/set_lesson_access.php');
		// set_material_acces_by_lesson_progress();
	} else if (isset($_GET['get_student_info_by_phone'])) {
		try {
			$phone = $_GET['phone'];

			$query = "SELECT s.id,
							s.last_name,
							s.first_name,
							s.school,
							s.class,
							s.city,
							s.instagram,
							s.phone
						FROM student s
						WHERE s.phone = :phone";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 1) {
				$value = $stmt->fetch(PDO::FETCH_ASSOC);
				$data['data'] = array('student_id' => $value['id'],
										'last_name' => $value['last_name'],
										'first_name' => $value['first_name'],
										'school' => $value['school'],
										'class' => $value['class'],
										'city' => $value['city'],
										'instagram' => $value['instagram'],
										'phone' => $value['phone']);
			} else {
				$data['data'] = array();
			}

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_POST['marathon-registration-submit'])) {
		try {
			$last_name = $_POST['last_name'];
			$first_name = $_POST['first_name'];
			$phone = $_POST['phone'];
			$school = $_POST['school'];
			$class = $_POST['class'];
			$city = $_POST['city'];
			$instagram = $_POST['instagram'];
			$subjects = json_encode($_POST['subject']);

			$query = "INSERT INTO marathon_form (last_name, first_name, phone, city, school, class, instagram, subject_ids)
											VALUES (:last_name, :first_name, :phone, :city, :school, :class, :instagram, :subject_ids)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
			$stmt->bindParam(':city', $city, PDO::PARAM_STR);
			$stmt->bindParam(':school', $school, PDO::PARAM_STR);
			$stmt->bindParam(':class', $class, PDO::PARAM_STR);
			$stmt->bindParam(':instagram', $instagram, PDO::PARAM_STR);
			$stmt->bindParam(':subject_ids', $subjects, PDO::PARAM_STR);
			$stmt->execute();

			header('Location:registration_marathon.php?registration=true');
			
		} catch (Exception $e) {
			throw $e;
		}
	} else if (isset($_GET['create_entrance_examination_object'])) {
		try {
			$code = '';
			for($i = 0; $i < 2; $i++) {
		        $code .= mt_rand(1, 9);
		    }

			$query = "INSERT INTO entrance_examination_student (eep_id, entrance_code)
														VALUES (16, :code)";
			$stmt = $ab_connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_INT);
			$stmt->execute();
			$ees_id = $ab_connect->lastInsertId();

			$query = "SELECT ees.id,
							ees.entrance_code
						FROM entrance_examination_student ees
						WHERE ees.id = :id";
			$stmt = $ab_connect->prepare($query);
			$stmt->bindParam(':id', $ees_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$data['success'] = get_data_from_entrance_examination_student($query_result['entrance_code'], $query_result['id']);
			$data['data'] = array('ees_id' => $_SESSION['ees_id'],
								'ees_code' => $_SESSION['ees_code'],
								'ees_surname' => $_SESSION['ees_surname'],
								'ees_name' => $_SESSION['ees_name'],
								'test_result' => $_SESSION['test_result'],
								'finish' => $_SESSION['finish']);
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['send-sms-code'])) {
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
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$data['student_exists'] = false;
			if ($row_count == 1) {
				$data['student_exists'] = true;
				$sms_code = set_sms_code($phone);
				$recipient = '7'.$phone;
				$fio = $query_result['last_name'].' '.$query_result['first_name'];
				$text = "Код: ".$sms_code."\n";
				$text .= $end_text;
				$text = kiril2latin($text);
				$recipient_info = array('recipient' => $recipient,
									'text' => $text);
				$sms_response = send_sms($recipient_info, $fio);
				save_sms_response_to_db($sms_response);
			}
			
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['check_sms_code'])) {
		try {

			$phone = $_GET['phone'];
			$code = $_GET['code'];

			$query = "SELECT sc.id
						FROM sms_code sc
						WHERE sc.phone = :phone
							AND sc.code = :code";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			$validation = $row_count == 1 ? true : false;

			$data['validation'] = false;
			
			if ($validation) {
				$data['validation'] = true;

				$query = "SELECT s.id,
								s.first_name,
								s.last_name
							FROM student s
							WHERE s.phone = :phone";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
				$stmt->execute();
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$student_id = $query_result['id'];

				$_SESSION['first_register'] = true;
				$_SESSION['last_name'] = $query_result['last_name'];
				$_SESSION['first_name'] = $query_result['first_name'];
				$_SESSION['user_id'] = $student_id;
				reset_student_to_default_password($student_id);
			}

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = 'ERROR: '.$e->getMessage()."!!!";
		}
		echo json_encode($data);
	}
	else {
		header("Location:index.php");
	}

	function set_sms_code ($phone) {
		GLOBAL $connect;

		try {

			clear_expired_sms_codes();
			$code = generate_unique_sms_code();

			$query = "INSERT INTO sms_code (phone, code) VALUES (:phone, :code)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->execute();

			return $code;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function generate_unique_sms_code () {
		GLOBAL $connect;
		GLOBAL $digit_chars;

		try {

			$code = generate_string($digit_chars, 4);

			if (strlen($code) != 4) {
				return generate_unique_sms_code();
			}

			$query = "SELECT sc.id
						FROM sms_code sc
						WHERE sc.code = :code";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				return generate_unique_sms_code();
			}

			return $code;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_data_from_entrance_examination_student ($code, $ees_id) {
		GLOBAL $ab_connect;
		GLOBAL $ab_root;

		try {

			$stmt = $ab_connect->prepare("SELECT entrance_code, student_name, student_surname, result_json, finish FROM entrance_examination_student WHERE entrance_code = :code AND id = :id ");
			$stmt->bindParam(':code', $code, PDO::PARAM_INT);
			$stmt->bindParam(':id', $ees_id, PDO::PARAM_INT);
			$stmt->execute();
		   	$result = $stmt->fetch(PDO::FETCH_ASSOC);
		   	$c = $stmt->rowCount();
		   	if($c==1){
		   		$_SESSION['finish'] = $result['finish']==1 ? true : false;
		   		$_SESSION['test_result'] = $result['result_json'] != "" ? json_decode($result['result_json'], true) : "";
		   		$_SESSION['ees_name'] = $result['student_name'];
		   		$_SESSION['ees_surname'] = $result['student_surname'];
		   		$_SESSION['ees_code'] = $code;
		   		$_SESSION['ees_id'] = $ees_id;
		   		return true;
		   	} 
		   	return false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function timecode_detailed_information($reminder){
		$result = array('hour' => 0,
						'minute' => 0,
						'second' => 0,
						'total_seconds' => 0);

		if (strpos($reminder, 'h')) {
			$split_result = explode('h', $reminder);
			$result['hour'] = intval($split_result[0]);
			$result['total_seconds'] += $result['hour'] * 3600;
			$reminder = $split_result[1];
		}

		if (strpos($reminder, 'm')) {
			$split_result = explode('m', $reminder);
			$result['minute'] = intval($split_result[0]);
			$result['total_seconds'] += $result['minute'] * 60;
			$reminder = $split_result[1];
		}

		if (strpos($reminder, 's')) {
			$split_result = explode('s', $reminder);
			$result['second'] = intval($split_result[0]);
			$result['total_seconds'] += $result['second'];
			$reminder = $split_result[1];
		}

		return $result;
	}

?>