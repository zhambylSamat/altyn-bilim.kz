<!DOCTYPE html>
<html>
<head>
	<title>Test 2</title>
	<style type="text/css">
		body {
  position: relative;
  z-index: 0;
  margin: 0;
  padding: 0 0 1em 0;
}

.b-page__content {
  min-height: 200px
}

.b-head-decor {
  display: none
}

.b-page_newyear .b-head-decor {
  position: absolute;
  top: 0;
  left: 0;
  display: block;
  height: 115px;
  width: 100%;
  overflow: hidden;
  background: url(http://pve.su/example/balls/b-head-decor_newyear.png) repeat-x 0 0
}

.b-page_newyear .b-head-decor__inner {
  position: absolute;
  top: 0;
  left: 0;
  height: 115px;
  display: block;
  width: 373px
}

.b-page_newyear .b-head-decor::before {
  content: '';
  display: block;
  position: absolute;
  top: -115px;
  left: 0;
  z-index: 3;
  height: 115px;
  display: block;
  width: 100%;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.75)
}

.b-page_newyear .b-head-decor__inner_n2 {
  left: 373px
}

.b-page_newyear .b-head-decor__inner_n3 {
  left: 746px
}

.b-page_newyear .b-head-decor__inner_n4 {
  left: 1119px
}

.b-page_newyear .b-head-decor__inner_n5 {
  left: 1492px
}

.b-page_newyear .b-head-decor__inner_n6 {
  left: 1865px
}

.b-page_newyear .b-head-decor__inner_n7 {
  left: 2238px
}

.b-ball {
  position: absolute
}

.b-ball_n1 {
  top: 0;
  left: 3px;
  width: 59px;
  height: 83px
}

.b-ball_n2 {
  top: -19px;
  left: 51px;
  width: 55px;
  height: 70px
}

.b-ball_n3 {
  top: 9px;
  left: 88px;
  width: 49px;
  height: 67px
}

.b-ball_n4 {
  top: 0;
  left: 133px;
  width: 57px;
  height: 102px
}

.b-ball_n5 {
  top: 0;
  left: 166px;
  width: 49px;
  height: 57px
}

.b-ball_n6 {
  top: 6px;
  left: 200px;
  width: 54px;
  height: 70px
}

.b-ball_n7 {
  top: 0;
  left: 240px;
  width: 56px;
  height: 67px
}

.b-ball_n8 {
  top: 0;
  left: 283px;
  width: 54px;
  height: 53px
}

.b-ball_n9 {
  top: 10px;
  left: 321px;
  width: 49px;
  height: 66px
}

.b-ball_n1 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n1.png) no-repeat
}

.b-ball_n2 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n2.png) no-repeat
}

.b-ball_n3 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n3.png) no-repeat
}

.b-ball_n4 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n4.png) no-repeat
}

.b-ball_n5 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n5.png) no-repeat
}

.b-ball_n6 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n6.png) no-repeat
}

.b-ball_n7 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n7.png) no-repeat
}

.b-ball_n8 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n8.png) no-repeat
}

.b-ball_n9 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_n9.png) no-repeat
}

.b-ball_i1 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i1.png) no-repeat
}

.b-ball_i2 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i2.png) no-repeat
}

.b-ball_i3 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i3.png) no-repeat
}

.b-ball_i4 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i4.png) no-repeat
}

.b-ball_i5 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i5.png) no-repeat
}

.b-ball_i6 .b-ball__i {
  background: url(http://pve.su/example/balls/b-ball_i6.png) no-repeat
}

.b-ball_i1 {
  top: 0;
  left: 0;
  width: 25px;
  height: 71px
}

.b-ball_i2 {
  top: 0;
  left: 25px;
  width: 61px;
  height: 27px
}

.b-ball_i3 {
  top: 0;
  left: 176px;
  width: 29px;
  height: 31px
}

.b-ball_i4 {
  top: 0;
  left: 205px;
  width: 50px;
  height: 51px
}

.b-ball_i5 {
  top: 0;
  left: 289px;
  width: 78px;
  height: 28px
}

.b-ball_i6 {
  top: 0;
  left: 367px;
  width: 6px;
  height: 69px
}

.b-ball__i {
  position: absolute;
  width: 100%;
  height: 100%;
  -webkit-transform-origin: 50% 0;
  -moz-transform-origin: 50% 0;
  -o-transform-origin: 50% 0;
  transform-origin: 50% 0;
  -webkit-transition: all .3s ease-in-out;
  -moz-transition: all .3s ease-in-out;
  -o-transition: all .3s ease-in-out;
  transition: all .3s ease-in-out;
  pointer-events: none
}

.b-ball_bounce .b-ball__right {
  position: absolute;
  top: 0;
  right: 0;
  left: 50%;
  bottom: 0;
  z-index: 9
}

.b-ball_bounce:hover .b-ball__right {
  display: none
}

.b-ball_bounce .b-ball__right:hover {
  left: 0;
  display: block!important
}

.b-ball_bounce.bounce>.b-ball__i {
  -webkit-transform: rotate(-9deg);
  -moz-transform: rotate(-9deg);
  -o-transform: rotate(-9deg);
  transform: rotate(-9deg)
}

.b-ball_bounce .b-ball__right.bounce+.b-ball__i {
  -webkit-transform: rotate(9deg);
  -moz-transform: rotate(9deg);
  -o-transform: rotate(9deg);
  transform: rotate(9deg)
}

.b-ball_bounce.bounce1>.b-ball__i {
  -webkit-transform: rotate(6deg);
  -moz-transform: rotate(6deg);
  -o-transform: rotate(6deg);
  transform: rotate(6deg)
}

.b-ball_bounce .b-ball__right.bounce1+.b-ball__i {
  -webkit-transform: rotate(-6deg);
  -moz-transform: rotate(-6deg);
  -o-transform: rotate(-6deg);
  transform: rotate(-6deg)
}

.b-ball_bounce.bounce2>.b-ball__i {
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  transform: rotate(-3deg)
}

.b-ball_bounce .b-ball__right.bounce2+.b-ball__i {
  -webkit-transform: rotate(3deg);
  -moz-transform: rotate(3deg);
  -o-transform: rotate(3deg);
  transform: rotate(3deg)
}

.b-ball_bounce.bounce3>.b-ball__i {
  -webkit-transform: rotate(1.5deg);
  -moz-transform: rotate(1.5deg);
  -o-transform: rotate(1.5deg);
  transform: rotate(1.5deg)
}

.b-ball_bounce .b-ball__right.bounce3+.b-ball__i {
  -webkit-transform: rotate(-1.5deg);
  -moz-transform: rotate(-1.5deg);
  -o-transform: rotate(-1.5deg);
  transform: rotate(-1.5deg)
}
	</style>
</head>
<body>

	<div class="b-page_newyear">
  <div class="b-page__content">
    <!-- новогодняя мотня newyear.html -->

    <i class="b-head-decor">
        <i class="b-head-decor__inner b-head-decor__inner_n1">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>

    <i class="b-head-decor__inner b-head-decor__inner_n2">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    <i class="b-head-decor__inner b-head-decor__inner_n3">
 
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    <i class="b-head-decor__inner b-head-decor__inner_n4">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    <i class="b-head-decor__inner b-head-decor__inner_n5">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    <i class="b-head-decor__inner b-head-decor__inner_n6">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    <i class="b-head-decor__inner b-head-decor__inner_n7">
            <div class="b-ball b-ball_n1 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n2 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n3 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n4 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_n5 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n6 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n7 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n8 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_n9 b-ball_bounce"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
 
            <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
            <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
    </i>

  </div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="swfobject.min.js"></script>
<script type="text/javascript" src="newyear.js"></script>   
<script type="text/javascript">
	$(function() {
  var d = function() {};
  $(document).delegate(".b-ball_bounce", "mouseenter", function() {
    b(this);
    m(this)
  }).delegate(".b-ball_bounce .b-ball__right", "mouseenter", function(i) {
    i.stopPropagation();
    b(this);
    m(this)
  });

  function f() {
    var i = "ny2012.swf";
    i = i + "?nc=" + (new Date().getTime());
    swfobject.embedSWF(i, "z-audio__player", "1", "1", "9.0.0", null, {}, {
      allowScriptAccess: "always",
      hasPriority: "true"
    })
  }

  function h(i) {
    if ($.browser.msie) {
      return window[i]
    } else {
      return document[i]
    }
  }
  window.flashInited = function() {
    d = function(j) {
      try {
        h("z-audio__player").playSound(j)
      } catch (i) {}
    }
  };
  if (window.swfobject) {
    window.setTimeout(function() {
      $("body").append('<div class="g-invisible"><div id="z-audio__player"></div></div>');
      f()
    }, 100)
  }
  var l = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "=", "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "[", "]", "a", "s", "d", "f", "g", "h", "j", "k", "l", ";", "'", "\\"];
  var k = ["z", "x", "c", "v", "b", "n", "m", ",", ".", "/"];
  var g = 36;
  var a = {};
  for (var e = 0, c = l.length; e < c; e++) {
    a[l[e].charCodeAt(0)] = e
  }
  for (var e = 0, c = k.length; e < c; e++) {
    a[k[e].charCodeAt(0)] = e
  }
  $(document).keypress(function(j) {
    var i = $(j.target);
    if (!i.is("input") && j.which in a) {
      d(a[j.which])
    }
  });

  function b(n) {
    if (n.className.indexOf("b-ball__right") > -1) {
      n = n.parentNode
    }
    var i = /b-ball_n(\d+)/.exec(n.className);
    var j = /b-head-decor__inner_n(\d+)/.exec(n.parentNode.className);
    if (i && j) {
      i = parseInt(i[1], 10) - 1;
      j = parseInt(j[1], 10) - 1;
      d((i + j * 9) % g)
    }
  }

  function m(j) {
    var i = $(j);
    if (j.className.indexOf(" bounce") > -1) {
      return
    }
    i.addClass("bounce");

    function n() {
      i.removeClass("bounce").addClass("bounce1");

      function o() {
        i.removeClass("bounce1").addClass("bounce2");

        function p() {
          i.removeClass("bounce2").addClass("bounce3");

          function q() {
            i.removeClass("bounce3")
          }
          setTimeout(q, 300)
        }
        setTimeout(p, 300)
      }
      setTimeout(o, 300)
    }
    setTimeout(n, 300)
  }
});
</script>
</body>
</html>