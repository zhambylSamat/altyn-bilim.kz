<?php 
	include('../connection.php');
	if(!$_SESSION['load_page']){
		$_SESSION['page'] = 'teacher';
	}
?>
<button class='btn btn-success btn-sm new-teacher' at='new-teacher' id='new-teacher-btn'>Мұғалім енгізу</button>
<button class='btn btn-info btn-sm comment-for-teacher' at='comment-for-teacher' data-toggle='modal' data-target='.box-comment-for-teacher' id='new-teachcer-btn'><span class='glyphicon glyphicon-th-list'></span></button>
<a class='btn btn-sm btn-default news' data-toggle='modal' data-target='.box-news' data-type='teacher'>Жаңалықтар (Мұғалім)</a>
<button class='btn btn-success btn-sm <?php echo $_SESSION['role']==md5('admin') ? 'btn-suggestion' : 'suggestion' ; ?>' data-toggle='modal' data-target='.box-suggestion'>Ұсыныс</button>
<div id='new-teacher'>
	<form class='form-inline' id='create-teacher' method='post'>
		<div class="form-group">
	    	<label for="id-teacher-surname">Тегі</label>
	    	<input type="text" name='surname' class="form-control" id="id-teacher-surname" placeholder="Тегі" required="">
	  	</div>
		<div class="form-group">
			<label for="id-teacher-name">Аты</label>
	    	<input type="text" name='name' class="form-control" id="id-teacher-name" placeholder="Аты" required="">
	  	</div>
	  	<div class='form-group'>
	  		<input type="text" name="username" class='form-control' title='"name.surname" и все буквы должны в нижнем регисте' placeholder='name.surname' required="" pattern='[a-z]+[0-9]*(\.[a-z]+)[0-9]*'>
	  	</div>
	  	<input type="submit" class='btn btn-info btn-sm' value='Жіберу'>
	  	<a class='btn close-add-new-teacher' title='Отмена'><span class='glyphicon glyphicon-remove text-danger' style="font-size: 18px;"></span></a>
	</form>
</div>
<input type="text" name="search" data-name='teacher' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
<hr>
<div class='teachers'>
	<?php include_once('index_teachers.php');?>
</div>