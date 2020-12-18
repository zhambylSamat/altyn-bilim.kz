<?php
	$LEVEL = 2;
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/common/page_navigation.php');
	include_once($root.'/common/set_navigations.php');
    $content_key = '';
	if (isset($_GET['content_key'])) {
		$content_key = $_GET['content_key'];
		unset($_GET['content_key']);
	}
    change_navigation($LEVEL, $content_key);

    include_once($root.'/common/check_authentication.php');
    check_admin_access();
?>

<?php
	include_once($root.'/staff/student/view.php');
	$student_result = get_not_activated_students();
?>
<!-- <p class='bg-warning'><b>!!! Қабылданбаған оқушылар <u>2 аптадан</u> кейін автоматты түрде өшіріледі !!!</b></p> -->
<ul>
	<li><p style='color: #5cb85c; '><b>Группаға (ағымға) қосылу</b></p></li>
	<li><p style='color: #f0ad4e; '><b>Резерв</b></p></li>
</ul>
<table class='table table-striped table-bordered'>
	<?php
		$count = 0;
		foreach ($student_result as $id => $value) {
			$html = "<tr>";
			
			$html .= "<th class='count'>".(++$count)."</th>";
			
			$html .= "<th>";
			$html .= "<p title='Аты-жөні'>".$value['full_name']."</p>";
			$html .= "<p title='Телефон нөмірі'>+7 ".$value['phone']."</p>";
			$html .= "</th>";
			
			$html .= "<td>";
			$html .= "<p><span title='Мектебі'>".$value['school']." мектеп.</span> <span title='Сыныбы'>".$value['class']." сынып.</span></p>";
			$html .= "<p title='Email'>Email: ".($value['email'] != '' ? $value['email'] : 'N/A')."</p>";
			$html .= "<p title='Тіркелу сауалнамасын толтырған уақыты'>".$value['created_date']."<p>";
			$html .= "</td>";

			$html .= "<td>";
			$c = 0;
			foreach ($value['courses'] as $course) {
				$html .= "<span style='color: #5cb85c; '>".(++$c).") ".$course['group_name'].": ".$course['title']."</span><br>";
			}
			$c = 0;
			foreach ($value['reserves'] as $reserve) {
				$html .= "<span style='color: #f0ad4e; '>".(++$c).") ".$reserve['subject_title'].": ".$reserve['topic_title']."</span><br>";
			}
			$html .= "</td>";

			$html .= "<td>";
			$html .= "<center class='confirm-box'><div style='margin-bottom: 10px;'><button class='btn btn-xs btn-success full-width-btn not-activated-student' data-action='confirm-accept' data-id='".$id."'>Қабылдау</button></div>";
			$html .= "<div><button class='btn btn-xs btn-danger full-width-btn not-activated-student' data-action='confirm-delete' data-id='".$id."'>Өшіру</button></div></center>";

			$html .= "<center class='accept-box hide'><div style='margin-bottom: 10px;'><button class='btn btn-sm btn-success full-width-btn not-activated-student' data-action='accept' data-id='".$id."'><b>Қабылдауды растау</b></button></div>";
			$html .= "<div><button class='btn btn-xs btn-warning full-width-btn not-activated-student' data-action='cancel-accept'>Отмена</button></div></center>";
			$html .= "</td>";

			$html .= "</tr>";

			echo $html;
		}
	?>
</table>
