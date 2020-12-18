<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/global_controller.php');
	include_once($root.'/student/trial_test/view.php');

    // check_student_access();
    check_admin_student_access();

    if (isset($_GET['get_available_trial_test_count'])) {
    	try {
    		$available_trial_test = get_available_trail_tests();
    		$data['available_trial_test_count'] = count($available_trial_test);
    		$data['success'] = true;
	    } catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = "ERROR: ".$e->getMessage()."!!!";
    	}
    	echo json_encode($data);
    } else if (isset($_GET['submit-trial-test'])) {
    	try {

    		$student_trial_test_id = $_POST['student-trial-test-id'];
    		$answers = $_POST['answer'];

    		$query = "SELECT tt.id AS trial_test_id
    					FROM trial_test tt,
    						student_trial_test stt
    					WHERE stt.id = :student_trial_test_id
							AND tt.id = stt.trial_test_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_trial_test_id', $student_trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$trial_test_id = $stmt->fetch(PDO::FETCH_ASSOC)['trial_test_id'];

			$test_result = check_student_trial_test($answers, $trial_test_id);
			$test_result_json = json_encode($test_result, JSON_UNESCAPED_UNICODE);

			$query = "UPDATE student_trial_test SET result = :result, submit_date = NOW() WHERE id = :student_trial_test_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':result', $test_result_json, PDO::PARAM_STR);
			$stmt->bindParam(':student_trial_test_id', $student_trial_test_id, PDO::PARAM_INT);
			$stmt->execute();

    		$data['success'] = true;
	    } catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = "ERROR: ".$e->getMessage()."!!!";
    	}
    	echo json_encode($data);
    } else if (isset($_GET['get_student_trial_test_result'])) {
        try {

            $data['test_results'] = get_student_trial_test_result();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "ERROR: ".$e->getMessage()."!!!";
        }
        echo json_encode($data);
    }


    function check_student_trial_test ($answers, $trial_test_id) {
    	GLOBAL $connect;

    	try {
    		$query = "SELECT tta.id AS trial_test_answer_id, 
    						tta.numeration,
    						tta.prefix,
    						tta.torf
    					FROM trial_test_answer tta
    					WHERE tta.trial_test_id = :trial_test_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$sql_result = $stmt->fetchAll();

    		$answers_result = array('actual_result' => 0,
    						'total_result' => count($answers) == 20 ? 20 : 40,
    						'result' => array());

    		$answers_info = array();
    		$answers_count = array();
    		foreach ($sql_result as $value) {
    			if ($value['torf'] == 1) {
    				if (!isset($answers_info[$value['numeration']])) {
	    				$answers_info[$value['numeration']] = array();
	    			}
	    			array_push($answers_info[$value['numeration']], $value['trial_test_answer_id']);
    			}

    			if (!isset($answers_count[$value['numeration']])) {
    				$answers_count[$value['numeration']] = 0;
    			}
    			$answers_count[$value['numeration']]++;
    		}

    		foreach ($answers as $numeration => $trial_test_answers) {
    			$initial_mark = 1;
    			if ($answers_count[$numeration] == 8) {
    				$initial_mark = 2;
    			}
    			
    			$answers_diff = remove_unique_elements($trial_test_answers, $answers_info[$numeration]);
    			$answers_diff_count = count($answers_diff);
    			
    			$initial_mark -= $answers_diff_count;
    			if ($initial_mark < 0) {
    				$initial_mark = 0;
    			}
    			foreach ($trial_test_answers as $trial_test_answer_id) {
    				if (!isset($answers_result['result'][$numeration])) {
    					$answers_result['result'][$numeration]['actual_result'] = array();
    				}
    				array_push($answers_result['result'][$numeration]['actual_result'], $trial_test_answer_id);
    			}

    			$answers_result['actual_result'] += $initial_mark;
    		}

    		return $answers_result;

    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function remove_unique_elements ($arr1, $arr2) {
    	$sub_index = 0;
    	foreach ($arr1 as $i => $value) {
    		if (in_array($value, $arr2)) {
    			$index = array_search($value, $arr2);
    			array_splice($arr2, $index, 1);
    			array_splice($arr1, $i-$sub_index, 1);
    			$sub_index++;
    		}
    	}
    	return array_merge($arr1, $arr2);
    }
?>