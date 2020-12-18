<?php
	include_once('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'course_price';
	}
?>
<style type="text/css">
	/*#course-price input[type=checkbox], #course-price input[type=radio] {
	  -webkit-appearance: none;
	  -moz-appearance: none;
	}*/
	#course-price th, #course-price td {
		text-align: center;
	}
	#course-price input[type=radio], #course-price input[type=checkbox] {
		width: 20px;
		height: 20px;
	}
</style>
<section id='course-price'>
	<form id='calculate-course-price'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<table class='table table-bordered'>
					<tr>
						<th style='width: 30%;'>Пәннің аты</th>
						<th style='width: 10%;' colspan='2'>Аптасына сабақ саны</th>
						<th style='width: 20%;'>Төлемі айына тг.</th>
						<th style='width: 40%;'>Жалпы төлемі</th>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='1' class='subject' value='1'>
								<label for='1'>Мат. сауаттылық</label>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='11' class='intensive' value='11'>
								<label for='11'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[1]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[1]" id='ttt' value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='1'>-</td>
						<td id='total-result' rowspan='7' style='font-size: 25px;'></td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='2' class='subject' value='2'>
								<label for='2'>Қазақстан тарихы</>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='22' class='intensive' value='22'>
								<label for='22'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[2]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[2]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='2'>-</td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='3' class='subject' value='3'>
								<label for='3'>Математика</>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='33' class='intensive' value='33'>
								<label for='33'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[3]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[3]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='3'>-</td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='4' class='subject' value='4'>
								<label for='4'>Физика</>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='44' class='intensive' value='44'>
								<label for='44'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[4]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[4]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='4'>-</td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='5' class='subject' value='5'>
								<label for='5'>Химия</>								
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='55' class='intensive' value='55'>
								<label for='55'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[5]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[5]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='5'>-</td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='6' class='subject' value='6'>
								<label for='6'>География</>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='66' class='intensive' value='66'>
								<label for='66'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[6]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[6]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='6'>-</td>
					</tr>
					<tr>
						<td style='text-align: left !important; padding-left: 5%;'>
							<div class='col-md-9 col-sm-9 col-xs-12'>
								<input type="checkbox" id='7' class='subject' value='7'>
								<label for='7'>Биология</>
							</div>
							<div class='col-md-3 col-sm-3 col-xs-12 pull-right'>
								<input type="checkbox" id='77' class='intensive' value='77'>
								<label for='77'>И</label>
							</div>
						</td>
						<td>
							<label>
								<span>2 рет</span>
								<br>
								<input type="radio" class='2-lessons' name="price[7]" value='1:13500-3:12000-4:10500'>
							</label>
						</td>
						<td>
							<label>
								<span>3 рет</span>
								<br>
								<input type="radio" class='3-lessons' name="price[7]" value='1:18000-3:16200-4:10500+5250'>
							</label>
						</td>
						<td class='course-price' style='font-weight:bold; font-size: 20px;' data-val='7'>-</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</section>