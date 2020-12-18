<?php
	include_once('connection.php');
?>
<!DOCTYPE html>
<html lang='ru'>
<head>
	<?php include_once('meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('lp_style.php');?>
	<script type="text/javascript" src="assets/js/modernizr.custom.32033.js"></script>
    <script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full"></script>
    <script type="text/javascript">
        var map;

        DG.then(function () {
            map = DG.map('map', {
                center: [43.25611174779537,76.93221330642702],
                zoom: 16
            });

            DG.marker([43.25611174779537,76.93221330642702]).addTo(map).bindPopup('Сейфуллина 531, уг. ул. Қазыбек би. БЦ "Сарыарқа", 7 этаж, офис 704/4. Altyn Bilim.');
        });
    </script>
</head>
<body>
<?php include_once('header.php');?>
<?php include_once('header-fixed.php');?>
<div style='background-color: #24A0E6; width: 100%; padding:1% 0px;'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<p style='color:#fff; font-size: 130%; padding-top:1%;'> <b>«Altyn Bilim»</b> оқу орталығы – <b>Математика</b> және <b>Физика</b> пәндері бойынша мектеп бітіруші оқушыларды <b>Ұлттық Бірыңғай Тестілеуге (ҰБТ)</b> және 9 - 10 сынып оқушыларын осы пәндер бойынша мектеп бағдарламасына сапалы дайындайтын оқу мекемесі.</p>		
			</div>
		</div>
	</div>
</div>
<section id='s-1'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<h3>Оқу бағыттары</h3>
				<hr style='border:1px solid black;'>
			</div>
		</div>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<div class='s-1-section'>
						<a href="#">
							<div class='s-1-box'>
								<img src="lp_img/photo1.jpg">
							</div>
							<div class='s-1-txt'>
								<center>
									<p>Математика ҰБТ</p>
									<p>Жаңа формат</p>
								</center>
							</div>
						</a>
					</div>
					<div class='s-1-section'>
						<a href="#">	
							<div class='s-1-box'>
								<img src="lp_img/photo1.jpg">
							</div>
							<div class='s-1-txt'>
								<center>
									<p>Физика ҰБТ</p>
									<p>Жаңа формат</p>
								</center>
							</div>
						</a>
					</div>
					<div class='s-1-section'>
						<a href="#">	
							<div class='s-1-box'>
								<img src="lp_img/photo1.jpg">
							</div>
							<div class='s-1-txt'>
								<center>
									<p>Математикалық</p>
									<p>сауаттылық</p>
								</center>
							</div>
						</a>
					</div>
					<div class='s-1-section'>
						<a href="#">	
							<div class='s-1-box'>
								<img src="lp_img/photo3.jpg">
							</div>
							<div class='s-1-txt'>
								<center>
									<p>Математика</p>
									<p>9-10 сынып</p>
								</center>
							</div>
						</a>
					</div>
					<div class='s-1-section'>
						<a href="#">	
							<div class='s-1-box'>
								<img src="lp_img/photo4.png">
							</div>
							<div class='s-1-txt'>
								<center>
									<p>Физика</p>
									<p>9-10 сынып</p>
								</center>
							</div>
						</a>
					</div>
				</center>
			</div>
		</div>
	</div>
</section>
<?php 
	$data = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
?>
<?php
	$_SESSION['news_txt_1'] = "Достар, <b>Altyn Bilim</b>-де оқушыларды қабылдау басталды. Хабарласыңыздар, группада әлі орындар бар. Қабылдау күндері: <u>Дүйсенбі - Сенбі 14:00 - 19:00</u>";
	$_SESSION['news_txt_2'] = "<b>Достар, бізде керемет жаңалық!</b> <br>
Осы жылдың <b>ҚЫРКҮЙЕГІНДЕ</b> жаңа бағытта жұмыс істейтін топтар ашылғалы жатыр:<br>  
&nbsp;&nbsp;&nbsp;&nbsp;- Назарбаев зияткерлік мектебіне түсуге арналған математика/логика пәндеріне дайындайтын жаңа топ (<b>НИШ</b>)
<br>
&nbsp;&nbsp;&nbsp;&nbsp;- Математикалық сауаттылыққа (<b>ҰБТ</b>) дайындайтын жаңа топ.
<br><br>
Орындар шектеулі. ";
	$_SESSION['news_txt_3'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
	$_SESSION['news_txt_4'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
?>
<section id='s-2'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<h3>Соңғы жаңалықтар</h3>
				<hr style='border:1px solid black;'>
			</div>
		</div>
		<div class='row'>
			<div class='col-md-3 col-sm-4 col-xs-12'>
				<div class='s-2-section'>
					<div class='s-2-box'>
						<img src="news/n-1.JPG">
					</div>
					<div class='s-2-txt'>
						<p><?php echo substr($_SESSION['news_txt_1'],0,80);?><a href='news.php?data_name=news_txt_1&img=news/n-1.JPG' target='_blank' class='news-info' data-name = 'news_txt_1'>... <u>толығырақ</u></a></p>
					</div>
				</div>
			</div>
			<div class='col-md-3 col-sm-4 col-xs-12'>
				<div class='s-2-section'>
					<div class='s-2-box'>
						<img src="news/n-2.jpg">
					</div>
					<div class='s-2-txt'>
						<p><?php echo substr($_SESSION['news_txt_2'],0,80);?><a href='news.php?data_name=news_txt_2&img=news/n-2.jpg' target='_blank' class='news-info' data-name = 'news_txt_2'>... <u>толығырақ</u></a></p>
					</div>
				</div>
			</div>
			<div class='col-md-3 col-sm-4 col-xs-12'>
				<div class='s-2-section'>
					<div class='s-2-box'>
						<img src="lp_img/alt.png">
					</div>
					<div class='s-2-txt'>
						<p><?php echo substr($_SESSION['news_txt_3'],0,80);?><a href='news.php?data_name=news_txt_3&img=lp_img/alt.png' target='_blank' class='news-info' data-name = 'news_txt_3'>... <u>толығырақ</u></a></p>
					</div>
				</div>
			</div>
			<div class='col-md-3 col-sm-4 col-xs-12'>
				<div class='s-2-section'>
					<div class='s-2-box'>
						<img src="lp_img/alt.png">
					</div>
					<div class='s-2-txt'>
						<p><?php echo substr($_SESSION['news_txt_4'],0,80);?><a href='news.php?data_name=news_txt_4&img=lp_img/alt.png' target='_blank' class='news-info' data-name = 'news_txt_4'>... <u>толығырақ</u></a></p>
					</div>
				</div>
			</div>
		</div>
</section>
<!-- <div class='float-news-global float-news'>
	<div class='cover'></div>
	<div id='float-news-box'>
		<center>
			<div id='float-news-body'>
			</div>
			<div id='float-news-head'>
				<img src="">
				<span class='close2'>X</span>
			</div>
		</center>
	</div>
</div> -->
<section id='s-3'>
	<div class='container-fluid'>
		<div class='row'>
			<center>
				<div class='col-md-4 col-sm-4 col-xs-12 s-3-box'>
					<!-- <center> -->
						<p class='count' id='count-1'>0</p>
						<p>оқушы біздің орталықта оқудан өтті.</p>
					<!-- </center> -->
				</div>
			</center>
			<center>
				<div class='col-md-4 col-sm-4 col-xs-12 s-3-box'>
					<!-- <center> -->
						<p class='count' id='count-2'>0 %</p>
						<p>түлектеріміз мемлекеттік грант иелері атанды.</p>
					<!-- </center> -->
				</div>
			</center>
			<center>
				<div class='col-md-4 col-sm-4 col-xs-12 s-3-box'>
					<!-- <center> -->
						<p class='count' id='count-3'>0 жыл</p>
						<p><b>Altyn Bilim</b> оқу орталығы білім нарығында.</p>
					<!-- </center> -->
				</div>
			</center>
		</div>
	</div>
</section>
<section id='s-4'>
	<div>
		<center><h1><b>Оқу кезеңдеры</b></h1></center>
	</div>
	<img src="lp_img/v1.6.jpg" class='hidden-xs' style='width: 100%;'>
	<img src="lp_img/v2.2.jpg" class='hidden-lg hidden-md hidden-sm' style='width: 100%;'>
	<div>
		<center><button class='contact'>Жазылу</button></center>
	</div>
</section>
<section id='s-5'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<table class='table table-bordered table-hover' style='display: ;'>
					<tr>
						<th>Курстың атауы</th>
						<th>Аптадағы сабақ саны</th>
						<th><a href="#s-6">Қосымша қызметтер*</a></th>
						<th>Тіркелу</th>
					</tr>
					<tr>
						<th>Алгебра</th>
						<td>3</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'algebra'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Геометрия</th>
						<td>2</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'geometry'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика<br>(Алгебра + Геометрия)</th>
						<td>3</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Физика</th>
						<td>3</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'phys'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика + Физика</th>
						<td>6</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_phys'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика сауаттылық</th>
						<td>2</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_simple'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Индивидуалдық оқу</th>
						<td>Келісім бойынша*</td>
						<td><span class='glyphicon glyphicon-ok text-success'></span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'individual'>Курсқа қазір тіркелу</button></td>
					</tr>
				</table>
				<!-- <table class='table table-bordered table-hover' style='display:none;'>
					<tr>
						<th>Курстың атауы</th>
						<th>Аптадағы сабақ саны</th>
						<th>Бағасы</th>
						<th>Тіркелу</th>
					</tr>
					<tr>
						<th>Алгебра</th>
						<td>3</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'algebra'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Геометрия</th>
						<td>2</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'geometry'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика<br>(Алгебра + Геометрия)</th>
						<td>3</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика интенсив<br>(Алгебра + Геометрия)</th>
						<td>4</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_intensive'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Физика</th>
						<td>3</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'phys'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика + Физика</th>
						<td>6</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_phys'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика интенсив + Физика</th>
						<td>7</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_intensive_phys'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Математика сауаттылық</th>
						<td>2</td>
						<td>20 000 тг.</span></td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_simple'>Курсқа қазір тіркелу</button></td>
					</tr>
					<tr>
						<th>Индивидуалдық оқу</th>
						<td>Келісім бойынша*</td>
						<td>20 000 тг.</td>
						<td><button class='btn btn-sm btn-success contact contact-success' data-name = 'individual'>Курсқа қазір тіркелу</button></td>
					</tr>
				</table> -->
			</div>
		</div>
	</div>
</section>
<section id='s-6'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12 s-6-head'>
				<center><h1>Қосымша қызметтер*</h1></center>
			</div>
			<div class='col-md-6 col-sm-6 col-xs-12 s-6-body'>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Білім деңгейінің бастапқы және аралық диагностикасы</p>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Пән бойынша барлық қажетті теориялық материалдар мен оқу құралдары</p>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Айлық сынақ тесттері мен бақылаулары</p>
			</div>
			<div class='col-md-6 col-sm-6 col-xs-12 s-6-body'>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Психологиялық, мотивациялық және техникалық тренингтер мен семинарлар</p>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Оқушының даму мониторингі</p>
				<p><span class='glyphicon glyphicon-ok text-success'></span> Оқу курсын бітіргені жайлы сертификат</p>
			</div>
		</div>
	</div>
</section>
<section id='s-7'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<h1><center>Біздің артылықшықтар</center></h1>
			</div>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center>
					<table class='table table-hover'>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Оқушыларымыздың ҰБТ-дағы жоғары көрсеткіштері</td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Оқыту жүйесінің тиімділігі </td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Топта 7 адамнан артық емес</td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Оқытушылардың оқыту тәжірибесінің молдығы</td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Оқу бағасының орындылығы  </td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Жыл сайын оқу базасының жетілдірілуі</td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>Оқыту әдістемеміздің күшейіп отыруы: видеопроектор, видеосабақтар, сайт</td>
						</tr>
						<tr>
							<td><span class='glyphicon glyphicon-ok text-danger'></td>
							<td>«Altyn Bilim» оқу орталығымыздың ҰБТ саласындағы жұмысы – 5 жыл</td>
						</tr>
					</table>
				</center>
			</div>
		</div>
	</div>
</section>
<?php include_once('footer.php');?>

<section id='float-contact'>
	<div class='cover'></div>
	<center>
		<div id='contact-box'>
			<div id='contact-header'>
				ЖОО дейінгі дайындық <span class='pull-right close'>X</span>
			</div>
			<div id='contact-body'>
				<form action='send.php' method='post'>
					<input type="text" name="name" required="" placeholder="Атыңыз"><br>
					<input type="email" name="email" placeholder="Email поштаңыз"><br>
					<input type="text" name="phone" required="" placeholder="Телефон номеріңіз"><br>
					<select name='program' required="">
						<option value=''>Қызықтырған бағдарлама</option>
						<option value='algebra'>Алгебра</option>
						<option value='geometry'>Геометрия</option>
						<option value='math'>Математика (Алгебра + Геометрия)</option>
						<option value='math_intensive'>Метематика интенсив (Алгебра + Геометрия)</option>
						<option value='phys'>Физика</option>
						<option value='math_phys'>Математика + Физика</option>
						<option value='math_intensive_phys'>Метематика интенсив + Физика</option>
						<option value='math_simple'>Математикалық сауаттылық</option>
						<option value='individual'>Индивидуалдық оқу</option>
					</select><br>
					<input type="submit" name="submit" value='Жазылу'>
					<input type="hidden" name="from" value='index.php'>
				</form>
			</div>
		</div>
	</center>
</section>
<?php include_once('footer.php');?>
<?php include_once('lp_js.php');?>
<script type="text/javascript">
<?php include('js/header-fixed.js'); ?>
</script>
<script type="text/javascript">
$anim = true;
$(document).scroll(function(){
	$h = $(window).height();
	$y = $(this).scrollTop();
	$s_3 = $("#s-3").offset().top;
	if(($s_3-$y)<=(2*$h/3) && $anim){
		anim();
		$anim = false;
	}
	else if (($s_3-$y)>=$h){
		// anim();
		$anim = true;
	}
});
function anim(){
	var decimal_factor = 1;
	$('#count-1').animateNumber({
	    number: 700 * decimal_factor,
	    color: '#C7012E',
	    'font-size': '250%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number);
	    }
	},1500);

	decimal_factor = 1;
	$('#count-2').animateNumber({
	    number: 90 * decimal_factor,
	    color: '#C7012E',
	    'font-size': '250%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number+"%");
	    }
	},1500);

	decimal_factor = 1;
	$('#count-3').animateNumber({
	    number: 5 * decimal_factor,
	    color: '#C7012E',
	    'font-size': '250%',

	    numberStep: function(now, tween) {
	    	var floored_number = Math.floor(now) / decimal_factor,
	            target = $(tween.elem);

	        target.text(floored_number + " жыл");
	    }
	},1500);
}
$data_name = '';
$(document).on('click','.contact',function(){
	$data_name = $(this).attr('data-name');
	if (typeof $data_name !== typeof undefined && $data_name !== false) {
		$('#float-contact').find('option[value='+$data_name+']').attr("selected",'selected');
	}
	$('#float-contact').slideToggle();
});
$(document).on("click",'.cover, .close',function(){
	$('#float-contact').slideUp();
	if($data_name!=''){
		$('#float-contact').find('option[value='+$data_name+']').removeAttr("selected");
	}
});
$(document).ready(function(){
	$h = $('.iMap').prev().height();
	$w = $('.iMap').prev().width();
	// console.log($h+" px");
	$(".iMap").find('#map').css({"height":$h+"px","width":$w+"px"});
});
$(document).resize(function(){
	$h = $('.iMap').prev().height();
	$w = $('.iMap').prev().width();
	// console.log($h+" px");
	$(".iMap").find('#map').css({"height":$h+"px","width":$w+"px"});
});

$(document).on('click','.news-info',function(){
	$data_name = $(this).attr('data-name');
	$img = $(this).parents('.s-2-section').find('img').attr('src');
	$data = '';
	if($data_name == 'news_txt_1'){
		$data = '<?php echo str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), nl2br($news_txt_1));?>';
	}
	else if($data_name == 'news_txt_2'){
		$data = '<?php echo str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), nl2br($news_txt_2));?>';
	}
	else if($data_name == 'news_txt_3'){
		$data = '<?php echo str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), nl2br($news_txt_3));?>';
	}
	else if($data_name == 'news_txt_4'){
		$data = '<?php echo str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), nl2br($news_txt_4));?>';
	}
	console.log($data_name);
	console.log($data);
	console.log($img);
	$(".float-news-global").find('#float-news-body').html($data);
	$(".float-news-global").find('#float-news-head').find('img').attr('src',$img);
	$('.float-news-global').slideToggle();
});
$(document).on("click",'.cover, .close2',function(){
	// $("#float-news").find('#float-news-body').html("");
	// $("#float-news").find('#float-news-head').find('img').attr('src','');
	$('.float-news-global').slideUp();
});
</script> 
</body>
</html>