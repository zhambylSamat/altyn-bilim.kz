<div id='no_comment' style='background-color:#FFB564; padding:0.1% 0.1%; color:#555; margin:0.5% 0; font-size: 11px; display: none;'>
	<b>*Оқушылардың "комменттері" енгізілмеген</b> <i>(мұғалімдегре ескерту керек)</i>
</div>
<div id='insufficient_information' style='padding:0.1% 0.1%; color:red; margin:0.5% 0; font-size: 11px; display: none;'>
	<b>*Оқушылардың ақпараттары толық емес!</b>
</div>
<table class="table table-striped table-bordered">
	<?php
		$result_student = array();
		$school = 'school';
		$teacher = 'teacher';
		$subject = 'subject';
		$group = "group";
		include('../connection.php');

		try {
			$search_attr = isset($_GET['search_attr']) && $_GET['search_attr']!='' ? $_GET['search_attr'] : "";
			// echo $_GET['search_type'];
			$search_type = isset($_GET['search_type']) && $_GET['search_type']!='' ? $_GET['search_type'] : "";
			if($search_type==$school){
				$stmt = $conn->prepare("SELECT s.student_num,
										s.name,
									    s.surname,
									    s.username,
									    s.password_type,
									    s.phone,
									    s.school,
									    (SELECT n.readed FROM news n WHERE n.type = s.student_num ) AS readed,
                                        (SELECT count(r2.group_student_num) 
                                    	FROM review r2, 
                                    		group_student gs3,
                                    		group_info gr_info
                                    	WHERE r2.review_info_num != (SELECT review_info_num 
                                    								FROM review_info 
                                    								WHERE description = 'comment') 
                                    		AND r2.group_student_num = gs3.group_student_num 
                                    		AND gs3.group_info_num = gr_info.group_info_num
                                    		AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
                                    		AND gs3.start_date <= CURDATE()
                                    		AND s.student_num = gs3.student_num 
                                    		AND gs3.block != 6) AS c1,
										(SELECT count(group_student_num) 
										FROM group_student gs2,
											group_info gr_info
										WHERE gs2.student_num = s.student_num
											AND gs2.group_info_num = gr_info.group_info_num
											AND gs2.start_date <= CURDATE()
											AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
											AND gs2.block != 6) AS c2
									FROM student s
									WHERE s.block != 6
										AND IF(:school != '', s.school, '' ) = :school
									GROUP BY s.student_num 
									ORDER BY s.school, s.surname, s.name ASC");
				$stmt->bindValue(':school', $search_attr, PDO::PARAM_STR);
				$stmt->execute();
		    	$result_student = $stmt->fetchAll(); 
			}
			else if($search_type==$teacher){
				$stmt = $conn->prepare("SELECT s.student_num,
										s.name,
									    s.surname,
									    s.username,
									    s.password_type,
									    s.phone,
									    s.school,
									    t.name teacher_name,
									    t.surname teacher_surname,
									    t.teacher_num,
									    (SELECT n.readed FROM news n WHERE n.type = s.student_num ) AS readed,
                                        (SELECT count(r2.group_student_num) 
                                    	FROM review r2, 
                                    		group_student gs3,
                                    		group_info gr_info
                                    	WHERE r2.review_info_num != (SELECT review_info_num 
                                    								FROM review_info 
                                    								WHERE description = 'comment') 
                                    		AND r2.group_student_num = gs3.group_student_num 
                                    		AND gs3.group_info_num = gr_info.group_info_num
                                    		AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
                                    		AND gs3.start_date <= CURDATE()
                                    		AND s.student_num = gs3.student_num 
                                    		AND gs3.block != 6) AS c1,
										(SELECT count(group_student_num) 
										FROM group_student gs2,
											group_info gr_info
										WHERE gs2.student_num = s.student_num
											AND gs2.group_info_num = gr_info.group_info_num
											AND gs2.start_date <= CURDATE()
											AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
											AND gs2.block != 6) AS c2
									FROM student s,
										group_info gr_info,
										group_student gr_student,
										teacher t
									WHERE s.block != 6
										AND gr_student.student_num = s.student_num
										AND IF(:teacher_num != '', gr_info.teacher_num, '' ) = :teacher_num
										AND gr_info.group_info_num = gr_student.group_info_num
										AND gr_info.teacher_num = t.teacher_num
									-- GROUP BY t.teacher_num 
									ORDER BY t.surname, t.name, s.surname, s.name ASC");
				$stmt->bindValue(':teacher_num', $search_attr, PDO::PARAM_STR);
				$stmt->execute();
		    	$result_student = $stmt->fetchAll(); 
			}
			else if($search_type == $subject){
				$stmt = $conn->prepare("SELECT s.student_num,
										s.name,
									    s.surname,
									    s.username,
									    s.password_type,
									    s.phone,
									    s.school,
									    sj.subject_name,
									    sj.subject_num,
									    (SELECT n.readed FROM news n WHERE n.type = s.student_num ) AS readed,
                                        (SELECT count(r2.group_student_num) 
                                    	FROM review r2, 
                                    		group_student gs3,
                                    		group_info gr_info
                                    	WHERE r2.review_info_num != (SELECT review_info_num 
                                    								FROM review_info 
                                    								WHERE description = 'comment') 
                                    		AND r2.group_student_num = gs3.group_student_num 
                                    		AND gs3.group_info_num = gr_info.group_info_num
                                    		AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
                                    		AND gs3.start_date <= CURDATE()
                                    		AND s.student_num = gs3.student_num 
                                    		AND gs3.block != 6) AS c1,
										(SELECT count(group_student_num) 
										FROM group_student gs2,
											group_info gr_info
										WHERE gs2.student_num = s.student_num
											AND gs2.group_info_num = gr_info.group_info_num
											AND gs2.start_date <= CURDATE()
											AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
											AND gs2.block != 6) AS c2
									FROM student s,
										group_info gr_info,
										group_student gr_student,
										subject sj
									WHERE s.block != 6
										AND gr_student.block != 6
										AND gr_info.block != 6
										AND gr_student.student_num = s.student_num
										AND IF(:subject_num != '', gr_info.subject_num, '' ) = :subject_num
										AND gr_info.group_info_num = gr_student.group_info_num
										AND gr_info.subject_num = sj.subject_num
									ORDER BY sj.subject_name, s.surname, s.name ASC");
				$stmt->bindValue(':subject_num', $search_attr, PDO::PARAM_STR);
				$stmt->execute();
		    	$result_student = $stmt->fetchAll();
			} else if ($search_type == $group) {
				$stmt = $conn->prepare("SELECT gi.group_info_num, 
											gi.group_name, 
											s.student_num,
											s.name, 
											s.surname,
											s.password_type,
											s.phone,
											s.school,
											s.username,
											(SELECT n.readed FROM news n WHERE n.type = s.student_num ) AS readed,
											(SELECT count(r2.group_student_num) 
	                                    	FROM review r2, 
	                                    		group_student gs3,
	                                    		group_info gr_info
	                                    	WHERE r2.review_info_num != (SELECT review_info_num 
	                                    								FROM review_info 
	                                    								WHERE description = 'comment') 
	                                    		AND r2.group_student_num = gs3.group_student_num 
	                                    		AND gs3.group_info_num = gr_info.group_info_num
	                                    		AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
	                                    		AND gs3.start_date <= CURDATE()
	                                    		AND s.student_num = gs3.student_num 
	                                    		AND gs3.block != 6) AS c1,
											(SELECT count(group_student_num) 
											FROM group_student gs2,
												group_info gr_info
											WHERE gs2.student_num = s.student_num
												AND gs2.group_info_num = gr_info.group_info_num
												AND gs2.start_date <= CURDATE()
												AND gr_info.subject_num != 'S5985a7ea3d0ae721486338'
												AND gs2.block != 6) AS c2
										FROM group_info gi,
											group_student gs,
										    student s
										WHERE gi.block != 6
											AND IF(:group_info_num != '', gi.group_info_num, '') = :group_info_num
											AND gi.group_info_num = gs.group_info_num
										    AND gs.block != 6
										    AND gs.student_num = s.student_num
										    AND s.block != 6
										ORDER BY gi.group_name, s.surname, s.name");
				$stmt->bindValue(':group_info_num', $search_attr, PDO::PARAM_STR);
				$stmt->execute();
		    	$result_student = $stmt->fetchAll();
			}

		    $stmt = $conn->prepare("SELECT count(description) c FROM review_info where description = 'review' group by description");
		    $stmt->execute();
		    $total_comment_number = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error ".$e->getMessage()." !!!";
		}

		$student_number = 1;
		$no_comment_count = 0;
		$insufficient_information_count = 0;
		$element = '';
		$repeated_element = '';
		foreach ($result_student as $readrow) {
			if($search_type == $school){
				if($element != $readrow['school']){
					echo "</table><span>Мектеп: <b class='h4 text-success' style='font-weight:bold;'>".$readrow['school']."</b></span><table class='table table-striped table-bordered'>";
					$student_number = 1;
				}
				$element = $readrow['school'];
			}
			else if($search_type == $teacher){
				if($element != $readrow['teacher_num']){
					echo "</table><span>Мұғалім: <b class='h4 text-success' style='font-weight:bold;'>".$readrow['teacher_surname']." ".$readrow['teacher_name']."</b></span><table class='table table-striped table-bordered'>";
					$student_number = 1;
					$repeated_element = '';
				}
				else if($repeated_element == $readrow['student_num']){
					continue;
				}
				$repeated_element = $readrow['student_num'];
				$element = $readrow['teacher_num'];
			}
			else if($search_type == $subject){
				if($element != $readrow['subject_num']){
					echo "</table><span>Пән: <b class='h4 text-success' style='font-weight:bold;'>".$readrow['subject_name']."</b></span><table class='table table-striped table-bordered'>";
					$student_number = 1;
					// $repeated_element = '';
				}
				// else if($repeated_element == $readrow['student_num']){
				// 	continue;
				// }
				// $repeated_element = $readrow['student_num'];
				$element = $readrow['subject_num'];
			}
			else if ($search_type == $group) {
				if ($element != $readrow['group_info_num']) {
					echo "</table><span>Группа: <b class='h4 text-success' style='font-wight:bold;'>".$readrow['group_name']."</b></span><table class='table table-strpied table-bordered'>";
					$student_number = 1;
					$repeated_element = '';
				}
				$element = $readrow['group_info_num'];
			}
			$bg_color = '';
			$color = '';
			if($readrow['c1']!=$readrow['c2']*intval($total_comment_number['c'])){
				$bg_color='#FFB564';
				$no_comment_count++;
			}
			if($readrow['phone']=='' || $readrow['school']==''){
				$color = 'red';
				$insufficient_information_count++;
			}
	?>
	<tr class='head' style='background-color:<?php echo $bg_color;?>;'>
		<td style='width: 1%;'>
			<center>
				<h4 class='count'>
					<?php 
						echo $student_number;
					?>	
				</h4>
			</center>
		</td>
		<td style=''>
			<div class='user_info'>
				<table class='' style='width:100%; background-color:rgba(0,0,0,0); margin:0; padding:0; border:none;'>
					<tr style='width: 100%;'>
						<td style='width: 40%;'>
							<h4 class='text-success' style='display: inline-block;'>
								<a class='object-full-name' href="student_info_marks.php?data_num=<?php echo $readrow['student_num']; ?>" target="_blank" style='color:<?php echo $color; ?>'>
									<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>
								</a> 
							</h4> 
							<a data-toggle='modal' class='student-modal' data-target='.box-student-form' data-action='view' data-num="<?php echo $readrow['student_num'];?>">[инфо]</a>
						</td>
						<td style='width: 30%;'><h5>Username: <b class='text-info'><?php echo $readrow['username']?></b></h5></td>
						<td style='width: 30%;'>
							<div class='password'>
								<h5'>Пароль: 
								<?php if($readrow['password_type']!='default'){?>
								<button class='btn btn-info btn-xs reset_password' data-name='student' style='display: inline-block;'>Сбросить пароль</button>
								<input type="hidden" name="reset" value='<?php echo $readrow['student_num']?>'>
								<?php }else{?>
								<b><u><i>'12345'</i></u></b>
								<?php }?>
								</h5'>
							</div>
						</td>
					</tr>
				</table>				
			</div>
		</td>
		<td>
			<a class='btn btn-default btn-xs more_info' data-name='student' data_toggle='false' data_num = "<?php echo $readrow['student_num']; ?>" title='Толығырақ'>
				<span class='glyphicon glyphicon-list-alt text-primary' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>	
			<a class='btn btn-xs btn-danger to_archive' data-name='student' data-num = "<?php echo $readrow['student_num']; ?>" title='Архивировать'>
				<span class='glyphicon glyphicon-save-file' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
			</a>
			<a class="btn btn-default btn-xs single-student-news" data-num='<?php echo $readrow['student_num']?>' data-name='<?php echo $readrow['surname']?>&nbsp;<?php echo $readrow['name']?>' data-toggle='modal' data-target='.box-news'>
				<span class='glyphicon glyphicon-envelope' aria-hidden='true' style='font-size: 20px; cursor: pointer; <?php echo ($readrow['readed']=='') ? "color:black;" : (($readrow['readed']==0) ? "color:orange;" : "color:#00F300"); ?>'></span>
			</a>
			<form style='display: inline-block;' onsubmit="return confirm('Подтвердите действие!')" method='post' action='admin_controller.php'>
				<center>
					<input type="hidden" name="data_num" value='<?php echo $readrow['student_num']?>'>
					<button type='submit' name='student_no_payment' class="btn btn-default btn-xs" title='Оплатасы жоқ'>
						<span class='glyphicon glyphicon-usd text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form>
			<form style='display: inline-block;' onsubmit="return confirm('Подтвердите действие!')" method='post' action='admin_controller.php'>
				<center>
					<input type="hidden" name="data_num" value='<?php echo $readrow['student_num']?>'>
					<button type='submit' name='student_no_contract' class="btn btn-default btn-xs" title='Договор өткізбегендер'>
						<span class='glyphicon glyphicon-file text-danger' aria-hidden='true' style='font-size: 20px; cursor: pointer;'></span>
					</button>
				</center>
			</form>
		</td>
	</tr>
	<tr class='body'>
		
	</tr>
	<?php 
		$student_number++; 
	}
	if($student_number == 1){
		echo "<center><h1 class='text-primary'>N/A</h1></center>";
	}
	?>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		if(<?php echo $no_comment_count;?>>0){
			$("#no_comment").show();
		}
		if(<?php echo $insufficient_information_count;?>>0){
			$("#insufficient_information").show();
		}
	});
</script>