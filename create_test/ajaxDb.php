<?php
$includeBool = false;
$error = array();
$data = array();
$data['script'] = "";
$data['text'] = "";
$data['success'] = false;
$data['error'] = '';
$question_img = '';

if(isset($_GET['new_question'])){
	if((isset($_POST['question-txt']) && $_POST['question-txt']!= '') ||  (isset($_FILES['question-img']) && $_FILES['question-img']['name']!='') || (isset($_POST['question-img-hidden']) && $_POST['question-img-hidden']!='')){
		$data['script'] .= "<script type='text/javascript'> window['notEmptyQuestion'](); </script>";
		$photo_path = '../img/test/';
		$photo_path = $photo_path.basename($_FILES['question-img']['name']);
		$photo_torf = true;
		if($_FILES['question-img']['name']!=''){
			$question_img = $_FILES['question-img']['name'];
			$photo_torf = checkFile($photo_path, $_FILES['question-img']['name'], $_FILES['question-img']['tmp_name'], $_FILES['question-img']['size']);
		}
		else if(isset($_POST['question-img-hidden'])){
			$question_img = $_POST['question-img-hidden'];
		}
		if($photo_torf){
			if(isset($_POST['torf'])){
				$data['script'] .= "<script type='text/javascript'> window['notEmptyCheckbox'](); </script>";
				addToDB();
			}
			else{
				$data['script'] .= "<script type='text/javascript'> emptyCheckbox.call(); </script>";
				echo json_encode($data);
			}
		}
		else{
			echo json_encode($data);
		}
	}
	else{
		$data['script'] .= "<script type='text/javascript'> emptyQuestion.call(); </script>";
		echo json_encode($data);
	}	
}
else if(isset($_POST['delete_question']) && $_POST['delete_question'] = 'delete_question' && isset($_POST['question_num'])){
	deleteAnswer($_POST['question_num']);
	deleteQuestion($_POST['question_num']);
	// $obj = requireToVar('ajax_adminTest.php');
	$data['text'] = '';
	$data['success']=true;
	echo json_encode($data);
}

else if(isset($_GET['set_test_name'])){
	$test_name = $_POST['test_title'];
	$test_num = $_POST['test_num'];
	include("../connection.php");
	try {
		$stmt = $conn->prepare("UPDATE test SET name = :name WHERE test_num = :test_num");
		$stmt->bindParam(':test_num', $test_num, PDO::PARAM_STR);
		$stmt->bindParam(':name', $test_name, PDO::PARAM_STR);
		$stmt->execute();
		$data['success'] = true;
		echo json_encode($data);
	} catch(PDOException $e) {
		$data['error'] .= "Error: " . $e->getMessage();
        echo json_encode($data); 
    }
}









function checkFile($photo_path, $file, $tmp_name, $filesize){
	global $data;
	$file_test = false;
	$img_corr = 'false';
    $imageFileType = pathinfo($photo_path,PATHINFO_EXTENSION);
	if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "JPG" || $imageFileType == "PNG" || $imageFileType == "JPEG" || $imageFileType == "GIF"){
		if($filesize>307200){
			$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Максимальный размер изображении 300КБ ~ (307200 байт)'); </script>";
		}
        else if(!file_exists($photo_path)) {
        	// $data['test'] .= $tmp_name;
            if(move_uploaded_file($tmp_name, $photo_path)){
                $file_test = true;
            }
            else{
            	$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Картинка не загружено. Попробуйте еще раз!'); </script>";
            }
        }
        else{
        	$file_test = true;
        }
    }
    else{
    	$data['script'] .= "<script type='text/javascript'> alert('Не правильный формат картинки. Доступный форматы : \".jpg , .png , .jpeg , .gif\"'); </script>";
    }
    if($file_test){
    	return true;
    }
    else{ 
    	return false;
    }
    
}
function addToDB(){
	global $question_img;
	$photo_path = '../img/test/';
	$answer_txt = $_POST['answer'];
	$answer_img_file_size = array();
	$answer_torf = array();
	$answer_img = array();
	$answer_tmp_name = array();
	$answer_torf_array = $_POST['torf'];
	$number_of_answers = $_POST['number_of_answers'];
	$answer_error = false;
	global $data;
	$decrease = 0;
	$decrease_txt = 0;
	$decrease_img = 0;
	for($i=2; $i<$number_of_answers+2; $i++){
		if(isset($_POST['answer'][$i-2]) || isset($_FILES['answer_img']['name'][$i-2]) || isset($_POST['answer-img-hidden'][$i-2])){
			if(isset($_FILES['answer_img']['name'][$i-2]) && $_FILES['answer_img']['name'][$i-2]!=''){
				$answer_img[$decrease] = $_FILES['answer_img']['name'][$i-2];
				$answer_tmp_name[$decrease] = $_FILES['answer_img']['tmp_name'][$i-2];
				$answer_img_file_size[$decrease] = $_FILES['answer_img']['size'][$i-2];
			}
			else if (isset($_POST['answer-img-hidden'][$i-2]) && $_POST['answer-img-hidden'][$i-2]!=''){
				$answer_img[$decrease] = $_POST['answer-img-hidden'][$i-2];
				$answer_tmp_name[$decrease] = $_POST['answer-img-hidden'][$i-2];
			}
			else{
				$answer_img[$decrease] = '';
			}
		
			if($_POST['answer'][$i-2]=='' && $answer_img[$decrease]==''){
				$answer_error = true;
				$data['script'] .= "<script type='text/javascript'> window['emptyAnswer'](".$i."); </script>";
			}	
			else if($_POST['answer'][$i-2]!='' || $_FILES['answer_img']['name'][$i-2]!='') {
				$answer_txt[$decrease] = $_POST['answer'][$i-2];
				$data['script'] .= "<script type='text/javascript'> window['notEmptyAnswer'](".$i."); </script>";
			}
			$answer_torf[$decrease] = isset($answer_torf_array[$i-2]) ? $answer_torf_array[$i-2] : "0";
			$decrease++;
		}
	}
	$torf = true;
	for($i=0; $i<$decrease; $i++){
		$photo_path = '../img/test/';
		$photo_path = $photo_path.basename($answer_img[$i]);
		if($answer_img[$i]!='' && isset($answer_img_file_size[$i])){
			if(!checkFile($photo_path, $answer_img[$i], $answer_tmp_name[$i], $answer_img_file_size[$i])){
				$torf = false;
				$data['test'] = 'testing';
				echo json_encode($data);
				break;
			}
		}
	}
	if($answer_error){
		echo json_encode($data);
	}
	else if(!$torf){
		echo json_encode($data); 
	}
	try {
		if(!$answer_error){
			include("../connection.php");
			$question_num = '';
			if(isset($_POST['hidden_question_num'])){
				deleteAnswer($_POST['hidden_question_num']);
				$question_num = $_POST['hidden_question_num'];
				$text = isset($_POST['question-txt']) ? $_POST['question-txt'] : '';
				updateQuestion($_POST['hidden_question_num'],$text,$question_img);
			}
			else{
				$stmtT = $conn->prepare("SELECT test_num FROM test WHERE test_num = :test_num");
				$stmtT->bindParam(':test_num', $test_num, PDO::PARAM_STR);
				$test_num = $_GET['elementNum'];
				$stmtT->execute();
				$resultT = $stmtT->fetch(PDO::FETCH_ASSOC);

				$stmtQ = $conn->prepare("INSERT INTO question (question_num, test_num, question_text, question_img) VALUES(:question_num, :test_num, :question_text, :question_img)");
	   
			    $stmtQ->bindParam(':question_num', $question_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':test_num', $test_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_text', $question_txt, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_img', $question_img, PDO::PARAM_STR);
			    // preg_replace("/\n/", "<br />", $str)
			    // nl2br()


			    $question_num = uniqid('Q', true)."_".time();
			    $test_num = $resultT['test_num'];
			    // $question_txt = str_replace("\n", "<br>", $_POST['question-txt']);
			    // $question_txt =preg_replace("/\n/", '<br />', $_POST['question-txt']);
			    // $question_txt = nl2br($_POST['question-txt']);
			    $question_txt = $_POST['question-txt'];

			    $stmtQ->execute();
			}

		    $query = "INSERT INTO answer (answer_num, question_num, answer_text, answer_img, torf) VALUES";
		    $qPart = array_fill(0, $decrease, "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<$decrease; $i++){
		    	$answer_num = uniqid('A', true)."_".time();
		    	$stmtA->bindValue($j++, $answer_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $question_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_txt[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_img[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_torf[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		    // $obj = requireToVar('ajax_adminTest.php');
			$data['text'] = '';
			$data['success']=true;
			echo json_encode($data);
		}
	} catch(PDOException $e) {
		$data['error'] .= "Error: " . $e->getMessage();
        echo json_encode($data); 
    }
}
function deleteQuestion($question_num){
	try {
		include("../connection.php");
		$stmt = $conn->prepare("DELETE FROM question WHERE question_num = :question_num");

		$stmt->bindParam(':question_num',$question_num,PDO::PARAM_STR);

		$stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
function deleteAnswer($question_num){
	try {
		include("../connection.php");
		$stmt = $conn->prepare("DELETE FROM answer WHERE question_num = :question_num");

		$stmt->bindParam(':question_num',$question_num,PDO::PARAM_STR);

		$stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function updateQuestion($question_num,$question_txt,$question_img){
	try {
		include('../connection.php');
		$stmt = $conn->prepare("UPDATE question SET question_text = :question_text, question_img = :question_img WHERE question_num = :question_num");
   
	    $stmt->bindParam(':question_num', $question_num, PDO::PARAM_STR);
	    $stmt->bindParam(':question_img', $question_img, PDO::PARAM_STR);
	    $stmt->bindParam(':question_text', $question_txt, PDO::PARAM_STR);
	       
	    $stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>