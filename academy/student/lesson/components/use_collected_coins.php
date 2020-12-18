<?php
	if ($total_coins >= $amount_of_coins_for_bonus) {
?>
	<div id='collected-coin-box'>
		<span id='collected-coin-success'>ЖАРАЙСЫҢ!</span> Сен <span id='collected-coin-coins'><?php echo $total_coins; ?></span> монета жинадың! <br> Бұл монеталарды сен кез келген пәніңде <b>10 күн тегін</b> оқуға айырбастай аласың.
		<br>
		<button class='btn btn-sm btn-success collected-coin-btn' data-toggle="modal" data-target="#choose-groups-for-coins">Монеталарды ҚОЛДАНУ</button>
	</div>


	<div class="modal fade" id="choose-groups-for-coins" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
		    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    		<center><h4 id='title'>Монеталарды қай пәнге қолданғың келеді?</h4></center>
		  		</div>
		  		<div class="modal-body">
		  			<form id='use-coins-for-group-form' class='form-horizontal'>
		  			<?php
		  				$html = "";
		  				foreach ($group_info as $val) {
		  					if (!$val['is_marathon_group'] && $val['group_status_id'] == 2) {
		  						$title = $val['subject_title'].' | '.$val['group_name'];
		  						if ($val['is_army_group']) {
		  							$title .= " | <b>Армия</b>";
		  						}
			  					$html .= "<div class='radio'>";
			  						$html .= "<label>";
			  							$html .= "<input type='radio' name='group-student-id' value='".$val['group_student_id']."'>";
			  							$html .= $title;
			  						$html .= "</label>";
			  					$html .= "</div>";
		  					}
		  				}
		  				echo $html;
		  			?>
		  			<hr>
		  			<center><button class='btn btn-sm btn-success'>Сақтау</button></center>
		  			</form>
		  		</div>
			</div>
		</div>
	</div>
<?php } ?>