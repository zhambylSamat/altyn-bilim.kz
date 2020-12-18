<style type="text/css">
	.army-students {
		text-align: center;
	}

	.army-students .army-row-count {
		display: table-cell;
		vertical-align: middle;
	}

	.army-students .army-student-fio {
		display: table-cell;
		vertical-align: middle;
		font-size: 14px;
		font-weight: bold;
	}

	.army-students .army-student-percent {
		display: table-cell;
		vertical-align: middle;
		font-size: 14px;
		font-weight: bold;
	}

	.army-students .army-medal-icon {
		width: 70px;
		height: auto;
	}

	.army-students .student-avatar-box {
		position: relative;
	}

	.army-students .student-avatar-box .student-img-content {
		position: relative;
		width: 70px;
		height: 70px;
	}

	.army-students .student-avatar-box .student-img-content .no-img-content {
		width: 70px;
		height: 70px;
		border-radius: 35px;
		
	}
	.army-students .student-avatar-box .student-img-content .student-img-box {
		width: 70px;
		height: 70px;
		overflow: hidden;
		border-radius: 35px;
	}

	.army-students .student-avatar-box .student-img-content .student-img-box .army-student-avatar {
		width: 70px;
		height: auto;
		cursor: pointer;
	}
</style>
<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	if (isset($_GET['group'])) {
		$group_info_id = $_GET['group'];
	}

	include_once($root.'/staff/group/view.php');
	$student_army_medals = get_student_army_medals($group_info_id);

	// echo json_encode($student_army_medals, JSON_UNESCAPED_UNICODE);

	$html = "";
	$html .= "<table class='table table-striped table-bordered army-students'>";
		$count = 0;
		foreach ($student_army_medals as $group_student_id => $value) {

			$img_html = "<div class='student-avatar-box'>";
				$img_html .= "<center><div class='student-img-content'>";
				if ($value['avatar_link'] != '') {
					$img_html .= "<div class='student-img-box'><img data-toggle='modal' data-target='#army-user-info' data-user-fio='".$value['last_name']." ".$value['first_name']."' data-rank='".$value['army_medal_info']['title']."' class='army-student-avatar' src='".$ab_root."/academy/".$value['avatar_link']."' /></div>";
				} else {
					$img_html .= "<div class='student-img-box'><img class='no-img-content' data-user-fio='".$value['last_name']." ".$value['first_name']."' data-rank='".$value['army_medal_info']['title']."' src='".$ab_root."/academy/common/assets/img/noava.png' /></div>";
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