<?php
	include_once('../connection.php');
	$links = array();
	try {
		$vimeo_link = 'y';
		$stmt = $conn->prepare("SELECT video_num, video_link FROM video WHERE subtopic_num = :subtopic_num AND vimeo_link = :vimeo_link");
		$stmt->bindParam(':subtopic_num', $_SESSION['elementNum'], PDO::PARAM_STR);
		$stmt->bindParam(':vimeo_link', $vimeo_link, PDO::PARAM_STR);
		$stmt->execute();
		$vimeo_result = $stmt->fetchAll();
		foreach ($vimeo_result as $value) {
			$links[$value['video_num']] = $value['video_link'];
		}
	} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12' id='add-link'>
		<center><h2><b>Vimeo Link</b></h2></center>
		<form class='form-inline' id='vimeo-video-form'>
			<div class='form-group'>
				<input type="text" class='form-control' name="vimeo_link" placeholder="Add vimeo video link">
			</div>
			<input type="hidden" name="subtopic_num" value='<?php echo $_SESSION['elementNum'];?>'>
			<div class='form-group'>
				<input type="submit" value='Save' class='btn btn-success btn-sm'>
			</div>
		</form>
		<br>
	</div>
</div>
<script type="text/javascript">
	$links = <?php echo json_encode($links);?>;
	for($key in $links){
		$.ajax({
	    	url: "https://vimeo.com/api/oembed.json?url="+$links[$key],
			type: "GET",
			beforeSend:function(){
				$('#lll').css('display','block');
			},
			success: function(data){
		    	$('#lll').css('display','none');
		    	$('#add-link').after("<div class='vimeo_video'><center>"+data.html+"<form id='remove-link'><input type='hidden' name='video_num' value='"+$key+"'><input type='submit' class='btn btn-xs btn-danger' value='Удалить'></form></center></div><hr>");
		    },
		  	error: function(dataS) 
	    	{
	    		console.log(dataS);
	    	} 	     
	   	});
	}
</script>