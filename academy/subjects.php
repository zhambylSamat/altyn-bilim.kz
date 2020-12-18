<?php
	include_once('common/connection.php');
	// include_once('controller_functions.php');
	// $reserve_subjects = get_reserve_subjects();
?>
<style type="text/css">
	.subject-box-title {
		color: #444;
		border: 1px solid #4FA5BF;
		padding: 7px 0px;
		background-color: rgba(91,192,222, 0.2);
		border-radius: 10px;
		font-size: 20px;
	}
</style>
<div class='hidden-lg hidden-md hidden-sm'><br><br></div>
<div class='container'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<h3>Оқытатын пәндеріміз:</h3>
		</div>
		<div class='col-md-3 col-sm-3 col-xs-12 subject-box-btn' data-subject='16' data-toggle='modal' data-target='#reserve-topic' style='cursor:pointer;'>
			<center>
				<p class='subject-box-title'>Алгебра</p>
			</center>
		</div>
		<div class='col-md-3 col-sm-3 col-xs-12 subject-box-btn' data-subject='20' data-toggle='modal' data-target='#reserve-topic' style='cursor:pointer;'>
			<center>
				<p class='subject-box-title'>Мат сауаттылық</p>
			</center>
		</div>
		<div class='col-md-3 col-sm-3 col-xs-12 subject-box-btn' data-subject='21' data-toggle='modal' data-target='#reserve-topic' style='cursor:pointer;'>
			<center>
				<p class='subject-box-title'>Физика</p>
			</center>
		</div>
		<div class='col-md-3 col-sm-3 col-xs-12 subject-box-btn' data-subject='18' data-toggle='modal' data-target='#reserve-topic' style='cursor:pointer;'>
			<center>
				<p class='subject-box-title'>Геометрия</p>
			</center>
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
      		<div class="modal-footer">
		        <button type="button" class="btn btn-success select_topic_and_register_btn" data-dismiss="modal">Тіркелу</button>
  			</div>
    	</div>
	</div>
</div>