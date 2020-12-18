<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();

    $data = array();  

    if (isset($_GET['create-material-link'])) {

    	try {
    		$comment = $_POST['comment'];
    		$access_hours = $_POST['access_hours'];
    		$subtopics_and_materials_type = json_decode($_POST['datas'], true);
    		$data['before decode'] = $_POST['datas'];

    		$access_until = date('Y-m-d H:i:s', strtotime('+'.$access_hours.' hours'));
    		$subtopics = array();
    		foreach ($subtopics_and_materials_type as $value) {
    			array_push($subtopics, $value['id']);
    		}
    		$code = md5($access_until.$access_hours.implode(',', $subtopics));
    		$query = "INSERT INTO material_link (code, access_until, comment)
    										VALUES(:code, :access_until, :comment)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':code', $code, PDO::PARAM_STR);
    		$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
    		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    		$stmt->execute();
    		$material_link_id = $connect->lastInsertId();

    		$material_link_content_query = "INSERT INTO material_link_content (type, subtopic_id, material_link_id)
    																	VALUES (:type, :subtopic_id, :material_link_id)";
    		foreach ($subtopics_and_materials_type as $value) {
    			if ($value['tv']) {
    				$stmt = $connect->prepare($material_link_content_query);
    				$stmt->bindValue(':type', 'tutorial_video', PDO::PARAM_STR);
    				$stmt->bindParam(':subtopic_id', $value['id'], PDO::PARAM_INT);
    				$stmt->bindParam(':material_link_id', $material_link_id, PDO::PARAM_INT);
    				$stmt->execute();
    			}
    			if ($value['td']) {
    				$stmt = $connect->prepare($material_link_content_query);
    				$stmt->bindValue(':type', 'tutorial_document', PDO::PARAM_STR);
    				$stmt->bindParam(':subtopic_id', $value['id'], PDO::PARAM_INT);
    				$stmt->bindParam(':material_link_id', $material_link_id, PDO::PARAM_INT);
    				$stmt->execute();
    			}
    			if ($value['ev']) {
    				$stmt = $connect->prepare($material_link_content_query);
    				$stmt->bindValue(':type', 'end_video', PDO::PARAM_STR);
    				$stmt->bindParam(':subtopic_id', $value['id'], PDO::PARAM_INT);
    				$stmt->bindParam(':material_link_id', $material_link_id, PDO::PARAM_INT);
    				$stmt->execute();
    			}
                if ($value['mt']) {
                    $stmt = $connect->prepare($material_link_content_query);
                    $stmt->bindValue(':type', 'material_test', PDO::PARAM_STR);
                    $stmt->bindParam(':subtopic_id', $value['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':material_link_id', $material_link_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
    		}

    		$data['success'] = true;
    		$data['data'] = $subtopics_and_materials_type;
			
		} catch (Exception $e) {
			// throw $e;
			$data['success'] = false;
			$data['message'] = "Error : ".$e->getMessage()." !!!";
		}
		echo json_encode($data);
    }
?>