<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/connection.php');
	include_once($root.'/common/check_authentication.php');
	check_admin_access();

	if (!isset($_GET['student_id']) || !isset($student_id)) {
    	header($ab_root.'/academy');
    }
    if (isset($_GET['student_id'])) {
    	$student_id = $_GET['student_id'];
    }

    include_once($root.'/staff/student/student_cabinet/view.php');

    $student_payment_history = get_student_payment_history($student_id);
    $student_free_coins = get_student_free_coins($student_id);

    // echo json_encode($student_payment_history, JSON_UNESCAPED_UNICODE);
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<br>
		<div class="alert alert-success extra-coins-added-notification" style='display: none;' role="alert"><b>Оқушыға 50 монета берілді</b></div>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<h3 class='text-info'>Оқушыда қалған монеталар саны: <?php echo $student_free_coins; ?></h3>
	</div>
	<div class='col-md-6 col-sm-6 col-xs-12'>
		<br>
		<button class='btn btn-info btn-sm btn-block' data-toggle="modal" data-target="#set-bonuses-modal">Оқушыға қосымша бонус күндер беру</button>
		<br>
		<button class='btn btn-warning btn-sm btn-add-coins btn-block' data-id='<?php echo $student_id; ?>'>Оқушыға қосымша 50 монета беру</button>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<?php
			$html = '<div>';
				$html .= "<p id='title'>Өткен группалардан артылып қалған күндер:</p>";
				$html .= "<ul style='color: darkgreen'>";
					$total_days = 0;
					foreach ($student_payment_history['free_available_days'] as $value) {
						$html .= "<li>".$value['comment'].": ".$value['days']." күн</li>";
						$total_days += $value['days'];
					}
					$html .= "<li>Барлығы: ".$total_days." күн</li>";
				$html .= "</ul>";
				$html .= "<b style='color: #5CB85C;'>Келесі төлемде автоматты түрде ескеріледі!</b>";
			$html .= "</div>";
			foreach ($student_payment_history['payment_infos'] as $subject_id => $v) {
				$html .= "<h3><b>".$v['subject_title']."</b></h3>";
				$html .= "<table class='table table-bordered'>";
				foreach ($v['groups'] as $group_id => $group) {
					$payment_count = count($group['payment']);
					$html .= "<tr>";
						$html .= "<td rowspan='".$payment_count."'>";
							$html .= "<b>".$group['group_name']."</b><br>";
							$html .= "<span>Басталған уақыты: </span><b>".$group['start_date']."</b><br>";
							// $html .= "<span>Аяқталған уақыты: </span><b>".date('d.m.Y', strtotime($group['status_change_date'].' + 1 days'))."</b><br>";
						$html .= "</td>";
					$count = 0;
					foreach ($group['payment'] as $payment_id => $payment) {
						$td_class = '';
						if ($payment['payment_type'] == 'money') {
							$td_class .= 'success';
						}
						$payment_type = $payment['payment_type'] == 'money' ? 'Ақша' : 'Баланс';
						$payed_date = $payment['payed_date'];
						$used_date = $payment['used_date'];
						$start_date = $payment['payment_start_date'] != '' ? $payment['payment_start_date'] : $payment['start_date'];
						$access_until = date('d.m.Y', strtotime($payment['access_until']));
						if ($payment['finished_date'] == '') {
							$finished_date = 'Оқуы жалғасып жатыр';
						} else {
							$finished_date = date('d.m.Y', strtotime($payment['finished_date']));
						}
						$payment_days = $payment['partial_payment_days'] == '' ? '1 ай' : $payment['partial_payment_days'].' күн';
						$is_full_finished = $payment['full_finished'] == '1' ? 'Ия' : "Жоқ";
						$html .= $count >= 1 ? "<tr>" : '';
							$html .= "<td class='".$td_class."'>";
								$html .= "<b>".$payment_type."</b><br>";
								if ($payment['payment_type'] == 'balance') {
									// $html .= "<span>Баланстар қайдан келдгендері төменде көрсетілген:<span>";
									$html .= "<hr>";
									foreach ($payment['cause'] as $cause) {
										if ($cause['type'] == 'group_info') {
											$html .= "<span>".$cause['group_name'].": <span><b>".$cause['days']." күн</b><br>";
										} else if ($cause['type'] == 'bonus') {
											$html .= "<span>".$cause['comment'].": <span><b>".$cause['days']." күн</b><br>";
										}
									}
								}
							$html .= "</td>";
							$html .= "<td class='".$td_class."'>";
								if ($payment['payment_type'] == 'money') {
									$html .= "<span>Төленген уақыт: </span><b>".$payed_date."</b><br>";
								} else {
									$html .= "<span>Бонус күндері қолданылған уақыт: </span><b>".$payed_date."</b><br>";
								}
								$html .= "<span>Оплатасы жүре басталған уақыт: </span><b>".$start_date."</b><br>";
								$html .= "<span>Оплатасы біту керек уақыт: </span><b>".$access_until."</b><br>";
								$html .= "<span>Оплатасы аяқталған уақыт: </span><b>".$finished_date."</b><br>";
								$html .= "<span>Оплатасының ұзақтығы: </span><b>".$payment_days."</b><br>";
								$html .= "<span>Оплатасы толық аяқталдыма? </span><b>".$is_full_finished."</b><br>";
								if ($payment['full_finished'] != '1') {
									$html .= "<span>Осы группадан артылып қалған күндер: </span><b>".$group['left_days']." күн</b>";
								}
							$html .= "</td>";
						$html .= $count >= 1 ? "</tr>" : '';
						$count++;
					}
					$html .= "</tr>";
				}
				$html .= "</table><hr>";
			}
			echo $html;
		?>
	</div>
</div>

<div class='modal fade' id='set-bonuses-modal' tabindex='-1'>
	<div class='modal-dialog' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title">Оқушыға қосымша бонус күндер беру</h4>
			</div>
			<div class='modal-body'>
				<form class='form-horizontal' action='<?php echo $ab_root; ?>/academy/staff/student/student_cabinet/controller.php' method='post'>
					<input type="hidden" name="student-id" value='<?php echo $student_id; ?>'>
					<div class='form-group'>
						<label for='days' class='control-label col-md-4 col-sm-4 col-xs-6'>
							Бонус күндер: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
						</label>
						<div class='col-md-8 col-sm-8 col-xs-6'>
							<input type="number" min='1' class='form-control' step='1' id='days' name="days" required value='1'>
						</div>
					</div>
					<div class='form-group'>
						<label for='comment' class='control-label col-md-4 col-sm-4 col-xs-6'>
							Неге бонус берген жайлы коммент: <span style="font-size: 10px; color: #E52C38;" class="glyphicon glyphicon-asterisk"></span>
						</label>
						<div class='col-md-8 col-sm-8 col-xs-6'>
							<textarea name='comment' class='form-control' required id='comment' rows='4' cols='50'></textarea>
						</div>
					</div>
					<div class='form-group'>
						<center>
							<input type="submit" class='btn btn-sm btn-success' name="insert-bonus-days" value='Сақтау'>
						</center>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
