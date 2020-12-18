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
      <a class="navbar-brand" href="index.php" style='color:#ddd;'>Басты бет</a>
    </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a><?php echo $_SESSION['tst_number'];?>&nbsp;&nbsp;&nbsp;<span><button class='btn btn-default btn-sm'  onclick="location.href = '../logOut.php?tst';">Выйти</button></span></a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>