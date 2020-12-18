<!DOCTYPE html>
<html>
<head>
	<?php
		include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
		include_once($root.'/common/assets/meta.php');
	?>
	<title>Admin - Online Academy</title>
	<?php 
		include_once($root.'/common/assets/style.php');
		include_once($root.'/common/connection.php');
		include_once('student_style.php');

		include_once($root.'/common/assets/js.php');
		include_once('student_js.php');

		include_once('view.php');
	?>
</head>
<body>
	<?php
		check_student_access();
		include_once('nav.php');
	?>
	<?php
		include_once($root.'/common/set_navigations.php');
		$LEVEL = 0;
	?>
	<section id='body'>
		<div class='container-fluid'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12'>
					<?php
						set_navigation($LEVEL);
					?>
				</div>
			</div>
		</div>
	</section>

	<script type="text/javascript" id='a'>

		$(document).ready(function() {
			setInterval(check_token, 150000); //150000 seconds => 2.5 minutes
			toggle_army_diagram();
			go_to_registration_course_if_empty_group();
		});

		function check_token() {
			$.ajax({
				type: 'GET',
				url: 'controller.php?check_token',
				cache: false,
				success: function($result_data) {
					$json = $.parseJSON($result_data);
					if (!$json.success) {
						window.location.href = "../log_out.php";
					}
				},
				error: function() {
					window.location.href = "../log_out.php";
				}
			});
		}

		function go_to_registration_course_if_empty_group() {
			$lesson_box_length = $('.lessons-box').length;
			$is_navigation_active = $('.lesson-process-navigation').parents('li').hasClass('active');
			$has_lesson_body = $('.lesson-body');
			console.log('okkk', $is_navigation_active);
			if ($lesson_box_length == 0 && $is_navigation_active && !$has_lesson_body) {
				$element = $('.registration-navigation').parents('.navigation');
				set_navigation($element);
			}
		}

		function toggle_army_diagram() {
			$.ajax({
				type: 'GET',
				url: 'controller.php?has_army_group',
				cache: false,
				success: function($data) {
					console.log($data);
					$json = $.parseJSON($data);
					if ($json.success) {
						console.log($json.has_army_group);
						if (!$json.has_army_group) {
							$('.army-diagram-navigation').parents('.navigation').remove();
						} else {
							$('.army-diagram-navigation').removeClass('hide-army-diagram-navigation');
						}
					}
				}
			});
		}

		// $(document).ready(function() {
		// 	if ('true' == ('<?php echo (isset($_SESSION['alert']['r_done']) && $_SESSION['alert']['r_done'] == true) ? 'true' : 'false' ;?>')) {
		// 		$('#do-payment-register').modal('show');
		// 		console.log('<?php $_SESSION['alert']['r_done'] = false; ?>');
		// 	}
		// });

		$('#a').remove();

	</script>
</body>
</html>