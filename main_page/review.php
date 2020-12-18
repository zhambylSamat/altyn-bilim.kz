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

<section id='review'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h2 style='border-top:2px solid black; border-bottom:2px solid black; display: inline-block; padding:1% 0;'><b>Біз туралы пікірлер</b></h2>
                </center>
            </div>
            <div class='col-md-4 col-sm-4 col-xs-12' style='border-top:1px solid lightgray;'>
                <center>
                    <img src="lp_img/review/1.jpg" alt="Jasulan Satylkhanov" class="img-circle" style='width: 75%;'>
                    <h4><b style='color:#333;'>Жасұлан Сатылханов</b></h4>
                    <hr style='border:1px solid red;'>
                    <p><b>"Алтын былым"</b> очень понравился! Если хотите 23-25 баллов по математике и физики , всем советую сюда, это 100 раз лучше чем <u>Достык</u>, <u>Этэк</u> и т.д.</p>
                </center>
            </div>
            <div class='col-md-4 col-sm-4 col-xs-12' style='border-top:1px solid lightgray;'>
                <center>
                    <img src="lp_img/review/2.jpg" alt="Jasulan Satylkhanov" class="img-circle" style='width: 75%;'>
                    <h4><b style='color:#333;'>Бағлан Исақ</b></h4>
                    <hr style='border:1px solid red;'>
                    <p><b>Алтын білім</b> шынында да білім ордасы екеніне көзім жетті, ұстаздары өте білімді және әр балаға түсіндірудің өз жолын таба біледі. <b>Алтын білім</b> ұйымының ұстаздарына айтар алғысым шексіз, сіздердің арқаларыңызда өзім қиналған физика пәнін де түсіндім. Сіздерге өмірдегі бар жақсылықты тілеймін, ешқашан өкінбеймін <b>Алтын білімге</b> келгеніме, інілерімді де өз балаларымды да сіздерге жіберемін Құдай қаласа, жай рахмет айту сіздердің сіңірген еңбектеріңізге мүлдем аз,сіздерді өмір бойы ұмытпаймын! <b><u>Рахмет!!!</u></b></p>
                </center>
            </div>
            <div class='col-md-4 col-sm-4 col-xs-12' style='border-top:1px solid lightgray;'>
                <center>
                    <img src="lp_img/review/3.jpg" alt="Jasulan Satylkhanov" class="img-circle" style='width: 75%;'>
                    <h4><b style='color:#333;'>Айнұр Пірманова</b></h4>
                    <hr style='border:1px solid red;'>
                    <p><b>"АЛТЫН БIЛIМ"</b> керемет орталық!!! Келiңiздер, көрiңiздер өкiнбейсiздер!!! Алматыда бұдан артық үйрететін, бұдан мықты бiлiм беретiн орталық еш жерде таппайсыздар!!! Алтын бiлiмнiң басқа орталықтардан артықшылығы топта 6 адамнан аспайды!!! Бұл дегенiмiз әр балаға жеке көңіл бөлiнедi деген сөз!!! <b>"АЛТЫН БIЛIМДЕ"</b> ең маңызды сапа, балаға 100% бiлiм беруге, түсiндiруге тырысады!!! Мұғалімдері өте бiлiмдi жане тәжiрибелi!!! <b>"АЛТЫН БIЛIМГЕ"</b> келіңіздер, өкінбейсіздер!!!</p>
                </center>
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
    $(document).scroll(function(){
        $h = $(window).height();
        $y = $(this).scrollTop();
        $s_3 = $("#s-3").offsetTop;
        if(($s_3-$y)<=(2*$h/3) && $anim){
            anim();
            $anim = false;
        }
    });
    
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