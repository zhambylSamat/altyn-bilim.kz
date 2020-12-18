<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once('../common/connection.php');
	include_once('../common/global_controller.php');


	$from_subject_id = 20;
	$to_subject_id = 28;

	$data = array();

	$subject_info = get_subject_by_id($from_subject_id);
	// print_r($subject_info);
	insert_subject_material($to_subject_id, $subject_info['topics']);

	function insert_subject_material ($subject_id, $topics) {
		GLOBAL $connect;

		try {

			foreach ($topics as $topic) {
				$topic_id = insert_topic($subject_id, $topic['title'], $topic['topic_order']);

				foreach ($topic['subtopics'] as $subtopic) {
					$subtopic_id = insert_subtopic($topic_id, $subtopic['title'], $subtopic['subtopic_order']);

					foreach ($subtopic['materials']['tutorial_video'] as $tv) {
						insert_tutorial_video($subtopic_id, $tv);
					}

					foreach ($subtopic['materials']['tutorial_document'] as $td) {
						insert_tutorial_document($subtopic_id, $td);
					}

					foreach ($subtopic['materials']['end_video'] as $ev) {
						insert_end_video($subtopic_id, $ev);
					}

					foreach ($subtopic['materials']['material_test'] as $mt) {
						insert_material_test($subtopic_id, $mt);
					}

					foreach ($subtopic['materials']['test_solve'] as $mts) {
						insert_material_test_solve($subtopic_id, $mts);
					}
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_material_test_solve ($subtopic_id, $mts) {
		GLOBAL $connect;
		GLOBAL $permitted_chars;
		GLOBAL $root;

		try {

			$arr = explode('.', $mts['link']);
			$ext = $arr[count($arr)-1];
			$target_link = '/material_test_solve/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;
			copy($root.$mts['link'], $root.$target_link);

			$query = "INSERT INTO material_test_solve (subtopic_id, link, file_order, title)
												VALUES (:subtopic_id, :link, :file_order, :title)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $target_link, PDO::PARAM_STR);
			$stmt->bindParam(':file_order', $mts['file_order'], PDO::PARAM_INT);
			$stmt->bindParam(':title', $mts['title'], PDO::PARAM_STR);
			$stmt->execute();

			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_material_test ($subtopic_id, $mt) {
		GLOBAL $connect;
		GLOBAL $permitted_chars;
		GLOBAL $root;

		try {

			$arr = explode('.', $mt['link']);
			$ext = $arr[count($arr)-1];
			$target_link = '/material_tests/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;
			copy($root.$mt['link'], $root.$target_link);

			$query = "INSERT INTO material_test (subtopic_id, link, test_order, title)
										VALUES (:subtopic_id, :link, :test_order, :title)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $target_link, PDO::PARAM_STR);
			$stmt->bindParam(':test_order', $mt['test_order'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $mt['title'], PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_tutorial_document ($subtopic_id, $td) {
		GLOBAL $connect;
		GLOBAL $permitted_chars;
		GLOBAL $root;

		try {
			$arr = explode('.', $td['link']);
			$ext = $arr[count($arr)-1];
			$target_link = '/material_docs/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;
			copy($root.$td['link'], $root.$target_link);
			$query = "INSERT INTO tutorial_document (subtopic_id, link, document_order, title)
											VALUES (:subtopic_id, :link, :document_order, :title)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $target_link, PDO::PARAM_STR);
			$stmt->bindParam(':document_order', $td['document_order'], PDO::PARAM_INT);
			$stmt->bindParam(':title', $td['title'], PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
 
	function insert_end_video ($subtopic_id, $ev) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO end_video (subtopic_id, link, title, duration, video_order)
										VALUES (:subtopic_id, :link, :title, :duration, :video_order)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $ev['link'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $ev['title'], PDO::PARAM_STR);
			$stmt->bindParam(':duration', $ev['duration'], PDO::PARAM_INT);
			$stmt->bindParam(':video_order', $ev['video_order'], PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_tutorial_video ($subtopic_id, $tv) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO tutorial_video (subtopic_id, link, title, duration, pop_up, video_order)
											VALUES (:subtopic_id, :link, :title, :duration, :pop_up, :video_order)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $tv['link'], PDO::PARAM_STR);
			$stmt->bindParam(':title', $tv['title'], PDO::PARAM_STR);
			$stmt->bindParam(':duration', $tv['duration'], PDO::PARAM_INT);
			$stmt->bindParam(':pop_up', $tv['pop_up'], PDO::PARAM_INT);
			$stmt->bindParam(':video_order', $tv['video_order'], PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_subtopic($topic_id, $subtopic_title, $subtopic_order) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO subtopic (topic_id, title, subtopic_order)
										VALUES (:topic_id, :title, :subtopic_order)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $subtopic_title, PDO::PARAM_STR);
			$stmt->bindParam(':subtopic_order', $subtopic_order, PDO::PARAM_INT);
			$stmt->execute();
			$subtopic_id = $connect->lastInsertId();

			return $subtopic_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_topic ($subject_id, $topic_title, $topic_order) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO topic (subject_id, title, topic_order)
									VALUES (:subject_id, :title, :topic_order)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $topic_title, PDO::PARAM_STR);
			$stmt->bindParam(':topic_order', $topic_order, PDO::PARAM_INT);
			$stmt->execute();

			$topic_id = $connect->lastInsertId();

			return $topic_id;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subject_by_id($subject_id) {
		GLOBAL $connect;

		try {

			$result = array();
			$query = "SELECT sj.title
						FROM subject sj
						WHERE sj.id = :subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$result = array('subject_id' => $subject_id,
							'title' => $query_result['title'],
							'topics' => get_topic_by_subject_id($subject_id));
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topic_by_subject_id ($subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT t.id AS topic_id, 
							t.title,
							t.topic_order
						FROM topic t
						WHERE t.subject_id = :subject_id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['topic_id']] = array('topic_id' => $value['topic_id'],
													'title' => $value['title'],
													'topic_order' => $value['topic_order'],
													'subtopics' => get_subtopic_by_topic_id($value['topic_id']));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopic_by_topic_id ($topic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT st.id AS subtopic_id,
							st.title,
							st.subtopic_order
						FROM subtopic st
						WHERE st.topic_id = :topic_id
						ORDER BY st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['subtopic_id']] = array('title' => $value['title'],
														'subtopic_order' => $value['subtopic_order'],
														'materials' => 
															array('tutorial_video' => get_tutorial_video_by_subtopic($value['subtopic_id']),
																'tutorial_document' => get_tutorial_document_by_subtopic($value['subtopic_id']),
																'end_video' => get_end_video_by_subtopic($value['subtopic_id']),
																'material_test' => get_material_test_by_subtopic($value['subtopic_id']),
																'test_solve' => get_material_test_solve($value['subtopic_id'])));
			}
			
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_video_by_subtopic ($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tv.id,
							tv.link,
							tv.title,
							tv.duration, 
							tv.pop_up,
							tv.video_order
						FROM tutorial_video tv
						WHERE tv.subtopic_id = :subtopic_id
						ORDER BY tv.video_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link'],
												'title' => $value['title'],
												'duration' => $value['duration'],
												'pop_up' => $value['pop_up'],
												'video_order' => $value['video_order']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tutorial_document_by_subtopic ($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT td.id,
							td.link,
							td.title,
							td.document_order
						FROM tutorial_document td
						WHERE td.subtopic_id = :subtopic_id
						ORDER BY td.document_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link'],
												'title' => $value['title'],
												'document_order' => $value['document_order']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_end_video_by_subtopic ($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT ev.id,
							ev.link,
							ev.title,
							ev.duration,
							ev.video_order
						FROM end_video ev
						WHERE ev.subtopic_id = :subtopic_id
						ORDER BY ev.video_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link'],
												'title' => $value['title'],
												'duration' => $value['duration'],
												'video_order' => $value['video_order']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_by_subtopic ($subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mt.id,
							mt.link,
							mt.title,
							mt.test_order
						FROM material_test mt
						WHERE mt.subtopic_id = :subtopic_id
						ORDER BY mt.test_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link'],
												'test_order' => $value['test_order']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_solve ($subtopic_id) {
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
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['id']] = array('title' => $value['title'],
												'link' => $value['link'],
												'file_order' => $value['file_order']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>