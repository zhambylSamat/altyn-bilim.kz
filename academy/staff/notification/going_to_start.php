<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/notification/view.php');
	$before_N_lesson = 4;
	$starting_students = get_starting_students($before_N_lesson);
	$reserve_students = get_reserve_students($before_N_lesson);

	$no_payment_count = $starting_students['no_payment_count'] + $reserve_students['no_payment_count'];
	$no_payment_count = $no_payment_count == 0 ? '' : $no_payment_count;
	$total_count = count($starting_students['datas']) + count($reserve_students['datas']);
	$total_count = $total_count == 0 ? '' : $total_count;
    $html = '';
	if ($total_count > 0) { 
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <button class='btn btn-warning btn-md btn-block' data-toggle='modal' data-target='#starting-students'>
        Жақында бастайтын оқушылар
        <span class="badge"><?php echo $total_count; ?></span>
        <span class='label label-danger'><?php echo $no_payment_count; ?></span>
    </button>

    <div class="modal fade" id="starting-students" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    	<div class="modal-dialog modal-lg" role="document">
        	<div class="modal-content">
          		<div class="modal-header">
            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		<h4 class="modal-title" id="myModalLabel">Жақында бастайтын оқушылар</h4>
          		</div>
          		<div class="modal-body">
            		<?php
                        $html .= "<b>Алдағы ".$before_N_lesson." күндегі сабағын бастайтын оқушылар тізімі</b>";
            			$html .= "<table class='table table-striped table-bordered'>";
            			$html .= "<tr><td>Аты-жөні</td><td>Телефоны</td><td>Группасы</td><td>Осы тақырыптан оқуын басайды</td><td>Оқуын бастау керек уақыты</td><td>Оплатасы</td></tr>";
                        foreach ($starting_students['datas2'] as $val) {
                            $class_danger = "";
                            $learned_text = "";
                            if ($val['learned'] && mktime(strtotime($val['start_date'])) != mktime(strtotime(date('d.m.Y')))) {
                                $class_danger = "danger";
                            } else if ($val['learned']) {
                                $learned_text .= "<br><i class='text-danger'>Тақырып басталып кетті</i>";
                            }
                            $html .= '<tr class="'.$class_danger.'">';
                                $html .= "<td>".$val['last_name'].' '.$val['first_name']."</td>";
                                $html .= "<td>".$val['phone']."</td>";
                                if ($val['status'] == 'active' || $val['status'] == 'waiting') {
                                    $extra_html = "<span class='text-success'>Оплатасы төленді</span>";
                                } else {
                                    $extra_html = "<b class='text-danger'>Оплатасы жоқ</b>";
                                }
                                if ($val['ns_id'] != '') {
                                    $extra_html .= "<br><b><i class='text-success'>Ескертілді</i></b>";
                                } else {
                                    $extra_html .= "<span class='notification-select-content'><button class='btn btn-info btn-xs btn-block notification-select' data-id='".$val['group_student_id']."' data-type='1'>Ескертілді</button></span>";
                                }
                                $html .= "<td>".$val['group_name']."</td>";
                                $html .= "<td>".$val['subtopic_title'].$learned_text."</td>";
                                $html .= "<td>".$val['start_date']."</td>";
                                $html .= "<td>".$extra_html."</td>";
                            $html .= '</tr>';
                        }
                        $html .= "<hr>";
                        foreach ($reserve_students['datas2'] as $val) {
                            $class_danger = "";
                            $learned_text = "";
                            if ($val['learned'] && mktime(strtotime($val['start_date'])) != mktime(strtotime(date('d.m.Y')))) {
                                $class_danger = "danger";
                            } else if ($val['learned']) {
                                $learned_text .= "<br><i class='text-danger'>Тақырып басталып кетті</i>";
                            }
                            $html .= "<tr class='".$class_danger."'>";
                                $html .= "<td>".$val['last_name'].' '.$val['first_name']."</td>";
                                $html .= "<td>".$val['phone']."</td>";
                                if ($val['has_payment']) {
                                    $extra_html = "<span class='text-success'>Оплатасы төленді</span>";
                                } else {
                                    $extra_html = "<b class='text-danger'>Оплатасы жоқ</b>";
                                }
                                if ($val['ns_id'] != '') {
                                    $extra_html .= "<br><b><i class='text-success'>Ескертілді</i></b>";
                                } else {
                                    $extra_html .= "<span class='notification-select-content'><button class='btn btn-info btn-xs btn-block notification-select' data-id='".$val['course_id']."' data-type='2'>Ескертілді</button></span>";
                                }
                                $html .= "<td>".$val['group_name']."</td>";
                                $html .= "<td>".$val['subtopic_title'].$learned_text."</td>";
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