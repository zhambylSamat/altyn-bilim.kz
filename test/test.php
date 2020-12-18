<?php
include('../connection.php');
$test_data = array();
if(isset($_SESSION['ees_code']) && isset($_SESSION['ees_id'])){
	// unset($_SESSION['test_result']);
	if(!isset($_SESSION['test_result']) || $_SESSION['test_result'] == ""){
		if ($_SERVER['SERVER_NAME']=='old.altyn-bilim.kz') {
			$url = "https://".$_SERVER['SERVER_NAME']."/test/ajaxDb.php?".md5("create_test");
		} else if ($_SERVER['SERVER_NAME']=='localhost') {
			$url = "http://".$_SERVER['SERVER_NAME']."/altynbilim/test/ajaxDb.php?".md5("create_test");
		}
		// $url = 'https://altyn-bilim.kz/test/ajaxDb.php?'.md5("create_test");
		// $url = 'http://localhost/altynbilim/test/ajaxDb.php?'.md5("create_test");
		$params = array(
						"ees_id" => $_SESSION['ees_id']
					);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
		// This should be the default Content-type for POST requests
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded")); 
		$_SESSION['test_result'] = json_decode(curl_exec($ch), true); 
		// echo curl_exec($ch);
		if(curl_errno($ch) !== 0) { 
			error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch)); 
		} 
		curl_close($ch);
	}
	$result = $_SESSION['test_result'];
	// print_r($result);
} else {
	header('location:signin.php');
}

$prefixes = array('', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
?>

<div class='test-box'>
<?php
	if (!empty($result['content']) && !isset(end(end($result['content'])['content'])['result'])) {
		$qCount = 0;
		$test_name = "";
		foreach($result['content'] as $value) {
			$test_name = $value['test_name'];
			foreach($value['content'] as $value) {
				$qCount++;
			}
		}
		$value = end(end($result['content'])['content']);
?>
	<div class='box-test'>
		<div class='row'>
			<div class='col-md-12 col-sm-12 question'>
				<h3 class='question-title'>Сұрақ №<?php echo $qCount." | ".$test_name." (".count(end($result['content'])['content']).")"; ?>:</h3>
				<?php if($value['question_txt']!=''){ ?>
				<!-- <p class="text-danger" style='font-size:17px;'>Сұрақты білмесең қалдырып кет. Наугад белгілеме!</p> -->
				<div class='question_txt'>
					<?php echo nl2br($value['question_txt']);?>
				</div>
				<?php } ?>
				<?php if($value['question_img']!=''){ ?>
				<div class='question_img img-big'>
					<center><img src="../img/test/<?php echo $value['question_img'];?>"></center>
				</div>
				<?php } ?>
			</div>
			<div class='row' style='width: 100%;'>
				<div class='col-md-12 col-sm-12'>
					<!-- <h3>Ответы:</h3> -->
				</div>
				<?php 
					$aCount = 0;
					foreach($value['answer'] as $key => $value){
						if($aCount%3==0 && $aCount!=0){
							echo "</div><div class='row'>";
						} else if ($aCount==0){
							echo "<div class='row'>";
						}
						$aCount++;
						
				?>
				<div class='col-md-4 col-sm-4 answer'>
					<div class='row' for="<?php echo $aCount."checkbox"; ?>">
						<!-- <div class='col-md-11 col-sm-11'> -->
							<div class='col-md-12 col-sm-12 col-xs-3' style='display: inline-block;'>
								<label class='checkbox_container'>
									<input type="radio" class='answer-box' id='<?php echo $aCount."checkbox"; ?>' data-num = '<?php echo $value['answer_num'];?>' name="answer[]">
									<span class='checkbox_checkmark'></span>
									<span class='checkbox_checkmark_title'>
										<?php
											echo "&nbsp;&nbsp;".$prefixes[$aCount].")&nbsp;&nbsp;&nbsp;&nbsp;".$value['answer_txt'];
										?>
									</span>
								</label>
							</div>
							<?php if($value['answer_img']!=''){ ?>
							<div class='answer_img col-md-12 col-sm-12 col-xs-9 img-big'>
								<center>
									<img src="../img/test/<?php echo $value['answer_img'];?>">
								</center>
							</div>
						<!-- </div> -->
						<!-- <div class='col-md-1 col-sm-1'> -->
						<!-- </div> -->
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } else {
		header('location:index.php?ees_id='.$_SESSION['ees_id']);
	} ?>
</div>
<div class='submit-actions'>
	<!-- <button id='submit_question' data-type='skip' class='btn btn-lg btn-info pull-right submit-question hidden-lg hidden-md hidden-sm' data-num='<?php echo $_SESSION['ees_id'];?>'>Жауабын білмеймін</button> -->
	<button id='submit_question' data-type='submit' class='btn btn-lg btn-success pull-right submit-question' data-num='<?php echo $_SESSION['ees_id'];?>'>Келесі сұрақ</button>
	<!-- <button id='submit_question' data-type='skip' class='btn btn-lg btn-info pull-right submit-question hidden-xs' data-num='<?php echo $_SESSION['ees_id'];?>'>Жауабын білмеймін</button> -->
</div>

<div class='img-section'>
	<center>
		<div class='img-big-box'>
			<img src="" class='img-responsive'>
			<span class='glyphicon glyphicon-remove remove-img-section'></span>
		</div>
	</center>
</div>
<!-- <?php print_r($result['test_settings']); ?> -->
<br><br>
<!-- <?php print_r(end($result['content'])); ?> -->
<br><br>
<?php 
	// if(isset($result['tmp'])) print_r($result['tmp']);
?>
