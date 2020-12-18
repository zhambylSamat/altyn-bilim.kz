<?php
	$result_student = array();
	include('../connection.php');

	$total_comment_number = 0;
	$current_day = intval(date('d'));
	$start_day = 25;
	$end_day = 10;
	$start_date = "";
	$end_date = "";
	$is_active_period = false;

	if ($current_day >= $start_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y')));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
		$is_active_period = true;
	} else if ($current_day <= $end_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
		$is_active_period = true;
	}

	$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));
	$poll_activate_days_str =  date('d.m.Y', strtotime("-20 days"));


	if(!isset($_GET['search']) || $_GET['search']==''){ 
		try {

			$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi2.group_info_num
                                    	AND tr2.student_num = gs2.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";

			$stmt = $conn->prepare("SELECT s.student_num,
										s.name,
									    s.surname,
									    s.username,
									    s.password_type,
									    s.phone,
									    s.school,
									    s.altyn_belgi,
									    s.red,
									    (SELECT n.readed FROM news n WHERE n.type = s.student_num ) AS readed,
                                        (SELECT count(r2.group_student_num) 
                                    	FROM review r2, 
                                    		group_student gs3,
                                    		group_info gr_info
                                    	WHERE r2.review_info_num != (SELECT review_info_num 
                                    								FROM review_info 
                                    								WHERE description = 'comment') 
                                    		AND r2.group_student_num = gs3.group_student_num 
                                    		AND gs3.group_info_num = gr_info.group_info_num
                                    		AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
                                    		AND gs3.start_date <= CURDATE()
                                    		AND s.student_num = gs3.student_num 
                                    		AND gs3.block != 6) AS c1,
										(SELECT count(gs2.group_student_num) 
										FROM group_student gs2,
											group_info gi2
										WHERE gs2.student_num = s.student_num
											AND gs2.group_info_num = gi2.group_info_num
											AND gs2.start_date <= CURDATE()
											AND gi2.subject_num != 'S5985a7ea3d0ae721486338'
											AND gs2.block != 6
											AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs2.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')) AS c2,
										(SELECT count(sp.id)
										FROM student_poll sp
										WHERE sp.student_num = s.student_num
											AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
											AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')) AS is_polled,
										(SELECT count(DISTINCT gi2.teacher_num)
										FROM group_student gs2,
											group_info gi2
										WHERE gs2.student_num = s.student_num
											AND gs2.block != 6
											AND gi2.subject_num != 'S5985a7ea3d0ae721486338'
											AND gi2.group_info_num = gs2.group_info_num
											AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs2.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')) AS active_teacher_polls
									FROM student s
										WHERE s.block = 0
									GROUP BY s.student_num 
									ORDER BY s.surname, s.name ASC");
			$stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
			$stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
			$stmt->bindParam(":poll_activate_days", $poll_activate_days, PDO::PARAM_STR);
		    $stmt->execute();
		    $result_student = $stmt->fetchAll(); 

		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}
		$_SESSION['result_student'] = $result_student;
		$_SESSION['total_comment_number'] = $total_comment_number;
	}
	else{
		$q = $_GET['search'];
		$total_comment_number = $_SESSION['total_comment_number'];
		foreach ($_SESSION['result_student'] as $val) {
			if (strpos(mb_strtolower($val['name']), mb_strtolower($q)) !== false 
				|| strpos(mb_strtolower($val['surname']), mb_strtolower($q)) !== false 
				|| strpos(mb_strtolower($val['username']), mb_strtolower($q)) !== false 
				|| strpos((mb_strtolower($val['surname'])."_".mb_strtolower($val['name'])), mb_strtolower($q)) !== false 
				|| strpos((mb_strtolower($val['name'])."_".mb_strtolower($val['surname'])), mb_strtolower($q)) !== false) {
				array_push($result_student, $val);
			}
		}
	}

	try {
		
		$stmt = $conn->prepare("SELECT count(description) c FROM review_info where description = 'review' group by description");
	    $stmt->execute();
	    $total_comment_number = $stmt->fetch(PDO::FETCH_ASSOC);

	    $stmt = $conn->prepare("SELECT count(id) AS poll_info_count FROM teacher_poll_info");
	    $stmt->execute();
	    $total_poll_number = $stmt->fetch(PDO::FETCH_ASSOC)['poll_info_count'];
	    
	} catch (Exception $e) {
		throw $e;
	}

	$student_list = "<table id='student-list'>";
    foreach ($_SESSION['result_student'] as $value) {
		$student_list .= "<tr><td>".$value['surname']." ".$value['name']."</td><td>".$value['school']."</td><td>".$value['phone']."</td></tr>";
	}
	$student_list .= "</table>";
?>
<div id='copy-students-to-clipboard'>
	<button class='btn btn-info btn-xs pull-right copy-students'>Скопировать список студентов</button>
	<div style='font-size:0;'><?php echo $student_list; ?></div>
</div>
<br>
<div id='no_comment' style='padding:0.1% 0.1%; color:#555; margin:0.5% 0; font-size: 11px; display: none;'>
	<b class='glyphicon glyphicon-exclamation-sign notification-sign' title='Оқушыға комментарий енгізбеген' data-toggle='tooltip' data-placement='left' style='color: #FFB564; font-size: 15px;'></b><b> *Оқушылардың "комменттері" енгізілмеген</b> <i>(мұғалімдегре ескерту керек)</i>
	<br>
	<i><?php echo $poll_activate_days_str; ?> - күнінен кейін тіркелген оқушыларға комментарий толтырмайды!</i>
</div>
<div id='not_polled' style='padding:0.1% 0.1%; color:#555; margin:0.5% 0; font-size: 11px; display: none;'>
	<b class='glyphicon glyphicon-remove-circle notification-sign' title='Оқушылар мұғалімдерге опрос толтырмаған' data-toggle='tooltip' data-placement='left' style='color: #E52C38; font-size: 15px;'></b><b> *Оқушылар мұғалімдерге опрос толтырмаған</b> <i>(мұғалімдегре ескерту керек)</i>
	<br>
	<i><?php echo $poll_activate_days_str; ?> - күнінен кейін тіркелген оқушылар сауалнама толтырмайды!</i>
</div>
<div id='insufficient_information' style='padding:0.1% 0.1%; color:red; margin:0.5% 0; font-size: 11px; display: none;'>
	<b>*Оқушылардың ақпараттары толық емес!</b>
</div>
<table class="table table-striped table-bordered">
	<?php
		$student_number = 1;
		$no_comment_count = 0;
		$not_polled_count = 0;
		$insufficient_information_count = 0;
		foreach ($result_student as $readrow) {
			$bg_color = '';
			$no_comment = false;
			$not_polled = false;
			$color = '';
			if($readrow['c1']<$readrow['c2']*intval($total_comment_number['c']) && $readrow['student_num'] != 'US5985cba14b8d3100168809'){
				$no_comment = true;
				$no_comment_count++;
			}
			if ($total_poll_number > 0 &&
				$is_active_period &&
				$readrow['active_teacher_polls'] != 0 &&
				$readrow['is_polled'] < $readrow['active_teacher_polls'] && 
				$readrow['student_num'] != 'US5985cba14b8d3100168809') {

				$not_polled = true;
				$not_polled_count++;
			}
			if($readrow['phone']=='' || $readrow['school']==''){
				$color = 'red';
				$insufficient_information_count++;
			}
	?>
	<tr class='head'>
		<td style='width: 1%;'>
			<center>
				<h4 class='count'>
					<?php
						echo $student_number;
					?>	
				</h4>
			</center>
		</td>
		<td>
			<?php
				$html = "<center>";
				if ($no_comment) {
					$html .= "<p class='glyphicon glyphicon-exclamation-sign notification-sign' title='Оқушыға комментарий енгізбеген' data-toggle='tooltip' data-placement='left' style='color: #FFB564; font-size: 20px;'></p>&nbsp;&nbsp;";
				}
				if ($not_polled) {
					$html .= "<p class='glyphicon glyphicon-remove-circle notification-sign' title='Оқушы опрос толтырмаған' data-toggle='tooltip' data-placement='left' style='color: #E52C38; font-size: 20px;'></p>&nbsp;&nbsp;";
				}
				$html .= "</center>";
				echo $html;
			?>
		</td>
		<td style=''>
			<div class='user_info'>
				<table class='' style='width:100%; background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr style='width: 100%;'>
						<td style='width: 40%;'>
							<h4 class='text-success' style='display: inline-block;'>
								<?php if($readrow['altyn_belgi']==1){ ?><span class="glyphicon glyphicon-star" style='color:gold;'></span><?php } ?>
								<?php if($readrow['red']==1){ ?><span class="glyphicon glyphicon-star" style='color: #ED3E2F;'></span><?php } ?>
								<a class='object-full-name' href="student_info_marks.php?data_num=<?php echo $readrow['student_num']; ?>" target="_blank" style='color:<?php echo $color; ?>'>
									<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>
								</a> 
							</h4> 
							<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num="<?php echo $readrow['student_num'];?>">[инфо]</a>
							<a class='glyphicon glyphicon-earphone btn btn-info btn-xs hidden-lg hidden-md hidden-sm' aria-hidden='true' title='Позвонить' href='tel:+7<?php echo $readrow['phone']; ?>' style='font-size: 20px; cursor: pointer; text-decoration: none; display:block; color:#fefefe;'></a>
						</td>
						<td style='width: 30%;'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
						<td style='width: 30%;'>
							<div class='password'>
								<h5'>Пароль: 
								<?php if($readrow['password_type']!='default'){?>
								<button class='btn btn-info btn-xs reset_password' data-name='student' style='display: inline-block;'>Сбросить пароль</button>
								<input type="hidden" name="reset" value='<?php echo $readrow['student_num']?>'>
								<?php }else{?>
								<b><u><i>'12345'</i></u></b>
								<?php }?>
								</h5'>
							</div>
						</td>
					</tr>
				</table>				
			</div>
		</td>
		<td>
			<a class='btn btn-default btn-xs more_info' data-name='student' data_toggle='false' data_num = "<?php echo $readrow['student_num']; ?>" title='Толығырақ'>
				<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>	
			<a class='btn btn-xs btn-danger to_archive' data-name='student' data-num = "<?php echo $readrow['student_num']; ?>" title='Архивировать'>
				<span class='glyphicon glyphicon-save-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
			<!-- <form style='display: inline-block;' onsubmit="return confirm('Вы точно хотите удалить студента? Все данные об студенте будут удалены.')" action='' method=''>
				<center>
					<input type="hidden" name="remove-student-num" value="<?php echo $readrow['student_num']?>">
					<button class='btn btn-default btn-xs' type='submit' value='student_num' name='remove_student' title='Жою'>
						<span class='glyphicon glyphicon-remove text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form> -->
			<!-- <form style='display: inline-block;' onsubmit="return confirm('Заблокировать студента?')" method='post' action='admin_controller.php'>
				<center>
					<input type="hidden" name="data_num" value='<?php echo $readrow['student_num']?>'>
					<button type='submit' name='block_student' class="btn btn-default btn-xs" title='Блокировать'>
						<span class='glyphicon glyphicon-ban-circle text-default' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form> -->
			<a class="btn btn-default btn-xs single-student-news" data-num='<?php echo $readrow['student_num']?>' data-name='<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>' data-toggle='modal' data-target='.box-news'>
				<span class='glyphicon glyphicon-envelope' aria-hidden='true' style='font-size: 20px; cursor: pointer; <?php echo ($readrow['readed']=='') ? "color:black;" : (($readrow['readed']==0) ? "color:orange;" : "color:#00F300"); ?>'></span>
			</a>
			<form style='display: inline-block;' onsubmit="return confirm('Подтвердите действие!')" method='post' action='admin_controller.php'>
				<center>
					<input type="hidden" name="data_num" value='<?php echo $readrow['student_num']?>'>
					<button type='submit' name='student_no_payment' class="btn btn-default btn-xs" title='Оплатасы жоқ'>
						<span class='glyphicon glyphicon-usd text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form>
			<form style='display: inline-block;' onsubmit="return confirm('Подтвердите действие!')" method='post' action='admin_controller.php'>
				<center>
					<input type="hidden" name="data_num" value='<?php echo $readrow['student_num']?>'>
					<button type='submit' name='student_no_contract' class="btn btn-default btn-xs" title='Договор өткізбегендер'>
						<span class='glyphicon glyphicon-file text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form>
		</td>
	</tr>
	<tr class='body'>
		
	</tr>
	<?php $student_number++; }?>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		if(<?php echo $no_comment_count;?>>0){
			$("#no_comment").show();
		}
		if(<?php echo $not_polled_count; ?> > 0) {
			$("#not_polled").show();
		}
		if(<?php echo $insufficient_information_count;?>>0){
			$("#insufficient_information").show();
		}
	});
</script>

<hr>
<center><h3 class='text-warning'>Оплатасы жоқтар!</h3></center>
<table class="table table-striped table-bordered">
	<?php
		$result_no_payment_student = array();
		if(!isset($_GET['search']) || $_GET['search']==''){ 
			try {
				
				$stmt = $conn->prepare("SELECT * FROM student WHERE block = 2 OR block = 3 order by surname asc");
				$stmt->execute();
				$result_no_payment_student = $stmt->fetchAll(); 
			} catch (PDOException $e) {
				echo "Error ".$e->getMessage()." !!!";
			}
			$_SESSION['result_no_payment_student'] = $result_no_payment_student;
		}
		else{
			$q = $_GET['search'];
			foreach ($_SESSION['result_no_payment_student'] as $val) {
				if (strpos(mb_strtolower($val['name']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['surname']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['username']), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['surname'])."_".mb_strtolower($val['name'])), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['name'])."_".mb_strtolower($val['surname'])), mb_strtolower($q)) !== false) {
					array_push($result_no_payment_student, $val);
				}
			}
		}
		$student_no_payment_number = 1;
		foreach ($result_no_payment_student as $readrow) {
	?>
	<tr class='head' style='<?php if($readrow['block']==3){ echo "border: 2px solid red;"; } ?>'>
		<td style='width: 5%;'><center><h4 class='count'><?php echo $student_no_payment_number;?></h4></center></td>
		<td style='width: 75%;'>
			<div>
				<table class='table' style='background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr style='width: 100%;'>
						<td style='width: 50%;'>
							<h4 class='text-success' style='display: inline-block;'>
								<a class='object-full-name' href="student_info_marks.php?data_num=<?php echo $readrow['student_num']; ?>" target="_blank">
									<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>
								</a>
							</h4>
							<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num="<?php echo $readrow['student_num'];?>">[инфо]</a>
							<a class='glyphicon glyphicon-earphone btn btn-info btn-sm hidden-lg hidden-md hidden-sm' aria-hidden='true' title='Позвонить' href='tel:+7<?php echo $readrow['phone']; ?>' style='font-size: 17px; cursor: pointer; text-decoration: none; display:block; color:#fefefe;'></a>
						</td>
						<td style='width: 50%;'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
						<td class='warned'>
							<?php if($readrow['block']==3){?><b style='color:#f00;'>Ескертілген</b><?php } ?>
						</td>
					</tr>
				</table>				
			</div>
		</td>
		<td style='width:20%'>
			<center>
				<a class='btn btn-default btn-sm more_info' data-name='student' data_toggle='false' data_num = "<?php echo $readrow['student_num']?>" title='Толығырақ'>
					<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
				</a>
				<form style='display: inline-block;' onsubmit='return confirm("Восстановить студента?");' method='post' action='admin_controller.php'>
					<center>
						<input type="hidden" name="data_num" value='<?php echo $readrow['student_num'];?>'>
						<button type='submit' class='btn btn-default btn-sm' name='unblock_student'>
							<span class='glyphicon glyphicon-ok-circle text-success' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
						</button>
					</center>
				</form>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<!-- <form style='display: inline-block;' onsubmit='return confirm("Вы точно хотите удалить студента? Все данные об студенте будут удалены.");' method='post' action='admin_controller.php'>
					<center>
						<input type="hidden" name="remove-student-num" value="<?php echo $readrow['student_num']?>">
						<button class='btn btn-danger btn-xs' type='submit' value='student_num' name='remove_student' title='Жою' style='height:25px;'>
							<b style='color:white; vertical-align: middle;'>Удалить</b>
						</button>
					</center>
				</form> -->
				<a class='btn btn-xs btn-danger to_archive' data-name='student' data-num = "<?php echo $readrow['student_num']; ?>" title='Архивировать'>
					<span class='glyphicon glyphicon-save-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
				</a>
				<br>
				<br>
				<?php if($readrow['block']!=3){ ?>
				<a class='btn btn-warning btn-sm open-access' data-num='<?php echo $readrow['student_num']?>' data-block='3'>Открыть портал</a>
				<?php }?>
			</center>
		</td>
	</tr>
	<tr class='body'>
		
	</tr>
	<?php
			$student_no_payment_number++; 
		} 
		if($student_no_payment_number == 1){
			echo "<center><h1 class='text-primary'>N/A</h1></center>";
		}
	?>
</table>
<hr>
<center><h3 class='text-warning'>Договор өткізбегендер!</h3></center>
<table class="table table-striped table-bordered">
	<?php
		$result_no_contract_student = array();
		if(!isset($_GET['search']) || $_GET['search']==''){ 
			try {
				
				$stmt = $conn->prepare("SELECT * FROM student WHERE block = 4 OR block = 5 order by surname asc");
				$stmt->execute();
				$result_no_contract_student = $stmt->fetchAll(); 
			} catch (PDOException $e) {
				echo "Error ".$e->getMessage()." !!!";
			}
			$_SESSION['result_no_contract_student'] = $result_no_contract_student;
		}
		else{
			$q = $_GET['search'];
			foreach ($_SESSION['result_no_contract_student'] as $val) {
				if (strpos(mb_strtolower($val['name']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['surname']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['username']), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['surname'])."_".mb_strtolower($val['name'])), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['name'])."_".mb_strtolower($val['surname'])), mb_strtolower($q)) !== false) {
					array_push($result_no_contract_student, $val);
				}
			}
		}
		$student_no_contract_number = 1;
		foreach ($result_no_contract_student as $readrow) {
	?>
	<tr class='head' style='<?php if($readrow['block']==5){ echo "border: 2px solid red;"; } ?>'>
		<td style='width: 5%;'><center><h4 class='count'><?php echo $student_no_contract_number;?></h4></center></td>
		<td style='width: 75%;'>
			<div>
				<table class='table' style='background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr style='width: 100%;'>
						<td style='width: 50%;'>
							<h4 class='text-success' style='display:inline-block;'>
								<a href="student_info_marks.php?data_num=<?php echo $readrow['student_num']; ?>" class='object-full-name' target="_blank">
									<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>		
								</a>
							</h4>
							<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num="<?php echo $readrow['student_num'];?>">[инфо]</a>
							<a class='glyphicon glyphicon-earphone btn btn-info btn-sm hidden-lg hidden-md hidden-sm' aria-hidden='true' title='Позвонить' href='tel:+7<?php echo $readrow['phone']; ?>' style='font-size: 17px; cursor: pointer; text-decoration: none; display:block; color:#fefefe;'></a>
						</td>
						<td style='width: 50%;'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
						<td class='warned'>
							<?php if($readrow['block']==5){?><b style='color:#f00;'>Ескертілген</b><?php } ?>
						</td>
					</tr>
				</table>				
			</div>
		</td>
		<td style='width:20%'>
			<center>
				<a class='btn btn-default btn-sm more_info' data-name='student' data_toggle='false' data_num = "<?php echo $readrow['student_num']?>" title='Толығырақ'>
					<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
				</a>
				<form style='display: inline-block;' onsubmit='return confirm("Восстановить студента?");' method='post' action='admin_controller.php'>
					<center>
						<input type="hidden" name="data_num" value='<?php echo $readrow['student_num'];?>'>
						<button type='submit' class='btn btn-default btn-sm' name='unblock_student'>
							<span class='glyphicon glyphicon-ok-circle text-success' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
						</button>
					</center>
				</form>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a class='btn btn-xs btn-danger to_archive' data-name='student' data-num = "<?php echo $readrow['student_num']; ?>" title='Архивировать'>
					<span class='glyphicon glyphicon-save-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
				</a>
				<br>
				<br>
				<?php if($readrow['block']!=5){ ?>
				<a class='btn btn-warning btn-sm open-access' data-num='<?php echo $readrow['student_num']?>' data-block='5'>Открыть портал</a>
				<?php }?>
			</center>
		</td>
	</tr>
	<tr class='body'>
		
	</tr>
	<?php
			$student_no_contract_number++; 
		} 
		if($student_no_contract_number == 1){
			echo "<center><h1 class='text-primary'>N/A</h1></center>";
		}
	?>
</table>