<?php
	include('../connection.php');
	if(isset($_GET[md5('elementNum')]) || isset($_SESSION['elementNum'])){
		$elementNum = isset($_GET[md5('elementNum')]) ? $_GET[md5('elementNum')] : $_SESSION['elementNum'];
		$_SESSION['elementNum'] = $elementNum;
	}
	else{
		header('location:index.php');
	}
?>
<button id='refresh'>Refresh</button>
<div id='vimeo-content'>
	<?php include_once('ajax_vimeo_video.php');?>
</div>










<hr><hr><hr>


<div>
	<?php
		$result_count = '';
		try {
			$stmt = $conn->prepare("SELECT * FROM video WHERE subtopic_num = :subtopic_num AND vimeo_link = ''  ORDER BY updated_date ASC");
		    $stmt->bindParam(':subtopic_num', $elementNum, PDO::PARAM_STR);

		    $stmt->execute();
		    $result_question = $stmt->fetchAll(); 
		    $result_count = $stmt->rowCount();
		} catch(PDOException $e) {
	        echo "Error: " . $e->getMessage();
	    }
	?>
	<div class='row'>
		<div class='col-md-12 col-sm-12'>
	<?php

		if($result_count){
			foreach($result_question as $readrow){

	?>
			<center>
				<video id="my-video" class="video-js" controls controlsList="nodownload" preload="auto" width="700" height="300" style='width:none; height: none;' data-setup="{}">
				    <source src="../video/video_lesson/<?php echo $readrow['video_link']?>" type='video/mp4'>
				    Техникалық жағдайға байланысты видео қосылмайды.
				  </video>
				  <!-- <video width="700" height="300" controls controlsList="nodownload">
				  	<source src="../video/video_lesson/<?php echo $readrow['video_link']?>" type="video/mp4">
					Your browser does not support the video tag.
					</video> -->
                <h4><?php echo $readrow['video_link']?> <span><button data_num = "<?php echo $readrow['video_num'];?>" data_name = '<?php echo $readrow['video_link'];?>' class='btn btn-danger btn-xs remove_video'>Удалить</button></span></h4>
            </center>
            <hr>
	<?php }} else {?>
	<h4><center>Видео сабақ(тар) жүктелмеген</center></h4>
	<?php } ?>
			</div>
	</div>
	<script type="text/javascript">
		function _(el){
			return document.getElementById(el);
		}

		function uploadVideo(){
			var video_name = _('video-name').value;
			var video_link = _('video-link').files[0];
			var formdata = new FormData();
			formdata.append('name', video_name);
			formdata.append('video',video_link);
			formdata.append('<?php echo md5(md5('addNewVideo'));?>','<?php echo md5(md5('addNewVideo'));?>');
			formdata.append('<?php echo md5('elementNum');?>','<?php echo $elementNum;?>');
			var ajax = new XMLHttpRequest();
			ajax.upload.addEventListener("progress", progressHandler, false);
			ajax.addEventListener("load", completeHandler, false);
			ajax.addEventListener("error", errorHandler, false);
			ajax.addEventListener("abort", abortHandler, false);
			ajax.open("POST", "uploadVideo.php");
			ajax.send(formdata);
		}

		function progressHandler(event){
			$('#lll').css('display','block');
			var percent = (event.loaded/event.total)*100;
			_('progressBar').value = Math.round(percent);
			_('progressStatus').innerHTML = percent+"%";
		}
		function completeHandler(event){
			$('#lll').css('display','none');
			var dataS = event.target.responseText;
			console.log(dataS);
			var data = JSON.parse(dataS);
			console.log(data);
			if(data.success){
				// console.log('asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdf'+data.text);
				document.getElementById('main-content').innerHTML = data.text;
			}
			else{
				alert("Устраните ошибки ниже: \n"+data.error);
			}
			_('progressBar').value = 0;
			_('progressStatus').innerHTML = "0%";
		}
		function errorHandler(event){
			$('#lll').css('display','none');
			_("status").innerHTML = "Ошибка! Повторите пожалуйста";
		}
		function abortHandler(event){
			$('#lll').css('display','none');
			_("status").innerHTML = "Ошибка! Повторите пожалуйста";
		}
	</script>
	<script type="text/javascript">
$(document).ready(function(){
	$('script[src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"]').remove();
    $('<script>').attr('src', 'https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js').appendTo('body');
    $('script[src="https://vjs.zencdn.net/6.1.0/video.js"]').remove();
    $('<script>').attr('src', 'https://vjs.zencdn.net/6.1.0/video.js').appendTo('body');
});
</script>
	<center>
		<h3>Добавить новое видео</h3>
		<form id='upload_video' enctype="multipart/form-data" method="post">
			<div class='form-group'>
				<label for='video_name'>Название видео</label>
				<input type="text" name="video-name" id='video-name' placeholder="Видеоның аты">
			</div>
			<div class='form-group'>
				<input type="file" id='video-link' name="video-link" style='display:block;'>
				<p class='help-block'>Видео должно быть в формате ".mp4".</p>
			</div>
			<div class='form-group'>
				<progress id='progressBar' value="0" max="100" style='width: 300px;'></progress>
				<span id='progressStatus'>0%</span>
			</div>
			<input type='button' onclick='uploadVideo()' class='btn btn-default btn-sm' value='Отправить'>
		</form>
	</center>
</div>
