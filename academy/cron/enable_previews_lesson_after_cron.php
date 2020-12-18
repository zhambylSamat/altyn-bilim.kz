<?php
	include_once('connection.php');
	include_once('../common/send_email.php');
	include_once('../common/emails.php');

	// if (isset($_GET['student_id'])) {
	// 	$student_id = $_GET['student_id'];
	// 	$group_infos = get_active_group_infos_by_student_id($student_id);
	// } else {
	$group_infos = get_active_group_infos();
	// }
	// echo 'group_infos array<br>';
	// print_r($group_infos);
	// echo "<br><br>";

	$lesson_progress = get_last_lesson_progress_with_material_count($group_infos);
	// echo "lesson_progress array <br>";
	// print_r($lesson_progress);
	// echo "<br><br>";

	$group_students_for_enable_material = get_group_students_for_enable_material($lesson_progress);
	// echo "group_students_for_enable_material array<br>";
	// print_r($group_students_for_enable_material);
	// echo "<br><br>";

	set_forced_material_access($group_students_for_enable_material);

	function get_active_group_infos_by_student_id ($student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id AS group_info_id
						FROM group_info gi,
							group_student gs
						WHERE gs.student_id = %s
							AND gi.id = gs.group_info_id
							AND 1 = (SELECT s.is_active FROM status s WHERE s.id = gi.status_id)
							AND gi.is_freeze = 0";
			$sql_result = mysqli_query($connect, $query);
			if (!$sql_result) {
				echo mysqli_error($connect).'   get_active_group_infos<br><br>';
				return array();
			}
			$result = array();
			while ($value = mysqli_fetch_assoc($sql_result)) {
				array_push($result, $value['group_info_id']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_active_group_infos() {
		GLOBAL $connect;

		try {
			$dayofweek = date('w') == '0' ? '7' : date('w');
			$query = "SELECT gi.id AS group_info_id
						FROM group_info gi
						WHERE 1 = (SELECT s.is_active FROM status s WHERE s.id = gi.status_id)
							AND gi.is_freeze = 0";
			// $sql_result = mysqli_query($connect, sprintf($query, $student_id));
			$sql_result = mysqli_query($connect, $query);
			if (!$sql_result) {
				echo mysqli_error($connect).'   get_active_group_infos<br><br>';
				return array();
			}
			$result = array();
			while ($value = mysqli_fetch_assoc($sql_result)) {
				array_push($result, $value['group_info_id']);
			}
			return $result;
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function get_last_lesson_progress_with_material_count($group_infos) {
		GLOBAL $connect;

		try {

			$query = "SELECT lp.id,
							lp.subtopic_id,
							DATE_FORMAT(lp.created_date, '%%Y-%%m-%%d %%H:%%i') AS created_date,
							lp.group_info_id,
							(DATE_FORMAT(NOW(), '%%Y-%%m-%%d') = DATE_FORMAT(lp.created_date, '%%Y-%%m-%%d')) AS is_today,
							0 AS ev_count,
							0 AS tv_count,			
							st.subtopic_order
						FROM lesson_progress lp,
								subtopic st
							WHERE lp.group_info_id = %s
								AND st.id = lp.subtopic_id
						ORDER BY lp.created_date DESC, st.subtopic_order DESC
						LIMIT 3";
			// (SELECT count(ev.id) FROM end_video ev WHERE ev.subtopic_id = lp.subtopic_id) AS ev_count,
			// (SELECT count(tv.id) FROM tutorial_video tv WHERE tv.subtopic_id = lp.subtopic_id AND tv.pop_up = 0) AS tv_count,

			$result = array();
			foreach ($group_infos as $group_id) {
				$sql_result = mysqli_query($connect, sprintf($query, $group_id));
				if (!$sql_result) {
					echo sprintf($query, $group_id)."<br><br>";
					echo "<br>".mysqli_error($connect).'   get_last_lesson_progress_with_material_count<br><br>';
					return array();
				}
				$row_num = mysqli_num_rows($sql_result);
				if ($row_num >= 2) {
					$lp_0 = mysqli_fetch_assoc($sql_result);
					$lp_1 = mysqli_fetch_assoc($sql_result);
					if ($row_num == 3) {
						$lp_2 = mysqli_fetch_assoc($sql_result);
					}
					if (!$lp_0['is_today'] && strtotime($lp_0['created_date']) == strtotime($lp_1['created_date'])) {
						$group_info_id = $lp_0['group_info_id'];
						$result[$group_info_id] = array('group_info_id' => $lp_0['group_info_id'],
														'lp_infos' => array(array('id' => $lp_0['id'],
																					'subtopic_id' => $lp_0['subtopic_id'],
																					'ev_count' => $lp_0['ev_count'],
																					'tv_count' => $lp_0['tv_count']),
																			array('id' => $lp_1['id'],
																					'subtopic_id' => $lp_1['subtopic_id'],
																					'ev_count' => $lp_1['ev_count'],
																					'tv_count' => $lp_1['tv_count'])));
						if (isset($lp_2)) {
							array_push($result[$group_info_id]['lp_infos'], array('id' => $lp_2['id'],
																					'subtopic_id' => $lp_2['subtopic_id'],
																					'ev_count' => $lp_2['ev_count'],
																					'tv_count' => $lp_2['tv_count']));
						}
					} else if (isset($lp_2) && strtotime($lp_1['created_date']) == strtotime($lp_2['created_date'])) {
						$group_info_id = $lp_1['group_info_id'];
						$result[$group_info_id] = array('group_info_id' => $group_info_id,
														'lp_infos' => array(array('id' => $lp_0['id'],
																					'subtopic_id' => $lp_0['subtopic_id'],
																					'ev_count' => $lp_0['ev_count'],
																					'tv_count' => $lp_0['tv_count']),
																			array('id' => $lp_1['id'],
																					'subtopic_id' => $lp_1['subtopic_id'],
																					'ev_count' => $lp_1['ev_count'],
																					'tv_count' => $lp_1['tv_count']),
																			array('id' => $lp_2['id'],
																					'subtopic_id' => $lp_2['subtopic_id'],
																					'ev_count' => $lp_2['ev_count'],
																					'tv_count' => $lp_2['tv_count']),));
					} else {
						$group_info_id = $lp_1['group_info_id'];
						if ($lp_0['is_today']) {
							$result[$group_info_id] = array('group_info_id' => $group_info_id,
															'lp_infos' => array(array('id' => $lp_1['id'],
																						'subtopic_id' => $lp_1['subtopic_id'],
																						'ev_count' => $lp_1['ev_count'],
																						'tv_count' => $lp_1['tv_count'])));	
						} else {
							$result[$group_info_id] = array('group_info_id' => $group_info_id,
															'lp_infos' => array(array('id' => $lp_0['id'],
																						'subtopic_id' => $lp_0['subtopic_id'],
																						'ev_count' => $lp_0['ev_count'],
																						'tv_count' => $lp_0['tv_count']),
																				array('id' => $lp_1['id'],
																						'subtopic_id' => $lp_1['subtopic_id'],
																						'ev_count' => $lp_1['ev_count'],
																						'tv_count' => $lp_1['tv_count'])));
						}
						
						if (isset($lp_2) && $lp_2['subtopic_order'] != 2) {
							array_push($result[$group_info_id]['lp_infos'], array('id' => $lp_2['id'],
																					'subtopic_id' => $lp_2['subtopic_id'],
																					'ev_count' => $lp_2['ev_count'],
																					'tv_count' => $lp_2['tv_count']));
						}
					}
				}
			}
			return $result;
			
		} catch (Exception $e) {
			echo $e->getMessage().' get_last_lesson_progress_with_material_count-catch';
			return array();
			// throw $e;
		}
	}

	function get_group_students_for_enable_material($lesson_progress) {
		GLOBAL $connect;

		try {

			$query_group_student = "SELECT gs.id,
										gs.status,
										gs.student_id,
										gs.group_info_id,
										gs.start_from
									FROM group_student gs
									WHERE gs.is_archive = 0
										AND gs.group_info_id = %s";

			$result = array();
			foreach ($lesson_progress as $group_info_id => $val) {
				$group_student_result = mysqli_query($connect, sprintf($query_group_student, $group_info_id));
				if (!$group_student_result) {
					echo sprintf($query_group_student, $group_info_id)."<br><br>";
					echo mysqli_error($connect).'   get_group_students_for_enable_material<br><br>';
					return array();
				}
				while ($group_student = mysqli_fetch_assoc($group_student_result)) {
					$payment = true;
					if ($group_student['status'] == 'inactive') {
						$payment = use_student_exist_payments($group_student['student_id'], $group_student['id'], $group_student['group_info_id']);
					}
					if ($payment) {
						if ($group_student['status'] == 'waiting' || $group_student['status'] == 'inactive') {
							check_access_until($group_student['group_info_id'], $group_student['start_from'], $group_student['id']);
						}
						foreach ($val['lp_infos'] as $lp_info) {
							$set_access = true;
							$is_first_lesson = get_first_access_lesson($group_info_id);
							if (!$is_first_lesson) {
								$has_test_submitted = get_has_test_submitted($lp_info['id'], $group_student['id'], $lp_info['subtopic_id']);
								if ($has_test_submitted) {
									$has_army_group_student_submit_class_works = 
														get_army_group_student_submit_class_works($lp_info['id'], $group_student['id'], $lp_info['subtopic_id']);
									if ($has_army_group_student_submit_class_works) {
										$set_access = false;
									}
								}
								// $eval_count = eval_count($lp_info['id'], $group_student['id']);
								// if ($eval_count[0] == $lp_info['ev_count'] || $eval_count[1] == $lp_info['ev_count']) {
								// 	$tval_count = tval_count($lp_info['id'], $group_student['id']);
								// 	if ($tval_count[0] >= $lp_info['tv_count'] || $tval_count[1] >= $lp_info['tv_count']) {
								// 		$set_access = false;
								// 	}
								// }
							}
							if ($set_access) {
								array_push($result, array('group_student_id' => $group_student['id'],
															'lesson_progress_id' => $lp_info['id']));
							}
						}
					}
				}
			}
			return $result;
			
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function get_army_group_student_submit_class_works ($lesson_progress_id, $group_student_id, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT ag.id
						FROM group_student gs,
							army_group ag
						WHERE gs.id = %s
							AND ag.group_info_id = gs.group_info_id";
			$stmt = mysqli_query($connect, sprintf($query, $group_student_id));
			$row_count = mysqli_num_rows($stmt);

			if ($row_count == 0) {
				return true;
			}

			$query = "SELECT count(td.id) AS tutorial_document_count
						FROM tutorial_document td
						WHERE td.subtopic_id = %s";
			$stmt = mysqli_query($connect, sprintf($query, $subtopic_id));
			$tutorial_document_count = mysqli_fetch_assoc($stmt)['tutorial_document_count'];

			if ($tutorial_document_count == 0) {
				return true;
			}

			$query = "SELECT gscws.id AS gscws_id,
							(SELECT count(gscwsf.id)
							FROM group_student_class_work_submit_files gscwsf
							WHERE gscwsf.group_student_class_work_submit_id = gscws.id) AS file_count
						FROM tutorial_document_action tda,
							group_student_class_work_submit gscws
						WHERE tda.lesson_progress_id = %s
							AND tda.group_student_id = %s
							AND gscws.tutorial_document_action_id = tda.id";
			$stmt = mysqli_query($connect, sprintf($query, $lesson_progress_id, $group_student_id));
			$row_count = mysqli_num_rows($stmt);

			if ($row_count == 0) {
				return false;
			}

			$gscws_result = mysqli_fetch_assoc($stmt);

			if ($gscws_result['file_count'] == 0) {
				return false;
			}
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_has_test_submitted ($lesson_progress_id, $group_student_id, $subtopic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(mt.id) AS material_test_count
						FROM material_test mt
						WHERE mt.subtopic_id = %s";
			$stmt = mysqli_query($connect, sprintf($query, $subtopic_id));
			$material_test_count = mysqli_fetch_assoc($stmt)['material_test_count'];

			if ($material_test_count == 0) {
				return true;
			}

			$query = "SELECT mtr.actual_result
						FROM material_test_action mta,
							material_test_result mtr
						WHERE mta.lesson_progress_id = %s
							AND mta.group_student_id = %s
							AND mtr.material_test_action_id = mta.id";
			$stmt = mysqli_query($connect, sprintf($query, $lesson_progress_id, $group_student_id));
			$row_count = mysqli_num_rows($stmt);

			if ($row_count == 0) {
				return false;
			}
			$actual_result = mysqli_fetch_assoc($stmt)['actual_result'];

			if ($actual_result == '') {
				return false;
			}
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_first_access_lesson ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(lp.id) AS lp_count
						FROM lesson_progress lp
						WHERE lp.group_info_id = %s";
						
			$stmt = mysqli_query($connect, sprintf($query, $group_info_id));
			$lp_count = mysqli_fetch_assoc($stmt)['lp_count'];
			return $lp_count == 2 ? true : false;
			
		} catch (Exception $e) {
			return false;
		}
	}

	function set_forced_material_access($datas) {
		GLOBAL $connect;

		try {

			$query_new_forced_material_access = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id)
																VALUES (%s, %s)";
			$query_exist_forced_material_access = "SELECT fma.id,
														fma.created_date
													FROM forced_material_access fma
													WHERE fma.lesson_progress_id = %s
														AND fma.group_student_id = %s
														AND DATE_FORMAT(fma.created_date, '%%Y-%%m-%%d') = DATE_FORMAT(NOW(), '%%Y-%%m-%%d')";

			foreach ($datas as $value) {
				$result_fma_exists = mysqli_query($connect, sprintf($query_exist_forced_material_access,
																		$value['lesson_progress_id'],
																		$value['group_student_id']));
				if (!$result_fma_exists) {
					echo mysqli_error($connect).'   set_forced_material_access two<br><br>';
					echo sprintf($query_exist_forced_material_access,
								$value['lesson_progress_id'],
								$value['group_student_id'])."<br><br>";
					return false;
				}
				$row_num = mysqli_num_rows($result_fma_exists);
				// echo "result_fma_exists json<br><br>";
				// echo json_encode(mysqli_fetch_assoc($result_fma_exists));
				// echo "<br><br>";
				if ($row_num == 0) {
					// echo 'okk<br>';
					$result_new_forced_material_access = mysqli_query($connect, sprintf($query_new_forced_material_access,
																						$value['lesson_progress_id'],
																						$value['group_student_id']));
					if (!$result_new_forced_material_access) {
						echo mysqli_error($connect).'   set_forced_material_access one<br><br>';
						return false;
					}
					$forced_material_access_id = mysqli_insert_id($connect);

					set_eva($value['group_student_id'], $value['lesson_progress_id'], $forced_material_access_id);
					set_tva($value['group_student_id'], $value['lesson_progress_id'], $forced_material_access_id);
					set_tda($value['group_student_id'], $value['lesson_progress_id'], $forced_material_access_id);
					set_mta_if_does_not_exists($value['group_student_id'], $value['lesson_progress_id']);
				} else {
					// print_r(mysqli_fetch_assoc($result_fma_exists)).' res_one';
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_mta_if_does_not_exists ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id
						FROM material_test_action mta
						WHERE mta.group_student_id = %s
							AND mta.lesson_progress_id = %s";

			$result_mta = mysqli_query($connect, sprintf($query,
														$group_student_id,
														$lesson_progress_id));

			if (!$result_mta) {
				echo mysqli_error($connect).'  set_mta_if_does_not_exists<br><br>';
			}
															
			$row_count = mysqli_num_rows($result_mta);

			if ($row_count == 0) {

				$query = "SELECT count(mt.id) AS c
							FROM material_test mt,
								lesson_progress lp
							WHERE mt.subtopic_id = lp.subtopic_id
								AND lp.id = %s";

				$result_count_mta = mysqli_query($connect, sprintf($query, $lesson_progress_id));
				$test_count = mysqli_fetch_assoc($result_count_mta)['c'];
				$is_finish = $test_count > 0 ? 1 : 0;

				$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish)
												VALUES (%s, %s, %s)";
				$result_insert_mta = mysqli_query($connect, sprintf($query, $group_student_id, $lesson_progress_id, $is_finish));
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_eva($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect; 

		try {
			$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
								VALUES ($group_student_id, $lesson_progress_id, $forced_material_access_id)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   set_eva<br><br>';
				return false;
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tva($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect; 

		try {
			$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, forced_material_access_id)
								VALUES ($group_student_id, $lesson_progress_id, $forced_material_access_id)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   set_tva<br><br>';
				return false;
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_tda($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect; 

		try {
			$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, forced_material_access_id)
								VALUES ($group_student_id, $lesson_progress_id, $forced_material_access_id)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   set_tda<br><br>';
				return false;
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function eval_count($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT count(eval.id) AS count
						FROM end_video_action_log eval
						WHERE eval.end_video_action_id = (SELECT eva.id 
														FROM end_video_action eva 
														WHERE eva.lesson_progress_id = $lesson_progress_id
															AND eva.group_student_id = $group_student_id
														ORDER BY eva.accessed_date DESC
														LIMIT 1)";
			$query_fma = "SELECT count(eval.id) AS count
							FROM end_video_action_log eval
							WHERE eval.end_video_action_id = (SELECT eva.id
																FROM end_video_action eva
																WHERE eva.lesson_progress_id = $lesson_progress_id
																	AND eva.group_student_id = $group_student_id
																	AND eva.forced_material_access_id = (SELECT fma.id
																									FROM forced_material_access fma
																									WHERE fma.lesson_progress_id = $lesson_progress_id
																										AND fma.group_student_id = $group_student_id
																									ORDER BY fma.created_date DESC
																									LIMIT 1)
																ORDER BY eva.accessed_date DESC
																LIMIT 1)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   eval_count<br><br>';
				return [0, 0];
			}
			$query_count = mysqli_fetch_assoc($result)['count'];

			$result = mysqli_query($connect, $query_fma);
			if (!$result) {
				echo mysqli_error($connect).'   eval_count_fma<br><br>';
				echo $query_fma."<br><br>";
				return [$query_count, 0];
			}
			$query_fma_count = mysqli_fetch_assoc($result)['count'];
			return [$query_count, $query_fma_count];
			
		} catch (Exception $e) {
			echo $e->getMessage().' eval_count-catch';
			return 0;
			throw $e;
		}
	}

	function tval_count($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT count(tval.id) AS count
						FROM tutorial_video_action_log tval
						WHERE tval.tutorial_video_action_id = (SELECT tva.id 
														FROM tutorial_video_action tva 
														WHERE tva.lesson_progress_id = $lesson_progress_id
															AND tva.group_student_id = $group_student_id
														ORDER BY tva.accessed_date DESC
														LIMIT 1)";
			$query_fma = "SELECT count(tval.id) AS count
							FROM tutorial_video_action_log tval
							WHERE tval.tutorial_video_action_id = (SELECT tva.id
																FROM tutorial_video_action tva
																WHERE tva.lesson_progress_id = $lesson_progress_id
																	AND tva.group_student_id = $group_student_id
																	AND tva.forced_material_access_id = (SELECT fma.id
																									FROM forced_material_access fma
																									WHERE fma.lesson_progress_id = $lesson_progress_id
																										AND fma.group_student_id = $group_student_id
																									ORDER BY fma.created_date DESC
																									LIMIT 1)
																ORDER BY tva.accessed_date DESC
																LIMIT 1)";

			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   tval_count<br><br>';
				return [0, 0];
			}
			$query_count = mysqli_fetch_assoc($result)['count'];

			$result = mysqli_query($connect, $query_fma);
			if (!$result) {
				echo mysqli_error($connect).'   tval_count_fma<br><br>';
				return [$query_count, 0];
			}
			$query_fma_count = mysqli_fetch_assoc($result)['count'];
			return [$query_count, $query_fma_count];
			
		} catch (Exception $e) {
			echo $e->getMessage().' tval_count-catch';
			return 0;
			throw $e;
		}
	}




	function use_student_exist_payments($student_id, $group_student_id, $group_info_id) {
		GLOBAL $connect;

		try {

			$query_update_gs = "UPDATE group_student SET status = 'waiting' WHERE id = $group_student_id;";
			$query_group_student_payment = "SELECT gsp.id AS group_student_payment_id
											FROM group_student_payment gsp
											WHERE gsp.group_student_id = $group_student_id
												AND gsp.is_used = 0
											ORDER BY gsp.payed_date ASC
											LIMIT 1";

			$result = mysqli_query($connect, $query_group_student_payment);
			if (!$result) {
				echo mysql_error($connect);
				return false;
			}

			$query_update_group_student_payment = "UPDATE group_student_payment SET is_used = 0, start_date = '%s' WHERE id = %s";

			$has_payment = mysqli_num_rows($result);
			if ($has_payment == 1) {
				$value = mysqli_fetch_assoc($result);
				if (!mysqli_query($connect, sprintf($query_update_group_student_payment, date('Y-m-d'), $value['group_student_payment_id']))) {
					echo mysqli_error($connect);
					return false;
				}

				if (!mysqli_query($connect, $query_update_gs)) {
					echo mysqli_error($connect);
					return false;
				}
				return true;
			} else {
				$query_student_balance = "SELECT sb.id,
											sb.days
										FROM student_balance sb
										WHERE sb.student_id = $student_id
											AND sb.is_used = 0";
				$result = mysqli_query($connect, $query_student_balance);
				if (!$result) {
					echo mysqli_error($connect);
					return false;
				}

				$days = 0;
				$query_update_sb = "UPDATE student_balance SET is_used = 1, used_for_group = %s, used_date = NOW() WHERE id = %s";
				while ($value = mysqli_fetch_assoc($result)) {
					$days += $value['days'];
					if (!mysqli_query($connect, sprintf($query_update_sb, $group_info_id, $value['id']))) {
						echo mysqli_error($connect);
						return false;
					}
				}
				if ($days > 0) {
					$query_new_payment = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, is_used, payment_type, partial_payment_days)
												VALUES ($group_student_id, NOW(), NOW(), 0, 'balance', $days)";
					if (!mysqli_query($connect, $query_new_payment)) {
						echo mysqli_error($connect);
						return false;
					}

					$query_update_gs = "UPDATE group_student SET status = 'waiting' WHERE id = $group_student_id;";
					if (!mysqli_query($connect, $query_update_gs)) {
						echo mysqli_error($connect);
						return false;
					}
					return true;
				}
				return false;
			}

		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_access_until($group_id, $subtopic_id, $group_student_id) {
		GLOBAL $connect;
		try {
			$query = "SELECT lp.id FROM lesson_progress lp WHERE lp.group_info_id = $group_id
																AND lp.subtopic_id = $subtopic_id
																AND lp.created_date <= TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d'), '07:10:00')";
			$result = mysqli_query($connect, $query);
			if ($result) {
				$row = mysqli_num_rows($result);
				echo "row: ".$row."<br>";
				if ($row > 0) {
					$query = "UPDATE group_student SET status = 'active' WHERE id = $group_student_id";
					$res = mysqli_query($connect, $query);
					if (!$res) {
						echo $query."<br>";
						print_r(mysqli_error($connect));
					}
					$query = "SELECT gsp.id, 
									gsp.payment_type,
									DATE_FORMAT(gsp.start_date, '%Y-%m-%d') AS start_date,
									gsp.partial_payment_days
								FROM group_student_payment gsp
								WHERE gsp.group_student_id = $group_student_id
									AND gsp.is_used = 0
									AND gsp.finished_date IS NULL
								ORDER BY gsp.payment_type ASC, gsp.payed_date ASC
								LIMIT 1";
					$res = mysqli_query($connect, $query);
					if (!$res) {
						echo $query."<br>";
						print_r(mysqli_error($connect));
					}
					$value = mysqli_fetch_assoc($res);
					$gsp_id = $value['id'];
					if ($value['payment_type'] == 'money') {
						echo 'one start_dateeeee'.$value['start_date'];
						if ($value['start_date'] != '') {
							echo 'two';
							if ($value['partial_payment_days'] != '') {
								echo 'three';
								$access_until = date('Y-m-d', strtotime($value['start_date'].' + '.$value['partial_payment_days'].' days'));
							} else {
								echo 'four';
								echo '<br>start_date: '.$value['start_date'].'<br>';
								$access_until = date('Y-m-d', strtotime($value['start_date'].' + 1 month'));
								echo '<br>access_until: '.$access_until.'<br>';
							}
						} else {
							$access_until = date('Y-m-d', strtotime(' + 1 month'));
						}
					} else if ($value['payment_type'] == 'balance') {
						if ($value['start_date'] != '') {
							$access_until = date('Y-m-d', strtotime($value['start_date'].' + '.$value['partial_payment_days'].' days'));
						} else {
							$access_until = date('Y-m-d', strtotime('+ '.$value['partial_payment_days'].' days'));
						}
					}
					$query = "UPDATE group_student_payment
								SET access_until = '$access_until',
									is_used = 1,
									used_date = NOW()
								WHERE id = $gsp_id";
					$res = mysqli_query($connect, $query);
					if (!$res) {
						echo $query."<br>";
						print_r(mysqli_error($connect));
					}
				}
			}
		} catch (Exception $e) {
			throw $e;
		}
	}
?>