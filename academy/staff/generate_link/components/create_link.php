<div id='create-link'>
	<button id='open-create-link-form' class='btn btn-md btn-default btn-block'>+ Материалдарға доступ құрастыру</button>
	<div id='create-link-content' class='hide'>
		<form class='form-horizontal' id='create-link-form'>
			<div class='form-group'>
				<label for='access-hours' class='col-sm-6 control-label'>Ссылканың ашық тұратын уақыты:</label>
				<div class='col-sm-6'>
					<div class='input-group'>
						<input type="number" min='1' max='999' step='1' name="access-hours" id='access-hours' class='form-control' value='72' required="">
						<div class="input-group-addon">сағ.</div>
					</div>
				</div>
			</div>
			<div class='form-group'>
				<label for='comment' class='col-sm-6 control-label'>Комментарий:</label>
				<div class='col-sm-6'>
					<textarea class='form-control' name='comment' rows='3' required placeholder="Мысалы: Аты-жөні"></textarea>
				</div>
			</div>
			<div class='form-group'>
				<div class='col-sm-6'>
					<button type='button' id='materials-for-link-btn' class='btn btn-sm btn-primary pull-right' data-toggle="modal" data-target="#materials-for-link" style='padding: 1% 10%;'>+ Материалдар</button>
				</div>
				<div class='col-sm-6'>
					<div id='material-link-content'></div>
					<p id='material-link-content-message' style='color:red;'></p>
				</div>
			</div>
			<button type='button' class='btn btn-sm btn-warning pull-right' id='close-create-link-form'>Отмена</button>
			<input type="submit" class="btn btn-md btn-success pull-right" style='margin-right: 15px;' value='Ссылка құрастыру'>
		</form>
	</div>
</div>

<div class="modal fade" id='materials-for-link' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	</div>
    		<div class="modal-body">
	    		
	    	</div>
    	</div>
	</div>
</div>