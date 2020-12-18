<?php
if(isset($_POST['new_question'])){
	if($_POST['question-txt']!= '' ||  $_POST['question-img']!=''){
		echo "string";
		addToDB();
	}
}
function addToDB(){
	$answer_txt = explode(",",$_POST['answer-txt']);
	$answer_img = explode(",",$_POST['answer-img']);
	$answer_torf = explode(",",$_POST['answer-torf']);
	$answer_empty = true;
	for($i=count($answer_txt)-1; $i>=0; $i--){
		if($answer_txt[$i]=='' && $answer_img[$i]==''){
			unset($answer_txt[$i]);
			unset($answer_img[$i]);
			unset($answer_torf[$i]);
		}	
		else if($answer_txt[$i]!='' || $answer_img[$i]!='') {
			$answer_empty = false;
		}
	}
	try {
		if(!$answer_empty){
			include("../connection.php");
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
			$question_img = $_POST['question-img'];

		    $stmtQ->execute();

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
		}
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>