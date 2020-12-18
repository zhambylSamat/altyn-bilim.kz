<?php
include('../connection.php');
if(!isset($_SESSION['parent_num'])){
	header('location:signin.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title>Altyn-bilim.kz</title>
	<?php include_once('style.php');?>
</head>
<body>

<?php include_once('nav.php');?>

<?php
	$student_num = $_GET['data_num'];
	$student_info = array();
	$subjects = array();
	try {
		$stmt = $conn->prepare("SELECT name, surname, username FROM student WHERE student_num = :student_num ");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$student_info = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $conn->prepare("SELECT s.subject_num subject_num, s.subject_name name, gi.group_info_num group_info_num, gi.group_name group_name FROM subject s, group_info gi, group_student gs WHERE gs.student_num = :student_num AND gi.group_info_num = gs.group_info_num AND s.subject_num = gi.subject_num AND group_name != 'TEST' ORDER BY s.subject_name ASC");
		$stmt->bindParam(':student_num', $student_num, PDO::PARAM_STR);
		$stmt->execute();
		$subjects = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error ".$e->getMessage()." !!!";
	}
?>

<section>
	<div class='container'>
		<div class='row'>
			<div class='col-md-12 col-sm-12' id='month'>
				<center>
					<h3 style='color:#555;'>
						Оқушы: <i><?php echo $student_info['name']." ".$student_info['surname'];?></i>
					</h3>
					<hr>
					<div>
						<h4>
							Группа:&nbsp;&nbsp;
							<?php 
								$count = 0;
								$first_group_info_num = '';
								$first_subject_num = '';
								foreach($subjects as $value){	
									if($count==0){
										$class='btn-warning';
										$first_group_info_num = $value['group_info_num'];
										$first_subject_num = $value['subject_num'];
									}
									else {
										$class='btn-default';
									}
									$count++;
									echo "<a data-gin=".$value['group_info_num']." data-sn=".$value['subject_num']." class='month btn btn-sm ".$class."' href='#".$value['group_name']."'><b>".$value['group_name']." | ".$value['name']."</b></a>&nbsp;";
								} 
							?>
						</h4>
					</div>
				</center>
			</div>
		</div>
		<hr>
	</div>
</section>
<div class="modal fade box-comment-for-teacher" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center>
    			<h3></h3>
    		</center>
    	</div>
    	<div class="modal-body">
    	</div> 
    </div>
  </div>
</div>
<section>
	<div class='container'>
		<div class='row' id='student-marks'>
			<?php include_once("student_info_marks_ajax.php"); ?>
		</div>
	</div>
</section>

<?php include_once('js.php');?>
<script type="text/javascript">
	// --------------------------------review_start----------------------------------------
	$(document).on('click','.open-review',function(){
		$gin = $(this).parent().find('input[name=gin]').val();
		$sn = $(this).parent().find('input[name=sn]').val();
		$review_header = $(this).parent().find('input[name=review-header]').val();
		$('.box-comment-for-teacher .modal-header h3').text($review_header+" пәні мұғалімінің оқушы туралы комментариі");
		$('.box-comment-for-teacher .modal-body').text("Loading...");
		$('.box-comment-for-teacher .modal-body').load('load-review.php?gin='+$gin+"&sn="+$sn);
	});
	// --------------------------------review_end------------------------------------------
	$(document).on('click','.month',function(){
		$("#month").find('.btn-warning').removeClass('btn-warning').addClass('btn-default');
		$(this).removeClass('btn-default').addClass('btn-warning');
		$data_gin = $(this).attr('data-gin');
		$data_sn = $(this).attr('data-sn');
		$('#student-marks').html("<cente><h3>Loading...</h3></center>");
		$("#student-marks").load("student_info_marks_ajax.php?stn=<?php echo $student_num; ?>&sn="+$data_sn+"&gin="+$data_gin);
	});
	$(document).on('click','.mnth',function(){
		$txt = $.trim($(this).text());
		if($(this).hasClass('btn-warning')){
			$(this).removeClass('btn-warning').addClass('btn-success');
		}
		else if($(this).hasClass('btn-success')){
			$(this).removeClass('btn-success').addClass('btn-warning');
		}
		$("."+$txt).toggle();
	});
</script>
</body>
</html>