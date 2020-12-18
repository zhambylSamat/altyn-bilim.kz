<?php
	if(!isset($_GET['data_num'])){
		header("location:index.php");
	}
	$group_info_num = $_GET['data_num'];
	include_once('../connection.php');

	try {
		$stmt = $conn->prepare("SELECT gi.block as group_block,
									gi.group_name, 
									t.block as teacher_block,
								    t.surname, 
								    t.name, 
								    sj.subject_name
								FROM group_info gi,
									teacher t,
								    subject sj
								WHERE gi.group_info_num = :group_info_num
									AND gi.teacher_num = t.teacher_num
								    AND gi.subject_num = sj.subject_num");
		$stmt->bindValue(':group_info_num', $group_info_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $group_result = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT s.student_num, 
									s.surname, 
									s.name, 
									gs.group_student_num,
									DATE_FORMAT(gs.start_date, '%d.%m.%Y') as start_date,
									DATE_FORMAT(gs.block_date, '%d.%m.%Y') as block_date
								FROM group_info gi,
									student s,
								    group_student gs
								WHERE gi.group_info_num = :group_info_num
									AND gs.group_info_num = gi.group_info_num
								    AND gs.block = 6
								    AND s.student_num = gs.student_num
								ORDER BY s.surname, s.name");
		$stmt->bindValue(':group_info_num', $group_info_num, PDO::PARAM_STR);
	    $stmt->execute();
	    $student_result = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Архив - <?php echo $group_result['group_name'];?></title>
	<?php include_once('style.php');?>
</head>
<body>
	<div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
		<center>
			<img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
		</center>
	</div>

	<?php include_once('nav.php');?>

	<section class='container'>
		<div class='row'>
			<div class='col-md-4 col-sm-4 col-xs-4'>
				<table class='table table-bordered table-striped'>
					<tr>
						<td>
							Группа: 
						</td>
						<td>
							<b><span class='<?php echo $group_result['group_block']==6 ? "text-warning" : "";?>'><?php echo $group_result['group_name'];?></span></b> <i><?php echo $group_result['group_block']==6 ? "(архив)" : ""; ?></i>
						</td>
					</tr>
					<tr>
						<td>
							Мұғалім: 
						</td>
						<td>
							<b><span class='<?php echo $group_result['teacher_block']==6 ? "text-warning" : "";?>'><?php echo $group_result['surname']." ".$group_result['name']?></span></b> <i><?php echo $group_result['teacher_block']==6 ? "(архив)" : ""; ?></i>
						</td>
					</tr>
					<tr>
						<td>
							Пән:
						</td>
						<td>
							<b><span><?php echo $group_result['subject_name'];?></span></b>
						</td>
					</tr>
				</table>
			</div>
			<div class='col-md-8 col-sm-8 col-xs-8'>
				<table class='table table-striped table-bordered'>
					<tr>
						<th>Студенттер</th>
						<th>Уақыт (Курс:)</th>
						<th>
							<?php
								if($group_result['group_block']==6){
									echo "<b class='text-danger'>Студентті архивтен шығару үшін группаны архивтен шығарыңыз!</b>"; 
								} 
							?>
						</th>
					</tr>
					<?php
						$student_count = 0;
						foreach ($student_result as $value) {
					?>
					<tr style='width: 100%;'>
						<td style='width: 30%;'>
							<span class='count'><?php echo ++$student_count;?></span><span>)</span> <span class='object_full_name'><?php echo $value['surname']." ".$value['name'];?></span>
						</td>
						<td style='width:25%;'>
							<span style='font-size:10px'>Бастауы: </span><span class='text-success' style='font-weight: 600;'><?php echo $value['start_date'];?></span>
							<br>
							<span style='font-size:10px'>Аяқтауы: </span><span class='text-danger' style='font-weight: 600;'><?php echo $value['block_date'];?></span>
						</td>
						<td>
							<?php
								if($group_result['group_block']==0){
							?>
							<a class='btn btn-xs btn-success from_archive' data-name='group_student' data-num="<?php echo $value['group_student_num'];?>" title='Восстановить'>
								<span class='glyphicon glyphicon-open-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
							</a>
							<?php
								}
								else if($group_result['group_block']==6) {
									echo "<b>-</b>";
								}
							?>		
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</section>

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

		$(document).on('click','.from_archive',function(){
			$student_full_name = $(this).parents('tr').find('.object_full_name').text();
			if(confirm("Вы точно хотите Восстановить? ("+$student_full_name.trim()+")")){
				$data_num = $(this).data('num');
				$data_name = $(this).data('name');
				$this = $(this);
				console.log($data_num+" "+$data_name);
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
				    		$elem.nextAll("tr").each(function(){
				    			$(this).find('.count').text(parseInt($(this).find('.count').text().trim())-1);
				    		});
				    		$this.parents("tr").stop().css({'background-color':"#5CB85C"}).animate({backgroundColor: 'rgba(255, 255, 255, 0)'},250,function(){
				    			$elem.remove();
				    		});
				    	} 
				    	else{
				    		console.log(data);
				    	}
				    },
				  	error: function(dataS) 
			    	{
			    		alert("bla bla bla");
			    		console.log(dataS);
			    	} 	        
			   	});
			}
		});
	</script>

</body>
</html>