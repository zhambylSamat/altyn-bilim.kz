<?php
	
include_once('../connection.php');

$is_phone = isset($is_phone) ? $is_phone : true;
$is_comment = isset($is_comment) ? $is_comment : true;
if (isset($_GET['group_info_num'])){
	$group_info_num = $_GET['group_info_num'];
}

$students_group_count = 0;

try {
	$stmt = $conn->prepare("SELECT s.subject_num,
								s.subject_name,
								gi.group_name,
								gi.comment
							FROM subject s,
								group_info gi
							WHERE gi.group_info_num = :group_info_num
								AND s.subject_num = gi.subject_num");
	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
    $stmt->execute();
    $result_group_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $subject_num = $result_group_info['subject_num'];


    $current_day = intval(date('d'));
	$start_day = 25;
	$end_day = 10;
	$start_date = "";
	$end_date = "";
	$is_active_period = false;

	if ($current_day >= $start_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('d-m-Y')));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
		$is_active_period = true;
	} else if ($current_day <= $end_day) {
		$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
		$end_date = date('d-m-Y', strtotime('10-'.date('m-Y')));
		$is_active_period = true;
	}
	$poll_activate_days =  date('d-m-Y', strtotime("-20 days"));

	
	$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi2.group_info_num
                                    	AND tr2.student_num = gs2.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
    $stmt = $conn->prepare("SELECT gs.group_student_num, 
    							s.student_num, 
    							s.name, 
    							s.surname, 
    							s.altyn_belgi,
    							s.red,
    							s.password_type, 
    							s.username,
    							s.block,
    							s.phone,
    							DATE_FORMAT(gs.start_date, '%d.%m.%Y') AS start_date,
    							(SELECT count(r.group_student_num) 
    							FROM review r 
    							WHERE r.group_student_num = gs.group_student_num) - (SELECT count(review_info_num) 
    																				FROM review_info ri 
    																				WHERE ri.description != 'comment') AS c,
    							(SELECT count(r.group_student_num) 
								    FROM review r
								    WHERE r.group_student_num = gs.group_student_num 
								    	AND r.review_info_num != (SELECT review_info_num 
								    								FROM review_info 
								    								WHERE description = 'comment') 
								    GROUP BY r.group_student_num) group_count,
    							(SELECT DATE_FORMAT(tr2.created_date, '%d.%m.%Y')
	                                    FROM transfer tr2
	                                    WHERE tr2.new_group_info_num = gi.group_info_num
	                                    	AND tr2.student_num = gs.student_num
	                                    ORDER BY tr2.created_date DESC
	                                    LIMIT 1) AS transfer_date,
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
    						FROM student s, 
    							group_student gs,
    							group_info gi
    						WHERE gs.student_num = s.student_num
    							AND gs.start_date <= CURDATE() 
    							AND gs.group_info_num = :group_info_num 
    							AND s.block != 1 
    							AND s.block != 6
    							AND gs.block != 6
    							AND gi.group_info_num = gs.group_info_num
    						ORDER BY surname, name ASC");
    $stmt->bindParam(":group_info_num", $group_info_num, PDO::PARAM_STR);
    $stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
	$stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
	$stmt->bindParam(":poll_activate_days", $poll_activate_days, PDO::PARAM_STR);
    $stmt->execute();
    $result_students_group = $stmt->fetchAll();
    $students_group_count = $stmt->rowCount();

    $stmt = $conn->prepare("SELECT count(description) c 
    						FROM review_info 
    						WHERE description = 'review' 
    						GROUP BY description");
    $stmt->execute();
    $total_comment_number = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT count(id) AS poll_info_count FROM teacher_poll_info");
	$stmt->execute();
    $total_poll_number = $stmt->fetch(PDO::FETCH_ASSOC)['poll_info_count'];


    $stmt = $conn->prepare("SELECT
								(CASE WHEN (WEEKDAY(CURDATE())+1 
											IN (SELECT sch.week_id 
                                    			FROM schedule sch 
                                    			WHERE sch.group_info_num = gi.group_info_num))
                                    AND (SELECT CURRENT_TIME)
                                    		BETWEEN 
                                				(SELECT SUBTIME(gi.start_lesson, '00:30:00')) 
                                				AND
                                				(SELECT ADDTIME(gi.finish_lesson, '00:30:00'))
							    THEN
							    	'true'
							    ELSE
							     	'false'
							    END) AS lesson
							FROM group_info gi, 
								teacher t
							WHERE gi.group_info_num = :group_info_num");
	$stmt->bindParam(':group_info_num', $group_info_num, PDO::PARAM_STR);
    $stmt->execute();
    $is_group_in_time = $stmt->fetch(PDO::FETCH_ASSOC);
    $lesson = $is_group_in_time['lesson'];
} catch (PDOException $e) {
	throw $e;
}
?>

<table class='table table-striped table-bordered'>
	<tr>
		<th style='width: 25%;'>
			Группа: <?php echo $result_group_info['group_name'];?>
		</th>
		<th style='width: 25%;'>
			Мұғалім: <?php echo $_SESSION['teacher_name']." ".$_SESSION['teacher_surname'];?>
		</th>
		<th style='width: 25%;'>
			Пән: <?php echo $result_group_info['subject_name'];?>
		</th>
		<th style='width: 25%;'>
			Түсініктеме: <br><?php echo $result_group_info['comment'];?>
		</th>
	</tr>
	<tr>
		<td colspan='4'>
			<b>Студенттер:</b>
			<table class='table'>
			<?php
				if($students_group_count==0){
					echo "N/A";
				}
				else{
					$count = 1;
					foreach ($result_students_group as $value) {
						$no_comment = false;

						if ($value['group_count']=='' || $value['group_count']%intval($total_comment_number['c'])!=0) {
							if ($value['transfer_date'] != '') {
								if (strtotime($poll_activate_days) >= strtotime($value['transfer_date'])) {
									$no_comment = true;
								}
							} else {
								if (strtotime($poll_activate_days) >= strtotime($value['start_date'])) {
									$no_comment = true;
								}
							}
						}
			?>
				<tr id='<?php echo $count;?>_tr' class='head-student <?php echo ($no_comment && $value['c']!=0 && $subject_num!='S5985a7ea3d0ae721486338' && $value['student_num'] != 'US5985cba14b8d3100168809') ? "warning" : "";?>' style='border:1px solid lightgray; border-bottom:none; cursor: pointer;'>
					<!-- <div class='row'> -->
						<td>
							<?php 
								if ($value['block']==2 || $value['block']==3){
									echo "<p class='helper'><b style='color:red;'>Оплатасы жоқ</b></p>";
								} else if ($value['block']==4 || $value['block']==5) {
									echo "<p class='helper'><b style='color:red;'>Договор өткізбеген</b></p>";
								} else {
									echo "";
								}

								if (isset($_SESSION['notification']) 
									&& array_key_exists($value['student_num'], $_SESSION['notification'])
									&& isset($_SESSION['notification'][$value['student_num']]['count'])
									&& $_SESSION['notification'][$value['student_num']]['count'] > 0) {
									echo "<p class='helper' style='color:green;'><b>Шоколад (".$_SESSION['notification'][$value['student_num']]['count']." шт.)</b></p>";
								}

							?>
							<?php 
								echo "<div style='padding-bottom: 10px;'>";
								if ($no_comment && $value['c']!=0 && $subject_num!='S5985a7ea3d0ae721486338' && $value['student_num'] != 'US5985cba14b8d3100168809') {
									echo "<span class='helper' style='border: 1px solid #ED6C6C; padding: 2px 5px; border-radius: 10px;'><b class='text-danger'>Коммент жоқ</b></span>&nbsp;&nbsp;";
								}
								if ($total_poll_number != 0 && $is_active_period && $value['active_teacher_polls'] != 0 && $value['is_polled'] < $value['active_teacher_polls']) {
									echo "<span class='helper' style='border: 1px solid #ED6C6C; padding: 2px 5px; border-radius: 10px;'><b class='text-danger'>Опрос толтырмаған</b></span>&nbsp;&nbsp;";
								}
								echo "</div>";
							?>
							<span><?php echo ($count++).") ";?></span>
							<?php if($value['altyn_belgi']==1){ ?><span class="glyphicon glyphicon-star" style='color:gold;'></span><?php } ?>
							<?php if($value['red']==1){ ?><span class="glyphicon glyphicon-star" style='color: #ED3E2F;'></span><?php } ?>
							<?php if($lesson=='true') {?>
								<a class='header-student' data-load='n' data-name='student_single' data-num='<?php echo $value['student_num'];?>' data-subject='<?php echo $subject_num; ?>'>
									<?php echo $value['surname']." ".$value['name']; ?></a>
							<?php }else if($lesson=='false'){ ?>
								<span><?php echo $value['surname']." ".$value['name']; ?></span>
							<?php } ?>
							&nbsp;
							<a href="../parent/student_info.php?data_num=<?php echo $value['student_num'];?>&user=<?php echo md5('tch');?>" target="_blank">[анкетасы]</a>
						</td>
						<td>
							Login: <b><?php echo $value['username'];?></b>
						</td>
						<?php if($is_phone) { ?>
						<td>
							<span>+7 <?php echo $value['phone']; ?></span>
						</td>
						<?php } ?>
						<td class='hidden-datas' style='display:none;'>
							<input type="hidden" name="sn" value='<?php echo $value['student_num']?>'>
							<input type="hidden" name="gsn" gsn='<?php echo $value['group_student_num'];?>' value='<?php echo $value['group_student_num'];?>'>
							<input type="hidden" name="student_name" value='<?php echo $value['surname']." ".$value['name'];?>'>
						</td>
						<td>
							<!-- <button class='btn btn-info btn-xs reset_password' data-name='student'>Сбросить пароль</button>
							<input type="hidden" name="reset" value='<?php echo $value['student_num']?>'> -->
							<div class='password' style='display:inline-block;'>
								<h5 style='display: inline-block;'>Пароль: </h5>
								<?php if($value['password_type']!='default'){?>
								<button class='btn btn-info btn-xs reset_password' data-name='student' style='display: inline-block;'>Сбросить пароль</button>
								<input type="hidden" name="reset" value='<?php echo $value['student_num']?>'>
								<?php }else{?>
								<span><b><u><i>'12345'</i></u></b></span>
								<?php }?>
							</div>
						<!-- </td>						
						<td class='comment-for-students'> -->
							<?php if($no_comment && $is_comment && $result_group_info['subject_num']!='S5985a7ea3d0ae721486338'){ ?>
							<a class='btn btn-success btn-xs set-comment' data-toggle='modal' data-num = '<?php echo $group_info_num;?>' subject-num="<?php echo $subject_num;?>" data-target='.box-comment-for-teacher'>
								<!-- <span class='glyphicon glyphicon-th-list'></span> -->
								Коммент
							</a>
							<?php } ?>
						</td>
					<!-- </div> -->
				</tr>
			<?php
					}
				}
			?>
			</table>
		</td>
	</tr>
	<tr>
		<hr>
		<?php if(!empty($result_queue_student)){ ?>
		<td colspan='4'>
			<b>Курсқа жақында келетін студенттер.</b>
			<table class='table'>
				<?php 
					$count = 1;
					foreach ($result_queue_student as $value) {
				?>
				<tr>
					<td><?php echo ($count++).") ".$value['surname']." ".$value['name'];?></td>
					<td>Курсқа келетін уақыты: <?php echo $value['start_date'];?></td>
				</tr>
				<?php } ?>
			</table>
		</td>
		<?php } ?>
	</tr>
</table>