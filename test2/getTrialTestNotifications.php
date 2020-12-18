<?php
	include_once '../connection.php';

	try {
		$query = "SELECT n.id,
						n.count,
						n.status
					FROM notification n
					WHERE n.object_id = 5
						AND n.status NOT IN ('D', 'DA', 'AD')
					ORDER BY n.object_parent_num, n.count DESC";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$sql_row_num = $stmt->rowCount();

		// $result = json_encode($res);
		// echo $result;
		
		$notification_id_list = array();
		$i = 0;
		while ($i < $sql_row_num) {
			if ($res[$i]['count'] == '1') {
				if ($res[$i]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
				}
				$i = $i + 1;
				
			} else if ($res[$i]['count'] == '2') {
				if ($res[$i]['status'] == 'A' && $res[$i+1]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
					array_push($notification_id_list, $res[$i+1]['id']);
				}
				$i = $i + 2;
			} else if ($res[$i]['count'] == '3') {
				if ($res[$i]['status'] == 'A' && $res[$i+1]['status'] == 'A' && $res[$i+2]['status'] == 'A') {
					array_push($notification_id_list, $res[$i]['id']);
					array_push($notification_id_list, $res[$i+1]['id']);
					array_push($notification_id_list, $res[$i+2]['id']);
				}
				$i = $i + 3;
			}
		}

		$query = "SELECT s.surname,
						s.name,
						sj.subject_name,
						ttm.mark,
						ttm.date_of_test
					FROM notification n,
						trial_test tt,
						trial_test_mark ttm,
						subject sj,
						student s
					WHERE n.id IN (".implode(', ', $notification_id_list).")
						AND n.object_num = ttm.trial_test_mark_num
						AND ttm.trial_test_num = tt.trial_test_num
						AND tt.subject_num = sj.subject_num
						AND tt.student_num = s.student_num
						AND s.block != 6
						AND s.student_num != 'US5985cba14b8d3100168809'
					ORDER BY s.surname, s.name, sj.subject_name, n.object_parent_num, n.count";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$result = json_encode($res);
		echo $result;
		// '2666', '2702', '3925', '3578', '3834', '3919', '4042', '3870', '4165', '3864', '4171', '3773', '3663', '3749', '3992', '4076', '3642', '3774', '3724', '3951', '2475', '3865', '4172', '3987', '3813', '3858', '4152', '3690', '3970', '4007', '3570', '3820', '4157', '3638', '3760', '4100', '3753', '4041', '3980', '4093', '3856', '3735', '3957', '3610', '3815', '3866', '4173', '4043', '4108', '4140', '3981', '4095', '4167', '3801', '4114', '3988', '2944', '3559', '3861', '4097', '2656', '2731', '2671', '2697', '3794', '4131', '3811', '4117', '3982', '4094', '3993', '4075', '4146', '3994', '4077', '2638', '3888', '4071', '4155', '3878', '3995', '4074', '3770', '4104', '3996', '4080', '4158', '3799', '3797', '4134', '3812', '4119', '3983', '4092', '3855', '3868', '4175', '3810', '4116', '3802', '4110', '4120', '3807', '3418', '3640', '4156', '3984', '3989', '4086', '3808', '2768', '3009', '4148', '3350', '2993', '4162', '4118', '4068', '3926', '3682', '3903', '4164', '3990', '4081', '4163', '3873', '4168', '4058', '3942', '4053', '4067', '3672', '3756', '3997', '2871', '3934', '4025', '3083', '3353', '3889', '4059', '3671', '3754', '3847', '4024', '3667', '3748', '3998', '4078', '3809', '3985', '4091', '4027', '3805', '4112', '3869', '4176', '3035'
	} catch (Exception $e) {
		throw $e;
	}
?>
