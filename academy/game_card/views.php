<?php
	if(!isset($_SESSION)) {
		session_start();
	}
	$random_cover = '0';
	$cover = 'oblozhka';
	$question_dir_name = 'surak';
	$answer_dir_name = 'zhauap';
	$stop_sheet = "stop";

	function get_cards() {
		GLOBAL $random_cover;
		GLOBAL $cover;

		$root_dir = 'cards';

		$subjects = array();


		$scan_card = scandir($root_dir);

		foreach ($scan_card as $card) {
			$subject_dir = $root_dir.'/'.$card;
			if ($card != '.' && $card != '..' && is_dir($subject_dir)) {
				$scan_subject = scandir($subject_dir);
				$cover_path = '';
				$choose_all_topic_cover_path = '';
				foreach ($scan_subject as $subject) {
					$topic_dir = $subject_dir.'/'.$subject;
					if (is_file($topic_dir)) {
						$topic_path_info = pathinfo($topic_dir);
						if ($topic_path_info['filename'] == $cover) {
							$cover_path = $topic_dir;
						} else if ($topic_path_info['filename'] == $random_cover) {
							$choose_all_topic_cover_path = $topic_dir;
						}
					}
				}
				if ($choose_all_topic_cover_path != '') {
					array_push($subjects, array('title' => $card,
												'dir' => $subject_dir,
												'cover' => $cover_path,
												'all_topic' => $choose_all_topic_cover_path,
												'status' => 'continue'));
				} else {
					array_push($subjects, array('title' => $card,
												'dir' => $subject_dir,
												'cover' => $cover_path,
												'status' => 'stop'));
				}
			}
		}
		return $subjects;
	}

	function get_topics($dir) {
		GLOBAL $cover;
		GLOBAL $random_cover;
		GLOBAL $stop_sheet;

		if (!is_dir($dir)) {
			return array();
		}

		$topics = array('all_topic_random' => array(),
						'topics' => array());
		$scan_topics = scandir($dir);
		foreach ($scan_topics as $topic) {
			if ($topic != '.' && $topic != '..') {
				$topic_dir = $dir.'/'.$topic;
				$topic_path_info = pathinfo($topic_dir);
				if ($topic_path_info['filename'] == $random_cover) {
					$topics['all_topic_random'] = array('cover_dir' => $topic_dir,
														'topics_dir' => $dir);
				} else if ($topic_path_info['filename'] != $cover && $topic_path_info['filename'] != $stop_sheet) {
					$subtopic_scan = scandir($topic_dir);
					$topic_cover = '';
					foreach ($subtopic_scan as $subtopic) {
						if ($subtopic != '.' && $subtopic != '..') {
							$subtopic_dir = $topic_dir.'/'.$subtopic;
							$subtopic_path_info = pathinfo($subtopic_dir);
							if ($subtopic_path_info['filename'] == $cover) {
								$topic_cover = $subtopic_dir;
								break;
							}
						}
					}
					if ($topic_cover != '') {
						array_push($topics['topics'], array('dir' => $topic_dir,
															'cover' => $topic_cover));
					}
				}
			}
		}
		return $topics;
	}

	function get_next_question_and_answer($index) {
		if (isset($_SESSION['card_questions']) && $index >= 3) {
			return array_slice($_SESSION['card_questions'], $index, 1, true);
		}
		return array();
	}

	function get_questions_and_answers ($dir, $is_random) {
		$result = array();
		$total_result = array();
		if ($is_random == 'false') {
			// array_push($result, get_questions_and_answers_by_dir($dir));
			$total_result = get_questions_and_answers_by_dir($dir);
		} else {
			$topics_scandir = scandir($dir);
			foreach ($topics_scandir as $topic) {
				if (is_dir($dir.'/'.$topic) && $topic != '.' && $topic != '..') {
					array_push($result, get_questions_and_answers_by_dir($dir.'/'.$topic));
				}
			}
			foreach ($result as $topic) {
				foreach ($topic as $question) {
					array_push($total_result, $question);
				}
			}
		}
		shuffle($total_result);
		$_SESSION['card_questions'] = $total_result;
		return array_slice($total_result, 0, 3);
	}

	function get_questions_and_answers_by_dir($dir) {
		GLOBAL $question_dir_name;
		GLOBAL $answer_dir_name;
		$total_result = array();
		$result = array();
		if (is_dir($dir)) {
			$question_scandir = scandir($dir.'/'.$question_dir_name);
			foreach ($question_scandir as $question) {
				$question_dir = $dir.'/'.$question_dir_name.'/'.$question;
				$question_path_info = pathinfo($question_dir);
				if (is_file($question_dir)) {
					if (!isset($result[$question_path_info['filename']])) {
						$result[$question_path_info['filename']] = array('q_dir' => $question_dir,
																		'a_dir' => null);
					}
				}
			}

			$answer_scandir = scandir($dir.'/'.$answer_dir_name);
			foreach ($answer_scandir as $answer) {
				$answer_dir = $dir.'/'.$answer_dir_name.'/'.$answer;
				$answer_path_info = pathinfo($answer_dir);
				if (is_file($answer_dir)) {
					if (isset($result[$answer_path_info['filename']])) {
						$result[$answer_path_info['filename']]['a_dir'] = $answer_dir;
					}
				}
			}

			foreach ($result as $value) {
				if ($value['a_dir'] != null) {
					array_push($total_result, $value);
				}
			}
		}
		return $total_result;
	}
?>