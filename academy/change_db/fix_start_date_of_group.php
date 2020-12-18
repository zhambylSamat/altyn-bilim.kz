<?php
	include_once('../common/connection.php');

	try {

		$query = "SELECT gi.id,
						(select lp.created_date 
                        from lesson_progress lp, subtopic st
                        where lp.group_info_id = gi.id and st.id = lp.subtopic_id
                        order by lp.created_date, st.subtopic_order
                        limit 1) AS start_date
					FROM group_info gi";
		$stmt = $connect->prepare($query);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();

		$query = "UPDATE group_info set start_date = :start_date where id = :group_id";
		foreach ($sql_result as $value) {
			if ($value['start_date'] != '') {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':start_date', $value['start_date'], PDO::PARAM_STR);
				$stmt->bindParam(':group_id', $value['id'], PDO::PARAM_INT);
				$stmt->execute();
			}
		}
		
	} catch (Exception $e) {
		throw $e;
	}
?>