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
<section id='s-13'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center><h2>Оқушыны қабылдау реті</h2></center>
            </div>
            <div class='col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12'>
                <p>
                    <b>1.</b> Біздің орталыққа келердің алдында біздің байланыс орталығымызға хабарласыңыз. Біздің менеджер сізбен және балаңызбен кездесетін уақытты белгілеп, сізге мәлімдейді. Байланыс орталығымыздың номері: <b><u>8 777 7044551</u></b><br><br>
                    <b>2.</b> Кездесуге келген уақытта біздің менеджер сізбен және балаңызбен жақынырақ танысып, сізге «оқушы анкетасын» толтыруға береді. Оқушы анкетасында оқушы туралы мәліметтер көрсетіледі.<br><br>
                    <a href="docs/Анкета.pdf">Оқушы анкетасын осы жерден жүктеп</a>, алдын ала толтырып келсеңіз болады. Бұл сіздің уақытыңызды үнемдеуге септігін тигізеді.<br><br>
                    <b>3.</b> Содан кейін оқушыға пән бойынша білім деңгейін анықтау үшін «Кіріспе бақылау» бақылауы беріледі. Бақылау есептері 5-11 сынып арасындағы математика пәнінен өткен тақырыптарды  қамтиды. Бақылаудың қиындық деңгейі – «орташа».<br><br>
                    Осы бақылауға сәйкес оқушының білім деңгейі анықталып, өзінің деңгейіне сәйкес топ таңдалынады.<br><br>
                    <b>4.</b> Менеджер сабақ кестесін сізге мәлімдеп, сізге оқу жайлы қосымша ақпарат береді
                </p>
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
                    <input type="hidden" name="from" value='accept.php'>
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