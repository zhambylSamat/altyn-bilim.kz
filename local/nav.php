<?php 
  // include_once("../new_year/new_year.php"); 
?>
<?php
  try {
    include('../connection.php');
    if(isset($_SESSION['access']) && $_SESSION['access']==md5('true')){
      $news_type = "student";
      $stmt = $conn->prepare("SELECT * FROM news WHERE type = :type");
      $stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
      $stmt->execute();
      $news_res = $stmt->fetch(PDO::FETCH_ASSOC);
      $date = date("Y-m-d",strtotime(date("Y-m-d")."-7 days"));
      if($news_res['publish']==1 && $news_res['last_updated_date']>$date && ((isset($news_res['header']) && $news_res['header']!='') || (isset($news_res['content']) && $news_res['content']!='') || (isset($news_res['img']) && $news_res['img']!=''))){
        $_SESSION['news_res_student'] = $news_res;
        $_SESSION['news_notificaiton_student2'] = 'true';
      }
      else {
        $_SESSION['news_notificaiton_student2'] = 'false';
      }

      $stmt = $conn->prepare("SELECT content FROM news WHERE type = :student_num AND readed = 0");
      $stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
      $stmt->execute();
      $news_res = $stmt->fetch(PDO::FETCH_ASSOC);
      $ccc = $stmt->rowCount();
      if($ccc==1){
        $_SESSION['news_res_self_student'] = $news_res;
        $_SESSION['news_notificaiton_self_student2'] = 'true';
      }
      else {
        $_SESSION['news_notificaiton_self_student2'] = 'false'; 
      }
    }
  } catch (PDOException $e) {
    echo "Error : ".$e->getMessage()." !!!";
  }
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
      <a class="navbar-brand" href="#">Altyn Bilim</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <!-- <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li> -->
        <?php 
          if(isset($_SESSION['access']) && $_SESSION['access']==md5('true')){
           if(!isset($_GET['user']) || $_GET['user']!=md5('std')){ 
        ?>
        <li><a href="../parent/student_info.php?data_num=<?php echo $_SESSION['student_num'];?>&user=<?php echo md5('std');?>" target="_blank"><button style='width: 150px;' class='btn btn-md btn-info'>Оқушының үлгерімі</button></a></li>
        <?php } ?>
        <li>
          <a data-toggle='modal' data-target='.box-schedule' data-type='schedule'><button class='btn btn-md btn-success'>Сабақ кестесі</button></a>
        </li>
        <?php if((isset($_SESSION['news_notificaiton_student2']) && $_SESSION['news_notificaiton_student2']=='true')){?><li><a><button class='btn btn-md btn-info news' data-toggle='modal' data-target='.box-news' data-type='student' style='width: 150px;'>Жалпы жаңалықтар</button></a></li><?php }?>
        <?php if(isset($_SESSION['news_notificaiton_self_student2']) && $_SESSION['news_notificaiton_self_student2']=='true'){?><li><a><button class='btn btn-md btn-info single-news' data-toggle='modal' data-target='.box-self-news' data-type='student' style='width: 150px;'>Жеке хабарлама</button></a></li><?php } } ?>
        <!-- <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li> -->
      </ul>
      <!-- <form class="navbar-form navbar-left">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
      <ul class="nav navbar-nav navbar-right">
        <li><a><?php echo $_SESSION['student_name']." ".$_SESSION['student_surname'];?>&nbsp;&nbsp;&nbsp;<span><button class='btn btn-default btn-sm'  onclick="location.href = '../logOut.php?local';">Выйти</button></span></a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<?php include_once('js.php');?>
<?php include_once('../ent_timer.php'); ?>
<div style='height: 20px; width: 100%;'></div>