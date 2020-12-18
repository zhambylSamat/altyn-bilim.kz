<!DOCTYPE html>
<html lang='en'>
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
                center: [43.256955631108845,76.93257808685304],
                zoom: 16
            });

            DG.marker([43.256955631108845,76.93257808685304]).addTo(map).bindPopup('Сейфуллина 531, уг. ул. Қазыбек би. БЦ "Сарыарқа", 7 этаж, офис 704/4. Altyn Bilim.');
        });
    </script>
</head>
<body>
<?php include_once('header.php');?>
<?php include_once('header-fixed.php');?>

<div style='background-color: #007CC2; width: 100%; height: 30px;'></div>
<section id='s-11'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <p>
                    <b style='font-size: 130%;'><i>Altyn Bilim</i>-де оқыту 2 бағытта жүзеге асады:</b><br>
                    <span>•   Мектеп бітіруші оқушыларды <b>ҰБТ</b>-ға дайындау</span><br>
                    <span>•   9-10 сынып мектеп оқушыларын <b>мектеп бағдарламасы</b> бойынша дайындау</span><br>
                </p>
                <p>
                    &nbsp;&nbsp;&nbsp;&nbsp;Сіздерге ыңғайлы және тиімді болуы үшін біз осы екі бағытқа орай келесі оқу бағдарламаларын құрдық:
                </p>
                <table class='table table-bordered table-hover' style='display: ;'>
                    <tr>
                        <th>Курстың атауы</th>
                        <th>Аптадағы сабақ саны</th>
                        <th><a href="#s-12">Қосымша қызметтер*</a></th>
                        <th>Бағасы*</th>
                        <th>Тіркелу</th>
                    </tr>
                    <tr>
                        <th>Алгебра</th>
                        <td>3</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>22 500 (19 500 тг.)**</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'algebra'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Геометрия</th>
                        <td>2</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>15 000 (13 000 тг.)</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'geometry'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Математика<br>(Алгебра + Геометрия)</th>
                        <td>3</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>22 500 (19 500 тг.)</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Физика</th>
                        <td>3</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>22 500 (19 500 тг.)</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'phys'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Математика + Физика</th>
                        <td>6</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>42 000 (36 000 тг.)</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_phys'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Математика сауаттылық</th>
                        <td>2</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>15 000 тг.</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'math_simple'>Курсқа қазір тіркелу</button></td>
                    </tr>
                    <tr>
                        <th>Индивидуалдық оқу</th>
                        <td>Келісім бойынша*</td>
                        <td><span class='glyphicon glyphicon-ok text-success'></span></td>
                        <td>3000 тг/сағ.</td>
                        <td><button class='btn btn-sm btn-success contact contact-success' data-name = 'individual'>Курсқа қазір тіркелу</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</section>
<section id='s-12'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12 s-6-head'>
                <center><h1>*Қосымша қызметтерге не кіреді:</h1></center>
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
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <hr>
                <center><h4><b>*Оқу бағасы жылына бір рет өзгеруі мүмкін</b></h4></center>
                <center><h4><b>**11 сынып оқушыларына арналған баға жақша ішінде</b></h4></center>
            </div>
        </div>
    </div>
</section>
<center style='padding:1%'><button class='contact'>Жазылу</button></center>


<div style='background-color: #007CC2; width: 100%; height: 30px;'></div>
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
                    <input type="hidden" name="from" value='program.php'>
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
    });
    function anim(){
        var decimal_factor = 1;
        $('#count-1').animateNumber({
            number: 700 * decimal_factor,
            color: '#FFCC29',
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
            color: '#FFCC29',
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
            color: '#FFCC29',
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
    $(document).ready(function(){
        $h = $("#header-title").parent().prev().height()/3;
        $("#header-title").css({"margin-top":$h+"px"});
    });
</script>
</body>
</html>