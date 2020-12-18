<?php
	include_once('connection.php');
	include_once('emails.php');
	include_once(dirname(__FILE__).'/../send_sms/index.php');
	global $ENT_RESULT;

	$sql = "SELECT parse FROM config_ent WHERE id = 1";
	if (mysqli_query($conn, $sql)) {
	    echo "Record select successfully<br><br>";
	} else {
	    echo "Error updating record: " . mysqli_error($conn)."<br><br>";
	}
	$result_sql = mysqli_query($conn, $sql);
	$parse = mysqli_fetch_assoc($result_sql)['parse'];
	$email_order = array();
	
	if ($parse == '1') {
		$sql = "SELECT er.tzk,
					er.iin,
					er.phone
				FROM ent_result er
				WHERE er.has_result = 0";
		
		if (mysqli_query($conn, $sql)) {
		    echo "Record select successfully<br><br>";
		} else {
		    echo "Error updating record: " . mysqli_error($conn)."<br><br>";
		}
		$result_sql = mysqli_query($conn, $sql);
		$rowcount=mysqli_num_rows($result_sql);
		$result = array();
		$ch = curl_init('https://res.testcenter.kz/test-result/api/userdata/');
		while($value = mysqli_fetch_assoc($result_sql)){
			$tzk = $value['tzk'];
			$iin = $value['iin'];
			$test_type_id = "35";
			$langId = "1";

			$data = array(
						"idtc" => $tzk, 
						"iin" => $iin,
						"idTestType" => $test_type_id,
						"langId" => $langId
					);                                                                    
			$data_string = json_encode($data);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result_json = json_decode(curl_exec($ch), true);
			// echo "checked<br>".$iin." ".$tzk;
			// print_r($result_json);
			// echo "<br><br>";
			if (isset($result_json['userBallList']) && count($result_json['userBallList']) != 0) {
				$result[$tzk]['tzk'] = $result_json['idtc'];
				$result[$tzk]['iin'] = $result_json['iin'];
				$result[$tzk]['phone'] = $value['phone'];
				$result[$tzk]['fio'] = $result_json['fio'];
				$result[$tzk]['stage'] = $result_json['stageNameKz'];
				$result[$tzk]['univer'] = $result_json['vptNameKz'];
				$result[$tzk]['maxSumMark'] = $result_json['maxSumBall'];
				$result[$tzk]['sumMark'] = 0;
				$result[$tzk]['marks'] = array();
				foreach ($result_json['userBallList'] as $val) {
					$id = $val['subjectId'];
					$result[$tzk]['marks'][$id]['name'] = $val['subjectNameKz'];
					$result[$tzk]['marks'][$id]['mark'] = $val['ball'];
					$result[$tzk]['sumMark'] += $val['ball'];
				}
				print_r($result_json);
				echo "<br><br>";
			}
		}
		$sms_result = array();
		foreach ($result as $key_tzk => $value) {
			$result_str = json_encode($value, true);
			echo $result_str."<br><br>";
			$sql = "UPDATE ent_result SET has_result = 1, result = '".$result_str."', total_mark = ".$value['sumMark']." WHERE tzk = ".$key_tzk;
			if (mysqli_query($conn, $sql)) {
			    echo "Record updated successfully<br><br>";
			} else {
			    echo "Error updating record: " . mysqli_error($conn)."<br><br>";
			}

			$html = "<table>";
			$html .= "<tr><th>Барлығы:</th><td>".$value['sumMark']."/".$value['maxSumMark']."</td></tr>";
			foreach ($value['marks'] as $val) {
				$html .= "<tr><td>".$val['name']."</td><td>".$val['mark']."</td></tr>";
			}
			$html .= "</table>";

			$to = $super_admin_mail;
		    $subject = "ҰБТ қорытынды жауабы: ".$value['fio']." ".$value['sumMark'];
		    $headers = "MIME-Version: 1.0" . "\r\n";
		    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		        // More headers
		    $headers .= 'From: system@altyn-bilim.kz' . "\r\n";
		    $headers .= 'Bcc: '.$developer_mail. "\r\n";
		    if ($rowcount > 0) {
		    	$tmp = array("to" => $to,
		    				"subject" => $subject,
		    				"content" => $html,
		    				"headers" => $headers);
		    	array_push($email_order, $tmp);
		    	// send_email($to,$subject,$html,$headers);
		    }
		    $subject_and_marks = array();
		    foreach ($value['marks'] as $v) {
		    	$tmp = $v['name'].": ".$v['mark'];
		    	array_push($subject_and_marks, $tmp);
		    }
		    $data = array(
		    	$RECIPIENT => "7".$value['phone'],
				$TEXT => kiril2latin(sprintf($ENT_RESULT,
											$value['fio'],
											$value['sumMark'],
											$subject_and_marks[0],
											$subject_and_marks[1],
											$subject_and_marks[2],
											$subject_and_marks[3],
											$subject_and_marks[4]))
		    );
		    $IS_AUTO_SEND = true;
		    $res = send_sms($data, $RECIPIENT_TYPE_S, $value['fio']);
			array_push($sms_result, $res['manual_sms_response']);
		}
		$IS_AUTO_SEND = true;
		$save_sms_res = save_sms_by_cron($sms_result);
	    foreach ($email_order as $v) {
	    	send_email($v['to'], $v['subject'], $v['content'], $v['headers']);
	    }
	    // send_email($to,$subject,$res,$headers);
    	// if(mail($to,$subject,$res,$headers)){
    	// 	echo "<br>Message sent successfully2";
    	// }
	} else {
		echo "parse false";
	}

	function send_email($to, $subject, $content, $headers){
		if(mail($to,$subject,$content,$headers)){
    		echo "<br>Message sent successfully";
    	}
	}
?>