<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_short_info_by_material_code($code) {
		GLOBAL $connect;

		try {

			$query = "DELETE ml, mlc
						FROM material_link ml
							JOIN material_link_content mlc
								ON mlc.material_link_id = ml.id
						WHERE access_until <= NOW()
							AND code = :code";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->execute();
			
			$query = "SELECT count(mlc.subtopic_id) AS c,
							mlc.subtopic_id
						FROM material_link ml,
							material_link_content mlc
						WHERE ml.code = :code
							AND ml.id = mlc.material_link_id
						GROUP BY mlc.subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				return $sql_result;
			}
			return array();

		} catch (Exception $e) {
			return array();
		}
	}

	function get_materials_by_subtopic($code, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mlc.type
						FROM material_link ml,
							material_link_content mlc
						WHERE ml.code = :code
							AND mlc.subtopic_id = :subtopic_id
							AND mlc.material_link_id = ml.id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$materials_type_result = $stmt->fetchAll();

			$stmt = $connect->prepare("SELECT st.title FROM subtopic st WHERE st.id = :subtopic_id");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$subtopic_title = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

			$result = array('title' => $subtopic_title,
							'tutorial_video' => array(),
							'tutorial_document' => array(),
							'end_video' => array(),
							'material_test_st_id' => 0);
			foreach ($materials_type_result as $value) {
				if ($value['type'] == 'tutorial_video') {
					$result['tutorial_video'] = get_tutorial_videos($subtopic_id);
				} else if ($value['type'] == 'tutorial_document') {
					$result['tutorial_document'] = get_tutorial_documents($subtopic_id);
				} else if ($value['type'] == 'end_video') {
					$result['end_video'] = get_end_videos($subtopic_id);
				} else if ($value['type'] == 'material_test') {
					$result['material_test'] = get_material_test_st_id($subtopic_id);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_videos($subtopic_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT tv.id,
							tv.link,
							tv.title,
							tv.duration
						FROM tutorial_video tv
						WHERE tv.subtopic_id = :subtopic_id
						ORDER BY tv.video_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();

			foreach ($sql_result as $value) {
				$seconds = $value['duration'];
				$hours = floor($seconds / 3600);
				$mins = floor($seconds / 60 % 60);
				$secs = floor($seconds % 60);
				$duration = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
				$vimeo_code = explode('/', $value['link'])[3];
				$result[$value['id']] = array('title' => $value['title'],
												'vimeo_code' => $vimeo_code,
												'link' => $value['link'],
												'duration' => $duration,
												'second_duration' => $seconds);
			}
			
			return $result;

		} catch (Exception $e) {
			return array();
		}
	}

	function get_tutorial_documents($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT td.id,
							td.link,
							td.title
						FROM tutorial_document td
						WHERE td.subtopic_id = :subtopic_id
						ORDER BY td.document_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();

			foreach ($sql_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link']);
			}

			return $result;
			
		} catch (Exception $e) {
			return array();
		}
	}

	function get_end_videos($subtopic_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT ev.id,
							ev.link,
							ev.title AS end_video_title,
							ev.duration
						FROM end_video ev
						WHERE ev.subtopic_id = :subtopic_id
						ORDER BY ev.video_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array();

			foreach ($sql_result as $value) {
				$seconds = $value['duration'];
				$hours = floor($seconds / 3600);
				$mins = floor($seconds / 60 % 60);
				$secs = floor($seconds % 60);
				$duration = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
				$vimeo_code = explode('/', $value['link'])[3];
				$result[$value['id']] = array('title' => $value['end_video_title'],
												'link' => $value['link'],
												'vimeo_code' => $vimeo_code,
												'duration' => $duration,
												'second_duration' => $seconds);
			}
			
			return $result;

		} catch (Exception $e) {
			return array();
		}
	}

	function get_material_test_st_id ($subtopic_id) {
		GLOBAL $connect;

		try {
			
			$query = "SELECT count(mt.id) AS material_test_count
						FROM material_test mt
						WHERE mt.subtopic_id = :subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$mt_count = $stmt->fetch(PDO::FETCH_ASSOC)['material_test_count'];

			return $mt_count == 0 ? 0 : $subtopic_id;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_info ($fio, $code, $subtopic_id) {
		GLOBAL $connect;
		GLOBAL $ab_root;

		try {

			$query = "SELECT ml.id AS material_link_id,
							mlc.subtopic_id
						FROM material_link ml,
							material_link_content mlc
						WHERE ml.code = :code
							AND mlc.material_link_id = ml.id
							AND mlc.type = 'material_test'
							AND mlc.subtopic_id = :subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			$result = array('is_test_access' => false,
							'test_result' => array(),
							'material_test' => array('test' => array(),
													'answers' => array()),
							'material_test_solve' => array(),
							'subtopic_id' => 0,
							'material_link_id' => 0);
			if ($row_count == 0) {
				return $result;
			}

			$material_link_content = $stmt->fetch(PDO::FETCH_ASSOC);
			$result['is_test_access'] = true;
			$result['subtopic_id'] = $material_link_content['subtopic_id'];
			$result['material_link_id'] = $material_link_content['material_link_id'];

			$query = "SELECT mlti.result_json
						FROM material_link_test_info mlti
						WHERE mlti.material_link_id = :material_link_id
							AND mlti.fio = :fio
							AND mlti.subtopic_id = :subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':material_link_id', $material_link_content['material_link_id'], PDO::PARAM_INT);
			$stmt->bindParam(':fio', $fio, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$material_link_test_info_exists = $stmt->rowCount();

			if ($material_link_test_info_exists != 0) {
				$material_link_test_info = $stmt->fetch(PDO::FETCH_ASSOC);
				$result['test_result'] = json_decode($material_link_test_info['result_json'], true);

				$query = "SELECT mts.link,
								mts.title
							FROM material_test_solve mts
							WHERE mts.subtopic_id = :subtopic_id
							ORDER BY mts.file_order";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subtopic_id', $material_link_content['subtopic_id'], PDO::PARAM_INT);
				$stmt->execute();
				$material_test_solve = $stmt->fetchAll();

				foreach ($material_test_solve as $value) {
					array_push($result['material_test_solve'], array('link' => $ab_root.'/academy'.$value['link'],
																	'title' => $value['title']));
				}
			}

			$query = "SELECT mt.link
						FROM material_test mt
						WHERE mt.subtopic_id = :subtopic_id
						ORDER BY mt.test_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $material_link_content['subtopic_id'], PDO::PARAM_INT);
			$stmt->execute();
			$material_test_img_query = $stmt->fetchAll();

			foreach ($material_test_img_query as $value) {
				array_push($result['material_test']['test'], $ab_root.'/academy'.$value['link']);
			}

			$query = "SELECT a.id AS answer_id, 
							a.numeration,
							a.prefix
						FROM answers a
						WHERE a.subtopic_id = :subtopic_id
						ORDER BY a.numeration, a.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $material_link_content['subtopic_id'], PDO::PARAM_INT);
			$stmt->execute();
			$material_test_answers = $stmt->fetchAll();

			foreach ($material_test_answers as $value) {
				if (!isset($result['material_test']['answers'][$value['numeration']])) {
					$result['material_test']['answers'][$value['numeration']] = array();
				}

				// array_push($result['material_test']['answers'][$value['numeration']], array($value['answer_id'] => $value['prefix']));
				$result['material_test']['answers'][$value['numeration']][$value['answer_id']] = $value['prefix'];
			}


			
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}
?>