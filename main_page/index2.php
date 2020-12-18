
<?php
	include_once('connection.php');
	if(!isset($_SESSION['student_num'])){
		header('location:signin.php');
	}
	else{
		// echo $_SESSION['student_num'];
	}
    // try {
    //     $stmt = $conn->prepare("SELECT count(*) FROM user_connection_tmp WHERE student_num = :student_num");
    //     $stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
    //     $stmt->execute();
    //     $result_ip = $stmt->fetchColumn(); 
    //     if($result_ip==0){
    //         header('location:signin.php');
    //     } 
    // } catch (PDOException $e) {
    // echo "Error : ".$e->getMessage()." !!!";
    // }
?>
<?php
	$permission_count = 0;
	try {
		$stmt_permission = $conn->prepare("SELECT stp.video_permission videoPermission, stp.test_permission testPermission, stp.subtopic_num subtopicNum FROM student_permission sp, student_test_permission stp WHERE sp.student_num = :student_num AND stp.student_permission_num = sp.student_permission_num");

		$stmt_permission->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
     	
	    $stmt_permission->execute();
	    $permission_count = $stmt_permission->rowCount();
	    $result_permission = $stmt_permission->fetchAll(); 
	    if($permission_count!=0){

		    $content_name = array();
			$subject_arr = array();
			$topic_arr = array();
			$subtopic_arr = array();
	    	foreach($result_permission as $readrow_permission){
	    		if($readrow_permission['videoPermission']=='t'){
		    		$stmt_section = $conn->prepare("SELECT s.subject_name subjectName, s.subject_num subjectNum FROM subject s, topic t, subtopic st WHERE st.subtopic_num = :subtopic_num AND st.topic_num = t.topic_num AND t.subject_num = s.subject_num");

					$stmt_section->bindParam(':subtopic_num', $readrow_permission['subtopicNum'], PDO::PARAM_STR);
			     	
				    $stmt_section->execute();
				    $result_subject = $stmt_section->fetch(PDO::FETCH_ASSOC);
				    if(!in_array($result_subject['subjectNum'],$subject_arr)){
				    	array_push($subject_arr, $result_subject['subjectNum']);
				    	$content_name[$result_subject['subjectNum']] = $result_subject['subjectName'];
				    }
				    $stmt_topic = $conn->prepare("SELECT t.topic_num topicNum, t.topic_name topicName FROM topic t, subtopic st WHERE st.subtopic_num = :subtopic_num AND st.topic_num = t.topic_num");
					$stmt_topic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_topic->execute();
					$result_topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_topic['topicNum'],$topic_arr)){
						$topic_arr[$result_topic['topicNum']] = $result_subject['subjectNum'];
						$content_name[$result_topic['topicNum']] = $result_topic['topicName'];
					}

					$stmt_subtopic = $conn->prepare("SELECT * FROM subtopic WHERE subtopic_num = :subtopic_num");
					$stmt_subtopic->bindParam(':subtopic_num',$readrow_permission['subtopicNum'], PDO::PARAM_STR);
					$stmt_subtopic->execute();
					$result_subtopic = $stmt_subtopic->fetch(PDO::FETCH_ASSOC);
					if(!array_key_exists($result_subtopic['subtopic_num'],$subtopic_arr)){
						$subtopic_arr[$result_subtopic['subtopic_num']] = $result_topic['topicNum'];
						$content_name[$result_subtopic['subtopic_num']] = $result_subtopic['subtopic_name'];
					}
				}
			}
		}
	} catch (PDOException $e) {
		echo "Error ".$e->getMessge()." !!!";
	}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<?php include_once('meta.php');?>
	<title>Altyn Bilim</title>
	<?php include_once('style.php');?>
	<style type="text/css">
		.secondary{
			display:none;
		}
		.video, .test{
			display:none;
		}
		.question{
			display:none;
		}
		.question:first-child{
			display:block;
		}
		video::-internal-media-controls-download-button {
		    display:none;
		}
	</style>
	<script>
 function fallback(video) {
   // replace <video> with its contents
   while (video.hasChildNodes()) {
     if (video.firstChild instanceof HTMLSourceElement)
       video.removeChild(video.firstChild);
     else
       video.parentNode.insertBefore(video.firstChild, video);
   }
   video.parentNode.removeChild(video);
 }
</script>

</head>
<body>
<?php include_once('nav.php');?>
	<section class='box'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-3 col-sm-3'>
					<div class='btn-group-vertical' style='width:100%;'>
						<button type='button' class='btn btn-info' onclick='mainContent()'>Басты бет</button>
						<?php 
						for($i = 0; $i<count($subject_arr); $i++){
						?>
						<button class='btn btn-default arena_section' data_name='subject' data_num = "<?php echo $subject_arr[$i];?>">
						<?php echo $content_name[$subject_arr[$i]];?>							
						</button>
						<?php
							foreach($topic_arr as $topic_key => $topic_val){ 
								if($topic_val==$subject_arr[$i]){
						?>
						<div class='btn-group' role='group'>
							<button id='btn-dropdown-1' type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
								<?php 
									echo $content_name[$topic_key];
								?>
								<span class='caret'></span>
							</button>
							<div class='dropdown-backdrop'></div>
							<ul class='dropdown-menu' aria-labelledby='btn-dropdown-1' style="width:100%;">
							<?php 
								foreach($subtopic_arr as $subtopic_key => $subtopic_val){ 
								if($subtopic_val==$topic_key){
							?>
								<li>
									<!-- <a onclick="show('#'+'topic_num','#primary')">Тақырып 1</a> -->
									<a data_name='subtopic' data_num = "<?php echo $subtopic_key?>" class='arena_section'>
									<?php echo $content_name[$subtopic_key];?>
									</a>
								</li>
							<?php }}?>
							</ul>
						</div>
						<?php }}}?>
					</div>
				</div>

				<div class='col-md-9 col-sm-9'>
					<div id='arena_section'>
						
					</div>
				</div>
			</div>
		</div>
	</section>

<?php include_once('js.php');?>
<script type="text/javascript">
	mainContent();
	function mainContent(){
		$(function(){
			$('#arena_section').load('total_info.php');
		});
	}
	$(document).on("click",".arena_section",function(){
		$data_name = $(this).attr('data_name');
		$data_num = $(this).attr('data_num');
		console.log($data_name);
		if($data_name == 'subject'){
			console.log($data_num);
			$('#arena_section').load('total_info.php #'+$data_num);
		}
		else if($data_name == 'subtopic'){
			console.log($data_num);
			$('#arena_section').load('lesson.php?<?php echo md5(md5('dataNum'));?>='+$data_num);
		}
		else if($data_name=='startTest'){
			var alert = confirm("-Тестті бастағаннан кейін оны бітірмей шыға алмайсыз!\n--Тесттен өту үшін сіздің үпайыңыз 80%-дан асуы тиіс. \n---Кейбір сұрақтардың бірнеше жауаптары болуы мүмкін*");
			if(alert){
				$('#arena_section').load('test.php?<?php echo md5(md5('dataNum'));?>='+$data_num);
			}
		}
	});
</script>


<script type="text/javascript">
	function uplFile(){
		$(function(){
			$("#idd").load('admin/abc.php');
		});
	}
</script>
<script type="text/javascript">
	$count_question = 0;
	$(document).ready(function(){
		$count_question = $('.question').length;
	});
	var arr = new Array($count_question);
	function show(objShow,objHide){
		$(function(){
			if(!$('.btn-group-vertical button').hasClass('disabled')){
				console.log($('.btn-group-vertical button').hasClass('disabled'))
				$(objHide).css("display","none");
				$(objShow).css("display","block");
			}
		});
	}
	function show2(objShow,objHide){
		$(function(){			
			console.log($('.btn-group-vertical button').hasClass('disabled'));
			$(objHide).css("display","none");
			$(objShow).css("display","block");
		});
	}
	$(function(){
		$('.btn-answer').on("click",function(){
			if($(this).hasClass('btn-info')){
				$(this).removeClass('btn-info').addClass('btn-primary');
			}
		});
	});
	$(function(){
		$(".answer_checkbox").on("change",function(){
			$attr = $(this).attr('data');
			if($(this).prop('checked')==true){
				if(arr[$attr]==undefined){
					arr[$attr] = 0;
				}
				arr[$attr]++;
			}
			else{
				arr[$attr]--;
			}
			if($('.btn-answer').eq($attr).hasClass("btn-primary") && arr[$attr]!=0){
				$('.btn-answer').eq($attr).removeClass("btn-primary").addClass("btn-success");
			}
			else if(arr[$attr]==0){
				$('.btn-answer').eq($attr).removeClass("btn-success").addClass("btn-primary");
			}
		});
	});
	function startTest(objShow, objHide){
		var alert = confirm("-Тестті бастағаннан кейін оны бітірмей шыға алмайсыз!\n--Тесттен өту үшін сіздің үпайыңыз 80%-дан асуы тиіс. \n---Кейбір сұрақтардың бірнеше жауаптары болуы мүмкін*");
		if(alert == true){
			$(function(){
				$objDisable='.btn-group-vertical button';
				$(objHide).css("display","none");
				$($objDisable).addClass('disabled');
				$(objShow).css("display","block");
				// obj_Hide = objHide;
				// obj_Show = objShow;
				// obj_Disable = '.btn-group-vertical button';
				// $('#css').val(objHide+'!'+$objDisable+'!'+objShow);
				// console.log($css);
			});
		}
	}	
</script>
</body>
</html>


<!-- <div id='primary'>
	<center><h1>Физика әлеміне қош келдіңіз.<br> Сәттілік!!!</h1></center>
</div>
<div class='secondary' id='topic_num'>
	<div class='content'>
		<center><h2>Кинематика - Тақырып 1</h2></center>
		<h3><a onclick="show('#'+'topic_num #video','.content')" style='cursor: pointer;'>> Видео сабақ</a></h3>
		<h3><a onclick="startTest('#'+'topic_num #test','.content')" style='cursor: pointer;'>> Тақарыпқа байланысты тест</a></h3>
	</div>
	<div class='video' id='video'>
		<a onclick="show('#'+'topic_num .content','#'+'topic_num #video')" style='cursor:pointer;'>< Артқа</a>
		<center><h2>Кинематика - Тақырып 1</h2></center>
		<div class='row'>
			<div class='col-md-12 col-sm-12' id='idd'>
				<a onclick='uplFile()'>asdfasd</a>
				
            </div>
            asdfasdfsad
        </div>
	</div>
	<div class='test' id='test'>
		<center><h2>Кинематика - Тақырып 1</h2></center>
		<h4>Тесттен өту үшін сіздің үпайыңыз 80%-дан асуы тиіс</h4>
		<h5>Кейбір сұрақтардың бірнеше жауаптары болуы мүмкін*</h5><br><br>
		<form action='index.php' method="post">
			<div class='question' id='question_num1'>
				<div class='question_txt'>
					<h4>
						#1 Lorem ipsum dolor sit amet, consectetur adipisicing elit...?<br>
						<img src="img/123.JPG" style='max-width: 200px; max-height: 200px; width: 100%; height: auto;'>
					</h4>
					<input type="hidden" name="question_num[]" value='question_num'>
				</div>
				<hr>
				<div class='answer'>
					<div class='row'>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-1' class='answer_checkbox' type="checkbox" data='0' name="answer[question_num][]" value='answer_num'>
							<label for='answer-1'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-2' class='answer_checkbox' type="checkbox" data='0' name="answer[question_num][]" value='answer_num'>
							<label for='answer-2'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-3' class='answer_checkbox' type="checkbox" data='0' name="answer[question_num][]" value='answer_num'>
							<label for='answer-3'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-4' class='answer_checkbox' type="checkbox" data='0' name="answer[question_num][]" value='answer_num'>
							<label for='answer-4'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-5' class='answer_checkbox' type="checkbox" data='0' name="answer[question_num][]" value='answer_num'>
							<label for='answer-5'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class='question' id='question_num2'>
				<div class='question_txt'>
					<h4># 2Lorem ipsum dolor sit amet, consectetur adipisicing elit...?</h4>
					<input type="hidden" name="question_num[]" value='question_num'>
				</div>
				<hr>
				<div class='answer'>
					<div class='row'>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-6' class='answer_checkbox' type="checkbox" data='1' name="answer[question_num][]" value='answer_num'>
							<label for='answer-6'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-7' class='answer_checkbox' type="checkbox" data='1' name="answer[question_num][]" value='answer_num'>
							<label for='answer-7'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-8' class='answer_checkbox' type="checkbox" data='1' name="answer[question_num][]" value='answer_num'>
							<label for='answer-8'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-9' class='answer_checkbox' type="checkbox" data='1' name="answer[question_num][]" value='answer_num'>
							<label for='answer-9'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class='question' id='question_num3'>
				<div class='question_txt'>
					<h4>#3 Lorem ipsum dolor sit amet, consectetur adipisicing elit...?</h4>
					<input type="hidden" name="question_num[]" value='question_num'>
				</div>
				<hr>
				<div class='answer'>
					<div class='row'>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-10' class='answer_checkbox' type="checkbox" data='2' name="answer[question_num][]" value='answer_num'>
							<label for='answer-10'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-11' class='answer_checkbox' type="checkbox" data='2' name="answer[question_num][]" value='answer_num'>
							<label for='answer-11'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
						<div class='col-md-4 col-sm-4' style='border:1px dashed lightgray;'>
							<input id='answer-12' class='answer_checkbox' type="checkbox" data='2' name="answer[question_num][]" value='answer_num'>
							<label for='answer-12'>
								Жауап #1<br>
								<img src="img/123.JPG" style='max-width: 150px; max-height: 150px; width: 100%; height: auto;'>
							</label>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class='row'>
				<a class='btn btn-primary btn-sm btn-answer' onclick="show2('#'+'question_num1','.question')">1</a>
				<a class='btn btn-info btn-sm btn-answer' onclick="show2('#'+'question_num2','.question')">2</a>
				<a class='btn btn-info btn-sm btn-answer' onclick="show2('#'+'question_num3','.question')">3</a>
			</div>
			<input type="hidden" name="css" id='css' value=''>
			<input type="submit" name="submit" class='btn btn-success btn-md pull-right' value='Тапсыру'>
		</form>
	</div>
</div> -->