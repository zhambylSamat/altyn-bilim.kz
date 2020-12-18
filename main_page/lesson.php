<?php
include('connection.php');
if($_SESSION['student_num'] && isset($_GET[md5(md5('dataNum'))])){
	try {
		$stmt_permission = $conn->prepare("SELECT * FROM student_test_permission WHERE subtopic_num = :subtopic_num");

		$stmt_permission->bindParam(':subtopic_num', $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);
     	
	    $stmt_permission->execute();
	    $result_permission = $stmt_permission->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo "Error ".$e->getMessge()." !!!";
	}
}
else{
	header("location:index.php");
}
?>
<?php 
	try {
		if($result_permission['video_permission']=='t'){
			$stmt_video = $conn->prepare("SELECT * FROM video WHERE subtopic_num = :subtopic_num");

			$stmt_video->bindParam(':subtopic_num', $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);
	     	
		    $stmt_video->execute();
		    $result_video = $stmt_video->fetchAll(); 
		    $count_video = $stmt_video->rowCount();
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessge()." !!!";
	}
?>
<?php if($count_video==0){?>
<center><h4 class='text-danger'>Видео не добавлено!</h4></center>
<?php } ?>
<?php foreach($result_video as $readrow_video){?>
<center>
	<video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
	    <source src="video/video_lesson/<?php echo $readrow_video['video_link']?>" type='video/mp4'>
	    Техникалық жағдайға байланысты видео қосылмайды.
	</video>
    <h4><?php echo $readrow_video['video_link']?> <span></span></h4>
</center>
<hr>
<?php } ?>
<?php
	if($result_permission['test_permission']=='t'){
?>
<center><button class='btn btn-info btn-lg arena_section' data_name='startTest' data_num='<?php echo $_GET[md5(md5('dataNum'))];?>'>Начать тест</button></center>
<?php } else {?>
<center><h4 class='text-danger'>Тест не доступен. Досмотрите видео и обратитесь к учителю.</h4></center>
<?php }?>
<script type="text/javascript">
$(document).ready(function(){
	$('script[src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"]').remove();
    $('<script>').attr('src', 'http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js').appendTo('body');
    $('script[src="http://vjs.zencdn.net/6.1.0/video.js"]').remove();
    $('<script>').attr('src', 'http://vjs.zencdn.net/6.1.0/video.js').appendTo('body');
});
</script>