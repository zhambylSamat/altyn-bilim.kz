<?php 
  // include_once('../new_year/new_year.php');
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
      <a class="navbar-brand" href="index.php">Altyn Bilim | ADMIN</a>
    </div>
    <div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="../archive/index.php" target="_blank">АРХИВ</a></li>
        <li><a><?php echo $_SESSION['adminName']." ".$_SESSION['adminSurname'];?>&nbsp;&nbsp;&nbsp;<span><button class='btn btn-default btn-sm'  onclick="location.href = '../logOut.php?admin';">Выйти</button></span></a></li>
      </ul>
    </div>
  </div>
</nav>
<?php
  include_once('js.php');
?>