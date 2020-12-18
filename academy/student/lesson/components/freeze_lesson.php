<?php
	$freeze_lesson_exist = get_freeze_lesson_exist();

	if (!$freeze_lesson_exist) {
?>
<div class='freeze-lesson-btn-content'>
	<button class='btn btn-sm btn-info pull-right' style='margin-top: 1%;' data-toggle="modal" data-target="#freeze-lesson-modal">Сабақты уақытша тоқтату</button>
</div>

<?php 
	} else {
?>
<div class='freeze-lesson-cancel-btn-content'>
	<button class='btn btn-sm btn-warning pull-right cancel-freeze-lesson'>Оқуды жалғастыру</button>
	<br>
</div>
<?php } ?>

<div class='modal fade' id='freeze-lesson-modal' tabindex='-1' role='dialog'>
	<div class='modal-dialog' role='document'>
		<div class='modal-content'>
			<form action='lesson/controller.php' method='post'>
				<center>
					<div class='modal-header'>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><b>Сабақты уақытша тоқтату</b></h4>
					</div>
					<div class='modal-body'>
							<p>Егер ауырып қалсаң, демалысқа кетіп бара жатсаң немесе басқа да жағдайлармен бірнеше күн сабақ оқи алмайтын болсаң, сабақты уақытша тоқтатсаң болады.</p>
							<p>Бұл жағдайда тақырыптарың ашылмай күтіп тұрады. Алайда, оқу төлемі жалғаса береді.</p>
							<p>Кез келген уақытта сабақты белгілеген күннен ертерек жалғастырып кетсең болады.</p>
							<p>("Армия" курсының группаларына әсер етпейді!)</p>
							<p><b>Оқуды уақытша тоқтататын күндердің аралығын таңда:</b></p>
						<div class="input-daterange freeze-lesson-datepicker">
							<input type="text" name="from-date" autocomplete="off" class="input-sm freeze-lesson-datepicker-input" required="" style="border: 1px solid gray; border-radius: 3px;">
							<span class="add-on">-</span>
							<input type="text" name="to-date" autocomplete="off" class="input-sm freeze-lesson-datepicker-input" required="" style="border: 1px solid gray; border-radius: 3px;">
						</div>
					</div>
					<div class='modal-footer' style='text-align: center;'>
						<div class='form-group'>
							<input type="submit" class='btn btn-success btn-sm' name="set-freeze-lessons" value='Сақтау'>
							<input type='reset' class='btn btn-warning btn-xs' data-dismiss="modal" value='Отмена'>
						</div>
					</div>
				</center>
			</form>
		</div>
	</div>
</div>
