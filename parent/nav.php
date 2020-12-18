<?php 
  // include_once("../new_year/new_year.php"); 
?>
<nav class="navbar navbar-inverse">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php" style='color:#ddd;'>'Altyn bilim' оқу орталығы.</a>
    </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a><?php echo $_SESSION['parent_name']." ".$_SESSION['parent_surname'];?>&nbsp;&nbsp;&nbsp;<span><button class='btn btn-default btn-sm'  onclick="location.href = '../logOut.php?parent';">Выйти</button></span></a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<?php include_once('js.php');?>
<?php include_once('../ent_timer.php'); ?>
<div style='height: 20px; width: 100%;'></div>
