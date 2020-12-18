
<table class="table table-striped table-bordered">
	<?php
		$result_teacher = array();
		include('../connection.php');
		if(!isset($_GET['search']) || $_GET['search']==''){ 
			try {

				$stmt = $conn->prepare("SELECT *,
										    (SELECT AVG(sps.mark)
										    FROM student_poll sp,
										        student_polls sps
										    WHERE sp.teacher_num = t.teacher_num
										        AND sps.student_poll_id = sp.id
										        AND sp.polled_date >= STR_TO_DATE((SELECT (
										                                    CASE
										                                        WHEN DATE_FORMAT(sp2.polled_date, '%d') <= 10 THEN DATE_FORMAT(DATE_SUB(sp2.polled_date, INTERVAL 1 MONTH), '25-%m-%Y')
										                                        WHEN DATE_FORMAT(sp2.polled_date, '%d') >= 25 THEN DATE_FORMAT(sp2.polled_date, '25-%m-%Y')
										                                    END
										                                ) AS month
										                                FROM student_poll sp2
										                                WHERE sp2.teacher_num = t.teacher_num
										                                ORDER BY month DESC
										                                LIMIT 1), '%d-%m-%Y')
										                             ) AS poll_avg
										FROM teacher t
										WHERE t.block != 6
										ORDER BY t.surname ASC");
			     
			    $stmt->execute();
			    $result_teacher = $stmt->fetchAll(); 
			} catch (PDOException $e) {
				echo "Error ".$e->getMessage()." !!!";
			}
			$_SESSION['result_teacher'] = $result_teacher;
		}
		else{
			$q = $_GET['search'];
			foreach ($_SESSION['result_teacher'] as $val) {
				if (strpos(mb_strtolower($val['name']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['surname']), mb_strtolower($q)) !== false 
					|| strpos(mb_strtolower($val['username']), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['surname'])."_".mb_strtolower($val['name'])), mb_strtolower($q)) !== false 
					|| strpos((mb_strtolower($val['name'])."_".mb_strtolower($val['surname'])), mb_strtolower($q)) !== false) {
					array_push($result_teacher, $val);
				}
			}
		}

		$teacher_number = 1;
		foreach ($result_teacher as $readrow) {
	?>
	<tr class='head'>
		<td style='width: 5%;'><center><h4 class='count'><?php echo $teacher_number;?></h4></center></td>
		<td style='width: 75%'>
			<form class='form-inline form-edit user_info' action='admin_controller.php' method='post'>
				<div class='form-group'>
					<input type="text" class='form-control' name="surname" required="" value="<?php echo $readrow['surname'];?>">
				</div>
				<div class='form-group'>
					<input type="text" class='form-control' name="name" required="" value="<?php echo $readrow['name'];?>">
				</div>
				<div class='form-group'>
					<input type="text" class='form-control' name="username" required="" value='<?php echo $readrow['username']?>'>
				</div>
				<div class='form-group'>
					<input type="date" name="dob" required="" class='form-control' value='<?php echo $readrow['dob']; ?>'>
				</div>
				<input type="hidden" name="edit-teacher-num" value='<?php echo $readrow['teacher_num'];?>'>
				<button type='submit' name='edit_teacher' class='btn btn-default btn-md' title='OK'>
					<span class='glyphicon glyphicon-ok-sign text-success pull-right' aria-hidden='true' style='cursor:pointer;'></span>
				</button>
				<a class='btn btn-default btn-md cancel_edit' type='reset' title='Отмена'>
					<span class='glyphicon glyphicon-remove-sign text-warning pull-right' aria-hidden='true' style='cursor:pointer;'></span>
				</a>
			</form>
			<div class='user_info'>
				<table class='' style='width:100%; background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr>
						<td style='width: 30%'><h4 class='text-success object-full-name'><?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?></h4></td>
						<td style='width: 15%;'><button class='btn btn-info btn-xs teacher-poll-result' data-toggle='modal' data-target='.box-universal' data-num="<?php echo $readrow['teacher_num'];?>">Опрос: <?php echo isset($readrow['poll_avg']) ? round($readrow['poll_avg'], 2) : "N/A"; ?></button></td>
						<td style='width: 25%'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
						<td style='width: 30%'>
							<div class='password'>
								<h5>Пароль: 
									<?php if($readrow['password_type']!='default'){?>
									<button class='btn btn-info btn-xs reset_password' data-name='teacher' style='display: inline-block;'>Сбросить пароль</button>
									<input type="hidden" name="reset" value='<?php echo $readrow['teacher_num']?>'>
									<?php }else{?>
									<span><b><u><i>'123456'</i></u></b></span>
									<?php }?>
								</h5>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td style='width: 20%;'>
			<!-- <form onsubmit="return confirm('Вы точно хотите удалить учителя? Все данные об учителе будут удалены.')" action='admin_controller.php' method='post'> -->
				<center>
					<!-- <a class='btn btn-default btn-sm more_info' data-name='teacher' data_toggle='false' data_num = "<?php echo $readrow['teacher_num']?>" title='Толығырақ'>
						<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</a> -->
					<a class='btn btn-default btn-xs edit_user' title='Өзгерту'>
						<span class='glyphicon glyphicon-pencil text-warning' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</a>
					<a class='btn btn-xs btn-danger to_archive' data-name='teacher' data-num = "<?php echo $readrow['teacher_num']; ?>" title='Архивировать'>
						<span class='glyphicon glyphicon-save-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</a>
					<!-- <input type="hidden" name="remove-teacher-num" value="<?php echo $readrow['teacher_num']?>"> -->
					<!-- <button class='btn btn-default btn-sm' type='submit' value='teacher_num' name='remove_teacher' title='Жою'>
						<span class='glyphicon glyphicon-remove text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button> -->
					<a href="schedule.php?data_num=<?php echo $readrow['teacher_num'];?>" target="_blank" title='Сабақ кестесі' class='btn btn-xs btn-default'><span style='font-size: 20px; cursor: pointer;' class='glyphicon glyphicon-calendar text-info'></span></a>
				</center>
			<!-- </form> -->
		</td>
	</tr>
	<tr class='body'>
		
	</tr>
	<?php $teacher_number++; }?>
</table>