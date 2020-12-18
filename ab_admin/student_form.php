<?php
	$action = (isset($_GET['action'])) ? $_GET['action'] : "";
	$student_num = isset($_GET['data_num']) ? $_GET['data_num'] : "";
	$view = false;
	$result = array();
	if($action == 'view'){
		$view = true;
	}
	if($action == 'view'){
		include_once('../connection.php');
		try {
			$stmt = $conn->prepare("SELECT name,
										surname,
										phone,
										username,
										dob,
										school,
										class,
										home_phone,
										address,
										altyn_belgi,
										red,
										target_subject,
										target_from,
										instagram,
										DATE_FORMAT(created_date, '%d.%m.%Y %H:%I:%S') as created_date,
										(SELECT parent_num 	FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Num,
										(SELECT name 		FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Name,
									    (SELECT surname 	FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Surname,
									    (SELECT phone 		FROM parent WHERE student_num = s.student_num AND parent_order = 1) as p1Phone,
									    (SELECT parent_num 	FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Num,
									    (SELECT name 		FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Name,
									    (SELECT surname 	FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Surname,
									    (SELECT phone 		FROM parent WHERE student_num = s.student_num AND parent_order = 2) as p2Phone
									FROM student s
									WHERE s.student_num = :student_num");
			$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}
	}
?>
<div class='container-fluid'>
	<form class='student-form form-horizontal'>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='student-surname' class='control-label col-md-4 col-sm-4 col-xs-4'>Тегі:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="student_surname" id='student-surname' class='form-control' placeholder="Тегі" required="" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['surname'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='student-name' class='control-label col-md-4 col-sm-4 col-xs-4'>Есімі:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" id='student-name' name="student_name" class='form-control' placeholder="Аты" required="" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['name'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='student_altyn_belgi' class='control-label col-md-4 col-sm-4 col-xs-4'>Алтын белгі:</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="checkbox" name="student_altyn_belgi" id='student_altyn_belgi' class='form-control' style='width: 100%;' <?php echo $action=="view" ? "disabled " : ""; echo isset($result['altyn_belgi']) && $result['altyn_belgi']==1 ? 'checked="checked"' : ""; ?>  >
				</div>
			</div>
			<div class='row'>
				<label for='student_altyn_belgi' class='control-label col-md-4 col-sm-4 col-xs-4'>Қызыл аттестат:</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="checkbox" name="student_red" id='student_red' class='form-control' style='width: 100%;' <?php echo $action=="view" ? "disabled " : ""; echo isset($result['red']) && $result['red']==1 ? 'checked="checked"' : ""; ?>  >
				</div>
			</div>
		</div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='student-dob' class='control-label col-md-4 col-sm-4 col-xs-4'>Туылған күні:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="date" id='student-dob' name="student_dob" class='form-control' placeholder="dob" required="" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['dob'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='student-username' class='control-label col-md-4 col-sm-4 col-xs-4'>Username:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="student_username" id='student-username' class='form-control check-for-existing check-required' autocomplete="off" placeholder="surname.name" required="" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['username'] : "" ?>'>
					<p class="pull-right text-danger student-username"></p>
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' style='border-top:1px dashed lightgray; margin:0 0 1% 0;'></div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='student-phone' class='control-label col-md-4 col-sm-4 col-xs-4'>Телефон:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<div class='input-group'>
						<div class="input-group-addon">+7</div>
						<input type="number" name="student_phone" class='form-control check-for-existing check-required' max='7999999999' min='7000000000' id='student-phone' placeholder="7071234455" required="" <?php echo $action=="view" ? "disabled" : "";?> autocomplete="off" value='<?php echo $view ? $result['phone'] : "" ?>'>
					</div>
					<p class="text-danger pull-right student-phone"></p>
				</div>
			</div>
		</div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='student-school' class='control-label col-md-4 col-sm-4 col-xs-4'>Мектебі:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="student_school" id='student-school' class='form-control' placeholder="Оқушының мектебі" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['school'] : "" ?>' required>
				</div>
			</div>
			<div class='row'>
				<label for='student-class' class='control-label col-md-4 col-sm-4 col-xs-4'>Сыныбы:</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="number" name="student_class" id='student-class' class='form-control' placeholder="Класс" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['class'] : "" ?>'>
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' style='border-top: 1px dashed lightgray; margin:0 0 1% 0;'></div>
		<div class='form-group col-md-6 col-sm-6 col-xs-8'>
			<div class='row'>
				<label form='student-target-subject' class='control-label col-md-4 col-sm-4 col-xs-4'>Қатысатын пән:<span class='glyphicon glyphicon-asterisk required'></span></label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="target_subject" id='target-subject' class='form-control' required placeholder="Пәннің атауы" <?php echo $action=="view" ? "disabled" : ""; ?> value="<?php echo $view ? $result['target_subject'] : ""; ?>">
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' style='border-top: 1px dashed lightgray; margin:0 0 1% 0;'></div>
		<div class='dol-md-12 col-sm-12 col-xs-12'><center><h4 style='color:gray;'>Ата-ана</h4></center></div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12' style='border: 1px solid #ccc; padding-top:1%; padding-bottom:1%; border-radius: 10px;'>
			<center><h4 style='color:#aaa;'>Порталға кіре алатын ата-ана</h4></center>
			<div class='row'>
				<label for='parent-name-1' class='control-label col-md-4 col-sm-4 col-xs-4'>Есімі</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="parent_name_1" id='parent-name-1' class='form-control' placeholder="Есімі" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p1Name'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='parent-surname-1' class='control-label col-md-4 col-sm-4 col-xs-4'>Тегі</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="parent_surname_1" id='parent-surname-1' class='form-control' placeholder="Тегі" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p1Surname'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='parent-phone-1' class='control-label col-md-4 col-sm-4 col-xs-4'>Телефон:</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<div class='input-group'>
						<div class="input-group-addon">+7</div>
						<input type="number" name="parent_phone_1" class='form-control check-for-existing' max='7999999999' min='7000000000' id='parent-phone-1' placeholder="7011111111" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p1Phone'] : "" ?>' autocomplete="off">
					</div>
					<p class="text-warning pull-right parent-phone-1"></p>
				</div>
			</div>
		</div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='parent-name-2' class='control-label col-md-4 col-sm-4 col-xs-4'>Есімі</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="parent_name_2" id='parent-name-2' class='form-control' placeholder="Есімі" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p2Name'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='parent-surname-2' class='control-label col-md-4 col-sm-4 col-xs-4'>Тегі</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="parent_surname_2" id='parent-surname-2' class='form-control' placeholder="Тегі" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p2Surname'] : "" ?>'>
				</div>
			</div>
			<div class='row'>
				<label for='parent-phone-2' class='control-label col-md-4 col-sm-4 col-xs-4'>Телефон:</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<div class='input-group'>
						<div class="input-group-addon">+7</div>
						<input type="number" name="parent_phone_2" class='form-control check-for-existing' max='7999999999' min='7000000000' id='parent-phone-2' placeholder="7011111111" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['p2Phone'] : "" ?>' autocomplete="off">
					</div>
					<p class="text-warning pull-right parent-phone-2"></p>
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' style='border-top: 1px dashed lightgray; margin:0 0 1% 0;'></div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='instagram' class='control-label col-md-4 col-sm-4 col-xs-4'>Instagram</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="instagram" class='form-control' id='instagram' placeholder="Instagram accaunt" <?php echo $action=="view" ? "disabled" : ""; ?> value='<?php echo $view ? $result['instagram'] : ""; ?>'>
				</div>
			</div>
		</div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='target-from' class='control-label col-md-4 col-sm-4 col-xs-4'>АБ-ны қайдан білді?</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="text" name="target_from" class='form-control' id='target-from' placeholder="АБ-ны қайдан білді?" <?php echo $action=="view" ? "disabled" : ""; ?> value='<?php echo $view ? $result['target_from'] : ""; ?>'>
				</div>
			</div>
		</div>
		<div class='form-group col-md-6 col-sm-6 col-xs-12'>
			<div class='row'>
				<label for='home-phone' class='control-label col-md-4 col-sm-4 col-xs-4'>Үй телефоны</label>
				<div class='col-md-8 col-sm-8 col-xs-8'>
					<input type="number" name="home_phone" class='form-control' id='home-phone' min='0' max=3999999 placeholder="3223344" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['home_phone'] : "" ?>'>
				</div>
			</div>
		</div>
		<div class='form-group col-md-12 col-sm-12 col-xs-12'>
			<div class='row'>
				<label for='home-address' class='control-label col-md-2 col-sm-2 col-xs-4'>Мекен-жайы</label>
				<div class='col-md-10 col-sm-10 col-xs-8'>
					<input type="text" name="home_address" class='form-control' id='home-address' placeholder="Мекен-жайы" <?php echo $action=="view" ? "disabled" : "";?> value='<?php echo $view ? $result['address'] : "" ?>'>
				</div>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<div class='row'>
				<b class='col-md-2 col-sm-2 col-xs-4'>Тіркелген күні:</b>
				<span class='col-md-10 col-sm-10 col-xs-8'><?php echo $action=="view" ? $result['created_date'] : date('d.m.Y H:i:s'); ?></span>
			</div>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12 btns'>
			<center><input type="submit" name="<?php echo $view ? 'edit-student' : 'new-student'?>" style='<?php echo $view ? "display:none;" : "" ;?>' class='btn btn-sm btn-success student-save-btn' value='Сақтау'></center>
			<?php if($view){ ?>
				<input type="hidden" name="student_num" value="<?php echo $student_num;?>">
				<input type="hidden" name="parent_num_1" value='<?php echo $result['p1Num'];?>'>
				<input type="hidden" name="parent_num_2" value='<?php echo $result['p2Num'];?>'>
				<center>
					<input type="reset" class='btn btn-sm btn-warning' style='display: none;' value='Отмена'>
					<a class='btn btn-sm btn-info student-edit-btn'>Өзгерту</a>
				</center>
			<?php } ?>
		</div>
	</form>
</div>