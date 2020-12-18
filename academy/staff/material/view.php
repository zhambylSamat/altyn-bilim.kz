<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_subjects() {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT s.id, 
											s.title,
											s.is_active
										FROM subject s
										ORDER BY s.title");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subects_info($subjects) {
		GLOBAL $connect;

		try {
			
			$subject_ids_str = "";
			foreach ($subjects as $value) {
				$subject_ids_str .= $value['id'].",";
			}
			$subject_ids_str = mb_substr($subject_ids_str, 0, -1);

			$stmt = $connect->prepare("SELECT t.subject_id,
											count(t.id) AS topic_count,
											(SELECT count(st2.id)
											FROM subtopic st2,
												topic t2
											WHERE st2.topic_id = t2.id
												AND t2.subject_id = t.subject_id) AS subtopic_count,
											(SELECT count(tv2.id)
											FROM topic t2,
												subtopic st2,
												tutorial_video tv2
											WHERE tv2.subtopic_id = st2.id
												AND st2.topic_id = t2.id
												AND t2.subject_id = t.subject_id) AS tutorial_video_count,
											(SELECT count(td2.id)
											FROM topic t2,
												subtopic st2,
												tutorial_document td2
											WHERE td2.subtopic_id = st2.id
												AND st2.topic_id = t2.id
												AND t2.subject_id = t.subject_id) AS tutorial_document_count,
											(SELECT count(ev2.id)
											FROM topic t2,
												subtopic st2,
												end_video ev2
											WHERE ev2.subtopic_id = st2.id
												AND st2.topic_id = t2.id
												AND t2.subject_id = t.subject_id) AS end_video_count,
											(SELECT count(mt2.id)
											FROM topic t2,
												subtopic st2,
												material_test mt2
											WHERE mt2.subtopic_id = st2.id
												AND st2.topic_id = t2.id
												AND t2.subject_id = t.subject_id) AS material_test_count
										FROM topic t
										WHERE t.subject_id IN (".$subject_ids_str.")
										GROUP BY t.subject_id");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topics($subject_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT t.id,
											t.title,
											t.topic_order
									FROM topic t
									WHERE t.subject_id = :subject_id
									ORDER BY t.topic_order");
			$stmt->bindParam(":subject_id", $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topics_info($topics) {

		GLOBAL $connect;

		try {

			$topic_ids_str = "";
			foreach ($topics as $value) {
				$topic_ids_str .= $value['id'].",";
			}
			$topic_ids_str = mb_substr($topic_ids_str, 0, -1);

			$stmt = $connect->prepare("SELECT st.topic_id,
											count(st.id) AS subtopic_count,
											(SELECT count(tv2.id)
											FROM subtopic st2,
												tutorial_video tv2
											WHERE tv2.subtopic_id = st2.id
												AND st2.topic_id = st.topic_id) AS tutorial_video_count,
											(SELECT count(td2.id)
											FROM subtopic st2,
												tutorial_document td2
											WHERE td2.subtopic_id = st2.id
												AND st2.topic_id = st.topic_id) AS tutorial_document_count,
											(SELECT count(ev2.id)
											FROM subtopic st2,
												end_video ev2
											WHERE ev2.subtopic_id = st2.id
												AND st2.topic_id = st.topic_id) AS end_video_count,
											(SELECT count(mt2.id)
											FROM subtopic st2,
												material_test mt2
											WHERE mt2.subtopic_id = st2.id
												AND st2.topic_id = st.topic_id) AS material_test_count
										FROM subtopic st
										WHERE st.topic_id IN (".$topic_ids_str.")
										GROUP BY st.topic_id");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}

	}

	function get_subtopics($topic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT st.id,
											st.title,
											st.subtopic_order
										FROM subtopic st
										WHERE st.topic_id = :topic_id
										ORDER BY st.subtopic_order");

			$stmt->bindParam(":topic_id", $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopics_info($subtopics) {
		GLOBAL $connect;

		try {

			$subtopic_ids_str = "";
			foreach ($subtopics as $value) {
				$subtopic_ids_str .= $value['id'].",";
			}
			$subtopic_ids_str = mb_substr($subtopic_ids_str, 0, -1);

			$stmt = $connect->prepare("SELECT st.id,
											(SELECT count(tv2.id)
											FROM tutorial_video tv2
											WHERE tv2.subtopic_id = st.id) AS tutorial_video_count,
											(SELECT count(td2.id)
											FROM tutorial_document td2
											WHERE td2.subtopic_id = st.id) AS tutorial_document_count,
											(SELECT count(ev2.id)
											FROM end_video ev2
											WHERE ev2.subtopic_id = st.id) AS end_video_count,
											(SELECT count(mt2.id)
											FROM material_test mt2
											WHERE mt2.subtopic_id = st.id) AS material_test_count
										FROM subtopic st
										WHERE st.id IN (".$subtopic_ids_str.")");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_config($subtopic_id, $type) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT id
										FROM subtopic_material_config
										WHERE subtopic_id = :subtopic_id
											AND perceive = :type");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->rowCount() == 0 ? false : true;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_video($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT tv.id,
											tv.link,
											tv.title,
											tv.duration,
											tv.video_order,
											tv.upload_date,
											tv.pop_up
										FROM tutorial_video tv
										WHERE tv.subtopic_id = :subtopic_id
										ORDER BY tv.video_order ASC");
			$stmt->bindParam(":subtopic_id", $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_end_video($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT ev.id,
											ev.link,
											ev.title,
											ev.duration,
											ev.video_order,
											ev.upload_date
										FROM end_video ev
										WHERE ev.subtopic_id = :subtopic_id
										ORDER BY ev.video_order ASC");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_document($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT td.id,
											td.link,
											td.title,
											td.document_order,
											td.upload_date
										FROM tutorial_document td
										WHERE td.subtopic_id = :subtopic_id
										ORDER BY td.document_order ASC");
			$stmt->bindParam(":subtopic_id", $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_by_subtopic_id($subtopic_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT mt.id,
							mt.link,
							mt.test_order,
							mt.title
						FROM material_test mt
						WHERE mt.subtopic_id = :subtopic_id
						ORDER BY mt.test_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$test_info = $stmt->fetchAll();

			$query = "SELECT a.id,
							a.numeration,
							a.prefix,
							a.torf,
							a.material_test_id
						FROM answers a
						WHERE a.subtopic_id = :subtopic_id
						ORDER BY a.numeration, a.prefix";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			$answers_info_result = $stmt->fetchAll();

			$answers_info = array();

			foreach ($answers_info_result as $value) {
				if (!isset($answers_info[$value['numeration']])) {
					$answers_info[$value['numeration']] = array();
				}

				array_push($answers_info[$value['numeration']], array('id' => $value['id'],
																		'prefix' => $value['prefix'],
																		'torf' => $value['torf']));
			}

			$result = array('test' => $test_info,
						'answers' => $answers_info);

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_solve_by_subtopic_id ($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mts.id,
							mts.link,
							mts.file_order,
							mts.title
						FROM material_test_solve mts
						WHERE mts.subtopic_id = :subtopic_id
						ORDER BY mts.file_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$test_solve_info = $stmt->fetchAll();
			
			return $test_solve_info;

		} catch (Exception $e) {
			throw $e;
		}
	}
?>