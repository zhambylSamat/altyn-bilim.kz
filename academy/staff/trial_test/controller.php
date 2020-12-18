<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/global_controller.php');

	if (isset($_GET['add_new_trial_test'])) {
		try {

			$subject_id = $_POST['subject_id'];
			$trial_test_title = $_POST['trial_test_title'];

			$query = "INSERT INTO trial_test (subject_id, title) VALUES (:subject_id, :title)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $trial_test_title, PDO::PARAM_STR);
			$stmt->execute();

			$trial_test_id = $connect->lastInsertId();

			for ($i = 0; $i < 20; $i++) {
				insert_new_answer($trial_test_id);
			}

			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
	} else if (isset($_GET['edit_trial_test_title'])) {
		try {
			$title = $_POST['trial-test-title'];
			$trial_test_id = $_POST['trial-test-id'];

			$query = "UPDATE trial_test SET title = :title WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $trial_test_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->execute();

			$data['data']['trial_test_title'] = $title;
			$data['data']['trial_test_id'] = $trial_test_id;
			$data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
	} else if (isset($_GET['upload_trial_test_img'])) {
		try {

			$file = $_FILES['trial_test_img'];
			$trial_test_id = $_POST['trial_test_id'];

			if ($file['error'] == UPLOAD_ERR_OK) {
				$allowed_extentions = array('jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG');
                $filename = $file['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed_extentions)) {
                	$query = "SELECT ttf.file_order
                				FROM trial_test_file ttf
                				WHERE ttf.trial_test_id = :trial_test_id
                				ORDER BY ttf.file_order DESC
                				LIMIT 1";
                	$stmt = $connect->prepare($query);
                	$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
                	$stmt->execute();
                	$row_count = $stmt->rowCount();
                	if ($row_count == 0) {
                		$file_order = 1;
                	} else {
                		$file_order = $stmt->fetch(PDO::FETCH_ASSOC)['file_order'] + 1;
                	}

                	$target_file = '/trial_test_img/'.time().'_'.generate_string($permitted_chars, 10).'.'.$ext;

                	if (move_uploaded_file($file['tmp_name'], $root.$target_file)) {
                		$query = "INSERT INTO trial_test_file (trial_test_id, file_link, file_order) 
                										VALUES (:trial_test_id, :file_link, :file_order)";
                		$stmt = $connect->prepare($query);
                		$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
                		$stmt->bindParam(':file_link', $target_file, PDO::PARAM_STR);
                		$stmt->bindParam(':file_order', $file_order, PDO::PARAM_INT);
                		$stmt->execute();

                		$trial_test_file_id = $connect->lastInsertId();

                		$data['data'] = array('trial_test_file_id' => $trial_test_file_id,
                								'trial_test_file_link' => $ab_root.'/academy/'.$target_file,
                								'trial_test_file_order' => $file_order);
                		$data['success'] = true;
                	}

                } else {
                    $data['success'] = false;
                    $data['message'] = 'Жүктелген суреттің типі "jpeg", "jpg", "png" болу керек.';
                }

			} else if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
				$data['message'] = 'Жүктелген суреттің салмағы 5 мб (мега бит) тан көп болмауы керек';
                $data['success'] = false;
			} else if ($file['error'] == UPLOAD_ERR_NO_FILE) {
				$data['message'] = 'Сурет жүктелмеді. Қайталап көріңіз';
                $data['success'] = false;
			}
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['remove-trial-test-file'])) {
		try {

			$trial_test_file_id = $_GET['trial_test_file_id'];

			$query = "SELECT ttf.file_link,
							ttf.trial_test_id
						FROM trial_test_file ttf
						WHERE ttf.id = :trial_test_file_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_file_id', $trial_test_file_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$file_link = $query_result['file_link'];
			$trial_test_id = $query_result['trial_test_id'];

			if (file_exists($root.$file_link)) {
				unlink($root.$file_link);
			}

			$query = "DELETE FROM trial_test_file WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $trial_test_file_id, PDO::PARAM_INT);
			$stmt->execute();

			fix_trial_test_file_order($trial_test_id);

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['change-trial-test-img-order'])) {
		try {

			$trial_test_file_ids = json_decode($_POST['trial_test_file_ids'], true);

			$query = "UPDATE trial_test_file SET file_order = :file_order WHERE id = :id";

			foreach ($trial_test_file_ids as $order => $trial_test_file_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':file_order', $order, PDO::PARAM_INT);
				$stmt->bindParam(':id', $trial_test_file_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['add_new_answer'])) {
		try {

			$trial_test_id = $_GET['trial_test_id'];

			$answer_info = insert_new_answer($trial_test_id);

			$data['values'] = $answer_info['values'];
			$data['numeration'] = $answer_info['numeration'];
			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['add_new_prefix'])) {
		try {
			$trial_test_id = $_GET['trial_test_id'];
			$numeration = $_GET['numeration'];

			$available_prefixes = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

			$query = "SELECT tta.numeration,
							tta.prefix
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
							AND tta.numeration = :numeration
						ORDER BY tta.prefix DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$prefix = 'A';
			} else {
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$last_prefix = $query_result['prefix'];
				$last_prefix_index = array_search($last_prefix, $available_prefixes);

				if ($last_prefix_index < count($available_prefixes) - 1) {
					$prefix = $available_prefixes[$last_prefix_index + 1];
				} else {
					$prefix = "";
				}
			}

			if ($prefix != "") {
				$query = "INSERT INTO trial_test_answer (trial_test_id, numeration, prefix) VALUES (:trial_test_id, :numeration, :prefix)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
				$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
				$stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
				$stmt->execute();
			}

			$query = "SELECT tta.id,
							tta.prefix,
							tta.torf
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
							AND tta.numeration = :numeration
						ORDER BY tta.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$data['exists'] = false;
			} else {
				$query_result = $stmt->fetchAll();

				$result = array();
				foreach ($query_result as $value) {
					$result[$value['prefix']] = array('trial_test_answer_id' => $value['id'],
													'torf' => $value['torf']);
				}

				$data['numeration'] = $numeration;
				$data['values'] = $result;
				$data['exists'] = true;
			}
			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['remove_last_answer'])) {
		try {

			$trial_test_answer_id = $_GET['trial_test_answer_id'];
			$trial_test_id = $_GET['trial_test_id'];
			$numeration = $_GET['numeration'];

			$query = "DELETE FROM trial_test_answer WHERE id = :id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':id', $trial_test_answer_id, PDO::PARAM_INT);
			$stmt->execute();


			$query = "SELECT tta.id,
							tta.prefix,
							tta.torf
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
							AND tta.numeration = :numeration
						ORDER BY tta.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$data['exists'] = false;
				fix_trial_test_answer_order($trial_test_id);
			} else {
				$query_result = $stmt->fetchAll();

				$result = array();
				foreach ($query_result as $value) {
					$result[$value['prefix']] = array('trial_test_answer_id' => $value['id'],
													'torf' => $value['torf']);
				}

				$data['numeration'] = $numeration;
				$data['values'] = $result;
				$data['exists'] = true;
			}
			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['remove_answer'])) {
		try {
			$trial_test_id = $_GET['trial_test_id'];
			$numeration = $_GET['numeration'];

			$query = "DELETE FROM trial_test_answer WHERE trial_test_id = :trial_test_id AND numeration = :numeration";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
			$stmt->execute();

			fix_trial_test_answer_order($trial_test_id);

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['set_true_ans'])) {
		try {
			$trial_test_answer_id = $_GET['trial_test_answer_id'];

			$query = "UPDATE trial_test_answer SET torf = 1 WHERE id = :trial_test_answer_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_answer_id', $trial_test_answer_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['unset_true_ans'])) {
		try {

			$trial_test_answer_id = $_GET['trial_test_answer_id'];

			$query = "UPDATE trial_test_answer SET torf = 0 WHERE id = :trial_test_answer_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_answer_id', $trial_test_answer_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	} else if (isset($_GET['remove_trial_test'])) {
		try {
			$trial_test_id = $_GET['trial_test_id'];

			$query = "DELETE FROM trial_test WHERE id = :trial_test_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();

			$data['success'] = true;
			
		} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = 'ERROR: '.$e->getMessage().'!!!';
        }
        echo json_encode($data);
	}

	function fix_trial_test_answer_order ($trial_test_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tta.id,
							tta.numeration
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
						ORDER BY tta.numeration, tta.prefix";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count > 0) {
				$query_result = $stmt->fetchAll();

				$query = "UPDATE trial_test_answer SET numeration = :numeration WHERE id = :id";

				$numeration = 0;
				$old_numeration = 0;
				foreach ($query_result as $value) {
					if ($old_numeration != $value['numeration']) {
						$old_numeration = $value['numeration'];
						$numeration++;
					}

					$stmt = $connect->prepare($query);
					$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
					$stmt->bindParam(':id', $value['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}


	function fix_trial_test_file_order ($trial_test_id) {
		GLOBAL $connect;

		try {

			$set_order = "SET @position:=0;
								UPDATE trial_test_file
								SET file_order=@position:=@position+1 
								WHERE trial_test_id=:id
								ORDER BY file_order";
			$stmt = $connect->prepare($set_order);
			$stmt->bindParam(':id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_new_answer ($trial_test_id) {
		GLOBAL $connect;

		try {

			$default_prefixes = array('A', 'B', 'C', 'D', 'E');
			$default_full_prefixes = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

			$result = array();

			$query = "SELECT tta.numeration
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
						ORDER BY tta.numeration DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$row_count = $stmt->rowCount();

			if ($row_count == 0) {
				$numeration = 1;
			} else {
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$numeration = intval($query_result['numeration']) + 1;
			}

			$result['numeration'] = $numeration;
			$result['values'] = array();
			$query = "INSERT INTO trial_test_answer (trial_test_id, numeration, prefix) VALUES (:trial_test_id, :numeration, :prefix)";

			$prefixes = $default_prefixes;
			if ($numeration >= 21) {
				$prefixes = $default_full_prefixes;
			}
			foreach ($prefixes as $prefix) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
				$stmt->bindParam(':numeration', $numeration, PDO::PARAM_INT);
				$stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
				$stmt->execute();
				$trial_test_answer_id = $connect->lastInsertId();
				$result['values'][$prefix] = array('trial_test_answer_id' => $trial_test_answer_id,
													'torf' => 0);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>