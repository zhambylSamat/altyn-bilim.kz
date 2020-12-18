<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');
	include_once($root.'/staff/accounting/views.php');

?>
<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-4 col-sm-4 col-xs-12'>
			<?php 
				if (admin_create_edit_remove_access()) {
					include_once($root.'/staff/accounting/components/category_coming.php');
				}
			?>
		</div>
		<div class='col-md-4 col-sm-4 col-xs-12'>
			<?php
				if (admin_create_edit_remove_access()) {
					include_once($root.'/staff/accounting/components/category_expenditure.php');
				}
			?>
		</div>
		<div class='col-md-4 col-sm-4 col-xs-12'>
			<?php
				if (admin_create_edit_remove_access()) {
					include_once($root.'/staff/accounting/components/money_transfer.php');
				}
			?>
		</div>
		<div class='col-md-6 col-md-6 col-xs-12'>
			<?php
				if (admin_create_edit_remove_access()) {
					include_once($root.'/staff/accounting/components/account_actions.php');
				}
			?>
		</div>
		<div class='col-md-6 col-md-6 col-xs-12'>
			<?php
				include_once($root.'/staff/accounting/components/account_filters.php');
				include_once($root.'/staff/accounting/components/account_static_amounts.php');
			?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' id='account-table' style='padding: 0;'>
			<?php include_once($root.'/staff/accounting/components/account_table.php'); ?>
		</div>
		<div class='col-md-12 col-sm-12 col-xs-12' id='account-analize-btn'>
			<?php include_once($root.'/staff/accounting/components/account_analize_form.php'); ?>
		</div>
	</div>
</div>


<div class="modal fade" id='comings-expenditures-list' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-xs" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>