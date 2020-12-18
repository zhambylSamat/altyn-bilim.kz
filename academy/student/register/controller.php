<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/student/register/controller_functions.php');
	include_once($root.'/common/global_controller.php');

	if (isset($_POST['register_course'])) {
		$courses = isset($_POST['courses']) && $_POST['courses'] != '1' && $_POST['courses'] != '' ? explode('|', $_POST['courses']) : array();
		$reserves = isset($_POST['reserves']) && $_POST['reserves'] != '' ? explode('|', $_POST['reserves']) : array();

		$_SESSION['extra_registration']['has_error'] = false;

		$_SESSION['extra_registration']['courses']['value'] = '';
		if (count($courses) == 0 && count($reserves) == 0) {
			$_SESSION['extra_registration']['courses']['err_display'] = "block";
			$_SESSION['extra_registration']['has_error'] = true;
		} else {
			$_SESSION['extra_registration']['courses']['err_display'] = "none";
		}

		if (!$_SESSION['extra_registration']['has_error']) {
			try {

				if (count($reserves) > 0) {
					$new_group_infos = array();
					foreach ($reserves as $value) {
						$subject_id = explode('-', $value)[0];
						$topic_id = explode('-', $value)[1];

						array_push($new_group_infos, array('subject_id' => $subject_id,
															'topic_id' => $topic_id));
					}
					$student_id = $_SESSION['user_id'];
					create_new_group_for_student($student_id, $new_group_infos);
				}

				// if (count($courses) > 0) {
				// 	$query = "INSERT INTO group_student (group_info_id, student_id, start_from) VALUES";
				// 	$qPart = array_fill(0, count($courses), "(?, ?, ?)");
				// 	$query .= implode(',', $qPart);
				// 	$stmtA = $connect->prepare($query);
				// 	$j = 1;
				// 	foreach ($courses as $value) {
				// 		$stmtA->bindValue($j++, explode('-', $value)[0], PDO::PARAM_INT);
				// 		$stmtA->bindValue($j++, $_SESSION['user_id'], PDO::PARAM_INT);
				// 		$stmtA->bindValue($j++, explode('-', $value)[1], PDO::PARAM_INT);
				// 	}
				// 	$stmtA->execute();
				// }

				// if (count($reserves) > 0) {
				// 	$student_id = $_SESSION['user_id'];
				// 	foreach ($reserves as $value) {
				// 		$topic_id = explode('-', $value)[1];
				// 		$stmt = $connect->prepare("SELECT rr.id
				// 									FROM registration_reserve rr
				// 									WHERE rr.student_id = :student_id
				// 										AND rr.topic_id = :topic_id");
				// 		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				// 		$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
				// 		$stmt->execute();
				// 		$row_count = $stmt->rowCount();
				// 		if ($row_count == 0) {
				// 			$stmt = $connect->prepare("INSERT INTO registration_reserve (student_id, topic_id) VALUES (:student_id, :topic_id)");
				// 			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				// 			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
				// 			$stmt->execute();
				// 		}
				// 	}
				// 	// $query_registration_reserve = "INSERT INTO registration_reserve (student_id, topic_id) VALUES (:student_id, :topic_id)";
				// 	// foreach ($reserves as $value) {
						
				// 	// 		$stmt = $connect->prepare($query_registration_reserve);
				// 	// 		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				// 	// 		$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
				// 	// 		$stmt->execute();
				// 	// }
				// }
				
				$_SESSION['alert']['r_done'] = true;
				// session_unset($_SESSION['extra_registration']);
				header('Location:../index.php');

			} catch (Exception $e) {
				throw $e;
			}
		} else {
			$_SESSION['alert']['r_error'] = true;
			header("Location:../index.php");
		}
	}

	 else if (isset($_GET['get_groups_by_subject'])) {
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
	}
?>