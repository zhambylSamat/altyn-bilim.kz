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

    // echo json_encode($payments['student_used_promo_code_infos'], JSON_UNESCAPED_UNICODE);

?>
<style type="text/css">
	.swal-wide{
	    width:850px !important;
	}
	#no-payment-exist-content {
		border-radius: 5px;
		border: 1px solid #4FA5BF;
		background-color: #DEF2F8;
		margin: 1% 2%;
		padding: 1% 2%;
	}
	#no-payment-exist-text {
		font-size: 16px;
		font-weight: bold;
	}
</style>
<h4>Төлем жасау парақшасы</h4>
<p class='payment-subtitle'><i class="fas fa-circle" style='font-size: 5px; vertical-align: middle;'></i> <b>Төлем ақпараттарыңыз толық қауіпсіздікте</b></p>
<?php
	if (count($payments['payment_infos']) > 0) {
?>
<form class='payment-form' action='payment/payment.php' method='post'>
	<?php
		$html = "";
		$html .= "<input type='hidden' id='promo-code-discount' value='".$payments['discount_for_promo_codes']."'>";
		foreach ($payments['student_used_promo_code_infos'] as $promo_code_info) {
			$html .= "<div class='col-md-7 col-sm-7 col-xs-12'>";
				$html .= "<div class='form-group'>";
					$html .= "<label class='control-label col-md-4 col-sm-4 col-xs-12' style='text-align: center;'>";
						$html .= $promo_code_info['last_name'].' '.$promo_code_info['first_name']."<br>досыңнан ".$payments['discount_for_promo_codes']."% жеңілдік";
					$html .= "</label>";
				
					$html .= "<div class='col-md-8 col-sm-8 col-xs-12'>";
						$html .= "<select class='form-control use-promo-code-selection' name='student_used_promo_code[]'>";
							$html .= "<option value=''>Қолданғың келетін пәнді таңда</option>";
							foreach ($payments['payment_infos'] as $group_info_id => $group_info) {
								$html .= "<option value='".$group_info['group_student_id']."|".$promo_code_info['student_used_promo_code_id']."'>".$group_info['subject_title'].' | '.$group_info['group_name']."</option>";
							}
						$html .= "</select>";
					$html .= "</div>";
				$html .= "</div>";
			$html .= "</div>";
		}
		echo $html;
	?>
	<div class='col-md-7 col-sm-7 col-xs-12'>
		<label class="control-label" style='color: green;'>Төлем жасайтын пәніңіз:</label>
		<table class='table table-bordered'>
			<?php
				$html = "";

				foreach ($payments['payment_infos'] as $group_info_id => $value) {
					$group_extra_text = "";
					if ($value['is_army_group']) {
						$group_extra_text = " | <b>Армия</b>";
					}
					$payment = $value['payment']['sum'];
					$payment_html = "";
					if (count($value['discount']) > 0) {
						if ($value['discount']['type'] == 'percent') {
							$payment *= (1-($value['discount']['amount']/100.00));
						} else if ($value['discount']['type'] == 'money') {
							$payment -= $value['discount']['amount'];
						}
						$payment_html = "<span style='text-decoration: line-through;'>".$value['payment']['sum']."</span><span> теңгенің орнына:</span>";
						$payment_html .= "<p><b>".$value['discount']['amount']."% жеңілдікпен: ".$payment." тг.</b></p>";
						$payment_html .= "<input type='hidden' class='fixed-discount' value='".$value['discount']['amount']."'>";
					} else {
						$payment_html = "<span>".$payment." тг.</span>";
					}
					$html .= "<tr id='row-".$value['group_student_id']."'>";
						$html .= "<td><center><input type='checkbox' class='select-group-to-pay' name='group_student_id[]' value='".$value['group_student_id']."' checked></center></td>";
						$html .= "<td><span>".$value['subject_title']."</span></td>";
						$html .= "<td><span>".$value['group_name'].$group_extra_text."</span></td>";
						$html .= "<td class='amounts'>";
							$html .= "<span class='payment-amount-info'>".$payment_html."</span>";
							$html .= "<input type='hidden' name='amount' value='".$payment."'>";
							$html .= "<input type='hidden' name='init-amount' value='".$value['payment']['sum']."'>";
						$html .= "</td>";
					$html .= "</tr>";
				}
				echo $html;
			?>
		</table>
		<p id='payment-total-amount-info' class='pull-right'>Жалпы соммасы: <span id='payment-total-amount'>0</span> тг.</p>
	</div>
	<div class='col-md-7 col-sm-7 col-xs-12'>
		<div class='form-group' style='margin-bottom: 2%;'>
			<label class="control-label" style='color: green;'>Ұялы телефоныңыздың номері:</label>
			<div class="input-group">
				<div class="input-group-addon">+7</div>
				<input type="text" class="form-control" name='phone-number' placeholder="Ұялы телефоның" required value='<?php echo $payments['student_info']['phone']; ?>'>
			</div>
		</div>
		<div class='form-group'>
			<label class="control-label" style='color: green;'>Электрондық почтаңызды жазыңыз. Осы почтаңызға төлемнің чегі жіберіледі:</label>
			<input type="email" name="email" class='form-control' placeholder="Мысалы: info@mail.ru" required value='<?php echo $payments['student_info']['email']; ?>'>
		</div>
		<label class="control-label" style='margin-bottom: 2%; color: green;'>"Төлем жасау" батырмасын басыңыз. Осы арқылы сіз карточкаңызды енгізетін парақшаға өтесіз:</label>
		<center><button type='submit' disabled class='btn btn-md btn-success'>Төлем жасау</button></center>
	</div>
</form>
<?php
	} else {
		$html = "<div class='row' id='no-payment-exist-content'>";
			$html .= "<center>";
				$html .= "<p id='no-payment-exist-text'>Оқуыңның төлемі өткізілген. Келесі төлем жасайтын күнің:<p>";
				$html .= "<div class='col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-12'>";
					$html .= "<table class='table' style='font-size: 16px; color: green; margin-bottom: 0;'>";
						foreach ($payments['payed_payment_infos'] as $value) {
							$html .= "<tr>";
								$html .= "<th style='border: none; padding-top: 0; padding-bottom: 0;'><p class='pull-right'>".$value['subject_title'].":</p></th>";
								$html .= "<th style='border: none; padding-top: 0; padding-bottom: 0;'><p class='pull-left'>".$value['payment']['access_until']."</p></th>";
							$html .= "</tr>";
						}
					$html .= "</table>";
				$html .= "</div>";
				$html .= "<hr>";
			$html .= "</center>";
		$html .= "</div>";
		echo $html;
	}
?>
<div class='row'>
	<div class='col-md-7 col-sm-7 col-xs-12'>
		<div id='payment-video-vimeo-instruction'>
			
		</div>
	</div>
</div>

<div class='modal fade' id='payment-instruction-modal' tabindex='-1' role='dialog'>
	<div class='modal-dialog' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Төлем жасау видео инструкциясы</h4>
			</div>
			<div class='modal-body'>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	render_payment_video_instruction();

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

		// if ($json.success) {
		// 	if ($json.result.length > 0) {
		// 		$payment_success = $payment_success_add_subject;
		// 	} else {
		// 		$payment_success = $payment_success_no_subject;
		// 	}
		// } else {
		// 	$payment_success = $payment_success_no_subject;
		// }
		$payment_success = $payment_success_no_subject;

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