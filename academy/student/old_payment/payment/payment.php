<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/student/payment/view.php');
	include_once($root.'/common/constants.php');

	$group_student_ids = isset($_POST['group_student_id']) ? $_POST['group_student_id'] : array();

	if (count($group_student_ids) == 0 || !isset($_SESSION['user_id'])) {
		header('Location:../index.php');
	}

	$phone = $_POST['phone-number'];
	$email = $_POST['email'];

	$students_payment = get_students_no_payments();
	// echo json_encode($students_payment, JSON_UNESCAPED_UNICODE);
	$total_amount = 0.0;

	$student_payment_info = array('groups' => array(),
									'order_id' => '');
	foreach ($students_payment['payment_infos'] as $value) {
		if (in_array($value['group_student_id'], $group_student_ids)) {
			$student_payment_info['groups'][$value['group_student_id']] = array('amount' => $value['payment']['sum'],
																				'partial_payment_days' => $value['payment']['days']);
			$total_amount += $value['payment']['sum'];
		}
	}

	$order_id = time().$_SESSION['user_id'];
	$student_payment_info['order_id'] = $order_id;
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
	// echo json_encode($student_payment_info);

	ksort($request); //sort alphabetically
	array_unshift($request, 'payment.php');
	array_push($request, $payment_info['secret_key_for_accepting_payment']);

	$request['pg_sig'] = md5(implode(';', $request));
	unset($request[0], $request[1]);
	$query = http_build_query($request);
	header('Location:https://api.paybox.money/payment.php?'.$query);
?>