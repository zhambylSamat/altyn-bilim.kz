<div style='margin: 5% 0 10% 0;'>
	<center>
		<button class='btn btn-info btn-md account-analize-modal-btn' data-toggle="modal" data-target="#account-analize-modal">Отчет</button>
	</center>
</div>

<div class="modal fade" id='account-analize-modal' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Қай уақыттың арасындағы бухгалерияның отчетын шығару</h4>
			</div>
			<div class="modal-body">
				<center>
					<div class='input-daterange accounting-analize-datepicker'>
						<input type="text" name="from-date" autocomplete="off" class='input-sm accounting-analize-datepicker-input' required style='border: 1px solid gray; border-radius: 3px;'>
						<span class='add-on'>-</span>
						<input type="text" name='to-date' autocomplete="off" class='input-sm accounting-analize-datepicker-input' required style='border: 1px solid gray; border-radius: 3px;'>
					</div>
				</center>
			</div>
			<div class='modal-footer'>
				<center>
					<button class='btn btn-md btn-success set-accounting-analize'>Отчет</button>
					<button class='btn btn-sm btn-warning' data-dismiss="modal">Отмена</button>
				</center>
			</div>
		</div>
	</div>
</div>