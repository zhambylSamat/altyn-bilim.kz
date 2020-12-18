<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	if (isset($_GET['submit_test'])) {

		try {

			$fio = $_POST['fio'];
			$subtopic_id = $_POST['subtopic_id'];
			$material_link_id = $_POST['material_link_id'];

			$answers = $_POST['answer'];
			$test_result = check_students_test($answers, $subtopic_id);
            $test_result_json = json_encode($test_result, JSON_UNESCAPED_UNICODE);

            $query = "INSERT INTO material_link_test_info (material_link_id, fio, subtopic_id, result_json)
            										VALUES (:material_link_id, :fio, :subtopic_id, :result_json)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':material_link_id', $material_link_id, PDO::PARAM_INT);
            $stmt->bindParam(':fio', $fio, PDO::PARAM_STR);
            $stmt->bindParam(':result_json', $test_result_json, PDO::PARAM_STR);
            $stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;

		} catch (Exception $e) {
    		$data['success'] = false;
    		$data['message'] = 'ERROR: '.$e->getMessage().'!!!';
    	}
    	echo json_encode($data);
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
?>