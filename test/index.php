<?php
	include_once('../connection.php');
	// if(!isset($_SESSION['ees_code']) && !isset($_SESSION['adminNum'])){
	// 	header('location:signin.php');
	// }
	if(!isset($_SESSION['ees_code'])) {
		header('location:signin.php');
	}
	// echo $_SESSION['ees_code'];
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Тест | Altyn-bilim</title>
	<?php include_once("style.php"); ?>
</head>
<body>
	<?php
		if(isset($_SESSION['adminNum'])){
			include_once('../ab_admin/nav.php');
		} 
		else{
			include_once('nav.php');
		}
	?>
	<?php include_once("js.php"); ?>

	<section id='main'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12'>
					
				</div>
			</div>
		</div>
	</section>

	<section id='test-content'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12 col-xs-12' id='main-content'>
					<?php
						if(isset($_GET['ees_id'])){
							$ees_id = $_GET['ees_id'];
							include_once('test_result.php');
						}
						else if(isset($_SESSION['finish']) && $_SESSION['finish']){
							include_once('test_result.php');
						}
						else{
							include_once('test.php');
						}
					?>		
				</div>
			</div>
		</div>
	</section>

	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>

	<div class='modal fade' id='instruction-modal' tabindex='-1' role='dialog'>
		<div class='modal-dialog' role='document'>
			<div class='modal-content'>
				<div class='modal-header'>
					<button type="button" class="close" data-dismiss="modal" style='opacity: 0.4;' aria-label="Close"><span style='color: white; box-shadow: 0;' aria-hidden="true">&times;</span></button>
        			<h4 class="modal-title">Тест тапсыруға НҰСҚАУЛЫҚ</h4>
				</div>
				<div class='modal-body'>
					<p class='rules-text'>&nbsp;&nbsp;&nbsp;Тест тапсыру барысында келесі <b>ережелерді</b> ұстан:</p>
					<ol id='rules-list'>
						<li>Әр есепте тек бір ғана дұрыс жауап бар;</li>
						<li>Жауаптарыңды шаршының ішіне "птичка" белгісін қою арқылы белгіле;</li>
						<li>Егер есептің шешімін таба алмасаң наугад белгілеме;</li>
						<li>Тест барысында сенің жауаптарыңа байланысты сұрақтар қиындап немесе оңайлап отырады;</li>
						<li>Сұрақтар саны нақты белгіленбеген, сенің білім деңгейіңді анықтаған соң тест автоматты түрде аяқталады.</li>
					</ol>
					<p class='rules-text' style='margin-bottom: 0;'>&nbsp;&nbsp;&nbsp;Тесттің соңында саған математиканы қай тараудан бастау керек екендігі туралы кеңесіміз шығады.</p>
				</div>
				<div class='modal-footer'>
					<center>
						<button class='btn btn-md btn-default' data-dismiss='modal'>Түсіндім</button>
					</center>
				</div>
			</div>
		</div>
	</div>

	<div class='modal fade' id='select-answer-modal' tabindex='-1' role='dialog'>
		<div class='modal-dialog' role='document'>
			<div class='modal-content'>
				<div class='modal-header'>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        			<h4 class="modal-title" id="myModalLabel">Есептің жауабы белгіленбеді</h4>
				</div>
				<div class='modal-body'>
					<table class='table' style='margin: 0;'>
						<tr>
							<td id='content-text'>Дурыс жауабын білмесең <i><b>"Жауабын білмеймін"</b></i> батырмасын бас 
								&nbsp;<span class='glyphicon glyphicon-arrow-right hidden-xs'></span><span class='glyphicon glyphicon-arrow-down hidden-lg hidden-md hidden-sm'></span>
							</td>
							<td id='content-action' class='hidden-xs'>
								<button id='submit_question' data-type='skip' class='btn btn-md btn-info pull-right' data-num='<?php echo $_SESSION['ees_id'];?>'>Жауабын білмеймін</button>
							</td>
						</tr>
						<tr class='hidden-lg hidden-md hidden-sm'>
							<td id='content-action'>
								<button id='submit_question' data-type='skip' class='btn btn-md btn-info pull-right' data-num='<?php echo $_SESSION['ees_id'];?>'>Жауабын білмеймін</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script type="text/javascript">

	$(document).ready(function() {
		$url_params = get_url_params('first_instruction');
		if ($url_params) {
			$('#instruction-modal').modal('show');
			clean_url();
		}
	});
	
	var get_url_params = function get_url_parameter(sParam) {
	    var sPageURL = window.location.search.substring(1),
	        sURLVariables = sPageURL.split('&'),
	        sParameterName,
	        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === sParam) {
	            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
	        }
	    }
	};

	var clean_url = function clean_url_parameter() {
		var uri = window.location.toString();
		if (uri.indexOf("?") > 0) {
		    var clean_uri = uri.substring(0, uri.indexOf("?"));
		    window.history.replaceState({}, document.title, clean_uri);
		}
	}
</script>