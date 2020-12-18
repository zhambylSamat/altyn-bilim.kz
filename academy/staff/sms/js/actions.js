function check_sms_statuses () {
	$message_ids = [];
	$('.sms-history-row').each(function() {
		$is_finish_step = $(this).data('is-finish-step');
		$message_id = $(this).data('id');

		if ($is_finish_step == 0) {
			$message_ids.push($message_id);	
		}
	});
	if ($message_ids.length > 0) {
		$.when(check_sms_status_by_id($message_ids)).done(function($data) {
			console.log($data);
			$json = $.parseJSON($data);
			if ($json.success) {
				$.each($json.statuses, function($message_id, $value) {
					console.log($message_id, $value);
					$('#message-id-'+$message_id).attr('data-is-finish-step', $value['is_finish_step']);
					$('#message-id-'+$message_id).find('.status-text').html($value['status_text']);
					lightAlert($('#message-id-'+$message_id), 'green', 0, 500);
				});
			}
		});
	}
}

function check_sms_status_by_id ($message_ids) {
	$message_ids_str = JSON.stringify($message_ids);
	return $.ajax({
		type: "GET",
		url: 'sms/controller.php?check_sms_status&message_ids='+$message_ids_str
	});
}

$(document).on('click', '.load-more-btn', function() {
	$last_num = $('.sms-history-row').last().find('.count').text();
	$.ajax({
		type: 'GET',
		url: 'sms/components/sms_history.php?offset='+$last_num,
		beforeSend: function() {
			$('.load-more-btn').text("Загрузка...");
		},
		success: function($html) {
			$('.sms-history-table').append($html);
			$('.load-more-btn').text("Загрузить еще +");
		}
	});
});

$(document).ready(function() {
	get_mobizon_balance();
});

function get_mobizon_balance() {
	$.ajax({
		type: 'GET',
		url: 'sms/controller.php?get_mobizon_balance',
		beforeSend: function() {
			$('#mobizon-balance').html('Загрузка...');
		},
		success: function($data) {
			console.log($data);
			$json = $.parseJSON($data);
			if ($json.success) {
				$('#mobizon-balance').html($json.balance);
			} else {
				$('#mobizon-balance').html('Some error');
			}
		}
	});
}