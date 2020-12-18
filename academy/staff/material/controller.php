<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

	$data = array();

	if (isset($_GET['change-order']) && isset($_POST['data'])) {
		try {
			$update_datas = $_POST['data'];

			if ($_GET['change-order'] == 'topic') {
				$query = 'UPDATE topic SET topic_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'subtopic') {
				$query = 'UPDATE subtopic SET subtopic_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'tutorial_video') {
				$query = 'UPDATE tutorial_video SET video_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'end_video') {
				$query = 'UPDATE end_video SET video_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'tutorial_document') {
				$query = 'UPDATE tutorial_document SET document_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'material_test') {
				$query = 'UPDATE material_test SET test_order = ? WHERE id = ?';
			} else if ($_GET['change-order'] == 'material_test_solve') {
				$query = 'UPDATE material_test_solve SET file_order = ? WHERE id = ?';
			}

			$stmt = $connect->prepare($query);
			foreach ($update_datas as $value) {
				if (isset($value['id'])) {
					$stmt->execute(array($value['order'], $value['id']));
				}
			}

			$data['success'] = true;
			echo json_encode($data);

		} catch (Exception $e) {
			throw $e;
		}
	} else if (isset($_GET['vimeo-link'])) {
		try {

			$subtopic_id = $_POST['subtopic_id'];
			$link = $_POST['vimeo-link'];
			$duration = $_POST['duration'];
			$title = $_POST['title'];

			$table_name = "";
			if ($_GET['type'] == 'tutorial_video') {
				$table_name = "tutorial_video";
			} else if ($_GET['type'] == 'end_video') {
				$table_name = "end_video";
			}

			$video_order = get_video_order_by_subtopic_id($table_name, $subtopic_id);
			$data['video_order'] = $video_order;

			$stmt = $connect->prepare("INSERT INTO ".$table_name." (subtopic_id, link, title, duration, video_order) VALUES(:subtopic_id, :link, :title, :duration, :video_order)");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':link', $link, PDO::PARAM_STR);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':duration', $duration, PDO::PARAM_STR);
			$stmt->bindParam(':video_order', $video_order, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
			// throw $e;
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['document-link'])) {

		try {
			$file = $_FILES['document'];

			if ($file['size'] <= 10410760) { //2097152

				$title = $_POST['title'];
				$subtopic_id = $_POST['subtopic_id'];
				$document_order = get_document_order_by_subtopic_id($subtopic_id);

				$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				$target_file = '/material_docs/'.time().'_'.generateRandomString().'.'.$ext;

				$stmt = $connect->prepare("INSERT INTO tutorial_document (subtopic_id, link, document_order, title)
																VALUES (:subtopic_id, :link, :document_order, :title)");
				$stmt->bindParam(':subtopic_id', 	$subtopic_id, 		PDO::PARAM_INT);
				$stmt->bindParam(':link', 			$target_file, 		PDO::PARAM_STR);
				$stmt->bindParam(':document_order', $document_order,	PDO::PARAM_INT);
				$stmt->bindParam(':title', 			$title, 			PDO::PARAM_STR);
				$stmt->execute();

				move_uploaded_file($_FILES['document']["tmp_name"], $root.$target_file);

				$data['success'] = true;

			} else {
				$data['file_size'] = $file['size'];
				$data['success'] = false;
			}
			
		} catch (Exception $e) {
			// throw $e;
			$data['success'] = false;
			$data['message'] = "db error";
		}
		echo json_encode($data);

	} else if (isset($_GET['remove_materials'])) {
		try {

			$id = $_GET['id'];

			$table_name = "";
			if ($_GET['obj'] == 'tutorial_video') {
				remove_tutorial_video($id);
			} else if ($_GET['obj'] == 'end_video') {
				remove_end_video($id);
			} else if ($_GET['obj'] == 'subtopic') {
				remove_subtopic($id);
			} else if ($_GET['obj'] == 'topic') {
				remove_topic($id);
			} else if ($_GET['obj'] == 'subject') {
				remove_subject($id);
			} else if ($_GET['obj'] == 'tutorial_document') {
				remove_tutorial_document($id);
			} else if ($_GET['obj'] == 'material_test') {
				remove_material_test($id);
			} else if ($_GET['obj'] == 'material_test_solve') {
				remove_material_test_solve($id);
			}
			$data['success'] = true;
			
		} catch (Exception $e) {
			// throw $e;
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['create-materials'])) {
		try {

			$id = isset($_POST['id']) ? $_POST['id'] : 0;
			$obj = $_POST['obj'];
			$title = $_POST['title'];

			if ($obj == 'subtopic') {
				$order = get_subtopic_order_by_topic_id($id);
				$query = "INSERT INTO subtopic (topic_id, title, subtopic_order) VALUES (:topic_id, :title, :subtopic_order)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':topic_id', $id, PDO::PARAM_INT);
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->bindParam(':subtopic_order', $order, PDO::PARAM_INT);
				$stmt->execute();

				$data['success'] = true;
			} else if ($obj == 'topic') {
				$order = get_topic_order_by_subject_id($id);
				$query = "INSERT INTO topic (subject_id, title, topic_order) VALUES (:subject_id, :title, :topic_order)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subject_id', $id, PDO::PARAM_INT);
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->bindParam(':topic_order', $order, PDO::PARAM_INT);
				$stmt->execute();

				$data['success'] = true;
			} else if ($obj == 'subject') {
				$stmt = $connect->prepare("INSERT INTO subject (title) VALUES (:title)");
				$stmt->bindParam(":title", $title, PDO::PARAM_STR);
				$stmt->execute();

				$data['success'] = true;
			}

		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['edit-material-config'])) {
		try {

			$subtopic_id = $_POST['subtopic_id'];
			$type = $_POST['type'];
			$is_checked = $_POST['is_checked'];

			$perceive = '';
			if ($type == 'tutorial-video') {
				$perceive = 'tutorial_video';
			} else if ($type == 'tutorial-document') {
				$perceive = 'tutorial_document';
			} else if ($type == 'end-video') {
				$perceive = 'end_video';
			}

			if ($is_checked == 'true') {
				$stmt = $connect->prepare("INSERT INTO subtopic_material_config (subtopic_id, perceive) VALUES (:subtopic_id, :perceive)");
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':perceive', $perceive, PDO::PARAM_STR);
				$stmt->execute();
			} else {
				$stmt = $connect->prepare("DELETE FROM subtopic_material_config WHERE subtopic_id = :subtopic_id AND perceive = :perceive");
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':perceive', $perceive, PDO::PARAM_STR);
				$stmt->execute();
			}

			$data['success'] = true;
			$data['type'] = $type;
			$data['perceive'] = $perceive;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['add-time-code'])) {

		try {
			$end_video_id = $_POST['end-video-id'];
			$timecode = $_POST['timecode'];
			$title = $_POST['title'];

			$stmt = $connect->prepare("INSERT INTO end_video_timecode (end_video_id, timecode, title) VALUES (:end_video_id, :timecode, :title)");
			$stmt->bindParam(':end_video_id', $end_video_id, PDO::PARAM_INT);
			$stmt->bindParam(':timecode', $timecode, PDO::PARAM_STR);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['remove-timecode'])) {
		try {
			$id = $_GET['id'];

			$stmt = $connect->prepare("DELETE FROM end_video_timecode WHERE id = :id");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['add-test'])) {
		try {
			$title = $_POST['title'];
			$file = $_FILES['document'];
			$subtopic_id = $_POST['subtopic'];
			if ($file['size'] <= 10410760) {
				$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				$target_file = '/material_tests/'.time().'_'.generateRandomString().'.'.$ext;
				$test_order = get_material_test_order_by_subtopic_id($subtopic_id);

				$stmt = $connect->prepare("INSERT INTO material_test (subtopic_id, link, title, test_order) 
																VALUES (:subtopic_id, :link, :title, :test_order)");
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':link', $target_file, PDO::PARAM_STR);
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->bindParam(':test_order', $test_order, PDO::PARAM_INT);
				$stmt->execute();

				move_uploaded_file($_FILES['document']['tmp_name'], $root.$target_file);

				$data['success'] = true;
			} else {
				$data['file_size'] = $file['size'];
				$data['success'] = false;
			}
			
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);

	} else if (isset($_GET['add-test-solve'])) {
		try {
		
			$title = $_POST['title'];
			$file = $_FILES['document'];
			$subtopic_id = $_POST['subtopic'];

			if ($file['size'] <= 10410760) {
				$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				$target_file = '/material_test_solve/'.time().'_'.generateRandomString().'.'.$ext;
				$file_order = get_material_test_solve_order_by_subtopic_id($subtopic_id);

				$stmt = $connect->prepare("INSERT INTO material_test_solve (subtopic_id, link, title, file_order)
																	VALUES (:subtopic_id, :link, :title, :file_order)");
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->bindParam(':link', $target_file, PDO::PARAM_STR);
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->bindParam(':file_order', $file_order, PDO::PARAM_INT);
				$stmt->execute();

				move_uploaded_file($_FILES['document']['tmp_name'], $root.$target_file);

				$data['success'] = true;
			} else {
				$data['file_size'] = $file['size'];
				$data['success'] = false;
			}

		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['add_test_prefixes'])) {
		try {
			$ans_ids = isset($_POST['ans_id']) ? $_POST['ans_id'] : array();
			$prefixes = isset($_POST['prefix']) ? $_POST['prefix'] : array();
			$torfs = isset($_POST['ans_radio']) ? $_POST['ans_radio'] : array();
			// $material_test_id = $_POST['material_test_id'];
			$subtopic_id = $_POST['subtopic_id'];
			$numeration = $_POST['numeration'];
		
			remove_unneccessary_answers($ans_ids, $numeration, $subtopic_id);
			if (count($ans_ids) > 0) {
				update_answers($ans_ids, $prefixes, $torfs);
				insert_answers($ans_ids, $prefixes, $torfs, $numeration, $subtopic_id);
			}
			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['set_pop_up_on'])) {
		try {
			$tva_id = $_GET['tva_id'];
			$subtopic_id = $_GET['subtopic_id'];

			$query = "UPDATE tutorial_video SET pop_up = 0 WHERE subtopic_id = :subtopic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "UPDATE tutorial_video SET pop_up = 1 WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $tva_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	} else if (isset($_GET['set_pop_up_off'])) {
		try {

			$tva_id = $_GET['tva_id'];

			$query = "UPDATE tutorial_video SET pop_up = 0 WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $tva_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
		} catch (Exception $e) {
			$data['success'] = false;
			$data['message'] = "ERROR: ".$e->getMessage()."!!!";
		}
		echo json_encode($data);
	}

	function remove_unneccessary_answers($ans_ids, $numeration, $subtopic_id) {
		GLOBAL $connect;

		try {

			$real_ans_ids = array();

			foreach ($ans_ids as $value) {
				if ($value != 'new') {
					array_push($real_ans_ids, $value);
				}
			}

			$query = "DELETE FROM answers WHERE subtopic_id = :subtopic_id AND numeration = :numeration AND id NOT IN ('".implode("','", $real_ans_ids)."')";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function update_answers($ans_ids, $prefixes, $torfs) {
		GLOBAL $connect;

		try {

			$query = "UPDATE answers SET prefix = :prefix, torf = :torf WHERE id = :ans_id";

			foreach ($ans_ids as $key => $value) {
				if ($value != 'new') {
					$ans_id = $value;
					$prefix = $prefixes[$key];
					$torf = (isset($torfs[$key]) && $torfs[$key] == 'on') ? 1 :0;
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
					$stmt->bindParam(':torf', $torf, PDO::PARAM_INT);
					$stmt->bindParam(':ans_id', $ans_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_answers($ans_ids, $prefixes, $torfs, $numeration, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO answers (numeration, prefix, torf, subtopic_id)
									VALUES(:numeration, :prefix, :torf, :subtopic_id)";
			foreach ($ans_ids as $key => $value) {
				if ($value == 'new') {
					$prefix = $prefixes[$key];
					$torf = (isset($torfs[$key]) && $torfs[$key] == 'on') ? 1 : 0;
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
					$stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
					$stmt->bindParam(':torf', $torf, PDO::PARAM_INT);
					$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_subject($subject_id) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM subject WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_topic($topic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT t.subject_id FROM topic t WHERE t.id = :id");
			$stmt->bindParam(':id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$subject_id = $stmt->fetch(PDO::FETCH_ASSOC)['subject_id'];

			$query = "DELETE FROM topic WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();

			$set_order = "SET @position:=0;
								UPDATE topic
								SET topic_order=@position:=@position+1 
								WHERE subject_id=:id
								ORDER BY topic_order";
			$stmt = $connect->prepare($set_order);
			$stmt->bindParam(':id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_subtopic($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT st.topic_id FROM subtopic st WHERE st.id = :id");
			$stmt->bindParam(':id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$topic_id = $stmt->fetch(PDO::FETCH_ASSOC)['topic_id'];

			$query = "DELETE FROM subtopic WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			$set_order = "SET @position:=0;
								UPDATE subtopic
								SET subtopic_order=@position:=@position+1 
								WHERE topic_id=:id
								ORDER BY subtopic_order";
			$stmt = $connect->prepare($set_order);
			$stmt->bindParam(':id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tutorial_video($tutorial_video_id) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_video WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $tutorial_video_id, PDO::PARAM_INT);
			$stmt->execute();
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tutorial_document($tutorial_document_id) {
		GLOBAL $connect;

		try {

			if (remove_tutorial_document_file($tutorial_document_id)) {
				$query = "DELETE FROM tutorial_document WHERE id = :id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $tutorial_document_id, PDO::PARAM_INT);
				$stmt->execute();
				return true;
			}
			return false;
			
		} catch (Exception $e) {
			return false;
			// throw $e;
		}
	}

	function remove_material_test($material_test_id) {
		GLOBAL $connect;

		try {
			remove_material_test_file($material_test_id);
			$query = "DELETE FROM material_test WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $material_test_id, PDO::PARAM_INT);
			$stmt->execute();

			return true;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_material_test_solve($material_test_solve_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT mts.subtopic_id FROM material_test_solve mts WHERE mts.id = :id");
			$stmt->bindParam(':id', $material_test_solve_id, PDO::PARAM_INT);
			$stmt->execute();
			$subtopic_id = $stmt->fetch(PDO::FETCH_ASSOC)['subtopic_id'];

			remove_material_test_solve_file($material_test_solve_id);
			$query = "DELETE FROM material_test_solve WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $material_test_solve_id, PDO::PARAM_INT);
			$stmt->execute();

			$set_order = "SET @position:=0;
							UPDATE material_test_solve
							SET file_order=@position:=@position+1
							WHERE subtopic_id=:id
							ORDER BY file_order";
			$stmt = $connect->prepare($set_order);
			$stmt->bindParam(':id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_end_video($end_video_id) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM end_video_timecode WHERE end_video_id = :end_video_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":end_video_id", $id, PDO::PARAM_INT);
			$stmt->execute();

			$query = "DELETE FROM end_video WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $end_video_id, PDO::PARAM_INT);
			$stmt->execute();
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function get_video_order_by_subtopic_id($table_name, $subtopic_id) {

		GLOBAL $connect;

		$query = "SELECT video_order
					FROM ".$table_name."
					WHERE subtopic_id = :subtopic_id
					ORDER BY video_order DESC
					LIMIT 1";

		try {
			
			$stmt = $connect->prepare($query);
			$stmt->bindParam(":subtopic_id", $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$order = $stmt->fetch(PDO::FETCH_ASSOC)['video_order'];

			return $order != '' ? intval($order) + 1 : 1;

		} catch (Exception $e) {
			return "Error : ".$e->getMessage()." !!!".$query."!!!";
		}
	}

	function get_document_order_by_subtopic_id($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT document_order
										FROM tutorial_document
										WHERE subtopic_id = :subtopic_id
										ORDER BY document_order DESC
										LIMIT 1");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();
			$order = $stmt->fetch(PDO::FETCH_ASSOC)['document_order'];

			return $order != '' ? intval($order) + 1 : 1;			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subtopic_order_by_topic_id($topic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT subtopic_order
										FROM subtopic
										WHERE topic_id = :topic_id
										ORDER BY subtopic_order DESC
										LIMIT 1");
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$order = $stmt->fetch(PDO::FETCH_ASSOC)['subtopic_order'];

			return $order != '' ? intval($order) + 1 : 1;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_topic_order_by_subject_id($subject_id) {

		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT topic_order
										FROM topic 
										WHERE subject_id = :subject_id
										ORDER BY topic_order DESC
										LIMIT 1");
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$order = $stmt->fetch(PDO::FETCH_ASSOC)['topic_order'];

			return $order != '' ? intval($order) + 1 : 1;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_order_by_subtopic_id($subtopic_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT test_order
										FROM material_test
										WHERE subtopic_id = :subtopic_id
										ORDER BY test_order DESC
										LIMIT 1");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			$order = $stmt->fetch(PDO::FETCH_ASSOC)['test_order'];

			return $order != '' ? intval($order) + 1 : 1;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_material_test_solve_order_by_subtopic_id ($subtopic_id) {
		GLOBAL $connect;

		try {
			$stmt = $connect->prepare("SELECT file_order
										FROM material_test_solve
										WHERE subtopic_id = :subtopic_id
										ORDER BY file_order DESC
										LIMIT 1");
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->execute();

			$order = $stmt->fetch(PDO::FETCH_ASSOC)['file_order'];

			return $order != '' ? intval($order) + 1 : 1;

		} catch (Exception $e) {
			throw $e;	
		}
	}

	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	function remove_tutorial_document_file($id) {
		GLOBAL $connect; 
		GLOBAL $root;
		try {

			$stmt = $connect->prepare("SELECT link
										FROM tutorial_document
										WHERE id = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$link = $stmt->fetch(PDO::FETCH_ASSOC)['link'];
			if (file_exists($root.$link)) {
				unlink($root.$link);
				return true;
			}
			return false;
			
		} catch (Exception $e) {
			return false;
		}
	}

	function remove_material_test_file($id) {
		GLOBAL $connect;
		GLOBAL $root;

		try {
			$stmt = $connect->prepare("SELECT link
										FROM material_test
										WHERE id = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$link = $stmt->fetch(PDO::FETCH_ASSOC)['link'];

			if (file_exists($root.$link)) {
				unlink($root.$link);
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_material_test_solve_file ($id) {
		GLOBAL $connect;
		GLOBAL $root;

		try {

			$stmt = $connect->prepare("SELECT link
										FROM material_test_solve
										WHERE id = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$link = $stmt->fetch(PDO::FETCH_ASSOC)['link'];

			if (file_exists($root.$link)) {
				unlink($root.$link);
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>