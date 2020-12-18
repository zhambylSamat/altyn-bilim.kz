<?php
$includeBool = false;
$error = array();
$data = array();
$data['script'] = "";
$data['text'] = "";
$data['test'] = "";
$data['success'] = false;
$question_img = '';

if(isset($_GET['new_question'])){
	if($_POST['question-txt']!= '' ||  $_FILES['question-img']['name']!=''){
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
if(isset($_POST['delete_question']) && $_POST['delete_question'] = 'delete_question' && isset($_POST['question_num'])){
	deleteQuestion($_POST['question_num']);
	deleteAnswer($_POST['question_num']);
	$obj = requireToVar('ajax_adminTest.php');
	$data['text'] = $obj;
	$data['success']=true;
	echo json_encode($data);

}
function checkFile($photo_path, $file, $tmp_name, $filesize){
	global $data;
	$file_test = false;
	$img_corr = 'false';
    $imageFileType = pathinfo($photo_path,PATHINFO_EXTENSION);
	if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "JPG" || $imageFileType == "PNG" || $imageFileType == "JPEG" || $imageFileType == "GIF"){
		if($filesize>61450){
			$data['script'] .= "<script type='text/javascript'> alert('Ошибка! Максимальный размер изображении 60КБ ~ (61 440 байт)'); </script>";
		}
        else if(!file_exists($photo_path)) {
        	$data['test'] .= $tmp_name;
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
	if(isset($_POST['hidden_question_num'])){
		updateQuestion($_POST['hidden_question_num'],$_POST['question_txt'],$question_img);
	}
	$photo_path = '../img/test/';
	$answer_txt = $_POST['answer'];
	$answer_img = $_FILES['answer_img']['name'];
	$answer_img_tmp_name = $_FILES['answer_img']['tmp_name'];
	$answer_img_file_size = $_FILES['answer_img']['size'];
	$answer_torf = array();
	$answer_torf_array = $_POST['torf'];
	$number_of_answers = $_POST['number_of_answers'];
	$answer_error = false;
	global $data;
	for($i=2; $i<$number_of_answers+2; $i++){
		$answer_torf[$i-2] = isset($answer_torf_array[$i-2]) ? $answer_torf_array[$i-2] : "0";
		// echo $torf." - ".$answer_txt[$i];
		if($answer_txt[$i-2]=='' && $answer_img[$i-2]==''){
			$answer_error = true;
			$data['script'] .= "<script type='text/javascript'> window['emptyAnswer'](".$i."); </script>";
			// $data['script'] .= 
		}	
		else if($answer_txt[$i-2]!='' || $answer_img[$i-2]!='') {
			$data['script'] .= "<script type='text/javascript'> window['notEmptyAnswer'](".$i."); </script>";
		}
	}
	
	$torf = true;
	for($i=0; $i<$number_of_answers; $i++){
		$photo_path = '../img/test/';
		$photo_path = $photo_path.basename($answer_img[$i]);
		if($answer_img[$i]!=''){
			if(!checkFile($photo_path, $answer_img[$i], $answer_img_tmp_name[$i], $answer_img_file_size[$i])){
				$torf = false;
				$data['test'] = 'testing';
				echo json_encode($data);
				$answer_error = true;
				break;
			}
		}
	}
	if($answer_error && $torf){
		echo json_encode($data);
	}
	try {
		if(!$answer_error){
			include("../connection.php");
			$question_num = '';
			if(isset($_POST['hidden_question_num'])){
				deleteAnswer($_POST['hidden_question_num']);
				$question_num = $_POST['hidden_question_num'];
			}
			else{
				$stmtT = $conn->prepare("SELECT test_num FROM test WHERE subtopic_num = :subtopic_num");
				$stmtT->bindParam(':subtopic_num', $subtopic_num, PDO::PARAM_STR);
				$subtopic_num = $_GET['elementNum'];
				$stmtT->execute();
				$resultT = $stmtT->fetch(PDO::FETCH_ASSOC);

				$stmtQ = $conn->prepare("INSERT INTO question (question_num, test_num, question_text, question_img) VALUES(:question_num, :test_num, :question_text, :question_img)");
	   
			    $stmtQ->bindParam(':question_num', $question_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':test_num', $test_num, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_text', $question_txt, PDO::PARAM_STR);
			    $stmtQ->bindParam(':question_img', $question_img, PDO::PARAM_STR);

			    $question_num = str_replace('.','',uniqid('Q', true));
			    $test_num = $resultT['test_num'];
			    $question_txt = $_POST['question-txt'];
				// $question_img = $_FILES['question-img']['name'];

			    $stmtQ->execute();
			}

		    $query = "INSERT INTO answer (answer_num, question_num, answer_text, answer_img, torf) VALUES";
		    $qPart = array_fill(0, count($answer_txt), "(?, ?, ?, ?, ?)");
		    $query .= implode(",",$qPart);
		    $stmtA = $conn->prepare($query);
		    $j = 1;
		    for($i = 0; $i<count($answer_txt); $i++){
		    	$answer_num = str_replace('.','',uniqid('A', true));
		    	$stmtA->bindValue($j++, $answer_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $question_num, PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_txt[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_img[$i], PDO::PARAM_STR);
		    	$stmtA->bindValue($j++, $answer_torf[$i], PDO::PARAM_STR);
		    }
		    $stmtA->execute();
		    $obj = requireToVar('ajax_adminTest.php');
			$data['text'] = $obj;
			$data['success']=true;
			echo json_encode($data);
		}
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
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
function requireToVar($file){
    ob_start();
    require($file);
    return ob_get_clean();
}
function updateQuestion($question_num,$question_txt,$question_img){
	try {
		$stmt = $conn->prepare("UPDATE question SET question_text = :question_text, question_img = :question_img WHERE question_num = :question_num");
   
	    $stmt->bindParam(':question_num', $question_num, PDO::PARAM_STR);
	    $stmt->bindParam(':question_img', $question_img, PDO::PARAM_INT);
	    $stmt->bindParam(':question_text', $question_txt, PDO::PARAM_INT);
	       
	    $stmt->execute();
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>