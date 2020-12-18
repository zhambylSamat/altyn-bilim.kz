<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'teacher';
	}

	// $current_day = intval(date('d'));
	// $start_day = 16;
	// $end_day = 11;
	// $start_date = "";
	// $end_date = "";
	// $is_active_period = false;

	// if ($current_day >= $start_day) {
	// 	$start_date = date('d-m-Y', strtotime('25-'.date('d-m-Y')));
	// 	$end_date = date('d-m-Y', strtotime('10-'.date('m-Y', strtotime('+1 month', strtotime(date('d-m-Y'))))));
	// 	$is_active_period = true;
	// } else if ($current_day <= $end_day) {
	// 	$start_date = date('d-m-Y', strtotime('25-'.date('m-Y', strtotime("-1 month", strtotime(date('d-m-Y'))))));
	// 	$end_date = date('d-m-Y', strtotime('10-'.date('d-m-Y')));
	// 	$is_active_period = true;
	// }

	// $stmt = $conn->prepare("SELECT s.surname,
 //   								s.name,
 //   								(SELECT count(sp.id)
	// 							FROM student_poll sp
	// 							WHERE sp.student_num = s.student_num
	// 								AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
	// 								AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y')) AS is_polled
 //   							FROM student s
 //   							WHERE s.student_num != 'US5985cba14b8d3100168809'
	// 								AND s.block != 6
 //   							GROUP BY s.student_num
 //   							ORDER BY s.surname, s.name");
 //   	$stmt->bindParam(":start_date", $start_date, PDO::PARAM_STR);
	// $stmt->bindParam(":end_date", $end_date, PDO::PARAM_STR);
 //   	$stmt->execute();
 //   	$not_polled_students_query = $stmt->fetchAll();

 //   	$stmt = $conn->prepare("SELECT count(id) AS poll_info_count FROM teacher_poll_info");
	// $stmt->execute();
 //    $total_poll_number = $stmt->fetch(PDO::FETCH_ASSOC)['poll_info_count'];
?>
<button class='btn btn-success btn-sm new-teacher' at='new-teacher' id='new-teacher-btn'>Мұғалім енгізу</button>
<button class='btn btn-info btn-sm comment-for-teacher' at='comment-for-teacher' data-toggle='modal' data-target='.box-comment-for-teacher' id='new-teachcer-btn'><span class='glyphicon glyphicon-th-list'></span></button>
<a class='btn btn-sm btn-default news' data-toggle='modal' data-target='.box-news' data-type='teacher'>Жаңалықтар (Мұғалім)</a>
<button class='btn btn-success btn-sm <?php echo $_SESSION['role']==md5('admin') ? 'btn-suggestion' : 'suggestion' ; ?>' data-toggle='modal' data-target='.box-suggestion'>Ұсыныс</button>

<?php if ($_SESSION['role']==md5('admin')) { ?>
<button class='btn btn-info btn-sm poll-info' data-toggle='modal' data-target='.box-universal'>Опросник</button>
<?php } ?>
<!-- <hr>
<div class="btn-group-vertical" role='group'>
	<?php if (count($not_polled_students_query) > 0 && $is_active_period) { ?>
		<button style='text-align: left !important;'
				data-toggle='modal'
				data-target='.box-not-polled-student-notification'
				class="btn btn-success not-polled-student-notification">
			Опрос толтырмаған оқушылар
			<span class='badge'><?php echo count($not_polled_students_query); ?></span>
		</button>
	<?php } ?>
</div> -->
<hr>
<p style='font-size: 13px;'><i>Оқушылардың сауалнама толтыратын уақыты ар айдың 25-не басталып келесі айдың 10-на дейін жалғасады</i></p>
<div id='new-teacher'>
	<form class='form-inline' id='create-teacher' method='post'>
		<div class="form-group">
	    	<label for="id-teacher-surname">Тегі</label>
	    	<input type="text" name='surname' class="form-control" id="id-teacher-surname" placeholder="Тегі" required="">
	  	</div>
		<div class="form-group">
			<label for="id-teacher-name">Аты</label>
	    	<input type="text" name='name' class="form-control" id="id-teacher-name" placeholder="Аты" required="">
	  	</div>
	  	<div class='form-group'>
	  		<input type="text" name="username" class='form-control' title='"name.surname" и все буквы должны в нижнем регисте' placeholder='name.surname' required="" pattern='[a-z]+[0-9]*(\.[a-z]+)[0-9]*'>
	  	</div>
	  	<div class='form-group'>
	  		<input type="date" name="dob" class='form-control' required="">
	  	</div>
	  	<input type="submit" class='btn btn-info btn-sm' value='Жіберу'>
	  	<a class='btn close-add-new-teacher' title='Отмена'><span class='glyphicon glyphicon-remove text-danger' style="font-size: 18px;"></span></a>
	</form>
</div>
<input type="text" name="search" data-name='teacher' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
<hr>
<div class='teachers'>
	<?php include_once('index_teachers.php');?>
</div>