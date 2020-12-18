<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'calculator';
	}
?>
<section id='calculator-price'>
	<form id='calculate-price'>
		<div class='row'>
			<div class='col-md-4 col-sm-4 col-xs-12' style='border: 1px solid lightgray; border-radius: 5px;'>
				<div class='row' style='padding:5px;'>
					<label for='price' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Жалпы суммасы</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<select id='price' class='form-control' required=''>
							<option vaule=''>Таңдау жүргіз</option>
							<option value='10500'>10500тг.</option>
							<option value='12000'>12000тг.</option>
							<option value='13500'>13500тг.</option>
							<option value='16200'>16200 тг.</option>
							<option value='18000'>18000 тг.</option>
							<option value='19500'>19500 тг.</option>
							<option value='21000'>21000 тг.</option>
							<option value='22500'>22500 тг.</option>
							<option value='32400'>32400 тг.</option>
							<option value='39000'>39000 тг.</option>
							<option value='-1'>Басқа</option>
						</select>
					</div>
				</div>
				<br>
				<div class='row' id='price-extra' style='padding: 5px; display: none;'>
					<div class='col-md-6 col-md-offsett-6 col-sm-6 col-sm-offset-6 col-xs-12'>
						<input type="number" min='0' step='1' class='form-control' value='0'>
					</div>
				</div>
				<br>
				<div class='row' style='padding:5px;'>
					<label for='discount' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Скидка</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<select id='discount' class='form-control'>
							<option vaule='0'>0%</option>
							<option value='5'>5%</option>
							<option value='10'>10%</option>
							<option value='15'>15%</option>
							<option value='20'>20%</option>
							<option value='25'>25%</option>
							<option value='30'>30%</option>
							<option value='35'>35%</option>
						</select>
					</div>
				</div>
				<hr>
				<div class='row' style='padding:5px;'>
					<label for='start-date' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Оқудың басталған уақыты</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<input type="text" class='form-control start-date' autocomplete="off" placeholder="кк.аа.жжжж" id='start-date' required="">
					</div>
				</div>
				<br>
				<div class='row' style='padding:5px;'>
					<label for='end-date' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Оқудың аяақталған уақыты</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<input type="text" disabled class='form-control end-date' autocomplete="off" placeholder="кк.аа.жжжж" id='end-date' required="">
					</div>
				</div>
				<hr>
			</div>
			<div class='col-md-4 col-sm-4 col-xs-12' style='border: 1px solid lightgray; border-radius: 5px;'>
				<div class='row' style='padding:5px;'>
					<label for='lesson-days' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Сабақ күндері</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<select id='lesson-days' class='form-control' required=''>
							<option vaule="">Таңдау жүргіз</option>
							<option value='1-3-5'>пн. ср. пт.</option>
							<option value='2-4-6'>вт. чт. сб.</option>
							<option value='1-2-3-4-5-6'>пн. вт. ср. чт. пт. сб.</option>
							<option value='1-3'>пн. ср.</option>
							<option value='3-5'>ср. пт.</option>
							<option value='1-5'>пн. пт.</option>
							<option value='2-4'>вт. чт.</option>
							<option value='4-6'>чт. сб.</option>
							<option value='2-6'>вт. сб.</option>
						</select>
					</div>
				</div>
				<div class='row' style='padding:5px;'>
					<label for='lesson-cancel' class='col-md-6 col-sm-6 col-xs-12' style='text-align: right;'>Сабақ болмаған күндер саны</label>
					<div class='col-md-6 col-sm-6 col-xs-12'>
						<select id='lesson-cancel' class='form-control'>
							<option vaule='0'>0</option>
							<option value='1'>1</option>
							<option value='2'>2</option>
							<option value='3'>3</option>
							<option value='4'>4</option>
							<option value='5'>5</option>
							<option value='6'>6</option>
							<option value='7'>7</option>
							<option value='8'>8</option>
							<option value='9'>9</option>
							<option value='10'>10</option>
							<option value='11'>11</option>
							<option value='12'>12</option>
							<option value='13'>13</option>
							<option value='14'>14</option>
							<option value='15'>15</option>
							<option value='16'>16</option>
							<option value='17'>17</option>
							<option value='18'>18</option>
							<option value='19'>19</option>
							<option value='20'>20</option>
							<option value='21'>21</option>
							<option value='22'>22</option>
							<option value='23'>23</option>
							<option value='24'>24</option>
						</select>
					</div>
				</div>
			</div>
			<div class='col-md-4 col-sm-4 col-xs-12' style='padding:5px;'>
				<div class='row'>
					<center>
						<input type='submit' class='btn btn-success btn-xs' id='answer-btn' value='Есептеу'>
						<b id='errors'>
							<ol id='errors-content'>
								
							</ol>
						</b>
					</center>
				</div>
			</div>
			<hr>
			<div class='col-md-4 col-sm-4 col-xs-12' id='result' style='display: none;'>
				<p><span>Бір айдағы бағасы:</span> <b id='price-result'></b></p>
				<p><span>Жеңілдік:</span> <b id='discount-result'></b></p>
				<!-- <p><span>Бір аптадағы сабақ саны:</span> <b id='lessons-in-week-result'></b></p> -->
				<!-- <p><span>Интенсив:</span> <b id='intensive-result'></b></p> -->
				<p><span>Толық сабақ саны: </span><b id='total-days-result'></b></p>
				<p><span>Сабақ болмаған кундер саны: </span><b id='cancelled-days-result'></b></p>
				<p><span>Оқушының қатысқан сабақтар саны: </span><b id='taked-days-result'></b></p>
				<br>
				<br>
				<b id='result-calculator'></b>
				<br>
				<br>
				<b>Оқушының төлеу керек төлемі:</b>
				<b id='result-total' style='font-size:30px;'></b>
			</div>
		</div>
	</form>
</section>