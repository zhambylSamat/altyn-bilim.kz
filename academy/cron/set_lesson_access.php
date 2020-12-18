<?php
	include_once('connection.php');
	include_once('../common/send_email.php');
	include_once('../common/emails.php');

// JSON_UNESCAPED_UNICODE

	$to_mail_data = array();
	if (!is_holiday()) {
		$group_result = get_group_info_with_lesson_progress();
		echo 'group_result<br>';
		echo json_encode($group_result, JSON_UNESCAPED_UNICODE);
		echo "<br><br>";
		$subject_result = get_subject_topic_subtopics();
		echo 'subject_result<br>';
		echo json_encode($subject_result, JSON_UNESCAPED_UNICODE);
		echo "<br><br>";
		$next_group_lesson = get_next_group_lesson($group_result, $subject_result);
		echo 'next_group_lesson<br>';
		echo json_encode($next_group_lesson, JSON_UNESCAPED_UNICODE);
		echo "<br><br>";
		$lesson_progress_and_group_students = set_lesson_progress($next_group_lesson);
		echo 'lesson_progress_and_group_students<br>';
		echo json_encode($lesson_progress_and_group_students,JSON_UNESCAPED_UNICODE);
		echo "<br>";
		set_material_acces_by_lesson_progress();
		$to_mail_data = count($lesson_progress_and_group_students) > 0 ? get_group_title_and_student_fio($lesson_progress_and_group_students) : array();
	} else {
		echo 'is_holiday';
	}
	
	$html = "<table>";
		foreach ($to_mail_data as $gi_id => $gi_val) {
			$group_id = 0;


			$html .= "<tr>";
			if ($group_id != $gi_id) {
				$html .= "<td style='border: 1px solid black;' rowspan='".$gi_val['row_span']."'>".$gi_val['group_name']."</td>";
				$group_id = $gi_id;
			}

			foreach ($gi_val['contents'] as $st_id => $st_val) {
				$subtopic_id = 0;

				if ($subtopic_id != $st_id) {
					$html .= "<td style='border: 1px solid black;' rowspan='".$st_val['row_span']."'>".$st_val['subject_title'].' | '.$st_val['topic_title'].' | '.$st_val['subtopic_title']."</td>";
					$subtopic_id = $st_id;
				}

				foreach ($st_val['students'] as $std_fio) {
					$html .= "<td style='border: 1px solid black;'>".$std_fio."<td></tr>";
				}
			}

			$html .= "<tr></tr>";
		}
	$html .= "</table>";

	echo $html;

	function is_holiday() {
		GLOBAL $connect;

		try {

			$query = "SELECT h.id
						FROM holidays h
						WHERE DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN h.from_date AND h.to_date";
			$row = mysqli_query($connect, $query);
			$row_count = mysqli_num_rows($row);
			if ($row_count == 0) {
				return false;
			} else {
				$holidays_id = mysqli_fetch_assoc($row)['id'];
				echo $holidays_id;
				return $holidays_id == "" ? false : true;
			}

			return false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_title_and_student_fio($datas) {
		GLOBAL $connect;

		try {

			$result = array();
			foreach ($datas as $lp_val) {
				// $lp_ids_str .= "'".$lp_val['lp_id']."', ";
				$lp_id = $lp_val['lp_id'];
				$gs_ids_str = "";
				foreach ($lp_val['group_students_id'] as $gs_id) {
					$gs_ids_str .= "'".$gs_id."', ";
				}
				$gs_ids_str .= '0';

				$sql = "SELECT gi.id AS gi_id,
							sj.title AS subject_title,
							t.title AS topic_title,
							st.id AS subtopic_id,
							st.title AS subtopic_title,
							gi.group_name,
							s.last_name,
							s.first_name
						FROM lesson_progress lp,
							group_info gi,
							group_student gs,
							student s,
							subject sj,
							topic t,
							subtopic st
						WHERE lp.id = $lp_id
							AND gi.id = lp.group_info_id
							AND gs.id in ($gs_ids_str)
							AND st.id = lp.subtopic_id
							AND t.id = st.topic_id
							AND sj.id = t.subject_id
							AND s.id = gs.student_id
						ORDER BY gi.group_name, st.subtopic_order, s.last_name, s.first_name";

				if ($row = mysqli_query($connect, $sql)) {
					while ($value = mysqli_fetch_assoc($row)) {
						if (!isset($result[$value['gi_id']])) {
							$result[$value['gi_id']]['group_name'] = $value['group_name'];
							$result[$value['gi_id']]['contents'] = array();
							$result[$value['gi_id']]['row_span'] = 0;
						}
						if (!isset($result[$value['gi_id']]['contents'][$value['subtopic_id']])) {
							$result[$value['gi_id']]['contents'][$value['subtopic_id']] = array('subject_title' => $value['subject_title'],
																							'topic_title' => $value['topic_title'],
																							'subtopic_title' => $value['subtopic_title'],
																							'row_span' => 0,
																							'students' => array());
						}
						$full_name = $value['last_name']." ".$value['first_name'];
						array_push($result[$value['gi_id']]['contents'][$value['subtopic_id']]['students'], $full_name);
						$result[$value['gi_id']]['row_span']++;
						$result[$value['gi_id']]['contents'][$value['subtopic_id']]['row_span']++;
					}
				}
			}

			return $result;
			
		} catch (Exception $e) {
			return array();
		}
	}

	function set_lesson_progress($datas) {
		GLOBAL $connect;

		try {
			$result = array();
			foreach ($datas as $value) {
				if ($value['subtopic_id'] != -1) {
					$subtopic_id = $value['subtopic_id'];
					$group_info_id = $value['group_info_id'];
					$cdate = explode('-', date('Y-m-d'));
					$day = intval($cdate[2]);
					$month = intval($cdate[1]);
					$year = intval($cdate[0]);
					$d = mktime(7, 0, 0, $month, $day, $year);
					$created_date = date('Y-m-d H:i:s', $d);
					$query_lp_exists = "SELECT lp.id
										FROM lesson_progress lp
										WHERE lp.subtopic_id = $subtopic_id
											AND lp.group_info_id = $group_info_id";
					$res_lp_exists = mysqli_query($connect, $query_lp_exists);
					if (!$res_lp_exists) {
						echo $query_lp_exists."<br><br>";
						echo "<br>".mysqli_error($connect);
					}
					$row_count = mysqli_num_rows($res_lp_exists);
					if ($row_count == 0) {
						$sql = "INSERT INTO lesson_progress (subtopic_id, group_info_id, created_date)
										VALUES($subtopic_id, $group_info_id, STR_TO_DATE('".$created_date."', '%Y-%m-%d %H:%i:%s'))";
						$res = mysqli_query($connect, $sql);
						if (!$res) {
							echo $sql."<br><br>";
							echo "<br>".mysqli_error($connect);
						}
						$lp_id = mysqli_insert_id($connect);

						$sql = "SELECT gs.id
								FROM group_student gs
								WHERE gs.group_info_id = $group_info_id";

						$group_students_id = array();
						if ($row = mysqli_query($connect, $sql)) {
							while ($value = mysqli_fetch_assoc($row)) {
								array_push($group_students_id, $value['id']);
							}
						} else {
							echo $sql."<br><br>";
							echo "<br>".mysqli_error($connect);
						}

						array_push($result, array('lp_id' => $lp_id,
												'group_students_id' => $group_students_id));
					}

				} else {
					transfer_or_finish($value['group_info_id']);
					archive_group($value['group_info_id']);
				}
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function transfer_or_finish($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT t.topic_order,
							t.subject_id,
							gi.status_id
						FROM topic t,
							group_info gi
						WHERE gi.id = $group_info_id
							AND t.id = gi.topic_id";
			$result = mysqli_query($connect, $query);
			if (!$result){
				echo $query."<br><br>";
				echo "<br>".mysqli_error($connect);
				return false;
			}
			$current_topic = mysqli_fetch_assoc($result);

			$subject_id = $current_topic['subject_id'];
			$topic_order = intval($current_topic['topic_order']) + 1;
			$group_info_status = $current_topic['status_id'];
			$query = "SELECT t.id,
							t.title,
							(SELECT st.id
							FROM subtopic st
							WHERE st.topic_id = t.id
								AND st.subtopic_order = 1) AS first_subtopic_id
						FROM topic t
						WHERE t.subject_id = $subject_id
							AND topic_order = $topic_order";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				echo $query."<br><br>";
				echo "<br>".mysqli_error($connect);
			}

			$row_num = mysqli_num_rows($result);

			if ($group_info_status == 2) {
				if ($row_num == 0) {
					set_balance_and_transfer_group($group_info_id, null);
				} else {
					$value = mysqli_fetch_assoc($result);
					$topic_info = array('id' => $value['id'],
										'title' => $value['title'],
										'first_subtopic_id' => $value['first_subtopic_id']);
					set_balance_and_transfer_group($group_info_id, $topic_info);
				}
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function set_balance_and_transfer_group($old_group_id, $topic_info) {
		GLOBAL $connect;

		try {

			$query_group_infos = "SELECT gi.subject_id,
									gi.lesson_type,
									gi.group_name,
									gi.status_id,
									(SELECT DATE_FORMAT(lp.created_date, '%Y-%m-%d')
									FROM lesson_progress lp
									WHERE lp.group_info_id = gi.id
									ORDER BY lp.created_date DESC
									LIMIT 1) AS last_lesson_date
							FROM group_info gi
							WHERE gi.id = $old_group_id";
			$result = mysqli_query($connect, $query_group_infos);
			if (!$result) {
				echo mysqli_error($connect)." one<br>";
				return false;
			}

			$group_infos = mysqli_fetch_assoc($result);
			if (isset($topic_info)) {
				$new_group_name = $topic_info['title'];
				echo $new_group_name;
				$subject_id = $group_infos['subject_id'];
				$topic_id = $topic_info['id'];
				$status_id = $group_infos['status_id'];
				$query_new_group = "INSERT INTO group_info (subject_id, topic_id, lesson_type, group_name, start_date, created_date, status_id)
										VALUES ($subject_id, $topic_id, 'topic', '$new_group_name', NOW(), NOW(), $status_id)";
				$result = mysqli_query($connect, $query_new_group);
				if (!$result) {
					echo mysqli_error($connect)." two<br>";
					return false;
				}
				$new_group_id = mysqli_insert_id($connect);

				$query = "SELECT ag.id 
							FROM army_group ag
							WHERE ag.group_info_id = $old_group_id";
				$result = mysqli_query($connect, $query);
				$ag_row_nums = mysqli_num_rows($result);
				if ($ag_row_nums == 1) {
					$query = "INSERT INTO army_group (group_info_id) VALUES ($new_group_id)";
					$insert_army_group_result = mysqli_query($connect, $query);
				}

				$query = "SELECT schg.id
							FROM school_group schg
							WHERE schg.group_info_id = $old_group_id";
				$result = mysqli_query($connect, $query);
				$schg_row_nums = mysqli_num_rows($result);

				if ($schg_row_nums == 1) {
					$query = "INSERT INTO school_group (group_info_id) VALUES ($new_group_id)";
					$insert_army_group_result = mysqli_query($connect, $query);
				}


				$query_old_group_schedule = "SELECT week_day_id FROM group_schedule WHERE group_info_id = $old_group_id";
				$result = mysqli_query($connect, $query_old_group_schedule);
				if (!$result) {
					echo mysqli_error($connect)." nine<br>";
					return false;
				}
				$query_new_group_schedule = "INSERT INTO group_schedule (group_info_id, week_day_id)
													VALUES (%s, %s)";
				while ($value = mysqli_fetch_assoc($result)) {
					if (!mysqli_query($connect, sprintf($query_new_group_schedule, $new_group_id, $value['week_day_id']))) {
						echo mysqli_error($connect)." ten<br>";
						return false;
					}
				}
			}

			$query_old_group_student = "SELECT gs.id,
											gs.student_id
										FROM group_student gs
										WHERE gs.group_info_id = $old_group_id
											AND gs.is_archive = 0";
			$result = mysqli_query($connect, $query_old_group_student);
			if (!$result) {
				echo mysqli_error($connect)." three<br>";
				return false;
			}
			while ($value = mysqli_fetch_assoc($result)) {
				$query_group_student_payment = "SELECT gsp.id,
													DATE_FORMAT(gsp.access_until, '%%Y-%%m-%%d') AS access_until
												FROM group_student_payment gsp
												WHERE gsp.group_student_id = %s
													AND gsp.is_used = 1
													AND gsp.finished_date IS NULL
												ORDER BY payed_date ASC
												LIMIT 1";
				$res_group_student_payment = mysqli_query($connect, sprintf($query_group_student_payment, $value['id']));
				if (!$res_group_student_payment) {
					echo mysqli_error($connect)." four<br>";
					return false;
				}
				$days = 0;
				$group_student_payment = mysqli_fetch_assoc($res_group_student_payment);
				$old_group_student_payment_count = mysqli_num_rows($res_group_student_payment);
				if ($old_group_student_payment_count > 0) {
					$date1 = date_create($group_student_payment['access_until']);
					$date2 = date_create($group_infos['last_lesson_date']);
					$days = date_diff($date1, $date2)->format('%a');
				}

				$already_learned = false;
				// if (isset($topic_info)) {
				// 	$already_learned = check_student_for_learned_topic($value['student_id'], $topic_info['id']);
				// }

				$full_finished = 1;
				$status = 'inactive';
				if ($days > 0) {
					$full_finished = 0;
					$status = 'waiting';

					$is_used = 0;
					$used_for_group = null;
					if (isset($topic_info)) { //&& !$already_learned
						$is_used = 1;
						$used_for_group = $new_group_id;
					}
					if ($used_for_group == null) {
						$query_new_student_balance_with_not_used = "INSERT INTO student_balance (student_id, group_id, is_used, days, used_date)
																		VALUES (%s, %s, %s, %s, NOW())";
						$res_new_student_balance = mysqli_query($connect, sprintf($query_new_student_balance_with_not_used,
																				$value['student_id'],
																				$old_group_id,
																				$is_used, $days));
					} else {
						$query_new_student_balance = "INSERT INTO student_balance (student_id, group_id, is_used, used_for_group, days, used_date)
														VALUES (%s, %s, %s, %s, %s, NOW())";
						$res_new_student_balance = mysqli_query($connect, sprintf($query_new_student_balance,
																				$value['student_id'],
																				$old_group_id,
																				$is_used, $used_for_group, $days));
					}
					if (!$res_new_student_balance) {
						echo mysqli_error($connect)." five<br>";
						return false;
					}
				}
				if (isset($topic_info) && isset($new_group_id)) { // && !$already_learned
					$query_new_group_student = "INSERT group_student (group_info_id, transfer_from_group, student_id, start_from, status)
												VALUES (%s, %s, %s, %s, '%s')";
					if (!mysqli_query($connect, sprintf($query_new_group_student,
														$new_group_id, $old_group_id, $value['student_id'], $topic_info['first_subtopic_id'], $status))) {
						echo mysqli_error($connect)." six<br>";
						return false;
					}
					$new_group_student_id = mysqli_insert_id($connect);
					if ($days > 0) {
						$query_new_gsp = "INSERT INTO group_student_payment (group_student_id, payed_date, start_date, is_used, payment_type, partial_payment_days)
												VALUES (%s, NOW(), '%s', 0, 'balance', %s)";
						$start_date = $group_infos['last_lesson_date'];
						if (!mysqli_query($connect, sprintf($query_new_gsp, $new_group_student_id, $start_date, $days))) {
							echo mysqli_error($connect)." seven<br>";
							return false;
						}
						transfer_unused_payments($value['id'], $new_group_student_id);
					}
				}
				if ($old_group_student_payment_count > 0) {
					$query_update_gsp = "UPDATE group_student_payment SET full_finished = %s, finished_date = '%s' WHERE id = %s";
					if (!mysqli_query($connect, sprintf($query_update_gsp, 
														$full_finished, $group_infos['last_lesson_date'], $group_student_payment['id']))) {
						echo sprintf($query_update_gsp, $full_finished, $group_infos['last_lesson_date'], $group_student_payment['id'])."<br>";
						echo mysqli_error($connect)." eight<br>";
						return false;
					}
				}
				// if (!mysqli_query($connect, sprintf($query_inactive_group_student, $value['id']))) {
					// echo mysqli_error($connect)." nine<br>";
					// return false;
				// }
			}
			
		} catch (Exception $e) {
			throw $e;
		}

	}

	function transfer_unused_payments($old_group_student_id, $new_group_student_id) {
		GLOBAL $connect;

		try {

			$query = "UPDATE group_student_payment SET group_student_id = %s WHERE group_student_id = %s AND is_used = 0";
			if (!mysqli_query($connect, sprintf($query, $new_group_student_id, $old_group_student_id))) {
				echo sprintf($query, $new_group_student_id, $old_group_student_id);
				echo mysqli_error($connect);
				return false;
			}
			return true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_student_for_learned_topic($student_id, $topic_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gi.id
						FROM group_student gs,
							group_info gi
						WHERE gs.student_id = $student_id
							AND gi.id = gs.group_info_id
							AND gi.topic_id = $topic_id";
			$result = mysqli_query($connect, $query);
			if (!$result) {
				return false;
			}
			$row_num = mysqli_num_rows($result);
			if ($row_num > 0) {
				return true;
			}
			return false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function has_access($group_id, $subtopic_id) {

		GLOBAL $connect;

		try {

			$query = "SELECT lp.subtopic_id
						FROM lesson_progress lp
						WHERE lp.group_info_id = $group_id";
			$subtopics_id = array();
			$row = mysqli_query($connect, $query);
			while ($val = mysqli_fetch_assoc($row)) {
				array_push($subtopics_id, $val['subtopic_id']);
			}
			return in_array($subtopic_id, $subtopics_id);

		} catch (Exception $e) {
			return false;
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

	function set_material_acces_by_lesson_progress() {
		GLOBAL $connect;

		try {
			$query = "SELECT lp.id, lp.subtopic_id, lp.group_info_id
						FROM lesson_progress lp
						WHERE DATE_FORMAT(lp.created_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')";
			$row = mysqli_query($connect, $query);

			while ($value = mysqli_fetch_assoc($row)) {
				$group_id = $value['group_info_id'];
				$subtopic_id = $value['subtopic_id'];
				$query = "SELECT gs.id,
								gs.group_info_id,
								gs.start_from,
								gs.student_id,
								gs.status,
								gs.id NOT IN (SELECT tva.group_student_id
												FROM tutorial_video_action tva
												WHERE DATE_FORMAT(tva.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_tva_exists,
								gs.id NOT IN (SELECT tda.group_student_id
												FROM tutorial_document_action tda
												WHERE DATE_FORMAT(tda.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_tda_exists,
								gs.id NOT IN (SELECT eva.group_student_id
												FROM end_video_action eva
												WHERE DATE_FORMAT(eva.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_eva_exists,
								gs.id NOT IN (SELECT mta.group_student_id
												FROM material_test_action mta
												WHERE DATE_FORMAT(mta.accessed_date, '%Y-%m-%d') > DATE_FORMAT(NOW(), '%Y-%m-%d')) AS is_mta_exists
							FROM group_student gs
							WHERE gs.group_info_id = $group_id
								AND gs.is_archive = 0";
				$row2 = mysqli_query($connect, $query);
				$payment_students_count = 0;
				while ($val = mysqli_fetch_assoc($row2)) {
					$lp_id = $value['id'];
					$group_student = $val;
					if ($group_student['start_from'] == 0 || has_access($group_student['group_info_id'], $group_student['start_from'])) {
						$payment = true;
						if ($val['status'] == 'inactive') {
							$payment = use_student_exist_payments($group_student['student_id'], $group_student['id'], $group_student['group_info_id']);
						}
						if ($payment) {
							$payment_students_count++;
							if ($val['status'] == 'waiting' || $val['status'] == 'inactive') {
								echo $group_student['group_info_id'].' '.$group_student['start_from'].' '.$val['id']."<br><br>";
								check_access_until($group_student['group_info_id'], $group_student['start_from'], $val['id']);
							}
							if ($val['is_tva_exists'] && $val['is_tda_exists'] && $val['is_eva_exists'] && $val['is_mta_exists']) {
								set_tutorial_video_action($lp_id, $group_student);
								set_tutorial_document_action($lp_id, $group_student);
								set_end_video_action($lp_id, $group_student);
								set_material_test_action($lp_id, $group_student);
							}
						}
					}
				}

				if ($payment_students_count == 0) {
					$query = "SELECT count(gs.id) AS student_count,
									(SELECT ag.id 
									FROM army_group ag
									WHERE ag.group_info_id = gs.group_info_id) AS army_group_id
								FROM group_student gs
								WHERE gs.group_info_id = $group_id";
					$r = mysqli_query($connect, $query);
					$res = mysqli_fetch_assoc($r);

					if ($res['student_count'] == 1 && $res['student_count'] == '') {
						$query = "DELETE lesson_progress WHERE group_info_id = $group_id AND subtopic_id = $subtopic_id";
						$r = mysqli_query($connect, $query);
					}
				}
			}
			echo "SUCESSFUL";
		} catch (Exception $e) {
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
					$query_new_payment = "INSERT INTO group_student_payment (group_student_id, payed_date, is_used, payment_type, partial_payment_days)
												VALUES ($group_student_id, NOW(), 0, 'balance', $days)";
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

	function set_tutorial_video_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_id = $group_students['id'];
			$sql = "SELECT * FROM tutorial_video_action WHERE lesson_progress_id = $lp_id AND group_student_id = $gs_id";
			$res = mysqli_query($connect, $sql);
			if (mysqli_num_rows($res) == 0) {
				$sql = "INSERT INTO tutorial_video_action (lesson_progress_id, group_student_id) VALUES($lp_id, $gs_id)";
				mysqli_query($connect, $sql);
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function set_tutorial_document_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_id = $group_students['id'];
			$sql = "SELECT * FROM tutorial_document_action WHERE lesson_progress_id = $lp_id AND group_student_id = $gs_id";
			$res = mysqli_query($connect, $sql);
			if (mysqli_num_rows($res) == 0) {
				$sql = "INSERT INTO tutorial_document_action (lesson_progress_id, group_student_id) VALUES($lp_id, $gs_id)";
				mysqli_query($connect, $sql);
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function set_end_video_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_id = $group_students['id'];
			$sql = "SELECT * FROM end_video_action WHERE lesson_progress_id = $lp_id AND group_student_id = $gs_id";
			$res = mysqli_query($connect, $sql);
			if (mysqli_num_rows($res) == 0) {
				$sql = "INSERT INTO end_video_action (lesson_progress_id, group_student_id) VALUES($lp_id, $gs_id)";
				mysqli_query($connect, $sql);
			}
			return true;

		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}

	function set_material_test_action($lp_id, $group_students) {
		GLOBAL $connect;

		try {
			$gs_id = $group_students['id'];
			$sql = "SELECT * FROM material_test_action WHERE lesson_progress_id = $lp_id AND group_student_id = $gs_id";
			$res = mysqli_query($connect, $sql);
			if (mysqli_num_rows($res) == 0) {
				$sql = "SELECT mt.id
						FROM material_test mt,
							lesson_progress lp
						WHERE lp.id = $lp_id
							AND mt.subtopic_id = lp.subtopic_id";
				$res = mysqli_query($connect, $sql);
				$is_finish = 0;
				if (mysqli_num_rows($res) == 0) {
					$is_finish = 1;
				}

				$sql = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, is_finish) VALUES($gs_id, $lp_id, $is_finish)";
				mysqli_query($connect, $sql);
			}
			return true;
		} catch (Exception $e) {
			return false;
			throw $e;
		}
	}


	function archive_group($group_info_id) {
		GLOBAL $connect;

		$status_ids = array('archive' => 4,
							'pre_archive' => 5,
							'active' => 2);

		try {

			$query = "SELECT gi.status_id, gi.status_change_date = DATE_FORMAT(NOW(), '%Y-%m-%d') AS today_changed
						FROM group_info gi
						WHERE gi.id = $group_info_id";

			$result = mysqli_query($connect, $query);
			if (!$result) {
				return false;
			}
			$value = mysqli_fetch_assoc($result);

			$query_set_status_group = "UPDATE group_info SET status_id = %s, status_change_date = NOW() WHERE id = %s";
			$query_archive_group_students = "UPDATE group_student SET status='inactive', is_archive = 1 WHERE group_info_id = %s";
			if ($value['today_changed'] != null && $value['today_changed'] != '') {

				if (!$value['today_changed'] && $value['status_id'] == $status_ids['pre_archive']) {
					$result = mysqli_query($connect, sprintf($query_set_status_group, $status_ids['archive'], $group_info_id));
					if (!$result) {
						return false;
					}
					$result = mysqli_query($connect, sprintf($query_archive_group_students, $group_info_id));
					if (!$result) {
						return false;
					}
				} else if (!$value['today_changed'] && $value['status_id'] == $status_ids['active']) {
					$result = mysqli_query($connect, sprintf($query_set_status_group, $status_ids['pre_archive'], $group_info_id));
					if (!$result) {
						return false;
					}	
				}
			} else {
				$result = mysqli_query($connect, sprintf($query_set_status_group, $status_ids['pre_archive'], $group_info_id));
				if (!$result) {
					return false;
				}
			}

			return true;
			
		} catch (Exception $e) {
			return false;
			throw $e;
		}

	}


	function get_next_group_lesson($group_result, $subject_result){

		$total_result = array();
		$current_date = date('Y-m-d H:i:s');

		foreach ($group_result as $value) {
			if ($value['lp_id'] == null) {
				$topic = array();
				if ($value['lesson_type'] == 'subject') {
					$topic = $subject_result[$value['subject_id']]['topics'][1];
				} else if ($value['lesson_type'] == 'topic') {
					$topic = $subject_result[$value['subject_id']]['topics'][$value['topic_order']];
				}
				// if ($value['group_id'] == 766) {
				// 	echo "<br><br><br>";
				// 	print_r($subject_result[$value['subject_id']]['topics']);
				// 	// print_r($next_subtopic);
				// 	echo '<br><br><br>';
				// }
				if (count($topic['subtopics']) > 0) {
					$next_subtopic = next_subtopics($topic['subtopics'], null, $value['group_id']);
					if (count($next_subtopic) == 0) {
						array_push($total_result, array('subtopic_id' => -1,
														'group_info_id' => $value['group_id'],
														'position' => 31));
					} else {
						$total_result = array_merge($total_result, $next_subtopic);
					}
				}

			} else if ($current_date >= date('Y-m-d H:i:s', strtotime($value['compare_date']))) {
				if ($value['lesson_type'] == 'subject') {
					$topic_order = 0;
					foreach ($subject_result[$value['subject_id']]['topics'] as $t_order => $topic_val) {
						foreach ($topic_val['subtopics'] as $subtopic_val) {
							if ($subtopic_val['id'] == $value['subtopic_id']) {
								$topic_order = $t_order;
								break;
							}
						}
						if ($topic_order != 0) {
							break;
						}
					}

					$subtopics = $subject_result[$value['subject_id']]['topics'][$topic_order]['subtopics'];
					$next_subtopic = next_subtopics($subtopics, $value['subtopic_order'], $value['group_id']);
					if (count($next_subtopic) == 0) {
						$topics = $subject_result[$value['subject_id']]['topics'];
						if (isset($topics[$topic_order + 1])) {
							$subtopics = $topics[$topic_order + 1]['subtopics'];
							$next_subtopic = next_subtopics($subtopics, null, $value['group_id']);
							if (count($next_subtopic) == 0) {
								array_push($total_result, array('subtopic_id' => -1,
																'group_info_id' => $value['group_id'],
																'position' => 32));
							} else {
								$total_result = array_merge($total_result, $next_subtopic);
							}
						} else {
							array_push($total_result, array('subtopic_id' => -1,
															'group_info_id' => $value['group_id'],
															'position' => 33));
						}
					} else {
						$total_result = array_merge($total_result, $next_subtopic);
					}

				} else if ($value['lesson_type'] == 'topic') {
					$subtopics = $subject_result[$value['subject_id']]['topics'][$value['topic_order']]['subtopics'];
					$next_subtopic = next_subtopics($subtopics, $value['subtopic_order'], $value['group_id']);
					if (count($next_subtopic) == 0) {
						array_push($total_result, array('subtopic_id' => -1,
														'group_info_id' => $value['group_id'],
														'position' => 34));
					} else {
						$total_result = array_merge($total_result, $next_subtopic);
					}
				}
			}
		}

		return $total_result;
	}

	function next_subtopics($subtopics, $subtopic_order, $group_id) {
		$result = array();
		if ($subtopic_order == null) {
			$count = count($subtopics);
			if ($count >= 1) {
				array_push($result, array('subtopic_id' => $subtopics[1]['id'],
											'group_info_id' => $group_id,
											'position' => 11));
			}
			if ($count >= 2) {
				array_push($result, array('subtopic_id' => $subtopics[2]['id'],
											'group_info_id' => $group_id,
											'position' => 12));
			}
			if ($count == 3) {
				array_push($result, array('subtopic_id' => $subtopics[3]['id'],
											'group_info_id' => $group_id,
											'position' => 13));
			}
		} else {
			$count = count($subtopics) - $subtopic_order;

			if ($count == 2) {
				array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 1]['id'],
											'group_info_id' => $group_id,
											'position' => 21));

				array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 2]['id'],
											'group_info_id' => $group_id,
											'position' => 22));
			} else if ($count >= 1) {
				array_push($result, array('subtopic_id' => $subtopics[$subtopic_order + 1]['id'],
											'group_info_id' => $group_id,
											'position' => 23));					
			}
		}
		// echo "subtopics: ";
		// print_r($subtopics);
		// echo "<br>";
		// echo "subtopic_order: ".$subtopic_order."<br>";
		// echo "group_id: ".$group_id."<br>results: ";
		// print_r($result);
		// echo "<br>";
		return $result;
	}

	function get_group_info_with_lesson_progress() {
		GLOBAL $connect;

		$dayofweek = date('w') == '0' ? '7' : date('w');
		$sql = "SELECT gi.id AS group_info_id, 
					gi.group_name,
					gi.lesson_type,
					gi.start_date,
					gi.created_date AS group_info_created_date,
					gi.subject_id,
					gi.topic_id,
					t.topic_order,
					lp.id AS lesson_progress_id,
					lp.subtopic_id,
					st.subtopic_order,
					DATE_ADD(DATE_FORMAT(lp.created_date, '%Y-%m-%d %H:%i:%s'), INTERVAL 24 HOUR) AS compare_date
				FROM group_info gi
				INNER JOIN group_schedule gsh
					ON gsh.group_info_id = gi.id
						AND gsh.week_day_id = $dayofweek
				INNER JOIN status
					ON status.id = gi.status_id
						AND status.is_active = 1
				LEFT JOIN lesson_progress lp
					ON lp.id = (SELECT lp2.id
								FROM lesson_progress lp2,
									subtopic st2
								WHERE lp2.group_info_id = gi.id
									AND st2.id  = lp2.subtopic_id
								ORDER BY st2.subtopic_order DESC
								LIMIT 1)
				LEFT JOIN topic t
					ON t.id = gi.topic_id
				LEFT JOIN subtopic st
					ON st.id = lp.subtopic_id
				WHERE gi.start_date <= DATE_FORMAT(NOW(), '%Y-%m-%d')
					AND gi.id NOT IN (SELECT fl.group_info_id
										FROM freeze_lesson fl
										WHERE fl.from_date <= DATE_FORMAT(NOW(), '%Y-%m-%d')
											AND fl.to_date >= DATE_FORMAT(NOW(), '%Y-%m-%d'))
					AND gi.is_freeze = 0
				ORDER BY gi.start_date DESC, gi.id, gsh.week_day_id";

		$group_info_result = array();

		if ($result = mysqli_query($connect, $sql)) {

			while($value = mysqli_fetch_assoc($result)) {
				$group_info_result[$value['group_info_id']] = array('group_id' => intval($value['group_info_id']),
																	'group_name' => $value['group_name'],
																	'lesson_type' => $value['lesson_type'],
																	'subject_id' => $value['subject_id'],
																	'topic_id' => $value['topic_id'],
																	'topic_order' => intval($value['topic_order']),
																	'start_date' => $value['start_date'],
																	'created_date' => $value['group_info_created_date'],
																	'lp_id' => $value['lesson_progress_id'],
																	'subtopic_id' => $value['subtopic_id'],
																	'subtopic_order' => intval($value['subtopic_order']),
																	'compare_date' => $value['compare_date']);
			}
			return $group_info_result;
		} else {
			return array();
		}
	}

	function get_subject_topic_subtopics() {
		GLOBAL $connect;

		$sql = "SELECT sj.id AS subject_id,
					sj.title AS subject_title,
					t.id AS topic_id,
					t.title AS topic_title,
					t.topic_order,
					st.id AS subtopic_id,
					st.title AS subtopic_title,
					st.subtopic_order
				FROM subject sj,
					topic t,
					subtopic st
				WHERE t.subject_id = sj.id
					AND st.topic_id = t.id
				ORDER BY sj.title, t.topic_order, st.subtopic_order";

		$subject_result = array();
		if ($result = mysqli_query($connect, $sql)) {
			while ($value = mysqli_fetch_assoc($result)) {
				$topic_order = intval($value['topic_order']);
				$subtopic_order = intval($value['subtopic_order']);
				if (!isset($subject_result[$value['subject_id']])) {
					$subject_result[$value['subject_id']] = array('id' => $value['subject_id'],
																'title' => $value['subject_title'],
																'topics' => array());
				}

				if (!isset($subject_result[$value['subject_id']]['topics'][$topic_order])) {
					$subject_result[$value['subject_id']]['topics'][$value['topic_order']] = array('id' => $value['topic_id'],
																								'title' => $value['topic_title'],
																								'subtopics' => array());
				}

				$subject_result[$value['subject_id']]['topics'][$topic_order]['subtopics'][$subtopic_order] = array('id' => $value['subtopic_id'],
																													'title' => $value['subtopic_title'],
																													'order' => $subtopic_order);
			}
			return $subject_result;
		} else {
			// print_r(mysqli_error($connect););
			return array();
		}
	}


	$subject = "Төмендегі группадағы оқушыларға тақырыпқа доступ ашылды!";
    $from = $system_mail;
    $to = $super_admin_mail.", ".$developer_mail;
    $body = $html;
    // send_email($subject, $from, $to, $body);
?>




<!--
delete from end_video_action;
delete from tutorial_document_action;
delete from tutorial_video_action;
delete from lesson_progress;
-->