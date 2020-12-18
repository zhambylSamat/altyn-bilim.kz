<?php
	include_once('connection.php');
	
	function change_navigation($level, $navigator_index) {
		$level--;
		
		if ($navigator_index == '') {
			$navigator_index = define_navigation_index($level, $navigator_index);
		}

		$page_navigator = &$_SESSION['page_navigator'];
		for ($i = 0; $i <= $level; $i++) {
			for ($j = 0; $j < count($page_navigator); $j++) { 
				if ($i == $level) {
					if ($j == $navigator_index) {
						$page_navigator[$j]['show'] = true;
					} else {
						$page_navigator[$j]['show'] = false;
					}
				}
				else {
					if ($page_navigator[$j]['show'] && isset($page_navigator[$j]['pages']) && count($page_navigator[$j]['pages']) > 0) {
						$page_navigator = &$page_navigator[$j]['pages'];
						break;
					}
				}
			}
		}
	}

	function define_navigation_index($level, $navigator_index) {
		$NAVIGATION_INDEX = 0;
		$page_navigator = $_SESSION['page_navigator'];
		for ($i=0; $i <= $level; $i++) {
			foreach ($page_navigator as $key => $value) {
				if ($i == $level) {
					if ($value['show']) {
						return $key;
					}
				} else {
					if ($value['show'] && isset($value['pages']) && count($value['pages']) >0) {
						$page_navigator = $value['pages'];
						break;
					}
				}
			}
		}
	}
?>