<?php
	include('../connection.php');
	if(!isset($_SESSION['adminNum'])){
		header('location:../ab_admin/signin.php');
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
						<?php 
							try {
								
								$stmt = $conn->prepare("SELECT d
														FROM
														 (
														    SELECT id, 
														     	(CASE
														         	WHEN DATE_FORMAT(block_date, '%Y-%m-%d') 
														         		BETWEEN DATE_FORMAT(CONCAT(YEAR(DATE_ADD(block_date, INTERVAL -1 YEAR)), '-07-16'), '%Y-%m-%d') 
														         			AND DATE_FORMAT(CONCAT(YEAR(block_date), '-07-15'),'%Y-%m-%d') THEN YEAR(block_date)
														         	WHEN DATE_FORMAT(block_date, '%Y-%m-%d') 
														         		BETWEEN  DATE_FORMAT(CONCAT(YEAR(block_date), '-07-16'),'%Y-%m-%d')
														         			AND DATE_FORMAT(CONCAT(YEAR(DATE_ADD(block_date, INTERVAL +1 YEAR)), '-07-15'), '%Y-%m-%d') THEN YEAR(DATE_ADD(block_date, INTERVAL +1 YEAR))
														         	ELSE YEAR(DATE_ADD(block_date, INTERVAL -1 YEAR))
														     	END) AS d
														    FROM student
														 ) AS tmp
														GROUP BY d
														ORDER BY d DESC");
								$stmt->execute();
								$year_session = $stmt->fetchAll();
							} catch (PDOException $e) {
								throw $e;
							}
						?>
						<div id='filtering' class='row'>
							<div class='col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-3 col-xs-12'>
								<form method='post' action='controllers/student_filter_controller.php'>
									<div>
										<label for='filter_study_session'>Оқу жылы</label>
										<select class='form-control' id='filter_study_session' name='filter_study_session'>
											<option value="">Барлық оқу жылы</option>
											<?php foreach ($year_session as $value) { ?>
											<option <?php echo isset($_SESSION['filter_study_session']) && $_SESSION['filter_study_session'] == $value['d'] ? "selected" : ""; ?> value='<?php echo $value['d']; ?>'><?php echo $value['d']." - ".intval($value['d']-1); ?></option>
											<?php } ?>
										</select>	
									</div>
									<table>
										<tr>
											<td style='width: 50%;'>
												<label class='pull-right' for='filter_school'>Мектеп</label>
											</td>
											<td style='width: 50%;'>
												<center>
													<input type="checkbox" id='filter_school' name="filter_school" <?php echo isset($_SESSION['filter_school']) && $_SESSION['filter_school'] ? "checked" : ""; ?>>
												</center>
											</td>
										</tr>
										<tr>
											<td style='width: 50%;'>
												<label class='pull-right' for='filter_subject'>Пән</label>
											</td>
											<td style='width: 50%;'>
												<center>
													<input type="checkbox" id='filter_subject' name="filter_subject" <?php echo isset($_SESSION['filter_subject']) && $_SESSION['filter_subject'] ? "checked" : ""; ?> >
												</center>
											</td>
										</tr>
										<tr>
											<td style='width: 50%;'>
												<label class='pull-right' for='filter_finish_course'>Курсты аяқтаған оқушылар</label>
											</td>
											<td style='width: 50%;'>
												<center>
													<input type="checkbox" id='filter_finish_course' name="filter_finish_course" <?php echo isset($_SESSION['filter_finish_course']) && $_SESSION['filter_finish_course'] ? "checked" : ""; ?> >
												</center>
											</td>
										</tr>
									</table>
									<br>
									<input type="submit" name="set_student_filter" class='btn btn-md btn-info pull-right start_loading' value='Фильтр'>
								</form>
							</div>
						</div>
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
			console.log($data_num);
			$('.box-pop-up .modal-title').html('<center><h1>Оқушы</h1></center>');
			$('.box-pop-up .modal-body').html('<center><h3>Loading...</h3></center>');
			$('.box-pop-up .modal-body').load('student_info.php?data_num='+$data_num);
		});

		$(document).on('click', '.start_loading', function() {
			$('#lll').css('display','block');
		});	

		window.onbeforeunload = function(event) {
	        $('#lll').css('display','block');
	    };

	    $(document).on('click', '.copy-students', function(){
			var el = document.getElementById('student-list');
			var body = document.body, range, sel;
			if (document.createRange && window.getSelection) {
				range = document.createRange();
				sel = window.getSelection();
				sel.removeAllRanges();
				try {
					range.selectNodeContents(el);
					sel.addRange(range);
				} catch (e) {
					range.selectNode(el);
					sel.addRange(range);
				}
				document.execCommand("copy");
			} else if (body.createTextRange) {
				range = body.createTextRange();
				range.moveToElementText(el);
				range.select();
				range.execCommand("Copy");
			}
			alert("Скопировано");
		});
	</script>
</body>
<?php $_SESSION['archive_load_page'] = false; ?>
</html>