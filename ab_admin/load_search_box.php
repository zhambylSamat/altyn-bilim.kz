<?php
	include_once('../connection.php');
	$school = 'school';
	$teacher = 'teacher';
	$subject = 'subject';
	$group = 'group';
	$search_type_arr = [$school, $teacher, $subject, $group];
	$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : ""; 
	if(!in_array($search_type, $search_type_arr)){
?>
<input type="text" name="search" data-name='student' class='form-control pull-right' id='search' style='width: 20%;' placeholder="Поиск...">
<?php } 
	else if(isset($search_type) && in_array($search_type, $search_type_arr)){ 
		$result_search_arguments = array();
		if($search_type == $school){
			$stmt = $conn->prepare("SELECT DISTINCT s.school, 
										((SELECT count(s2.student_num) 
										FROM student s2
										WHERE s2.school = s.school
											AND s2.block != 6)
										* 100 /
										(SELECT count(s2.student_num) 
										FROM student s2
										WHERE s2.block != 6)) as f 
									FROM student s 
									WHERE s.block != 6 
										AND s.school != '' 
									ORDER BY s.school ASC");
			$stmt->execute();
			$result_search_arguments = $stmt->fetchAll();
		}
		else if($search_type == $teacher){
			$stmt = $conn->prepare("SELECT t.teacher_num, 
										t.name, 
										t.surname
									FROM teacher t 
									WHERE t.block != 6 
									ORDER BY t.surname, t.name ASC");
			$stmt->execute();
			$result_search_arguments = $stmt->fetchAll();
		}
		else if($search_type == $subject){
			$stmt = $conn->prepare("SELECT sj.subject_num, 
										sj.subject_name
									FROM subject sj 
									ORDER BY sj.subject_name ASC");
			$stmt->execute();
			$result_search_arguments = $stmt->fetchAll();
		} else if ($search_type == $group){
			$stmt = $conn->prepare("SELECT gi.group_info_num,
										gi.group_name,
										(SELECT COUNT(gs2.group_student_num)
								    	FROM group_student gs2
								    	WHERE gi.group_info_num = gs2.group_info_num 
								    		AND gs2.block != 6) c
									FROM group_info gi
									WHERE gi.block != 6
									ORDER BY gi.group_name ASC");
			$stmt->execute();
			$result_search_arguments = $stmt->fetchAll();
		}
?>
	<select class='form-control pull-right select_search' search-type='<?php echo $search_type;?>' style='width: 20%;'>
		<option value=''>Барлығы</option>
		<?php
			foreach ($result_search_arguments as $value) {
				if($search_type == $school){
					$text = $value['school']."&nbsp;&nbsp;&nbsp;&nbsp;".round($value['f'],1)."%";
					echo "<option value='".$value['school']."' title='".$text."'>".$text."</option>";
				}
				else if($search_type == $teacher){
					$text = $value['surname']." ".$value['name'];
					echo "<option value='".$value['teacher_num']."' title='".$text."'>".$text."</option>";
				}
				else if($search_type == $subject){
					$text = $value['subject_name'];
					echo "<option value='".$value['subject_num']."' title='".$text."'>".$text."</option>";
				} else if ($search_type == $group) {
					$text = $value['group_name']." ".$value['name'];
					echo "<option value='".$value['group_info_num']."' title='".$text."'>".$text." ".$value['c']."</option>";
				}
			}
		?>
	</select>
<?php } ?>