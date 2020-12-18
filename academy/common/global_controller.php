<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/constants.php');

	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$promo_code_chars = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
	$digit_chars = '0123456789';

	function generate_string($input, $max = 25) {
		$input_length = strlen($input) - 1;
	    $random_string = '';
	    for($i = 0; $i < $max; $i++) {
	        $random_character = $input[mt_rand(0, $input_length)];
	        $random_string .= $random_character;
	    }
	 
	    return $random_string;
	}

	function set_generated_promo_code ($student_id, $chars = '', $max = 6) {
		GLOBAL $promo_code_chars;
		GLOBAL $connect;

		try {
			if ($chars == "") {
				$chars = $promo_code_chars;
			}

			$promo_code = generate_string($chars, $max);
			$query = "SELECT count(spc.id) AS promo_code_count
						FROM student_promo_code spc
						WHERE spc.code = :promo_code";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':promo_code', $promo_code, PDO::PARAM_STR);
			$stmt->execute();
			$promo_code_count = $stmt->fetch(PDO::FETCH_ASSOC)['promo_code_count'];

			if ($promo_code_count == 0) {
				$query = "SELECT count(spc.id) AS student_already_exists
							FROM student_promo_code spc
							WHERE spc.student_id = :student_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->execute();
				$student_already_exists = $stmt->fetch(PDO::FETCH_ASSOC)['student_already_exists'];

				if ($student_already_exists == 0) {
					$query = "INSERT INTO student_promo_code (student_id, code) VALUES (:student_id, :promo_code)";
					$stmt = $connect->prepare($query);
					$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
					$stmt->bindParam(':promo_code', $promo_code, PDO::PARAM_STR);
					$stmt->execute();
				}
				return true;
			}
			return set_generated_promo_code($student_id, $chars, $max);

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_holiday () {
		GLOBAL $connect;

		try {

			$query = "SELECT h.title,
							h.comment
						FROM holidays h
						WHERE DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN DATE_FORMAT(h.from_date, '%Y-%m-%d')
																	AND DATE_FORMAT(h.to_date, '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$holiday_info = array('title' => $query_result['title'],
									'comment' => $query_result['comment']);

			return $holiday_info;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function check_current_student_token() {
		GLOBAL $connect;
		GLOBAL $block_count;

		try {
			
			if (isset($_SESSION['is_staff']) && $_SESSION['is_staff'] == 1) {
				return true;
			}
			
			$id = $_SESSION['user_id'];
			$session_token = $_SESSION['token'];
			$uid = $_SESSION['uid'];

			$stmt = $connect->prepare("SELECT count(id) AS student_exist FROM student WHERE id = :id AND token = :token");
			$stmt->bindParam(':token', $session_token, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$student_exist = $stmt->fetch(PDO::FETCH_ASSOC)['student_exist'];

			if ($student_exist == 1 && $uid == get_user_unique_id()) {
				// $stmt = $connect->prepare("SELECT s.block_count,
				// 								s.is_block,
				// 								(DATE_ADD(s.last_login_time, INTERVAL 1 HOUR) <= NOW()) AS reset_block_count
				// 							FROM student s
				// 							WHERE s.id = :id");
				// $stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// $stmt->execute();
				// $student_info = $stmt->fetch(PDO::FETCH_ASSOC);
				// if ($student_info['is_block'] == 0 && $student_info['reset_block_count']) {
				// 	$stmt = $connect->prepare("UPDATE student SET block_count = 0 WHERE id = :id");
				// 	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// 	$stmt->execute();
				// }
				return true;
			} else {
				// $stmt = $connect->prepare("SELECT s.block_count,
				// 								s.is_block,
				// 								(DATE_ADD(s.last_login_time, INTERVAL 1 HOUR) <= NOW()) AS reset_block_count
				// 							FROM student s
				// 							WHERE s.id = :id");
				// $stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// $stmt->execute();
				// $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

				// if ($student_info['is_block'] == 0) {
				// 	if ($student_info['reset_block_count']) {
				// 		$stmt = $connect->prepare("UPDATE student SET block_count = 0 WHERE id = :id");
				// 		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// 		$stmt->execute();
				// 	} else {
				// 		if ($student_info['block_count'] + 1 < $block_count) {
				// 			$stmt = $connect->prepare("UPDATE student SET block_count = block_count + 1 WHERE id = :id");
				// 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// 			$stmt->execute();
				// 		} else {
				// 			$stmt = $connect->prepare("UPDATE student SET token = NULL, block_count = block_count + 1, is_block = 1 WHERE id = :id");
				// 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				// 			$stmt->execute();
				// 		}
				// 	}
				// }

				return false;
			}
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_user_unique_id() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	   		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}

		$uid = md5($_SERVER['HTTP_USER_AGENT'].$ip);
		return $uid;
	}

	function get_material_action_log ($material_id, $material_action_id, $obj) {
		GLOBAL $connect;

		try {

			if ($obj == 'tutorial_video') {
				$sql = "SELECT tval.opened_date AS opened_time,
							DATE_FORMAT(tval.opened_date, '%d.%m.%Y %H:%i:%s') AS formatted_opened_time,
							NOW() <= tval.access_before AS is_access,
							tval.access_before,
							DATE_FORMAT(tval.access_before, '%H:%i:%s') AS formatted_access_before
						FROM tutorial_video_action_log tval
						WHERE tval.tutorial_video_id = :obj_id
							AND tval.tutorial_video_action_id = :obj_action_id";
			} else if ($obj == 'tutorial_document') {
				$sql = "SELECT tdal.opened_date AS opened_time,
							DATE_FORMAT(tdal.opened_date, '%d.%m.%Y %H:%i:%s') AS formatted_opened_time,
							NOW() <= tdal.access_before AS is_access,
							tdal.access_before,
							DATE_FORMAT(tdal.access_before, '%H:%i:%s') AS formatted_access_before
						FROM tutorial_document_action_log tdal
						WHERE tdal.tutorial_document_id = :obj_id
							AND tdal.tutorial_document_action_id = :obj_action_id";
			} else if ($obj == 'end_video') {
				$sql = "SELECT eval.opened_date AS opened_time,
							DATE_FORMAT(eval.opened_date, '%d.%m.%Y %H:%i:%s') AS formatted_opened_time,
							NOW() <= eval.access_before AS is_access,
							eval.access_before,
							DATE_FORMAT(eval.access_before, '%H:%i:%s') AS formatted_access_before
						FROM end_video_action_log eval
						WHERE eval.end_video_id = :obj_id
							AND eval.end_video_action_id = :obj_action_id";
			} else {
				return array();
			}

			$stmt = $connect->prepare($sql);
			$stmt->bindParam(":obj_id", $material_id, PDO::PARAM_INT);
			$stmt->bindParam(":obj_action_id", $material_action_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
			$sql_row_num = $stmt->rowCount();

			$result = array("opened_time" => $sql_result['opened_time'],
							"formatted_opened_time" => $sql_result['formatted_opened_time'],
							"is_access" => $sql_row_num == 0 ? 1 : intval($sql_result['is_access']),
							"access_before" => $sql_result['access_before'],
							"formatted_access_before" => $sql_result['formatted_access_before']);

			return $result;
			
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function get_subtopics_total_order($subject_id) {
		GLOBAL $connect;

		try {
			// 16
			$query = "SELECT t.id,
							count(st.id) AS subtopic_count
						FROM subject sj,
							topic t,
							subtopic st
						WHERE sj.id = :subject_id
							AND t.subject_id = sj.id
							AND st.topic_id = t.id
						GROUP BY t.id
						ORDER BY t.topic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_subtopics_count = $stmt->fetchAll();

			$subtopics_count = array();
			foreach ($query_subtopics_count as $v) {
				$subtopics_count[$v['id']] = $v['subtopic_count'];
			}

			$query = "SELECT sj.id AS subject_id,
							t.id AS topic_id,
							t.topic_order,
							st.id AS subtopic_id,
							st.subtopic_order
						FROM subject sj,
							topic t,
							subtopic st
						WHERE sj.id = :subject_id
							AND t.subject_id = sj.id
							AND st.topic_id = t.id
						ORDER BY t.topic_order, st.subtopic_order";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetchAll();

			$result = array('content_topic' => array(),
							'content_subtopic' => array(),
							'total_order' => 0);

			$sub_order = 0;
			$order = 0;
			foreach ($sql_result as $v) {
				if (!isset($result['content'][$v['topic_id']])) {
					$result['content'][$v['topic_id']] = array('topic_id' => $v['topic_id'],
																'topic_order' => $v['topic_order'],
																'subtopic_sub_count' => 0,
																'subtopic' => array());
					$sub_order = 0;
				}
				if ($v['subtopic_order'] == 1 || $v['subtopic_order'] == $subtopics_count[$v['topic_id']]) {
					$sub_order++;
					$order--;
				}
				$result['content'][$v['topic_id']]['subtopic'][$v['subtopic_id']] = array('subtopic_id' => $v['subtopic_id'],
																						'subtopic_order' => $v['subtopic_order'],
																						'sub_order' => $v['subtopic_order'] - $sub_order);
				$result['content_subtopic'][$v['subtopic_id']] = array('subtopic_id' => $v['subtopic_id'],
																		'subtopic_order' => $v['subtopic_order'],
																		'sub_order' => $v['subtopic_order'] - $sub_order,
																		'order_from_start' => ++$order);
			}
			$count = 0;
			foreach ($result['content'] as $key => $val) {
				$count += count($val['subtopic']) - 2;
				$result[$key]['subtopic_sub_count'] = $count;
			}
			$result['total_order'] = $count;

			return $result;
			
		} catch (Exception $e) {
			return array();
		}
	}

	function get_end_date_by_days($start_date, $days, $schedules) {
		
		$d = new DateTime($start_date);
	    $t = $d->getTimestamp();
	    for($i = 0; $i < $days; $i++){
	        $addDay = 86400;
	        $nextDay = date('w', ($t+$addDay));

	        $week_day_id = ($nextDay == 0) ? 7 : $nextDay;
	        if(count($schedules) > 0 && !in_array($week_day_id, $schedules)) {
	            $i--;
	        }
	        $t = $t+$addDay;
	    }

	    $d->setTimestamp($t);

	    return $d->format('d.m.Y');
	}

	function get_last_lesson($group_info_id, $group_student_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT (SELECT CONCAT(DATE_FORMAT(eva.accessed_date, '%Y-%m-%d'), '!', lp.subtopic_id)
								FROM end_video_action eva,
									lesson_progress lp
								WHERE eva.group_student_id = :group_student_id
									AND eva.lesson_progress_id = lp.id
									AND lp.group_info_id = :group_info_id
								ORDER BY eva.accessed_date DESC
								LIMIT 1) AS eva,
							(SELECT CONCAT(DATE_FORMAT(tva.accessed_date, '%Y-%m-%d'), '!', lp.subtopic_id)
								FROM tutorial_video_action tva,
									lesson_progress lp
								WHERE tva.group_student_id = :group_student_id
									AND tva.lesson_progress_id = lp.id
									AND lp.group_info_id = :group_info_id
								ORDER BY tva.accessed_date DESC
								LIMIT 1) AS tva,
							(SELECT CONCAT(DATE_FORMAT(tda.accessed_date, '%Y-%m-%d'), '!', lp.subtopic_id)
								FROM tutorial_document_action tda,
									lesson_progress lp
								WHERE tda.group_student_id = :group_student_id
									AND tda.lesson_progress_id = lp.id
									AND lp.group_info_id = :group_info_id
								ORDER BY tda.accessed_date DESC
								LIMIT 1) AS tda,
							(SELECT CONCAT(DATE_FORMAT(mta.accessed_date, '%Y-%m-%d'), '!', lp.subtopic_id)
								FROM material_test_action mta,
									lesson_progress lp
								WHERE mta.group_student_id = :group_student_id
									AND mta.lesson_progress_id = lp.id
									AND lp.group_info_id = :group_info_id
								ORDER BY mta.accessed_date DESC
								LIMIT 1) AS mta";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);

			$last_date = strtotime('- 1 month');
			$last_subtopic_id = 0;

			$eva = explode('!', $sql_result['eva']);
			$tva = explode('!', $sql_result['tva']);
			$tda = explode('!', $sql_result['tda']);
			$mta = explode('!', $sql_result['mta']);

			if ($tva[0] != '' && strtotime($tva[0]) > $last_date) {
				$last_date = strtotime($tva[0]);
				$last_subtopic_id = $tva[1];
			}
			if ($tda[0] != '' && strtotime($tda[0]) > $last_date) {
				$last_date = strtotime($tda[0]);
				$last_subtopic_id = $tda[1];
			}
			if ($eva[0] != '' && strtotime($eva[0]) > $last_date) {
				$last_date = strtotime($eva[0]);
				$last_subtopic_id = $eva[1];
			}
			if ($mta[0] != '' && strtotime($mta[0]) > $last_date) {
				$last_date = strtotime($mta[0]);
				$last_subtopic_id = $mta[1];
			}
			$last_date = strtotime(date('Y-m-d', $last_date).' + 1 days');
			return array(date('Y-m-d', $last_date), $last_subtopic_id);

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_group_schedules($group_info_id) {
		GLOBAL $connect;

		try {

			$stmt = $connect->prepare("SELECT gsh.week_day_id
										FROM group_schedule gsh
										WHERE gsh.group_info_id = :group_info_id");
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$schedules = array();
			foreach ($stmt->fetchAll() as $value) {
				array_push($schedules, intval($value['week_day_id']));
			}
			sort($schedules);
			return $schedules;
		} catch (Exception $e) {
			return array();
		}
	}

	;
	function get_next_payment_price($group_info_id, $group_student_id, $subject_topics_order, $group_start_date, $start_from) {
		GLOBAL $connect;
		GLOBAL $full_lesson_price;
		GLOBAL $one_lesson_price;
		GLOBAL $full_army_lesson_price;
		GLOBAL $one_army_lesson_price;

		try {
			$schedules = get_group_schedules($group_info_id);

			if (count($schedules) == 6) {
				$one_academy_lesson_price = $one_lesson_price * 2;
				$full_academy_lesson_price = $full_lesson_price * 2;
			} else {
				$one_academy_lesson_price = $one_lesson_price;
				$full_academy_lesson_price = $full_lesson_price;
			}


			$is_army_group = get_is_army_group($group_info_id);
			if ($is_army_group) {
				$full_month = $full_army_lesson_price;
				$one_day = $one_army_lesson_price;
			} else {
				$full_month = $full_academy_lesson_price;
				$one_day = $one_academy_lesson_price;
			}
			$result = array('type' => '',
							'sum' => $full_month,
							'start_date' => null,
							'days' => null);

			$last_lesson = get_last_lesson($group_info_id, $group_student_id);
			$start_date = $last_lesson[0];
			$last_subtopic_id = $last_lesson[1];
			if ($last_subtopic_id == 0) {
				$last_subtopic_id = $start_from;
				// $start_date = $group_start_date;
				// $start_date = date('Y-m-d');
				$query = "SELECT lp.created_date
							FROM lesson_progress lp,
								subtopic st
							WHERE lp.group_info_id = :group_info_id
								AND st.id = lp.subtopic_id
								AND st.subtopic_order >= (SELECT st2.subtopic_order
															FROM subtopic st2,
																group_student gs
															WHERE gs.id = :group_student_id
																AND st2.id = gs.start_from
																AND st2.subtopic_order)
							ORDER BY st.subtopic_order ASC
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
				$stmt->execute();
				$row_count = $stmt->rowCount();

				if ($row_count == 0) {
					if (strtotime($group_start_date) >= strtotime(date('Y-m-d'))) {
						$start_date = $group_start_date;
					} else {
						$start_date = date('Y-m-d');
					}
				} else {
					$lp_query_result = $stmt->fetch(PDO::FETCH_ASSOC);
					$start_date = $lp_query_result['created_date'];
				}
			}
			if ($last_subtopic_id != 0) {
				$days = $subject_topics_order['total_order']-$subject_topics_order['content_subtopic'][$last_subtopic_id]['order_from_start'];	
			} else {
				$days = $subject_topics_order['total_order'];
			}
			

			$query = "SELECT DATE_FORMAT(gsp.access_until, '%d.%m.%Y') AS access_until
						FROM group_student_payment gsp
						WHERE gsp.group_student_id = :group_student_id
						ORDER BY gsp.payed_date DESC 
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$access_until = $stmt->fetch(PDO::FETCH_ASSOC)['access_until'];

			$result['access_until'] = $access_until;

			if ($days >= 31) {
				$result['type'] = 'full';
				$result['sum'] = $full_month;
				$result['start_date'] = $start_date;
				return $result;
			}

			$end_date = get_end_date_by_days($start_date, $days, $schedules);

			if (strtotime($start_date.' + 1 month') > strtotime($end_date)) {
				$result['type'] = 'partial';
				$date1 = date_create($start_date);
				$date2 = date_create($end_date);
				$days = date_diff($date1, $date2)->format('%a');
				$result['sum'] = $one_day * $days;
				$result['start_date'] = $start_date;
				$result['days'] = $days;
				return $result;
			}
			$result['type'] = 'full';
			$result['sum'] = $full_month;
			$result['start_date'] = $start_date;
		
			return $result;
			
		} catch (Exception $e) {
			return $full_month;
		}
	}

	function get_starting_groups($before_n_days, $topic_id = '') {
		GLOBAL $connect;

		try {

			if ($topic_id == '') {
				$query_groups = "SELECT gi.id,
										gi.status_id,
										gi.group_name,
										gi.topic_id,
										gi.subject_id,
										sj.title
									FROM group_info gi,
										subject sj
									WHERE gi.status_id IN (2)
										AND gi.lesson_type = 'topic'
										AND sj.id = gi.subject_id
										AND gi.start_date < DATE_FORMAT(NOW(), '%Y-%m-%d')";
				$stmt = $connect->prepare($query_groups);
				$stmt->execute();
				$sql_groups = $stmt->fetchAll();
			} else {
				$query_groups = "SELECT gi.id,
										gi.status_id,
										gi.group_name,
										gi.topic_id,
										gi.subject_id,
										sj.title
									FROM group_info gi,
										subject sj
									WHERE gi.status_id IN (2)
										AND gi.lesson_type = 'topic'
										AND gi.topic_id = (SELECT t2.id
															FROM topic t2
															WHERE t2.subject_id = gi.subject_id
																AND t2.topic_order = (SELECT t3.topic_order - 1
																						FROM topic t3
																						WHERE t3.id = :topic_id))
										AND sj.id = gi.subject_id
										AND gi.start_date < DATE_FORMAT(NOW(), '%Y-%m-%d')";
				$stmt = $connect->prepare($query_groups);
				$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
				$stmt->execute();
				$sql_groups = $stmt->fetchAll();
			}

			$result = array();

			foreach ($sql_groups as $v) {
				$next_group = next_group_topic($v, $before_n_days);
				if (count($next_group) > 0) {
					if (!isset($result[$v['subject_id']])) {
						$result[$v['subject_id']] = array('subject_title' => $v['title'],
														'groups' => array());
					}
					array_push($result[$v['subject_id']]['groups'], $next_group);
				}
			}
			return $result;
			
		} catch (Exception $e) {
			// return array();
			throw $e;
		}
	}

	function get_group_last_lesson_info($group_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT DATE_FORMAT(lp.created_date, '%Y-%m-%d') AS last_lesson_date,
							(SELECT count(st2.id) - 1 - st.subtopic_order
							FROM subtopic st2
							WHERE st2.topic_id = st.topic_id) AS subtopic_order_diff
						FROM lesson_progress lp,
							subtopic st
						WHERE lp.group_info_id = :group_info_id
							AND st.id = lp.subtopic_id
						ORDER BY lp.created_date DESC
						LIMIT 1";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_id, PDO::PARAM_INT);
			$stmt->execute();
			$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
			return $sql_result;
			
		} catch (Exception $e) {
			return array();
			throw $e;
		}
	}

	function next_group_topic($group_info, $n_days) {
		GLOBAL $connect;

		try {
			$group_last_lesson_info = get_group_last_lesson_info($group_info['id']);
			if (is_array($group_last_lesson_info) && count($group_last_lesson_info) > 0 && $group_last_lesson_info['subtopic_order_diff'] <= $n_days) {
				$query = "SELECT t.id AS topic_id,
								t.title AS topic_title,
								sj.id AS subject_id,
								sj.title AS subject_title
							FROM topic t,
								subject sj
							WHERE t.subject_id = :subject_id
								AND sj.id = t.subject_id
								AND t.topic_order = (SELECT t2.topic_order + 1
													FROM topic t2
													WHERE t2.id = :topic_id)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':subject_id', $group_info['subject_id'], PDO::PARAM_INT);
				$stmt->bindParam(':topic_id', $group_info['topic_id'], PDO::PARAM_INT);
				$stmt->execute();
				$sql_result = $stmt->fetch(PDO::FETCH_ASSOC);
				$row_count = $stmt->rowCount();
				if ($row_count > 0) {
					$group_prefix = explode('-', $group_info['group_name'])[0];
					$topic_name = explode('-', $sql_result['topic_title'])[1];
					$group_name = implode('-', array($group_prefix, $topic_name));
					$schedules = get_group_schedules($group_info['id']);
					$days = $group_last_lesson_info['subtopic_order_diff'] < 0 ? 1 : ($group_last_lesson_info['subtopic_order_diff'] + 1);
					$start_date = get_end_date_by_days($group_last_lesson_info['last_lesson_date'], $days, $schedules);
					$result = array('group_name' => $group_name,
									'parent_group_id' => $group_info['id'],
									'topic_id' => $sql_result['topic_id'],
									'topic_title' => $sql_result['topic_title'],
									'subject_id' => $sql_result['subject_id'],
									'subject_title' => $sql_result['subject_title'],
									'start_date' => $start_date,
									'subtopic_diff' => $group_last_lesson_info['subtopic_order_diff'] + 1);
					return $result;
				}
				return array();
			}

			return array();

		} catch (Exception $e) {
			return array();
			throw $e;
		}
	}

	function check_is_first_subject_group($student_id, $subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(gs.id) AS c
						FROM group_student gs,
							group_info gi
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND gi.subject_id = :subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];

			return $count == 0 ? true : false;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_subject_schedule_configuration ($subject_id) {
		GLOBAL $connect;

		try {
			$result = array();
			$query = "SELECT ssc.week_day_id
						FROM subject_schedule_configuration ssc
						WHERE ssc.subject_id = :subject_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				array_push($result, $value['week_day_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			return array();
		}
	}

	function get_student_already_started_subject ($student_id, $subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT count(gi.subject_id) AS subjects
						FROM group_info gi,
							group_student gs
						WHERE gs.student_id = :student_id
							AND gi.id = gs.group_info_id
							AND gi.subject_id = :subject_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$subjects_count = $stmt->fetch(PDO::FETCH_ASSOC)['subjects'];

			return $subjects_count == 0 ? false : true;
			
		} catch (Exception $e) {
			return true;
		}
	}

	function create_new_group_with_schedule ($subject_id, $topic_id) {
		GLOBAL $connect; 
		try {
			$subject_schedule_configuration = get_subject_schedule_configuration($subject_id);

			$query = "SELECT t.title AS topic_title
						FROM topic t
						WHERE t.id = :topic_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$topic_title = $stmt->fetch(PDO::FETCH_ASSOC)['topic_title'];

			$week_day_id = date('w') == 0 ? 7 : date('w');
			if (in_array($week_day_id, $subject_schedule_configuration)) {
				$start_date = date('Y-m-d');
			} else {
				if (($week_day_id == 6 && in_array(1, $subject_schedule_configuration))
					|| ($week_day_id == 7 && in_array(2, $subject_schedule_configuration))) {
					$start_date = date('Y-m-d', strtotime('+ 2 days'));
				} else {
					$start_date = date('Y-m-d', strtotime('+ 1 days'));
				}
			}

			$query = "INSERT INTO group_info (subject_id, topic_id, lesson_type, group_name, start_date)
											VALUES (:subject_id, :topic_id, 'topic', :group_name, :start_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_name', $topic_title, PDO::PARAM_STR);
			$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
			$stmt->execute();
			$new_group_info_id = $connect->lastInsertId();
			$query = "INSERT INTO group_schedule (group_info_id, week_day_id) VALUES (:group_info_id, :week_day_id)";

			foreach ($subject_schedule_configuration as $week_day_id) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_info_id', $new_group_info_id, PDO::PARAM_INT);
				$stmt->bindParam(':week_day_id', $week_day_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			return $new_group_info_id;

		} catch (Exception $e) {
			return 0;
		}
	}

	function get_first_second_subtopic_id_by_topic ($topic_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT st.id AS subtopic_id,
							st.subtopic_order
						FROM subtopic st
						WHERE st.topic_id = :topic_id
							AND st.subtopic_order IN (1, 2)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$subtopics = array();
			foreach ($query_result as $value) {
				$subtopics[$value['subtopic_order']] = $value['subtopic_id'];
			}

			return $subtopics;
		} catch (Exception $e) {
			return array();
			// throw $e;
		}
	}

	function insert_student_to_group($student_id, $new_group_info_id, $subject_id, $topic_id) {
		GLOBAL $connect;

		try {
			$student_already_started_subject = get_student_already_started_subject($student_id, $subject_id);

			$group_student_status = $student_already_started_subject ? 'inactive' : 'active';
			$first_second_sutbtopics_id = get_first_second_subtopic_id_by_topic($topic_id);
			if (count($first_second_sutbtopics_id) == 0) {
				return 0;
			}
			$first_subtopic_id = $first_second_sutbtopics_id[1];

			$query = "INSERT INTO group_student (group_info_id, student_id, start_from, status)
											VALUES (:group_info_id, :student_id, :start_from, :status)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $new_group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
			$stmt->bindParam(':start_from', $first_subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':status', $group_student_status, PDO::PARAM_STR);
			$stmt->execute();
			$new_group_student_id = $connect->lastInsertId();

			$current_week_day_id = date('w');
			$free_days = 2;
			if ($current_week_day_id == 6 || $current_week_day_id == 0) {
				$free_days = 3;
			}
			// $free_days = 1;

			if (!$student_already_started_subject) {
				$query = "INSERT INTO student_balance (student_id, used_for_group, is_used, days, comment, used_date)
									VALUES (:student_id, :used_for_group, 1, :days, 'Тегін 2 күндік сабақ', NOW())";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
				$stmt->bindParam(':days', $free_days, PDO::PARAM_INT);
				$stmt->bindParam(':used_for_group', $new_group_info_id, PDO::PARAM_INT);
				$stmt->execute();

				$access_until = date('Y-m-d', strtotime(" + ".$free_days." days"));

				$query = "INSERT INTO group_student_payment
								(group_student_id, payed_date, start_date, access_until, is_used, used_date, payment_type, partial_payment_days)
							VALUES (:group_student_id, NOW(), NOW(), :access_until, 1, NOW(), 'balance', :partial_payment_days)";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':group_student_id', $new_group_student_id, PDO::PARAM_INT);
				$stmt->bindParam(':access_until', $access_until, PDO::PARAM_STR);
				$stmt->bindParam(':partial_payment_days', $free_days, PDO::PARAM_INT);
				$stmt->execute();
			}
			return $new_group_student_id;
		} catch (Exception $e) {
			return 0;
		}
	}

	function insert_into_lesson_progress ($subtopic_id, $group_info_id) {
		GLOBAL $connect;

		try {

			$created_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO lesson_progress (subtopic_id, group_info_id, created_date) VALUES(:subtopic_id, :group_info_id, :created_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
			$stmt->execute();

			$lesson_progress_id = $connect->lastInsertId();

			return $lesson_progress_id;
			
		} catch (Exception $e) {
			return 0;
		}
	}

	function insert_forced_lesson_progress ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$query = "INSERT INTO forced_material_access (lesson_progress_id, group_student_id)
												VALUES (:lesson_progress_id, :group_student_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$forced_material_access_id = $connect->lastInsertId();
			return $forced_material_access_id;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_into_tutorial_video_action ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, accessed_date)
												VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_forced_tutorial_video_action ($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO tutorial_video_action (group_student_id, lesson_progress_id, accessed_date, forced_material_access_id)
												VALUES (:group_student_id, :lesson_progress_id, :accessed_date, :forced_material_access_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_into_tutorial_document_action($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, accessed_date)
													VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_forced_tutorial_document_action($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO tutorial_document_action (group_student_id, lesson_progress_id, accessed_date, forced_material_access_id)
													VALUES (:group_student_id, :lesson_progress_id, :accessed_date, :forced_material_access_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_into_end_video_action ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, accessed_date)
											VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_forced_end_video_action ($group_student_id, $lesson_progress_id, $forced_material_access_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO end_video_action (group_student_id, lesson_progress_id, accessed_date, forced_material_access_id)
											VALUES (:group_student_id, :lesson_progress_id, :accessed_date, :forced_material_access_id)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->bindParam(':forced_material_access_id', $forced_material_access_id, PDO::PARAM_INT);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function insert_into_material_test_action ($group_student_id, $lesson_progress_id) {
		GLOBAL $connect;

		try {

			$accessed_date = date('Y-m-d').' 07:00:00';
			$query = "INSERT INTO material_test_action (group_student_id, lesson_progress_id, accessed_date)
												VALUES (:group_student_id, :lesson_progress_id, :accessed_date)";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->bindParam(':lesson_progress_id', $lesson_progress_id, PDO::PARAM_INT);
			$stmt->bindParam(':accessed_date', $accessed_date, PDO::PARAM_STR);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	function get_has_grant_to_access ($group_info_id) {
		GLOBAL $connect;

		try {

			// $holiday = get_holiday();
			// if ($holiday['title'] == '' && $holiday['title'] == '') {
			$query = "SELECT gs.week_day_id
						FROM group_schedule gs
						WHERE gs.group_info_id = :group_info_id";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$schedules = array();
			$week_day_id = date('w') == 0 ? 7 : date('w');
			foreach ($query_result as $value) {
				array_push($schedules, $value['week_day_id']);
			}
			return in_array($week_day_id, $schedules);
			// }
			// return false;
		} catch (Exception $e) {
			return false;
		}
	}

	function get_group_student_status ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.status
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC)['status'];
			
		} catch (Exception $e) {
			return 'inactive';
		}
	}

	function set_lesson_access ($group_info_id, $group_student_id, $topic_id) {
		GLOBAL $connect;

		try {
			$has_grant_to_access = get_has_grant_to_access($group_info_id);
			// $has_grant_to_access = true;
			// if ($has_grant_to_access) {
			$first_second_sutbtopics_id = get_first_second_subtopic_id_by_topic($topic_id);
			$group_student_status = get_group_student_status($group_student_id);
			foreach ($first_second_sutbtopics_id as $subtopic_order => $subtopic_id) {
				$lesson_progress_id = insert_into_lesson_progress($subtopic_id, $group_info_id, $group_student_id);

				if ($lesson_progress_id != 0 && $group_student_status == 'active') {
					insert_into_tutorial_video_action($group_student_id, $lesson_progress_id);
					insert_into_tutorial_document_action($group_student_id, $lesson_progress_id);
					insert_into_end_video_action($group_student_id, $lesson_progress_id);
					insert_into_material_test_action($group_student_id, $lesson_progress_id);
				}
				if (!$has_grant_to_access) {
					$forced_material_access_id = insert_forced_lesson_progress($group_student_id, $lesson_progress_id);
					insert_forced_tutorial_video_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
					insert_forced_tutorial_document_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
					insert_forced_end_video_action($group_student_id, $lesson_progress_id, $forced_material_access_id);
				}
			}
			// }
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function create_new_group_for_student ($student_id, $new_group_infos) {
		GLOBAL $connnect;

		try {

			foreach ($new_group_infos as $value) {
				$subject_id = $value['subject_id'];
				$topic_id = $value['topic_id'];
				$new_group_info_id = create_new_group_with_schedule($subject_id, $topic_id);
				if ($new_group_info_id != 0) {
					$new_group_student_id = insert_student_to_group($student_id, $new_group_info_id, $subject_id, $topic_id);
					if ($new_group_student_id != 0 && !(date('H') >= 0 && date('H') < 7)) {
						set_lesson_access($new_group_info_id, $new_group_student_id, $topic_id);
					}
				}
			}

			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function post_without_wait_curl($url, $params) {

	    $curl = curl_init();                
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt ($curl, CURLOPT_POST, TRUE);
	    curl_setopt ($curl, CURLOPT_POSTFIELDS, $params); 

	    curl_setopt($curl, CURLOPT_USERAGENT, 'api');

	    curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
	    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
	    curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

	    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

	    curl_exec($curl);   

	    curl_close($curl); 
	}

	function get_is_army_group($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT ag.id
						FROM army_group ag
						WHERE ag.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$army_group_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			return $army_group_id == "" ? false : true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_active_army_groups () {
		GLOBAL $connect;

		try {

			$query = "SELECT ag.group_info_id
						FROM army_group ag,
							group_info gi
						WHERE gi.id = ag.group_info_id
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();
			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['group_info_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_is_marathon_group ($group_info_id) {
		GLOBAL $connect;

		try {
			$query = "SELECT mt.id
						FROM marathon_group mt
						WHERE mt.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();
			$marathon_group_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			return $marathon_group_id == "" ? false : true;
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_active_marathon_groups () {
		GLOBAL $connect;
		
		try {
			$query = "SELECT mg.group_info_id
						FROM marathon_group mg,
							group_info gi
						WHERE gi.id = mg.group_info_id
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				array_push($result, $value['group_info_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_is_school_group ($group_info_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT schg.id
						FROM school_group schg
						WHERE schg.group_info_id = :group_info_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_info_id', $group_info_id, PDO::PARAM_INT);
			$stmt->execute();

			$school_group_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			return  $school_group_id == "" ? false : true;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_all_active_school_groups () {
		GLOBAL $connect;

		try {

			$query = "SELECT sg.group_info_id
						FROM school_group sg,
							group_info gi
						WHERE gi.id = sg.group_info_id
							AND gi.is_archive = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				array_push($result, $value['group_info_id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function compressImage($source, $destination, $quality) {
		$info = getimagesize($source);
		if ($info['mime'] == 'image/jpeg') {
			list($width, $height) = $info;
			if($width > $height){
				$image = imagecreatefromjpeg($source);
				$rotatedImg = imagerotate($image, -90, 0);
				return imagejpeg($rotatedImg, $destination, $quality);
			} else {
				$image = imagecreatefromjpeg($source);
				return imagejpeg($image, $destination, $quality);
			}
		} else if ($info['mime'] == 'image/jpg') {
			list($width, $height) = $info;
			if($width > $height){
				$image = imagecreatefromjpeg($source);
				$rotatedImg = imagerotate($image, -90, 0);
				return imagejpeg($rotatedImg, $destination, $quality);
			} else {
				$image = imagecreatefromjpeg($source);
				return imagejpeg($image, $destination, $quality);
			}
		} else if ($info['mime'] == 'image/gif') { 
			list($width, $height) = $info;
			if($width > $height){
				$image = imagecreatefromgif($source);
				$rotatedImg = imagerotate($image, -90, 0);
				return imagegif($rotatedImg, $destination);
			} else {
				$image = imagecreatefromgif($source);
				return imagegif($image, $destination);
			}
		} else if ($info['mime'] == 'image/png') {
			$quality = 9;
			list($width, $height) = $info;
			if($width > $height){
				$image = imagecreatefrompng($source);
				$rotatedImg = imagerotate($image, -90, 0);
				return imagepng($rotatedImg, $destination, $quality, PNG_NO_FILTER);
			} else {
				$image = imagecreatefrompng($source);
				return imagepng($image, $destination, $quality, PNG_NO_FILTER);
			}
		}
	}

	function get_group_student_discount ($group_student_id, $transfer_from_group) {
        GLOBAL $connect;

        try {

            $query = "SELECT d.id AS discount_id,
                            d.title,
                            d.amount,
                            d.type,
                            d.cant_insert_promo_code,
                            dgs.id AS discount_group_student_id,
                            dgs.used_count,
                            d.for_month
                        FROM discount_group_student dgs,
                            discount d
                        WHERE dgs.group_student_id = :group_student_id
                            AND dgs.status = 'active'
                            AND d.id = dgs.discount_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
            $stmt->execute();
            $row_count = $stmt->rowCount();

            if ($row_count == 0) {
                if ($transfer_from_group == '') {
                    return array();
                }

                $query = "SELECT gs.id AS group_student_id,
                                gs.transfer_from_group
                            FROM group_student gs
                            WHERE gs.is_archive = 1
                                AND gs.group_info_id = :group_info_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':group_info_id', $transfer_from_group, PDO::PARAM_INT);
                $stmt->execute();
                $row_count = $stmt->rowCount();

                if ($row_count == 0) {
                    return array();
                }

                $group_info = $stmt->fetch(PDO::FETCH_ASSOC);
                return get_group_student_discount($group_info['group_student_id'], $group_info['transfer_from_group']);
            }

            $query_result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = array('discount_id' => $query_result['discount_id'],
                            'title' => $query_result['title'],
                        	'amount' => $query_result['amount'],
                        	'type' => $query_result['type'],
                        	'discount_group_student_id' => $query_result['discount_group_student_id'],
                        	'used_count' => $query_result['used_count'],
                        	'for_month' => $query_result['for_month'],
                        	'cant_insert_promo_code' => $query_result['cant_insert_promo_code']);

            return $result;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    function get_student_active_promo_codes($student_id) {
    	GLOBAL $connect;

    	try {
    		
    		$query = "SELECT supc.id AS student_used_promo_code_id,
    						s.last_name,
    						s.first_name
    					FROM student_used_promo_code supc,
    						student_promo_code spc,
    						student s
    					WHERE supc.student_id = :student_id
    						AND supc.is_used = 0
    						AND spc.id = supc.student_promo_code_id
    						AND s.id = spc.student_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		$student_used_promo_code_infos = array();
    		if ($row_count > 0) {
    			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
	    		$student_used_promo_code_infos[$query_result['student_used_promo_code_id']] = 
	    														array('student_used_promo_code_id' => $query_result['student_used_promo_code_id'],
				    													'last_name' => $query_result['last_name'],
				    													'first_name' => $query_result['first_name']);
    		}

    		$query = "SELECT supc.id AS student_used_promo_code_id,
    						s.last_name,
    						s.first_name
    					FROM student_used_promo_code supc,
    						student_promo_code spc,
    						student s
    					WHERE supc.is_used = 1
    						AND supc.student_promo_code_id = spc.id
    						AND spc.student_id = :student_id
    						AND s.id = supc.student_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		foreach ($query_result as $value) {
    			$student_used_promo_code_infos[$value['student_used_promo_code_id']] =
    																array('student_used_promo_code_id' => $value['student_used_promo_code_id'],
    																		'last_name' => $value['last_name'],
    																		'first_name' => $value['first_name']);
    		}

    		return $student_used_promo_code_infos;

    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function set_student_trial_test ($student_id, $trial_test_list) {
    	GLOBAL $connect;

    	try {
    		shuffle($trial_test_list);
    		foreach ($trial_test_list as $trial_test_id) {
    			$query = "SELECT stt.id
    						FROM student_trial_test stt
    						WHERE stt.student_id = :student_id
    							AND stt.trial_test_id = :trial_test_id";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
    			$stmt->execute();
    			$row_count = $stmt->rowCount();
    			if ($row_count == 0) {
    				$query = "INSERT INTO student_trial_test (student_id, trial_test_id)
    												VALUES (:student_id, :trial_test_id)";
    				$stmt = $connect->prepare($query);
    				$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    				$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
    				$stmt->execute();
    				return $connect->lastInsertId();
    			}
    		}
    		return 0;
    		
    	} catch (Exception $e) {
    		throw $e;	
    	}
    }

    function clear_expired_sms_codes() {
    	GLOBAL $connect;

    	try {

    		$query = "DELETE FROM sms_code WHERE DATE_ADD(created_date, INTERVAL 5 MINUTE) <= NOW()";
    		$stmt = $connect->prepare($query);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

?>