<?php
	$LEVEL = 2;
	include($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');
    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);

    include_once($root.'/common/check_authentication.php');
    check_admin_access();
    $count = 0;
    
	include_once($root.'/staff/student/view.php');
	// if (isset($_SESSION['student_list'])) {
		// $active_students = $_SESSION['student_list'];
	// } else {
		// $active_students = get_active_students();
		$active_students = get_archive_students();
		// echo 'okokkkkkkk22222222222222';
	// }
?>
<div class='row'>
	<div class='col-md-4 col-md-offset-8 col-sm-6 col-sm-offset-6 col-xs-12'>
		<input type="text" style='margin-bottom: 10px;' name="search_student" class='form-control' value='' placeholder="Оқушыны іздеу">
	</div>
</div>
<table class='table table-bordered table-hover student-table'>
	<?php foreach ($active_students as $student_id => $value) { ?>
	<tr>
		<td style='width: 10px;' class='count'><center><?php echo ++$count; ?></center></td>
		<td>
			<div class='row' style='padding: 0px 20px;'>
				<div class='short_info std-info'>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<a href='<?php echo $ab_root."/academy/staff/student/student_cabinet?student_id=".$student_id; ?>' target='_blank' class='cursor-pointer student-short-info'><?php echo $value['full_name']." <span class='phone-number'>".$value['phone']; ?></span></a>
						<button class='btn btn-xs btn-info copy-phone-number-btn'><i class="fas fa-copy"></i></button>
						<?php if ($value['total_balance_days'] > 0) { ?>
						&nbsp;&nbsp;
						<span>Счетында <b><?php echo $value['total_balance_days']; ?></b> күн бар</span>
						<?php } ?>
					</div>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<div class='pull-right password'>
							<?php
								if ($value['password_reset'] == 0) {
									echo "<div class='pull-right'><button class='btn btn-xs btn-info reset-password-btn' data-id='".$student_id."'>Сбросить пароль</button></div>";
								} else {
									echo "<div class='password pull-right'><i class='text-warning'>Пароль: <b>12345</b></i></div>";
								}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class='row' style='padding: 0px 20px;'>
				<div class='full-info hide' style='border-top: 1px solid lightgray;'>
					<div class='extra-info'>
						<div class='col-md-6 col-sm-6 col-xs-12'>
							<div class='student-info'>
								<p><?php echo $value['school']; ?> мектеп. <?php echo $value['class']; ?> сынып.</p>
								<p>Тіркелген күні: <?php echo $value['created_date']; ?></p>
								<p>+7 <?php echo $value['phone']; ?></p>
								<p>Ата-анасы: +7 <?php echo $value['parent_phone']; ?></p>
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
			</div>
		</td>
		<td>
			<div>
				<button class='btn btn-xs btn-default show-student-info' title='Оқушының анкетасы'>
					<span class='glyphicon glyphicon-align-justify'></span>
				</button>
			</div>
		</td>
	</tr>
	<?php
		}
	?>
</table>