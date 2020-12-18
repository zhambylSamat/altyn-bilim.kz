<?php
	include('../connection.php');
	$news_type = $_GET['data_type'];
	try {
		$stmt = $conn->prepare("SELECT * FROM news WHERE type = :type");
		$stmt->bindParam(':type', $news_type, PDO::PARAM_STR);
		$stmt->execute();
		$news_res = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo "Error : ".$e->getMessage()." !!!";
	}
?>
<form onsubmit='return newsValidation();' class='form-horizontal' action='admin_controller.php' method='post'  enctype="multipart/form-data">
	<!-- <form id='news-form' method='post'> -->
	<div class='form-group'>
		<label for='header' class='col-md-2 col-sm-2 col-xs-6 control-label'>Тақырып:</label>
		<div class='col-md-10 col-sm-10 col-xs-6'>
			<input type="text" class='form-control' name="news_header" placeholder="Жаңалықтың тақырыбы" value='<?php echo $news_res['header'];?>'>
		</div>
	</div>
	<div class='form-group'>
		<label for='context' class='col-md-12 col-sm-12 col-xs-12'>Мәтін:</label>
		<div class='col-md-12 col-sm-12 col-xs-2'>
			<textarea style='font-family: "Times New Roman", Times, serif; font-size:20px; line-height: 20px; padding:30px; resize:none;' class="form-control" name='news_context' id='context' rows="10" cols='5' wrap='hard' placeholder="Жаңалықтың мәтіні"><?php echo $news_res['content'];?></textarea>
		</div>
	</div>
	<div class='form-group'>
		<input type="hidden" name="uploaded_photo" value='<?php echo $news_res['img'];?>'>
		<label class='col-md-2 col-sm-2 col-xs-3 control-label'>Суреттің аты:</label>
		<div class='col-md-4 col-sm-4 col-xs-3'>
			<b>
				<?php 
					echo ($news_res['img']!='') ? $news_res['img']."&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn btn-xs btn-danger' id='remove_img'><b><span class='glyphicon glyphicon-trash'></span></b></a>" : "Сурет енгізілмеген!";
				?>		
			</b>
		</div>
		<label for='news_img' class='col-md-2 col-sm-2 col-xs-3 control-label'>Сурет:</label>
		<div class='col-md-4 col-sm-4 col-xs-3'>
			<input type='file' id='news_img' name="news_img" style='display: block !important;'>
		</div>
	</div>
	<div class='form-group'>
		<center>
			<input type="hidden" name="news_type" value='<?php echo $news_type;?>'>
			<input type="submit" class='btn btn-sm btn-success' name="submit_news" value='Сақтау'>
		</center>
	</div>
</form>
<hr>
<div class='row news-label'>
	<?php
		$date = date("Y-m-d",strtotime(date("Y-m-d")."-7 days"));
		if($news_res['last_updated_date']<=$date){
			echo "<h4><b class='text-danger'>&nbsp;&nbsp;&nbsp;&nbsp;Жаңалық ескірген</b></h4>";
		}
	?>
	<?php if(isset($news_res['header']) && $news_res['header']!=''){?>
	<div class="col-md-12 col-sm-12 col-xs-12 header">
		<center>
			<div class='news-header' style='background-color: #AFDFF7; padding:1% 0 1% 0;'>
				<h3><b><?php echo $news_res['header'];?></b></h3>
			</div>
		</center>
	</div>
	<?php }?>
	<?php if(isset($news_res['content']) && $news_res['content']!=''){?>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<div class='news-content'>
			<pre style='font-size:20px; line-height: 10px; padding:30px; margin-top:10px; background-color: rgba(0,0,0,0); font-family: "Times New Roman", Times, serif; background-color: #FEFAD7;'><?php echo nl2br($news_res['content']);?></pre>
		</div>
	</div>
	<?php }?>
	<?php if(isset($news_res['img']) && $news_res['img']!=''){?>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<center>
			<img src="../news_img/<?php echo $news_res['img'];?>" alt="<?php echo $news_type;?>-image" class="img-thumbnail img-responsive">
		</center>
	</div>
	<?php } ?>
	<?php if((isset($news_res['header']) && $news_res['header']!='') || (isset($news_res['content']) && $news_res['content']!='') || (isset($news_res['img']) && $news_res['img']!='')){?>
	<div class='col-md-12 col-sm-12 col-xs-12 btns'>
		<center>
			<form action='admin_controller.php' method='post'>
				<input type="hidden" name="news_type" value='<?php echo $news_res['type'];?>'>
				<input type="submit" class='btn btn-sm btn-success' name="news_publish" value='Жариялау' style='display:<?php echo ($news_res['publish']==0 && $news_res['last_updated_date']>$date) ? 'block;' : 'none;';?>'>
				<input type="submit" class='btn btn-sm btn-warning' name="news_unpublish" value='Жарияламау' style='display:<?php echo ($news_res['publish']==1 && $news_res['last_updated_date']>$date) ? 'block;' : 'none;';?>'>
				<input type="submit" class='btn btn-sm btn-success' name="news_publish" value='Қайта жариялау' style='display:<?php echo ($news_res['last_updated_date']<=$date) ? 'block;' : 'none;';?>'>
			</form>
		</center>
	</div>
	<?php }?>
</div>
