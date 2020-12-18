<?php
	include_once('../common/connection.php');

	if (date('d') == 1) {
		unset_marks_to_static_accounting_table();
	}

	function unset_marks_to_static_accounting_table () {
		GLOBAL $connect;

		try {

			$query = "UPDATE accounting_static_category_amount SET is_marked = 0";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}
?>