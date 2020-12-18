<?php
	include('../connection.php');
	try {
		$stmt = $conn->prepare("SELECT s.subject_name,
									s.subject_num, 
									gi.group_info_num, 
									t.teacher_num,
									t.name, 
									t.surname, 
									gi.group_name, 
									gi.comment,
									gs.block,
									DATE_FORMAT(gs.start_date, '%d.%m.%Y') AS start_date,
									DATE_FORMAT(gs.block_date, '%d.%m.%Y') AS block_date,
									(SELECT DATE_FORMAT(tr2.created_date, '%d.%m.%Y')
	                                    FROM transfer tr2
	                                    WHERE tr2.new_group_info_num = gi.group_info_num
	                                    	AND tr2.student_num = gs.student_num
	                                    ORDER BY tr2.created_date DESC
	                                    LIMIT 1) AS transfer_date,
								    (SELECT count(r.group_student_num) 
								    FROM review r
								    WHERE r.group_student_num = gs.group_student_num 
								    	AND r.review_info_num != (SELECT review_info_num 
								    								FROM review_info 
								    								WHERE description = 'comment') 
								    GROUP BY r.group_student_num) c
								FROM group_info gi, 
									group_student gs, 
									subject s, 
									teacher t
								WHERE gi.subject_num = s.subject_num 
									AND gs.student_num = :student_num 
									AND gs.start_date <= CURDATE()
									AND gs.group_info_num = gi.group_info_num 
									AND gi.teacher_num = t.teacher_num
								ORDER BY gs.block ASC, s.subject_name ASC, gs.start_date ASC");
		$stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result = $stmt->fetchAll(); 
	    $result_count = $stmt->rowCount();

	    $stmt = $conn->prepare("SELECT count(description) c 
	    						FROM review_info 
	    						WHERE description = 'review' 
	    						GROUP BY description");
	    $stmt->execute();
	    $total_comment_number = $stmt->fetch(PDO::FETCH_ASSOC);

	    $stmt = $conn->prepare("SELECT gi.group_info_num, 
	    							gi.group_name, 
	    							DATE_FORMAT(gs.start_date, '%d.%m.%y') as start_date
	    						FROM group_info gi,
	    							group_student gs
	    						WHERE gs.student_num = :student_num
	    							AND gs.start_date > CURDATE() 
	    							AND gi.group_info_num = gs.group_info_num
	    						ORDER BY gs.start_date ASC");
	    $stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
	    $stmt->execute();
	    $result_queue_student = $stmt->fetchAll();


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

		$stmt = $conn->prepare("SELECT sp.teacher_num
								FROM student_poll sp
								WHERE sp.student_num = :student_num
									AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
									AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')");
		$stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
		$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
		$stmt->execute();
		$polled_teachers = $stmt->fetchAll();

		$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi.group_info_num
                                    	AND tr2.student_num = gs.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";

		$stmt = $conn->prepare("SELECT DISTINCT gi.teacher_num
								FROM group_student gs,
									group_info gi
								WHERE gs.student_num = :student_num
									AND gs.block != 6
									AND gi.subject_num != 'S5985a7ea3d0ae721486338'
									AND gi.group_info_num = gs.group_info_num
									AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')");
		$stmt->bindParam(':student_num', $_GET['data_num'], PDO::PARAM_STR);
		$stmt->bindParam(':poll_activate_days', $poll_activate_days, PDO::PARAM_STR);
		$stmt->execute();
		$active_polled_teachers = $stmt->fetchAll();

		$not_polled_teachers = array();
		foreach ($active_polled_teachers as $value) {
			$exists = false;
			foreach ($polled_teachers as $val) {
				if ($value['teacher_num'] == $val['teacher_num']) {
					$exists = true;
				}
			}
			if (!$exists) {
				array_push($not_polled_teachers, $value['teacher_num']);
			}
		}

	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<td colspan='4' style='padding:0;'>
<?php if($result_count > 0) {?>
<table style='border:2px solid gray; margin:0;' class='table table-striped table-bordered'>
	<tr>
		<th>#</th>
		<th>Группа</th>
		<th>Пән</th>
		<th>Мұғалім</th>
		<th>Курс:</th>
		<th>Трансфер:</th>
		<th>Статус</th>
	</tr>

	<?php 
		for($i = 0; $i<count($result); $i++){ 
			$alert = 'hide';

			if ($result[$i]['c']=='' || $result[$i]['c']%intval($total_comment_number['c'])!=0) {
				if ($result[$i]['transfer_date'] != '') {
					if (strtotime($poll_activate_days) >= strtotime($result[$i]['transfer_date'])) {
						$alert = 'show';
					}
				} else {
					if (strtotime($poll_activate_days) >= strtotime($result[$i]['start_date'])) {
						$alert = 'show';
					}
				}
			}
	?>
	<tr>
		<td>
			<?php echo $i+1;?>
			<?php if($alert=='show' && $result[$i]['subject_num']!='S5985a7ea3d0ae721486338' && $result[$i]['block']!=6){ ?>
			<span class='glyphicon glyphicon-exclamation-sign notification-sign-detail pull-right' title='Оқушыға комментарий енгізбеген' data-toggle='tooltip' data-placement='left' style='color: #FFB564; font-size: 18px;'></span>
			<?php } if (in_array($result[$i]['teacher_num'], $not_polled_teachers) && $result[$i]['block']!=6 && $is_active_period) { ?>
			<span class='glyphicon glyphicon-remove-circle notification-sign pull-right' title='Оқушылар мұғалімдерге опрос толтырмаған' data-toggle='tooltip' data-placement='left' style='color: #E52C38; font-size: 15px;'></span>
			<?php }?>
		</td>
		<td>
			<?php
				$link = "group.php?data_num=".$result[$i]['group_info_num'];
				if($result[$i]['block']==6){
					$link = "../archive/group.php?data_num=".$result[$i]['group_info_num'];
				}
			?>
			<a href="<?php echo $link;?>" target='_blank'><?php echo $result[$i]['group_name'];?></a>
		</td>
		<td><?php echo $result[$i]['subject_name'];?></td>
		<td><?php echo $result[$i]['name']." ".$result[$i]['surname'];?></td>
		<td>
			<span style='font-size:10px;'>Бастауы: </span><span class='text-success'><?php echo $result[$i]['start_date'];?></span>
			<?php if($result[$i]['block']==6){?>
			<br>
			<span style='font-size:10px;'>Аяқтауы: </span><span class='text-danger'><?php echo $result[$i]['block_date'];?></span>
			<?php }?>
		</td>
		<td>
			<?php
				if ($result[$i]['transfer_date'] != '') {
					echo $result[$i]['transfer_date'];
				} else {
					echo "Трансфер жасалмаған";
				}
			?>
		</td>
		<?php if($result[$i]['block']==6){ ?>
		<td style='background-color: #ffae19;'><b class='text-warning'>Архив</b></td>
		<?php }else{ ?>
		<td style='background-color: lightgreen;'><b class='text-success'>Активный</b></td>
		<?php }?>
	</tr>
	<?php } ?>

</table>
<?php }else { ?>
<center><b>N/A</b></center>
<?php } ?>





<?php 
	if(!empty($result_queue_student)){
		echo "<hr>
			<table style='border:2px solid gray;' class='table table-striped table-bordered'>
				<tr>
					<th>#</th>
					<th>Группа</th>
					<th class='text-warning'>Курсты бастайтын уақыты</th>
				</tr>";
		// foreach ($result_queue_student as $value) 
		for($i = 0; $i<count($result_queue_student); $i++){
?>
	<tr>
		<td><?php echo $i+1;?></td>
		<td>
			<a href="group.php?data_num=<?php echo $result_queue_student[$i]['group_info_num'];?>" target='_blank'>
				<?php echo $result_queue_student[$i]['group_name'];?>
			</a>	
		</td>
		<td>
			<b><?php echo $result_queue_student[$i]['start_date'];?></b>
		</td>
	</tr>
<?php } echo "</table>"; } ?>
</td>
