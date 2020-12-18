<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');

	function get_active_subjects () {
		GLOBAL $connect;

		try {

			$query = "SELECT sj.id AS subject_id,
							sj.title
						FROM subject_configuration sc,
							subject sj
						WHERE sj.id = sc.subject_id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				$result[$value['subject_id']] = $value['title'];
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_trial_tests ($subject_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT tt.id AS trial_test_id,
							tt.title,
							(SELECT count(ttf.id)
							FROM trial_test_file ttf
							WHERE ttf.trial_test_id = tt.id) AS files_count,
							(SELECT tta.numeration
							FROM trial_test_answer tta
							WHERE tta.trial_test_id = tt.id
							ORDER BY tta.numeration DESC
							LIMIT 1) AS answers_count
						FROM trial_test tt
						WHERE tt.subject_id = :subject_id
						ORDER BY tt.created_date DESC";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {

				$query = "SELECT tta.numeration,
								tta.torf
							FROM trial_test_answer tta
							WHERE tta.trial_test_id = :trial_test_id
							ORDER BY tta.numeration ASC";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':trial_test_id', $value['trial_test_id'], PDO::PARAM_INT);
				$stmt->execute();
				$query_result = $stmt->fetchAll();

				$trial_test_numerations = array();
				foreach ($query_result as $val) {
					if (isset($trial_test_numerations[$val['numeration']])) {
						if ($val['torf'] == 1) {
							$trial_test_numerations[$val['numeration']] = 1;
						}
					} else {
						$trial_test_numerations[$val['numeration']] = $val['torf'];
					}
				}

				$trial_test_no_true_answers_numerations = array();
				foreach ($trial_test_numerations as $numeration => $torf) {
					if ($torf == 0) {
						array_push($trial_test_no_true_answers_numerations, $numeration);
					}
				}
				
				$no_ans_info = "";
				if (count($trial_test_no_true_answers_numerations) > 0) {
					$no_ans_info = implode(', ', $trial_test_no_true_answers_numerations).' сұрақтардың дұрыс жауаптары белгіленбеген';
				}

				$result[$value['trial_test_id']] = array('title' => $value['title'],
														'files_count' => $value['files_count'],
														'answers_count' => $value['answers_count'],
														'no_true_ans_numerations' => $no_ans_info);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_trial_test_datas ($trial_test_id) {
		GLOBAL $connect;

		try {

			$result = array('files' => array(),
							'answers' => array());

			$query = "SELECT ttf.id AS trial_test_file_id,
							ttf.file_link,
							ttf.file_order
						FROM trial_test_file ttf
						WHERE ttf.trial_test_id = :trial_test_id
						ORDER BY ttf.file_order ASC";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				$result['files'][$value['trial_test_file_id']] = array('file_link' => $value['file_link'],
																		'file_order' => $value['file_order']);
			}

			$query = "SELECT tta.id AS trial_test_answer_id,
							tta.numeration,
							tta.prefix,
							tta.torf
						FROM trial_test_answer tta
						WHERE tta.trial_test_id = :trial_test_id
						ORDER BY tta.numeration, tta.prefix";

			$stmt = $connect->prepare($query);
			$stmt->bindParam(':trial_test_id', $trial_test_id, PDO::PARAM_INT);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				if (!isset($result['answers'][$value['numeration']])) {
					$result['answers'][$value['numeration']] = array();
				}
				$result['answers'][$value['numeration']][$value['prefix']] = array('trial_test_answer_id' => $value['trial_test_answer_id'],
																					'torf' => $value['torf']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function trial_test_single_img ($trial_test_file_id, $trial_test_file_link, $trial_test_file_order) {
		
		$html = "";
		// $html .= "<li class='trial-test-img-box ui-sortable-handle' data-order='".$trial_test_file_order."' data-trial-test-file-id='".$trial_test_file_id."'>";
		// 	$html .= "<center>";
		// 		$html .= "<div class='trial-test-img'>";
		// 			$html .= "<img src='".$trial_test_file_link."' class='expand-trial-test-img' data-toggle='modal' data-target='#trial-test-img-modal'  data-file-link='".$trial_test_file_link."'/>";
		// 		$html .= "</div>";
		// 		$html .= "<span class='trial-test-img-controls'>";
		// 			$html .= "<span class='numeration'>".$trial_test_file_order."</span>";
		// 			$html .= "<i class='remove fas fa-trash-alt' data-trial-test-file-id='".$trial_test_file_id."'></i>";
		// 			$html .= "<i class='expand fas fa-expand' data-toggle='modal' data-target='#trial-test-img-modal' data-file-link='".$trial_test_file_link."'></i>";
		// 		$html .= "</span>";
		// 	$html .= "</center>";
		// $html .= "</li>";
		$html .= "<div  class='trial-test-img-box ui-sortable-handle' data-order='".$trial_test_file_order."' data-trial-test-file-id='".$trial_test_file_id."'>";
			$html .= "<figure class='trial-test-figure ui-sortable-handle' data-order='".$trial_test_file_order."' data-trial-test-file-id='".$trial_test_file_id."' itemprop='associatedMedia' itemscope>";
				$html .= "<a href='".$trial_test_file_link."' itemprop='contentUrl' data-size='1500x2000'>";
					$html .= "<img src='".$trial_test_file_link."' class='trial-test-img' itemprop='thumbnail'>";
				$html .= "</a>";
				$html .= "<span class='trial-test-img-controls'>";
					$html .= "<span class='numeration'>".$trial_test_file_order."</span>";
					$html .= "<i class='remove fas fa-trash-alt' data-trial-test-file-id='".$trial_test_file_id."'></i>";
				$html .= "</span>";
			$html .= "</figure>";
		$html .= "</div>";

		return $html;
	}

	function trial_test_single_answer ($numeration, $value) {

		$true_count = 0;
		foreach ($value as $previx => $val) {
			if ($val['torf'] == 1) {
				$true_count++;
			}
		}

		$extra_class = '';
		if ($true_count == 0) {
			$extra_class = "no-true-ans";
		}

		$html = "";
		$html .= "<div class='trial-test-answer-box ".$extra_class."'>";
			$html .= "<span class='numeration'>".$numeration.") </span>";
			$html .= "<input type='hidden' name='numeration' value='".$numeration."'>";
			foreach ($value as $prefix => $val) {
				$checked = "";
				$checkbox_extra_class = "";
				if ($val['torf'] == 1) {
					$checked = 'checked';
					$checkbox_extra_class = "ans-checked";
				}
				$html .= "<label class='prefix-box ".$checkbox_extra_class."'>";
					$html .= "<input type='checkbox' class='answer-checkbox pull-right' ".$checked.">";
					$html .= "<span class='prefix'>".$prefix."</span>";
					$html .= "<input type='hidden' name='trial-test-answer-id' value='".$val['trial_test_answer_id']."'>";
				$html .= "</label>";
			}
			$html .= "<div class='add-prefix'>";
				$html .= "<div class='add-prefix-mark'>+</div>";
			$html .= "</div>";
			$html .= "<div class='remove-last-answer'>";
				$html .= "<div class='remove-last-answer-mark' data-trial-test-answer-id='".$val['trial_test_answer_id']."'><i class='fas fa-backspace'></i></div>";
			$html .= "</div>";
			$html .= "<div class='remove-answer'>";
				$html .= "<div class='remove-answer-mark'><i class='remove fas fa-trash-alt'></i></div>";
			$html .= "</div>";
		$html .= "</div>";

		return $html;
	}
?>