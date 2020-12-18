<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/constants.php');
	include_once($root.'/common/global_controller.php');

	$subject_topics_order = array();

	function get_not_activated_students() {
		GLOBAL $connect;
		try {

			$status_id = 3; // not activated students status

			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name,
											s.school,
											s.class,
											s.phone,
											s.email,
											DATE_FORMAT(s.created_date, '%d.%m.%Y %k:%i') AS created_date
										FROM student s
										WHERE s.status_id = :status_id
										ORDER BY s.created_date DESC");
			$stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_results = $stmt->fetchAll();

			$total_result = array();
			foreach ($student_results as $value) {
				$total_result[$value['id']] = array('full_name' => ($value['last_name']." ".$value['first_name']),
													'phone' => $value['phone'],
													'email' => $value['email'],
													'created_date' => $value['created_date'],
													'school' => $value['school'],
													'class' => $value['class'],
													'courses' => array(),
													'reserves' => array());
			}

			$stmt = $connect->prepare("SELECT rc.id AS course_id,
											gi.id AS group_id,
											gi.group_name,
											rc.student_id,
											st.id AS subtopic_id,
											st.title
										FROM registration_course rc,
											group_info gi,
											subtopic st,
											student s
										WHERE rc.is_done = 0
											AND gi.id = rc.group_info_id
											AND st.id = rc.subtopic_id
											AND s.id = rc.student_id
											AND s.status_id = :status_id
										ORDER BY gi.group_name");
			$stmt->bindParam(":status_id", $status_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			foreach ($sql_res as $val) {
				$total_result[$val['student_id']]['courses'][$val['course_id']] = array("group_id" => $val['group_id'],
																						'group_name' => $val['group_name'],
																						'subtopic_id' => $val['subtopic_id'],
																						'title' => $val['title']);
			}

			$stmt = $connect->prepare("SELECT rr.id AS reserve_id,
											rr.student_id,
											t.id AS topic_id,
											t.title AS topic_title,
											sj.id AS subject_id,
											sj.title AS subject_title
										FROM registration_reserve rr,
											topic t,
											subject sj,
											student s
										WHERE rr.is_done = 0
											AND s.id = rr.student_id
											AND s.status_id = :status_id
											AND t.id = rr.topic_id
											AND sj.id = t.subject_id
										ORDER BY sj.title, t.title");
			$stmt->bindParam(":status_id", $status_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_res = $stmt->fetchAll();

			foreach ($sql_res as $val) {
				$total_result[$val['student_id']]['reserves'][$val['reserve_id']] = array('subject_id' => $val['subject_id'],
																							'subject_title' => $val['subject_title'],
																							'topic_id' => $val['topic_id'],
																							'topic_title' => $val['topic_title']);
			}

			return $total_result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_active_students($limit=0, $offset=0) {
		GLOBAL $connect;
		try {

			$status_id = 2; // active students status

			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name,
											s.phone,
											s.school,
											s.class,
											s.instagram,
											s.created_date,
											s.is_block,
											s.parent_phone,
											DATE_FORMAT(s.created_date, '%d.%m.%Y %k:%i') AS created_date,
											s.password_reset
										FROM student s
										WHERE s.status_id = :status_id
										ORDER BY s.last_name, s.first_name");
										// LIMIT ".($limit * $offset)." , ".$limit
			$stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_results = $stmt->fetchAll();

			$total_result = array();
			$student_ids = array();
			foreach ($student_results as $value) {
				if (!isset($total_result[$value['id']])) {
					array_push($student_ids, $value['id']);
					$total_result[$value['id']] = array('full_name' => ($value['last_name']." ".$value['first_name']),
														'phone' => $value['phone'],
														'parent_phone' => $value['parent_phone'],
														'is_block' => $value['is_block'],
														'instagram' => $value['instagram'],
														'created_date' => $value['created_date'],
														'school' => $value['school'],
														'class' => $value['class'],
														'password_reset' => $value['password_reset'],
														'no_payment_count' => 0,
														'groups' => array(),
														'courses' => array(),
														'reserves' => array(),
														'balances' => array(),
														'total_balance_days' => 0);
				}
			}

			foreach (get_groups_by_student_id($student_ids) as $value) {
				if (!isset($subject_topics_order[$value['subject_id']])) {
					$subjects_order[$value['subject_id']] = get_subtopics_total_order($value['subject_id']);
				}
				$payment = get_next_payment_price($value['group_id'], $value['group_student_id'], $subjects_order[$value['subject_id']],
												$value['start_date'], $value['start_from']);
				$is_army_group = get_is_army_group($value['group_id']);
				$subtopic_title = $value['start_from'] == 0 ? 'Басынан бастайды' : $value['subtopic_title'];
				$total_result[$value['student_id']]['groups'][$value['group_id']] = array('group_name' => $value['group_name'],
																							'has_payment' => $value['has_payment'],
																							'payment' => $payment,
																							'status' => $value['status'],
																							'access_until' => $value['access_until'],
																							'subtopic_title' => $subtopic_title,
																							'group_student_id' => $value['group_student_id'],
																							'is_army_group' => $is_army_group);
				if ($value['status'] == 'inactive') {
					$total_result[$value['student_id']]['no_payment_count']++;
				}

			}

			foreach (get_student_balances($student_ids) as $val) {
				$total_result[$val['student_id']]['balances'][$val['student_balance_id']] = array('group_name' => $val['group_name'],
																									'comment' => $val['comment'],
																									'days' => $val['days']);
				$total_result[$val['student_id']]['total_balance_days'] += $val['days'];
			}

			$_SESSION['student_list'] = $total_result;
			return $total_result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_archive_students($limit=0, $offset=0) {
		GLOBAL $connect;
		try {

			$status_id = 2; // active students status

			$stmt = $connect->prepare("SELECT s.id,
											s.first_name,
											s.last_name,
											s.phone,
											s.school,
											s.class,
											s.instagram,
											s.created_date,
											s.is_block,
											s.parent_phone,
											DATE_FORMAT(s.created_date, '%d.%m.%Y %k:%i') AS created_date,
											s.password_reset
										FROM student s
										WHERE s.status_id = :status_id
											AND DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 3 MONTH) 
													>= (SELECT DATE_FORMAT(gsp.access_until, '%Y-%m-%d')
														FROM group_student_payment gsp,
															group_student gs
														WHERE gs.student_id = s.id
															AND gsp.group_student_id = gs.id
														ORDER BY gsp.access_until DESC
														LIMIT 1)
										ORDER BY s.last_name, s.first_name");
										// LIMIT ".($limit * $offset)." , ".$limit

			// SELECT DATE_FORMAT(gsp.access_until, '%Y-%m-%d')
			// 											FROM group_student_payment gsp
			// 											WHERE gsp.group_student_id IN (SELECT gs.id
			// 																			FROM group_student gs 
			// 																			WHERE gs.student_id = s.id)
			// 											ORDER BY gsp.access_until DESC
			// 											LIMIT 1
			$stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
			$stmt->execute();
			$student_results = $stmt->fetchAll();

			$total_result = array();
			$student_ids = array();
			foreach ($student_results as $value) {
				if (!isset($total_result[$value['id']])) {
					array_push($student_ids, $value['id']);
					$total_result[$value['id']] = array('full_name' => ($value['last_name']." ".$value['first_name']),
														'phone' => $value['phone'],
														'parent_phone' => $value['parent_phone'],
														'is_block' => $value['is_block'],
														'instagram' => $value['instagram'],
														'created_date' => $value['created_date'],
														'school' => $value['school'],
														'class' => $value['class'],
														'password_reset' => $value['password_reset'],
														'balances' => array(),
														'total_balance_days' => 0);
				}
			}

			foreach (get_student_balances($student_ids) as $val) {
				$total_result[$val['student_id']]['balances'][$val['student_balance_id']] = array('group_name' => $val['group_name'],
																									'comment' => $val['comment'],
																									'days' => $val['days']);
				$total_result[$val['student_id']]['total_balance_days'] += $val['days'];
			}

			$_SESSION['student_list'] = $total_result;
			return $total_result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_student_balances($student_ids) {
		GLOBAL $connect;

		try {

			if (count($student_ids) == 0) {
				$students_str = 0;
			} else {
				$students_str = implode(',', $student_ids);
			}

			$stmt = $connect->prepare("SELECT sb.id AS student_balance_id,
											sb.student_id,
											sb.days,
											sb.comment,
											gi.group_name
										FROM student_balance sb,
											group_info gi
										WHERE sb.is_used = 0
											AND gi.id = sb.group_id
											AND sb.student_id IN ($students_str)");
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_groups_by_student_id($student_ids) {
		GLOBAL $connect;

		try {
			if (count($student_ids) == 0) {
				$students_str = 0;
			} else {
				$students_str = implode(',', $student_ids);
			}

			$stmt = $connect->prepare("SELECT gi.id AS group_id,
											gi.group_name,
											gi.subject_id,
											gi.start_date,
											gs.student_id,
											gs.has_payment,
											gs.status,
											gs.id AS group_student_id,
											gs.access_until,
											gs.start_from,
											(SELECT st2.title FROM subtopic st2 WHERE id = gs.start_from) AS subtopic_title
										FROM group_info gi,
											group_student gs
										WHERE gs.student_id IN ($students_str)
											AND gi.id = gs.group_info_id
											AND gs.is_archive = 0
											AND gi.status_id = 2"); // temp script change status part
			$stmt->execute();
			return $stmt->fetchAll();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_start_date_by_group_and_subtopic($group_id, $start_date, $subtopic_id, $subtopic_order, $lesson_type) {
		GLOBAL $connect;

		try {
			$stmt = $connect->prepare("SELECT gsh.week_day_id
										FROM group_schedule gsh
										WHERE gsh.group_info_id = :group_id");
			$stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$schedules = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($schedules, intval($value['week_day_id']));
			}

			if ($lesson_type == 'subject') {
				$stmt = $connect->prepare("SELECT t.topic_order, t.subject_id
											FROM topic t,
												subtopic st
											WHERE st.id = :subtopic_id
												AND t.id = st.topic_id");
				$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
				$stmt->execute();
				$last_topic = $stmt->fetch(PDO::FETCH_ASSOC);

				$stmt = $connect->prepare("SELECT count(st.id) - 2 AS subtopic_count
											FROM subtopic st
											WHERE st.topic_id IN ((SELECT t2.id
																	FROM topic t2
																	WHERE t2.subject_id = :subject_id
																		AND t2.topic_order < :topic_order))
											GROUP BY st.topic_id");
				$stmt->bindParam(':subject_id', $last_topic['subject_id'], PDO::PARAM_INT);
				$stmt->bindParam(':topic_order', $last_topic['topic_order'], PDO::PARAM_INT);
				$stmt->execute();
				$sql_res = $stmt->fetchAll();

				$total_count = $subtopic_order == 1 ? 1 : $subtopic_order - 1;
				foreach ($sql_res as $value) {
					$total_count += $value['subtopic_count'];
				}

				$date = strtotime($start_date);
				for ($i=0; $i < $total_count; $i++) { 
					while (!in_array(get_week_day($date), $schedules)) {
						$date = increase_to_one_day($date);
					}
				}
				return date('d.m.Y', $date);
			} else if ($lesson_type == 'topic') {
				$total_count = $subtopic_order == 1 ? 1 : $subtopic_order - 1;

				$date = strtotime($start_date);
				for ($i=0; $i < $total_count; $i++) { 
					while (!in_array(get_week_day($date), $schedules)) {
						$date = increase_to_one_day($date);
					}
				}
				return date('d.m.Y', $date);
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_week_day($datetime) {
		return intval(date('N', $datetime));
	}
	function increase_to_one_day($datetime) {
		return strtotime(date('Y-m-d', $datetime).' + 1 day');
	}


	//depreciated
	function get_active_students_as_html($offset) {
		$limit = 50;

		$student_result = get_active_students($limit, $offset);

		$html = "<table class='table table-striped table-bordered'>";
		$count = 0;
		foreach ($student_result as $id => $value) {
			$html .= get_tr_for_active_students_html_pattern($id, ++$count, $value['full_name'], $value['school'], $value['class'],
															$value['phone'], $value['created_date'],
															$value['password_reset'], $value['groups'], $value['courses'], $value['reserves']);
		}
		$html .= "</table>";
		return $html;
	}

	//deprecitated
	function get_tr_for_active_students_html_pattern($student_id, $count, $full_name, $school, $class,
													$phone_number, $created_date, $password_reset, $groups,
													$courses, $reserves) {
		$no_payment_count = '';
		$html = "<tr>";
			$html .="<td style='width: 10px;' class='count'><center>%d</center></td>";	// count

			$html .= "<td>";
				$html .= "<div class='row' style='padding: 0px 20px;'>";
					$html .= "<div class='short_info std-info'>";
						$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
							$html .= "<span class='label label-warning reserve-count'>%s</span>";		// number of reserves		
							$html .= "&nbsp;";
							$html .= "<span class='label label-danger payment-count'>%s</span>";		// number of no pay
							$html .= "&nbsp;&nbsp;";
							$html .= "<a class='cursor-pointer'>%s &nbsp; &nbsp; %s</a>"; // last_name + first_name // phone
						$html .= "</div>";
						$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
							$html .= "<div class='pull-right password'>";
							if ($password_reset == 0) {
								$html .= "<div class='pull-right'><button class='btn btn-xs btn-info reset-password-btn' data-id='".$student_id."'>Сбросить пароль</button></div>";
							} else {
								$html .= "<div class='password pull-right'><i class='text-warning'>Пароль: <b>12345</b></i></div>";
							}
							$html .= "</div>";
						$html .= "</div>";
					$html .= "</div>";
				$html .= "</div><div class='row' style='padding: 0px 20px;'>";
					$html .= "<div class='full-info hide' style='border-top: 1px solid lightgray;'>";
						$html .= "<div class='extra-info'>";
							$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
								$html .= "<div class='student_info'>";
									$html .= "<p>%s мектеп. %s сынып.</p>"; 				// school & class
									$html .= "<p>Тіркелген күні: %s</p>"; 					// created_date
									$html .= "<p>+7 %s</p>";								// phone number
								$html .= "</div>";
								$html .= "<div class='group-info'>";
									$html .= "<b>Оқушының қатысатын группалары: </b>";
									$html .= "<ol>";
										foreach ($groups as $id => $value) {
											$html .= "<li>";
												$html .= "<span data-id='".$id."'><a href='?page=group&group=".$id."' target='_blank'>".$value['group_name']."</a> | ".$value['subtopic_title']."</span>";
												// if (strtotime($value['access_until']) >= strtotime('now')) {
												if ($value['status'] == 'active' || $value['status'] == 'waiting') {
													$html .= "<span class='tmp'style='margin-left: 10px;'><span style='color: #5cb85c; '>Оплатасы өткізілді<span></span>";
													// $html .= "<button class='btn btn-xs btn-danger remove-lesson' data-type='group' data-id='".$value['group_student_id']."' style='margin-left: 10px;'>Өшіру</button>";
												} else {
													$no_payment_count++;
													$html .= "<button class='btn btn-xs btn-default start-lesson' data-type='group' data-id='".$value['group_student_id']."' style='margin-left: 10px;'>Төлемі жоқ</button>";
													$html .= "<span class='tmp'style='margin-left: 10px;'></span>";
													// $html .= "<button class='btn btn-xs btn-danger remove-lesson' data-type='group' data-id='".$value['group_student_id']."' style='margin-left: 10px;'>Өшіру</button>";
												}
											$html .= "</li>";
										}
										foreach ($courses as $id => $val) {
											$no_payment_count++;
											$html .= "<li>";
												$html .= "<span>".$val['group_name']." : ".$val['start_date']." (".$val['subtopic_title'].")</span>";
												$html .= "<button class='btn btn-xs btn-default start-lesson' data-type='course' data-id='".$id."' style='margin-left: 10px;'>Төлемі жоқ</button>";
												$html .= "<span class='tmp'style='margin-left: 10px;'></span>";
												// $html .= "<button class='btn btn-xs btn-danger remove-lesson' data-type='course' data-id='".$id."' style='margin-left: 10px;'>Өшіру</button>";
											$html .= "</li>";
										}
									$html .= "</ol>";
									if (count($reserves) > 0) {
										$html .= "<i>Резерв</i>";
										foreach ($reserves as $id => $value) {
											$html .= "<p>".$value['subject_title']." : ".$value['topic_title']."</p>";
										}
									}
								$html .= "</div>";
							$html .= "</div>";
						$html .= "</div>";
					$html .= "<div>";
				$html .= "</div>";
			$html .= "</td>";

			$html .= "<td><div>";
				$html .= "<button class='btn btn-xs btn-default show-student-info' title='Оқушының анкетасы'><span class='glyphicon glyphicon-align-justify'></span></button>";
			$html .= "</div></td>";

		$html .= "</tr>";
		$reserve_count = count($reserves) == 0 ? '' : count($reserves);
		return sprintf($html, $count, $reserve_count, $no_payment_count, $full_name, $phone_number, $school, $class, $created_date, $phone_number);
	}


	function get_next_payed_payment($group_student_id) {
		GLOBAL $connect;
		GLOBAL $one_lesson_price;
		GLOBAL $full_lesson_price;

		try {

			$query = "SELECT gsp.partial_payment_days,
							DATE_FORMAT(gsp.payed_date, '%d.%m.%Y') AS payed_date,
							gsp.id
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
							AND gsp.is_used = 0
							AND gsp.payment_type = :payment_type
						ORDER BY gsp.payed_date DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindValue(':payment_type', 'money', PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$query_count = $stmt->rowCount();

			$result = array();
			if ($query_count > 0) {
				$result = array('payed_date' => $query_result['payed_date'],
							'price' => ($query_result['partial_payment_days'] == ''
										? $full_lesson_price
										: $query_result['partial_payment_days'] * $one_lesson_price),
							'days' => ($query_result['partial_payment_days'] == '' ? 'full' : $query_result['partial_payment_days']));
			}
			return $result;
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function get_next_payment_details($group_student_id) {
		GLOBAL $connect;
		GLOBAL $one_lesson_price;
		GLOBAL $full_lesson_price;

		try {

			$active_payment_query = "SELECT gsp.id,
											gsp.access_until
										FROM group_student_payment gsp
										WHERE gsp.group_student_id = :group_student_id
											AND gsp.is_used = 1
											AND gsp.full_finished IS NULL";
			$stmt = $connect->prepare($active_payment_query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$active_payment_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$subject_info_query = "SELECT gi.subject_id,
										gi.topic_id,
										(SELECT st.id FROM subtopic st WHERE st.topic_id = gi.topic_id AND st.subtopic_order = 1) AS start_subtopic_id,
										gs.group_info_id
									FROM group_student gs,
										group_info gi
									WHERE gs.id = :group_student_id
										AND gi.id = gs.group_info_id";
			$stmt = $connect->prepare($subject_info_query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$subject_info_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$subjects_element = get_subtopics_total_order($subject_info_result['subject_id']);

			$last_lesson_progress_info_query = "SELECT lp.id,
													lp.subtopic_id,
													st.subtopic_order,
													lp.created_date
												FROM lesson_progress lp,
													group_student gs,
													subtopic st
												WHERE gs.id = :group_student_id
													AND lp.group_info_id = gs.group_info_id
													AND st.id = lp.subtopic_id
												ORDER BY lp.created_date DESC
												LIMIT 1";
			$stmt = $connect->prepare($last_lesson_progress_info_query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$last_lesson_progress_info_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$last_lesson_progress_info_row_count = $stmt->rowCount();

			// if ($subjects_element['content_subtopic'][$last_lesson_progress_info_result['subtopic_id']] == $last_lesson_progress_info_result['subtopic_order']) {
			// 	$last_lesson_progress_info_result['subtopic_order'] = $last_lesson_progress_info_result['subtopic_order'] - 2;
			// } else {
			// 	$last_lesson_progress_info_result['subtopic_order']--;
			// }
			if ($last_lesson_progress_info_row_count == 0) {
				$count_till_end = $subjects_element['total_order'] - $subjects_element['content_subtopic'][$subject_info_result['start_subtopic_id']]['order_from_start'];
			} else {
				$count_till_end = $subjects_element['total_order'] - $subjects_element['content_subtopic'][$last_lesson_progress_info_result['subtopic_id']]['order_from_start'];
			}

			$group_schedule = get_group_schedules($subject_info_result['group_info_id']);

			$get_last_lesson_date = get_end_date_by_days($last_lesson_progress_info_result['created_date'], $count_till_end, $group_schedule);
				
			$last_payment_day = strtotime($active_payment_result['access_until']);
			$last_payment_day_plus_month = strtotime(date('Y-m-d', strtotime('+1 month', $last_payment_day)));
			$last_lesson_day = strtotime($get_last_lesson_date);

			$result = array('may_payment' => false,
							'payment_days' => 0);

			if ($last_payment_day_plus_month <= $last_lesson_day) {
				$result = array('may_payment' => true,
								'payment_days' => 'full',
								'last_lesson_day' => date('Y-m-d', $last_lesson_day),
								'last_payment_day_plus_month' => date('Y-m-d', $last_payment_day_plus_month));
			} else if ($last_payment_day < $last_lesson_day) {
				$date1 = date_create($active_payment_result['access_until']);
				$date2 = date_create($get_last_lesson_date);
				$left_days = date_diff($date1, $date2)->format('%a');
				$result = array('may_payment' => true,
								'payment_days' => $left_days);
			}
			return $result;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
?>