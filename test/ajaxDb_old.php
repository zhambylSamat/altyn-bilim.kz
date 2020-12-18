<?php
$includeBool = false;
$error = array();
$data = array();
$data['success'] = false;
$question_img = '';
include_once('../connection.php');

if (isset($_GET[md5('create_test')])) {
	try {
		$ees_id = $_POST['ees_id'];
		$result_arr = array();
		
		$result_arr['content'] = array();
		$result_arr['false_count'] = 0;
		$result_arr['finish'] = false;
		$test_result = get_next($ees_id, array(), "new", false, array());
		if (count($test_result)!=0) {
			array_push($result_arr['content'], $test_result['test']);
			$result_arr['test_settings'] = $test_result['test_settings'];
			$result_arr['count'] = 1;
		}
		
		if (save_test_result($result_arr, $ees_id, 0)) {
			echo json_encode($result_arr);
		} else {
			echo "Something wrong when updating database <br>".$data['error'];
		}
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
}
else if (isset($_GET['test_result'])){
	try {

		$ees_id = $_POST['ees_id'];
		$answer_num = $_POST['answer_num'];
		$result_arr = $_SESSION['test_result'];
		$data['finish'] = false;
		$next = false;
		$end_test_index = array_search(end($result_arr['content'])['test_num'], array_column($result_arr['content'], "test_num"));
		$end_question_index = array_search(end($result_arr['content'][$end_test_index]['content'])['question_num'], array_column($result_arr['content'][$end_test_index]['content'], "question_num"));
		// $result_arr['content'][$end_test_index]['content'][$end_question_index]['answer_num'] = $answer_num;

		$is_correct = false;

		if($answer_num == "") {
			$is_correct = false;
			$result_arr['content'][$end_test_index]['content'][$end_question_index]['answer_num'] = "";
			$result_arr['content'][$end_test_index]['content'][$end_question_index]['result'] = 0;
			$result_arr['false_count']++;
			$next = true;
		} else {
			$question = end(end($result_arr['content'])['content']);
			foreach ($question['answer'] as $value) {
				if ($value['answer_num'] == $answer_num && $value['torf'] == '1') {
					$next = true;
					$is_correct = true;
					break;
				}
			}

			if (!$next) {
				$result_arr['content'][$end_test_index]['content'][$end_question_index]['answer_num'] = $answer_num;
				$result_arr['content'][$end_test_index]['content'][$end_question_index]['result'] = -1;
				if (count(end($result_arr['content'])['content']) == 2 || count(end($result_arr['content'])['content']) == 3) {
					$result_arr['false_count']++;
					$next = true;
				}
			} else if ($next) {
				$result_arr['content'][$end_test_index]['content'][$end_question_index]['answer_num'] = $answer_num;
				$result_arr['content'][$end_test_index]['content'][$end_question_index]['result'] = 1;
				if (count(end($result_arr['content'])['content']) == 2) {
					$next = false;
				}
			}
		} 

		$data['finish'] = $result_arr['false_count'] == 5 ? true : false;
		if (!$data['finish']) {
			if (!$next) {
				$test_num = end($result_arr['content'])['test_num'];
				$test_index = array_search($test_num, array_column($result_arr['content'], "test_num"));
				$question_nums = array();
				foreach (end($result_arr['content'])['content'] as $value) {
					array_push($question_nums, $value['question_num']);
				}

				$res = get_next($ees_id, $question_nums, "nextQuestion", $is_correct, $result_arr['test_settings']);
				$data['res'] = $res;
				if (count($res)!=0){
					$result_arr['test_settings'] = $res['test_settings'];
					$res = end($res['test']['content']);
					array_push($result_arr['content'][$test_index]['content'], $res);
				} else {
					$data['finish'] = true;
				}
			} else {
				$res = get_next($ees_id, array(), "nextTest", $is_correct, $result_arr['test_settings']);
				if(count($res)!=0) {
					$data['res'] = $res;
					array_push($result_arr['content'], $res['test']);
					$result_arr['test_settings'] = $res['test_settings'];
					$result_arr['count'] += 1;
				} else {
					$data['finish'] = true;
				}	
			}
		}

		$_SESSION['test_result'] = $result_arr;
		$data['success'] = true;
		if ($data['finish']) {
			$result_arr['submit_date'] = date('Y-m-d H:i:s');
			$data['success'] = save_test_result($result_arr, $ees_id, 1);
		} else {
			save_test_result($result_arr, $ees_id, 0);
		}
		echo json_encode($data);
	} catch(PDOException $e) {
		$data['success'] = false;
		$data['error'] = "Error: " . $e->getMessage();
    	echo json_encode($data);
	}
}



	function get_next($ees_id, $question_nums, $action, $is_correct, $test_settings){

		// function get_test_orders($conn, $eep_id) {
		// 	try {
		// 		$stmt = $conn->prepare("SELECT count(test_order) as c,
		// 									test_order
		// 								FROM entrance_examination
		// 								WHERE eep_id = :eep_id
		// 								GROUP BY test_order
		// 								ORDER BY test_order");
		// 		$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
		// 		$stmt->execute();
		// 		$stmt_res = $stmt->fetchAll();
		// 		$res = array();
		// 		foreach ($stmt_res as $value) { 
		// 			$res[(int)$value['test_order']] = (int)$value['c'];
		// 		}
		// 		return $res;
		// 	} catch (PDOException $e) { throw $e; }
		// }

		function get_eep_id($conn, $ees_id){
			try {
				$stmt = $conn->prepare("SELECT eep_id FROM entrance_examination_student WHERE id = :id");
				$stmt->bindParam(':id', $ees_id, PDO::PARAM_INT);
				$stmt->execute();
				return $stmt->fetch(PDO::FETCH_ASSOC)['eep_id'];
			} catch (PDOException $e) { throw $e; }
		}

		function one_direction_array_to_str_for_sql_in($array){
			$res = "";
			for ($i=0; $i<count($array); $i++) { 
				$res .= "'".$array[$i]."'";
				if ($i != count($array)-1) $res .= ", ";
			}
			if (count($array)==0) return "''";
			return $res;
		}

		function get_test($conn, $eep_id, $test_nums) {
			$test_nums_str = one_direction_array_to_str_for_sql_in($test_nums);
			try {
				$query = "SELECT ee.test_num,
								ee.test_order
							FROM entrance_examination ee,
								test t
							WHERE ee.eep_id = :eep_id
								AND ee.test_num NOT IN ($test_nums_str)
								AND t.test_num = ee.test_num
							ORDER BY SUBSTRING_INDEX(t.name, ' ', 1),
								CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
								CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
								SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 3), ' ', -1)
							LIMIT 1";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':eep_id', $eep_id, PDO::PARAM_INT);
				$stmt->execute();
				return $stmt->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) { 
				throw $e; 
			}
		}

		function get_question($conn, $question_nums, $test_num) {
			$question_nums_str = one_direction_array_to_str_for_sql_in($question_nums);
			try {
				$query = "SELECT t.test_num,
								t.name,
								q.question_num,
								q.question_text,
								q.question_img,
								a.answer_num,
								a.answer_text,
								a.answer_img,
								a.torf
							FROM test t,
								question q,
								answer a
							WHERE t.test_num = :test_num
								AND q.question_num = (SELECT q2.question_num
													FROM question q2
													WHERE q2.test_num = t.test_num
														AND q2.question_num NOT IN ($question_nums_str)
													ORDER BY q2.id
													LIMIT 1)
								AND a.question_num = q.question_num";
				$stmt = $conn->prepare($query);
				$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
				$stmt->execute();
				return $stmt->fetchAll();
			} catch (PDOException $e) { 
				throw $e; 
			}
		}

		function check_test_order($prev_test_order, $is_correct) {
			if ($prev_test_order == -1) {
				return "start";
			} else if ($prev_test_order!=0 && $prev_test_order%2==0) {
				if ($is_correct) {
					return "plus_two";
				} else if (!$is_correct) {
					return "minus_one";
				}
			} else if ($prev_test_order!=0 && $prev_test_order%2!=0) {
				return "get";
			} else if ($prev_test_order==0) {
				return "get";
			}
		}

		global $conn;

		$eep_id = get_eep_id($conn, $ees_id);
		if (count($test_settings) == 0) {
			$test_settings['finish'] = false;
			$test_settings['next_with_test_order'] = true;
			$test_settings['current_test_order'] = -1;
			$test_settings['test_nums'] = array();
		} 

		$test_order = $test_settings['current_test_order'];
		$next_to = $test_settings['next_with_test_order'];
		$test_nums = $test_settings['test_nums'];

		$result = array();

		if ($action=='new') {
			$test = get_test($conn, $eep_id, $test_order, $test_nums);
			check_test_order($test_order, $test);
		} else if ($action=='nextTest') {

		} else if ($action=='nextQuestion') {

		}

		$test_settings['current_test_order'] = $test_order;

		if ($test_settings['finish']) return array();
		return map_test_db_to_json($result, $test_settings, $test_order);
	}

	function map_test_db_to_json($res, $test_settings, $test_order) {
		$test = array();
		$question = array();
		$answer = array();
		$test_num = "";
		$question_num = "";
		$answer_num = "";

		foreach ($res as $value) {
			if ($test_num != $value["test_num"]) {
				$test_num = $value['test_num'];
				$test["test_num"] = $value['test_num'];
				$test["test_name"] = $value['name'];
				$test['test_order'] = $test_order;
				$test['content'] = array();
			}
			if ($question_num != $value['question_num']) {
				$question_num = $value['question_num'];
				$question["question_num"] = $value["question_num"];
				$question["question_txt"] = $value["question_text"];
				$question["question_img"] = $value["question_img"];
				$question['answer'] = array();
			}
			if ($answer_num != $value['answer_num']) {
				$answer_num = $value['answer_num'];
				$answer_tmp['answer_num'] = $value['answer_num'];
				$answer_tmp['answer_txt'] = $value['answer_text'];
				$answer_tmp['answer_img'] = $value['answer_img'];
				$answer_tmp['torf'] = $value['torf'];
			}
			array_push($answer, $answer_tmp);
		}
		if (empty($answer) || empty($question) || empty($test)) {
			return null;
		}

		$question['answer'] = $answer;
		array_push($test['content'], $question);
		$res['test_settings'] = $test_settings;
		$res['test'] = $test;
		return $res;
	}

	function get_test_name($ees_id){
		try {
			global $conn;

			$stmt = $conn->prepare("SELECT t.name,
										t.test_num,
										ee.test_order
									FROM test t,
										entrance_examination_student ees,
										entrance_examination ee
									WHERE ees.id = :ees_id
										AND ee.eep_id = ees.eep_id
										AND t.test_num = ee.test_num
									ORDER BY SUBSTRING_INDEX(t.name, ' ', 1),
											CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
											CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED),
											SUBSTRING_INDEX(SUBSTRING_INDEX(t.name, ' ', 3), ' ', -1)");
			$stmt->bindParam(':ees_id', $ees_id, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();
			$res = array();

			foreach ($result as $value) {
				$res[$value['test_num']]['name'] = $value['name'];
				$res[$value['test_num']]['test_order'] = $value['test_order'];
			}
			return $res;

		} catch (PDOException $e) {
			throw $e;
		}
	}

	function save_test_result($test_result, $ees_id, $finish){
		try {
			global $conn;
			$result_json = json_encode($test_result, true);
			$avg_result = null;
			$submit_date = null;
			if ($finish == 1) {
				$submit_date = $test_result['submit_date'];
				$total_count = $test_result['count'];
				$false_count = $test_result['false_count'];
				$avg_result = round(($total_count-$false_count)/$total_count, 2);
			}

			$test_name_list = "";
			if ($finish) {
				$test_name_list = json_encode(get_test_name($ees_id), true);
			}

			$sql_query = "UPDATE entrance_examination_student 
		 					SET submit_date = :submit_date, 
		 						result_json = :result_json,
		 						avg_result = :avg_result,
		 						finish = :finish,
		 						test_content = :test_content
		 					WHERE id = :id";
			$stmt = $conn->prepare($sql_query);
			$stmt->bindParam(':id', $ees_id, PDO::PARAM_INT);
			$stmt->bindParam(':avg_result', $avg_result, PDO::PARAM_STR);
			$stmt->bindParam(':result_json', $result_json, PDO::PARAM_STR);
			$stmt->bindParam(':submit_date', $submit_date, PDO::PARAM_STR);
			$stmt->bindParam(':finish', $finish, PDO::PARAM_INT);
			$stmt->bindParam(':test_content', $test_name_list, PDO::PARAM_STR);
			$stmt->execute();

			return true;
		} catch(PDOException $e) {
			throw $e;
    	}
	}












// function get_test($ees_id, $test_num, $question_nums, $test_nums, $action, $test_order) {
	// 	global $conn;
	// 	$sqlTest = "";
	// 	$sqlQuestion = "";

	// 	if ($action == "new") {
	// 		$sqlTest = "t2.test_num IN (ee.test_num)";
	// 		$sqlQuestion = "WHERE q2.test_num = t.test_num";
	// 	} else if ($action == "nextQuestion") {
	// 		if ($test_num == "" || $question_nums == "") {
	// 			return false;
	// 		}
	// 		$sqlTest = " t2.test_num = '$test_num'";
	// 		$sqlQuestion = "WHERE q2.test_num = t.test_num AND q2.question_num NOT IN (".$question_nums.")";
	// 	} else if ($action == "nextTest") {
	// 		if ($test_nums == "") {
	// 			return false;
	// 		}
	// 		$sqlTest = " t2.test_num IN (ee.test_num) AND t2.test_num NOT IN (".$test_nums.")";
	// 		$sqlQuestion = "WHERE q2.test_num = t.test_num";
	// 	} 

	// 	if ($action == "checkpoint") {
	// 		$parent_sql_test = "(SELECT t2.test_num 
	// 								FROM test t2,
	// 									entrance_examination ee
	// 								WHERE ee.eep_id = ees.eep_id
	// 									ee.test_order = :test_order
	// 							)";
	// 	} else {
	// 		$parent_sql_test = "(SELECT t2.test_num 
	// 							FROM test t2,
	// 								entrance_examination ee
	// 							WHERE ee.eep_id = ees.eep_id
	// 								AND ".$sqlTest." 
	// 								AND (ee.test_order = :test_order)
	// 							ORDER BY SUBSTRING_INDEX(t2.name, ' ', 1),
	// 								CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t2.name, ' ', 2), ' ', -1), '.', 1) AS UNSIGNED),
	// 								CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(t2.name, ' ', 2), ' ', -1), '.', -1) AS UNSIGNED)
	// 							LIMIT 1)"
	// 	}


	// 	$query = "SELECT t.test_num, 
	// 					t.name,
	// 					q.question_num,
	// 					q.question_text,
	// 					q.question_img,
	// 					a.answer_num,
	// 					a.answer_text,
	// 					a.answer_img,
	// 					a.torf
	// 				FROM entrance_examination_student ees,
	// 					test t,
	// 					question q,
	// 					answer a
	// 				WHERE ees.id = :ees_id
	// 					AND t.test_num = $parent_sql_test
	// 					AND q.question_num = (SELECT q2.question_num 
	// 										FROM question q2 
	// 										$sqlQuestion
	// 										ORDER BY q2.id
	// 										LIMIT 1)
	// 					AND a.question_num = q.question_num";
	// 	try {
	// 		$stmt = $conn->prepare($query);
			
	// 		$stmt->bindParam(':ees_id', $ees_id, PDO::PARAM_INT);
	// 		$stmt->bindParam(':test_order', $test_order, PDO::PARAM_INT);

	// 		$stmt->execute();
	// 		$res = $stmt->fetchAll();
	// 		return map_test_db_to_json($res, $test_order);
	// 	} catch(PDOException $e) {
	// 		$data['error'] ="Error: " . $e->getMessage(); 
	// 		$data['query'] = $query;
	// 		return $data;
 //    	}
	// }












	// function get_number_of_test_order_zero($conn) {
		// 	$stmt = $conn->prepare("SELECT count(id) AS c
		// 							FROM entrance_examination
		// 							WHERE test_order = 0");
		// 	$stmt->execute();
		// 	return $stmt->fetch(PDO::FETCH_ASSOC)['c'];
		// }

		// function check_test_order($conn, $current_checkpoint_tests, $test_order, $is_correct) {
		// 	$is_even = false;
		// 	if ($test_order%2==0) $is_even = true;

		// 	$query_even = "SELECT count(test_order) AS c,
		// 					test_order
		// 				FROM entrance_examination
		// 				WHERE test_order = (:test_order-1)
		// 					OR test_order = (:test_order+2)
		// 				GROUP BY test_order";
		// 	$query_odd = "SELECT count(test_order) AS c,
		// 					test_order
		// 				FROM entrance_examintion 
		// 				WHERE test_order = (:test_order+3)
		// 					OR test_order = (:test_order)";
		// 	$result_query = $query_odd;

		// 	if ($is_even) {
		// 		$result_query = $query_even;
		// 	}

		// 	$stmt = $conn->prepare($result_query);
		// 	$stmt->bindParam(':test_order', $test_order, PDO::PARAM_STR);
		// 	$stmt->execute();
		// 	$test_order_result = $stmt->fetchAll();

		// 	$minus_one = -1;
		// 	$plus_two = -1;
		// 	$plus_three = -1;
		// 	$plus_zero = -1;

		// 	foreach ($test_order_result as $value) {
		// 		if ($value['test_order'] == ($test_order-1)) $minus_one = (int)$value['c'];
		// 		if ($value['test_order'] == ($test_order+2)) $plus_two = (int)$value['c'];
		// 		if ($value['test_order'] == ($test_order+3)) $plus_three = (int)$value['c'];
		// 		if ($value['test_order'] == ($test_order)) $plus_zero = (int)$value['c'];
		// 	}

		// 	if (count($test_order_result) > 0) {
		// 		if ($is_even && $is_correct) {
					
		// 		}
		// 	} 
		// 	return 0;
		// }