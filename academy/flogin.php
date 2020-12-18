<!DOCTYPE html>
<html>
<head>
    <?php include_once('common/assets/meta.php');?>
    <title>Login Force</title>
    <?php include_once('common/assets/style.php');?>
</head>
<body>
    <?php
        include_once('common/assets/js.php');
        include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
    ?>

    <div class='container'>
        <div class='row'>
            <div class='col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12'>
                <form onsubmit='return beforeSubmit();' style='margin-top: 15%;' class="form-horizontal" action='<?php echo $ab_root; ?>/academy/controller.php' method='post' autocomplete='off'>
                    <input type="hidden" name="force-login" value='1'>
                    <div class="form-group">
                      <label for="phone" class="col-sm-3 control-label">Телефон</label>
                      <div class="col-sm-9">
                        <div class='input-group'>
                        <div class='input-group-addon'>+7</div>
                            <input type="number" max='7999999999' min='7000000000' step='1' autocomplete="off" name='phone' class="form-control" id="phone" placeholder="Телефон нөмірін енгізіңіз" value='7*********' autofocus required>
                          </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="password" class="col-sm-3 control-label">Құпия сөз</label>
                      <div class="col-sm-9">
                          <input type="password" class="form-control" name='password' autocomplete="off" id="password" placeholder="Құпия сөз" autocomplete='off' required>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-offset-3 col-sm-9">
                          <button type="submit" name='signIn' class="btn btn-success">Кіру</button>
                      </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</body>
</html>
