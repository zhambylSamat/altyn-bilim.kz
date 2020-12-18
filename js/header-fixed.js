$(document).ready(function(){
	$width = $(document).width();
	if($width<=1286){
		$(".header-fixed").removeClass('header-fixed').addClass('header-fixed-mob');
		// $(".float-news-global").removeClass('float-news').addClass('float-news-mob');
	}
});
$(window).resize(function(){
	$width = $(document).width();
	if($width<=1286){
		$(".header-fixed").removeClass('header-fixed').addClass('header-fixed-mob');
		// $(".float-news-global").removeClass('float-news').addClass('float-news-mob');
	}
	else{
		$(".header-fixed-mob").removeClass('header-fixed-mob').addClass('header-fixed');
		// $(".float-news-global").removeClass('float-news-mob').addClass('float-news');
	}
});
$(document).scroll(function(){
	$height = $('.h-fix').height();
	$y = $(this).scrollTop();
    $yy = $('.header').height();
    $diff = $y-$yy;
    if($diff>0){
    	$('.h-fix').css({"position":"fixed","z-index":"100","top":"0","width":"100%"});
    	$('.empty').css({'height':$height+"px"});
    }
    else if($diff<=0){
    	$('.h-fix').css({"position":"relative","z-index":"0","width":"100%"});
    	$('.empty').css({'height':"0px"});
    }
});