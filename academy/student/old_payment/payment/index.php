<?php
	$LEVEL = 1;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');
	include_once($root.'/student/payment/view.php');

    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);

    $payments = get_students_no_payments();

    // echo json_encode($payments, JSON_UNESCAPED_UNICODE);

?>
<style type="text/css">
	.swal-wide{
    width:850px !important;
}
</style>
<center><h4>Курстардың төлем жүргізу парақшасы</h4></center>
<?php
	if (count($payments['payment_infos'])) {
?>
<form class='payment-form' action='payment/payment.php' method='post'>
	<div class='col-md-7 col-sm-7 col-xs-12'>
		<label class="control-label" style='color: green;'>1) Керек пәннің жанына "птичка" белгісін қойыңыз:</label>
		<table class='table table-bordered'>
			<?php
				$html = "";

				foreach ($payments['payment_infos'] as $group_info_id => $value) {
					$html .= "<tr>";
						$html .= "<td><center><input type='checkbox' class='select-group-to-pay' name='group_student_id[]' value='".$value['group_student_id']."' checked></center></td>";
						$html .= "<td><span>".$value['subject_title']."</span></td>";
						$html .= "<td><span>".$value['group_name']."</span></td>";
						$html .= "<td>";
							$html .= "<span>".$value['payment']['sum']." тг.</span>";
							$html .= "<input type='hidden' name='amount' value='".$value['payment']['sum']."'>";
						$html .= "</td>";
					$html .= "</tr>";
				}
				echo $html;
			?>
		</table>
	</div>
	<div class='col-md-7 col-sm-7 col-xs-12'>
		<div class='form-group' style='margin-bottom: 2%;'>
			<label class="control-label" style='color: green;'>2) Ұялы телефоныңыздың номерін жазыңыз:</label>
			<div class="input-group">
				<div class="input-group-addon">+7</div>
				<input type="text" class="form-control" name='phone-number' placeholder="Ұялы телефоның" required value='<?php echo $payments['student_info']['phone']; ?>'>
			</div>
		</div>
		<div class='form-group'>
			<label class="control-label" style='color: green;'>3) Электрондық поштаңызды жазыңыз. Осы поштаңызға төлемнің чегі жіберіледі:</label>
			<input type="email" name="email" class='form-control' placeholder="Жеке электрондық поштаң" required value='<?php echo $payments['student_info']['email']; ?>'>
		</div>
		<label class="control-label" style='margin-bottom: 3%; color: green;'>4) "Төлем жасау" батырмасын басыңыз. Сіз карточкаңызды енгізетін парақшаға ауысасыз. Төлем ақпараттарыңыз қауіпсіздікте болады:</label>
		<p id='payment-total-amount-info' class='pull-right'>Жалпы соммасы: <span id='payment-total-amount'>0</span> тг.</p>
		<button type='submit' disabled class='btn btn-md btn-success'>Төлем жасау</button>
	</div>
</form>
<?php } ?>

<script type="text/javascript">

	$payment_success_add_subject = {
		choose_new_subject: true,
		showCancelButton: true,
		imgUrl: 'http://localhost/altynbilim/academy/student/img/50.png',
		// imgUrl: 'https://online.altyn-bilim.kz/academy/student/img/50.png',
		imageWidth: 400,
		imageHeight: 200,
		confirmButtonText: 'Қосымша жаңа пәнге тіркелу',
		title: 'Төлем сәтті аяқталды!<br>Келесі курсқа 50% жеңілдік ал!',
	};

	$payment_success_no_subject = {
		choose_new_subject: true,
		showCancelButton: false,
		imgUrl: '',
		imageWidth: 0,
		imageHeight: 0,
		confirmButtonText: 'ОК',
		title: 'Төлем сәтті аяқталды!',
	};


check_any_alerts_shown();

function check_any_alerts_shown() {
	$payment_status = getUrlParameter('payment_status');
	if ($payment_status !== undefined) {
		if ($payment_status == 'success') {
			set_payment_success_alert();
		} else if ($payment_status == 'fail') {
			set_payment_fail_alert();
		}

		var uri = window.location.toString();
		if (uri.indexOf("?") > 0) {
		    var clean_uri = uri.substring(0, uri.indexOf("?"));
		    window.history.replaceState({}, document.title, clean_uri);
		}
	}
}

function set_payment_success_alert() {
	$.when(get_not_started_subjects()).done(function($result) {
		$json = $.parseJSON($result);

		if ($json.success) {
			if ($json.result.length > 0) {
				$payment_success = $payment_success_add_subject;
			} else {
				$payment_success = $payment_success_no_subject;
			}
		} else {
			$payment_success = $payment_success_no_subject;
		}

		Swal.fire({
			width: '50em',
			title: $payment_success.title,
			confirmButtonText: $payment_success.confirmButtonText,
			showCancelButton: $payment_success.showCancelButton,
			cancelButtonText: 'Жабу',
			imageUrl: $payment_success.imgUrl,
			imageWidth: $payment_success.imageWidth,
			imageHeight: $payment_success.imageHeight,
			icon: 'success'
		}).then((result) => {
			if (result.isConfirmed) {
				if ($payment_success.choose_new_subject) {
					$element = $('.registration-navigation').parents('.navigation');
					set_navigation($element);
				}
			}
		});
	});
}

function set_payment_fail_alert() {
	Swal.fire({
		width: '50em',
		title: 'Төлем сәтсіз аяқталды!<br>Қайталап көр.',
		icon: 'error'
	});
}

$(document).load('.payment-form', function() {
	check_payment_checkbox();
});

</script>