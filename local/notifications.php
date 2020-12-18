<?php
	
	include_once("../connection.php");

	try {

		$config_subject_quiz = array();

		$stmt = $conn->prepare("SELECT * FROM config_subject_quiz");
		$stmt->execute();
		foreach ($stmt->fetchAll() as $value) {
			$config_subject_quiz[$value['subject_num']]['practice'] = $value['practice'];
			$config_subject_quiz[$value['subject_num']]['theory'] = $value['theory'];
		}

	    $stmt = $conn->prepare("SELECT n.id, 
	  								s.student_num,
	  								s.surname, 
									s.name, 
								    sj.subject_name,
								    ttm.mark,
								    DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') AS date_of_test
								FROM notification n,
									trial_test_mark ttm,
								    trial_test tt,
								    subject sj,
								    student s
								WHERE n.status in ('A', 'D') 
									AND DATE_ADD(n.created_date, INTERVAL 14 DAY) >= NOW()
									AND n.object_id = 4
									AND n.object_num = ttm.trial_test_mark_num
								    AND ttm.trial_test_num = tt.trial_test_num
								    AND tt.subject_num = sj.subject_num
								    AND tt.student_num = s.student_num
								    AND s.block != 6");
	  	$stmt->execute();
	  	$result_trial_test_top_notification = $stmt->fetchAll();
	  	$trial_test_top_notification_count = $stmt->rowCount();

	  	$stmt = $conn->prepare("SELECT n.id,
	  								n.object_parent_num,
	  								s.student_num,
	  								s.surname,
	  								s.name,
	  								sj.subject_name,
	  								ttm.mark,
	  								n.status,
	  								DATE_FORMAT(ttm.date_of_test, '%d.%m.%Y') AS date_of_test
	  							FROM notification n,
	  								trial_test tt,
	  								trial_test_mark ttm,
	  								subject sj,
	  								student s
	  							WHERE n.object_id = 5
	  								AND ttm.trial_test_mark_num = n.object_num
  									AND tt.trial_test_num = n.object_parent_num
  									AND sj.subject_num = tt.subject_num
  									AND s.student_num = tt.student_num
  									AND n.status in ('A', 'D')
  									AND 3 = (SELECT count(n1.object_parent_num) 
  											FROM notification n1
  											WHERE n1.object_parent_num = n.object_parent_num
  												AND n1.status in ('A', 'D'))
  									AND NOW() <= DATE_ADD((SELECT n1.created_date
  												FROM notification n1
  												WHERE n1.object_parent_num = n.object_parent_num
  													AND n1.status in ('A', 'D')
  												ORDER BY n1.created_date DESC
  												LIMIT 1), INTERVAL 14 DAY)
								    AND s.block != 6
  								ORDER BY n.object_parent_num, n.id");
	  	$stmt->execute();
	  	$result_trial_test_increase_notification = $stmt->fetchAll();
	  	$trial_test_increase_notification_count = $stmt->rowCount()/3;


	  	$stmt = $conn->prepare("SELECT n.id, 
	  								n.object_num,
	  								s.student_num,
	  								s.name, 
	  								s.surname,
	  								t.topic_name,
	  								sj.subject_name,
	  								sj.subject_num,
	  								qm.mark_theory,
	  								qm.mark_practice,
	  								DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
	  							FROM notification n,
	  								student s,
	  								topic t,
	  								subject sj,
	  								quiz q,
	  								quiz_mark qm
	  							WHERE n.object_id = 6
  									AND n.status in ('A', 'D')
  									AND DATE_ADD(n.created_date, INTERVAL 14 DAY) >= NOW() 
  									AND qm.quiz_mark_num = n.object_num
  									AND q.quiz_num = qm.quiz_num
  									AND t.topic_num = q.topic_num
  									AND sj.subject_num = t.subject_num
  									AND s.student_num = qm.student_num
  									AND s.block != 1
  									AND s.block != 6");
	  	$stmt->execute();
	  	$result_quiz_max_mark_notification = $stmt->fetchAll();
	  	$quiz_max_mark_notification_count = $stmt->rowCount();
		

		$stmt = $conn->prepare("SELECT n.id, 
	  								n.object_num,
	  								s.student_num,
	  								s.name, 
	  								s.surname,
	  								t.topic_name,
	  								sj.subject_name,
	  								sj.subject_num,
	  								qm.mark_theory,
	  								qm.mark_practice,
	  								DATE_FORMAT(qm.created_date, '%d.%m.%Y') AS created_date
	  							FROM notification n,
	  								student s,
	  								topic t,
	  								subject sj,
	  								quiz q,
	  								quiz_mark qm
	  							WHERE n.object_id = 7
	  								AND n.status in ('A', 'D')
	  								AND DATE_ADD(n.created_date, INTERVAL 14 DAY) >= NOW() 
	  								AND qm.quiz_mark_num = n.object_num 
	  								AND q.quiz_num = qm.quiz_num 
	  								AND t.topic_num = q.topic_num 
	  								AND sj.subject_num = t.subject_num 
	  								AND s.student_num = qm.student_num 
	  								AND s.block != 1
	  								AND s.block != 6");
	  	$stmt->execute();
	  	$result_quiz_max_mark_2_notification = $stmt->fetchAll();
	  	$quiz_max_mark_2_notification_count = $stmt->rowCount();
		
	} catch (PDOException $e) {
		throw $e;
	}
?>
<div class="btn-group-vertical" style='width: 100%;'>
	<?php if ($trial_test_increase_notification_count>0) {?>
		<button style='text-align: left !important; padding-right:0px;' 
				data-toggle='modal' data-target='.box-trial-test-increase-notification' 
				class="btn btn-success trial_test_increase_notification"
				title="Пробный тесттен қатарынан 3 рет балын көтерген оқушы(лар)">
			Пробный тесттен қатарынан 3 ...
			<span class='badge'><?php echo $trial_test_increase_notification_count; ?></span>
		</button>
	<?php } ?>
	<?php if ($trial_test_top_notification_count>0) {?>
		<button style='text-align: left !important; padding-right:0px;' 
				data-toggle='modal' data-target='.box-trial-test-top-notification' 
				class="btn btn-success trial_test_top_notification" 
				title="Пробный тесттен жоғарғы балл жинаған оқушы(лар)">
			Пробный тесттен жоғарғы ...
			<span class='badge'><?php echo $trial_test_top_notification_count; ?></span>
		</button>
	<?php } ?>
	<?php if ($quiz_max_mark_notification_count>0) {?>
		<button style='text-align: left !important; padding-right:0px;' 
				data-toggle='modal' data-target='.box-quiz-max-mark-notification'  
				class="btn btn-success quiz_max_mark_notification"
				title="Аралық бақылаудан 100% балл жинаған оқушы(лар)">
			Аралық бақылаудан 100% ...
			<span class='badge'><?php echo $quiz_max_mark_notification_count; ?></span>
		</button>
	<?php } ?>
	<?php if ($quiz_max_mark_2_notification_count>0) {?>
		<button style='text-align: left !important; padding-right:0px;' 
				data-toggle='modal' data-target='.box-quiz-max-mark-2-notification'  
				class="btn btn-success quiz_max_mark_2_notification"
				title="Аралық бақылаудан 100% баллды 1 айда 2 рет жинаған оқушы(лар)">
			Аралық бақылаудан 100% ...
			<span class='badge'><?php echo $quiz_max_mark_2_notification_count; ?></span>
		</button>
	<?php } ?>
</div>
<br><br>

<div class="modal fade box-quiz-max-mark-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Аралық бақылаудан 100% балл алған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + 10% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_quiz_max_mark_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo $value['surname']." ".$value['name'];?></b></span>
		    						</span>
		    						<br>
		    						<span>
		    							<?php
		    								if (array_key_exists($value['subject_num'], $config_subject_quiz) && $config_subject_quiz[$value['subject_num']]) {
		    									if ($config_subject_quiz[$value['subject_num']]['theory'] == 1) {
		    										echo "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>&nbsp;&nbsp;";
		    									}
		    									if ($config_subject_quiz[$value['subject_num']]['practice'] == 1) {
		    										echo "<span class='h4 text-success'><b>Есеп: ".$value['mark_practice']."</b></span>&nbsp;&nbsp;";
		    									}
		    								}
		    							?>
		    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
		    							&nbsp;&nbsp;
		    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
		    						</span>
		    					</center>
	    					</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-quiz-max-mark-2-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Аралық бақылаудан 100% баллды 1 айда 2 рет алған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + қосымша 5% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_quiz_max_mark_2_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo $value['surname']." ".$value['name'];?></b></span>
		    						</span>
		    						<br>
		    						<span>
		    							<?php
		    								if (array_key_exists($value['subject_num'], $config_subject_quiz) && $config_subject_quiz[$value['subject_num']]) {
		    									if ($config_subject_quiz[$value['subject_num']]['theory'] == 1) {
		    										echo "<span class='h4 text-success'><b>Теория: ".$value['mark_theory']."</b></span>&nbsp;&nbsp;";
		    									}
		    									if ($config_subject_quiz[$value['subject_num']]['practice'] == 1) {
		    										echo "<span class='h4 text-success'><b>Есеп: ".$value['mark_practice']."</b></span>&nbsp;&nbsp;";
		    									}
		    								}
		    							?>
		    							<span class='h5'>[<?php echo $value['subject_name'].", ".$value['topic_name']; ?>]</span>
		    							&nbsp;&nbsp;
		    							<span class='h5'>[<?php echo $value["created_date"]; ?>]</span>
		    						</span>
		    					</center>
	    					</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-trial-test-top-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пробный тесттен жоғарғы балл жинаған оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + 10% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					foreach ($result_trial_test_top_notification as $key => $value) { 
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'><b><?php echo $value['surname']." ".$value['name'];?></b></span>
		    						</span>
		    						<br>
		    						<span>
		    							<span class='h4 text-success'><b><?php echo "Балл: ".$value['mark'];?></b></span>
		    							&nbsp;&nbsp;
		    							<span class='h5'>[<?php echo $value['subject_name']; ?>]</span>
		    							&nbsp;&nbsp;
		    							<span class='h5'>[<?php echo $value["date_of_test"]; ?>]</span>
		    						</span>
		    					</center>
	    					</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>

<div class="modal fade box-trial-test-increase-notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
    		<center><h3>Пробный тесттен қатарынан 3 рет балын көтерген оқушы(лар) <br><i style='color:grey;'>Приз: Шоколад + 10% скидка</i></h3></center>
    	</div>
    	<div class="modal-body">
    		<div class='row'>
    			<div class='col-md-12 col-sm-12'>
    				<table class='table table-bordered'>
    					<?php
    					$count = 0; 
    					for ($i=0; $i < count($result_trial_test_increase_notification); $i=$i+3) {
    					?>
    					<tr>
    						<td><center><?php echo ++$count;?></center></td>
    						<td>
    							<center>
		    						<span>
		    							<span class='h4'>
		    								<b>
		    									<?php 
			    									echo $result_trial_test_increase_notification[$i]['surname'];
			    									echo " ";
			    									echo $result_trial_test_increase_notification[$i]['name'];
			    									echo " | ";
			    									echo $result_trial_test_increase_notification[$i]['subject_name'];
		    									?>
		    								</b>
		    							</span>
		    						</span>
		    						<br>
		    						<span>
		    							<span class='h4' style='color:#999;'>
		    								<?php echo $result_trial_test_increase_notification[$i]['date_of_test']; ?>
		    							</span>
		    							<b>:</b> 
		    							<span class='h4 text-success'>
		    								<b><?php echo $result_trial_test_increase_notification[$i]['mark']; ?></b>
		    							</span> &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
		    							
		    							<span class='h4' style='color:#999;'>
		    								<?php echo $result_trial_test_increase_notification[$i+1]['date_of_test']; ?>
		    							</span>
		    							<b>:</b>
		    							<span class='h4 text-success'>
		    								<b>
		    									<?php echo $result_trial_test_increase_notification[$i+1]['mark']; ?>
		    								</b>
		    							</span> &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
		    							
		    							<span class='h4' style='color:#999;'>
		    								<?php echo $result_trial_test_increase_notification[$i+2]['date_of_test']; ?>
		    							</span>
		    							<b>:</b>
		    							<span class='h4 text-success'>
		    								<b>
		    									<?php echo $result_trial_test_increase_notification[$i+2]['mark']; ?>
		    								</b>
		    							</span> 
		    						</span>
		    					</center>
	    					</td>
    					</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div> 
    </div>
  </div>
</div>