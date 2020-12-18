<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/payment/view.php');
	include_once($root.'/common/constants.php');

	$group_student_ids = isset($_POST['group_student_id']) ? $_POST['group_student_id'] : array();
	$student_used_discount_ids = isset($_POST['student_used_promo_code_id']) ? $_POST['student_used_promo_code_id'] : array();
	// $group_student_ids = array('2097', '2095');

	if (count($group_student_ids) == 0 || !isset($_SESSION['user_id'])) {
		header('Location:../index.php');
	}

	$student_id = $_SESSION['user_id'];
	$phone = $_POST['phone-number'];
	// $phone = 7771234567;
	$email = $_POST['email'];
	// $email = 'zhambyl.9670@gmail.com';

	// echo $_POST['phone-number']."<br>";
	// echo $_POST['email']."<br>";
	// echo "<br>";
	// echo json_encode($_POST['group_student_id'])."<br>";
	// echo "<br><br><br>";
	// print_r($_POST['group_student_id']);
	// echo "<br><br>";

	$students_payment = get_students_no_payments();
	// echo json_encode($students_payment, JSON_UNESCAPED_UNICODE);
	$total_amount = 0.0;
	$student_used_promo_code_infos = get_student_active_promo_codes($student_id);
	$student_payment_info = array('g' => array(),
									'oid' => '');
	foreach ($students_payment['payment_infos'] as $value) {
		if (in_array($value['group_student_id'], $group_student_ids)) {
			$transfer_from_group = get_transfer_from_group($value['group_student_id']);
			$discount_info = get_group_student_discount($value['group_student_id'], $transfer_from_group);
			$payment_with_discount = $value['payment']['sum'];
			$discount_group_student_id = '';
			$percent = 0.0;
			if (count($discount_info) > 0) {
				$discount_group_student_id = $discount_info['discount_group_student_id'];
				if ($discount_info['type'] == 'percent') {
					// $payment_with_discount *= 1-($discount_info['amount']/100.00);
					$percent += $discount_info['amount'];
				}
				// else if ($discount_info['type'] == 'money') {
				// 	$payment_with_discount -= $discount_info['amount'];
				// }
			}

			$supc_ids = array();
			if (isset($student_used_discount_ids[$value['group_student_id']])) {
				foreach ($student_used_discount_ids[$value['group_student_id']] as $student_used_promo_code_id) {
					if (isset($student_used_promo_code_infos[$student_used_promo_code_id])) {
						$percent += $discount_for_promo_codes;
						array_push($supc_ids, $student_used_promo_code_id);
					}
				}
			}

			$payment_with_discount *= (100-$percent)/100;
			if ($payment_with_discount < 0) {
				$payment_with_discount = 0;
			}

			$student_payment_info['g'][$value['group_student_id']] = array('1' => $payment_with_discount, // amount
																			'3' => $discount_group_student_id, // discount_group_student_id
																			'4' => $value['payment']['days'], // partial_payment_days
																			'supc_ids' => $supc_ids); // student_used_promo_code_ids
 			$total_amount += $payment_with_discount;
		}
	}

	function get_transfer_from_group ($group_student_id) {
		GLOBAL $connect;

		try {

			$query = "SELECT gs.transfer_from_group
						FROM group_student gs
						WHERE gs.id = :group_student_id";
			$stmt = $connect->prepare($query);
			$stmt->bindParam(':group_student_id', $group_student_id, PDO::PARAM_INT);
			$stmt->execute();
			$transfer_from_group = $stmt->fetch(PDO::FETCH_ASSOC)['transfer_from_group'];
			return $transfer_from_group;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	$order_id = time().$_SESSION['user_id'];
	$student_payment_info['oid'] = $order_id;
	$some_salt = md5($order_id);

	$total_amount_info = number_format($total_amount, 2, ',', ' ');

	$request = [
	    'pg_merchant_id'=> $payment_info['merchant_id'],
	    'pg_amount' => $total_amount,
	    'pg_salt' => $some_salt,
	    'pg_order_id'=> $order_id,
	    'pg_description' => $total_amount_info.' теңге',
	    'pg_result_url' => $payment_info['academy']['result_url'],
	    'pg_success_url' => $payment_info['academy']['success_url'],
	    'pg_failure_url' => $payment_info['academy']['failure_url'],
	    'pg_user_phone' => '+7'.$phone,
	    'pg_user_contact_email' => $email,
	   	'pg_testing_mode' => 0,
	    'payment_info' => json_encode($student_payment_info)
	];

	// print_r($student_payment_info);
	// echo "<br><br>";
	// print_r($request);
	// echo "<br><br>";
	// echo json_encode($student_payment_info);

	ksort($request); //sort alphabetically
	array_unshift($request, 'payment.php');
	array_push($request, $payment_info['secret_key_for_accepting_payment']);

	$request['pg_sig'] = md5(implode(';', $request));
	unset($request[0], $request[1]);
	$query = http_build_query($request);
	header('Location:https://api.paybox.money/payment.php?'.$query);
?>