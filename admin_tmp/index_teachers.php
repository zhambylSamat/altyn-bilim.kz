
<table class="table table-striped table-bordered">
	<?php
		$result_teacher = array();
		include('../connection.php');
		if(!isset($_GET['search']) || $_GET['search']==''){ 
			try {
				$stmt = $conn->prepare("SELECT * FROM teacher WHERE block != 6 ORDER BY surname ASC");
			     
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
				<div class='input-group'>
					<input type="text" class='form-control' name="username" required="" value='<?php echo $readrow['username']?>'>
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
						<td style='width: 40%'><h4 class='text-success object-full-name'><?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?></h4></td>
						<td style='width: 30%'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
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