<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
    check_admin_access();


    if (isset($_GET['get_all_category_coming'])) {
    	try {

    		$query = "SELECT cc.id AS category_coming_id,
    						cc.category_coming_id AS parent_id,
    						cc.title
    					FROM category_coming cc
    					ORDER BY cc.category_coming_id, cc.title";
    		$stmt = $connect->prepare($query);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$result = array();
    		foreach ($query_result as $value) {
    			if ($value['parent_id'] == 0 && !isset($result[$value['category_coming_id']])) {
    				$result[$value['category_coming_id']] = array('title' => $value['title'],
    															'subcategory' => array());
    			} else if ($value['parent_id'] != 0 && !isset($result[$value['parent_id']]['subcategory'][$value['category_coming_id']])) {
    				$result[$value['parent_id']]['subcategory'][$value['category_coming_id']] = array('title' => $value['title'],
    																									'parent_id' => $value['parent_id']);
    			}
    		}

    		$data['info'] = $result;
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['add-category-coming'])) {
    	try {

    		$parent_id = $_GET['parent_id'];
    		$title = $_GET['title'];

    		$query = "INSERT INTO category_coming (category_coming_id, title) VALUES (:category_coming_id, :title)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':category_coming_id', $parent_id, PDO::PARAM_INT);
    		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
    		$stmt->execute();
    		$category_coming_id = $connect->lastInsertId();

    		$data['info'] = array('category_coming_id' => $category_coming_id,
    								'title' => $title,
    								'parent_id' => $parent_id);
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['add-category-expenditure'])) {
    	try {

    		$parent_id = $_GET['parent_id'];
    		$title = $_GET['title'];
            $budget = $_GET['budget'];

    		$query = "INSERT INTO category_expenditure (category_expenditure_id, title, budget) VALUES (:category_expenditure_id, :title, :budget)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':category_expenditure_id', $parent_id, PDO::PARAM_INT);
    		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':budget', $budget, PDO::PARAM_STR);
    		$stmt->execute();
    		$category_expenditure_id = $connect->lastInsertId();

    		$data['info'] = array('category_expenditure_id' => $category_expenditure_id,
    								'title' => $title,
    								'parent_id' => $parent_id,
                                    'budget' => $budget);
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['update_subcategory_expenditure'])) {
        try {

            $category_expenditure_id = $_GET['category_id'];
            $title = $_GET['title'];
            $budget = $_GET['budget'];

            $query = "UPDATE category_expenditure SET title = :title, budget = :budget WHERE id = :category_expenditure_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':budget', $budget, PDO::PARAM_STR);
            $stmt->bindParam(':category_expenditure_id', $category_expenditure_id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['get_all_category_expenditure'])) {
    	try {

    		$query = "SELECT ce.id AS category_expenditure_id,
    						ce.category_expenditure_id AS parent_id,
    						ce.title,
                            ce.budget
    					FROM category_expenditure ce
    					ORDER BY ce.category_expenditure_id, ce.title";
    		$stmt = $connect->prepare($query);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$result = array();
    		foreach ($query_result as $value) {
    			if ($value['parent_id'] == 0 && !isset($result[$value['category_expenditure_id']])) {
    				$result[$value['category_expenditure_id']] = array('title' => $value['title'],
    															'subcategory' => array());
    			} else if ($value['parent_id'] != 0 && !isset($result[$value['parent_id']]['subcategory'][$value['category_expenditure_id']])) {
    				$result[$value['parent_id']]['subcategory'][$value['category_expenditure_id']] = array('title' => $value['title'],
    																									'parent_id' => $value['parent_id'],
                                                                                                        'budget' => $value['budget']);
    			}
    		}

    		$data['info'] = $result;
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['add_new_amount'])) {
    	try {

    		$date = $_POST['date'];
    		$date = date_format(date_create($date), 'Y-m-d');
    		$amount = $_POST['account-amount'];
    		$money_type_id = $_POST['money-type'];
    		$account_type = $_POST['account-type'];

    		if (isset($_POST['category_type'])) {
    			$category_type = $_POST['category_type'];
    			if (isset($_POST['group_type']) && isset($_POST['has_subcategory'])) {
    				$group_type = $_POST['group_type'];
    				$has_subcategory = $_POST['has_subcategory'];
    				if ($has_subcategory == '1') {
    					if (isset($_POST['subject_title'])) {
    						$subject_title = $_POST['subject_title'];
    						insert_accounting_amount($account_type, $date, $amount, $money_type_id, '', $group_type, $subject_title);
    						$data['success'] = true;
    					} else {
    						$data['success'] = false;
    					}
    				} else {
    					insert_accounting_amount($account_type, $date, $amount, $money_type_id, '', $group_type, '');
    					$data['success'] = true;
    				}
    			} else if (isset($_POST['category_id']) && isset($_POST['has_subcategory'])) {
    				$category_id = $_POST['category_id'];
    				$has_subcategory = $_POST['has_subcategory'];
    				if ($has_subcategory == '1') {
    					if (isset($_POST['subcategory_id'])) {
    						$subcategory_id = $_POST['subcategory_id'];
    						insert_accounting_amount($account_type, $date, $amount, $money_type_id, $subcategory_id, '', '');
    						$data['success'] = true;
    					} else {
    						$data['success'] = false;
    					}
    				} else {
    					insert_accounting_amount($account_type, $date, $amount, $money_type_id, $category_id, '', '');
    					$data['success'] = true;
    				}
    			} else {
    				$data['success'] = false;
    			}
    		} else {
    			$data['success'] = false;
    		}
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data);
    } else if (isset($_GET['get_categories'])) {
    	try {

    		$account_type = $_GET['account_type'];
            $date = $_GET['date'];
    		$result = array('static' => array(),
    						'dynamic' => array());
    		if ($account_type == 'coming') {
    			$result['static'] = array(array('group_type' => 'Онлайн академия',
												'subcategories' => array(array('subject_title' => 'Алгебра'),
																			array('subject_title' => 'Физика'),
																			array('subject_title' => 'Матсауаттылық'))),
										array('group_type' => 'Армия',
												'subcategories' => array(array('subject_title' => 'Алгебра'),
																			array('subject_title' => 'Физика'),
																			array('subject_title' => 'Матсауаттылық'))));
    			$query = "SELECT cc.id AS category_coming_id,
    							cc.category_coming_id AS parent_id,
    							cc.title
    						FROM category_coming cc
    						ORDER BY cc.category_coming_id, cc.id";
    			$stmt = $connect->prepare($query);
    			$stmt->execute();
    			$query_result = $stmt->fetchAll();

    			$categories = array();
    			foreach ($query_result as $value) {
    				if ($value['parent_id'] == 0 && !isset($categories[$value['category_coming_id']])) {
    					$categories['!'.$value['category_coming_id']] = array('title' => $value['title'],
                                                                            'id' => $value['category_coming_id'],
    																		'subcategories' => array());
    				}
    				if ($value['parent_id'] != 0) {
    					$categories['!'.$value['parent_id']]['subcategories']['!'.$value['category_coming_id']] = array('title' => $value['title'],
                                                                                                                        'id' => $value['category_coming_id']);
    				}
    			}
    			$result['dynamic'] = $categories;
    		} else if ($account_type == 'expenditure') {

                $query = "SELECT ce.id AS category_expenditure_id,
                                ce.title
                            FROM category_expenditure ce
                            WHERE ce.category_expenditure_id = 0
                            ORDER BY ce.title";
                $stmt = $connect->prepare($query);
                $stmt->execute();
                $query_result = $stmt->fetchAll();

                $categories = array();

                foreach ($query_result as $value) {
                    $categories['!'.$value['category_expenditure_id']] = array('title' => $value['title'],
                                                                                'id' => $value['category_expenditure_id'],
                                                                                'subcategories' => array(),
                                                                                'exceeded' => 0);
                }

                $query = "SELECT ce.id AS category_expenditure_id,
                                ce.title,
                                ce.category_expenditure_id AS parent_id
                            FROM category_expenditure ce
                            WHERE ce.category_expenditure_id != 0
                            ORDER BY ce.title";
                $stmt = $connect->prepare($query);
                $stmt->execute();
                $query_result = $stmt->fetchAll();

                foreach ($query_result as $value) {
                    $categories['!'.$value['parent_id']]['subcategories']['!'.$value['category_expenditure_id']] 
                                                                                        = array('title' => $value['title'],
                                                                                                'id' => $value['category_expenditure_id'],
                                                                                                'parent_id' => $value['parent_id'],
                                                                                                'exceeded' => 2);
                }

                if ($date != '') {
                    $date_splitted = explode('-', $date);
                    $month = $date_splitted[1];
                    $year = $date_splitted[0];
                    $query = "SELECT ce.category_expenditure_id AS parent_id,
                                    ce.id AS category_expenditure_id,
                                    (ce.budget < SUM(e.amount)) AS exceeded
                                FROM expenditure e,
                                    category_expenditure ce
                                WHERE ce.category_expenditure_id != 0
                                    AND ce.budget > 0.00
                                    AND DATE_FORMAT(e.date, '%m') = :month
                                    AND DATE_FORMAT(e.date, '%Y') = :year
                                    AND e.category_expenditure_id = ce.id
                                GROUP BY e.category_expenditure_id";
                    $stmt = $connect->prepare($query);
                    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->execute();
                    $row_count = $stmt->rowCount();

                    if ($row_count > 0) {
                        $query_result = $stmt->fetchAll();

                        foreach ($query_result as $value) {
                            if ($categories['!'.$value['parent_id']]['exceeded'] == 0) {
                                $categories['!'.$value['parent_id']]['exceeded'] = intval($value['exceeded']);
                            }
                            $categories['!'.$value['parent_id']]['subcategories']['!'.$value['category_expenditure_id']]['exceeded'] = intval($value['exceeded']);
                        }
                    }
                }

    			$result['dynamic'] = $categories;
    		}

    		$data['info'] = $result;
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_comings_by_date'])) {
    	try {
    		$date = $_GET['date'];
    		$money_type_id = $_GET['money_type_id'];

    		$query = "SELECT c.id,
    						c.group_type,
    						c.subject_title,
    						c.amount,
    						c.category_coming_id
    					FROM coming c
    					WHERE c.date = :date
    						AND c.money_type_id = :money_type_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$category_coming_list = get_category_coming();

    		$result = array();
    		foreach ($query_result as $value) {
                $category_id = 0;
                $category_title = '';
                $subcategory_id = 0;
                $subcategory_title = '';
    			$title = "";
    			if ($value['subject_title'] != '') {
    				$title = $value['group_type'].' | '.$value['subject_title'];
    			} else if ($value['group_type'] != '') {
    				$title = $value['group_type'];
    			} else if ($value['category_coming_id'] != '') {
    				$title = $category_coming_list[$value['category_coming_id']]['title'];

                    if ($category_coming_list[$value['category_coming_id']]['parent_id'] == 0) {
                        $category_id = $value['category_coming_id'];
                        $category_title = $category_coming_list[$value['category_coming_id']]['title'];
                    } else {
                        $category_id = $category_coming_list[$value['category_coming_id']]['parent_id'];
                        $category_title = $category_coming_list[$category_id]['title'];

                        $subcategory_id = $value['category_coming_id'];
                        $subcategory_title = ltrim(explode('|', $category_coming_list[$value['category_coming_id']]['title'])[1], ' ');
                    }
    			}
                
    			$result[$value['id']] = array('amount' => $value['amount'],
    											'title' => $title,
                                                'group_type' => $value['group_type'],
                                                'subject_title' => $value['subject_title'],
                                                'category_id' => $category_id,
                                                'category_title' => $category_title,
                                                'subcategory_id' => $subcategory_id,
                                                'subcategory_title' => $subcategory_title,
    											'money_transfer_id' => get_money_transfer_id($value['id']));
    		}

    		$data['info'] = $result;
            $data['has_create_edit_remove_access'] = admin_create_edit_remove_access();
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_expenditures_by_date'])) {
    	try {
    		$date = $_GET['date'];
    		$money_type_id = $_GET['money_type_id'];

    		$query = "SELECT e.id,
    						e.amount,
    						e.category_expenditure_id,
    						e.money_transfer_id,
                            e.category_expenditure_id
    					FROM expenditure e
    					WHERE e.date = :date
    						AND e.money_type_id = :money_type_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$category_expenditure_list = get_expenditure_coming();

    		$result = array();
    		foreach ($query_result as $value) {
                $category_id = 0;
                $category_title = "";
                $subcategory_id = 0;
                $subcategory_title = '';
                $title = "";
                if ($value['category_expenditure_id'] != '') {
                    $title = $category_expenditure_list[$value['category_expenditure_id']]['title'];

                    if ($category_expenditure_list[$value['category_expenditure_id']]['parent_id'] == 0) {
                        $category_id = $value['category_expenditure_id'];
                        $category_title = $category_expenditure_list[$value['category_expenditure_id']]['title'];
                    } else {
                        $category_id = $category_expenditure_list[$value['category_expenditure_id']]['parent_id'];
                        $category_title = $category_expenditure_list[$category_id]['title'];

                        $subcategory_id = $value['category_expenditure_id'];
                        $subcategory_title = ltrim(explode('|', $category_expenditure_list[$value['category_expenditure_id']]['title'])[1], ' ');
                    }
                }

    			$result[$value['id']] = array('amount' => $value['amount'],
    											'title' => $title,
                                                'category_id' => $category_id,
                                                'category_title' => $category_title,
                                                'subcategory_id' => $subcategory_id,
                                                'subcategory_title' => $subcategory_title,
    											'money_transfer_id' => $value['money_transfer_id']);
    		}

    		$data['info'] = $result;
            $data['has_create_edit_remove_access'] = admin_create_edit_remove_access();
            $data['success'] = true;
            
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['edit-amount'])) {
    	try {
    		$id = $_POST['id'];
    		$amount = $_POST['account-amount'];
    		$money_type_id = $_POST['money-type-id'];
    		$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
    		$accounting_type = $_POST['accounting_type'];

    		$query = "";
    		if ($accounting_type == 'coming') {
    			$query = "UPDATE coming SET amount = :amount WHERE id = :id";
    		} else if ($accounting_type == 'expenditure') {
    			$query = "UPDATE expenditure SET amount = :amount WHERE id = :id";
    		}

    		if ($query != '') {
    			$stmt = $connect->prepare($query);
				$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();

	    		set_begin_end_day_of_balance($money_type_id, $date);
	    		$data['success'] = true;
    		} else {
    			$data['success'] = false;
    		}
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

    } else if (isset($_GET['remove-amount'])) {
    	try {
    		$id = $_GET['id'];
    		$money_type_id = $_GET['money_type_id'];
    		$date = $_GET['date'];
    		$accounting_type = $_GET['accounting_type'];

    		$query = "";
    		if ($accounting_type == 'coming') {
    			$query = "DELETE FROM coming WHERE id = :id";
    		} else if ($accounting_type == 'expenditure') {
    			$query = "DELETE FROM expenditure WHERE id = :id";
    		}

    		if ($query != '') {
    			$stmt = $connect->prepare($query);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();

	    		set_begin_end_day_of_balance($money_type_id, $date);
	    		$data['success'] = true;
    		} else {
    			$data['success'] = false;
    		}

    	} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['money_transfer'])) {
    	try {

    		$from_money_type = $_POST['from-money-type'];
    		$to_money_type = $_POST['to-money-type'];
    		$money_transfer_date = date_format(date_create($_POST['money-transfer-date']), 'Y-m-d');
    		$fee = $_POST['fee'];
    		$amount = $_POST['amount'];

    		$from_coming_id = insert_coming_money_transfer($from_money_type, $money_transfer_date, $amount*(-1));
    		$to_coming_id = insert_coming_money_transfer($to_money_type, $money_transfer_date, $amount-$fee);
    		$money_transfer_id = insert_money_transfer($from_coming_id, $to_coming_id);
    		$money_type_id = 2;
    		if ($fee > 0) {
    			insert_expenditure_money_transfer($money_type_id, $money_transfer_id, $fee, $money_transfer_date);
    		}
    		set_begin_end_day_of_balance($from_money_type, $money_transfer_date);
    		set_begin_end_day_of_balance($to_money_type, $money_transfer_date);
    		set_begin_end_day_of_balance($money_type_id, $money_transfer_date);
    		$data['success'] = true;
    	} catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['remove-money-transfer'])) {
    	try {

    		$money_transfer_id = $_GET['money_transfer_id'];

    		$money_transfer_info = remove_money_transfer_info($money_transfer_id);
    		$expenditure_info = remove_expenditure_by_money_transfer_id($money_transfer_id);
    		$from_coming_info = remove_coming_by_id($money_transfer_info['from_coming_id']);
    		$to_coming_info = remove_coming_by_id($money_transfer_info['to_coming_id']);

    		set_begin_end_day_of_balance($from_coming_info['money_type_id'], $from_coming_info['date']);
    		set_begin_end_day_of_balance($to_coming_info['money_type_id'], $to_coming_info['date']);
            if (count($expenditure_info) > 0) {
                set_begin_end_day_of_balance($expenditure_info['money_type_id'], $expenditure_info['date']);
            }
    		$data['success'] = true;
            $data['from-coming-info'] = $from_coming_info;
            $data['to-comint-info'] = $to_coming_info;
            $data['expenditure-info'] = $expenditure_info;
    	} catch (Exception $e) {
    		throw $e;
    	}
    	echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_coming_group_type_category_info'])) {
        try {

            $group_type = $_GET['group_type'];
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];

            $query = "SELECT c.id AS coming_id,
                            c.group_type,
                            DATE_FORMAT(c.date, '%d.%m.%Y') AS date,
                            c.amount,
                            mt.title_short,
                            mt.title_full
                        FROM coming c,
                            money_type mt
                        WHERE c.group_type = :group_type
                            AND c.subject_title IS NULL
                            AND mt.id = c.money_type_id
                            AND c.date BETWEEN :from_date AND :to_date
                        ORDER BY c.date";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_type', $group_type, PDO::PARAM_STR);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                array_push($result, array('group_type' => $value['group_type'],
                                        'date' => $value['date'],
                                        'amount' => $value['amount'],
                                        'title_short' => $value['title_short'],
                                        'title_full' => $value['title_full'],
                                        'coming_id' => $value['coming_id']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_coming_group_type_subcategory_info'])) {
        try {

            $group_type = $_GET['group_type'];
            $subject_title = $_GET['subject_title'];
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];

            $query = "SELECT DATE_FORMAT(c.date, '%d.%m.%Y') AS date,
                            c.amount,
                            mt.title_short,
                            mt.title_full
                        FROM coming c,
                            money_type mt
                        WHERE c.group_type = :group_type
                            AND c.subject_title = :subject_title
                            AND mt.id = c.money_type_id
                            AND c.DATE BETWEEN :from_date AND :to_date
                        ORDER BY c.date ASC";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':group_type', $group_type, PDO::PARAM_STR);
            $stmt->bindParam(':subject_title', $subject_title, PDO::PARAM_STR);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                array_push($result, array('date' => $value['date'],
                                            'amount' => $value['amount'],
                                            'title_short' => $value['title_short'],
                                            'title_full' => $value['title_full']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_coming_category_info'])) {
        try {

            $category_id = $_GET['category_id'];
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];

            $query = "SELECT cc.title
                        FROM category_coming cc
                        WHERE cc.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT c.id AS coming_id,
                            DATE_FORMAT(c.date, '%d.%m.%Y') AS date,
                            c.amount,
                            mt.title_short,
                            mt.title_full
                        FROM coming c,
                            money_type mt
                        WHERE c.category_coming_id = :category_id
                            AND c.date BETWEEN :from_date AND :to_date
                            AND mt.id = c.money_type_id
                        ORDER BY c.date ASC";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                array_push($result, array('date' => $value['date'],
                                        'amount' => $value['amount'],
                                        'title_short' => $value['title_short'],
                                        'title_full' => $value['title_full']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_coming_subcategory_info'])) {
        try {

            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];
            $category_id = $_GET['category_id'];
            $category_parent_id = $_GET['category_parent_id'];

            $query = "SELECT cc.title
                        FROM category_coming cc
                        WHERE cc.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_parent_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT cc.title
                        FROM category_coming cc
                        WHERE cc.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT c.id AS coming_id,
                            DATE_FORMAT(c.date, '%d.%m.%Y') AS date,
                            c.amount,
                            mt.title_short,
                            mt.title_full
                        FROM coming c,
                            money_type mt
                        WHERE c.category_coming_id = :category_id
                            AND c.date BETWEEN :from_date AND :to_date
                            AND mt.id = c.money_type_id
                        ORDER BY c.date ASC";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                array_push($result, array('date' => $value['date'],
                                        'amount' => $value['amount'],
                                        'title_short' => $value['title_short'],
                                        'title_full' => $value['title_full']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_expenditure_category_info'])) {
        try {
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];
            $category_id = $_GET['category_id'];

            $query = "SELECT ce.title
                        FROM category_expenditure ce
                        WHERE ce.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT e.id AS expenditure_id,
                            DATE_FORMAT(e.date, '%d.%m.%Y') AS date,
                            e.amount,
                            mt.title_short,
                            mt.title_full
                        FROM expenditure e,
                            money_type mt
                        WHERE e.category_expenditure_id = :category_id
                            AND e.date BETWEEN :from_date AND :to_date
                            AND mt.id = e.money_type_id
                        ORDER BY e.date ASC";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as  $value) {
                array_push($result, array('date' => $value['date'],
                                            'amount' => $value['amount'],
                                            'title_short' => $value['title_short'],
                                            'title_full' => $value['title_full']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_expenditure_subcategory_info'])) {
        try {

            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];
            $category_id = $_GET['category_id'];
            $category_parent_id = $_GET['category_parent_id'];

            $query = "SELECT ce.title
                        FROM category_expenditure ce
                        WHERE ce.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_parent_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT ce.title
                        FROM category_expenditure ce
                        WHERE ce.id = :category_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            $data['category_title'] = $stmt->fetch(PDO::FETCH_ASSOC)['title'];

            $query = "SELECT e.id AS expenditure_id,
                            DATE_FORMAT(e.date, '%d.%m.%Y') AS date,
                            e.amount,
                            mt.title_short,
                            mt.title_full
                        FROM expenditure e,
                            money_type mt
                        WHERE e.category_expenditure_id = :category_id
                            AND e.date BETWEEN :from_date AND :to_date
                            AND mt.id = e.money_type_id
                        ORDER BY e.date ASC";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as  $value) {
                array_push($result, array('date' => $value['date'],
                                            'amount' => $value['amount'],
                                            'title_short' => $value['title_short'],
                                            'title_full' => $value['title_full']));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_fee_info'])) {
        try {

            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];

            $query = "SELECT mt.id,
                            mt.title_short,
                            mt.title_full
                        FROM money_type mt";
            $stmt = $connect->prepare($query);
            $stmt->execute();
            $query_result = $stmt->fetchAll();
            $money_type_info = array();

            foreach ($query_result as $value) {
                $money_type_info[$value['id']] = array('title_short' => $value['title_short'],
                                                        'title_full' => $value['title_full']);
            }

            $query = "SELECT DATE_FORMAT(e.date, '%d.%m.%Y') AS date,
                            e.amount,
                            e.money_transfer_id
                        FROM expenditure e
                        WHERE e.date BETWEEN :from_date AND :to_date
                            AND e.category_expenditure_id IS NULL
                            AND e.money_transfer_id IS NOT NULL";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
            $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                $query = "SELECT c.amount,
                                c.money_type_id
                            FROM coming c,
                                money_transfer mt
                            WHERE mt.id = :money_transfer_id
                                AND c.id = mt.from_coming_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':money_transfer_id', $value['money_transfer_id'], PDO::PARAM_INT);
                $stmt->execute();
                $from_coming_info = $stmt->fetch(PDO::FETCH_ASSOC);

                $query = "SELECT c.amount,
                                c.money_type_id
                            FROM coming c,
                                money_transfer mt
                            WHERE mt.id = :money_transfer_id
                                AND c.id = mt.to_coming_id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':money_transfer_id', $value['money_transfer_id'], PDO::PARAM_INT);
                $stmt->execute();
                $to_coming_info = $stmt->fetch(PDO::FETCH_ASSOC);                

                array_push($result, array('date' => $value['date'],
                                            'fee_amount' => $value['amount'],
                                            'from' => array('amount' => $from_coming_info['amount'],
                                                            'title_short' => $money_type_info[$from_coming_info['money_type_id']]['title_short'],
                                                            'title_full' => $money_type_info[$from_coming_info['money_type_id']]['title_full']),
                                            'to' => array('amount' => $to_coming_info['amount'],
                                                            'title_short' => $money_type_info[$to_coming_info['money_type_id']]['title_short'],
                                                            'title_full' => $money_type_info[$to_coming_info['money_type_id']]['title_full'])));
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['get_static_category_amount'])) {
        try {

            $date_month = $_GET['date_month'];
            $date_year = $_GET['date_year'];

            $query = "SELECT asca.id,
                            asca.title,
                            asca.amount,
                            ascm.id AS ascm_id
                        FROM accounting_static_category_amount asca
                            LEFT JOIN accounting_static_category_mark ascm
                                ON ascm.accounting_static_category_amount_id = asca.id
                                    AND DATE_FORMAT(ascm.mark_date, '%m') = :month
                                    AND DATE_FORMAT(ascm.mark_date, '%Y') = :year 
                        ORDER BY asca.id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':month', $date_month, PDO::PARAM_STR);
            $stmt->bindParam(':year', $date_year, PDO::PARAM_STR);
            $stmt->execute();
            $query_result = $stmt->fetchAll();

            $result = array();

            foreach ($query_result as $value) {
                $result[$value['id']] = array('title' => $value['title'],
                                                'amount' => $value['amount'],
                                                'ascm_id' => $value['ascm_id']);
            }

            $data['data'] = $result;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['add_asca'])) {
        try {

            $title = $_GET['title'];
            $amount = $_GET['amount'];

            $query = "INSERT INTO accounting_static_category_amount (title, amount) VALUES (:title, :amount)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['update_asca'])) {
        try {

            $id = $_GET['id'];
            $title = $_GET['title'];
            $amount = $_GET['amount'];

            if ($title == '') {
                $query = "DELETE FROM accounting_static_category_amount WHERE id = :id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $query = "DELETE FROM accounting_static_category_mark WHERE accounting_static_category_amount_id = :id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $query = "UPDATE accounting_static_category_amount SET title = :title, amount = :amount WHERE id = :id";
                $stmt = $connect->prepare($query);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['set_mark'])) {
        try {
            
            $id = $_GET['id'];
            $date_year = $_GET['date_year'];
            $date_month = $_GET['date_month'];
            $mark_date = $date_year.'-'.$date_month.'-01';

            $query = "INSERT INTO accounting_static_category_mark (accounting_static_category_amount_id, mark_date)
                                                            VALUES (:asca_id, :mark_date)";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':asca_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':mark_date', $mark_date, PDO::PARAM_STR);
            $stmt->execute();

            $date['mark_date'] = $mark_date;
            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else if (isset($_GET['unset_mark'])) {
        try {
            
            $id = $_GET['id'];
            $ascm_id = $_GET['ascm_id'];

            $query = "DELETE FROM accounting_static_category_mark WHERE id = :id AND accounting_static_category_amount_id = :asca_id";
            $stmt = $connect->prepare($query);
            $stmt->bindParam(':id', $ascm_id, PDO::PARAM_INT);
            $stmt->bindParam(':asca_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $data['success'] = true;
        } catch (Exception $e) {
            $data['success'] = false;
            $data['message'] = "Error : ".$e->getMessage()." !!!";
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    function remove_coming_by_id ($coming_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT c.money_type_id,
    						c.date
    					FROM coming c
    					WHERE c.id = :coming_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':coming_id', $coming_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$coming_info = $stmt->fetch(PDO::FETCH_ASSOC);

    		$query = "DELETE FROM coming WHERE id = :coming_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':coming_id', $coming_id, PDO::PARAM_INT);
    		$stmt->execute();

    		return $coming_info;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function remove_expenditure_by_money_transfer_id ($money_transfer_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT e.money_type_id,
    						e.date
    					FROM expenditure e
    					WHERE e.money_transfer_id = :money_transfer_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_transfer_id', $money_transfer_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetch(PDO::FETCH_ASSOC);
            $expenditure_row_count = $stmt->rowCount();

    		$query = "DELETE FROM expenditure WHERE money_transfer_id = :money_transfer_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_transfer_id', $money_transfer_id, PDO::PARAM_INT);
    		$stmt->execute();


            if ($expenditure_row_count == 0) {
                return array();
            }

            $expenditure_info = array('money_type_id' => $query_result['money_type_id'],
                                        'date' => $query_result['date']);

    		return $expenditure_info;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function remove_money_transfer_info ($money_transfer_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT mt.from_coming_id,
    						mt.to_coming_id
    					FROM money_transfer mt
    					WHERE id = :money_transfer_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_transfer_id', $money_transfer_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

    		$query = "DELETE FROM money_transfer WHERE id = :money_transfer_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_transfer_id', $money_transfer_id, PDO::PARAM_INT);
    		$stmt->execute();
    		return $query_result;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_money_transfer_id ($coming_id) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT mt.id
						FROM money_transfer mt
						WHERE mt.from_coming_id = :coming_id 
							OR mt.to_coming_id = :coming_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':coming_id', $coming_id, PDO::PARAM_INT);
			$stmt->execute();
			$money_transfer_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

			return $money_transfer_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_expenditure_money_transfer ($money_type_id, $money_transfer_id, $amount, $date) {
    	GLOBAL $connect;

    	try {
    		$query = "INSERT INTO expenditure (money_type_id, money_transfer_id, date, amount)
    									VALUES (:money_type_id, :money_transfer_id, :date, :amount)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':money_transfer_id', $money_transfer_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    		$stmt->execute();
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_money_transfer($from_coming_id, $to_coming_id) {
    	GLOBAL $connect;
    	try {

    		$query = "INSERT INTO money_transfer (from_coming_id, to_coming_id) VALUES (:from_coming_id, :to_coming_id)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':from_coming_id', $from_coming_id, PDO::PARAM_INT);
    		$stmt->bindParam(':to_coming_id', $to_coming_id, PDO::PARAM_INT);
    		$stmt->execute();

    		$money_transfer_id = $connect->lastInsertId();
    		return $money_transfer_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_coming_money_transfer ($money_type_id, $date, $amount) {
    	GLOBAL $connect;
    	try {

    		$query = "INSERT INTO coming (money_type_id, date, amount) VALUES (:money_type_id, :date, :amount)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    		$stmt->execute();

    		$coming_id = $connect->lastInsertId();

    		return $coming_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_category_coming () {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT cc.id AS category_coming_id,
    						cc.category_coming_id AS parent_id,
    						cc.title
    					FROM category_coming cc
    					ORDER BY cc.id ASC, cc.category_coming_id DESC";
    		$stmt = $connect->prepare($query);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$result = array();
    		foreach ($query_result as $value) {
				$result[$value['category_coming_id']] = array('title' => $value['title'],
                                                                'parent_id' => $value['parent_id']);
				if ($value['parent_id'] != 0) {
					$result[$value['category_coming_id']]['title'] = $result[$value['parent_id']]['title'].' | '.$result[$value['category_coming_id']]['title'];
				}
    		}
    		return $result;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_expenditure_coming () {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT ce.id AS category_expenditure_id,
    						ce.category_expenditure_id AS parent_id,
    						ce.title
    					FROM category_expenditure ce
    					ORDER BY ce.id ASC, ce.category_expenditure_id DESC";
    		$stmt = $connect->prepare($query);
    		$stmt->execute();
    		$query_result = $stmt->fetchAll();

    		$result = array();
    		foreach ($query_result as $value) {
				$result[$value['category_expenditure_id']] = array('title' => $value['title'],
                                                                    'parent_id' => $value['parent_id']);
				if ($value['parent_id'] != 0) {
					$result[$value['category_expenditure_id']]['title'] = $result[$value['parent_id']]['title'].' | '.$result[$value['category_expenditure_id']]['title'];
				}
    		}
    		return $result;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }


    function insert_accounting_amount($account_type, $date, $amount, $money_type_id, $category_id, $group_type, $subject_title) {
    	GLOBAL $connect;

    	try {

    		if ($account_type == 'coming') {
    			$coming_id = insert_coming($money_type_id, $category_id, $group_type, $subject_title, $date, $amount);
    		} else if ($account_type == 'expenditure') {
    			$expenditure_id = insert_expenditure($money_type_id, $category_id, $date, $amount);
    		}

	 		set_begin_end_day_of_balance($money_type_id, $date);
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_coming ($money_type_id, $category_id, $group_type, $subject_title, $date, $amount) {
    	GLOBAL $connect;

    	try {

    		$query = "";

    		if ($category_id != '') {
    			$query = "INSERT INTO coming (money_type_id, category_coming_id, date, amount)
    									VALUES (:money_type_id, :category_coming_id, :date, :amount)";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    			$stmt->bindParam(':category_coming_id', $category_id, PDO::PARAM_INT);
    			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    			$stmt->execute();
    		} else if ($subject_title != '') {
    			$query = "INSERT INTO coming (money_type_id, group_type, subject_title, date, amount)
    									VALUES (:money_type_id, :group_type, :subject_title, :date, :amount)";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    			$stmt->bindParam(':group_type', $group_type, PDO::PARAM_STR);
    			$stmt->bindParam(':subject_title', $subject_title, PDO::PARAM_STR);
    			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    			$stmt->execute();
    		} else {
    			$query = "INSERT INTO coming (money_type_id, group_type, date, amount)
    									VALUES (:money_type_id, :group_type, :date, :amount)";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    			$stmt->bindParam(':group_type', $group_type, PDO::PARAM_STR);
    			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    			$stmt->execute();
    		}

    		$coming_id = $connect->lastInsertId();
    		return $coming_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function insert_expenditure ($money_type_id, $category_id, $date, $amount) {
    	GLOBAL $connect;

    	try {

    		$query = "INSERT INTO expenditure (money_type_id, category_expenditure_id, date, amount)
    									VALUES (:money_type_id, :category_expenditure_id, :date, :amount)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':category_expenditure_id', $category_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    		$stmt->execute();
    		$expenditure_id = $connect->lastInsertId();

    		return $expenditure_id;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function set_begin_end_day_of_balance ($money_type_id, $date) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT eodb.date
    					FROM end_of_day_balance eodb
    					WHERE eodb.money_type_id = :money_type_id
    						AND eodb.date < :date
    					ORDER BY eodb.date DESC
    					LIMIT 1";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 1) {
    			$first_date = $stmt->fetch(PDO::FETCH_ASSOC)['date'];
    		} else {
    			$first_date = $date;
    		}

    		$query = "SELECT eodb.date 
    					FROM end_of_day_balance eodb
    					WHERE eodb.money_type_id = :money_type_id
    						AND eodb.date > :date
    					ORDER BY eodb.date DESC
    					LIMIT 1";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 1) {
    			$end_date = $stmt->fetch(PDO::FETCH_ASSOC)['date'];
    		} else {
    			$end_date = $date;
    		}

    		if ($first_date == $end_date) {
    			$end_day_of_balance = set_end_day_of_balance($money_type_id, $date);
    		} else {
    			while ($first_date != $end_date) {
	    			$end_of_day_balance = set_end_day_of_balance($money_type_id, $first_date);
	    			set_begin_day_of_balance($money_type_id, $end_of_day_balance['date'], $end_of_day_balance['amount']);
	    			$first_date = $end_of_day_balance['date'];
	    		}
	    		$end_of_day_balance = set_end_day_of_balance($money_type_id, $end_date);
    		}
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function set_end_day_of_balance ($money_type_id, $date) {
    	GLOBAL $connect;

    	try {

    		$begin_day_of_balance_amount = get_begin_of_day_balance_amount($money_type_id, $date);
    		$coming_sum = get_coming_sum($money_type_id, $date);
    		$expenditure_sum = get_expenditure_sum($money_type_id, $date);

    		$total_amount = $begin_day_of_balance_amount + $coming_sum - $expenditure_sum;

    		$query = "SELECT eodb.id
    					FROM end_of_day_balance eodb
    					WHERE eodb.date = :date
    						AND eodb.money_type_id = :money_type_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 1) {
    			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

    			$query = "UPDATE end_of_day_balance SET amount = :amount WHERE id = :id";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':amount', $total_amount, PDO::PARAM_STR);
    			$stmt->bindParam(':id', $query_result['id'], PDO::PARAM_INT);
    			$stmt->execute();
    		} else {
    			$query = "INSERT INTO end_of_day_balance (money_type_id, date, amount)
    												VALUES (:money_type_id, :date, :amount)";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    			$stmt->bindParam(':amount', $total_amount, PDO::PARAM_STR);
    			$stmt->execute();
    		}

    		$result = array('amount' => $total_amount,
    						'date' => date('Y-m-d', strtotime($date.' + 1 days')));

			return $result;    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_expenditure_sum($money_type_id, $date) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT e.amount
    					FROM expenditure e
    					WHERE e.date = :date
    						AND e.money_type_id = :money_type_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		$amount = 0.0;
    		if ($row_count > 0) {
    			$query_result = $stmt->fetchAll();

    			foreach ($query_result as $value) {
    				$amount += $value['amount'];
    			}
    		}
    		return $amount;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_coming_sum ($money_type_id, $date) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT c.amount
    					FROM coming c
    					WHERE c.money_type_id = :money_type_id
    						AND c.date = :date";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		$amount = 0.0;
    		if ($row_count > 0) {
    			$query_result = $stmt->fetchAll();

    			foreach ($query_result as $value) {
    				$amount += $value['amount'];
    			}
    		}
    		return $amount;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function get_begin_of_day_balance_amount ($money_type_id, $date) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT bodb.amount
    					FROM begin_of_day_balance bodb
    					WHERE bodb.money_type_id = :money_type_id
    						AND bodb.date = :date";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 1) {
    			return $stmt->fetch(PDO::FETCH_ASSOC)['amount'];
    		}

    		$query = "INSERT INTO begin_of_day_balance (money_type_id, date, amount)
    												VALUES (:money_type_id, :date, 0.0)";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->execute();

    		return 0.0;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    function set_begin_day_of_balance ($money_type_id, $date, $amount) {
    	GLOBAL $connect;

    	try {

    		$query = "SELECT bodb.id
    					FROM begin_of_day_balance bodb
    					WHERE bodb.date = :date
    						AND bodb.money_type_id = :money_type_id";
    		$stmt = $connect->prepare($query);
    		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    		$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    		$stmt->execute();
    		$row_count = $stmt->rowCount();

    		if ($row_count == 1) {
    			$query_result = $stmt->fetch(PDO::FETCH_ASSOC);

    			$query = "UPDATE begin_of_day_balance SET amount = :amount WHERE id = :id";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    			$stmt->bindParam(':id', $query_result['id']);
    			$stmt->execute();
    		} else {
    			$query = "INSERT INTO begin_of_day_balance (money_type_id, date, amount)
    													VALUES (:money_type_id, :date, :amount)";
    			$stmt = $connect->prepare($query);
    			$stmt->bindParam(':money_type_id', $money_type_id, PDO::PARAM_INT);
    			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    			$stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    			$stmt->execute();
    		}
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
?>