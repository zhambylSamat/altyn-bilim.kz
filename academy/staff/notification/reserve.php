<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/notification/view.php');

	$result = get_reserve_notification_data();
	// echo json_encode($result, JSON_UNESCAPED_UNICODE);
	if (true) {
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="btn-group-vertical" role="group">
    	<?php
    		if (count($result['danger']) > 0) {
    	?>
    	<button class='btn btn-danger btn-md btn-block'  data-toggle='modal' data-target='#students-reserve-danger'>
    		Тіркелгендеріне 7 күннен асқан оқушылар
	  		<span class="badge"><?php echo count($result['danger']); ?></span>
    	</button>
    	<?php } if (count($result['warning']) > 0) { ?>
    	<button class='btn btn-warning btn-md btn-block'  data-toggle='modal' data-target='#students-reserve-warning'>
    		Тіркелгендеріне 7 күнне аз болган оқушылар
			<span class="badge"><?php echo count($result['warning']); ?></span>
    	</button>
    	<?php } if (count($result['ok']) > 0) { ?>
    	<button class='btn btn-info btn-md btn-block'  data-toggle='modal' data-target='#students-reserve-ok'>
    		Жақында оқуын бастайтын тіркелген оқушылар
			<span class="badge"><?php echo count($result['ok']); ?></span>
    	</button>
    	<?php } ?>
    </div>

    <div class="modal fade" id="students-reserve-danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header bg-danger">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Тіркелгендеріне 7 күннен асқан оқушылар</h4>
          		</div>
          		<div class="modal-body">
					<?php
          				$html = '';
          				foreach ($result['danger'] as $subject_id => $subject) {
          					$html .= "<table class='table table-bordered table-striped'>";
          						$html .= "<tr>";
          							$html .= "<td colspan='2'><center><b>".$subject['subject_title']."</b></td>";
          						$html .= "</tr>";
          					foreach ($subject['topics'] as $topic) {
          						$html .= "<tr>";
          							$html .= "<td>".$topic['topic_title']."</td>";
                        $html .= "<td>";
                          $html .= "<ol>";
                            foreach ($topic['student_fio'] as $fio) {
                              $html .= "<li>".$fio."</li>";
                            }
                          $html .= "</ol>";
                        $html .= "</td>";
          							// $html .= "<td>".$topic['students_count']." </td>";
          						$html .= "</tr>";
          					}
          					$html .= "</table>";
          				}
          				echo $html;
          			?>
          		</div>
        	</div>
    	</div>
    </div>
    <div class="modal fade" id="students-reserve-warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header bg-warning">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Тіркелгендеріне 7 күнне аз болган оқушылар</h4>
          		</div>
          		<div class="modal-body">
          			<?php
          				$html = '';
          				foreach ($result['warning'] as $subject_id => $subject) {
          					$html .= "<table class='table table-bordered table-striped'>";
          						$html .= "<tr>";
          							$html .= "<td colspan='2'><center><b>".$subject['subject_title']."</b></td>";
          						$html .= "</tr>";
          					foreach ($subject['topics'] as $topic) {
          						$html .= "<tr>";
          							$html .= "<td>".$topic['topic_title']."</td>";
                        $html .= "<td>";
                          $html .= "<ol>";
                            foreach ($topic['student_fio'] as $fio) {
                              $html .= "<li>".$fio."</li>";
                            }
                          $html .= "</ol>";
                        $html .= "</td>";
          							// $html .= "<td>".$topic['students_count']." </td>";
          						$html .= "</tr>";
          					}
          					$html .= "</table>";
          				}
          				echo $html;
          			?>
          		</div>
        	</div>
    	</div>
    </div>
    <div class="modal fade" id="students-reserve-ok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header bg-info">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Жақында оқуын бастайтын тіркелген оқушылар</h4>
          		</div>
          		<div class="modal-body">
          			<?php
          				$html = '';
          				foreach ($result['ok'] as $subject_id => $subject) {
          					$html .= "<table class='table table-bordered table-striped'>";
          						$html .= "<tr>";
          							$html .= "<td colspan='3'><center><b>".$subject['subject_title']."</b></td>";
          						$html .= "</tr>";
          					foreach ($subject['topics'] as $topic) {
          						$html .= "<tr>";
          							$html .= "<td>".$topic['topic_title']."</td>";
          							$html .= "<td>".$topic['start_date']."</td>";
                        $html .= "<td>";
                          $html .= "<ol>";
                            foreach ($topic['student_fio'] as $fio) {
                              $html .= "<li>".$fio."</li>";
                            }
                          $html .= "</ol>";
                        $html .= "</td>";
          							// $html .= "<td>".$topic['students_count']." </td>";
          						$html .= "</tr>";
          					}
          					$html .= "</table>";
          				}
          				echo $html;
          			?>
          		</div>
        	</div>
    	</div>
    </div>
</div>
<?php } ?>