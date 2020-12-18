<?php
							$i = 0;
							$current_month_n = 0;
							while($result_month = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$class = (++$i==$count) ? 'btn-warning' : 'btn-default';
								$current_month_n = $result_month['month'];
								$current_month_s = $month[intval(explode("-",$result_month['month'])[1])];
								$next_month_s = $month[intval(explode("-",$result_month['month'])[1])+1];
						?>
						<button class='btn btn-sm <?php echo $class;?> month_for_marks exists-month' date-number="<?php echo $current_month_n;?>" date-text = "<?php echo $current_month_s;?>"><?php echo $current_month_s; ?></button>
						<?php } ?>
						<?php
							if(date('m')==date(intval(substr($current_month_n,5,2))+1)){
						?>
						<button class='btn btn-sm  month_for_marks' data-name='new' date-number="<?php echo date('m');?>" date-text="<?php echo $month[intval(date('m'))];?>"><?php echo $month[(intval(date('m'))+1>12) ? 1 : (intval(date('m'))+1)]; ?></button>
						<?php } ?>





						<?php
							$i = 0;
							$current_month_n = date('Y-m');
							$august = true;
							$september = true;
							$current_month_s = $month[intval(date('m'))];
							while($result_month = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$class = (++$i==$count) ? 'btn-warning' : 'btn-default';
								$current_month_n = $result_month['month'];
								$current_month_s = $month[intval(explode("-",$result_month['month'])[1])];
								$next_month_s = $month[intval(explode("-",$result_month['month'])[1])+1];
						?>
						<button class='btn btn-sm <?php echo $class;?> month_for_marks exists-month' date-number="<?php echo $current_month_n;?>" date-text = "<?php echo $current_month_s;?>"><?php echo $current_month_s; ?></button>
						<?php
								if($august==true && $current_month_s == "Август"){
									$august = false;
								}
								if($september==true && $current_month_s == "Сентябрь"){
									$september = false;
								} 
							} 
						?>

						<?php
							if($august){
						?>
						<button class='btn btn-sm  month_for_marks' data-name='new' date-number="<?php echo '2017-08';?>" date-text="Август">Август</button>
						<?php } ?>
						<?php
							if($september){
						?>
						<button class='btn btn-sm  month_for_marks' data-name='new' date-number="<?php echo '2017-09';?>" date-text="Сентябрь">Сентябрь</button>
						<?php } ?>