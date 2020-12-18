  <style type="text/css">
  @media (max-width: 421px) {
    #student-nav {
      padding-top: 0;
    }
    #student-contancts {
      padding-top: 0;
    }
    #student-nav-brand {
      padding-bottom: 0;
      height: 38px !important;
    }
  }
  #student-contancts {
    padding-bottom: 0;
  }
</style>

<nav class="navbar navbar-inverse">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button> -->
      <a class="navbar-brand" id='student-nav-brand' style='cursor: pointer;'>Altyn Bilim</a>
    </div>
    <div>
      <ul class="nav navbar-nav">
        <li>
          <a id='student-contancts'>
            Сұрақтар бойынша бізге хабарласыңыз: <br class='hidden-lg hidden-md hidden-sm'> <a style='padding-top:0; padding-bottom: 0; text-decoration: underline;' target="_blank" href="https://wa.me/77773890099?text=Сәлеметсіз бе. Мен ҰБТ онлайн академиясы бойынша хабарласып тұрмын.">+7 777 389 0099</a>
          </a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a id='student-nav' class='pull-right'><?php echo $_SESSION['first_name']." ".$_SESSION['last_name'];?>&nbsp;&nbsp;&nbsp;<span><button class='btn btn-default btn-sm'  onclick="location.href = '../log_out.php';">Выйти</button></span></a></li>
      </ul>
    </div>
  </div>
</nav>