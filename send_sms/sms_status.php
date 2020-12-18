<?php
	
	$NEW = "NEW";
	$ENQUEUED = "ENQUEUD";
	$ACCEPTED = "ACCEPTD";
	$UNDELIVERED = "UNDELIV";
	$REJECTED = "REJECTD";
	$DELIVERED = "DELIVRD";
	$DELETED = "DELETED";
	$POSTDELIVERED = "PDLIVRD";
	$EXPIRED = "EXPIRED";

	// READY_FOR_SEND
	// SENT

	$DECLINED = "DECLINED";
	$MODERATION = "MODERATION";

	$WAITING_FOR_SEND = "waiting_for_send";
	$REJECT_BY_ADMIN = "reject_by_admin";

	// $DONE = "done";
	// $WAITING = "waiting";
	$FAIL = "fail";

	$SMS_STATUS = array(
		$NEW => array(
			"description" => "Новое сообщение, еще не было отправлено",
			"is_finish_step" => false
		),
		$ENQUEUED => array(
			"description" => "Прошло модерацию и поставлено в очередь на отправку",
			"is_finish_step" => false
		),
		$ACCEPTED => array(
			"description" => "Отправлено из системы и принято оператором для дальнейшей пересылки получателю",
			"is_finish_step" => false
		),
		$UNDELIVERED => array(
			"description" => "Не доставлено получателю",
			"is_finish_step" => true,
		),
		$REJECTED => array(
			"description" => "Отклонено оператором по одной из множества причин - неверный номер получателя, запрещенный текст и т.д.",
			"is_finish_step" => true
		),
		$DELIVERED => array(
			"description" => "Доставлено получателю полностью",
			"is_finish_step" => true
		),
		$POSTDELIVERED => array(
			"description" => "Не все сегменты сообщения доставлены получателю, некоторые операторы возвращают отчет только о первом доставленном сегменте, поэтому такое сообщение после истечения срока жизни перейдет в статус DELIVRD",
			"is_finish_step" => false
		),
		$DELETED => array(
			"description" => "Удалено из-за ограничений и не доставлено до получателя",
			"is_finish_step" => true
		),
		$DECLINED => array(
			"description" => "СМС отклонено модератором",
			"is_finish_step" => true
		),
		$EXPIRED => array(
			"description" => "Доставка не удалась так как истек срок жизни сообщения (по умолчанию 1 суток)",
			"is_finish_step" => true
		),
		$MODERATION => array(
			"description" => "На модераций",
			"is_finish_step" => false
		),

		$REJECT_BY_ADMIN => array(
			"description" => "Отклонено администратором портала",
			"is_finish_step" => true
		)
	);

?>