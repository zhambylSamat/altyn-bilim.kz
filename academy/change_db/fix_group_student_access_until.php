<?php
	include_once('../common/connection.php');

	try {

		$new_group_id = 27; //25, 24, 23, 27
		$old_group_id = 10; //16, 15, 14, 10

		$query = "SELECT gs.group_info_id,
						gs.id,
						gs.access_until AS access_until_old,
						(CASE
							WHEN (SELECT gs2.student_id FROM group_student gs2 WHERE gs2.group_info_id = :old_group_id AND gs2.student_id = gs.student_id) IS NULL THEN NULL
							ELSE (SELECT gs2.access_until FROM group_student gs2 WHERE gs2.group_info_id = :old_group_id AND gs2.student_id = gs.student_id)
						END) AS access_until
					FROM group_student gs
					WHERE gs.group_info_id = :new_group_id";
		$stmt = $connect->prepare($query);
		$stmt->bindParam(':new_group_id', $new_group_id, PDO::PARAM_INT);
		$stmt->bindParam(':old_group_id', $old_group_id, PDO::PARAM_INT);
		$stmt->execute();
		$sql_result = $stmt->fetchAll();


		$query = "UPDATE group_student SET access_until = :access_until WHERE id = :group_student_id";
		$group_id = 0;
		foreach ($sql_result as $value) {
			// if ($group_id != $value['group_info_id']) {
			// 	$group_id = $value['group_info_id'];
			// 	echo "<hr>";
			// }
			// echo "group_info_id: ".$value['group_info_id']."<br>";
			// echo "group_student_id: ".$value['id']."<br>";
			// echo "access_until: ".$value['access_until']."<br>";
			// echo "access_until_old: ".$value['access_until_old']."<br>";
			// echo "<br>";
			if($value['access_until'] != '' && $value['access_until'] != null) {
				$stmt = $connect->prepare($query);
				$stmt->bindParam(':access_until', $value['access_until'], PDO::PARAM_STR);
				$stmt->bindParam(':group_student_id', $value['id'], PDO::PARAM_INT);
				$stmt->execute();
			}
		}

	} catch (Exception $e) {
		throw $e;
	}
?>