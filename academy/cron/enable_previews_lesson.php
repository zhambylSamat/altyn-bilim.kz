<?php
	include_once('connection.php');
	include_once('../common/send_email.php');
	include_once('../common/emails.php');

	$group_infos = get_active_group_infos();
	$lesson_progress = get_last_lesson_progress_with_material_count($group_infos);
	$group_students_for_enable_material = get_group_students_for_enable_material($lesson_progress);
	set_forced_material_access($group_students_for_enable_material);

	function get_active_group_infos() {
		GLOBAL $connect;

		try {
			$dayofweek = date('w') == '0' ? '7' : date('w');
			$query = "SELECT gi.id AS group_info_id
						FROM group_info gi
						WHERE 1 = (SELECT s.is_active FROM status s WHERE s.id = gi.status_id)
							AND $dayofweek IN (SELECT gsh.week_day_id
												FROM group_schedule gsh
												WHERE gsh.group_info_id = gi.id)";
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
							('2020-4-24' = DATE_FORMAT(lp.created_date, '%%Y-%%m-%%d')) AS is_today,
							(SELECT count(ev.id) FROM end_video ev WHERE ev.subtopic_id = lp.subtopic_id) AS ev_count,
							(SELECT count(tv.id) FROM tutorial_video tv WHERE tv.subtopic_id = lp.subtopic_id) AS tv_count,
							(SELECT count(td.id) FROM tutorial_document td WHERE td.subtopic_id = lp.subtopic_id) AS td_count
						FROM lesson_progress lp
							WHERE lp.group_info_id = %s
						ORDER BY lp.created_date DESC, lp.subtopic_id DESC
						LIMIT 2";

			$result = array();
			foreach ($group_infos as $group_id) {
				$sql_result = mysqli_query($connect, sprintf($query, $group_id));
				if (!$sql_result) {
					echo sprintf($query, $group_id)."<br><br>";
					echo "<br>".mysqli_error($connect).'   get_last_lesson_progress_with_material_count<br><br>';
					return array();
				}
				$row_num = mysqli_num_rows($sql_result);
				if ($row_num > 0) {
					// $lp_0 = mysqli_fetch_assoc($sql_result);
					$lp_1 = mysqli_fetch_assoc($sql_result);
					// if ($row_num == 3) {
						$lp_2 = mysqli_fetch_assoc($sql_result);
					// }
					// if (!$lp_0['is_today'] && strtotime($lp_0['created_date']) == $lp_0['created_date']) {
					// 	$group_info_id = $lp_0['group_info_id'];
					// 	$result[$group_info_id] = array('group_info_id' => $lp_0['group_info_id'],
					// 									'lp_infos' => array(array('id' => $lp_0['id'],
					// 																'subtopic_id' => $lp_0['subtopic_id'],
					// 																'ev_count' => $lp_0['ev_count'],
					// 																'tv_count' => $lp_0['tv_count'],
					// 																'td_count' => $lp_0['td_count']),
					// 														array('id' => $lp_1['id'],
					// 																'subtopic_id' => $lp_1['subtopic_id'],
					// 																'ev_count' => $lp_1['ev_count'],
					// 																'tv_count' => $lp_1['tv_count'],
					// 																'td_count' => $lp_1['td_count'])));
					// } else 
					if (isset($lp_2) && strtotime($lp_1['created_date']) == $lp_2['created_date']) {
						$group_info_id = $lp_1['group_info_id'];
						$result[$group_info_id] = array('group_info_id' => $lp_1['group_info_id'],
														'lp_infos' => array(array('id' => $lp_1['id'],
																					'subtopic_id' => $lp_1['subtopic_id'],
																					'ev_count' => $lp_1['ev_count'],
																					'tv_count' => $lp_1['tv_count'],
																					'td_count' => $lp_1['td_count']),
																			array('id' => $lp_2['id'],
																					'subtopic_id' => $lp_2['subtopic_id'],
																					'ev_count' => $lp_2['ev_count'],
																					'tv_count' => $lp_2['tv_count'],
																					'td_count' => $lp_2['td_count'])));
					} else {
						$group_info_id = $lp_1['group_info_id'];
						$result[$group_info_id] = array('group_info_id' => $lp_1['group_info_id'],
														'lp_infos' => array(array('id' => $lp_1['id'],
																					'subtopic_id' => $lp_1['subtopic_id'],
																					'ev_count' => $lp_1['ev_count'],
																					'tv_count' => $lp_1['tv_count'],
																					'td_count' => $lp_1['td_count'])));
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

			$query_group_student = "SELECT gs.id
									FROM group_student gs
									WHERE gs.status = 'active'
										AND gs.is_archive = 0
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
					foreach ($val['lp_infos'] as $lp_info) {
						$set_access = true;
						$eval_count = eval_count($lp_info['id'], $group_student['id']);
						if ($eval_count == $lp_info['ev_count']) {
							$tval_count = tval_count($lp_info['id'], $group_student['id']);
							if ($tval_count == $lp_info['tv_count']) {
								$tdal_count = tdal_count($lp_info['id'], $group_student['id']);
								if ($tdal_count == $lp_info['td_count']) {
									$set_access = false;
								}
							}
						}
						if ($set_access) {
							array_push($result, array('group_student_id' => $group_student['id'],
														'lesson_progress_id' => $lp_info['id']));
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

	function set_forced_material_access($datas) {
		GLOBAL $connect;

		try {

			$query_new_forced_material_access = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id)
																VALUES (%s, %s)";
			$query_exist_forced_material_access = "SELECT fma.id
													FROM forced_material_access fma
													WHERE fma.lesson_progress_id = %s
														AND fma.group_student_id = %s";

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
				if ($row_num == 0) {
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
				}
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
														ORDER BY eva.accessed_date ASC
														LIMIT 1)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   eval_count<br><br>';
				return 0;
			} 
			return mysqli_fetch_assoc($result)['count'];
			
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
														ORDER BY tva.accessed_date ASC
														LIMIT 1)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   tval_count<br><br>';
				return 0;
			} 
			return mysqli_fetch_assoc($result)['count'];
			
		} catch (Exception $e) {
			echo $e->getMessage().' tval_count-catch';
			return 0;
			throw $e;
		}
	}

	function tdal_count($lesson_progress_id, $group_student_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT count(tdal.id) AS count
						FROM tutorial_document_action_log tdal
						WHERE tdal.tutorial_document_action_id = (SELECT tda.id 
														FROM tutorial_document_action tda 
														WHERE tda.lesson_progress_id = $lesson_progress_id
															AND tda.group_student_id = $group_student_id
														ORDER BY tda.accessed_date ASC
														LIMIT 1)";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo mysqli_error($connect).'   tdal_count<br><br>';
				return 0;
			} 
			return mysqli_fetch_assoc($result)['count'];
			
		} catch (Exception $e) {
			echo $e->getMessage().' tdal_count-catch';
			return 0;
			throw $e;
		}
	}
?>