<?php
	$tmp_name = selectTmpName('video');
	$name = uniqid('Video', true);
	if($name != null){
		$allow = renameFolder('video',$name);
		if($allow){
			rename('httpdocs'.$tmp_name, $name);
			echo "Folder name changed from (".$tmp_name.") to (".$name.")";
		}
	}
	function renameFolder($folder_name,$name){
		$return = false;
		try {
			include('connection.php');
			$stmt = $conn->prepare("UPDATE rename_folder SET tmp_name = :tmp_name WHERE folder_name = :folder_name");
		    $stmt->bindParam(':tmp_name', $name, PDO::PARAM_STR);
		    $stmt->bindParam(':folder_name', $folder_name, PDO::PARAM_INT);
		    $stmt->execute();
		    $return = true;
		} catch (PDOException $e) {
			$return = false;
			echo "Error : ".$e->getMessage()." !!!";
		}
		return $return;
	}
	function selectTmpName($folder_name){
		$return = null;
		try {
			include('connection.php');
			$stmt = $conn->prepare("SELECT * FROM rename_folder WHERE folder_name = :folder_name");
			$stmt->bindParam(':folder_name', $folder_name, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$return = $result['tmp_name'];
		} catch (PDOException $e) {
			$return = null;
			echo "Error : ".$e->getMessage()." !!!";
		}
		return $return;
	}
?>