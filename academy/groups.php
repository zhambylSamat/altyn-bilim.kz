<?php
	include_once('common/connection.php');

	$query = "SELECT sj.id AS subject_id,
					sj.title AS subject_title,
					gi.id AS group_id,
					gi.group_name,
					gi.topic_id,
					t.title AS topic_title,
					(SELECT st2.title
					FROM lesson_progress lp2,
						subtopic st2
					WHERE lp2.group_info_id = gi.id
						AND st2.id = lp2.subtopic_id
					ORDER BY lp2.created_date DESC
					LIMIT 1) AS current_subtopic,
					DATE_FORMAT(gi.start_date, '%d.%m.%Y') AS start_date
				FROM group_info gi,
					subject sj,
					topic t
				WHERE sj.id = gi.subject_id
					AND gi.is_archive = 0
					AND t.id = gi.topic_id
					AND 1 = (SELECT s2.is_active FROM status s2 WHERE s2.id = gi.status_id)
				ORDER BY sj.title, gi.group_name";
	$stmt = $connect->prepare($query);
	$stmt->execute();
	$sql_res = $stmt->fetchAll();

	$active_groups = array();
	$future_groups = array();

	foreach ($sql_res as $val) {
		if ($val['current_subtopic'] == "") {
			if (!isset($future_groups[$val['subject_id']])) {
				$future_groups[$val['subject_id']] = array('subject_title' => $val['subject_title'],
															'groups' => array());
			}
			array_push($future_groups[$val['subject_id']]['groups'], array('group_name' => $val['group_name'],
																			'group_id' => $val['group_id'],
																			'start_date' => $val['start_date'],
																			'subject_id' => $val['subject_id'],
																			'topic_id' => $val['topic_id'],
																			'subject_title' => $val['subject_title'],
																			'topic_title' => $val['topic_title']));
		} else {
			if (!isset($active_groups[$val['subject_id']])) {
				$active_groups[$val['subject_id']] = array('subject_title' => $val['subject_title'],
															'groups' => array());
			}
			array_push($active_groups[$val['subject_id']]['groups'], array('group_id' => $val['group_id'],
																			'group_name' => $val['group_name'],
																			'subtopic_title' => $val['current_subtopic']));
		}
	}

	$group_on_plan = get_starting_groups(5);
	foreach ($group_on_plan as $subject_id => $value) {
		if (array_key_exists($subject_id, $future_groups)) {
			$future_groups[$subject_id]['groups'] = array_merge($future_groups[$subject_id]['groups'], $value['groups']);
		} else {
			$future_groups[$subject_id] = $value;
		}
	}

	function sortFunction_group_name($a, $b) {
	    return strnatcmp($a["group_name"], $b["group_name"]);
	}
	foreach ($future_groups as $subject_id => $value) {
		usort($future_groups[$subject_id]['groups'], "sortFunction_group_name");
	}

	function sortFunction_subject_title($a, $b) {
		return strnatcmp($a['subject_title'], $b['subject_title']);
	}
	usort($future_groups, "sortFunction_subject_title");

?>
<div class='container'>
	<div class='row'>
		<div class='col-md-12 col-sm-12 col-xs-12'>
			<?php echo count($future_groups) > 0 ? '<h3>Жақында ашылатын ағымдар:</h3>' : ''; ?>
			<div style='padding-left: 10px;'>
				<?php
					$html = "";
					foreach ($future_groups as $subject) {
						$html .= "<h4>".$subject['subject_title']."</h4>";
						foreach ($subject['groups'] as $value) {
							$group_id = isset($value['group_id']) ? $value['group_id'] : $value['parent_group_id'];
							$html .= "<div class='groups future' style='margin:0.5%;'>";
								$html .= "<a style='cursor:pointer; text-decoration:underline;'class='future-group'
											data-toggle='modal'
											data-target='#topic-list'
											data-id='".$value['topic_id']."'
											data-group-id='".$group_id."'
											data-start-date='".$value['start_date']."'>".$value['group_name']."</a>&nbsp;&nbsp;&nbsp;";
								$html .= "<i>".$value['start_date']."</i><br>";
								$html .= "<center><button style='margin-top: 7px;'
													class='btn btn-success btn-sm btn-block future-group-register'
													data-topic-id='".$value['topic_id']."'
													data-subject-id='".$value['subject_id']."'
													data-topic-title='".$value['topic_title']."'
													data-subject-title='".$value['subject_title']."'
													>Тіркелу</button></center>";
							$html .= "</div>";
						}
					}
					echo $html;
				?>
			</div>
			<hr>
			<h3>Қазір болып жатқан ағымдар:</h3>
			<div style='padding-left: 10px; margin-top: 2%;'>
				<?php
					$html = "";
					foreach ($active_groups as $subject) {
						$html .= "<h4>".$subject['subject_title']."</h4>";
						foreach ($subject['groups'] as $value) {
							$html .= "<div class='groups active' style='margin:0.5%;' data-id='".$value['group_id']."' data-toggle='modal' data-target='#topic-list'>";
								$html .= "<b>".$value['group_name']."</b><br>";
								$html .= "<i>".$value['subtopic_title']."</i>";
								$html .= "<center><span class='text-primary'>Толығырақ</span></center>";
							$html .= "</div>";
						}
					}
					echo $html;
				?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="topic-list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel">Өтілетін тақырыптар</h4>
      		</div>
      		<div class="modal-body">
      		</div>
    	</div>
	</div>
</div>
