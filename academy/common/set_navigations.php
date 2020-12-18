<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	function set_navigation($level) {
		GLOBAL $root;
		GLOBAL $ab_root;

		$page_navigator = $_SESSION['page_navigator'];
		$dir = $level == 0 ? '' : get_dir(0, $level - 1, $page_navigator);
		for ($i = 0; $i < $level; $i++) {
			foreach ($page_navigator as $value) {
				if ($value['show']) {
					if (isset($value['pages']) && count($value['pages']) > 0) {
						$page_navigator = $value['pages'];
						break;
					} else {
						return;
					}
				}
			}
		}
		$html = "<div class='container-fluid'><div class='row'>";
		$html .= "<div class='col-md-2 col-sm-2 col-xs-1 nav-animation nav-animation-menu' style='padding-right: 0; padding-left: 0;'>";
		$html .= "<ul class='nav nav-pills nav-stacked'>";
			$html .= "<li><a class='hidden-lg hidden-md hidden-sm mob-nav-btn mob-nav-expand-btn'><span class='fas fa-bars'></span></a></li>";
		foreach ($page_navigator as $key => $level_value) {
			$is_active = '';
			$full_dir = $dir.$level_value['short_name'].'/';
			if ($level_value['show']) {
				$is_active = 'active';
				$return_dir = $full_dir;
			}
			$html .= "<li role='presentation'
					class='navigation nav-".$level." ".$is_active."'
					level='".$level."'
					dir='".$full_dir."'
					content-key='".$key."'
					style='cursor:pointer;'
					title='".$level_value['description']."'>";
			$extra_class = '';
			$extra_id = '';
			if (isset($level_value['class'])) {
				$extra_class .= $level_value['class'];
			}
			if (isset($level_value['id'])) {
				$extra_id .= $level_value['id'];
			}
			if (isset($level_value['icon'])) {
				$html .= "<a style='padding-top: 7px !important; padding-bottom: 7px; !important' class='".$extra_class." hidden-xs' id='".$extra_id."'>";
					$html .= "<span>".$level_value['full_name']."</span>";
				$html .= "</a>";
				$html .= "<a style='padding-top: 7px !important; padding-bottom: 7px; !important' class='".$extra_class." hidden-lg hidden-md hidden-sm mob-nav-btn' id='".$extra_id."'>";
					$html .= "<span class='mob-nav-icon'>".$level_value['icon']."</span>";
					$html .= "<span class='mob-nav-text'>".$level_value['full_name']."</span>";
				$html .= "</a>";
			} else {
				$html .= "<a class='".$extra_class."' id='".$extra_id."' href='#'>";
						$html .= "<span>".$level_value['full_name']."</span>";
				$html .= "</a>";	
			}
			
			$html .= "</li>";
		}
		$html .= "</ul>
			</div>
			<div class='col-md-10 col-sm-10 col-xs-11 nav-animation nav-animation-content'>
			<div class='box-content-".$level."' style='position: relative;'>";
		echo $html;
			include_once($root.$_SESSION['user_direction'].'/'.$return_dir.'/index.php');
		$html = "</div></div>"; //.col-... .box-content...
		$html .= "</div></div>"; //.row .container
		echo $html;
	}

	function get_dir($count, $level, $page_navigator) {
		$dir = "";
		$tmp_arr = array();
		foreach ($page_navigator as $value) {
			if ($value['show']) {
				$dir = $value['short_name'].'/';
				if (isset($value['pages']) && count($value['pages']) > 0) {
					$tmp_arr = $value['pages'];
				}
				break;
			}
		}

		if ($count < $level && count($tmp_arr) != 0) {
			$dir .= get_dir($count++, $level, $value['pages']);
		}
		return $dir;
	}

?>