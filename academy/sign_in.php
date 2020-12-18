<?php
	if (!isset($get_sign_in) && !$get_sign_in) {
		header("Location:index.php");
	}
?>
<div class='container'>
	<div class='row'>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<br><br><br>
			<center>
				<iframe style='width:100%;' height="400" src="https://player.vimeo.com/video/398538806" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe>
				<!-- <div style='border: 1px solid lightgray; border-radius: 10px; padding: 20px 40px'>
					<center><h2>Бұл жерде видео болуы керек :)</h2></center>
				</div> -->
			</center>
		</div>
		<div class='col-md-6 col-sm-6 col-xs-12'>
			<div class='hidden-xs'><br><br><br></div><br><br><br>
			<center><h3 style="font-family: 'Times New Roman'; font-weight: 900; color:#555;">Altyn Bilim Онлайн академиясы</h3></center>
			<form onsubmit='return beforeSubmit();' class="form-horizontal" action='<?php echo $ab_root; ?>/academy/controller.php' method='post' autocomplete='off'>
			  	<div class="form-group">
			    	<label for="phone" class="col-sm-3 control-label">Телефон</label>
			    	<div class="col-sm-9">
			    		<div class='input-group'>
							<div class='input-group-addon'>+7</div>
			      			<input type="number" max='7999999999' min='7000000000' step='1' name='phone' class="form-control" id="phone" placeholder="Телефон нөмірін енгізіңіз" value='7*********' autofocus required>
			      		</div>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="password" class="col-sm-3 control-label">Құпия сөз</label>
			    	<div class="col-sm-9">
			      		<input type="password" class="form-control" name='password' id="password" placeholder="Құпия сөз" autocomplete='off' required>
			    	</div>
			  	</div>
			  	<div class='col-md-12 col-sm-12 col-xs-12'>
			  		<span style='color: #666; font-size: 13px;' class='pull-right'>Құпия сөзді ұмытып қалсаң менеджерге whatsapp-қа жаз: +7 777 389 0099</span>
			  	</div>
			  	<div class='form-group'>
			  		<div class='col-sm-12'>
			  			<center>
			  				<label class='control-label'>Құпия сөзді осы браузерде сақтау</label>
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
		</div>
	</div>
</div>