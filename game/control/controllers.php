<?php
	include_once('../connection.php');

	if (isset($_GET['get_teams']) && $_GET['get_teams']==md5('success')) {
		$stmt = $conn->prepare("SELECT * FROM teames ORDER BY id");
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$result = array();
		foreach ($sql_result as $value) {
			$result[$value['id']] = array(
										'id'=> $value['id'],
										'title' => $value['title'],
										'img' => $value['img'],
										'color' => $value['color']
									);
		}
		echo json_encode($result);
	} else if (isset($_GET['get_infos']) && $_GET['get_infos'] == md5('true')) {

		$answer_button_access = get_answer_button_access($conn);
		$results = get_results($conn);

		$data['answer_button_access'] = $answer_button_access;
		$data['results'] = $results;

		echo json_encode($data);
	} else if (isset($_GET['reset_datas']) && $_GET['reset_datas']==md5('true')) {
		reset_results($conn);
		set_answer_button_access($conn);
	}

	function get_answer_button_access($conn) {

		$stmt = $conn->prepare("SELECT * FROM answer_button_access WHERE is_access = 0");
		$stmt->execute();
		$aba_row_count = $stmt->rowCount();
		$sql_result = $stmt->fetchAll();

		$result = array('count' => 0,
						'data' => array());
		foreach ($sql_result as $value) {
			$result['count'] += $value['is_access'];
			$result['data'][$value['id']] = array('team_id' => $value['team_id'],
												'is_access' => $value['is_access']);
		}
		return $result;
	}

	function get_results($conn) {
		$stmt = $conn->prepare("SELECT * FROM results ORDER BY id");
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$result = array('count' => $result_row_count,
						'data' => array());

		foreach ($sql_result as $value) {
			array_push($result['data'], $value['team_id']);
		}
		return $result;
	}

	function reset_results($conn) {
		$stmt = $conn->prepare("DELETE FROM results WHERE team_id IN (1, 2, 3)");
		$stmt->execute();
	}

	function set_answer_button_access($conn) {
		$stmt = $conn->prepare("UPDATE answer_button_access SET is_access = 1 WHERE team_id IN (1, 2, 3)");
		$stmt->execute();
	}
?>