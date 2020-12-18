<?php
	include_once('../common/connection.php');


	$lp_infos = get_special_lp();
	echo 'lp_infos<br>';
	echo json_encode($lp_infos);
	echo "<br><br>";

	if (count($lp_infos['lps']) > 0) {
		$fma_infos = get_fmas($lp_infos);
		echo 'fma_infos<br>';
		echo json_encode($fma_infos);
		echo "<br><br>";

		$tvas = get_tvas($lp_infos);
		echo "tvas<br>";
		echo json_encode($tvas);
		echo "<br><br>";

		$tdas = get_tdas($lp_infos);
		echo "tdas<br>";
		echo json_encode($tdas);
		echo "<br><br>";

		$evas = get_evas($lp_infos);
		echo "evas<br>";
		echo json_encode($evas);
		echo "<br><br>";

		$mtas = get_mtas($lp_infos);
		echo "mtas<br>";
		echo json_encode($mtas);
		echo "<br><br>";


		$tvals = get_tvals($tvas);
		echo "tvals<br>";
		echo json_encode($tvals);
		echo "<br><br>";

		$tdals = get_tdals($tdas);
		echo "tdals<br>";
		echo json_encode($tdals);
		echo "<br><br>";

		$evals = get_evals($evas);
		echo "evals<br>";
		echo json_encode($evals);
		echo "<br><br>";

		$mtrs = get_mtrs($mtas);
		echo "mtrs<br>";
		echo json_encode($mtrs);
		echo "<br><br>";


		if (count($lp_infos['lps']) > 0) {
			remove_special_lps($lp_infos);
		}
		if (count($fma_infos) > 0) {
			remove_special_fmas($fma_infos);
		}
		if (count($tvas) > 0) {
			remove_tvas($tvas);
		}
		if (count($tdas) > 0) {
			remove_tdas($tdas);
		}
		if (count($evas) > 0) {
			remove_evas($evas);
		}
		if (count($mtas) > 0) {
			remove_mtas($mtas);
		}
		if (count($tvals) > 0) {
			remove_tvals($tvals);
		}
		if (count($tdals) > 0) {
			remove_tdals($tdals);
		}
		if (count($evals) > 0) {
			remove_evals($evals);
		}
		if (count($mtrs) > 0) {
			remove_mtrs($mtrs);
		}
	}


	function remove_mtrs ($mtrs) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM material_test_result WHERE id IN (".implode(',', $mtrs).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_evals ($evals) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM end_video_action_log WHERE id IN (".implode(',', $evals).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tdals ($tdals) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_document_action_log WHERE id IN (".implode(',', $tdals).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tvals ($tvals) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_video_action_log WHERE id IN (".implode(',', $tvals).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_mtas ($mtas) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM material_test_action WHERE id IN (".implode(',', $mtas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_evas ($evas) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM end_video_action WHERE id IN (".implode(',', $evas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tdas ($tdas) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_document_action WHERE id IN (".implode(',', $tdas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_tvas ($tvas) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM tutorial_video_action WHERE id IN (".implode(',', $tvas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_special_fmas ($fma_infos) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM forced_material_access WHERE id IN (".implode(',', $fma_infos).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function remove_special_lps ($lp_infos) {
		GLOBAL $connect;

		try {

			$query = "DELETE FROM lesson_progress WHERE id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_mtrs ($mtas) {
		GLOBAL $connect;

		try {

			$query = "SELECT mtr.id
						FROM material_test_result mtr
						WHERE mtr.material_test_action_id IN (".implode(',', $mtas).")";

			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tvals ($tvas) {
		GLOBAL $connect; 

		try {

			$query = "SELECT tval.id
						FROM tutorial_video_action_log tval
						WHERE tval.tutorial_video_action_id IN (".implode(',', $tvas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tdals ($tdas) {
		GLOBAL $connect; 

		try {

			$query = "SELECT tdal.id
						FROM tutorial_document_action_log tdal
						WHERE tdal.tutorial_document_action_id IN (".implode(',', $tdas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_evals ($evas) {
		GLOBAL $connect; 

		try {

			$query = "SELECT eval.id
						FROM end_video_action_log eval
						WHERE eval.end_video_action_id IN (".implode(',', $evas).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_mtas ($lp_infos) {
		GLOBAL $connect;

		try {

			$query = "SELECT mta.id
						FROM material_test_action mta
						WHERE mta.lesson_progress_id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_evas ($lp_infos) {
		GLOBAL $connect;

		try {

			$query = "SELECT eva.id
						FROM end_video_action eva
						WHERE eva.lesson_progress_id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tdas ($lp_infos) {
		GLOBAL $connect;

		try {

			$query = "SELECT tda.id
						FROM tutorial_document_action tda
						WHERE tda.lesson_progress_id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_tvas ($lp_infos) {
		GLOBAL $connect;

		try {

			$result = array();

			$query = "SELECT tva.id
						FROM tutorial_video_action tva
						WHERE tva.lesson_progress_id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}
			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_fmas ($lp_infos) {
		GLOBAL $connect;

		try {

			$result = array();

			$query = "SELECT fma.id
						FROM forced_material_access fma
						WHERE fma.lesson_progress_id IN (".implode(',', $lp_infos['lps']).")";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			foreach ($query_result as $value) {
				array_push($result, $value['id']);
			}

			return $result;
			
		} catch (Exception $e) {
			throw $e;
		}
	}

	function get_special_lp () {
		GLOBAL $connect;

		try {
			$query = "SELECT lp.id,
							lp.group_info_id
						FROM lesson_progress lp
						WHERE subtopic_id = 426
							AND DATE_FORMAT(created_date, '%Y-%m-%d') >= DATE_FORMAT('2020-08-27', '%Y-%m-%d')";
			$stmt = $connect->prepare($query);
			$stmt->execute();
			$query_result = $stmt->fetchAll();

			$result = array('lp_infos' => array(),
							'lps' => array());

			foreach ($query_result as $value) {
				array_push($result['lp_infos'], array('lp_id' => $value['id'],
														'group_info_id' => $value['group_info_id']));
				array_push($result['lps'], $value['id']);
			}

			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}
?>