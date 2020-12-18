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


<section id='gallery'>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 col-xs-12'>
				<center><h2 class='gallery-header'>Галерея</h2></center>
				<hr style='border:1px solid black;'>
			</div>
            <div id='all-gallery-img'>
    			
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
<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0.5); z-index: 100;'>
        <center>
            <img src="img/loader.gif" style='width: 10%; margin-top:25%;'>
        </center>
    </div>
<?php include_once('footer.php');?>
<?php include_once('lp_js.php');?>
<script type="text/javascript">
    <?php include('js/header-fixed.js'); ?>
</script>
<script type="text/javascript">
// ------------------------------------start_float_img------------------------------------------------
    $(window).load(function(){
        for ($i = 1; $i <= 30; $i++) {
            $("#all-gallery-img").append('<div class="col-md-3 col-sm-3 col-xs-6 gallery-img"><img src="lp_img/gallery-small/'+$i+'.jpg" class="img-responsive big-img" data-toggle="modal" data-target=".modal"></div>');
        }
        $("#lll").css('display','none');
    });
    $big_img_arr = [];
    $point = 0;
    $max_width = $(window).width();
    $(window).resize(function(){
        $max_width = $(window).width();
    });
    $(document).on('click','.big-img',function(){
        $first_img = $(this).attr('src');
        $big_img_arr = [];
        $point = 0;
        $(this).parents("#all-gallery-img").find("div").each(function(){ 
            if($max_width<=768){
                $big_img_arr.push($(this).find('img').attr('src'));
            }
            else{
                $big_img_arr.push($(this).find('img').attr('src').replace("small", "big").replace("jpg", "JPG"));
            }
            if($(this).find('img').attr('src') == $first_img) $point = $big_img_arr.length-1;
        });
        // console.log($big_img_arr);
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
// ------------------------------------end_float_img--------------------------------------------------
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