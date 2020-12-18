<?php
	$group_army_infos = get_group_army_infos();

	// echo json_encode($group_army_infos, JSON_UNESCAPED_UNICODE);

	$html = "";
	$html .= "<div class='container-fluid'><div class='row'>";
	$html .= "<input type='hidden' name='ab-root' value='".$ab_root."'>";

	foreach ($group_army_infos as $group_info_id => $group_info) {
		$html .= "<div class='col-md-6 col-sm-6 col-xs-12'>";
		$html .= "<div class='comander-content'>";
		$html .= "<p class='group-subject-title'>".$group_info['subject_title']."</p>";
		$html .= "<div class='comander-img-box'>";
			$html .= "<center><p class='comander-img-content'>";
				$html .= "<img class='comander-avatar' data-toggle='modal' data-target='#army-user-info' data-user-fio='".$group_info['last_name']." ".$group_info['first_name']."' data-rank='Бас қолбасшы' src='".$ab_root."/academy/".$group_info['avatar_link']."'>";
				$html .= "<img class='comander-rank-img' src='".$ab_root."/academy/common/assets/img/comander.png'>";
			$html .= "</p></center>";
		$html .= "</div>"; // .comander-img-box;
		$html .= "<p class='comander-fio'>".$group_info['last_name']." ".$group_info['first_name']."</p>";
		$html .= "<p class='comander-rank'>Бас қолбасшы</p>";
		$html .= "</div>"; // .comander-content

		$html .= "<table class='table table-striped table-bordered army-students'>";
			$count = 0;
			foreach ($group_info['group_students'] as $group_student_id => $value) {

				$img_html = "<div class='student-avatar-box'>";
					$img_html .= "<center><div class='student-img-content'>";
					if ($value['avatar_link'] != '') {
						$extra_class = $value['student_id'] == $_SESSION['user_id'] ? 'self-avatar-img' : '';
						$img_html .= "<div class='student-img-box'><img data-toggle='modal' data-target='#army-user-info' data-user-fio='".$value['last_name']." ".$value['first_name']."' data-rank='".$value['army_medal_info']['title']."' class='army-student-avatar ".$extra_class."' src='".$ab_root."/academy/".$value['avatar_link']."' /></div>";
					} else {
						$extra_class = $value['student_id'] == $_SESSION['user_id'] ? 'self-avatar-no-img' : '';
						$img_html .= "<div class='student-img-box'><img class='no-img-content ".$extra_class."' data-user-fio='".$value['last_name']." ".$value['first_name']."' data-rank='".$value['army_medal_info']['title']."' src='".$ab_root."/academy/common/assets/img/noava.png' /></div>";
					}
					if ($value['student_id'] == $_SESSION['user_id']) {
						$img_html .= "<label for='student-avatar-file'><div class='edit-avatar'><span class='fas fa-pen'></span></div></label>";
						$img_html .= "<input type='file' name='student-avatar' id='student-avatar-file' style='display: none;'>";
					}
				$img_html .= "</div></center>";
				$img_html .= "</div>";

				$html .= "<tr>";
					$html .= "<td class='army-row-count'>".(++$count)."</td>";
					$html .= "<td>".$img_html."</td>";
					$html .= "<td class='army-student-fio'>".$value['last_name']." ".$value['first_name']."</td>";
					$html .= "<td class='army-student-percent'>".$value['army_medal_info']['percent']." / 100</td>";
					$html .= "<td title='".$value['army_medal_info']['title']."'><img class='army-medal-icon' src='".$ab_root."/academy/".$value['army_medal_info']['icon_link']."'><p>".$value['army_medal_info']['title']."</p></td>";
				$html .= "</tr>";
			}
		$html .= "</table>"; // .table

		$html .= "</div>"; // .col-...
	}
	$html .= "</div></div>"; // .container-fluid .row

	echo $html;
?>


<div class="modal fade" id="army-user-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  		<div class="modal-header">
	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    		<h4 class="modal-title" id="myModalLabel"></h4>
	  		</div>
	  		<div class="modal-body">
	    		
	  		</div>
		</div>
	</div>
</div>