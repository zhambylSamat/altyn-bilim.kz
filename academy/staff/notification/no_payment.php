<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/notification/view.php');

	$group_students = get_no_payment_and_started_students();
	$registration_course = get_no_payment_and_started_students_from_registration_course();
	$total_count = count($group_students['datas']) + count($registration_course['datas']);
	$count = intval($registration_course['not_seleceted_count']) + intval($group_students['not_seleceted_count']);
	if ($total_count > 0) {
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <button class='btn btn-danger btn-md btn-block' data-toggle='modal' data-target='#students-no-payment'>
        Оқуы басталған ж/е оплатасы жоқ оқушылар
        <span class="badge"><?php echo $total_count; ?></span>
        <br>
        Ескертілгендер <span class="badge"><?php echo $total_count-$count; ?></span>
    </button>

    <div class="modal fade" id="students-no-payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Оқуы басталған ж/е оплатасы жоқ оқушылар</h4>
          		</div>
          		<div class="modal-body">
            		<?php
            			$html = "<table class='table table-striped table-bordered'>";
            			$html .= "<tr><td>#</td><td>Аты-жөні</td><td>Телефоны</td><td>Группасы</td><td>Бастау керек тақырыбы</td><td>Оқуын бастау керек уақыты</td><td></td></tr>";
            			$count = 1;
                        foreach ($group_students['datas'] as $val) {
                            $html .= '<tr>';
                            	$html .= "<td>".($count++)."</td>";
                                $html .= "<td>".$val['last_name'].' '.$val['first_name']."</td>";
                                $html .= "<td>".$val['phone']."</td>";
                                $extra_html = "<i class='text-danger'>Оплатасы жоқ</i>";
                                if ($val['ns_id'] != '') {
                                    $extra_html .= "<br><b><i class='text-success'>Ескертілді</i></b>";
                                } else {
                                    $extra_html .= "<span class='notification-select-content'><button class='btn btn-info btn-xs btn-block notification-select' data-id='".$val['group_student_id']."' data-type='3'>Ескертілді</button></span>";
                                }
                                $html .= "<td>".$val['group_name']."</td>";
                                $html .= "<td>".$val['subtopic_title']."</td>";
                                $html .= "<td>".$val['start_date']."</td>";
                                $html .= "<td>".$extra_html."</td>";
                            $html .= '</tr>';
                        }
                        $html .= "<hr>";
                        foreach ($registration_course['datas'] as $val) {
                            $html .= "<tr>";
                            	$html .= "<td>".($count++)."</td>";
                                $html .= "<td>".$val['last_name'].' '.$val['first_name']."</td>";
                                $html .= "<td>".$val['phone']."</td>";
                                $extra_html = "<i class='text-danger'>Оплатасы жоқ</i>";
                                if ($val['ns_id'] != '') {
                                    $extra_html .= "<br><b><i class='text-success'>Ескертілді</i></b>";
                                } else {
                                    $extra_html .= "<span class='notification-select-content'><button class='btn btn-info btn-xs btn-block notification-select' data-id='".$val['course_id']."' data-type='2'>Ескертілді</button></span>";
                                }
                                $html .= "<td>".$val['group_name']."</td>";
                                $html .= "<td>".$val['subtopic_title']."</td>";
                                $html .= "<td>".$val['start_date']."</td>";
                                $html .= "<td>".$extra_html."</td>";
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