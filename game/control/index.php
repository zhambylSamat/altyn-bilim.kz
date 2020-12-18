<?php

	include_once("../connection.php");
	$link = $_SESSION['link'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Ведущий</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<style type="text/css">
		.team, .full-height-img {
			height: 24vh;
		}
		.display-none {
			display:none;
		}
		.success-wrong-height, .success-wrong-img-height {
			height: 7vh;
			cursor: pointer;
		}
		#success {
			background-color: #C0DBBA;
		}
		#wrong {
			background-color: #F7B9B9;
		}
	</style>
</head>
<body>
	<audio id="selected-sound">
	  <source src="<?php echo $link; ?>game/sounds/selected.mp3" type="audio/mpeg">
	</audio>
	<audio id="success-sound">
	  <source src="<?php echo $link; ?>game/sounds/success.mp3" type="audio/mpeg">
	</audio>
	<audio id="wrong-sound">
	  <source src="<?php echo $link; ?>game/sounds/wrong.mp3" type="audio/mpeg">
	</audio>
	<div class='container'>
		<div class='row'>
			<div class='col-12 col-sm-12 col-md-12 mb-5'>
				<center>
					<h1 id='is_ready'></h1>
					<?php
						// $html = "";
						// if ($aba_row_count != 3 || $result_row_count != 0 || true) {
						// 	$html .= "";
						// }
						// echo $html;
					?>
					<button class='btn btn-md btn-info display-none' id='access-btn'>Сброс</button>
				</center>
			</div>
			<div class='col-12 col-sm-6 col-md-6 success-wrong-height' id='success'>
				<center><img class='success-wrong-img-height' src="<?php echo $link; ?>game/img/success.png"></center>
			</div>
			<div class='col-12 col-sm-6 col-md-6 success-wrong-height' id='wrong'>
				<center><img class='success-wrong-img-height' src="<?php echo $link; ?>game/img/wrong.png"></center>
			</div>
			<div class='col-12 col-sm-12 col-md-12'>
				<input type="hidden" id="link" value="<?php echo $link; ?>">
				<table class='table table-bordered'>
					<tr id='team-1' data='' class='team'>
						<td></td>
					</tr>
					<tr id='team-2' data='' class='team'>
						<td></td>
					</tr>
					<tr id='team-3' data='' class='team'>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script type="text/javascript">
		var teams_info = {};
		$(document).ready(function() {
			$.when(get_team()).done(function($result){
				$result = $.parseJSON($result);
				teams_info = $result;
				setInterval(get_infos, 100);
			});
		});

		function get_team() {
			return $.ajax({
				type: 'GET',
				url: 'controllers.php?get_teams=<?php echo md5('success'); ?>', 
				cache: false,
				beforeSend:function(){
					$('#is_ready').html('Loading...');
				},
				success: function(dataS){
					$('#is_ready').html('');
				}
			});
		}

		function get_infos() {
			$.when(get_bd_infos()).done(function($result) {
				$result = $.parseJSON($result);
				if ($result.answer_button_access.count != 3 || $result.results.count != 0) {
					$('#access-btn').removeClass('display-none');
				} else {
					$('#access-btn').addClass('display-none');
				}

				$count = 0;
				$link = $('#link').val();
				$result.results.data.forEach(function($team_id) {
					$count++;
					if ($('#team-'+$count).attr('data') != 'has') {
						$('#team-'+$count).attr('data', 'has');
						$('#team-'+$count+' td').css({'background-color': teams_info[$team_id]['color']}).html('<center><img class="full-height-img" src="'+$link+teams_info[$team_id]['img']+'"></center>');
						document.getElementById('selected-sound').play();
					}
				});
			});
		}

		function get_bd_infos() {
			return $.ajax({
				type: "GET",
				url: 'controllers.php?get_infos=<?php echo md5('true'); ?>',
				cache: false
			});
		}

		function reset_result_tables() {
			$('#team-1 td').css({'background-color': 'white'}).html('');
			$('#team-2 td').css({'background-color': 'white'}).html('');
			$('#team-3 td').css({'background-color': 'white'}).html('');
		}

		function remove_team_datas() {
			$('#team-1').attr('data', '');
			$('#team-2').attr('data', '');
			$('#team-3').attr('data', '');
		}

		$(document).on('click', '#access-btn', function() {
			$.ajax({
				type: 'GET',
				url: 'controllers.php?reset_datas=<?php echo md5('true'); ?>',
				cache: false,
				success: function() {
					remove_team_datas();
					reset_result_tables();
				}
			});
		});
		$(document).on('click', '#success', function() {
			document.getElementById('success-sound').play();
		});
		$(document).on('click', '#wrong', function() {
			document.getElementById('wrong-sound').play();
		});
		$(document).on('keydown', function(event) {
			console.log(event.which);
			if (event.which == 37) {
				document.getElementById('success-sound').play();	
			} else if (event.which == 39) {
				document.getElementById('wrong-sound').play();
			}
		});	
	</script>
</body>
</html>