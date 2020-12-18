<?php
	$LEVEL = 1;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');
    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);

    include_once($root.'/common/connection.php');
	include_once($root.'/student/register/controller_functions.php');

    $subjects = get_available_subjects();
	$reserve_subjects = get_reserve_subjects();
	if (!isset($_SESSION['extra_registration'])) {
		$_SESSION['extra_registration'] = Array('courses' => Array('err_display' => 'none',
															'value' => ''),
										'has_error' => false);
	}

	$display = Array('courses' 		=> $_SESSION['extra_registration']['courses']['err_display']);



?>

<style type="text/css">
  .go-to-instruction-page-on-register {
    margin-top: -0.5%;
  }

  #registration-title {
    font-size: 20px;
    padding-bottom: 0.7%;
  }
  .submit-register {
    margin-top: 1.5%;
  }

  @media (max-width: 768px) {
    .go-to-instruction-page-on-register {
      margin-top: -3% !important;
    }
    #registration-title {
      font-size: 18px;
    }
    #registartion-info-title {
      margin-bottom: 1.5% !important;
      margin-top: 4% !important;
    }
    #choose-topic-title {
      margin-bottom: 3%;
    }
    .submit-register {
      margin-top: 5%;
    }
    .registartion-info-subtitle {
      line-height: 19px;
    }
    .entrance-examination-btn {
      padding-top: 0;
    }

    #notification-box {
      margin-bottom: 5% !important;
      width: 95% !important;
    }
    #notification-title {
      font-size: 15px !important;
    }
  }

  #notification-box {
    border-radius: 10px;
    padding: 1% 1%;
    background-color: #FFDE59;
    margin-bottom: 2%;
    width: 70%;
  }
  #notification-title {
    font-size: 16px;
    color: #2A3279;
  }
</style>

<?php 
	if (isset($_SESSION['alert']['r_done']) && $_SESSION['alert']['r_done'] == true) {
?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Құттықтаймыз! Тіркелу сәтті аяқталды.</strong>
</div>
<?php } ?>
<?php 
	if (isset($_SESSION['alert']['r_error']) && $_SESSION['alert']['r_error'] == true) {
		$_SESSION['alert']['r_error'] = false;
?>
<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Пәнді және модульді таңдаңыз.</strong>
</div>
<?php } ?>
<!-- <button class='btn btn-sm btn-default pull-right go-to-instruction-page go-to-instruction-page-on-register'>Инструкция</button> -->
<center><p id='registration-title'>Курстарға тіркелу</p></center>
<?php
  // echo $_GET['recomendation_text'];
  if (isset($_GET['recomendation_text']) && $_GET['recomendation_text'] != '') {
    $recomendation_text = str_replace('_', ' ', $_GET['recomendation_text']);
    $html = "<center><div id='notification-box'>";
      $html .= "<span id='notification-title'>";
        $html .= 'Тест қорытындысы бойынша саған Алгебра пәнінен <br class="hidden-lg hidden-md hidden-sm"><b>"'.$recomendation_text.'"</b><br class="hidden-lg hidden-md hidden-sm"> тарауынан бастауыңа кеңес берілді. Төмендегі керек пәнді және модульді таңда';
      $html .= "</span>";
    $html .= "</div></center>";
    echo $html;
  }
?>
<form class="form-horizontal" action='register/controller.php' method='post' autocomplete='off'>
  	<div class='form_group'>
  		<label class='col-sm-3 col-xs-12 control-label'>
  			<p id='choose-topic-title'>Оқитын пәнді және<br class='hidden-xs'> модульді таңда:</p>
  		</label>
  		<div class='col-sm-6 col-xs-12'>
  			<?php
  				foreach ($reserve_subjects as $value) {
  			?>
  			<button type='button' style='font-size: 15px;' class='btn btn-default btn-xs btn-info btn-block reserve-topic' data-toggle='modal' data-subject='<?php echo $value['id']; ?>' data-target='#reserve-topic'><?php echo $value['title']; ?></button>
  			<?php } ?>
  			<div class='choosen-reserves hidden' style='border: 1px solid lightgray; border-radius: 5px; padding: 5px 10px; margin-top: 24px; box-shadow: 0px 0px 11px gray;'>
                <p style='color: green; font-weight: bold;'>Таңдаған пәнің мен тарауың:</p>
                <div class='reserve-content' style='font-size: 15px;'></div>
                <i><b style='color: #5cb85c;'>Қосымша тағы бірнеше пәнді таңдасаң болады</b></i>
              </div>
  			<input type="hidden" name="reserves">
  		</div>
  	</div>
  	<div class="form-group">
    	<div class="col-md-12 col-sm-12 col-xs-12">
      		<center><button type="submit" name='register_course' class="btn btn-md btn-success submit-register">Тіркелу</button></center>
    	</div>
  	</div>
    <div class='form-group'>
      <center>
        <div id='registartion-info'>
          <p id='registartion-info-title'>Ұсыныс!</p>
          <p class='registartion-info-subtitle'>Алгебра пәнінен дайындықты өзің білмейтін тараудан, ал <br> Физика және Геометрияны ең бірінші тараудан бастағаның дұрыс.</p>
          <p class='registartion-info-subtitle' style='margin: 0;'>Алгебрадан оқуды қай тақырыптан бастайтыныңды білмесең, төмендегі батырма арқылы тестті орындап шық. Тест соңында біз саған оқуды қай тараудан бастау керек екендігі бойынша ұсыныс береміз:</p>
        </div>
      </center>
    </div>
    <div class='form-group'>
      <center>
        <a type='button' class='btn btn-sm entrance-examination-btn' style='font-size: 15px; cursor: pointer; text-decoration: underline;'>Математикадан деңгейіңді <br class='hidden-lg hidden-md hidden-sm'> анықтайтын тест</a>
      </center>
    </div>
</form>
<div class='container-fluid'>
  <div class='row'>
    <div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12'>
      <div id='register-course-instruction-video'>
        
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="choose-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Группаны таңда | <span class='title'></span>
      		</div>
      		<div class="modal-body">
        		
      		</div>
    	</div>
		</div>
</div>

<div class="modal fade" id="choose-topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Тарауды таңда | <span class='title'></span></h4>
      		</div>
      		<div class="modal-body">
        		
      		</div>
    	</div>
		</div>
</div>

<div class="modal fade" id="choose-subtopic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Қай тақырыптан бастайтыныңды таңда | <span class='title'></span></h4>
      		</div>
      		<div class="modal-body">
        		
      		</div>
      		<!-- <div class="modal-footer">
		        <button type="button" class="btn btn-success" data-dismiss="modal">Сақтау</button>
          </div> -->
    	</div>
		</div>
</div>

<div class="modal fade" id="reserve-topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Тарауды таңдаңыз | <span class='title'></span></h4>
      		</div>
      		<div class="modal-body">
        		
      		</div>
      		<!-- <div class="modal-footer">
		        <button type="button" class="btn btn-success" data-dismiss="modal">Сақтау</button>
          </div> -->
    	</div>
		</div>
</div>


<!-- <div class='modal fade' id='do-payment-register' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
      </div>
      <div class='modal-body'>
        <h4 style='color: #666;'>Төлемі каспи банк картасы арқылы жүзеге асырылады</h4>
        <h2><b>5169 4931 2313 8979</b></h2>
        <i style='font-size: 16px;'>(Алмат Серікұлы М.)</i>
        <p style='color: #999;'>Төлем жасалған соң чекті менеджерге жіберіңіз</p>
        <center>
          <a class='btn btn-sm btn-success' href="https://wa.me/77773890099?text=Altyn%20Bilim%20онлайн%20академиясының%20төлемін%20төледім.%20Аты-жөнім:%20%20.%20Қатысатын%20пән(дер)ім:%20" target="_blank">WHATSAPP номер</a>
        </center>
      </div>
      </div>
  </div>
</div> -->

<script type="text/javascript">
  render_register_course_video_instruction();
</script>
