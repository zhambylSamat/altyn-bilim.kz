<?php
	include('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		header('location:../admin/signin.php');
	}
	if(!isset($_SESSION['archive_load_page']) || !$_SESSION['archive_load_page']){
		$_SESSION['archive_load_page'] = true;
	}
	$pages = array('student','teacher','group');
	if(isset($_SESSION['archive_page'])){
		if(!in_array($_SESSION['archive_page'], $pages)){
			$_SESSION['archive_page'] = $pages[0];
		}
	}
	else{
		$_SESSION['archive_page'] = $pages[0];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Архив - Altyn Bilim</title>
	<?php include_once('style.php');?>
</head>
<body>
	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>

	<?php include_once('nav.php');?>
	<section id='body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-12 col-sm-12'>
					<ul class="nav nav-tabs">
						<li role="presentation" class="navigation <?php echo ($_SESSION['archive_page']==$pages[0]) ? "active" : "" ;?>" data='student'>
							<a href="#">Студент</a>
						</li>
						<li role="presentation" class="navigation <?php echo ($_SESSION['archive_page']==$pages[1]) ? "active" : "" ;?>" data='teacher'>
							<a href="#">Мұғалім</a>
						</li>
						<li role="presentation" class='navigation <?php echo ($_SESSION['archive_page']==$pages[2]) ? "active" : "" ;?>' data='group'>
							<a href="#">Группа</a>
						</li>
					</ul>
					<br>
					<div class='student box' style='<?php echo ($_SESSION['archive_page']==$pages[0]) ? "display: block;" : "display: none;"; ?>'>
						<input type="text" name="search" data-name='student' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
						<hr>
						<div class='content'>
							<?php include_once('archive_student.php');?>
						</div>
					</div>
					<div class='teacher box' style='<?php echo ($_SESSION['archive_page'])==$pages[1] ? "display: block;" : "display: none;" ?>'>
						<input type="text" name="search" data-name='teacher' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
						<hr>
						<div class='content'>
							<?php
								if($_SESSION['archive_page']==$pages[1]){
									include_once('archive_teacher.php');
								}
							?>
						</div>
					</div>
					<div class='group box' style='<?php echo ($_SESSION['archive_page'])==$pages[2] ? "display: block;" : "display: none;" ?>'>
						<input type="text" name="search" data-name='group' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
						<hr>
						<div class='content'>
							<?php
								if($_SESSION['archive_page']==$pages[2]){
									include_once('archive_group.php');
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>



	<div class="modal fade box-pop-up" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	    	<div class="modal-header">
	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
	    		<br>
	    		<center><h3 class="modal-title"></h3></center>
	    	</div>
	    	<div class="modal-body">
	    	</div> 
	    </div>
	  </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#lll").css('display','none');
		});
		$(function(){
			$('#lll').hide().ajaxStart( function() {
				$(this).css('display','block');  // show Loading Div
			} ).ajaxStop ( function(){
				$(this).css('display','none'); // hide loading div
			});
		});

		$(document).on('click','.navigation',function(){
			$attr = $(this).attr('data');
			$('.navigation').removeClass('active');
			$(this).addClass('active');
			$('.box').css('display',"none");
			$('.'+$attr).css('display','block');
			loadPage($attr, "");
		});

		$(document).on('keyup','#search',function(){
			$data_name = $(this).data('name');
			$val = $(this).val();
			$val = $val.replace(" ","_");
			loadPage($data_name, $val);
		});

		function loadPage(attr, val){
			console.log(attr);
			$("."+attr+" .content").html("<center><h1>Loading...</h1></center>");
			$('.'+attr+' .content').load('archive_'+attr+'.php?search='+val);
		}

		$(document).on('click','.from_archive',function(){
			$student_full_name = $(this).parents('tr').find('.object_full_name').text();
			if(confirm("Вы точно хотите Восстановить? ("+$student_full_name.trim()+")")){
				$data_num = $(this).data('num');
				$data_name = $(this).data('name');
				$this = $(this);
				$.ajax({
			    	url: "ajaxDb.php?<?php echo md5(md5('fromArchive'))?>&data_num="+$data_num+"&data_name="+$data_name,
			    	contentType: false,
		    	    cache: false,
					processData:false,
					beforeSend:function(){
						$('#lll').css('display','block');
					},
					success: function(dataS){
						$('#lll').css('display','none');
				    	// console.log(dataS);
				    	data = $.parseJSON(dataS);
				    	// console.log(data);
				    	if(data.success){
				    		$elem = $this.parents("tr");
				    		$elem.find('.count').text("-");
				    		$elem.nextAll(".head").each(function(){
				    			$(this).find('.count').text(parseInt($(this).find('.count').text().trim())-1);
				    		});
				    		$this.parents("tr").stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},250,function(){
				    			$elem.next().remove()
				    			$elem.remove();
				    		});
				    	} 
				    	else{
				    		console.log(data);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		console.log(dataS);
			    	} 	        
			   	});
			}
		});

		$(document).on('click','.more_info',function(){
			$data_num = $(this).attr('data_num');
			$data_name = $(this).attr('data-name');
			if($data_name == 'student'){
					$(this).parents('.head').next().html("<b>Loading...</b>");
					$(this).parents('.head').next().load("students_in_group.php?data_num="+$data_num);
			}
			$(this).parents('.head').next().toggle();
		});


		$(document).on('click','.student-modal',function(){
			$data_num = $(this).data('num');
			$('.box-pop-up .modal-title').html('<center><h1>Оқушы</h1></center>');
			$('.box-pop-up .modal-body').html('<center><h3>Loading...</h3></center>');
			$('.box-pop-up .modal-body').load('student_info.php?data_num='+$data_num);

		});
	</script>
</body>
<?php $_SESSION['archive_load_page'] = false; ?>
</html>