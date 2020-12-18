<?php
	if (!isset($get_sign_in) && !$get_sign_in) {
		header("Location:index.php");
	}
?>
<form onsubmit='return beforeSubmit();' class="form-horizontal" action='<?php echo $ab_root; ?>/academy/controller.php' method='post' autocomplete='off'>
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
  	<!-- <div class='col-md-12 col-sm-12 col-xs-12'>
  		<span style='color: #666; font-size: 13px;' class='pull-right'>*Құпия сөзді ұмытып қалсаң менеджерге whatsapp-қа жаз: <a target='_blank' href='https://wa.me/77773890099?text=Сәлеметсіз бе. Құпия сөзді ұмытып қалдым, жеке кабинетке кіре алмай жатырмын.'>+7 777 389 0099</a></span>
  	</div> -->
    <div class='form-group'>
      <div class='col-md-12 col-sm-12 col-xs-12'>
        <a type='button' class='pull-right' id='reset-password-btn' style='cursor: pointer;'>Құпия сөзді ұмытып қалдың ба?</a>
      </div>
    </div>
  	<div class='form-group'>
  		<div class='col-sm-12'>
  			<center>
  				<label class=''>Құпия сөзді осы браузерде сақтау</label>
  				<input type="checkbox" id='save-password'>
  			</center>
  		</div>
  	</div>
  	<div class="form-group">
    	<div class="col-sm-offset-3 col-sm-9">
      		<button type="submit" name='signIn' class="btn btn-success">Кіру</button>
      		<a href='registration.php' class='btn btn-info'>Тіркелу</a>
    	</div>
  	</div>
</form>