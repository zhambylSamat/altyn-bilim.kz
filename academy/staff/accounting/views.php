<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');


	function get_money_type() {
		GLOBAL $connect;

		try {

			$query = "SELECT mt.id, 
							mt.title_short,
							mt.title_full
						FROM money_type mt
						ORDER BY mt.id";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('mt_ids' => array(),
							'datas' => array());
			foreach ($query_result as $value) {
				array_push($result['mt_ids'], $value['id']);
				$result['datas'][$value['id']] = array('title_short' => $value['title_short'],
														'title_full' => $value['title_full']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_end_of_day_balances($month_num, $year_num) {
		GLOBAL $connect;

		try {

			$query = "SELECT eodb.money_type_id, 
							eodb.amount,
							DATE_FORMAT(eodb.date, '%d') AS day
						FROM end_of_day_balance eodb
						WHERE DATE_FORMAT(eodb.date, '%Y') = :year
							AND DATE_FORMAT(eodb.date, '%m') = :month";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':year', $year_num, PDO::PARAM_STR);
			$stmt->bindParam(':month', $month_num, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['day']])) {
					$result[$value['day']] = array('data' => array());
				}

				$result[$value['day']]['data'][$value['money_type_id']] = array('amount' => $value['amount']);
			}

			$money_type = get_money_type();
			$last_amounts = array();
			foreach ($money_type['datas'] as $money_type_id => $value) {
				$query = "SELECT eodb.amount,
								DATE_FORMAT(DATE_ADD(eodb.date, INTERVAL 1 DAY), '%d') AS day,
								DATE_FORMAT(DATE_ADD(eodb.date, INTERVAL 1 DAY), '%Y-%m') AS d,
								DATE_FORMAT(eodb.date, '%Y-%m') > DATE_FORMAT(STR_TO_DATE(:date, '%Y-%m-%d'), '%Y-%m') AS is_future,
								DATE_FORMAT(eodb.date, '%Y-%m') < DATE_FORMAT(STR_TO_DATE(:date, '%Y-%m-%d'), '%Y-%m') AS is_past
							FROM end_of_day_balance eodb
							WHERE eodb.money_type_id = :money_type_id
							ORDER BY eodb.date DESC
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$d = $year_num.'-'.$month_num.'-01';
				$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
				$stmt->bindParam(':date', $d, PDO::PARAM_STR);
				$stmt->execute();
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$query_result['is_future']) {
					$last_amounts[$money_type_id] = array('day' => intval($query_result['day']),
															'amount' => $query_result['amount'],
															'date' => $query_result['d'],
															'is_past' => $query_result['is_past']);
				}
			}

			foreach ($last_amounts as $money_type_id => $value) {
				if ($value['is_past'] == 1) {
					$date = $year_num.'-'.$month_num;
					$day = 1;
				} else {
					$date = $value['date'];
					$day = $value['day'];
				}
				$days = date('t', strtotime($date));
				for ($i = $day; $i <= $days; $i++) {
					$day = $i;
					if ($i < 10) {
						$day = '0'.$i;
					}
					$result[$day]['data'][$money_type_id] = array('amount' => $value['amount']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_begin_of_day_balances ($month_num, $year_num) {
		GLOBAL $connect;

		try {

			$query = "SELECT bodb.money_type_id,
							bodb.amount,
							DATE_FORMAT(bodb.date, '%d') AS day
						FROM begin_of_day_balance bodb
						WHERE DATE_FORMAT(bodb.date, '%Y') = :year
							AND DATE_FORMAT(bodb.date, '%m') = :month
						ORDER BY bodb.date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':year', $year_num, PDO::PARAM_STR);
			$stmt->bindParam(':month', $month_num, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['day']])) {
					$result[$value['day']] = array('data' => array());
				}
				$result[$value['day']]['data'][$value['money_type_id']] = array('amount' => $value['amount']);
			}

			$money_type = get_money_type();
			$last_amounts = array();
			foreach ($money_type['datas'] as $money_type_id => $value) {
				$query = "SELECT eodb.amount,
								DATE_FORMAT(DATE_ADD(eodb.date, INTERVAL 1 DAY), '%d') AS day,
								DATE_FORMAT(DATE_ADD(eodb.date, INTERVAL 1 DAY), '%Y-%m') AS d,
								DATE_FORMAT(eodb.date, '%Y-%m') > DATE_FORMAT(STR_TO_DATE(:date, '%Y-%m-%d'), '%Y-%m') AS is_future,
								DATE_FORMAT(eodb.date, '%Y-%m') < DATE_FORMAT(STR_TO_DATE(:date, '%Y-%m-%d'), '%Y-%m') AS is_past
							FROM end_of_day_balance eodb
							WHERE eodb.money_type_id = :money_type_id
							ORDER BY eodb.date DESC
							LIMIT 1";
				$stmt = $connect->prepare($query);
				$d = $year_num.'-'.$month_num.'-01';
				$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
				$stmt->bindParam(':date', $d, PDO::PARAM_STR);
				$stmt->execute();
				$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$query_result['is_future']) {
					$last_amounts[$money_type_id] = array('day' => intval($query_result['day']),
															'amount' => $query_result['amount'],
															'date' => $query_result['d'],
															'is_past' => $query_result['is_past']);
				}
			}

			foreach ($last_amounts as $money_type_id => $value) {
				if ($value['is_past'] == 1) {
					$date = $year_num.'-'.$month_num;
					$day = 1;
				} else {
					$date = $value['date'];
					$day = $value['day'];
				}
				$days = date('t', strtotime($date));
				for ($i = $day; $i <= $days; $i++) {
					$day = $i;
					if ($i < 10) {
						$day = '0'.$i;
					}
					$result[$day]['data'][$money_type_id] = array('amount' => $value['amount']);
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_comings ($month_num, $year_num) {
		GLOBAL $connect;

		try {

			$query = "SELECT c.money_type_id,
							SUM(c.amount) AS amount,
							DATE_FORMAT(c.date, '%d') AS day
						FROM coming c
						WHERE DATE_FORMAT(c.date, '%Y') = :year
							AND DATE_FORMAT(c.date, '%m') = :month
						GROUP BY c.money_type_id, c.date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':year', $year_num, PDO::PARAM_STR);
			$stmt->bindParam(':month', $month_num, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['day']])) {
					$result[$value['day']] = array('data' => array());
				}

				$result[$value['day']]['data'][$value['money_type_id']] = array('amount' => $value['amount']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_expenditures ($month_num, $year_num) {
		GLOBAL $connect;

		try {

			$query = "SELECT e.money_type_id,
							SUM(e.amount) AS amount,
							DATE_FORMAT(e.date, '%d') AS day
						FROM expenditure e
						WHERE DATE_FORMAT(e.date, '%Y') = :year
							AND DATE_FORMAT(e.date, '%m') = :month
						GROUP BY e.money_type_id, e.date";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':year', $year_num, PDO::PARAM_STR);
			$stmt->bindParam(':month', $month_num, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				if (!isset($result[$value['day']])) {
					$result[$value['day']] = array('data' => array());
				}

				$result[$value['day']]['data'][$value['money_type_id']] = array('amount' => $value['amount'],
																				'exceeded' => 0);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_total_amount_of_coming_and_expenditure ($from_date, $to_date) {
		GLOBAL $connect;

		try {

			$query = "SELECT mt.id,
							mt.title_short,
							mt.title_full
						FROM money_type mt";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$money_type_query = $stmt->fetchAll();

			$money_type = array();
			foreach ($money_type_query as $value) {
				$money_type[$value['id']] = array('title_short' => $value['title_short'],
													'title_full' => $value['title_full']);
			}

			$query = "SELECT SUM(c.amount) AS total_amount,
							c.money_type_id
						FROM coming c
						WHERE c.date BETWEEN :from_date AND :to_date
						GROUP BY c.money_type_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
			$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
			$stmt->execute();
			$coming_query = $stmt->fetchAll();
			$coming = array();
			foreach ($coming_query as $value) {
				$coming[$value['money_type_id']] = $value['total_amount'];
			}

			$query = "SELECT SUM(e.amount) AS total_amount,
							e.money_type_id
						FROM expenditure e
						WHERE e.date BETWEEN :from_date AND :to_date
						GROUP BY e.money_type_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
			$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
			$stmt->execute();
			$expenditure_query = $stmt->fetchAll();
			$expenditure = array();
			foreach ($expenditure_query as $value) {
				$expenditure[$value['money_type_id']] = $value['total_amount'];
			}

			$result = array('coming_partial_amount' => array(),
							'expenditure_partial_amount' => array(),
							'coming_amount' => 0.0,
							'expenditure_amount' => 0.0,
							'total_amount' => 0.0,
							'money_type' => $money_type);

			foreach ($money_type as $money_type_id => $value) {
				$coming_amount = 0;
				$expenditure_amount = 0;
				if (isset($coming[$money_type_id])) {
					$coming_amount = $coming[$money_type_id];
				}
				if (isset($expenditure[$money_type_id])) {
					$expenditure_amount = $expenditure[$money_type_id];
				}

				$result['coming_amount'] += $coming_amount;
				$result['expenditure_amount'] += $expenditure_amount;
				$result['total_amount'] += $coming_amount;
				$result['total_amount'] -= $expenditure_amount;
				$result['coming_partial_amount'][$money_type_id] = $coming_amount;
				$result['expenditure_partial_amount'][$money_type_id] = $expenditure_amount;
			}

			$result['total_amount'] = round($result['total_amount'], 2);

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_coming_full_info ($from_date, $to_date) {
		GLOBAL $connect;

		try {

			$query = "SELECT cc.id AS category_id,
							cc.category_coming_id AS category_parent_id,
							cc.title
						FROM category_coming cc
						ORDER BY cc.id ASC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$category_query_result = $stmt->fetchAll();


			$result = array('static_category' => array(
										'Армия' => array('subcategory' => array('Алгебра' => array('total_sum' => 0.0),
																				'Матсауаттылық' => array('total_sum' => 0.0),
																				'Физика' => array('total_sum' => 0.0)),
														'category' => array('total_sum' => 0.0)),
										'Онлайн академия' => array('subcategory' => array('Алгебра' => array('total_sum' => 0.0),
																						'Матсауаттылық' => array('total_sum' => 0.0),
																						'Физика' => array('total_sum' => 0.0)),
																	'category' => array('total_sum' => 0.0))),
							'dynamic_category' => array(),
							'category_info' => array());

			foreach ($category_query_result as $value) {
				if (!isset($result['category_info'][$value['category_id']])) {
					$result['category_info'][$value['category_id']] = array('category_id' => $value['category_id'],
																			'category_parent_id' => $value['category_parent_id'],
																			'category_title' => $value['title']);
				}

				if ($value['category_parent_id'] == 0 && !isset($result['dynamic_category'][$value['category_id']])) {
					$result['dynamic_category'][$value['category_id']] = array('subcategory' => array(),
																				'category' => array('total_sum' => 0.0));
				}
				if ($value['category_parent_id'] != 0 && !isset($result['dynamic_category'][$value['category_parent_id']][$value['category_id']])) {
					$result['dynamic_category'][$value['category_parent_id']]['subcategory'][$value['category_id']] = array('total_sum' => 0.0);
				}
			}

			$query = "SELECT c.money_type_id,
							c.category_coming_id,
							c.group_type,
							c.subject_title,
							sum(c.amount) AS amount,
							cc.category_coming_id AS category_parent_id
						FROM coming c
						LEFT JOIN category_coming cc
							ON cc.id = c.category_coming_id
						WHERE c.date BETWEEN :from_date AND :to_date
						GROUP BY c.category_coming_id, c.group_type, c.subject_title";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
			$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
			$stmt->execute();
			$coming_query_result = $stmt->fetchAll();

			foreach ($coming_query_result as $value) {
				if ($value['category_coming_id'] != '' || $value['group_type'] != '') {
					if ($value['category_coming_id'] == '') {
						if ($value['subject_title'] == '') {
							$result['static_category'][$value['group_type']]['category']['total_sum'] += $value['amount'];
						} else {
							$result['static_category'][$value['group_type']]['subcategory'][$value['subject_title']]['total_sum'] += $value['amount'];
						}
					} else {
						if ($value['category_parent_id'] == 0) {
							$result['dynamic_category'][$value['category_coming_id']]['category']['total_sum'] += $value['amount'];
						} else {
							$result['dynamic_category'][$value['category_parent_id']]['subcategory'][$value['category_coming_id']]['total_sum'] += $value['amount'];
						}
					}
				}
			}

			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_expenditure_full_info ($from_date, $to_date) {
		GLOBAL $connect;

		try {
			
			$except_category_ids = array(18, 35);


			$query = "SELECT ce.id AS category_id,
							ce.category_expenditure_id AS category_parent_id,
							ce.title
						FROM category_expenditure ce
						ORDER BY ce.id ASC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$category_query_result = $stmt->fetchAll();


			$result = array('data' => array(),
							'money_transfer' => array('total_sum' => 0.0),
							'category_info' => array());

			foreach ($category_query_result as $value) {
				if (!isset($result['category_info'][$value['category_id']])) {
					$result['category_info'][$value['category_id']] = array('category_id' => $value['category_id'],
																			'category_parent_id' => $value['category_parent_id'],
																			'category_title' => $value['title']);
				}

				if ($value['category_parent_id'] == 0 && !isset($result['data'][$value['category_id']])) {
					$result['data'][$value['category_id']] = array('subcategory' => array(),
																	'category' => array('total_sum' => 0.0));
				}
				if ($value['category_parent_id'] != 0 && !isset($result['data'][$value['category_parent_id']][$value['category_id']])) {
					$result['data'][$value['category_parent_id']]['subcategory'][$value['category_id']] = array('total_sum' => 0.0);
				}
			}


			$query = "SELECT e.money_type_id,
							e.category_expenditure_id,
							sum(e.amount) AS amount,
							ce.category_expenditure_id AS category_parent_id,
							e.money_transfer_id
						FROM expenditure e
						LEFT JOIN category_expenditure ce
							ON ce.id = e.category_expenditure_id
						WHERE e.date BETWEEN :from_date AND :to_date
						GROUP BY e.category_expenditure_id, e.money_transfer_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
			$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
			$stmt->execute();
			$coming_query_result = $stmt->fetchAll();

			foreach ($coming_query_result as $value) {
				if (!in_array($value['category_parent_id'], $except_category_ids) && !in_array($value['category_expenditure_id'], $except_category_ids)) {
					if ($value['category_expenditure_id'] != '') {
						if ($value['category_parent_id'] == 0) {
							$result['data'][$value['category_expenditure_id']]['category']['total_sum'] += $value['amount'];
						} else {
							$result['data'][$value['category_parent_id']]['subcategory'][$value['category_expenditure_id']]['total_sum'] += $value['amount'];
						}
					} else if ($value['money_transfer_id'] != '') {
						$result['money_transfer']['total_sum'] += $value['amount'];
					}
				}
			}

			return $result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_avans_dividend ($from_date, $to_date) {
		GLOBAL $connect;

		try {

			$categories = array(18, 35);

			$query = "SELECT ce.id AS category_id,
							ce.title,
							ce.category_expenditure_id AS category_parent_id
						FROM category_expenditure ce
						WHERE ce.id IN (".implode(',', $categories).")
							OR ce.category_expenditure_id IN (".implode(',', $categories).")
						ORDER BY ce.id ASC";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$category_query_result = $stmt->fetchAll();

			$result = array('data' => array(),
							'category_info' => array());
			$category_ids = array();
			foreach ($category_query_result as $value) {
				array_push($category_ids, $value['category_id']);
				if (!isset($result['category_info'][$value['category_id']])) {
					$result['category_info'][$value['category_id']] = array('category_id' => $value['category_id'],
																			'category_parent_id' => $value['category_parent_id'],
																			'category_title' => $value['title']);
				}

				if ($value['category_parent_id'] == 0 && !isset($result['data'][$value['category_id']])) {
					$result['data'][$value['category_id']] = array('subcategory' => array(),
																	'category' => array('total_sum' => 0.0));
				}
				if ($value['category_parent_id'] != 0 && !isset($result['data'][$value['category_parent_id']][$value['category_id']])) {
					$result['data'][$value['category_parent_id']]['subcategory'][$value['category_id']] = array('total_sum' => 0.0);
				}
			}

			if (count($category_ids) > 0) {
				$query = "SELECT e.money_type_id,
								e.category_expenditure_id,
								sum(e.amount) AS amount,
								ce.category_expenditure_id AS category_parent_id,
								e.money_transfer_id
							FROM expenditure e
							LEFT JOIN category_expenditure ce
								ON ce.id = e.category_expenditure_id
							WHERE e.date BETWEEN :from_date AND :to_date
								AND e.category_expenditure_id IN (".implode(',', $category_ids).")
							GROUP BY e.category_expenditure_id";
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
				$stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
				$stmt->execute();
				$coming_query_result = $stmt->fetchAll();

				foreach ($coming_query_result as $value) {
					if ($value['category_expenditure_id'] != '') {
						if ($value['category_parent_id'] == 0) {
							$result['data'][$value['category_expenditure_id']]['category']['total_sum'] += $value['amount'];
						} else {
							$result['data'][$value['category_parent_id']]['subcategory'][$value['category_expenditure_id']]['total_sum'] += $value['amount'];
						}
					}
				}
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_expenditure_budget_list ($month, $year) {
		GLOBAL $connect;

		try {

			$query = "SELECT ce.id,
							ce.title AS subcategory_title,
							(SELECT ce2.title
							FROM category_expenditure ce2
							WHERE ce2.id = ce.category_expenditure_id) AS category_title,
							SUM(e.amount) AS sum_amount,
							ce.budget
						FROM expenditure e,
							category_expenditure ce
						WHERE ce.category_expenditure_id != 0
							AND ce.budget > 0.00
							AND e.category_expenditure_id = ce.id
							AND DATE_FORMAT(e.date, '%m') = :month
							AND DATE_FORMAT(e.date, '%Y') = :year
						GROUP BY e.category_expenditure_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':month', $month, PDO::PARAM_STR);
			$stmt->bindParam(':year', $year, PDO::PARAM_STR);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();
			foreach ($query_result as $value) {
				$left = round(($value['budget'] - $value['sum_amount']), 2);
				$result[$value['id']] = array('category_title' => $value['category_title'],
												'subcategory_title' => $value['subcategory_title'],
												'sum_amount' => number_format($value['sum_amount'], 2, '.', ' '),
												'budget' => number_format($value['budget'], 2, '.', ' '),
												'left' => number_format($left, 2, '.', ' '));
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>