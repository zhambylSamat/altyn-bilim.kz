<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/notification/view.php');

	$notification_info = get_no_progress_students_notification_info();

	if (count($notification_info) > 0) {
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
	<button class='btn btn-danger btn-md btn-block' data-toggle='modal' data-target='#no-progress-student'>
		Өтілген тақырыпты көрмеген оқушылар
        <span class="badge"><?php echo count($notification_info); ?></span>
	</button>

	<div class="modal fade" id="no-progress-student" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Өтілген тақырыпты көрмеген оқушылар</h4>
          		</div>
          		<div class="modal-body">
            		<?php
            			$html = "<table class='table table-bordered table-striped'>";
            			$html .= "<tr>";
            			$html .= "<th>№</th>";
            			$html .= "<th>Аты-жөні</th>";
            			$html .= "<th>Группасы</th>";
            			$html .= "<th>Тақырыбы</th>";
            			$html .= "<th>Әрекет</th>";
            			$html .= "</tr>";
            			$count = 0;
            			foreach ($notification_info as $value) {
            				$html .= "<tr>";
            				$html .= "<td>".(++$count)."</td>";
            				$html .= "<td>".$value['last_name'].' '.$value['first_name']."</td>";
            				$html .= "<td><a href='?page=group&group=".$value['group_info_id']."' target='_blank'>".$value['group_name']."</a></td>";
            				$html .= "<td>".$value['subtopic_title']."</td>";
            				$html .= "<td><button class='btn btn-sm btn-warning remove-no-progress-student-notification' data-id='".$value['npsn_id']."'>Өшіру</button></td>";
            				$html .= "</tr>";
            			}
            			$html .= "</table>";
            			echo $html;
            		?>
          		</div>
        	</div>
    	</div>
    </div>
</div>
<?php } ?>