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
<section id='s-10'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <img src="lp_img/altyn-bilim.png" class='img-responsive pull-right'>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <div id='header-title'>
                    <!-- <center> -->
                        <h1>"Altyn Bilim"</h1>
                        <h2>Оқу орталығы</h2>
                    <!-- </center> -->
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div id='s-10-body'>
                    <p>
                        &nbsp;&nbsp;&nbsp;&nbsp;<b><i>«Altyn Bilim»</i></b> оқу орталығы – 9-10 сынып мектеп оқушыларын <b>Математика</b>, <b>Физика</b> және <b>Химия</b> пәндерінен және мектеп бітіруші оқушыларды <b>Математика</b>, <b>Физика</b> және <b>Химия</b> пәндері бойынша <b>Ұлттық Бірыңғай Тестілеуге (ҰБТ)</b> сапалы дайындайтын оқу мекемесі. 
                    </p>
                    <p>
                        Орталық 2012 жылы құрылған және содан бері орталықта <b>700</b>-ден аса оқушы білім алған.
                    </p>
                    <p>
                        <b>Altyn Bilim-де оқыту 2 бағытта жүзеге асады:</b><br>
                        <span>•   ҰБТ-ға дайындық</span><br>
                        <span>•   Оқушыларды мектеп бағдарламасы бойынша дайындау</span><br>
                    </p>
                    <p>
                        <b>Біздің оқытушылар:</b><br>
                        <span>•   ҰБТ-дан жоғары балл иелері</span><br>
                        <span>•   Физика-математика мектептерінің үздік түлектері</span><br>
                        <span>•   Математика және Физика пәндері бойынша аудандық, қалалық және республикалық олимпиадалар жүлдегерлері.</span>

                    </p>
                    <p>
                        <b>Оқыту жүйеміз:</b><br>
                        Біздің орталықта оқушы тиімді және сапалы білім алуы үшін арнайы өзіміздің оқыту жүйесі жасалған. Әр оқу бағыты үшін арнайы оқу жоспарымыз құрастырылған. Сол жоспарға сәйкес қазіргі мектеп бағдарламасы және ҰБТ-ның жаңа форматына арналған есептерді жинау арқылы сынып және үй жұмыстары қарастырылған. 
                    </p>
                    <p>
                        <b>Біздің мақсат</b> – оқушыларға САПАЛЫ БІЛІМ беру арқылы жоғары оқу орындарына түсулеріне үлес қосу және білімге, оқуға деген құштарлықты арттыратын орта құру.
                    </p>
                    <p>
                        <b>Біздің құндылық</b> – біздің оқытушылар, оқу әдістемесі, ата-аналардың және оқушылардың бізге сенімі.
                    </p>
                    <p>
                        Біздің оқытушылардың тәжірибесі, арнайы дайындалған оқыту әдістемеміз және Сіздің оқуға деген құлшынысыңыз математика және физика пәндерінен сапалы білім алып, ҰБТ-ны сәтті тапсыруға жол ашады.
                    </p>
                    <center>
                        <p>
                            <u>Білім шыңына <i>БІЗБЕН БІРГЕ, ДОСТАРЫМ!</i></u>
                        </p>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <center><button class='contact'>Жазылу</button></center>
    <div style='padding:1%'></div>
</section>
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
                    <input type="hidden" name="from" value='about_altyn_bilim.php'>
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