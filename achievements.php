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

<section id='acievements-global'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h2>ҰБТ-дағы біздің оқушыларымыздың көрсеткіштері</h2>
                    <h2 style='background-color:#2196F3; color:white; padding:1% 2%; border-radius: 3px;'>2018 жыл</h2>
                </center>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2018_мат_1.jpg" alt="2018 математика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2018_мат_2.jpg" alt="2018 математика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2018_физ_1.jpg" alt="2017 физика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2018_физ_2.jpg" alt="2017 физика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2018_матсау.jpg" alt="2018 математикалық сауаттылық">
                </a>
            </div>


            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h2 style='background-color:#2196F3; color:white; padding:1% 2%; border-radius: 3px;'>2017 жыл</h2>
                </center>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2017_мат.jpg" alt="2017 математика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2017_физ.jpg" alt="2017 физика">
                </a>
            </div>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h2 style='background-color:#2196F3; color:white; padding:1% 2%; border-radius: 3px;'>2016 жыл</h    >
                </center>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2016_мат.jpg" alt="2016 математика">
                </a>
            </div>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <a class="thumbnail">
                    <img src="lp_img/achievements/2016_физ.jpg" alt="2016 физика">
                </a>
            </div>
        </div>
    </div>
</section>

<?php
    $img_phys = array('1.Арын_Ерсултан-физ', '2.Меңлібек_Ырысалды-физ', '3.Сабыр_Рамазан-физ', '4.Аман_Айгерим-физ', '5.Дайырбеков_Нурлыбек-физ', '6.Кайыпберген_Айжан-физ', '7.Мейрамбекова_Инкар-физ', '8.Нурболкызы_Актоты-физ', '9.Баяхан_Касиет-физ', '10.Ерболған_Нұргүл-физ', '11.Жумагали_Темирболат-физ', '12.Садықова_Мадина-физ');
    $img_math = array('1.Жакупбек_Асель-мат','2.Жақсыбекова_Мөлдір-мат','3.Медеубаева_Айжан-мат','4.Мейманкулова_Гулбану-мат');
?>
<section id='float-img-phys'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h3 style='background-color:#2196F3; color:white; padding:1% 2%; border-radius: 3px;'>Физика</h3>
                </center>
            </div>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <div class='arrow arrow-left' id='arrow-left-1'>
                        <span class='glyphicon glyphicon-chevron-left'></span>
                    </div>
                    <div id='slider-1' class='slider'>
                        <ul>
                        <?php
                            for ($i=0; $i < count($img_phys); $i++) {
                        ?>
                        <li class='box big-img' data-toggle="modal" data-target=".modal" data-name='phys'>
                            <img src="lp_img/achievements/phys/<?php echo $img_phys[$i];?>.jpg">
                        </li>
                        <?php } ?>
                        </ul>
                    </div>
                    <div class='arrow arrow-right' id='arrow-right-1'>
                        <span class='glyphicon glyphicon-chevron-right'></span>
                    </div>
                </center>
            </div>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <h3 style='background-color:#2196F3; color:white; padding:1% 2%; border-radius: 3px;'>Математика</h3>
                </center>
            </div>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <center>
                    <div class='arrow arrow-left' id='arrow-left-2'>
                        <span class='glyphicon glyphicon-chevron-left'></span>
                    </div>
                    <div id='slider-2' class='slider'>
                        <ul>
                        <?php
                            for ($i=0; $i < count($img_math); $i++) {
                        ?>
                        <li class='box big-img' data-toggle="modal" data-target=".modal" data-name='math'>
                            <img src="lp_img/achievements/math/<?php echo $img_math[$i];?>.jpg">
                        </li>
                        <?php } ?>
                        </ul>
                    </div>
                    <div class='arrow arrow-right' id='arrow-right-2'>
                        <span class='glyphicon glyphicon-chevron-right'></span>
                    </div>
                </center>
            </div>
        </div>
    </div>
</section>

<!-- ---------------------------start-float-img--------------------------------- -->
<a href="#main">
    <div class='scrolTop' id='sctp'>
    </div>
</a>
<div class="modal fade works" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
      <div class="modal-header" style='border-bottom:none;'>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style='font-size:30px; opacity:1;'><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class='row'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <img class='img-responsive' src=''>
            </div>
        </div>
      </div>
  </div><!-- /.modal-dialog -->
  <button type='button' class='mfp-arraw mfp-arraw-left'><center><img src="lp_img/left.png" class='img-responsive'></center></button>
  <button type=button class='mfp-arraw mfp-arraw-right'><center><img src="lp_img/right.png" class='img-responsive'></center></button>
</div><!-- /.modal -->

<!-- ---------------------------end-float-img----------------------------------- -->

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

    // -------------------------------start-float-img-jscript--------------------------------
    var slideWidth, slideHiehgt;
    $(window).load(function(){
        var slideCount = $('#slider-1 ul li').length;
        slideWidth = $('#slider-1 ul li').width();
        slideHeight = $('#slider-1 ul li').height();
        var sliderUlWidth = slideCount * slideWidth;
        var hHeight = (slideHeight/2)-$('.arrow').find('span').height();
        $('#slider-1, #slider-2').css({ width: "90%", height: slideHeight });
        $('#arrow-left-1, #arrow-right-1, #arrow-left-2, #arrow-right-2').css({"padding":hHeight+"px 1%"});
        $('#slider-1 ul, #slider-2 ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
    });
    function moveLeft(element) {
        $(element+' ul').animate({
            left: + slideWidth
        }, 200, function () {
            $(element+' ul li:last-child').prependTo(element+' ul');
            $(element+' ul').css('left', '');
        });
    };

    function moveRight(element) {
        $(element+' ul').animate({
            left: - slideWidth
        }, 200, function () {
            $(element+' ul li:first-child').appendTo(element+' ul');
            $(element+' ul').css('left', '');
        });
    };
                // ------------------------------slider-1-----------------------------------
    $('#slider-1 ul li:last-child').prependTo('#slider-1 ul');
    $('#arrow-left-1').click(function () { moveLeft("#slider-1"); });
    $('#arrow-right-1').click(function () { moveRight("#slider-1"); });
                // ----------------------------------slider-2--------------------------------------
    $('#slider-2 ul li:last-child').prependTo('#slider-2 ul');
    $('#arrow-left-2').click(function () { moveLeft("#slider-2"); });
    $('#arrow-right-2').click(function () { moveRight("#slider-2"); });


    $big_img_arr = [];
    $point = 0;
    $(document).on('click','.big-img',function(){
        $first_img = $(this).find('img').attr('src');
        $big_img_arr = [];
        $point = 0;
        $(this).parent().find("li").each(function(){ 
            $big_img_arr.push($(this).find('img').attr('src'));
            if($(this).find('img').attr('src') == $first_img) $point = $big_img_arr.length-1;
        });
        $(".modal .modal-dialog .modal-body img").attr("src",$big_img_arr[$point]);
    });
    
    $(document).on('click','.mfp-arraw-left',function(){
        $point = (--$point<0) ? $big_img_arr.length-1 : $point;
        $(".modal .modal-dialog .modal-body img").attr("src",$big_img_arr[$point]);
    });
    $(document).on('click','.mfp-arraw-right',function(){
        $point = (++$point==$big_img_arr.length) ? 0 : $point;
        $(".modal .modal-dialog .modal-body img").attr("src",$big_img_arr[$point]);
    });

    $(document).keydown(function(e){
        if (e.keyCode == 37 ){
            $point = (--$point<0) ? $big_img_arr.length-1 : $point;
            $(".modal .modal-dialog .modal-body img").attr("src",$big_img_arr[$point]);
        }
    });
    $(document).keydown(function(e){
        if (e.keyCode == 39 ){
            $point = (++$point==$big_img_arr.length) ? 0 : $point;
            $(".modal .modal-dialog .modal-body img").attr("src",$big_img_arr[$point]);
        }
    });
    // -------------------------------end-float-img-jscript----------------------------------


    $anim = true;
    $(document).scroll(function(){
        $h = $(window).height();
        $y = $(this).scrollTop();
        $s_3 = $("#s-3").offsetTop;
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