<?php 
	include("connection.php");

	try {
		$stmt = $conn->prepare("UPDATE student_test_permission stp
								INNER JOIN student_permission sp 
									ON stp.student_permission_num = sp.student_permission_num
								INNER JOIN group_student gs
									ON sp.student_num = gs.student_num
								INNER JOIN group_info gi
									ON gi.group_info_num = gs.group_info_num
										AND (SELECT CURRENT_TIME) 
											NOT BETWEEN (SELECT SUBTIME(gi.start_lesson, '00:30:00')) 
												AND (SELECT ADDTIME(gi.finish_lesson, '00:30:00')) 
								SET stp.video_permission = 'f'");
		$stmt->execute();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>	