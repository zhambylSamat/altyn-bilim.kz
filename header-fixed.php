
<nav class="navbar navbar-default h-fix header-fixed hidden-xs" style='position:sticky; top:0px; z-index:100;'>
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed hidden-xs" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php"><img alt="Brand" src="landing/img/logo.jpg"></a>
      <!-- <center><a class="navbar-brand" href="#">ҰБТ-ға дайындайтын үздік оқу орталығы</a> -->
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse hidden-xs" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="index.php" class='brand-2' style='margin-top:0; padding-top:0;'><h3><b>Altyn Bilim</b></h3></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="index.php">Басты бет</a></li>
        <li><a href="about_altyn_bilim.php">Altyn bilim жайлы</a></li>
        <!-- <li><a href="program.php">Оқу бағдарламалары</a></li> -->
        <li><a href="accept.php">Оқушыны қабылдау</a></li>
        <li><a href="achievements.php">Жетістіктер</a></li>
        <li><a href="review.php">Біз туралы пікірлер</a></li>
        <li><a href="gallery.php">Галерея</a></li>
        <li><a href="contacts.php">Байланыс</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class='empty'></div>

<?php
  if(isset($_SESSION['n']) && $_SESSION['n']=='true'){
?>
<section id='alert' style='position:absolute; top:3%; z-index: 100; width: 80%; left:10%;'>
  <?php
    if(isset($_GET['send']) && $_GET['send']=='true'){
  ?>
  <div class="alert alert-info alert-dismissible" role="alert" style='margin:0; box-shadow: 0px 0px 50px black;'>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <center><strong>Спасибо! Сообщение успешно отправлено!</strong> Менеджер обязательно вам позвонит.</center>
  </div>
  <?php
    } else if(isset($_GET['send']) && $_GET['send']=='false'){
  ?>
  <div class="alert alert-warning alert-dismissible" role="alert" style='margin:0; box-shadow: 0px 0px 50px black;'>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <center><strong>Сообщение не отправлено! Попробуйте еще раз.</strong></center>
  </div>
  <?php } ?>
  <?php $_SESSION['n'] = "false"; ?>
  <!-- <div class="alert alert-success alert-dismissible" role="alert" style='margin:0; box-shadow: 0px 0px 50px black;'>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Warning!</strong> Better check yourself, you're not looking too good.
  </div> -->
</section>
<?php } ?>