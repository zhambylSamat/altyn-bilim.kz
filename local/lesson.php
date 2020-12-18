<?php
include('../connection.php');

if($_SESSION['student_num'] && isset($_GET[md5(md5('dataNum'))])){
	try {
		$stmt_permission = $conn->prepare("SELECT stp.video_permission vPermission, stp.test_permission tPermission, stp.done done FROM student_test_permission stp, student_permission sp WHERE stp.subtopic_num = :subtopic_num AND sp.student_permission_num = stp.student_permission_num AND sp.student_num = :student_num");

		$stmt_permission->bindParam(':subtopic_num', $_GET[md5(md5('dataNum'))], PDO::PARAM_STR);
		$stmt_permission->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
     	
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
		if($result_permission['vPermission']=='t'){
			$stmt_video = $conn->prepare("SELECT video_link, vimeo_link FROM video WHERE subtopic_num = :subtopic_num AND vimeo_link != 'n'");

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
<!-- Видеосабақ жіберілмеген, мұғалімнен видеосабақ жіберуін сұраңыз! -->
<?php } ?>
<div id='video'>
<?php foreach($result_video as $readrow_video){
	if($readrow_video['vimeo_link']=='y'){
?>
	<script>
		load_vimeo_video("<?php echo $readrow_video['video_link'];?>","",-1);
	</script>
<?php }else { ?>
<center>
	<video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
	    <source src="../video/video_lesson/<?php echo $readrow_video['video_link']?>" type='video/mp4'>
	    Техникалық жағдайға байланысты видео қосылмайды.
	</video>
    <h4><?php echo $readrow_video['video_link']?> <span></span></h4>
</center>
<hr>
<?php } ?>
<?php } ?>
<script>
	// load_vimeo_video('',"load", <?php echo $count_video;?>);
</script>
</div>
<?php
	if($result_permission['tPermission']=='t' && $result_permission['done']=='n'){
?>
<center><button class='btn btn-info btn-lg arena_section' data_name='startTest' data_num='<?php echo $_GET[md5(md5('dataNum'))];?>'>Начать тест</button></center>
<?php }else if($result_permission['done']=='y') {?>
<center><h2 class='text-success'>Тест сдан!</h2></center>
<?php } else {?>
<center><h4 class='text-danger'>Тест не доступен. Досмотрите видео и обратитесь к учителю.</h4></center>
<?php }?>
<script type="text/javascript">
$(document).ready(function(){
	// $('script[src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"]').remove();
    // $('<script>').attr('src', 'http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js').appendTo('body');
    // $('script[src="https://vjs.zencdn.net/6.1.0/video.js"]').remove();
    // $('<script>').attr('src', 'https://vjs.zencdn.net/6.1.0/video.js').appendTo('body');
});
</script>