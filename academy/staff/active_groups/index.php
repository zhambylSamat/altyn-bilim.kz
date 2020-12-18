<?php
	$LEVEL = 2;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
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
    include_once($root.'/staff/group/view.php');
    $subjects = get_subjects();
    $active_grouped_groups = get_active_groups_by_groups_id();
?>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<center>
			<div id='add-group-btn' class='cursor-pointer'>
				+ Жаңа топ құрастыру
			</div>
			<div id='add-group-form' class='hide'>
				<form class='form-horizontal' style='padding-bottom: 100px;'>
					<div class='col-md-4 col-sm-4 col-xs-12'>
						<div class='form-group' id='subject'>
							<label class='col-sm-4 control-label'>Пән</label>
							<div class='col-sm-8'>
								<select class='form-control' name='subject' required>
									<option value=''>Пәнді таңдау</option>
									<?php
										foreach ($subjects as $value) {
											echo "<option value='".$value['id']."'>".$value['title']."</option>";
										}
									?>
								</select>
								<b id='subject-err' class='text-danger'></b>
							</div>
						</div>
						<div class='form-group' id='topic'>
							<label class='col-sm-4 control-label'>Тарау</label>
							<div class='col-sm-8'>
								<select class='form-control' name='topic' disabled>
									<option value=''>Тарауды таңда</option>
								</select>
								<b id='topic-err' class='text-danger'></b>
							</div>
						</div>
						<div class='form-group' id='group-type'>
							<label class='col-md-4 col-sm-4 col-xs-6'>Группаның типі:</label>
							<div class='col-md-8 col-sm-8 col-xs-6'>
								<select class='form-control' name='group-type'>
									<option value=''>Группаның типін таңда</option>
									<option value='army'>Армия</option>
									<option value='marathon'>Марафон</option>
								</select>
							</div>
						</div>
					</div>
					<div class='col-md-8 col-sm-8 col-xs-12 lesson-content-info' style='text-align: center;'>
					</div>
					<div class='col-md-12 col-sm-12 col-xs-12'>
						<h3>Сабақ кестесі</h3>
						<h4>Тақырыптарға доступ әр күні сағат 7:00 де автоматты түрде ашылады!</h4>
					</div>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<div class='form-group'>
							<div id='group-schedule-data'></div>
							<div id='group-schedule'>
								<a class='btn btn-sm btn-default' data-week-id='1'>ПН.</a>
								<a class='btn btn-sm btn-default' data-week-id='2'>ВТ.</a>
								<a class='btn btn-sm btn-default' data-week-id='3'>СР.</a>
								<a class='btn btn-sm btn-default' data-week-id='4'>ЧТ.</a>
								<a class='btn btn-sm btn-default' data-week-id='5'>ПТ.</a>
								<a class='btn btn-sm btn-default' data-week-id='6'>СБ.</a>
								<a class='btn btn-sm btn-default' data-week-id='7'>ВС.</a>
							</div>
							<b id='week-id-err' class='text-danger'></b>
						</div>
					</div>
					<div class='form-group col-md-6 col-sm-6 col-xs-12'>
						<label for='group-start-date' class='col-sm-4 control-label'>Сабақ басталатын күн</label>
						<div class='col-sm-8'>
							<input type="text" name="group-start-date" id='group-start-date' disabled class='form-control' required autocomplete="off" placeholder="кк.аа.жжжж">
							<b id='group-start-date-err' class='text-danger'></b>
						</div>
					</div>
					<div class='col-md-12 col-sm-12 col-xs-12'>
						<div id='group-name-info'></div>
						<div id='start-course-date-info'></div>
						<div id='end-course-date-info'></div>
						<div id='week-id-info'></div>
					</div>
					<div class='col-md-12 col-sm-12 col-xs-12'>
						<hr>
						<center>
							<input type="submit" class='btn btn-md btn-success hide' id='save-group-btn' value='Сақтау'>
							<input type="reset" class='btn btn-sm btn-warning' id='cancel-add-group-form' value='Cancel'>
						</center>
					</div>
				</form>
			</div>
		</center>
	</div>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<hr>
	</div>
	<div class='col-md-12 ccol-sm-12 col-xs-12'>
		<table class='table table-bordered table-striped group-list'>
			<?php
				$gt = '';
				foreach ($active_grouped_groups as $group_type => $active_groups) {
					foreach ($active_groups as $val) {
						$group_type_text = '';
						if ($group_type == 'army_group') {
							$group_type_text = '<b>Армия</b> ';
						} else if ($group_type == 'marathon_group') {
							$group_type_text = '<b>Марафон</b> ';
						}
			?>
				<tr data-subject-id='0'>
					<td colspan='3' class='info'><center><h4><?php echo $group_type_text.$val['subject_title']; ?></h4></center></td>
				</tr>
				<?php
						$count = 0;
						foreach ($val['groups'] as $group_id => $value) {
							$is_army_group = $value['is_army_group'];
							$is_marathon_group = $value['is_marathon_group'];
							$table_row_style = "";
							if ($is_army_group) {
								$table_row_style = "border: 2px solid #5B7742;";
							} else if ($is_marathon_group) {
								$table_row_style = "border: 2px solid #FF7E00";
							}
				?>
					<tr style='<?php echo $table_row_style; ?>' data-is-marathon='<?php echo $value['is_marathon_group'] ? 1 : 0; ?>' data-is-army='<?php echo $value['is_army_group'] ? 1 : 0; ?>' data-subject-id='<?php echo $value['subject']['id']; ?>'>
						<td style='width: 10%; text-align: center;' class='row-count'><?php echo ++$count; ?></td>
						<td>
							<div class='row' style='padding: 0px 20px;'>
								<div class='short-info'>
									<div class='col-md-6 col-sm-6 col-xs-12'>
										<a href='?page=group&group=<?php echo $group_id;?>' target='_blank'><?php echo ($value['is_school_group'] ? '<b>24 лицей</b> ' : '').$value['group_name']; ?> <span class="badge"><?php echo $value['student_count']; ?></span></a>
										<p>
											<?php
												foreach ($value['schedule'] as $day_id) {
													echo $_SESSION['day_name'][$day_id].' ';
												}
											?>
										</p>
									</div>
									<div class='col-md-6 col-sm-6 col-xs-12'>
										<p class='pull-right'>Группаның басталу уақыты: <b><?php echo $value['start_date']; ?></b></p>
									</div>
								</div>
							</div>
							<div class='row' style='padding: 0px 20px;'>
								<div class='full-info hide' style='border-top: 1px solid lightgray;'>
									<div class='extra-info'>
										<div class='col-md-12 col-sm-12 col-xs-12'>
											<p>Оқу түрі: <b><?php echo ($value['lesson_type'] == 'subject') ? 'Пән' : 'Тарау'; ?></b></p>
											<p>Пән: <b><?php echo $value['subject']['title']; ?></b></p>
											<?php
											if ($value['topic']['title'] != '') {
												echo "<p>Тарау: <b>".$value['topic']['title']."</b></p>";
											}  
											?>
										</div>
										<div class='col-md-12 col-sm-12 col-xs-12'>
											<span>Армия:</span>
											<input type="checkbox" name="army" id='group-army' <?php echo $value['is_army_group'] ? 'checked' : ''; ?> value='<?php echo $group_id; ?>'>
										</div>
										<div class='col-md-12 col-sm-12 col-xs-12'>
											<span>Марафон:</span>
											<input type="checkbox" name="marathon" id='group-marathon' <?php echo $is_marathon_group ? 'checked' : ''; ?> value='<?php echo $group_id; ?>'>
										</div>
										<div class='col-md-12 col-sm-12 col-xs-12'>
											<p>Оқушылар:</p>
											<ol>
												<?php foreach ($value['students'] as $value) { ?>
													<li style='padding: 5px 0px; border-bottom: 1px solid lightgray' class='std-info'>
														<span><?php echo $value['last_name'].' '.$value['first_name']; ?></span>
														<?php if ($value['instagram'] != '') { ?>
														<a target='_blank' href='https://www.instagram.com/<?php echo $value['instagram']; ?>'>@<?php echo $value['instagram']; ?></a>
														<?php } else { ?>
														<i><b>Инстаграмы жоқ</b></i>
														<?php } ?>
														<span class='password'>
															<?php if ($value['password_reset'] == 0) { ?>
															<div class='pull-right'>
																<button class='btn btn-xs btn-info reset-password-btn' data-id='<?php echo $value['student_id']; ?>'>Сбросить пароль</button>
															</div>
															<?php } else if ($value['password_reset']) { ?>
															<div class='password pull-right'>
																<i class='text-warning'>Пороль: <b>12345</b></i>
															</div>
															<?php } ?>
														</span>
													</li>
												<?php } ?>
											</ol>
										</div>
									</div>
								</div>
							</div>
						</td>
						<td>
							<div>
								<button class='btn btn-xs btn-info show-group-info' title='Группа жайлы ақпараттар'><span class='glyphicon glyphicon-align-justify'></span></button>
							</div>
						</td>
					</tr>
				<?php }}} ?>
		</table>
	</div>
</div>

<div class="modal fade" id="transfer-students-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Группны таңдаңыз</h4>
      		</div>
      		<div class="modal-body">
      			<center>
      				<form class='form-inline' id='transfer-to-form'>
	      				<div class='form-group'>
	      					<label>Группа</label>
	      					<select name='group' class='form-control' required>
	      					</select>
	      					<input type="hidden" name="group-id" value=''>
	      				</div>
	      				<br>
	      				<br>
	      				<div class='form-group'>
	      					<input type="submit" name="transfer-to" class='btn btn-success btn-sm' value='Сақтау'>
	      					<button type='button' class='btn btn-warning btn-xs transfer-students-cancel'>Отмена</button>
	      				</div>
	      			</form>
	      			<div class="alert alert-success" role="alert">
					 	<b id='old-group'></b> тан <b id='new-group'></b> қа оқушылар көшірілді!
					</div>
      			</center>
      		</div>
    	</div>
		</div>
</div>