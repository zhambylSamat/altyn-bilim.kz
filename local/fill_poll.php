<?php
	include_once('../connection.php');

	if (isset($_SESSION['access']) && $_SESSION['access'] != md5('false')) {
		header('location:index.php');
	}


	$stmt = $conn->prepare("SELECT id, text FROM teacher_poll_info ORDER BY text ASC");
	$stmt->execute();
	$poll_infos = $stmt->fetchAll();

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

	
	$transfer_students_tbl_sql = "SELECT tr2.created_date
                                    FROM transfer tr2
                                    WHERE tr2.new_group_info_num = gi.group_info_num
                                    	AND tr2.student_num = gs.student_num
                                    ORDER BY tr2.created_date DESC
                                    LIMIT 1";
	$stmt = $conn->prepare("SELECT DISTINCT 
								t.teacher_num,
							    t.name,
							    t.surname
							FROM group_info gi,
							    teacher t,
							    group_student gs
							WHERE gs.student_num = :student_num
								AND gs.block != 6
								AND gi.subject_num != 'S5985a7ea3d0ae721486338'
							    AND gi.group_info_num = gs.group_info_num
							    AND t.teacher_num = gi.teacher_num
							    AND STR_TO_DATE(:poll_activate_days, '%d-%m-%Y') >= DATE_FORMAT((CASE
                                                             	WHEN ($transfer_students_tbl_sql) IS NULL THEN DATE_FORMAT(gs.start_date, '%Y-%m-%d')
                                                              	ELSE ($transfer_students_tbl_sql)
                                                             END), '%Y-%m-%d')
							    AND t.teacher_num NOT IN (SELECT sp.teacher_num
									FROM student_poll sp
									WHERE sp.student_num = gs.student_num
										AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') >= STR_TO_DATE(:start_date, '%d-%m-%Y')
										AND DATE_FORMAT(sp.polled_date, '%Y-%m-%d') <= STR_TO_DATE(:end_date, '%d-%m-%Y'))");
	$stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
	$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
	$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
	$stmt->bindParam(':poll_activate_days', $poll_activate_days, PDO::PARAM_STR);
	$stmt->execute();
	$teacher_arr = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn Bilim | Rating</title>
	<?php include_once('style.php');?>

	<style type="text/css">
		.star, .star-select{
			font-size: 25px;
			margin: -2px;
			padding: none;
		}
		.star-select {
			color: #F7E600 !important;
			text-shadow: 0px 0px 10px #F6F669;
		}
		.star:hover, .star-select:hover {
			cursor: pointer;
		}
		.glyphicon-star {
			color: #F7D100;
		}
		.glyphicon-star-empty {
			color: lightgray;
		}
		#errors{
			color: red;
			font-size: 20px;
			display: none;
		}
	</style>
</head>
<body>
	<?php include_once('nav.php');?>
	<div class='container'>
		<div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2'>
			<center>
				<h3>Мұғалімдердің жұмысын бағалау сауалнамасы</h3>
				<hr>
				<p style='font-size: 15px; font-weight: bold; color: #666;'>
					Бұл сауалнама оқытудың сапасын арттыруға бағытталған.
					<br>
					Сол себепті сенің пікірің біз үшін маңызды. 
					<br>
					<br>
					<span>Сауалнама анонимді.</span>
				</p>
			</center>
			<form onsubmit="return validation()" id='poll-form' method='post' action="controll-user.php" style='margin-bottom: 30px;'>
				<table class='table table-bordered table-striped'>
						<?php
							$teacher_num = "";
							foreach ($teacher_arr as $value) {
								if ($teacher_num != $value['teacher_num']) {
									$teacher_num = $value['teacher_num'];

									$html = "<tr><td colspan='2'><center><b>";
									$html .= "Мұғалім: ".$value['surname']." ".$value['name'];
									$html .= "<input type='hidden' name='teacher-num[]' value='".$teacher_num."'>";
									$html .= "</b></center></td></tr>";

									echo $html;
								}
								foreach ($poll_infos as $val) {
						?>
						<tr>
							<td style='width: 65%;'>
								<?php echo $val['text']; ?>
							</td>
							<td style='width: 35%;'>
								<center>
								<?php for ($i = 1; $i <= 5; $i++) { ?>
									<span class='glyphicon glyphicon-star-empty star' data-count="<?php echo $i; ?>"></span>
								<?php } ?>
								</center>
								<input type="hidden" name="poll-res[]">
								<input type="hidden" name="poll-id[]" value="<?php echo $val['id']; ?>">
							</td>
						</tr>
						<?php }} ?>
				</table>
				<center>
					<p id='errors'>Сауалнама толықтай белгіленуі керек</p>
					<button role='submit' class='btn btn-success btn-sm' name='submit-poll' style='padding: 5px 30px;'>Сақтау</button>
				</center>
			</form>
		</div>
	</div>

	<script type="text/javascript">
		$(document).on('mouseenter', '.star', function(){
			$current_position = $(this).attr('data-count');
			$(this).parents('td').find('.star').each(function(){
				if ($(this).attr('data-count') <= $current_position){
					$(this).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
				}
			});
		});
		$(document).on('mouseout', '.star', function(){
			$('.star').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
		});
		$(document).on('click', '.star, .star-select', function(){
			$(this).parents('td').find('.star-select')
								.removeClass('star-select')
								.removeClass('glyphicon-star')
								.addClass('star')
								.addClass('glyphicon-star-empty');
			$current_position = $(this).attr('data-count');
			$(this).parents('td').find('input[name="poll-res[]"]').val($current_position);
			$(this).parents('td').find('.star').each(function(){
				if ($(this).attr('data-count') <= $current_position) {
					$(this).removeClass('star')
						.removeClass('glyphicon-star-empty')
						.addClass('star-select')
						.addClass('glyphicon-star');
				}
			});
		});
		function validation() {
			$has_error = false;
			$("#poll-form").find('input[name="poll-res[]"]').each(function(){
				if ($(this).val() == "" && !$has_error) {
					$has_error = true;
				}
			});
			if ($has_error) {
				$('#errors').show();
			}
			return !$has_error;
		}
	</script>
</body>
</html>