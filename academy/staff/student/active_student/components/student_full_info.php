<div class='full-info hide' style='border-top: 1px solid lightgray;'>
	<div class='extra-info'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div class='student-info'>
				<p><?php echo $value['school']; ?> мектеп. <?php echo $value['class']; ?> сынып.</p>
				<p>Тіркелген күні: <?php echo $value['created_date']; ?></p>
				<p>+7 <?php echo $value['phone']; ?></p>
			</div>
			<div class='group-info'>
				<b>Оқушының қатысатын группалары: </b>
				<ol style='padding: 0;'>
					<?php foreach ($value['groups'] as $group_id =>  $val) { ?>
					<li style='border: 1px solid lightgray; padding: 5px 0px 5px 10px; list-style-position: inside;'>
						<span data-id='<?php echo $group_id; ?>'>
							<a href="?page=group&group=<?php echo $group_id; ?>" target="_blank"><?php echo $val['group_name']; ?></a>
							| <?php echo $val['subtopic_title']; ?>
						</span>
						<?php
							$html = '';
							if ($val['status'] == 'inactive') {
								$html .= "<br>";
								$html .= "<button class='btn btn-xs btn-default start-lesson' data-type='group' data-id='".$val['group_student_id']."' style='margin-left: 10px;' data-student='".$student_id."' data-group='".$group_id."'>Төлемі жоқ</button>";
								$html .= "<div class='tmp' style='margin-left: 10px; display: inline;'>";
									$html .= "<span>".$val['payment']['sum']."тг. </span>";
									$html .= "&nbsp;&nbsp;&nbsp;<span title='Оплатасы осы күннен бастап жүреді'>".date('d.m.Y', strtotime($val['payment']['start_date']))."-дан бастап төлемі есептеледі</span>";
								
									$html .= "<div class='partial_payment'>";
										$html .= "<button class='btn btn-xs btn-default partial_payment_form_btn'>Күндер</button>";
										$html .= "<form class='form-inline partial_payment_form' style='display:none;'>";
											$html .= "<div class='form-group'>";
												$min_days = 1;
												$default_days = $val['payment']['days'] == '' ? 1 : $val['payment']['days'];
												if ($val['payment']['start_date'] != '') {
													$date1 = date_create($val['payment']['start_date']);
													$date2 = date_create(date('Y-m-d'));
													$min_days = date_diff($date1, $date2)->format('%a') + 1;
												}
												if ($min_days > $default_days) {
													$default_days = $min_days;
												}
												$html .= "<input type='number' class='partial_payment_days form-control' name='partial_days' min='".$min_days."' step='1' value='".$default_days."'>";
											$html .= "</div>";
											$html .= "<input type='hidden' name='group_student_id' value='".$val['group_student_id']."'>";
											$html .= "<input type='hidden' name='student_id' value='".$student_id."'>";
											$html .= "<input type='hidden' name='group_id' value='".$group_id."'>";
											$html .= "<span class='partial_payment_price'> ".($default_days*260)." тг. </span>";
											$html .= "<button type='submit' class='btn btn-success btn-xs'>Төлемін сақтау</button>";
											$html .= "<button type='button' class='btn btn-warning btn-xs partial_payment_form_cancel'>Отмена</button>";
										$html .= "</form>";
									$html .= "</div>";
								$html .= "</div>";
								echo $html;
							} else {
								echo "<div class='tmp' style='margin-left: 10px; display: inline-block;'>";
								$group_student_id = $val['group_student_id'];
								include($root.'/staff/student/active_student/components/extra_payments.php');
								echo "</div>";
							}
							// echo $html;
						?>
					</li>
					<?php } foreach ($value['courses'] as $course_id => $val) { ?>
						<li>
							<span><?php echo $val['group_name'].' : '.$val['start_date'].' ('.$val['subtopic_title'].')'; ?></span>
							<button class='btn btn-xs btn-default start-lesson' data-type='course' data-id='<?php echo $course_id; ?>' style='margin-left: 10px;'>Төлемі жоқ</button>
							<span class='tmp'style='margin-left: 10px;'></span>
						</li>
					<?php } ?>
				</ol>
				<?php 
					if (count($value['reserves']) > 0) {
						echo '<i>Резерв</i>';
						foreach ($value['reserves'] as $reserve_id => $val) {
							echo "<div style='border: 1px solid lightgray; padding: 5px 0px 5px 10px;'>";
								echo "<p>".$val['subject_title']." : ".$val['topic_title']."</p>";
								if ($val['is_free_trial_lesson']) {
									echo "<span class='tmp' style='margin-left: 10px;'><b style='color: #5cb85c;'>Алғашқы 7 күн тегін!</b></span>";
								}
								else if ($val['student_reserve_payment_id'] == '' || $val['student_reserve_payment_id'] == null) {
									echo "<button class='btn btn-xs btn-default start-lesson' data-type='reserve' data-id='".$reserve_id."'>Төлемі жоқ</button>";
									echo "<span class='tmp'></span>";
								} else {
									echo "<span class='tmp' style='margin-left: 10px;'><span style='color: #5cb85c;'>Төлемі өткізілген уақыт: ".$val['payed_date']."</span></span>";
								}
							echo "</div>";
						}
					}
				?>
			</div>
		</div>
		<?php if ($value['total_balance_days'] > 0) { ?>
		<div class='col-md-6 col-sm-6 col-xs-12' style='border-left: 1px solid lightgray;'>
			<div>
				<p id='title'>Өткен группалардан артылып қалған күндер:</p>
				<ul style='color: darkgreen;'>
					<?php foreach ($value['balances'] as $val) { ?>
					<li>
						<?php echo $val['group_name'].': <i>'.$val['days'].' күн'.($val['comment'] != '' ? " (".$val['comment'].")" : '').'</i>'; ?>
					</li>
					<?php } ?>
					<li>Барлығы: <?php echo $value['total_balance_days'].' күн'; ?></li>
				</ul>
				<b id='payment-message' style='color: #5CB85C;'>Келесі төлемде автоматты түрде ескеріледі!</b>
			</div>
		</div>
		<?php } ?>
	</div>
</div>