<?php
	include_once('../connection.php');

	try {
		$time = strtotime("-1 year", time());
  		$start_session = date("Y-07-15", $time);
  		$end_session = date("Y-07-16", time());
		$stmt = $conn->prepare("SELECT s.student_num, 
									s.surname,
									s.name,
									s.phone AS phone1,
									s.class,
									s.school,
									er.id,
									er.phone AS phone2,
									er.tzk,
									er.iin,
									er.potok,
									er.has_result,
									er.total_mark,
									er.result
								FROM student s
								LEFT JOIN ent_result er
									ON er.student_num = s.student_num
								WHERE (s.block != 6
										OR 
										(s.block = 6
										AND
											(
											DATE_FORMAT(s.block_date, '%Y-%m-%d') >= STR_TO_DATE(:start_session, '%Y-%m-%d')
												AND 
											DATE_FORMAT(s.block_date, '%Y-%m-%d') < STR_TO_DATE(:end_session, '%Y-%m-%d')
											)
										)
									  )
									AND s.student_num NOT IN ('US5985cba14b8d3100168809')
								ORDER BY er.has_result DESC, CAST(s.class AS SIGNED) DESC, s.surname ASC, s.name ASC");
		$stmt->bindParam(':start_session', $start_session, PDO::PARAM_STR);
		$stmt->bindParam(':end_session', $end_session, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
	} catch (PDOException $e) {
		throw $e;
	}
?>
<style type="text/css">
	.result-content{
		padding: 1% 5%;
	}
</style>

<div class='row'>
	<div class='col-md-12 col-sm-12 col-xs-12'>
		<table class='table table-bordered table-striped'>
			<tr style='position: sticky; top: 0px; z-index: 10; width: 100%;'>
				<th style='width: 2%;'>#</th>
				<th style='width: 25%;'>Аты-жөні</th>
				<th style='width: 20%;'>Сыныбы</th>
				<th style='width: 13%;'>Телефоны</th>
				<th style='width: 20%;'>Оқушының жеке нөмірлері</th>
				<th style='width: 8%;'>Поток</th>
				<th style='width: 13%;'>ҰБТ-дағы баллы</th>
			</tr>
			<?php
				$count = 0;
				$class = "";
				$border_css = "";
				foreach ($result as $val) {
					if ($class != $val['class']) {
						$class = $val['class'];
						$count = 0;
						echo "<tr><td colspan='7' style='background-color: #D9EDF7;'><center><b><h1>".$val['class']."- сынып</h1></b></center></td></tr>";
					}
					if ($val['has_result'] == 1) {
						$border_css = "border: 2px solid green; background-color: #88FF88;";
					} else if (($val['iin'] == "" || $val['tzk'] == "") && $val['class'] == '11') {
						$border_css = "border: 2px solid red;";
					} else {
						$border_css = "";
					}
					$count++;
			?>
			<tr style='<?php echo $border_css; ?>'>
				<td><?php echo $count; ?></td>
				<td>
					<a href="student_info_marks.php?data_num=<?php echo $val['student_num']; ?>" target="_blank">
						<?php echo $val['surname']." ".$val['name']; ?>
					</a>
				</td>
				<td>
					<?php if ($val['id'] == '') { ?>
					<form class='form-inline express_changing_school_class'>
						<input type="number" class='form-control' name="class" step=1 value="<?php echo $val['class']; ?>">
						<input type="hidden" name="student_num" value='<?php echo $val['student_num']; ?>'>
						<input type="submit" class='btn btn-success btn-xs' value='ок'>
					</form>		
					<?php } else { echo $val['class']; } ?>
				</td>
				<td>
					<?php
						$phone = "N/A";
						if ($val['phone2'] != '') {
							$phone = "+7".$val['phone2'];
						} else if ($val['phone1'] != '') {
							$phone = "+7".$val['phone1'];
						} 
						echo $phone;
					?>
				</td>
				<td>
					<?php
						$tzk = "<b>ТЖК:</b> <b style='color:red;'>N/A</b><br>";
						$iin = "<b>ИИН:</b> <b style='color:red;'>N/A</b><br>";
						$show_form_btn = "<button class='btn btn-info btn-xs pull-right show-form'>ТЖК / ИИН</button>";
						$is_form = true;
						if ($val['tzk'] != '' && $val['iin'] != '') {
							$tzk = "<b>ТЖК:</b> ".$val['tzk']."<br>";
							$iin = "<b>ИИН:</b> ".$val['iin'];
							$is_form = false;
						}
						$html = "<div class='student-info'>";
						$html .= $tzk.$iin;
						if ($is_form) {
							$html .= $show_form_btn;
						}
						$html .= "</div>";

						$html .= "<form class='student-form' style='display:none;'>";
						$html .= "<input type='number' name='tzk' class='form-control' placeholder='ТЖК енгізіңіз' value='".($val['tzk']!='' ? $val['tzk'] : '')."' required>";
						$html .= "<input type='number' name='iin' class='form-control' placeholder='ИИН енгізіңіз' value='".($val['iin']!='' ? $val['iin'] : '')."' required>";
						$html .= "<input type='number' name='potok' class='form-control' placeholder='Поток' value='".($val['potok']!='' ? $val['potok'] : '')."'  required>";
						$html .= "<center>";
						$html .= "<input type='submit' class='btn btn-success btn-xs' value='Сақтау'>";
						$html .= "<a class='btn btn-warning btn-xs cancel'>Отмена</a>";
						$html .= "</center>";
						$html .= "<input type='hidden' name='student_num' value='".$val['student_num']."'>";
						$html .= "<input type='hidden' name='phone1' value='".$val['phone1']."'>";
						$html .= "<input type='hidden' name='new' value='".($val['tzk']!='' || $val['iin']!='' ? 'false' : 'true')."'>";
						$html .= "</form>";

						echo $html;
					?>
				</td>
				<td>
					<?php
						echo ($val['potok'] != '') ? $val['potok'] : "<span style='color: gray;'>N/A</span>";
					?>
				</td>
				<td>
					<?php
						if ($val['total_mark'] != '') {
							echo "<a class='open-ent-result' data-toggle='modal' data-target='.box-ent-result'>".$val['total_mark']." балл</a>";
					?>
						<div style='display:none;' class='ent-result-content'>
							<div class='result-content'>
								<center><h3><?php echo $val['surname']." ".$val['name']; ?></h3></center>
								<div>
									<?php 
										$res_json = json_decode($val['result'], true);
									?>
									<table class='table table-bordered table-striped'>
										<tr>
											<th>Аты-жөні</th>
											<td><?php echo convertUnicode($res_json['fio']); ?></td>
										</tr>
										<tr>
											<th>ТЖК</th>
											<td><?php echo $res_json['tzk']; ?></td>
										</tr>
										<tr>
											<th>ИИН (ЖСН)</th>
											<td><?php echo $res_json['iin']; ?></td>
										</tr>
										<tr>
											<th>ҰБТ тапсырған орын</th>
											<td><?php echo convertUnicode($res_json['univer']); ?></td>
										</tr>
										<tr>
											<th>Жалпы жинаған баллы</th>
											<td><?php echo $res_json['sumMark']." балл"; ?></td>
										</tr>
										<?php
											foreach ($res_json['marks'] as $v) {
										?>
										<tr>
											<th><?php echo convertUnicode($v['name']); ?></th>
											<td><?php echo $v['mark']." балл"; ?></td>
										</tr>
										<?php } ?>
									</table>
								</div>
							</div>
						</div>
					<?php
						} else {
							echo "<span style='color:gray;'>N/A</span>";
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>

<?php
	function convertUnicode($str) {
		$converter = array("u" => "\u");
		$converted_str = strtr($str, $converter);
		$decoded_str = unicode_decode($converted_str);
		return $decoded_str;
	}
	function replace_unicode_escape_sequence($match) {
	    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	}
	function unicode_decode($str) {
	    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
	}
?>