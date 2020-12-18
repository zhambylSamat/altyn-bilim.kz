<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_available_trail_tests () {
		GLOBAL $connect;

		try {

			$student_id = $_SESSION['user_id'];

			$query = "SELECT stt.id AS student_trial_test_id,
							sj.title AS subject_title,
							gstt.id AS group_student_trial_test_id
						FROM student_trial_test stt
						INNER JOIN trial_test tt
							ON tt.id = stt.trial_test_id
						INNER JOIN subject sj
							ON sj.id = tt.subject_id
						LEFT JOIN group_student_trial_test gstt
							ON gstt.student_trial_test_id = stt.id
						WHERE stt.student_id = :student_id
							AND stt.result IS NULL";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['student_trial_test_id']] = array('subject_title' => $value['subject_title'],
																	'from_group' => ($value['group_student_trial_test_id'] != '' ? true : false));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_trial_test_result () {
		GLOBAL $connect;

		try {
			
			$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : $_SESSION['user_id'];

			$query = "SELECT stt.id AS student_trial_test_id,
							sj.id AS subject_id,
							sj.title AS subject_title,
							gstt.id AS group_student_trial_test_id,
							stt.result,
							DATE_FORMAT(stt.submit_date, '%d.%m.%Y') AS submit_date
						FROM student_trial_test stt
						INNER JOIN trial_test tt
							ON tt.id = stt.trial_test_id
						INNER JOIN subject sj
							ON sj.id = tt.subject_id
						LEFT JOIN group_student_trial_test gstt
							ON gstt.student_trial_test_id = stt.id
						WHERE stt.student_id = :student_id
							AND stt.result IS NOT NULL
						ORDER BY stt.submit_date DESC";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('result' => array(),
							'submit_dates' => array());

			foreach ($query_result as $value) {
				if (!isset($result['result'][$value['subject_id']])) {
					$result['result'][$value['subject_id']] = array('subject_title' => $value['subject_title'],
																		'stt' => array());
				}
				$res = json_decode($value['result'], true);
				$result['result'][$value['subject_id']]['stt'][$value['student_trial_test_id']] = array('actual_result' => $res['actual_result'],
																											'total_result' => $res['total_result'],
																											'submit_date' => $value['submit_date']);
				if (in_array($value['submit_date'], $result['submit_dates'])) {
					array_push($result['submit_dates'], $value['submit_date']);
				}
			}

			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_test_info_by_stt_id ($student_trial_test_id) {
		GLOBAL $connect;
		GLOBAL $ab_root;

		try {

			$query = "SELECT tt.id AS trial_test_id,
							stt.result
						FROM student_trial_test stt,
							trial_test tt
						WHERE stt.id = :student_trial_test_id
							AND tt.id = stt.trial_test_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_trial_test_id', $student_trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$stt_info = $stmt->fetch(PDO::FETCH_ASSOC);

			$query = "SELECT ttf.id AS trial_test_file_id,
							ttf.file_link
						FROM trial_test_file ttf
						WHERE ttf.trial_test_id = :trial_test_id
						ORDER BY ttf.file_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $stt_info['trial_test_id'], PDO::PARAM_INT);
			$stmt->execute();
			$trial_test_files = $stmt->fetchAll();

			$query = "SELECT tta.id AS trial_test_answer_id,
							tta.numeration,
							tta.prefix,
							tta.torf
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
						ORDER BY tta.numeration, tta.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $stt_info['trial_test_id'], PDO::PARAM_INT);
			$stmt->execute();
			$trial_test_answers = $stmt->fetchAll();

			$test_result = json_decode($stt_info['result'], true);
			$result = array('test' => array('test_files' => array(),
											'answers' => array()),
							'test_result' => $test_result == "" ? array() : $test_result);

			foreach ($trial_test_files as $value) {
				array_push($result['test']['test_files'], $ab_root.'/academy'.$value['file_link']);
			}

			foreach ($trial_test_answers as $value) {
				if (!isset($result['test']['answers'][$value['numeration']])) {
					$result['test']['answers'][$value['numeration']] = array();
				}

				$result['test']['answers'][$value['numeration']][$value['trial_test_answer_id']] = array('prefix' => $value['prefix'],
																										'torf' => $value['torf']);
			}
			
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}
?>